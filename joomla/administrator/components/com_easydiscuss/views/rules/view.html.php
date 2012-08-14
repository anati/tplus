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

class EasyDiscussViewRules extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.rules.filter_state', 	'filter_state', 	'*', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.rules.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.rules.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.rules.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$rules			= $this->get( 'Rules' );
		$pagination		= $this->get( 'Pagination' );

		$this->assign( 'rules'		, $rules );
		$this->assign( 'pagination'	, $pagination );
		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	public function installLayout( $tpl )
	{
		return parent::display( $tpl );
	}

	function registerToolbar()
	{
		$from	= JRequest::getVar( 'from' );

		if( $this->getLayout() != 'install' )
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_MANAGE_RULES' ), 'rules' );
			JToolBarHelper::back( 'COM_EASYDISCUSS_BACK' , 'index.php?option=com_easydiscuss&view=' . $from );
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'install' , 'save.png' , 'save_f2.png' , JText::_( 'COM_EASYDISCUSS_NEW_RULE_BUTTON' ) , false );
			JToolbarHelper::deleteList();
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_NEW_RULE_BUTTON' ), 'install' );
			JToolBarHelper::back( 'COM_EASYDISCUSS_BACK' , 'index.php?option=com_easydiscuss&view=' . $from );
		}
	}
}
