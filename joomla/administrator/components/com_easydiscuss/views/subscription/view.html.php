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

class EasyDiscussViewSubscription extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

 		$filter 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter', 	'filter', 	'site', 'word' );
 		$search 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.search', 	'search', 		'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
 		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order', 		'filter_order', 'fullname', 'cmd' );
 		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.subscription.filter_order_Dir','filter_order_Dir',		'', 'word' );


 		$model	= $this->getModel('Subscribe');
		$subscriptions	= $model->getSubscription();
 		$pagination 	= $model->getPagination();
 		$this->assignRef( 'subscriptions' 	, $subscriptions );
 		$this->assignRef( 'pagination'		, $pagination );

 		$this->assign( 'filter'			, $filter );
 		$this->assign( 'filterList'		, $this->_getFilter($filter) );
 		$this->assign( 'search'			, $search );
 		$this->assign( 'order'			, $order );
 		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function _getFilter( $filter )
	{
	    $filterType = array();
	    $attribs	= 'size="1" class="inputbox" onchange="submitform();"';

		$filterType[] = JHTML::_('select.option', 'site', JText::_( 'COM_EASYDISCUSS_SITE_OPTION' ) );
	    $filterType[] = JHTML::_('select.option', 'post', JText::_( 'COM_EASYDISCUSS_POST_OPTION' ) );


	    return JHTML::_('select.genericlist',   $filterType, 'filter', $attribs, 'value', 'text', $filter );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_DISCUSSIONS' ), 'discussions' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
