s<?php
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

jimport('joomla.application.component.controller');
require_once( DISCUSS_HELPERS . DS . 'helper.php' );

class EasyDiscussControllerProfile extends JController
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Display the view
	 *
	 * @since 0.1
	 */
	function display($cachable = false, $urlparams = false)
	{
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$view 		= & $this->getView( $viewName,'',  $viewType);
		$view->display();
	}

	function saveProfile()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		$post		= JRequest::get( 'post' );

		array_walk($post, array($this, '_trim') );

		if(! $this->_validateProfileFields($post))
		{
			$this->setRedirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&layout=edit' , false ) );
			return;
		}

		$my			= JFactory::getUser();
		$my->name	= $post['fullname'];

		if(!empty($post['password']))
		{
			$my->password = $post['password'];
			$my->bind($post);
		}

		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );
		$profile->bind( $post );

		//save avatar
		$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
		if(! empty($file['name']))
		{
			$newAvatar			= $this->_upload( $profile );

			// @rule: If this is the first time the user is changing their profile picture, give a different point
			if( $profile->avatar == 'default.png' )
			{
				// @rule: Process AUP integrations
				DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_AVATAR , $my->id , $newAvatar );
			}
			else
			{
				// @rule: Process AUP integrations
				DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_UPDATE_AVATAR , $my->id , $newAvatar );
			}

			// @rule: Badges when they change their profile picture
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.new.avatar' , $my->id , JText::_( 'COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_AVATAR') );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.avatar' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.avatar' , $my->id );

			// Reset the points
			$profile->updatePoints();

			$profile->avatar    = $newAvatar;
		}




		//save params
		$userparams	= new JParameter('');

		if ( isset($post['facebook']) )
		{
			$userparams->set( 'facebook', $post['facebook'] );
		}
		if ( isset($post['show_facebook']) )
		{
			$userparams->set( 'show_facebook', $post['show_facebook']);
		}
		if ( isset($post['twitter']) )
		{
			$userparams->set( 'twitter', $post['twitter'] );
		}
		if ( isset($post['show_twitter']) )
		{
			$userparams->set( 'show_twitter', $post['show_twitter']);
		}
		if ( isset($post['linkedin']) )
		{
			$userparams->set( 'linkedin', $post['linkedin'] );
		}
		if ( isset($post['show_linkedin']) )
		{
			$userparams->set( 'show_linkedin', $post['show_linkedin']);
		}

		$profile->params	= $userparams->toString();

		if( $profile->store() && $my->save(true) )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PROFILE_SAVED' )  , 'info');

			// @rule: Badges when they change their profile picture
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.update.profile' , $my->id , JText::_( 'COM_EASYDISCUSS_BADGES_HISTORY_UPDATED_PROFILE') );
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.update.profile' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.update.profile' , $my->id );
		}
		else
		{
		    DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_PROFILE_SAVE_ERROR' )  , 'error');
		    $this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' , false ) );
		    return;
		}

		$this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile' , false ) );
	}

	function _trim(&$text)
	{
		$text = JString::trim($text);
	}

	function _validateProfileFields($post)
	{
		$mainframe	= JFactory::getApplication();
		$valid		= true;

		$message    = '<ul class="reset-ul">';

		if(JString::strlen($post['fullname']) == 0)
		{
			$message    .= '<li>' . JText::_( 'COM_EASYDISCUSS_REALNAME_EMPTY' ) . '</li>';
			$valid	= false;
		}

		if(JString::strlen($post['nickname']) == 0)
		{
			$message    .= '<li>' . JText::_( 'COM_EASYDISCUSS_NICKNAME_EMPTY'  ) . '</li>';
			$valid	= false;
		}


		if(!empty($post['password']))
		{
			if ( $post['password'] != $post['password2'] )
			{
				$message    .= '<li>' . JText::_( 'COM_EASYDISCUSS_PROFILE_PASSWORD_NOT_MATCH'  ) . '</li>';
				$valid	= false;
			}
		}

		$message    .= '<ul>';

		DiscussHelper::setMessageQueue( $message , 'alert');

		return $valid;
	}

	function _upload( $profile, $type = 'profile' )
	{
	    $newAvatar  = '';

		//can do avatar upload for post in future.

		$newAvatar  = DiscussHelper::uploadAvatar($profile);

		return $newAvatar;
	}
}
