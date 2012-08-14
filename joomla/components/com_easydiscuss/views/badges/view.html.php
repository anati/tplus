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
defined('_JEXEC') or die('Restricted access');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'views.php' );

class EasyDiscussViewBadges extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$my 		= JFactory::getUser();

		$config		= DiscussHelper::getConfig();

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BADGES' ) );

		$model		= $this->getModel( 'Badges' );
		$badges		= $model->getBadges();

		$theme		= new DiscussThemes();
		$theme->set( 'active' , 'all' );
		$theme->set( 'badges' , $badges );
	
		echo $theme->fetch( 'badges.php' );
	}

	public function mybadges()
	{
		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BADGES' ) );

		$my 		= JFactory::getUser();

		if( !$my->id )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE') , DISCUSS_QUEUE_ERROR );
			JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss', false) );
		}

		$model		= $this->getModel( 'Badges' );
		$badges		= $model->getBadges( array( 'user' => $my->id ) );

		$theme		= new DiscussThemes();
		$theme->set( 'active' , 'mybadges' );
		$theme->set( 'badges' , $badges );
	
		echo $theme->fetch( 'badges.php' );
	}
	
	public function listings()
	{
		$app		= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();
		$id			= JRequest::getInt( 'id' );

		if( empty( $id ) )
		{
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges' , false ) , JText::_( 'COM_EASYDISCUSS_INVALID_BADGE' ) );
			$app->close();
		}

		$badge		= DiscussHelper::getTable( 'Badges' );
		$badge->load( $id );

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BADGES' ) , DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges' ) );
		$this->setPathway( JText::_( $badge->get( 'title') ) );

		$users		= $badge->getUsers();
		
		$theme		= new DiscussThemes();
		$theme->set( 'badge'	, $badge );
		$theme->set( 'users'	, $users );
		echo $theme->fetch( 'badge.php' );
	}
}