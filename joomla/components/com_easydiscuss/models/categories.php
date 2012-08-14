<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class EasyDiscussModelCategories extends JModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	var $_data = null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Method to get the total nr of the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the categories
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildQueryWhere();
		$orderby	= $this->_buildQueryOrderBy();
		$db			= $this->getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' )
				. $where . ' '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= $this->getDBO();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );

		$where = array();

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' );
			}
			else if ($filter_state == 'U' )
			{
				$where[] = $db->nameQuote( 'published' ) . '=' . $db->Quote( '0' );
			}
		}

		if ($search)
		{
			$where[] = ' LOWER( title ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_order', 		'filter_order', 	'lft', 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ordering';

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData( $usePagination = true )
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			if($usePagination)
				$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			else
				$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}


	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	function publish( $categories = array(), $publish = 1 )
	{
		if( count( $categories ) > 0 )
		{
			$db		= $this->getDBO();

			$tags	= implode( ',' , $categories );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_category' ) . ' '
					. 'SET ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( $publish ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . ' IN (' . $tags . ')';
			$db->setQuery( $query );

			if( !$db->query() )
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Returns the number of blog entries created within this category.
	 *
	 * @return int	$result	The total count of entries.
	 * @param boolean	$published	Whether to filter by published.
	 */
	function getUsedCount( $categoryId , $published = false )
	{
		$db			= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $categoryId );

		if( $published )
		{
			$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		}

		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
			$query .= ' AND `private` = ' . $db->Quote(BLOG_PRIVACY_PUBLIC);

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}


	public function getCategoryTree()
	{
		$db     = JFactory::getDBO();
		$my 	= JFactory::getUser();

        $config     = DiscussHelper::getConfig();
		$sortConfig = $config->get('layout_sorting_category','latest');
		
		$queryExclude	= '';
		$excludeCats	= array();

		// get all private categories id
		$excludeCats	= DiscussHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
			$queryExclude .= ' AND a.`id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT a.*, ';
		$query	.= ' ( SELECT COUNT(id) FROM ' . $db->nameQuote( '#__discuss_category' );
		$query	.= ' WHERE lft < a.lft AND rgt > a.rgt AND a.lft != ' . $db->Quote( 0 ) . ' ) AS depth ';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_category' ) . ' AS a ';
		$query	.= ' WHERE a.`published`=' . $db->Quote( DISCUSS_ID_PUBLISHED );
		$query	.= $queryExclude;
		
		switch( $sortConfig )
		{
		    case 'ordering':
		 		$query	.= ' ORDER BY `lft`, `ordering`';
		 		break;
			case 'alphabet':
		 		$query	.= ' ORDER BY `title`, `lft`';
		 		break;
		    case 'latest':
		    default:
		        $query	.= ' ORDER BY `rgt` DESC';
				break;
		}
		
		// echo $query;

		$db->setQuery( $query );

		$rows		= $db->loadObjectList();
		$total		= count( $rows );
		$categories = array();

		for( $i = 0; $i < $total; $i++ )
		{
			$category	= DiscussHelper::getTable( 'Category' );
			$category->bind( $rows[ $i ] );
			$category->depth	= $rows[ $i ]->depth;
			$categories[]		= $category;
		}
		return $categories;
	}

	public function getCategories( $parentId = 0 )
	{
		$db     = JFactory::getDBO();
		$my     = JFactory::getUser();

		$query  = 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) ;
		$query  .= ' WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $parentId );
		$query  .= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );

		if( $my->id == 0)
		{
			$query  .= ' AND ' . $db->nameQuote( 'private' ) . '!=' . $db->Quote( '1' );
		}
