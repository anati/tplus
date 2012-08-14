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

class EasyDiscussModelPosts extends JModel
{
	/**
	 * Post total
	 *
	 * @var integer
	 */
	var $_total 	= null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Post data array
	 *
	 * @var array
	 */
	var $_data 		= null;

	/**
	 * Parent ID
	 *
	 * @var integer
	 */
	var $_parent	= null;
	var $_isaccept	= null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.limit', 'limit', DiscussHelper::getListLimit() );
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
	function getTotal($sort, $filter, $category='', $featuredOnly = 'all')
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery($sort, $filter, $category, $featuredOnly);
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the posts
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination( $parent_id = 0, $sort = 'latest', $filter='', $category='', $featuredOnly = 'all' )
	{
		$this->_parent	= $parent_id;

		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			$this->_pagination	= DiscussHelper::getPagination( $this->getTotal($sort, $filter, $category, $featuredOnly), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Method to build the query for the tags
	 *
	 * @access private
	 * @return string
	 */
	function _buildQuery($sort = 'latest', $filter = '' , $category = '', $featuredOnly = 'all' , $reply = false )
	{
	    $my 	= JFactory::getUser();
	    $config = DiscussHelper::getConfig();

		// Get the WHERE and ORDER BY clauses for the query

		if(empty($this->_parent))
		{
			$parent_id = JRequest::getInt('parent_id', 0);
			$this->_parent = $parent_id;
		}

		$filteractive	= (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;
		$where		= $this->_buildQueryWhere( $filter , $category, $featuredOnly );
		$db			= $this->getDBO();

		$orderby    		= '';
		$queryExclude		= '';
		$excludeCats    	= array();

        $date       = JFactory::getDate();

        // We do not need to check for private categories for replies since replies are posted in that particular discussion.
        if( !$reply )
        {
        	$excludeCats    = DiscussHelper::getPrivateCategories();
        }

		if(! empty($excludeCats))
		{
		    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		//$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `noofdays`, ';
		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created` ) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `daydiff`, ';
		$query	.= ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) ) as `timediff`,';

		//sorting criteria
		if($sort == 'likes')
		{
			$query  .= ' a.`num_likes` as `likeCnt`,';
		}

		if($sort == 'popular')
		{
		    $query  .= ' count(c.id) as `PopularCnt`,';
		}

		if($sort == 'voted')
		{
		    $query  .= ' a.`sum_totalvote` as `VotedCnt`,';
		}

		if($my->id != 0)
		{
		    $query  .= ' count(d.id) as `isVoted`,';
		}
		else
		{
		    $query  .= ' ' . $db->Quote('0') . ' as `isVoted`,';
		}


		$query	.= ' a.*, count(c.id) as `num_replies`, e.`title` AS `category`,';
		$query	.= ' IF(a.`replied` = '.$db->Quote('0000-00-00 00:00:00') . ', a.`created`, a.`replied`) as `lastupdate`';
		
		
		//$query	.= ', count(b.id) as `total_vote_cnt`';
		$query	.= ', (select count(1) from `#__discuss_votes` where post_id = a.id) as `total_vote_cnt`';
		
		$query	.= ' FROM `#__discuss_posts` AS a';
		// $query	.= '    LEFT JOIN `#__discuss_votes` AS b ON a.`id` = b.`post_id`';
		$query	.= '    LEFT JOIN `#__discuss_posts` AS c ON a.`id` = c.`parent_id`';
		$query	.= '        AND c.`published` = ' . $db->Quote('1');

		if($my->id != 0)
		{
			$query	.= '    LEFT JOIN `#__discuss_votes` AS d ON a.`id` = d.`post_id`';
			$query	.= '        AND d.`user_id` = ' . $db->Quote($my->id);
		}

		$query  .= '	LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS e ON a.`category_id`=e.`id`';

		$query	.= $where;

		if(! empty($this->_isaccept))
		{
            $query	.= ' AND a.`answered` = ' . $db->Quote( '1' );
		}

		if( $filteractive == 'unanswered' )
		{
		    // Should not fetch posts which are resolved
		    $query  .= ' AND a.`isresolve`=' . $db->Quote( 0 );
		}

		$query	.= $queryExclude;
		
		if( $featuredOnly && $config->get('layout_featuredpost_style') != '0' && empty($this->_parent) )
		{
			switch( $config->get('layout_featuredpost_sort', 'date_latest') )
			{
				case 'date_oldest':
				    $orderby    = ' ORDER BY a.`replied` ASC'; //used in getdata only
				    break;
				case 'order_asc':
				    $orderby    = ' ORDER BY a.`ordering` ASC'; //used in getreplies only
				    break;
				case 'order_desc':
				    $orderby	= ' ORDER BY a.`ordring` DESC'; //used in getdate and getreplies
				    break;
				case 'date_latest':
			    default:
			        $orderby    = ' ORDER BY a.`replied` DESC'; //used in getsticky and get created date
			        break;
			}
		}
		else
		{
			switch($sort)
			{
				case 'popular':
				    $orderby	= ' ORDER BY `PopularCnt` DESC, a.`created` DESC'; //used in getdata only
				    break;
				case 'hits':
				    $orderby    = ' ORDER BY a.`hits` DESC'; //used in getdata only
				    break;
				case 'voted':
				    $orderby    = ' ORDER BY a.`sum_totalvote` DESC, a.`created` DESC'; //used in getreplies only
				    break;
				case 'likes':
				    $orderby	= ' ORDER BY a.`num_likes` DESC, a.`created` DESC'; //used in getdate and getreplies
				    break;
				case 'activepost':
					$orderby    = ' ORDER BY a.`replied` DESC'; //used in getsticky and getlastreply
					break;
				case 'featured':
					$orderby    = ' ORDER BY a.`featured` DESC, a.`created` DESC'; //used in getsticky and getlastreply
					break;
				case 'replylatest':
				    $orderby    = ' ORDER BY a.`created` ASC'; //used in discussion replies
				    break;
			    case 'latest':
			    default:
			        $orderby    = ' ORDER BY a.`replied` DESC'; //used in getsticky and get created date
			        break;
			}
		}


		if($filteractive == 'unanswered')
		{
		    $query	.= ' GROUP BY a.`id` HAVING(COUNT(c.id) = 0)';
		}
		else
		{
		    $query	.= ' GROUP BY a.`id`';
		}

		$query	.= $orderby;

		//echo $query ;exit;//. '<br /><br />';

		return $query;
	}

	function _buildQueryWhere($filter='' , $category = '', $featuredOnly = 'all' )
	{
		$mainframe	= JFactory::getApplication();
		$db			= $this->getDBO();

		$search			= $db->getEscaped( JRequest::getString( 'query' , '' ) );
		$filteractive	= (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;
		$where 			= array();


        $where[] = ' a.`published` = ' . $db->Quote('1');

		// get all posts where parent_id = 0
		if(empty($this->_parent))
		{
			$this->_parent  = '0';
		}

		if($filteractive == 'featured' || $featuredOnly === true)
		{
		    $where[] = ' a.`featured` = ' . $db->Quote('1');
		}
		else if( $featuredOnly === false )
		{
		    $where[] = ' a.`featured` = ' . $db->Quote('0');
		}

		if( $filteractive == 'myposts' )
		{
		    $my     = JFactory::getUser();
		    $where[]    = ' a.`user_id`= ' .$db->Quote( $my->id );
		}

		if( $filteractive == 'new' )
		{
			$config 	= DiscussHelper::getConfig();
			$where[]    = ' DATEDIFF( ' . $db->Quote( JFactory::getDate()->toMySQL() ) . ', a.`created` ) <= ' . $db->Quote( $config->get( 'layout_daystostaynew' ) );
		}

		if( $filteractive == 'myreplies' )
		{
		    $my     = JFactory::getUser();
		    $where[]    = ' c.`user_id`= ' .$db->Quote( $my->id );
		}

		if($this->_parent=='allreplies')
		{
			$where[] = ' a.`parent_id` != ' . $db->Quote( '0' );
		}
		else
		{
			$where[] = ' a.`parent_id` = ' . $db->Quote( $this->_parent );
			if( $this->_isaccept )
			    $where[] = ' a.`answered` = ' . $db->Quote( '1' );
			else
				$where[] = ' a.`answered` = ' . $db->Quote( '0' );
		}

		if ($search)
		{
			$where[] = ' LOWER( a.`title` ) LIKE \'%' . $search . '%\' ';
		}

		// Filter by category
		if( !empty( $category ) )
		{
		    require_once( dirname(__FILE__) . DS . 'categories.php' );

		    // Fetch all subcategories from within this category
			$model	= $this->getInstance( 'Categories' , 'EasyDiscussModel' );
			$childs	= $model->getChildIds( $category );

			if( $childs )
			{
			    $childs[]   = $category;

				foreach( $childs as &$child )
				{
				    $child  = $db->Quote( $child );
				}

				$where[]    = ' a.`category_id` IN (' . implode( ',' , $childs ) . ')';
			}
			else
			{
			    $where[]    = ' a.`category_id`=' . $db->Quote( $category );
			}
		}
		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order', 		'filter_order', 	'created DESC'	, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order_Dir',	'filter_order_Dir',	''				, 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get posts item data
	 *
	 * @access public
	 * @return array
	 */
	function getData( $usePagination = true, $sort = 'latest' , $limitstart = null, $filter = '' , $category = '', $limit = null, $featuredOnly = 'all' )
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery( $sort, $filter , $category, $featuredOnly );

			//echo '<br />' . $query . '<br /><br />';exit;

			if($usePagination)
			{
				$limitstart		= is_null( $limitstart ) ? $this->getState( 'limitstart') : $limitstart;
				$limit			= is_null( $limit ) ? $this->getState( 'limit') : $limit;
				$this->_data	= $this->_getList($query, $limitstart , $limit);
			}
			else
			{
				$limit			= is_null( $limit ) ? $this->getState( 'limit') : $limit;
				$this->_data	= $this->_getList($query, 0 , $limit);
			}
		}

		return $this->_data;
	}
	
	function clearData()
	{
	    $this->_data = null;
	}


	/**
	 * Method to get replies
	 *
	 * @access public
	 * @return array
	 */
	function getReplies( $id, $sort = 'latest' , $limitstart = null, $limit = null )
	{
	    $db				= JFactory::getDBO();
		$this->_parent	= $id;
		$query			= $this->_buildQuery( $sort , '' , '' , 'all' , true );

		$result = '';
		if( !empty( $limitstart ) )
		{
			if(empty($limit))
			{
				$limit = $this->getState('limit');
			}
			$result			= $this->_getList($query, $limitstart , $limit);
		}
		else
		{
			if( !empty($limit) )
			{
				$result	= $this->_getList($query, 0 , $limit);
			}
			else
			{
				$result	= $this->_getList( $query );
			}
		}

		return $result;
	}

	/**
	 * Method to publish or unpublish categories
	 *
	 * @access public
	 * @return array
	 */
	function publish( $categories = array(), $publish = 1 )
	{
	    $config = DiscussHelper::getConfig();

		if( count( $categories ) > 0 )
		{
			$db		= $this->getDBO();

			$tags	= implode( ',' , $categories );

			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_posts' ) . ' '
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

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'category_id' ) . '=' . $db->Quote( $categoryId );

		if( $published )
		{
			$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		}

		//blog privacy setting
		$my = JFactory::getUser();
		if($my->id == 0)
		    $query .= ' AND `private` = ' . $db->Quote(DISCUSS_PRIVACY_PUBLIC);

		$db->setQuery( $query );

		$result	= $db->loadResult();

		return $result;
	}


	function getPostsBy( $type, $typeId = 0, $sort = 'latest', $limitstart = null , $published = DISCUSS_FILTER_PUBLISHED , $search = '' , $limit = null )
	{
		$db	= JFactory::getDBO();

		$queryPagination	= false;
		$queryWhere		= '';
		$queryOrder		= '';
		$queryLimit		= '';
		$queryWhere		= '';

		switch( $published )
		{

			case DISCUSS_FILTER_PUBLISHED:
			default:
				$queryWhere	= ' WHERE a.`published` = ' . $db->Quote('1');
				break;
		}

		$contentId  = '';
		$isIdArray  = false;
		if(is_array($typeId))
		{
		    if(count($typeId) > 1)
		    {
			    $contentId	= implode(',', $typeId);
			    $isIdArray  = true;
		    }
		    else
		    {
		        $contentId	= $typeId[0];
		    }
		}
		else
		{
		    $contentId  = $typeId;
		}

		switch( $type )
		{
			case 'category':
				$queryWhere	.= ($isIdArray) ? ' AND a.`category_id` IN ('. $contentId .')' : ' AND a.`category_id` = ' . $db->Quote($contentId);
				break;
			case 'user':
				$queryWhere	.= ' AND a.`user_id`=' . $db->Quote( $contentId );
				break;
			default:
				break;
		}

		if( ! empty($search) )
		{
			$queryWhere	.= ' AND a.`title` LIKE ' . $db->Quote( '%' . $search . '%' );
		}


		//getting only main posts.
		$queryWhere	.= ' AND a.`parent_id` = 0';

		switch( $sort )
		{
			case 'latest':
				$queryOrder	= ' ORDER BY a.`created` DESC';
				break;
			case 'popular':
				$queryOrder	= ' ORDER BY a.`hits` DESC';
				break;
			case 'alphabet':
				$queryOrder	= ' ORDER BY a.`title` ASC';
			case 'likes':
				$queryOrder	= ' ORDER BY a.`num_likes` DESC';
				break;
			default :
				break;
		}

		$limitstart		= is_null( $limitstart ) ? $this->getState( 'limitstart') : $limitstart;
		$limit			= is_null( $limit ) ? $this->getState( 'limit' ) : $limit;
		$queryLimit		= ' LIMIT ' . $limitstart . ',' . $limit;

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` AS a';
		$query	.= $queryWhere;

		$db->setQuery( $query );
		$this->_total	= $db->loadResult();

		jimport('joomla.html.pagination');
		// $this->_pagination	= new JPagination( $this->_total , $limitstart , $limit);
		$this->_pagination	= DiscussHelper::getPagination( $this->_total, $limitstart, $limit );


        $date       = JFactory::getDate();

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
		$query	.= ' a.`id`, a.`title`, a.`alias`, a.`created`, a.`modified`, a.`replied`,';
		$query	.= ' a.`content`, a.`category_id`, a.`published`, a.`ordering`, a.`vote`, a.`hits`, a.`islock`,';
		$query	.= ' a.`featured`, a.`isresolve`, a.`isreport`, a.`user_id`, a.`parent_id`,';
		$query	.= ' a.`user_type`, a.`poster_name`, a.`poster_email`, a.`num_likes`,';
		$query	.= ' a.`num_negvote`, a.`sum_totalvote`,';
		$query  .= ' count(b.id) as `num_replies`,';
		$query  .= ' c.`title` AS `category`, a.`password`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a';
		$query	.= '   LEFT JOIN `#__discuss_posts` AS b ON a.`id` = b.`parent_id`';
		$query  .= '   AND b.`published` = 1';
		$query	.= '   LEFT JOIN `#__discuss_category` AS c ON a.`category_id` = c.`id`';

		$query .= $queryWhere;

		$query  .= ' GROUP BY (a.id)';

		$query .= $queryOrder;
		$query .= $queryLimit;
		
		// echo $query;exit;


		$db->setQuery($query);
		if($db->getErrorNum() > 0)
		{
			JError::raiseError( $db->getErrorNum() , $db->getErrorMsg() . $db->stderr());
		}

		$result	= $db->loadObjectList();
		return $result;
	}

	function getLastReply($id)
	{
		$db	= JFactory::getDBO();
		$query = 'SELECT * FROM #__discuss_posts WHERE ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote($id) . ' ORDER BY '  . $db->nameQuote('created') . ' DESC';
		$db->setQuery( $query );
		$result = $db->loadObject();

		return $result;
	}


	function getTotalReplies( $id )
	{
		$db	= JFactory::getDBO();
		$query = 'SELECT COUNT(id) AS `replies` FROM #__discuss_posts WHERE `parent_id` = ' . $db->Quote($id);
		$query  .= ' AND `answered` = ' . $db->Quote( '0' );
		$query  .= ' AND `published` = ' . $db->Quote('1');

		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}


	/**
	 * Method to retrieve blog posts based on the given tag id.
	 *
	 * @access public
	 * @param	int		$tagId	The tag id.
	 * @return	array	$rows	An array of blog objects.
	 */
	function getTaggedPost( $tagId = 0, $sort	= 'latest', $filter = '', $limitStart = '' )
	{
		if( $tagId ==  0 )
			return false;

		$db			= $this->getDBO();
		$limit		= $this->getState('limit');
		$limitstart = (empty($limitStart) ) ? $this->getState('limitstart') : $limitStart;

		$filteractive	= (empty($filter)) ? JRequest::getString('filter', 'allposts') : $filter;

        $date       = JFactory::getDate();

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', b.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', b.`created`) as `timediff`,';
		//sorting criteria
		if($sort == 'likes')
		{
			$query  .= ' b.`num_likes` as `likeCnt`,';
		}

		if($sort == 'popular')
		{
		    $query  .= ' count(c.id) as `PopularCnt`,';
		}

		if($sort == 'voted')
		{
		    $query  .= ' b.`sum_totalvote` as `VotedCnt`,';
		}

		$query	.= ' b.`id`, b.`title`, b.`alias`, b.`created`, b.`modified`, b.`replied`,';
		$query	.= ' b.`content`, b.`published`, b.`ordering`, b.`vote`, b.`hits`, b.`islock`,';
		$query	.= ' b.`featured`, b.`isresolve`, b.`isreport`, b.`user_id`, b.`parent_id`,';
		$query	.= ' b.`user_type`, b.`poster_name`, b.`poster_email`, b.`num_likes`,';
		$query	.= ' b.`num_negvote`, b.`sum_totalvote`, b.`category_id`, d.`title` AS category, b.`password`, ';
		$query	.= ' count(c.id) as `num_replies`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' AS a ';
		$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b ';
		$query	.= ' ON a.post_id=b.id ';
		$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS c ';
		$query	.= ' ON b.id=c.parent_id ';
		$query	.= ' AND c.`published` = ' . $db->Quote('1');
		$query  .= ' INNER JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS d ';
		$query  .= ' ON d.`id`=b.`category_id` ';
		$query	.= ' WHERE a.tag_id = ' . $db->Quote( $tagId );
		$query	.= ' AND b.`published` = ' . $db->Quote('1');
		if($filteractive == 'featured')
		{
		    $query .= ' AND b.`featured` = ' . $db->Quote('1');
		}

        $orderby = '';
		switch($sort)
		{
			case 'popular':
			    $orderby	= ' ORDER BY `PopularCnt` DESC, b.created DESC'; //used in getdata only
			    break;
			case 'hits':
			    $orderby    = ' ORDER BY b.hits DESC'; //used in getdata only
			    break;
			case 'voted':
			    $orderby    = ' ORDER BY b.`sum_totalvote` DESC, b.created DESC'; //used in getreplies only
			    break;
			case 'likes':
			    $orderby	= ' ORDER BY b.`num_likes` DESC, b.created DESC'; //used in getdate and getreplies
			    break;
			case 'activepost':
				$orderby    = ' ORDER BY b.featured DESC, b.replied DESC'; //used in getsticky and getlastreply
				break;
			case 'featured':
		    case 'latest':
		    default:
		        $orderby    = ' ORDER BY b.featured DESC, b.created DESC'; //used in getsticky and get created date
		        break;
		}

		if($filteractive == 'unanswered')
		{
		    $query	.= ' GROUP BY b.`id` HAVING(COUNT(c.id) = 0)';
		}
		else
		{
		    $query	.= ' GROUP BY b.`id`';
		}

		$query  .= $orderby;
		
		
		// echo $query;exit;
		

		//total tag's post sql
		$totalQuery = 'SELECT COUNT(1) FROM (';
		$totalQuery .= $query;
		$totalQuery .= ') as x';

		$query	.= ' LIMIT ' . $limitstart . ',' . $limit;

		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		$db->setQuery( $totalQuery );
		$db->loadResult();
		$this->_total	= $db->loadResult();

		$this->_pagination	= DiscussHelper::getPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		return $rows;
	}

	/**
	 * Get all child posts based on parent_id given
	 */
	function getAllReplies($parent_id)
	{
		$db = JFactory::getDBO();

	    $query = 'SELECT * FROM #__discuss_posts WHERE ' .  $db->nameQuote('parent_id') . ' = ' . $db->Quote($parent_id);
	    $query  .= ' AND `published` = 1';

	    $db->setQuery($query);

	    return $db->loadObjectList();
	}

	function deleteAllReplies($parent_id)
	{
		$db = JFactory::getDBO();

	    $query = 'DELETE FROM #__discuss_posts WHERE ' .  $db->nameQuote('parent_id') . ' = ' . $db->Quote($parent_id);
	    $db->setQuery($query);

	    return $db->query();
	}

	function getNegativeVote( $postId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'SELECT COUNT(1) FROM `#__discuss_votes`';
	    $query  .= ' WHERE `post_id` = ' . $db->Quote($postId);
	    $query  .= ' AND `value` = ' . $db->Quote('-1');

	    $db->setQuery($query);

	    $result = $db->loadResult();

	    return $result;
	}

	function getComments($postId)
	{
	    $db 	= JFactory::getDBO();

	    $date   = JFactory::getDate();

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
	    $query  .= ' a.* FROM `#__discuss_comments` AS a WHERE a.`post_id` = ' . $db->Quote($postId);
	    $query  .= ' ORDER BY a.`created` ASC';
	    $db->setQuery($query);

	    $result = $db->loadObjectList();

	    return $result;
	}

	/**
	 * Method to get replies
	 *
	 * @access public
	 * @return array
	 */
	function getAcceptedReply( $id )
	{
	    $db				= JFactory::getDBO();
		$this->_parent		= $id;
		$this->_isaccept    = true;

		$sort 			= 'latest';
		$query			= $this->_buildQuery($sort);
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

    function getUnansweredCount( $filter = '' , $category = '' , $tagId = '', $featuredOnly = 'all' )
    {
    	$db		= JFactory::getDBO();
        $my 	= JFactory::getUser();

		$queryExclude		= '';
		$excludeCats    	= array();

		// get all private categories id
		$excludeCats    = DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
		    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


		$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';
		$query	.= '  LEFT JOIN `#__discuss_posts` AS b';
		$query	.= '    ON a.`id`=b.`parent_id`';
		$query	.= '    AND b.`published`=' . $db->Quote('1');

    	if(! empty($tagId))
    	{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query	.= ' 	ON a.`id` = c.`post_id`';
			$query	.= ' 	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published`=' . $db->Quote('1');

		// @rule: Should not calculate resolved posts
		$query  .= ' AND a.`isresolve`=' . $db->Quote( 0 );
		
		if( $featuredOnly === true )
		{
		    $query	.= ' AND a.`featured`=' . $db->Quote('1');
		}
		else if( $featuredOnly === false)
		{
		    $query	.= ' AND a.`featured`=' . $db->Quote('0');
		}

    	if( $category )
    	{
    	    $model	= JModel::getInstance( 'Categories' , 'EasyDiscussModel' );
    	    $childs	= $model->getChildIds( $category );
    	    $childs[]   = $category;
    	    $query	.= ' AND a.`category_id` IN (' . implode( ',' , $childs ) . ')';
		}
		$query	.= ' AND b.`id` IS NULL';
		$query  .= $queryExclude;

		$db->setQuery( $query );

		return $db->loadResult();
	}

    function getNewCount( $filter = '' , $category = '' , $tagId = '', $featuredOnly = 'all' )
    {
    	$db		= JFactory::getDBO();
        $my 	= JFactory::getUser();

		$queryExclude		= '';
		$excludeCats    	= array();

		// get all private categories id
		$excludeCats    = DiscussHelper::getPrivateCategories();
		if(! empty($excludeCats))
		{
		    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}

		$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';
		$query	.= '  LEFT JOIN `#__discuss_posts` AS b';
		$query	.= '    ON a.`id`=b.`parent_id`';
		$query	.= '    AND b.`published`=' . $db->Quote('1');

    	if(! empty($tagId))
    	{
			$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
			$query	.= ' 	ON a.`id` = c.`post_id`';
			$query	.= ' 	AND c.`tag_id` = ' . $db->Quote($tagId);
		}

		$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
		$query	.= ' AND a.`published`=' . $db->Quote('1');
		
		if( $featuredOnly === true )
		{
		    $query	.= ' AND a.`featured`=' . $db->Quote('1');
		}
		else if( $featuredOnly === false)
		{
		    $query	.= ' AND a.`featured`=' . $db->Quote('0');
		}

		$config 	= DiscussHelper::getConfig();
		$query  .= ' AND ' . $db->nameQuote( 'DATEDIFF( ' . $db->Quote( JFactory::getDate()->toMySQL() ) . ', a.`created` )' ) . ' <= ' . $db->Quote( $config->get( 'layout_daystostaynew' ) );

    	if( $category )
    	{
    	    $model	= JModel::getInstance( 'Categories' , 'EasyDiscussModel' );
    	    $childs	= $model->getChildIds( $category );
    	    $childs[]   = $category;
    	    $query	.= ' AND a.`category_id` IN (' . implode( ',' , $childs ) . ')';
		}

		$query	.= ' AND b.`id` IS NULL';
		$query  .= $queryExclude;


		$db->setQuery( $query );

		return $db->loadResult();
	}
	
	
    function getFeaturedCount( $filter = '' , $category = '' , $tagId = '' )
    {
        $db = JFactory::getDBO();
        $my = JFactory::getUser();

		$queryExclude		= '';
		$excludeCats    	= array();

		// get all private categories id
		$excludeCats    = DiscussHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
		    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


        $query  = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';

        if(! empty($tagId))
		{
            $query  .= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
            $query  .= ' AND b.`tag_id` = ' . $db->Quote($tagId);
        }

        $query  .= ' WHERE a.`featured` = ' . $db->Quote('1');
        $query  .= ' AND a.`parent_id` = ' . $db->Quote('0');
        $query  .= ' AND a.`published` = ' . $db->Quote('1');
		if( $category )
		{
		    $query  .= ' AND a.`category_id`=' . $db->Quote( $category );
		}
		$query	.=	$queryExclude;

        $db->setQuery($query);

        $result = $db->loadResult();

        return $result;
    }
    
    function getFeaturedPosts( $category = '' )
    {
        $db = JFactory::getDBO();
        $my = JFactory::getUser();

		$queryExclude		= '';
		$excludeCats    	= array();

		// get all private categories id
		$excludeCats    = DiscussHelper::getPrivateCategories();

		if(! empty($excludeCats))
		{
		    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
		}


        $query  = 'SELECT a.* FROM `#__discuss_posts` AS a';

        if(! empty($tagId))
		{
            $query  .= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
            $query  .= ' AND b.`tag_id` = ' . $db->Quote($tagId);
        }

        $query  .= ' WHERE a.`featured` = ' . $db->Quote('1');
        $query  .= ' AND a.`parent_id` = ' . $db->Quote('0');
        $query  .= ' AND a.`published` = ' . $db->Quote('1');
		if( $category )
		{
		    $query  .= ' AND a.`category_id`=' . $db->Quote( $category );
		}
		$query	.=	$queryExclude;

        $db->setQuery($query);

        $result = $db->loadResult();

        return $result;
    }

    /**
     * Retrieve replies from a specific user
     **/
    public function getRepliesFromUser( $userId )
	{
		$db		= JFactory::getDBO();
		$date	= JFactory::getDate();
		
		$limitstart = $this->getState('limitstart');
		$limit 		= $this->getState('limit');

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', b.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', b.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', b.`created`) as `timediff`,';
		$query	.= ' b.`id`, b.`title`, b.`alias`, b.`created`, b.`modified`, b.`replied`,';
		$query	.= ' b.`content`, b.`category_id`, b.`published`, b.`ordering`, b.`vote`, a.`hits`, b.`islock`,';
		$query	.= ' b.`featured`, b.`isresolve`, b.`isreport`, b.`user_id`, b.`parent_id`,';
		$query	.= ' b.`user_type`, b.`poster_name`, b.`poster_email`, b.`num_likes`,';
		$query	.= ' b.`num_negvote`, b.`sum_totalvote`,';
		$query  .= ' count(a.id) as `num_replies`,';
		$query	.= ' c.`title` as `category`, b.`password`';
		$query	.= ' FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a ';
		$query	.= ' INNER JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS b ';
		$query	.= ' ON a.' . $db->nameQuote( 'parent_id' ) . ' = b.' . $db->nameQuote( 'id' );
		$query	.= ' LEFT JOIN ' . $db->nameQuote( '#__discuss_category' ) . ' AS c';
		$query	.= ' ON c.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'category_id' );
		$query	.= ' WHERE a.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$query	.= ' AND a.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 );
		
		$query  .= ' AND a.`parent_id` != ' . $db->Quote('0');
		$query	.= ' AND b.' . $db->nameQuote( 'published' ) . ' = ' . $db->Quote( 1 );
		$query  .= ' AND b.`parent_id` = ' . $db->Quote('0');
		
		$query	.= ' GROUP BY b.`id`';

		
		$this->_total = $this->_getListCount($query);
		
		
		// echo $query;exit;
		$this->_pagination	= DiscussHelper::getPagination( $this->_total, $limitstart, $limit );
		
		$this->_data		= $this->_getList($query, $limitstart , $limit);
		
		return $this->_data;
	}

	function getUserReplies( $postId, $excludeLastReplyUser	= false )
	{
	    $db = JFactory::getDBO();

	    $repliesUser    = '';
	    $lastReply      = '';

	    if( $excludeLastReplyUser )
	    {
	    	$query  = 'SELECT `id`, `user_id`, `poster_name`, `poster_email` FROM `#__discuss_posts` where parent_id = ' . $db->Quote( $postId ) ;
			$query  .= ' ORDER BY `id` DESC LIMIT 1';

			$db->setQuery( $query );
			$lastReply  = $db->loadAssoc();
	    }

	    if( isset($lastReply['id']) )
	    {
		    $query	= 'SELECT DISTINCT `user_id`, `poster_email`, `poster_name` FROM `#__discuss_posts`';
			$query	.= ' WHERE `parent_id` = ' . $db->Quote( $postId );
			$query	.= ' and `id` != ' . $db->Quote( $lastReply['id'] );

			if( !empty( $lastReply['user_id']  ) )
			    $query	.= ' and `user_id` != ' . $db->Quote( $lastReply['user_id'] );

			if( !empty( $lastReply['poster_email']  ) )
			    $query	.= ' and `poster_email` != ' . $db->Quote( $lastReply['poster_email'] );

			$query  .= ' ORDER BY `id` DESC';
			$query  .= ' LIMIT 5';

			$db->setQuery( $query );

			$repliesUser    = $db->loadObjectList();
		}

	    return $repliesUser;
	}

	/**
	 * Retrieves a list of user id's that has participated in a discussion
	 *
	 * @access	public
	 * @param	int $postId		The main discussion id.
	 * @return	Array	An array of user id's.
	 *
	 **/
	public function getParticipants( $postId )
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT DISTINCT `user_id` FROM `#__discuss_posts`';
		$query	.= ' WHERE `parent_id` = ' . $db->Quote( $postId );

		$db->setQuery( $query );
		$participants		= $db->loadResultArray();

		return $participants;
	}


	public function hasAttachments( $postId , $type )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_attachments' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $postId ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		return $result;
	}

	public function hasPolls( $postId )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT( DISTINCT(`post_id`) ) FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $postId );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		return $result;
	}
}
