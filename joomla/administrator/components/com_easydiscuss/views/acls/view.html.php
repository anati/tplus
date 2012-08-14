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

jimport( 'joomla.application.component.view');
require_once( DISCUSS_ADMIN_ROOT . DS . 'views.php');

class EasyDiscussViewAcls extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$model 		= $this->getModel( 'Acl' );
		$config		= DiscussHelper::getConfig();

		JHTML::_('behavior.tooltip');

		$type = $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_type', 'filter_type', 'group', 'word' );

		//filtering
		$filter = new stdClass();
		$filter->type 	= $this->getFilterType($type);
 		$filter->search = $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.search', 'search', '', 'string' );

		//sorting
		$sort = new stdClass();
		$sort->order			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_order', 'filter_order', 'a.`id`', 'cmd' );
 		$sort->orderDirection	= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		JHTML::_('behavior.tooltip');

		$rulesets	= $model->getRuleSets($type);
		$pagination = $model->getPagination($type);

		if ( $type == 'assigned' )
		{
			$document->setTitle( JText::_("COM_EASYDISCUSS_ACL_ASSIGN_USER") );
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ACL_ASSIGN_USER' ), 'acl' );
		}
		else
		{
			$document->setTitle( JText::_("COM_EASYDISCUSS_ACL_JOOMLA_USER_GROUP") );
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ACL_JOOMLA_USER_GROUP' ), 'acl' );
		}

		$this->assignRef( 'config' , $config );
		$this->assignRef( 'rulesets' , $rulesets );
		$this->assignRef( 'filter', $filter );
		$this->assignRef( 'sort', $sort );
		$this->assignRef( 'type', $type );
		$this->assignRef( 'pagination'	, $pagination );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ) , 'index.php?option=com_easydiscuss');
		JToolBarHelper::divider();

		$mainframe	= JFactory::getApplication();
		$type		= $mainframe->getUserStateFromRequest( 'com_easydiscuss.acls.filter_type', 'filter_type', 'group', 'word' );

		if($type=='assigned')
		{
			JToolbarHelper::addNew();
			JToolbarHelper::deleteList();
		}
	}

	function getFilterType( $filter_type='*', $group='COM_EASYDISCUSS_JOOMLA_GROUP', $assigned='COM_EASYDISCUSS_ASSIGNED' )
	{
		$filter[] = JHTML::_('select.option', '', '- '. JText::_( 'COM_EASYDISCUSS_ACL_SELECT_TYPE' ) .' -' );
		$filter[] = JHTML::_('select.option', 'group', JText::_( $group ) );
		$filter[] = JHTML::_('select.option', 'assigned', JText::_( $assigned ) );

		return JHTML::_('select.genericlist', $filter, 'filter_type', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

}
