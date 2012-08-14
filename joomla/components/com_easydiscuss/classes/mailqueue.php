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


class DMailQueue
{
	function DMailQueue()
	{
		//constructor
	}

	function sendOnPageLoad($max = 5)
	{
		$db 	= JFactory::getDBO();
		$config	= DiscussHelper::getConfig();

		// Delete existing mails that has already been sent.
		$query		= 'DELETE FROM ' . $db->nameQuote( '#__discuss_mailq' ) . ' WHERE '
					. $db->nameQuote( 'status' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$db->Query();

		$query  = 'SELECT `id` FROM `#__discuss_mailq` WHERE `status` = 0';
		$query  .= ' ORDER BY `created` ASC';
		$query  .= ' LIMIT ' . $max;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if(! empty($result))
		{
			foreach($result as $mail)
			{
				$mailq	= DiscussHelper::getTable( 'MailQueue' );
				$mailq->load($mail->id);

				if(JUtility::sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body , $mailq->ashtml ))
				{
					$mailq->status  = 1;
					$mailq->store();
				}
			}
		}

	}

	public function parseEmails()
	{
		require_once( DISCUSS_CLASSES . DS . 'mailbox.php' );

		$mailer	= new DiscussMailer();
		$state	= $mailer->connect();

		// @task: Only search for messages that are new.
		$unread	= $mailer->searchMessages( 'UNSEEN' );

		if( !$state )
		{
			echo JText::_( 'COM_EASYDISCUSS_UNABLE_TO_CONNECT_TO_MAIL_SERVER' );
			return false;
		}

		if( !$unread )
		{
			echo JText::_( 'COM_EASYDISCUSS_NO_EMAILS_TO_PARSE' );
			return false;
		}

		$filter		= JFilterInput::getInstance();
		$config		= DiscussHelper::getConfig();
		$total		= 0;

		foreach( $unread as $sequence )
		{
			$info		= $mailer->getMessageInfo( $sequence );
			$from		= $info->from;
			$senderName	= $from[0]->personal;
			$subject	= $filter->clean( $info->subject );

			// @rule: Detect if this is actually a reply.
			preg_match( '/\[\#(.*)\]/is' , $subject , $matches );

			$isReply	= !empty( $matches );
			$message	= new DiscussMailerMessage( $mailer->stream , $sequence );

			$post		= DiscussHelper::getTable( 'Post' );
			$post->set( 'title'		, $subject );
			$post->set( 'content' 	, $message->getPlain() );
			$post->set( 'published'	, DISCUSS_ID_PUBLISHED );
			$post->set( 'created'	, JFactory::getDate()->toMySQL() );
			$post->set( 'modified'	, JFactory::getDate()->toMySQL() );


			if( $isReply && !$config->get( 'main_email_parser_replies') )
			{
				continue;
			}

			if( $isReply )
			{
				$parentId	= (int) $matches[1];
				$post->set( 'parent_id' , $parentId );
			}

			// @TODO: Make this category configurable from the back end?
			$post->set( 'category_id' , $config->get( 'main_email_parser_category' ) );

			// @rule: Map the sender's email with the user in Joomla?
			$replyToEmail	= $info->senderaddress;

			// Lookup for the user based on their email address.
			$user			= DiscussHelper::getUserByEmail( $replyToEmail );

			if( $user instanceof JUser )
			{
				$post->set( 'user_id'	, $user->get( 'id' ) );
				$post->set( 'user_type'	, DISCUSS_POSTER_MEMBER );
			}
			else
			{
				// Guest posts
				$post->set( 'user_type' 	, DISCUSS_POSTER_GUEST );
				$post->set( 'poster_name'	, $senderName );
				$post->set( 'poster_email'	, $replyToEmail );
			}

			// Skip processing of unknown guest users.
			if( $post->get( 'user_type') == DISCUSS_POSTER_GUEST && !$config->get( 'main_allowguestpostquestion' ) )
			{
				continue;				
			}

			if( $config->get( 'main_email_parser_moderation' ) )
			{
				$post->set( 'published' , DISCUSS_ID_UNPUBLISHED );
			}

			// @rule: Process the post
			$post->store();

			// @task: Increment the count.
			$total	+= 1;

			// @rule: Only send autoresponders when it's a new post.
			if( !$isReply && $config->get( 'main_email_parser_receipt' ) && $post->get( 'published' ) == DISCUSS_ID_PUBLISHED )
			{
				$sendAsHTML	= (bool) $config->get( 'notify_html_format' );

				$theme		= new DiscussThemes();
				$postId		= $post->get( 'id' );

				if( $isReply )
				{
					$postId	= $parentId;
				}

				$url		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $postId , false , true );


				$emailData 				= array();
				$emailData['postLink']	= $url;

				if( $post->get( 'user_type') == DISCUSS_POSTER_GUEST )
				{
					$emailData[ 'postAuthor' ]	= $senderName;
				}
				else
				{
					$profile 	= DiscussHelper::getTable( 'Profile' );
					$profile->load( $user->id );

					$emailData['postAuthor' ]	= $profile->getName();
				}

				require_once( DISCUSS_CLASSES . DS . 'notification.php' );
				$notification	= new DNotification();
				$output 		= $notification->getEmailTemplateContent( 'email.accepted.responder.php' , $emailData );

				$app		= JFactory::getApplication();

				if( !$sendAsHTML )
				{
					$output	= strip_tags( $output );
				}

				// @rule: Send confirmation message.
				JUtility::sendMail( $app->getCfg( 'mailfrom' ) , $app->getCfg( 'fromname' ) , $replyToEmail , '[#' . $post->id . ']: ' . $subject , $output , $sendAsHTML );
			}
		}

		echo JText::sprintf( 'COM_EASYDISCUSS_EMAIL_PARSED' , $total );

		return true;
	}
}
