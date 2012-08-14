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

require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_ADMIN_ROOT . DS . 'views.php');

class EasyDiscussViewSpools extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$filter_state 	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.spools.filter_state', 		'filter_state', 	'*', 'word' );
		$search 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.spools.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.spools.filter_order', 		'filter_order', 	'created', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.spools.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );

		$mails			= $this->get( 'Data' );
		$pagination 	= $this->get( 'Pagination' );
		$this->assign( 'mails' , $mails );
		$this->assign( 'pagination'	, $pagination );
		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state , JText::_( 'COM_EASYDISCUSS_SENT' ) , JText::_( 'COM_EASYDISCUSS_PENDING' ) ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_SPOOLS_TITLE' ), 'spools' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
		JToolBarHelper::divider();
		JToolBarHelper::custom('purge','purge','icon-32-unpublish.png', 'COM_EASYDISCUSS_SPOOLS_PURGE_ALL_BUTTON', false);
	}
}
