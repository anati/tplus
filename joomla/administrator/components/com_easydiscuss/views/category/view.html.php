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

class EasyDiscussViewCategory extends EasyDiscussAdminView
{
	var $cat	= null;

	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config     = DiscussHelper::getConfig();

		//Load pane behavior
		jimport('joomla.html.pane');

		JHTML::_('behavior.modal' , 'a.modal' );
		JHTML::_('behavior.tooltip');

		$catId		= JRequest::getVar( 'catid' , '' );

		$cat		= JTable::getInstance( 'Category' , 'Discuss' );

		$cat->load( $catId );

		$this->cat	= $cat;


		// Set default values for new entries.
		if( empty( $cat->created ) )
		{
			$date   = DiscussDateHelper::getDate();
			$now 	= DiscussDateHelper::toFormat($date);

			$cat->created	= $now;
			$cat->published	= true;
		}


		$catRuleItems		= JTable::getInstance( 'CategoryAclItem' , 'Discuss' );
		$categoryRules  	= $catRuleItems->getAllRuleItems();

		$assignedGroupACL  = $cat->getAssignedACL( 'group' );
		$assignedUserACL   = $cat->getAssignedACL( 'user' );


		$joomlaGroups    	= DiscussHelper::getJoomlaUserGroups();

	    if( DiscussHelper::getJoomlaVersion() < '1.6' )
	    {
	        $guest  = new stdClass();
	        $guest->id		= '0';
	        $guest->name	= 'Public';
	        $guest->level	= '0';
	        array_unshift($joomlaGroups, $guest);
	    }


		$parentList = DiscussHelper::populateCategories('', '', 'select', 'parent_id', $cat->parent_id);

		$this->assignRef( 'cat'			, $cat );
		$this->assignRef( 'config'		, $config );
		$this->assignRef( 'acl'			, $acl );
		$this->assignRef( 'parentList'	, $parentList );
		$this->assignRef( 'categoryRules'	, $categoryRules );
		$this->assignRef( 'assignedGroupACL'	, $assignedGroupACL );
		$this->assignRef( 'assignedUserACL'		, $assignedUserACL );
		$this->assignRef( 'joomlaGroups'		, $joomlaGroups );


		parent::display($tpl);
	}

	function registerToolbar()
	{
		if( $this->cat->id != 0 )
		{
			JToolBarHelper::title( JText::sprintf( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_TITLE' , $this->cat->title ), 'category' );
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_ADD_CATEGORY_TITLE' ), 'category' );
		}

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolBarHelper::save();
		JToolBarHelper::custom('savePublishNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_AND_NEW' ) , false);
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
