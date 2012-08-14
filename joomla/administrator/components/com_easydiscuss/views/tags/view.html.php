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

class EasyDiscussViewTags extends JView
{

	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$filter_state 	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_state', 	'filter_state', 	'*', 'word' );
		$search 		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.search', 			'search', 			'', 'string' );

		$search 		= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_order', 	'filter_order', 	'id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.tags.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$tags			= $this->get( 'Data' );
		$model			= $this->getModel();

		for( $i = 0 ; $i < count( $tags ); $i++ )
		{
			$tag		= $tags[ $i ];
			$tag->count	= $model->getUsedCount( $tag->id );
			$tag->title = JString::trim($tag->title);
			$tag->alias = JString::trim($tag->alias);
		}
		$pagination 	= $this->get( 'Pagination' );

		$this->assignRef( 'tags' 		, $tags );
		$this->assignRef( 'pagination'	, $pagination );

		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_TAGS' ), 'tags' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::addNew();
		JToolbarHelper::deleteList();

	}

}
