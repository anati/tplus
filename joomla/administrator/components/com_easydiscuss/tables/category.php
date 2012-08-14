<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'constants.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'image.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );
require_once( DISCUSS_HELPERS . DS . 'date.php' );

class DiscussCategory extends JTable
{
	var $id 						= null;
	var $created_by		= null;
	var $title					= null;
	var $alias					= null;
	var $avatar					= null;
	var $parent_id				= null;
	var $private				= null;
	var $created				= null;
	var $status			= null;
	var $published		= null;
	var $ordering		= null;
	var $description	= null;
	var $params 		= null;

	public $level       = null;
	public $lft         = null;
	public $rgt         = null;

	private $_params	= null;
	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__discuss_category' , 'id' , $db );
	}

	function load( $key = null , $permalink = false )
	{
		if( !$permalink )
		{
			return parent::load( $key );
		}

		$db		= $this->getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $key );
		$db->setQuery( $query );

		$id		= $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if( !$id )
		{
			$query	= 'SELECT id FROM ' . $this->_tbl . ' '
					. 'WHERE alias=' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );
			$db->setQuery( $query );

			$id		= $db->loadResult();
		}
		return parent::load( $id );
	}

	/**
	 * Overrides parent's delete method to add our own logic.
	 *
	 * @return boolean
	 * @param object $db
	 */
	function delete( $pk = null )
	{
		$db		= $this->getDBO();
		$config = DiscussHelper::getConfig();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$count	= $db->loadResult();

		if( $count > 0 )
		{
			return false;
		}

		$this->removeAvatar();

		return parent::delete();
	}

	public function removeAvatar( $store = false )
	{
		$config		= DiscussHelper::getConfig();

		/* TODO */
		//remove avatar if previously already uploaded.
		$avatar = $this->avatar;

		if( $avatar != 'cdefault.png' && !empty($avatar))
		{

			$avatar_config_path = $config->get('main_categoryavatarpath');
			$avatar_config_path = rtrim($avatar_config_path, '/');
			$avatar_config_path = JString::str_ireplace('/', DS, $avatar_config_path);

			$upload_path		= JPATH_ROOT.DS.$avatar_config_path;

			$target_file_path		= $upload_path;
			$target_file 			= JPath::clean($target_file_path . DS. $avatar);

			if(JFile::exists( $target_file ))
			{
				if( !JFile::delete( $target_file ) )
				{
					return false;
				}

				$this->avatar	= '';
				
				if( $store )
				{
					$this->store();
				}
			}
		}
		return true;
	}

	function aliasExists( $alias )
	{
		$db		= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $alias );

		if( $this->id != 0 )
		{
			$query	.= ' AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $this->id );
		}
		$db->setQuery( $query );

		return $db->loadResult() > 0 ? true : false;
	}

	public function generateAlias( $title )
	{
		return JFilterOutput::stringURLSafe( $title );
	}

	/**
	 * Overrides parent's bind method to add our own logic.
	 *
	 * @param Array $data
	 **/
	function bind( $data , $ignore = array() )
	{
		parent::bind( $data );

		if( empty( $this->created ) )
		{
			$date			= JFactory::getDate();
			$this->created	= $date->toMySQL();
		}

		jimport( 'joomla.filesystem.filter.filteroutput');

		$this->setAlias();
	}

	public function setAlias()
	{
		jimport( 'joomla.filesystem.filter.filteroutput');

		$i		= 1;
		$alias 	= DiscussHelper::permalinkSlug( $this->title );
		$tmp    = $alias;

		while( $this->aliasExists( $tmp ) || empty( $tmp ) )
		{
			$alias  = empty( $alias ) ? DiscussHelper::permalinkSlug( $this->title ) : $alias;
			$tmp    = empty( $tmp ) ? DiscussHelper::permalinkSlug( $this->title ) : $alias . '-' . $i;

			$i++;
		}

		$this->alias    = $tmp;
	}

	public function getTitle()
	{
		return JText::_( $this->title );
	}

	function getRSS()
	{
		return DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=categories&id=' . $this->id );
	}

	function getAtom()
	{
		return DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=categories&id=' . $this->id , true );
	}

	function getAvatar()
	{
		$avatar_link    = '';

		if($this->avatar == 'cdefault.png' || $this->avatar == 'default_category.png' || $this->avatar == 'components/com_easydiscuss/assets/images/default_category.png' || $this->avatar == 'components/com_easydiscuss/assets/images/cdefault.png' || empty($this->avatar))
		{
			$avatar_link   = 'components/com_easydiscuss/assets/images/default_category.png';
		}
		else
		{
			$avatar_link   = DiscussImageHelper::getAvatarRelativePath('category') . '/' . $this->avatar;
		}

		return rtrim(JURI::root(), '/') . '/' . $avatar_link;
	}

	function getPostCount()
	{
		$db		= JFactory::getDBO();
		$my		= JFactory::getUser();

		$queryExclude		= '';
		$excludeCats		= array();

		// get all private categories id
		if($my->id == 0)
		{
			$query	=	'select a.`id`, a.`private`';
			$query	.=	' from `#__discuss_category` as a';
			$query	.=	' where a.`private` = ' . $db->Quote('1');

			$db->setQuery($query);
			$result = $db->loadObjectList();

			for($i=0; $i < count($result); $i++)
			{
				$item	= $result[$i];
				$item->childs = null;

				DiscussHelper::buildNestedCategories($item->id, $item);

				$catIds		= array();
				$catIds[]	= $item->id;
				DiscussHelper::accessNestedCategoriesId($item, $catIds);

				$excludeCats	= array_merge($excludeCats, $catIds);
			}
		}

		if( !class_exists( 'EasyDiscussModelCategories' ) )
		{
			JLoader::import( 'categories' , DISCUSS_ROOT . DS . 'models' );
		}
		$model		= JModel::getInstance( 'Categories' , 'EasyDiscussModel' );
		$childs		= $model->getChildIds( $this->id );
		$total		= count( $childs );
		$subcategories		= array();
		$subcategories[]	= $this->id;

		if( $childs )
		{
			for( $i = 0; $i < $total; $i++ )
			{
				$subcategories[]	= $childs[ $i ];
			}
		}
		$filtered	= array_diff($subcategories, $excludeCats);

		if (empty($filtered))
		{
			// just a temp fix when DiscussHelper::getPrivateCategories()
			// failed to get correct result and it will cause the following
			// query fails with error 500.
			return;
		}

		$query			= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'category_id' ) . ' IN (' . implode( ',' , $filtered ) . ') '
						. 'AND ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 ) . ' '
						. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( DISCUSS_ID_PUBLISHED );
		$db->setQuery($query);

		return $db->loadResult();
	}

	function getRecentPosts( $count = 5 )
	{
		$db 	= JFactory::getDBO();

		$query			= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id ) . ' '
						. 'AND ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( 0 ) . ' '
						. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( DISCUSS_ID_PUBLISHED )
						. 'LIMIT 0,' . $count;
		$db->setQuery($query);

		$data	= $db->loadObjectList();
		$total	= count( $data );
		$posts	= array();

		for( $i = 0; $i < $total; $i++ )
		{
			$post	= JTable::getInstance( 'Posts' , 'Discuss' );
			$post->bind( $data[ $i ] );

			$posts[]	= $post;
		}

		return $posts;
	}

	public function getPermalink( $external = false )
	{
		return DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index&category_id=' . $this->id );
	}

	public function getRSSPermalink( $external = false )
	{
		return DiscussRouter::_( 'index.php?option=com_easydiscuss&format=feed&view=index&category_id=' . $this->id );
	}

	function getChildCount()
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT count(1) FROM `#__discuss_category` WHERE `parent_id` = ' . $db->Quote($this->id);
		$db->setQuery($query);

		return $db->loadResult();
	}

	/*
	 * Retrieves a list of active bloggers that contributed in this category.
	 *
	 * @param	null
	 * @return	Array	An array of TableProfile objects.
	 */
	public function getActivePosters()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT DISTINCT(`user_id`) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$rows		= $db->loadObjectList();

		if( !$rows )
		{
			return false;
		}

		$bloggers	= array();
		foreach( $rows as $row )
		{
			$profile	= JTable::getInstance( 'Profile' , 'Discuss' );
			$profile->load( $row->user_id );

			$bloggers[]	= $profile;
		}

		return $bloggers;
	}

	public function store( $alterOrdering = false )
	{
		if( empty( $this->created ))
		{
			$offset     	= DiscussDateHelper::getOffSet();
			$newDate		= new JDate( '', $offset );
			$this->created  = $newDate->toMySQL();
		}
		else
		{
			$newDate		= new JDate( $this->created );
			$this->created  = $newDate->toMySQL();
		}

		if( empty( $this->alias) )
		{
			$this->setAlias();
		}


		// Figure out the proper nested set model
		// No parent id, we use the current lft,rgt
		if( $alterOrdering )
		{
			if( $this->parent_id )
			{
				$left           = $this->getLeft( $this->parent_id );
				$this->lft      = $left;
				$this->rgt      = $this->lft + 1;

				// Update parent's right
				$this->updateRight( $left );
				$this->updateLeft( $left );
			}
			else
			{
				$this->lft      = $this->getLeft() + 1;
				$this->rgt      = $this->lft + 1;
			}
		}

		return parent::store();
	}

	public function updateLeft( $left, $limit = 0 )
	{
		$db     = JFactory::getDBO();
		$query  = 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'lft' ) . '=' . $db->nameQuote( 'lft' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'lft' ) . '>=' . $db->Quote( $left );

		if( !empty( $limit ) )
			$query  .= ' and `lft`  < ' . $db->Quote( $limit );

		//echo '<br> ' . $query;

		$db->setQuery( $query );
		$db->Query();
	}

	public function updateRight( $right, $limit = 0 )
	{
		$db     = JFactory::getDBO();
		$query  = 'UPDATE ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'SET ' . $db->nameQuote( 'rgt' ) . '=' . $db->nameQuote( 'rgt' ) . ' + 2 '
				. 'WHERE ' . $db->nameQuote( 'rgt' ) . '>=' . $db->Quote( $right );

		if( !empty( $limit ) )
			$query  .= ' and `rgt`  < ' . $db->Quote( $limit );

		//echo '<br> ' . $query;

		$db->setQuery( $query );
		$db->Query();
	}

	public function getLeft( $parent = DISCUSS_CATEGORY_PARENT )
	{
		$db     = JFactory::getDBO();

		if( $parent != DISCUSS_CATEGORY_PARENT )
		{
		$query  = 'SELECT `rgt`' . ' '
				. 'FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $parent );
		}
		else
		{
		$query  = 'SELECT MAX(' . $db->nameQuote( 'rgt' ) . ') '
				. 'FROM ' . $db->nameQuote( $this->_tbl );
// 				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $parent );
		}
		$db->setQuery( $query );

		$left   = (int) $db->loadResult();

		return $left;
	}

	public function move( $direction , $where = '' )
	{
		$db = JFactory::getDBO();

		if( $direction == -1) //moving up
		{
			// getting prev parent
			$query  = 'select `id`, `lft`, `rgt` from `#__discuss_category` where `lft` < ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft desc limit 1';

			//echo $query;exit;
			$db->setQuery($query);
			$preParent  = $db->loadObject();

			// calculating new lft
			$newLft = $this->lft - $preParent->lft;
			$preLft = ( ($this->rgt - $newLft) + 1) - $preParent->lft;

			//get prevParent's id and all its child ids
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($preParent->lft) . ' and rgt <= ' . $db->Quote($preParent->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$preItemChilds = $db->loadResultArray();
			$preChildIds   = implode(',', $preItemChilds);
			$preChildCnt   = count($preItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();
			$childIds   = implode(',', $itemChilds);
			$ChildCnt   = count($itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.
			//update current parent block
			$query  = 'update `#__discuss_category` set';
			$query  .= ' lft = lft - ' . $db->Quote($newLft);
			if( $ChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($newLft);
			}
			$query  .= ' where `id` in (' . $childIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			$query  = 'update `#__discuss_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($preLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($preLft);
			$query  .= ' where `id` in (' . $preChildIds . ')';

			//echo '<br>' . $query;
			//exit;
			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__discuss_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);
			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__discuss_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($preParent->id);
			$db->setQuery($query);
			$db->query();

			return true;
		}
		else //moving down
		{
			// getting next parent
			$query  = 'select `id`, `lft`, `rgt` from `#__discuss_category` where `lft` > ' . $db->Quote($this->lft);
			if($this->parent_id == 0)
				$query  .= ' and parent_id = 0';
			else
				$query  .= ' and parent_id = ' . $db->Quote($this->parent_id);
			$query  .= ' order by lft asc limit 1';

			$db->setQuery($query);
			$nextParent  = $db->loadObject();


			$nextLft 	= $nextParent->lft - $this->lft;
			$newLft 	= ( ($nextParent->rgt - $nextLft) + 1) - $this->lft;


			//get nextParent's id and all its child ids
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($nextParent->lft) . ' and rgt <= ' . $db->Quote($nextParent->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$nextItemChilds = $db->loadResultArray();
			$nextChildIds   = implode(',', $nextItemChilds);
			$nextChildCnt   = count($nextItemChilds);

			//get current item's id and it child's id
			$query  = 'select `id` from `#__discuss_category`';
			$query  .= ' where lft >= ' . $db->Quote($this->lft) . ' and rgt <= ' . $db->Quote($this->rgt);
			$db->setQuery($query);

			//echo '<br>' . $query;
			$itemChilds = $db->loadResultArray();
			$childIds   = implode(',', $itemChilds);

			//now we got all the info we want. We can start process the
			//re-ordering of lft and rgt now.

			//update next parent block
			$query  = 'update `#__discuss_category` set';
			$query  .= ' `lft` = `lft` - ' . $db->Quote($nextLft);
			if( $nextChildCnt == 1 ) //parent itself.
			{
				$query  .= ', `rgt` = `lft` + 1';
			}
			else
			{
				$query  .= ', `rgt` = `rgt` - ' . $db->Quote($nextLft);
			}
			$query  .= ' where `id` in (' . $nextChildIds . ')';

			//echo '<br>' . $query;
			$db->setQuery($query);
			$db->query();

			//update current parent
			$query  = 'update `#__discuss_category` set';
			$query  .= ' lft = lft + ' . $db->Quote($newLft);
			$query  .= ', rgt = rgt + ' . $db->Quote($newLft);
			$query  .= ' where `id` in (' . $childIds. ')';

			//echo '<br>' . $query;
			//exit;

			$db->setQuery($query);
			$db->query();

			//now update the ordering.
			$query  = 'update `#__discuss_category` set';
			$query  .= ' `ordering` = `ordering` + 1';
			$query  .= ' where `id` = ' . $db->Quote($this->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			//now update the previous parent's ordering.
			$query  = 'update `#__discuss_category` set';
			$query  .= ' `ordering` = `ordering` - 1';
			$query  .= ' where `id` = ' . $db->Quote($nextParent->id);

			//echo '<br>' . $query;

			$db->setQuery($query);
			$db->query();

			return true;
		}
	}

	public function rebuildOrdering($parentId = null, $leftId = 0 )
	{
		$db = JFactory::getDBO();

		$query  = 'select `id` from `#__discuss_category`';
		$query  .= ' where parent_id = ' . $db->Quote( $parentId );
		$query  .= ' order by lft';

		$db->setQuery( $query );
		$children = $db->loadObjectList();

		// The right value of this node is the left value + 1
		$rightId = $leftId + 1;

		// execute this function recursively over all children
		foreach ($children as $node)
		{
			// $rightId is the current right value, which is incremented on recursion return.
			// Increment the level for the children.
			// Add this item's alias to the path (but avoid a leading /)
			$rightId = $this->rebuildOrdering($node->id, $rightId );

			// If there is an update failure, return false to break out of the recursion.
			if ($rightId === false) return false;
		}

		// We've got the left value, and now that we've processed
		// the children of this node we also know the right value.
		$updateQuery    = 'update `#__discuss_category` set';
		$updateQuery    .= ' `lft` = ' . $db->Quote( $leftId );
		$updateQuery    .= ', `rgt` = ' . $db->Quote( $rightId );
		$updateQuery    .= ' where `id` = ' . $db->Quote($parentId);

		$db->setQuery($updateQuery);

		// If there is an update failure, return false to break out of the recursion.
		if (! $db->query())
		{
			return false;
		}

		// Return the right value of this node + 1.
		return $rightId + 1;
	}

	public function getAssignedACL( $type = 'group' )
	{
		$db = JFactory::getDBO();

		$acl    = array();

		$query  = 'SELECT a.`category_id`, a.`content_id`, a.`status`, b.`id` as `acl_id`';
		$query  .= ' FROM `#__discuss_category_acl_map` as a';
		$query  .= ' LEFT JOIN `#__discuss_category_acl_item` as b';
		$query  .= ' ON a.`acl_id` = b.`id`';
		$query  .= ' WHERE a.`category_id` = ' . $db->Quote( $this->id );
		$query  .= ' AND a.`type` = ' . $db->Quote( $type );

		// echo $query;

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		if( count($result) > 0 )
		{
			$acl    = null;
			if( $type == 'group' )
			{
				$joomlaGroups    = DiscussHelper::getJoomlaUserGroups();
				
			    if( DiscussHelper::getJoomlaVersion() < '1.6' )
			    {
			        $guest  = new stdClass();
			        $guest->id		= '0';
			        $guest->name	= 'Public';
			        $guest->level	= '0';
			        array_unshift($joomlaGroups, $guest);
			    }
				
				$acl             = $this->_mapRules($result, $joomlaGroups);
			}
			else
			{
				$users    		 = $this->getAclUsers( $result );
				$acl             = $this->_mapRules($result, $users);
			}

			return $acl;
		}
		else
		{
			return null;
		}

	}

	function getAclUsers( $aclUsers )
	{
		$db = JFactory::getDBO();

		$users  = array();

		foreach( $aclUsers as $item)
		{
			$users[] = $item->content_id;
		}

		$userlist   = '';

		foreach($users as $user)
		{
			$userlist .= ( $userlist == '') ? $db->Quote($user) : ', ' . $db->Quote($user);
		}


		$query  = 'select id, name from `#__users` where `id` IN (' . $userlist . ')';
		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	public function saveACL( $post )
	{
		$catRuleItems	= JTable::getInstance( 'CategoryAclItem' , 'Discuss' );
		$categoryRules  = $catRuleItems->getAllRuleItems();
		$itemtypes   	= array('group', 'user');

		foreach( $categoryRules as $rule)
		{

			foreach( $itemtypes as $type )
			{
				$key    = 'acl_'.$type.'_'.$rule->action;
				if( isset( $post[ $key ] ) )
				{
					if( count( $post[ $key ] ) > 0)
					{
						foreach( $post[ $key ] as $contendid)
						{
							//now we reinsert again.
							$catRule	= JTable::getInstance( 'CategoryAclMap' , 'Discuss' );

							$catRule->category_id	= $this->id;
							$catRule->acl_id 		= $rule->id;
							$catRule->type 			= $type;
							$catRule->content_id 	= $contendid;
							$catRule->status 		= '1';
							$catRule->store();
						} //end foreach

					} //end if
				}//end if
			}
		}
	}

	public function deleteACL( $aclId = '' )
	{
		$db = JFactory::getDBO();

		$query  = 'delete from `#__discuss_category_acl_map`';
		$query	.= ' where `category_id` = ' . $db->Quote( $this->id );
		if( !empty($aclId) )
			$query	.= ' and `acl_id` = ' . $db->Quote( $aclId );

		$db->setQuery( $query );
		$db->query();

		return true;
	}

	public function _mapRules( $catRules, $joomlaGroups)
	{
		$db 	= JFactory::getDBO();
		$acl    = array();

		$query  = 'select * from `#__discuss_category_acl_item` order by id';
		$db->setQuery( $query );

		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $item )
		{
			$aclId 		= $item->id;
			$default    = $item->default;

			foreach( $joomlaGroups as $joomla )
			{
				$groupId    	= $joomla->id;
				$catRulesCnt    = count($catRules);
				//now match each of the catRules
				if( $catRulesCnt > 0)
				{
					$cnt    = 0;
					foreach( $catRules as $rule)
					{
						if($rule->acl_id == $aclId && $rule->content_id == $groupId)
						{
							$acl[$aclId][$groupId]->status  	= $rule->status;
							$acl[$aclId][$groupId]->acl_id  	= $aclId;
							$acl[$aclId][$groupId]->groupname	= $joomla->name;
							$acl[$aclId][$groupId]->groupid		= $groupId;
							break;
						}
						else
						{
							$cnt++;
						}
					}

					if( $cnt == $catRulesCnt)
					{
						//this means the rules not exist in this joomla group.
						$acl[$aclId][$groupId]->status  	= '0';
						$acl[$aclId][$groupId]->acl_id  	= $aclId;
						$acl[$aclId][$groupId]->groupname	= $joomla->name;
						$acl[$aclId][$groupId]->groupid		= $groupId;
					}
				}
				else
				{
					$acl[$aclId][$groupId]->status  	= $default;
					$acl[$aclId][$groupId]->acl_id  	= $aclId;
					$acl[$aclId][$groupId]->groupname	= $joomla->name;
					$acl[$aclId][$groupId]->groupid		= $groupId;
				}
			}
		}

		return $acl;
	}

	public function canAccess()
	{
		$privCats   = DiscussHelper::getPrivateCategories();

		if( in_array( $this->id,  $privCats) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function canReply()
	{
		$privCats   = DiscussHelper::getPrivateCategories( DISCUSS_CATEGORY_ACL_ACTION_REPLY );

		if( in_array( $this->id,  $privCats) )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	public function getPathway()
	{
		$obj		= new stdClass();
		$obj->link	= $this->getPermalink();
		$obj->title	= $this->getTitle();

		$data		= array( $obj );

		// @task: Detects if it has any parent.
		if( !$this->parent_id )
		{
			return $data;
		}

		$this->getNestedPathway( $this->parent_id , $data );

		// Reverse the data so we get it in a proper order.
		$data	= array_reverse( $data );

		return $data;
	}

	private function getNestedPathway( $parent , &$data )
	{
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $parent );

		$obj		= new stdClass();
		$obj->title	= $category->getTitle();
		$obj->link	= $category->getPermalink();

		$data[]		= $obj;

		if( $category->parent_id )
		{
			$this->getNestedPathway( $category->parent_id , $data );
		}
	}

	public function loadParams()
	{
		$this->_params 	= new JParameter( $this->params );	
	}

	/**
	 * Returns parameter values.
	 *
	 * @access 	public
	 * @param 	string $index 	The parameter key
	 * @return 	mixed
	 **/
	public function getParam( $index , $default = '')
	{
		if( !isset( $this->_params ) )
		{
			$this->loadParams();
		}

		return $this->_params->get( $index , $default );
	}
}
