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

jimport( 'joomla.application.component.view');

require_once( DISCUSS_HELPERS . DS . 'helper.php' );

class EasyDiscussViewUsers extends JView
{

	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$filter_state 	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_state', 	'filter_state', 	'*', 'word' );
		$search 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order', 	'filter_order', 	'id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$users			= $this->get( 'Users' );
		$pagination 	= $this->get( 'Pagination' );

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			if(count($users) > 0)
			{
				for($i = 0; $i < count($users); $i++)
				{
					$row    = $users[$i];

					$joomlaUser = JFactory::getUser($row->id);

					$userGroupsKeys		= array_keys($joomlaUser->groups);
					$userGroups			= implode(', ', $userGroupsKeys);
					$row->usergroups	= $userGroups;
				}
			}
		}

		$this->assignRef( 'users' 		, $users );
		$this->assignRef( 'pagination'	, $pagination );

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction = JRequest::getVar('browsefunction', 'insertMember');
		$this->assign( 'browse' , $browse );
		$this->assign( 'browsefunction' , $browsefunction );

		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function getTotalTopicCreated($userId)
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT COUNT(1) AS CNT FROM `#__discuss_posts`';
		$query  .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$query  .= ' AND `parent_id` = 0';

		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;


	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_USERS' ), 'users' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

	}

}
