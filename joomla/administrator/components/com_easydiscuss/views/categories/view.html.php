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

require_once( DISCUSS_ADMIN_ROOT . DS . 'views.php');

class EasyDiscussViewCategories extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$filter_state 	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_state', 		'filter_state', 	'*', 'word' );
		$search 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_order', 		'filter_order', 	'lft', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.categories.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );

		//Get data from the model
		$model          = $this->getModel( 'Categories' );
		$categories		= $model->getData();
		$ordering   	= array();

		JTable::addIncludePath( DISCUSS_TABLES );
		$category			= JTable::getInstance( 'Category' , 'Discuss' );

		for( $i = 0 ; $i < count( $categories ); $i++ )
		{
			$category			= $categories[ $i ];

			$category->count	= $model->getUsedCount( $category->id );
			$category->child_count	= $model->getChildCount( $category->id );

			// Preprocess the list of items to find ordering divisions.
			$ordering[$category->parent_id][] = $category->id;
		}
		$pagination 	= $this->get( 'Pagination' );

		$this->assignRef( 'categories' 	, $categories );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assignRef( 'ordering'	, $ordering );

		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_CATEGORIES_TITLE' ), 'category' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolBarHelper::makeDefault( 'makeDefault' );
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::addNew();
		JToolbarHelper::deleteList();

	}
}
