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

jimport( 'joomla.filesystem.file' );

class DiscussJomsocialHelper
{
	public function addActivityQuestion( $post )
	{
		$core	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		$config = DiscussHelper::getConfig();

		if( !JFile::exists( $core ) )
		{
		    return false;
		}

		require_once( $core );

		// @rule: Insert points for user.
		if( $config->get( 'integration_jomsocial_points' ) )
		{
		    CFactory::load( 'libraries' , 'userpoints' );
		    CUserPoints::assignPoint( 'com_easydiscuss.new.discussion' , $post->user_id );
		}

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_new_question' ) )
		{
			return false;
		}



		$link   		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id );
		$title          = JString::substr( $post->title, 0 , 30 ) . '...';
		$content        = '';

		if( $config->get( 'integration_jomsocial_activity_new_question_content' ) )
		{
		    $content    = $post->content;
			$pattern	= '#<img[^>]*>#i';
			preg_match( $pattern , $content , $matches );

			$content    = strip_tags( $content );
			$content    = JString::substr( $content , 0 , $config->get( 'integration_jomsocial_activity_content_length') ) . '...';

			if( $matches )
			{
			    $matches[0] = JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float:left;height:auto;width: 120px !important;"' , $matches[ 0 ] );
			    $content    = $matches[ 0 ] . $content . '<div style="clear:both;"></div>';
			}
			$content    .= '<div style="text-align: right;"><a href="' . $link . '">' . JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION_REPLY_QUESTION' ) . '</a></div>';
		}

