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
require( DISCUSS_ADMIN_ROOT . DS . 'views.php');

class EasyDiscussViewAcl extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		$mainframe	= JFactory::getApplication();
		$model 		= $this->getModel( 'Acl' );
		$document	= JFactory::getDocument();

		$cid	= JRequest::getVar('cid', '', 'REQUEST');
		$type	= JRequest::getVar('type', '', 'REQUEST');
		$add	= JRequest::getVar('add', '', 'REQUEST');

		JHTML::_('behavior.modal' , 'a.modal' );
		JHTML::_('behavior.tooltip');

		if((empty($cid) || empty($type)) && empty($add))
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=acls' , JText::_('Invalid Id or acl type. Please try again.') , 'error' );
		}

		$rulesets = $model->getRuleSet($type, $cid, $add);

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

		$joomlaVersion	= DiscussHelper::getJoomlaVersion();

		$this->assignRef( 'joomlaversion'	, $joomlaVersion );
		$this->assignRef( 'rulesets' 		, $rulesets );
		$this->assignRef( 'type' 			, $type );
		$this->assignRef( 'add' 			, $add );

		parent::display($tpl);
	}

	public function getRuleDescription( $action )
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT `description` FROM ' . $db->nameQuote( '#__discuss_acl' ) . ' '
				. 'WHERE `action`=' . $db->Quote( $action );

		$db->setQuery( $query );
		$description	= $db->loadResult();

		return $description;
	}

	function registerToolbar()
	{
		JToolBarHelper::back( 'COM_EASYDISCUSS_HOME' , 'index.php?option=com_easydiscuss');
 		JToolBarHelper::divider();
 		JToolBarHelper::save();
 		JToolBarHelper::apply();
 		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
