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
require_once( DISCUSS_HELPERS . DS . 'router.php' );

class EasyDiscussViewProfile extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		$document	= JFactory::getDocument();
		$userid		= JRequest::getInt( 'id' , null );
		$user		= JFactory::getUser( $userid );
		$config		= DiscussHelper::getConfig();
		
		$sort			= JRequest::getString('sort', 'latest');
		$filteractive	= JRequest::getString('filter', 'allposts');
		$viewType		= JRequest::getString('viewtype', 'user-post');

		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $user->id );

		if( !$profile->id )
		{
			JFactory::getApplication()->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss' , false ) );
		}

		$userparams	= new JParameter($profile->get('params'));

		$social		= array( 'facebook' , 'linkedin' , 'twitter' );
		$userparams	= new JParameter($profile->get('params'));

		foreach( $social as $site )
		{
			if( $userparams->get( $site  , '' ) != '' )
			{
				$url	= $userparams->get( $site );

				if( stristr( $site . '.com' , $url ) === false )
				{
					$userparams->set( $site , 'http://' . $site . '.com/' . $url );
				}
			}
		}
		
		$document->setTitle( JText::sprintf( 'COM_EASYDISCUSS_PROFILE_PAGE_TITLE' , $profile->getName() ) );
		
		$this->setPathway( JText::_( $profile->getName() ) );
		
		$model		= $this->getModel( 'Posts' );
		$tagsModel	= $this->getModel( 'Tags' );
		
		$posts      = array();
		$replies    = array();
		$tagCloud   = array();
		$badges		= array();
		$pagination = null;
		
		
		switch( $viewType)
		{
			case 'user-achievements':
			    $badges		= $profile->getBadges();
				break;
		
			case 'user-tags':
				$tagCloud   = $tagsModel->getTagCloud( '' , '' , '' , $profile->id );
				break;
		
			case 'user-replies':
				$replies	= $model->getRepliesFromUser( $profile->id );
				$pagination	= $model->getPagination();
				DiscussHelper::formatPost( $replies );
				break;
		
			case 'user-post':
			default:
				$posts		= $model->getPostsBy( 'user' , $profile->id );
				$pagination	= $model->getPagination();
				DiscussHelper::formatPost( $posts );
			    break;
		
		}


		$badges		= $profile->getBadges();

		// @rule: Clear up any notifications that are visible for the user.
		$notifications	= $this->getModel( 'Notification' );
		$notifications->markRead( $profile->id , false , array( DISCUSS_NOTIFICATIONS_PROFILE , DISCUSS_NOTIFICATIONS_BADGE ) );

		$tpl		= new DiscussThemes();
		$tpl->set( 'sort'		, $sort );
		$tpl->set( 'filter'		, $filteractive );
		$tpl->set( 'tagCloud'	, $tagCloud );
		$tpl->set( 'paginationType'	, DISCUSS_USERQUESTIONS_TYPE );
		$tpl->set( 'parent_id'		, $profile->id );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'posts'		, $posts );
		$tpl->set( 'badges'		, $badges );
		$tpl->set( 'profile'	, $profile );
		$tpl->set( 'config'		, $config );
		$tpl->set( 'replies'	, $replies );
		$tpl->set( 'userparams', $userparams );
		$tpl->set( 'viewType', $viewType );

		
		$filterArr  = array();
		$filterArr['filter'] 		= $filteractive;
		$filterArr['id'] 			= $profile->id;
		$filterArr['sort'] 			= $sort;
		$filterArr['viewtype'] 		= $viewType;

		$tpl->set( 'filterArr'		, $filterArr );
		$tpl->set( 'page'			, 'profile' );

		echo $tpl->fetch( 'user.php' );
	}

	function edit( $tmpl = null )
	{
		require_once( DISCUSS_HELPERS . DS . 'integrate.php' );

		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();

		// Load bbcode editor.
		DiscussHelper::loadEditor();
		
		if(empty($user->id))
		{
			$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_YOU_MUST_LOGIN_FIRST'), 'error');
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index'));
			return false;
		}

		$this->setPathway( JText::_('COM_EASYDISCUSS_PROFILE') , DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $user->id ) );
		$this->setPathway( JText::_('COM_EASYDISCUSS_EDIT_PROFILE') );

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$profile = DiscussHelper::getTable( 'Profile' );
		$profile->load($user->id);

		$userparams	= new JParameter($profile->get('params'));
		$maxSize	= ini_get( 'upload_max_filesize' );

		$configMaxSize  = $config->get( 'main_upload_maxsize', 0 );
		if( $configMaxSize > 0 )
		{
			$configMaxSize  = DiscussHelper::getHelper( 'String' )->bytesToSize($configMaxSize);
		}

		$avatar_config_path = $config->get('main_avatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = JString::str_ireplace('/', DS, $avatar_config_path);

		$croppable 			= false;
		
		if( $config->get( 'layout_avatarIntegration') == 'default' )
		{
			$original 	= JPATH_ROOT . DS . rtrim( $config->get( 'main_avatarpath' ) , '/' ) . DS . 'original_' . $profile->avatar;

			if( JFile::exists( $original ) )
			{
				$size 		= getimagesize( $original );

				$width 		= $size[0];
				$height 	= $size[1];

				if( $width > 160 && $height > 160 )
				{
					$croppable 	= true;
				}
			}
		}

		$tpl	= new DiscussThemes();
		$tpl->set( 'croppable' , $croppable );
		$tpl->set( 'size'	, $maxSize );
		$tpl->set( 'user'	, $user );
		$tpl->set( 'profile', $profile );
		$tpl->set( 'config'	, $config );
		$tpl->set( 'configMaxSize'	, $configMaxSize );
		$tpl->set( 'avatarIntegration', $config->get( 'layout_avatarIntegration', 'default' ) );
		$tpl->set( 'userparams', $userparams );

		echo $tpl->fetch( 'user.edit.php' );
	}
}
