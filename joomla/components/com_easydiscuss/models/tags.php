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

class EasyDiscussModelTags extends JModel
{
	/**
	 * Tag total
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
	 * Tag data array
	 *
	 * @var array
	 */
	var $_data = null;
	
	function __construct()
	{
		parent::__construct();

		
		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.limit', 'limit', DiscussHelper::getListLimit(), 'int');
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
		
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_tags' )
				. $where . ' '
				. $orderby;

		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= $this->getDBO();
		
		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.search', 'search', '', 'string' );
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

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_order', 		'filter_order', 	'title ASC'	, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_order_Dir',	'filter_order_Dir',		''			, 'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	/**
	 * Method to get categories item data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 * Method to publish or unpublish tags
	 *
	 * @access public
	 * @return array
	 */
	function publish( $tags = array(), $publish = 1 )
	{
		if( count( $tags ) > 0 )
		{
			$db		= $this->getDBO();
			
			$tags	= implode( ',' , $tags );
			
			$query	= 'UPDATE ' . $db->nameQuote( '#__discuss_tags' ) . ' '
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

	function searchTag($title)
	{
		$db	= JFactory::getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote('id') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('title') . ' = ' . $db->quote($title) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);
		
		$result	= $db->loadObject();
		
		return $result;
	}


	function getTagName($id)
	{
		$db	= JFactory::getDBO();
		
		$query	= 'SELECT ' . $db->nameQuote('title') . ' '
				. 'FROM ' 	. $db->nameQuote('#__discuss_tags') . ' '
				. 'WHERE ' 	. $db->nameQuote('id') . ' = ' . $db->quote($id) . ' '
				. 'LIMIT 1';
		$db->setQuery($query);
		
		$result	= $db->loadResult();
		
		return $result;
	}

	
	/**
	 * Method to get total tags created so far iregardless the status.
	 *
	 * @access public
	 * @return integer
	 */	
	function getTotalTags( $userId = 0)
	{
		$db		= $this->getDBO();
		$where  = array();
		
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_tags' );
		
		if(! empty($userId))
		    $where[]  = '`user_id` = ' . $db->Quote($userId);

		$extra 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );
		$query      = $query . $extra;
		
		
		
		$db->setQuery( $query );
		
		$result	= $db->loadResult();
	
		return (empty($result)) ? 0 : $result;
	}

	function isExist( $tagName, $excludeTagIds = '0' )
	{
	    $db = $this->getDBO();

	    $query  = 'SELECT COUNT(1) FROM #__discuss_tags';
		$query  .= ' WHERE `title` = ' . $db->Quote($tagName);
		if($excludeTagIds != '0')
		    $query  .= ' AND `id` != ' . $db->Quote($excludeTagIds);

		$db->setQuery($query);
		$result = $db->loadResult();

		return (empty($result)) ? 0 : $result;
	}
	
	function getTagCloud($limit='', $order='title', $sort='asc' , $userId = '' )
	{
	    $db = $this->getDBO();
		
	    $query  =   'select a.`id`, a.`title`, a.`alias`, a.`created`, count(c.`id`) as `post_count`';
		$query	.=  ' from #__discuss_tags as a';
		$query	.=  '    left join #__discuss_posts_tags as b';
		$query	.=  '    on a.`id` = b.`tag_id`';
		$query  .=  '    left join #__discuss_posts as c';
		$query  .=  '    on b.post_id = c.id';
		$query  .=  '    and c.`published` = ' . $db->Quote('1');
		$query  .= 	' where a.`published` = ' . $db->Quote('1');
		
		if( !empty( $userId ) )
		{
			$query	.= ' AND a.`user_id`=' . $db->Quote( $userId );
		}

		$query	.=  ' group by (a.`id`)';
		
		//echo $query;
		
		//order
		switch($order)
		{
			case 'postcount':
				$query	.=  ' ORDER BY (post_count)';
				break;
			case 'title':
			default:
				$query	.=  ' ORDER BY (a.`title`)';
		}
		
		//sort
		switch($sort)
		{
			case 'asc':
				$query	.=  ' asc ';
				break;
			case 'desc':
			default:
				$query	.=  ' desc ';
		}
		
		//limit
		if(!empty($limit))
		{
			$query	.=  ' LIMIT ' . (INT)$limit;
		}

		// echo $query;exit;
		
		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}
	
	function getTags($count="")
	{
	    $db = $this->getDBO();
	    
	    $query  =   ' SELECT `id`, `title`, `alias` ';
	    $query  .=  ' FROM #__discuss_tags ';
		$query	.=  ' WHERE `published` = 1 ';
		$query	.=  ' ORDER BY `title`';
		
		if(!empty($count))
		{
			$query	.=  ' LIMIT ' . $count;
		}
		
		
		$db->setQuery($query);
		$result = $db->loadObjectList();
		
		return $result;
	}
	
	
	/**
	 * *********************************************************************
	 * These part of codes will used in tag clod tags.
	 * *********************************************************************
	 */


// 	function _buildQueryByTagBlogs()
// 	{
// 		$db			= $this->getDBO();
// 
// 		$query	=  'SELECT COUNT(a.`tag_id`) AS `cnt`, b.*';
// 		$query	.= ' FROM `#__discuss_posts_tags` AS a';
// 		$query	.= '   INNER JOIN `#__discuss_posts` AS b ON a.`post_id` = b.`id`';
// 		$query	.= ' GROUP BY (a.`post_id`)';
// 		$query	.= ' ORDER BY `cnt` DESC';
// 
// 		return $query;
// 	}


// 	function getTagBlogs()
// 	{
// 	    $db = JFactory::getDBO();
// 
// 	    $query  = $this->_buildQueryByTagBlogs();
// 	    $pg		= $this->getPaginationByTagBlogs();
// 	    //$db->setQuery($query);
// 
// 	    $result = $this->_getList($query, $pg->limitstart, $pg->limit);
// 
// 	    return $result;
// 	}

// 	function getPaginationByTagBlogs()
// 	{
// 		jimport('joomla.html.pagination');
// 		$this->_pagination = new JPagination( $this->getTotalByTagBlogs(), $this->getState('limitstart'), $this->getState('limit') );
// 
// 		return $this->_pagination;
// 	}
// 
// 	function getTotalByTagBlogs()
// 	{
// 		// Lets load the content if it doesn't already exist
// 		$query = $this->_buildQueryByTagBlogs();
// 		$total = $this->_getListCount($query);
// 
// 		return $total;
// 	}

	/**
	 * *********************************************************************
	 * These part of codes will used in tag clod tags.
	 * *********************************************************************
	 */
	
// 	function getTagPrivateBlogCount( $tagId )
// 	{
// 		$db = JFactory::getDBO();
// 
// 		$query	= 'select count(1) from `#__discuss_posts` as a';
// 		$query	.= '  inner join `#__discuss_posts_tags` as b';
// 		$query	.= '    on a.`id` = b.`post_id`';
// 		$query	.= '    and b.`tag_id` = ' . $db->Quote($tagId);
// 		$query	.= '  where a.private = ' . $db->Quote(DISCUSS_PRIVACY_PRIVATE);
// 		
// 		$db->setQuery($query);
// 		$result = $db->loadResult();
// 		
// 		return (empty($result)) ? '0' : $result;
// 	}
	
	
}