		//get category privacy.
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $post->category_id );

		$obj			= new stdClass();
		$obj->access	= $category->private;
		$obj->title		= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_NEW_QUESTION' , $link , $title );
		$obj->content	= $content;
		$obj->cmd 		= 'easydiscuss.question.add';
		$obj->actor   	= $post->user_id;
		$obj->target  	= 0;
		$obj->like_id   = $post->id;
		$obj->like_type = 'com_easydiscuss';
		$obj->comment_id    = $post->id;
		$obj->comment_type  = 'com_easydiscuss';
		$obj->app		= 'com_easydiscuss';
		$obj->cid		= $post->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function addActivityReply( $post )
	{
		$core	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		$config = DiscussHelper::getConfig();

		if( !JFile::exists( $core ) )
		{
		    return false;
		}

		require_once( $core );

		// @rule: Insert points for user.
		if( $config->get( 'integration_jomsocial_points' ) )
		{
		    CFactory::load( 'libraries' , 'userpoints' );
		    CUserPoints::assignPoint( 'com_easydiscuss.reply.discussion' , $post->user_id );
		}

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_reply_question' ) )
		{
			return false;
		}

		$link   		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id );

		$parent			= DiscussHelper::getTable( 'Post' );
		$parent->load( $post->parent_id );

		$title          = JString::substr( $parent->title, 0 , 30 ) . '...';
		$content        = '';

		if( $config->get( 'integration_jomsocial_activity_reply_question_content' ) )
		{
		    $content    = $post->content;
			$pattern	= '#<img[^>]*>#i';
			preg_match( $pattern , $content , $matches );

			$content    = strip_tags( $content );
			$content    = JString::substr( $content , 0 , $config->get( 'integration_jomsocial_activity_content_length') ) . '...';

			if( $matches )
			{
			    $matches[0] = JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float:left;height:auto;width: 120px !important;"' , $matches[ 0 ] );
			    $content    = $matches[ 0 ] . $content . '<div style="clear:both;"></div>';
			}
			$content    .= '<div style="text-align: right;"><a href="' . $link . '">' . JText::_( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION_PARTICIPATE' ) . '</a></div>';
		}

		$obj			= new stdClass();
		$obj->title		= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_REPLY_QUESTION' , $link , $title );
		$obj->content	= $content;
		$obj->cmd 		= 'easydiscuss.question.reply';
		$obj->actor   	= $post->user_id;
		$obj->target  	= 0;
		$obj->like_id   = $post->id;
		$obj->like_type = 'com_easydiscuss';
		$obj->comment_id    = $post->id;
		$obj->comment_type  = 'com_easydiscuss';
		$obj->app		= 'com_easydiscuss';
		$obj->cid		= $post->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function addActivityLikes( $post )
	{
		$core	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		$config = DiscussHelper::getConfig();
		$my     = JFactory::getUser();

		if( !JFile::exists( $core ) )
		{
		    return false;
		}

		require_once( $core );

		// @rule: Insert points for user.
		if( $config->get( 'integration_jomsocial_points' ) )
		{
		    CFactory::load( 'libraries' , 'userpoints' );
		    CUserPoints::assignPoint( 'com_easydiscuss.like.discussion' , $my->id );
		}

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_likes' ) )
		{
			return false;
		}

		$link   		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id );

		$title          = JString::substr( $post->title, 0 , 30 ) . '...';
		$obj			= new stdClass();
		$obj->title		= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_LIKE_ITEM' , $link , $title );
		$obj->content	= '';
		$obj->cmd 		= 'easydiscuss.question.like';
		$obj->actor   	= $my->id;
		$obj->target  	= 0;
		$obj->like_id   = $post->id;
		$obj->like_type = 'com_easydiscuss';
		$obj->comment_id    = $post->id;
		$obj->comment_type  = 'com_easydiscuss';
		$obj->app		= 'com_easydiscuss';
		$obj->cid		= $post->id;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}
	
	public function addActivityBadges( $badge )
	{
		$core	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		$config = DiscussHelper::getConfig();
		$my     = JFactory::getUser();

		if( !JFile::exists( $core ) )
		{
		    return false;
		}

		require_once( $core );

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if new badges activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_badges', 0 ) )
		{
			return false;
		}

		$link   		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->id );
		$content        = '<img src="' . $badge->getAvatar() . '" />';

		$title          = JString::substr( $badge->title, 0 , 30 ) . '...';
		$obj			= new stdClass();
		$obj->title		= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_BADGES_ITEM' , $link , $title );
		$obj->content	= $content;
		$obj->cmd 		= 'easydiscuss.badges.earned';
		$obj->actor   	= $my->id;
		$obj->target  	= 0;
		$obj->like_id   = $badge->uniqueId;
		$obj->like_type = 'com_easydiscuss_badge';
		$obj->comment_id    = $badge->uniqueId;
		$obj->comment_type  = 'com_easydiscuss_badge';
		$obj->app		= 'com_easydiscuss';
		$obj->cid		= $badge->uniqueId;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}
	
	public function addActivityRanks( $userRanks )
	{
		$core	= JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'core.php';
		$config = DiscussHelper::getConfig();
		$my     = JFactory::getUser();

		if( !JFile::exists( $core ) )
		{
		    return false;
		}

		require_once( $core );

		$lang	= JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		// We do not want to add activities if ranking activity is disabled.
		if( !$config->get( 'integration_jomsocial_activity_ranks', 0 ) )
		{
			return false;
		}

		$title          = JString::substr( $userRanks->title, 0 , 30 );
		$obj			= new stdClass();
		$obj->title		= JText::sprintf( 'COM_EASYDISCUSS_JOMSOCIAL_ACTIVITY_RANKS_ITEM' , $title );
		$obj->content	= '';
		$obj->cmd 		= 'easydiscuss.rank.up';
		$obj->actor   	= $my->id;
		$obj->target  	= 0;
		$obj->like_id   = $userRanks->uniqueId;
		$obj->like_type = 'com_easydiscuss_rank';
		$obj->comment_id    = $userRanks->uniqueId;
		$obj->comment_type  = 'com_easydiscuss_rank';
		$obj->app		= 'com_easydiscuss';
		$obj->cid		= $userRanks->uniqueId;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}
}
