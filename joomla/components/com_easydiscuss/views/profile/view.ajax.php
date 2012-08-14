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


class EasyDiscussViewProfile extends EasyDiscussView
{
	public function filter( $viewtype = 'user-post', $profileId = null)
	{
		$ajax       	= new Disjax();
		$mainframe      = JFactory::getApplication();
		$config         = DiscussHelper::getConfig();
		$acl			= DiscussHelper::getHelper( 'ACL' );

		$sort			= 'latest';
		$data           = null;
		$pagination     = null;
		$model			= $this->getModel('Posts');
		$tagsModel		= $this->getModel( 'Tags' );


		switch( $viewtype )
		{
			case 'user-achievements':

				$profile	= DiscussHelper::getTable( 'Profile' );
				$profile->load( $profileId );
			    $data		= $profile->getBadges();

				break;

			case 'user-tags':
				$data   = $tagsModel->getTagCloud( '' , '' , '' , $profileId );
				break;

			case 'user-replies':
				$data		= $model->getRepliesFromUser( $profileId );
				$pagination	= $model->getPagination();
				DiscussHelper::formatPost( $data );
				break;

			case 'user-post':
			default:

			    if( is_null($profileId) )
			    {
			        break;
			    }

				$model		= $this->getModel('Posts');
				$data		= $model->getPostsBy( 'user' , $profileId );
				$data       = DiscussHelper::formatPost($data);
				$pagination	= $model->getPagination();
			    break;
		}

		// replace the content
		$content        = '';
		$tpl			= new DiscussThemes();

		$tpl->set( 'profileId' , $profileId );

		if( $viewtype   == 'user-post' || $viewtype   == 'user-replies' )
		{
			$nextLimit		= DiscussHelper::getListLimit();
			if( $nextLimit >= $pagination->total )
			{
				// $ajax->remove( 'dc_pagination' );
				$ajax->assign( $viewtype . ' #dc_pagination', '' );
			}

			$tpl->set( 'posts'		, $data );
			$content	= $tpl->fetch( 'main.item.php' );

			$ajax->assign( $viewtype . ' #dc_list' , $content );

			//var_dump($content);exit;

			//reset the next start limi
			$ajax->value( 'pagination-start' , $nextLimit );

			if( $nextLimit < $pagination->total )
			{
				$filterArr  = array();
				$filterArr['viewtype'] 		= $viewtype;
				$filterArr['id'] 			= $profileId;
				$ajax->assign( $viewtype . ' #dc_pagination', $pagination->getPagesLinks('profile', $filterArr, true) );
			}
		}
		else if( $viewtype == 'user-tags' )
		{
			$tpl->set( 'tagCloud'		, $data );
			$content	= $tpl->fetch( 'tags.item.php' );

			$ajax->assign( 'discuss-tag-list' , $content );
		}
		else if( $viewtype == 'user-achievements' )
		{
			$tpl->set( 'badges'		, $data );
			$content	= $tpl->fetch( 'users.achievements.list.php' );
			$ajax->assign( 'user-achievements' , $content );
		}

		$ajax->script( 'discuss.spinner.hide( "profile-loading" );' );


		//$ajax->assign( 'sort-wrapper' , $sort );
		//$ajax->script( 'Foundry("#pagination-filter").val("'.$viewtype.'");');
		$ajax->script( 'Foundry("#' . $viewtype . '").show();');
		$ajax->script( 'Foundry("#' . $viewtype. ' #dc_pagination").show();');

		$ajax->send();
	}

	public function cropPhoto( $x , $y )
	{
		$my 		= JFactory::getUser();
		$ajax 		= DiscussHelper::getHelper( 'Ajax' );

		if( !$my->id )
		{
			$ajax->fail( JText::_( 'You are not allowed here' ) );
			return $ajax->send();
		}

		$config 	= DiscussHelper::getConfig();
		$profile 	= DiscussHelper::getTable( 'Profile' );
		$profile->load( $my->id );

		$path 		= rtrim( $config->get( 'main_avatarpath') , DS );
		$path 		= JPATH_ROOT . DS . $path;

		$photoPath 		= $path . DS . $profile->avatar;
		$originalPath 	= $path . DS . 'original_' . $profile->avatar;
		// @rule: Delete existing image first.
		if( JFile::exists( $photoPath ) )
		{
			JFile::delete( $photoPath );
		}

		require_once( DISCUSS_CLASSES . DS . 'simpleimage.php' );
		$image 		= new SimpleImage();
		$image->load( $originalPath );
		$image->crop( 160 , 160 , $x , $y );
		$image->save( $photoPath );

		$ajax->success();
	}

	public function ajaxCheckAlias($alias)
	{
		$disjax		= new disjax();
		$my			= JFactory::getUser();

		// do not let unregistered user
		if ( $my->id <= 0 )
		{
			return false;
		}

		// satinize input
		$filter	= JFilterInput::getInstance();
		$alias	= $filter->clean( $alias, 'ALNUM' );

		// check for existance
		$db		= JFactory::getDBO();
		$query	= 'SELECT `alias` FROM `#__discuss_users` WHERE `alias` = ' . $db->quote($alias) . ' '
				. 'AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $my->id );
		$db->setQuery( $query );
		$result	= $db->loadResult();

		// prepare output
		if ( $result )
		{
			$html	= JText::_('COM_EASYDISCUSS_ALIAS_NOT_AVAILABLE');
			$class	= 'failed';
		}
		else
		{
			$html	= JText::_('COM_EASYDISCUSS_ALIAS_AVAILABLE');
			$class 	= 'success';
		}

		$options = new stdClass();

		// fill in the value
		$disjax->assign( 'profile-alias' , $alias );
		$disjax->script( 'Foundry( "#alias-status" ).html("'.$html.'").removeClass("failed").removeClass("success").addClass( "'.$class.'" );' );
		$disjax->value( 'profile-alias' , $alias );

		$disjax->send();
	}
}
