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

jimport( 'joomla.application.component.view');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'views.php' );
require_once( DISCUSS_HELPERS . DS . 'date.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'parser.php' );
require_once( DISCUSS_HELPERS . DS . 'filter.php' );
require_once( DISCUSS_HELPERS . DS . 'integrate.php' );
require_once( DISCUSS_CLASSES . DS . 'adsense.php' );

class EasyDiscussViewPost extends EasyDiscussView
{
	function display( $tpl = null )
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config 	= DiscussHelper::getConfig();
		$sort		= JRequest::getString('sort', DiscussHelper::getDefaultRepliesSorting() );
		$filteractive	= JRequest::getString('filter', 'allposts');
		$id		= JRequest::getInt('id');
		$acl 	= DiscussHelper::getHelper( 'ACL' );

		// @rule: Add syntax highlighting if required
		if( $config->get( 'main_syntax_highlighter' ) )
		{
			DisjaxLoader::_( 'syntax/shCore','js' ,'media');
			DisjaxLoader::_( 'syntax/shAutoloader','js' ,'media');
			DisjaxLoader::_( 'shCoreDefault' , 'css' );
		}

		// get my data
		$my				= JFactory::getUser();
		$isSiteAdmin	= DiscussHelper::isSiteAdmin();

		$likesModel	= $this->getModel('Likes');


		if ( empty($id) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND') );
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index', false));
		}

		$post	= DiscussHelper::getTable( 'Post' );
		$post->load($id);


		//check post category privacy.
		$catId  		= $post->category_id;
		$canReplyInCat  = false;

		$category	= DiscussHelper::getTable( 'Category' );
		$category->load($catId);

		if( !empty($catId) )
		{
			if( !$category->canAccess() )
			{
				DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_VIEW_POST')  , 'error' );
				$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index', false));
			}

			$canReplyInCat  = $category->canReply();
		}

		// @task: Get category pathways.
		$paths	= $category->getPathway();

		foreach( $paths as $path )
		{
			$this->setPathway( $path->title , $path->link );
		}
		// @task: Set breadcrumbs
		$this->setPathway( $this->escape( $post->title ) );

		$isModerate = ( $post->published == DISCUSS_ID_PENDING ) ? true : false;

		// @task: Add view
		$this->logView();

		$document->addScript(JURI::root() . 'media/foundry/js/dev/jquery.fancybox/jquery.fancybox.js');
		$document->addStylesheet(JURI::root() . 'media/foundry/js/dev/jquery.fancybox/jquery.fancybox.css');

		//check whether this the main post or not.
		if($post->parent_id != 0)
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID')  , 'error' );
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index', false));
		}

		if($post->published == DISCUSS_ID_PENDING && ( !$isSiteAdmin && $post->user_id != $my->id ))
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID')  , 'error' );
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index', false));
		}

		//update the hits
		$post->hit();

		//set page title
		$document->setTitle( $post->title );

		$document->addHeadLink( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id ) , 'canonical' , 'rel' );

		// parse BBCode to display
		$post->title_clear  = $post->title;
		$post->content_raw	= $post->content;

		// filtering badwords
		$post->title 		= DiscussHelper::wordFilter( $post->title );
		$post->content 		= DiscussHelper::wordFilter( $post->content );

		$post->content	= Parser::html2bbcode( $post->content ); //this line is to make compatible with 1.1.x
		$post->content	= $this->escape( $post->content );
		$post->content	= Parser::bbcode( $post->content );


		// format created date by adding offset if any
		$newDate    	= DiscussDateHelper::getDate( $post->created );
		$post->created	= $newDate->toFormat();

		$post->likesAuthor = DiscussHelper::getLikesAuthors('post', $post->id, $my->id);
		$post->isLike   	= $likesModel->isLike('post', $post->id, $my->id);

		// set post owner
		$owner = new stdClass();

		if ( $post->user_id == 0 )
		{
			$owner->id		= 0;
			$owner->name	= 'Guest';
			$post->user		= $owner;
		}
		else
		{
			$poster = DiscussHelper::getTable( 'Profile' );
			$poster->load($post->user_id );

			$owner->id		= $post->user_id;
			$owner->name	= $poster->getName();
			$owner->link	= $poster->getLink();
			$post->user		= $owner;
		}

		$voteModel			= $this->getModel('votes');
		$post->voted		= $voteModel->checkUserVote( $post->id );//$reply->hasVoted();
		$post->totalVote	= $post->sum_totalvote;

		if ( $config->get( 'main_content_trigger_posts' ) )
		{
			// process content plugins
			DiscussEventsHelper::importPlugin( 'content' );
			DiscussEventsHelper::onContentPrepare('post', $post);

			$post->event = new stdClass();

			$results	= DiscussEventsHelper::onContentAfterTitle('post', $post);
			$post->event->afterDisplayTtle	= trim(implode("\n", $results));

			$results	= DiscussEventsHelper::onContentBeforeDisplay('post', $post);
			$post->event->beforeDisplayContent	= trim(implode("\n", $results));

			$results	= DiscussEventsHelper::onContentAfterDisplay('post', $post);
			$post->event->afterDisplayContent	= trim(implode("\n", $results));
		}

		// now get all replies if any
		$replyModel		= $this->getModel('Posts');
		$replies		= $replyModel->getReplies( $id, $sort );
		$pagination		= $replyModel->getPagination( $id, $sort );
		$totalReplies   = $replyModel->getTotalReplies($id);

		$replies		= DiscussHelper::formatReplies( $replies );

		// get accepted reply
		$acceptedReplies	= $replyModel->getAcceptedReply( $id );
		$acceptedReplies	= DiscussHelper::formatReplies( $acceptedReplies );

		$acceptedReply      = '';
		if( count( $acceptedReplies ) > 0 )
			$acceptedReply  = $acceptedReplies[0];

		// get post tags
		$postsTagsModel	= $this->getModel('PostsTags');

		$tags = $postsTagsModel->getPostTags( $id );


		//check all the 'can' or 'canot' here.
		$isMainLocked   = ($post->islock) ? true : false;
		$canDeletePost  = false;
		$canDeleteReply = false;
		$canTag     	= false;
		$canReply   	= ((($my->id != 0) || ($my->id == 0 && $config->get('main_allowguestpost', 0))) && $acl->allowed('add_reply', '0') ) ? true : false;

		if( $canReply )
		{
			// now we need to check against the associated category acl huhu.
			$canReply = $canReplyInCat;
		}

		if($config->get('main_allowdelete', 2) == 2)
		{
			$canDeletePost	= (!$post->islock && $isSiteAdmin) ? true : false;
			$canDeleteReply	= (!$post->islock && $isSiteAdmin) ? true : false;
		}
		else if($config->get('main_allowdelete', 2) == 1)
		{
			$canDeletePost	= ( (!$post->islock || $isSiteAdmin) && $my->id != 0 && $acl->allowed('delete_question', '0') ) ? true : false;
			$canDeleteReply	= ( (!$post->islock || $isSiteAdmin) && $my->id != 0 && $acl->allowed('delete_reply', '0') ) ? true : false;
		}

		// load language strings
		DiscussHelper::loadString( JRequest::getVar('view') );

		// load editor
		DiscussHelper::loadEditor();

		//recaptcha integration
		$recaptcha	= '';
		$enableRecaptcha	= $config->get('antispam_recaptcha');
		$publicKey			= $config->get('antispam_recaptcha_public');

		if(  $enableRecaptcha && !empty( $publicKey ) )
		{
			require_once( DISCUSS_CLASSES . DS . 'recaptcha.php' );
			$recaptcha	= getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl') );
		}

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$creator = DiscussHelper::getTable( 'Profile' );
		$creator->load( $owner->id);

		// Facebook Like integrations
		//try to search for the 1st img in the post
		$img            = '';
		$pattern		= '#<img[^>]*>#i';
		preg_match( $pattern , $post->content , $matches );

		if($matches )
		{
			$img    = $matches[0];
		}

		//image found. now we process further to get the absolute image path.
		if(! empty($img))
		{
			//get the img src

			$pattern = '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
			preg_match($pattern, $img, $matches);
			if($matches)
			{
				$imgPath   = $matches[1];
				$imgSrc    = DiscussImageHelper::rel2abs($imgPath, JURI::root());
				$document->addCustomTag( '<meta property="og:image" content="' . $imgSrc . '"/> ');
			}
		}

		if( $config->get('integration_facebook_like') )
		{
			if( $config->get('integration_facebook_like_appid') != '')
				$document->addCustomTag( '<meta property="fb:app_id" content="' . $config->get('integration_facebook_like_appid') . '"/> ');

			if( $config->get('integration_facebook_like_admin') != '' )
				$document->addCustomTag( '<meta property="fb:admins" content="' . $config->get('integration_facebook_like_admin') . '"/>' );
		}

		$document->addCustomTag( '<meta property="og:title" content="' . $post->title . '" />' );


		$maxContentLen	= 350;
		$text			= $post->content;
		if( $maxContentLen > 0 )
		{
			$text		= strip_tags( $text );
			$text		= ( JString::strlen( $text ) > $maxContentLen ) ? JString::substr( $text, 0, $maxContentLen) . '...' : $text;

		}

		$activeCategory = DiscussHelper::getTable( 'Category' );
		$activeCategory->load( $post->category_id );

		$url	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id, false , true );
		$document->addCustomTag( '<meta property="og:description" content="' . Parser::removeCodes( $text ) . '" />' );
		$document->addCustomTag( '<meta property="og:type" content="article" />' );
		$document->addCustomTag( '<meta property="og:url" content="' . $url . '" />' );

		$googleAdsense	= DiscussGoogleAdsense::getHTML();

		// Add description into the headers.
		$document->setMetadata('keywords', $post->title );
		$document->setMetadata('description', str_ireplace( "\r\n" , '' , $text ) );

		// Add logging for user.
		if( $my->id != $post->get( 'user_id') )
		{
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.read.discussion' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_READ_POST' , $post->title ) );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.read.discussion' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.read.discussion' , $my->id );
		}

		// @rule: Clear up any notifications that are visible for the user.
		$notifications	= $this->getModel( 'Notification' );
		$notifications->markRead(	$my->id ,
									$post->id ,
									array(
											DISCUSS_NOTIFICATIONS_REPLY,
											DISCUSS_NOTIFICATIONS_RESOLVED,
											DISCUSS_NOTIFICATIONS_ACCEPTED,
											DISCUSS_NOTIFICATIONS_FEATURED,
											DISCUSS_NOTIFICATIONS_COMMENT,
											DISCUSS_NOTIFICATIONS_MENTIONED,
											DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION,
											DISCUSS_NOTIFICATIONS_LIKES_REPLIES
										)
								);

		// Parse @username links.
		$post->content 		= DiscussHelper::getHelper( 'String' )->nameToLink( $post->content );

		$isPostSubscribed = false;
		if( $my->id != 0 )
		{
			//logged user. check if this user subscribed to site subscription or not.
			$isPostSubscribed = DiscussHelper::isPostSubscribed( $my->id, $post->id );
		}

		// load main post view
		$tplA	= new DiscussThemes();
		$tplA->set( 'isMine'	, DiscussHelper::isMine($post->user_id) );
		$tplA->set( 'isAdmin'	, $isSiteAdmin );
		$tplA->set( 'post'		, $post );
		$tplA->set( 'tags'		, $tags );
		$tplA->set( 'config'	, $config );
		$tplA->set( 'canDeletePost'	, $canDeletePost );
		$tplA->set( 'creator'	, $creator );
		$tplA->set( 'my'		, $my );
		$tplA->set( 'isModerate', $isModerate );
		$tplA->set( 'acl'	, $acl );
		$tplA->set( 'polls'		, $post->getPolls() );
		$tplA->set( 'issubscribed'	, $isPostSubscribed );
		$tplA->set( 'activeCategory' , $activeCategory );
		$questionHTML	= $tplA->fetch( 'entry.post.php' );

		$tpl	= new DiscussThemes();
		$tpl->set( 'params'		, $post->params );
		$tpl->set( 'isMine'		, DiscussHelper::isMine($post->user_id) );
		$tpl->set( 'isAdmin'	, $isSiteAdmin );
		$tpl->set( 'questionHTML'	, $questionHTML );
		$tpl->set( 'replies'	, $replies );
		$tpl->set( 'totalReplies', $totalReplies );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'paginationType'	, DISCUSS_REPLY_TYPE );
		$tpl->set( 'parent_id'	, $post->id );
		$tpl->set( 'parent_catid'	, $post->category_id );
		$tpl->set( 'parent_title'	, $post->title_clear );
		$tpl->set( 'sort'		, $sort );
		$tpl->set( 'filter'		, $filteractive );
		$tpl->set( 'config'	, $config );
		$tpl->set( 'canReply', $canReply );
		$tpl->set( 'canDeleteReply'	, $canDeleteReply );
		$tpl->set( 'isMainLocked'	, $isMainLocked );
		$tpl->set( 'question'	, $post );
		$tpl->set( 'user', $my );
		$tpl->set( 'recaptcha' , $recaptcha );
		$tpl->set( 'isModerate'	, $isModerate );
		$tpl->set( 'acceptedReply'	, $acceptedReply );
		$tpl->set( 'acl'	, $acl );
		$tpl->set( 'googleAdsense'	, $googleAdsense );

		$filerBar   = $tpl->fetch( 'filter.php' );
		$tpl->set( 'filterbar', $filerBar );

		if( !empty( $post->password ) && !DiscussHelper::hasPassword( $post ) )
		{
			$tpl->set( 'post' , $post );
			$tpl->set( 'postview' , '1' );

			$tpl->set( 'activeCategory' , $activeCategory );
			$tpl->set( 'creator'	, $creator );
			$tpl->set( 'my'		, $my );

			echo $tpl->fetch( 'entry.password.php' );
			return;
		}

		echo $tpl->fetch( 'entry.php' );

	}

	/**
	 *	Deprecated Method
	 **/
	function submit()
	{
		$mainframe	= JFactory::getApplication();
		$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=ask', false) );
		$mainframe->close();
	}
}
