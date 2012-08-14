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
require_once( DISCUSS_HELPERS . DS . 'router.php' );

class EasyDiscussViewPosts extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$filter_state 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_state', 	'filter_state', 	'*', 'word' );
		$search 			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		$parentId       = JRequest::getString('pid', '');
		$parentTitle    = '';

		if(! empty($parentId))
		{
			$post		= JTable::getInstance( 'Posts' , 'Discuss' );
			$post->load($parentId);
			$parentTitle    = $post->title;
		}


		$postModel	= $this->getModel('Threaded');


		$posts			= $postModel->getPosts();
		$pagination 	= $postModel->getPagination();


		$this->assignRef( 'posts' 		, $posts );
		$this->assignRef( 'pagination'	, $pagination );

		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );
		$this->assign( 'parentId'		, $parentId );
		$this->assign( 'parentTitle'	, $parentTitle );

		parent::display($tpl);
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
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_DISCUSSIONS' ), 'discussions' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'feature' , 'discuss-feature' , '' , JText::_( 'COM_EASYDISCUSS_FEATURE_TOOLBAR' ) );
		JToolBarHelper::custom( 'unfeature' , 'discuss-unfeature' , '' , JText::_( 'COM_EASYDISCUSS_UNFEATURE_TOOLBAR' ) );
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		// JToolbarHelper::addNew();
		JToolbarHelper::deleteList();
	}
}
