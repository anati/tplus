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

require( DISCUSS_ADMIN_ROOT . DS . 'views.php');

class EasyDiscussViewTag extends EasyDiscussAdminView
{
	var $tag	= null;

	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		//Load pane behavior
		jimport('joomla.html.pane');

		$tagId		= JRequest::getVar( 'tagid' , '' );

		$tag		= JTable::getInstance( 'Tags' , 'Discuss' );

		$tag->load( $tagId );

		$tag->title = JString::trim($tag->title);
		$tag->alias = JString::trim($tag->alias);

		$this->tag	= $tag;

		// Set default values for new entries.
		if( empty( $tag->created ) )
		{
			$date			= JFactory::getDate();
			$date->setOffSet( $mainframe->getCfg('offset') );

			$tag->created	= $date->toFormat();
			$tag->published	= true;
		}

		$this->assignRef( 'tag'		, $tag );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		if( $this->tag->id != 0 )
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_EDITING_TAG' ), 'tags' );
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_ADD_NEW_TAG' ), 'tags' );
		}

		JToolBarHelper::back();
		JToolBarHelper::divider();
		JToolBarHelper::save();
		JToolBarHelper::custom('savePublishNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_AND_NEW' ) , false);
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

}