// 		else
// 		{
// 			//check categories acl here.
// 			$catIds  = DiscussHelper::getAclCategories(DISCUSS_CATEGORY_ACL_ACTION_VIEW, $my->id, $parentId);
//
// 			if( count($catIds) > 0 )
// 			{
// 				$strIds = '';
// 				foreach( $catIds as $cat )
// 				{
// 					$strIds = ( empty( $strIds ) ) ? $cat->id : $strIds . ', ' . $cat->id;
// 				}
//
// 				$query .= ' and `id` not in (';
// 				$query .= $strIds;
// 				$query .= ')';
// 			}
// 		}
		
		//check categories acl here.
		$catIds  = DiscussHelper::getAclCategories(DISCUSS_CATEGORY_ACL_ACTION_VIEW, $my->id, $parentId);

		if( count($catIds) > 0 )
		{
			$strIds = '';
			foreach( $catIds as $cat )
			{
				$strIds = ( empty( $strIds ) ) ? $cat->id : $strIds . ', ' . $cat->id;
			}

			$query .= ' and `id` not in (';
			$query .= $strIds;
			$query .= ')';
		}


		$query  .= ' order by `lft`';

		// echo $query;

		$db->setQuery( $query );

		$rows		= $db->loadObjectList();
		$total		= count( $rows );
		$categories = array();

		for( $i = 0; $i < $total; $i++ )
		{
			$category   = DiscussHelper::getTable( 'Category' );
			$category->bind( $rows[ $i ] );

			$categories[]   = $category;
		}
		return $categories;
	}

	function getParentCategories($contentId, $type = 'all', $isPublishedOnly = false, $showPrivateCat = true)
	{
		$db 		= JFactory::getDBO();
		$my     	= JFactory::getUser();
		$config 	= DiscussHelper::getConfig();
		$mainframe  = JFactory::getApplication();

		$sortConfig = $config->get('layout_sorting_category','latest');

		$query	= 	'select a.`id`, a.`title`, a.`alias`, a.`private`,a.`default`';
		$query	.=  ' from `#__discuss_category` as a';
		$query	.=  ' where a.parent_id = ' . $db->Quote('0');

		if($type == 'poster')
		{
			$query	.=  ' and a.created_by = ' . $db->Quote($contentId);
		}
		else if($type == 'category')
		{
			$query	.=  ' and a.`id` = ' . $db->Quote($contentId);
		}

		if( $isPublishedOnly )
		{
			$query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		if ( !$mainframe->isAdmin() )
		{
			// we do not need to see the privacy when user accessing category via backend because only admin can access it.
			// in a way, we do not resttict for admin.

			if( !$showPrivateCat)
			{
				$query	.=  ' and a.`private` = ' . $db->Quote('0');
			}
			else
			{
				//check categories acl here.
				$catIds  = DiscussHelper::getAclCategories(DISCUSS_CATEGORY_ACL_ACTION_SELECT, $my->id, '0');

				if( count($catIds) > 0 )
				{
					$strIds = '';
					foreach( $catIds as $cat )
					{
						$strIds = ( empty( $strIds ) ) ? $cat->id : $strIds . ', ' . $cat->id;
					}

					$query .= ' and a.id not in (';
					$query .= $strIds;
					$query .= ')';
				}

			}

		}

		switch($sortConfig)
		{
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`ordering` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query  .= $orderBy;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getChildIds( $parentId = 0 )
	{
		$categories = array();
		$this->getNestedIds( $parentId , $categories );

		return $categories;
	}

	private function getNestedIds( $parentId , & $result )
	{
		$db     = JFactory::getDBO();

		$query  = 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) .'=' . $db->Quote( $parentId );

		$db->setQuery( $query );
		$categories		= $db->loadObjectList();

		if( $categories )
		{
			foreach( $categories as $category )
			{
				$result[]	=  $category->id;
				$this->getNestedIds( $category->id , $result );
			}
		}
	}

	function getChildCategories($parentId , $isPublishedOnly = false, $includePrivate = true)
	{
		$db 		= JFactory::getDBO();
		$my     	= JFactory::getUser();
		$config 	= DiscussHelper::getConfig();
		$mainframe  = JFactory::getApplication();

		$sortConfig = $config->get('layout_sorting_category','latest');

		$query	= 	'select a.`id`, a.`title`, a.`alias`, a.`private`,a.`default`';
		$query	.=  ' from `#__discuss_category` as a';
		$query	.=  ' where a.parent_id = ' . $db->Quote($parentId);

		if( $isPublishedOnly )
		{
			$query	.=  ' and a.`published` = ' . $db->Quote('1');
		}

		if ( !$mainframe->isAdmin() )
		{

			if( !$includePrivate )
			{
				//check categories acl here.
				$catIds  = DiscussHelper::getAclCategories(DISCUSS_CATEGORY_ACL_ACTION_VIEW, $my->id, $parentId);

				if( count($catIds) > 0 )
				{
					$strIds = '';
					foreach( $catIds as $cat )
					{
						$strIds = ( empty( $strIds ) ) ? $cat->id : $strIds . ', ' . $cat->id;
					}

					$query .= ' and a.id not in (';
					$query .= $strIds;
					$query .= ')';
				}
			}

		}

		switch($sortConfig)
		{
			case 'alphabet' :
				$orderBy = ' ORDER BY a.`title` ASC';
				break;
			case 'ordering' :
				$orderBy = ' ORDER BY a.`ordering` ASC';
				break;
			case 'latest' :
			default	:
				$orderBy = ' ORDER BY a.`created` DESC';
				break;
		}

		$query  .= $orderBy;

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function getPrivateCategories()
	{
		$db 	= JFactory::getDBO();


		$query	= 	'select a.`id`';
		$query	.=  ' from `#__discuss_category` as a';
		$query	.=  ' where a.`private` = ' . $db->Quote('1');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	function getChildCount( $categoryId , $published = false )
	{
		$db			= $this->getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $categoryId );

		if( $published )
		{
			$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		}

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}

}
