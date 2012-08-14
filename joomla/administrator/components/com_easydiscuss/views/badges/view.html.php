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

require( DISCUSS_ADMIN_ROOT . DS . 'views.php');
require_once( DISCUSS_HELPERS . DS . 'helper.php' );

class EasyDiscussViewBadges extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		if( $this->getLayout() == 'install' )
		{
			return $this->installLayout();
		}

		if( $this->getLayout() == 'managerules' )
		{
			return $this->manageRules();
		}

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_state', 	'filter_state', 	'*', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.badges.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$badges			= $this->get( 'Badges' );
		$pagination		= $this->get( 'Pagination' );

		$this->assign( 'badges'		, $badges );
		$this->assign( 'pagination'	, $pagination );

		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	public function getTotalUsers( $badgeId )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'badge_id' ) . '=' . $db->Quote( $badgeId );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	function getFilterState ($filter_state='*')
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select State' ) .' -' );
		$state[] = JHTML::_('select.option',  'P', JText::_( 'Published' ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( 'Unpublished' ) );
		$state[] = JHTML::_('select.option',  'A', JText::_( 'Pending' ) );

		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_BADGES' ), 'badges' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'rules' , 'rules' , '' , JText::_( 'COM_EASYDISCUSS_MANAGE_RULES_BUTTON' ) , false );
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::addNew();
		JToolbarHelper::deleteList();
	}
}
