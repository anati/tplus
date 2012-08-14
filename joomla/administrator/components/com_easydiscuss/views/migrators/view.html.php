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

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_MIGRATORS' ), 'migrators' );
		JToolBarHelper::back( 'COM_EASYDISCUSS_BACK' , 'index.php?option=com_easydiscuss');
	}

	public function kunenaExists()
	{
		jimport( 'joomla.filesystem.file' );

		return JFile::exists( JPATH_ROOT . DS . 'components' . DS . 'com_kunena' . DS . 'kunena.php' );
	}

	public function registerSubmenu()
	{
		return 'submenu.php';
	}
}
