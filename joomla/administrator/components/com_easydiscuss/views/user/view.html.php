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

class EasyDiscussViewUser extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();

		$config		= DiscussHelper::getConfig();

		$id			= JRequest::getInt('id');
		$profile	= JTable::getInstance( 'Profile' , 'Discuss' );
		$profile->load( $id );

		$userparams	= new JParameter($profile->get('params'));

		$avatarIntegration = $config->get( 'layout_avatarIntegration', 'default' );

		$user	= JFactory::getUser( $id );
		$isNew	= ($user->id == 0) ? true : false;

		jimport('joomla.html.pane');

		$badges		= $profile->getBadges();

		$model		= $this->getModel( 'Badges' );
		$history	= $model->getBadgesHistory( $profile->id );

		// Load bbcode editor.
		DiscussHelper::loadEditor();

		$params	= $user->getParameters(true);

		$pane	= JPane::getInstance('Tabs');
		$this->assignRef( 'badges'	, $badges );
		$this->assignRef( 'history'	, $history );
		$this->assignRef( 'config' , $config );
		$this->assignRef( 'pane' , $pane );
		$this->assignRef( 'profile' , $profile );
		$this->assignRef( 'user' , $user );
		$this->assignRef( 'isNew' , $isNew );
		$this->assignRef( 'params' , $params );
		$this->assignRef( 'avatarIntegration' , $avatarIntegration );
		$this->assignRef( 'userparams' , $userparams );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		$id		= JRequest::getInt('id');
		$user	= JTable::getInstance( 'User' , 'JTable' );
		$user->load( $id );

		$title	= ($user->id == 0) ? JText::_('COM_EASYDISCUSS_NEW_USER') : JText::sprintf( 'COM_EASYDISCUSS_EDITING_USER' , $user->name );

		JToolBarHelper::title( $title , 'users' );

		//JToolBarHelper::back();
		//JToolBarHelper::divider();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();

	}
}
