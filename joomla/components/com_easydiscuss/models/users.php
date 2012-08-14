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

class EasyDiscussModelUsers extends JModel
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

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.limit', 'limit', DiscussHelper::getListLimit(), 'int');
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
			$this->_pagination	= DiscussHelper::getPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
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
		$groupby	= $this->_buildQueryGroupBy();
		$db			= $this->getDBO();

		$query		= 'SELECT u.`id`, u.`name`, u.`username`, u.`email`, u.`registerDate`, u.`lastvisitDate`, u.`params` '
					. ', d.`nickname`, d.`avatar`, d.`description`, d.`url`, d.`alias` '
					. ', MAX(p.`created`) AS lastPostCreated, COUNT(p.`id`) AS `postCount` '
					. 'FROM ' . $db->nameQuote( '#__users' ) . ' AS u '
					. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_users' ) . 'AS d ON d.`id` = u.`id` '
					. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS p ON p.`user_id` = u.`id` '
					. $where
					. $groupby
					. $orderby;
		return $query;
	}

	function _buildQueryWhere()
	{
		$mainframe			= JFactory::getApplication();
		$db					= $this->getDBO();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_state', 'filter_state', '', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.search', 'search', '', 'string' );
		$search 			= $db->getEscaped( trim(JString::strtolower( $search ) ) );
		$where				= array();

		$where[]            = 'u.`block`=' . $db->Quote( 0 );

		if ($search)
		{
			$where[] = ' LOWER( name ) LIKE \'%' . $search . '%\' ';
		}

		$where 		= ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildQueryOrderBy()
	{
		$mainframe			= JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order', 		'sort', 	'name ASC'	, 'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'dir',		''			, 'word' );

		if ( $filter_order == 'name' )
		{
			$filter_order		= '`name`';
			$filter_order_Dir	= 'ASC';
		}
		elseif ( $filter_order == 'lastvisit' )
		{
			$filter_order		= '`lastvisitDate`';
			$filter_order_Dir	= 'DESC';
		}
		elseif ( $filter_order == 'latest' )
		{
			$filter_order		= '`registerDate`';
			$filter_order_Dir	= 'DESC';
		}
		elseif ( $filter_order == 'postcount' )
		{
			$filter_order		= '`postcount`';
			$filter_order_Dir	= 'DESC';
		}
		elseif ( $filter_order == 'lastactive' )
		{
			$filter_order		= '`lastPostCreated`';
			$filter_order_Dir	= 'DESC';
		}

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir;

		return $orderby;
	}

	function _buildQueryGroupBy()
	{
		return ' GROUP BY u.`id`';
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
}
