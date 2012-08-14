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

jimport('joomla.application.component.controller');

require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'date.php' );
require_once( DISCUSS_HELPERS . DS . 'input.php' );
require_once( DISCUSS_HELPERS . DS . 'filter.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );


class EasyDiscussControllerPosts extends JController
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

	public function setPassword()
	{
		$id = JRequest::getVar('id', '');

		if(!empty($id))
		{
			$password	= JRequest::getVar('discusspassword', '');
			$session	= JFactory::getSession();
			$session->set('DISCUSSPASSWORD_' . $id , $password , 'com_easydiscuss' );

			$post		= DiscussHelper::getTable( 'Post' );
			$post->load( $id );

			if( $post->password != $password )
			{
				DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_INVALID_PASSWORD_PROVIDED') , DISCUSS_QUEUE_ERROR );
			}
		}

		$return = JRequest::getVar('return');

		$this->setRedirect( DiscussRouter::_( base64_decode($return) , false ) );
	}

	/*
	 * Allows anyone to approve replies provided that they get the correct key
	 *
	 * @param	null
	 * @return	null
	 */
	public function approvePost()
	{
		$mainframe	= JFactory::getApplication();
		$key		= JRequest::getVar( 'key' , '' );
		$redirect	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false );

		if( empty( $key ) )
		{
			$mainframe->redirect( $redirect , JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED_HERE' ) , 'error' );
			$mainframe->close();
		}

		$hashkey	= DiscussHelper::getTable( 'HashKeys' );

		if( !$hashkey->loadByKey( $key ) )
		{
			$mainframe->redirect( $redirect , JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED_HERE' ) , 'error' );
			$mainframe->close();
		}

		$post	= DiscussHelper::getTable( 'Post' );
		$post->load( $hashkey->uid );
		$post->published    = DISCUSS_ID_PUBLISHED;

		// @trigger: onBeforeSave
		$isNew	= (bool) $post->id;
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentBeforeSave('post', $post, $isNew);

		if ( !$post->store() )
		{
			JError::raiseError(500, $post->getError() );
		}

		// @trigger: onAfterSave
		DiscussEventsHelper::onContentAfterSave('post', $post, $isNew);

		// Delete the unused hashkey now.
		$hashkey->delete();

		$message    = $hashkey->type == DISCUSS_REPLY_TYPE ? JText::_( 'COM_EASYDISCUSS_MODERATE_REPLY_PUBLISHED' ) : JText::_( 'COM_EASYDISCUSS_MODERATE_POST_PUBLISHED' );
		$pid        = $hashkey->type == DISCUSS_REPLY_TYPE ? $post->parent_id : $post->id;
		$mainframe->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $pid , false ) , $message , 'success' );
	}

	/**
	 * Delete current post given the post id.
	 * It will also delete all childs related to this entry.
	 */
	function delete()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$my			= JFactory::getUser();
		$id			= JRequest::getInt( 'id' );
		$mainframe	= JFactory::getApplication();
		$url		= JRequest::getVar( 'url' , '' );

		if( !empty( $url ) )
		{
			$url		= base64_decode( $url );
		}
		else
		{
			$url		= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $id , false );
		}

		if( !$id )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_ENTRY_DELETE_MISSING_ID') , DISCUSS_QUEUE_ERROR );
			$mainframe->redirect( $url );
			return;
		}

		$post	= DiscussHelper::getTable( 'Post' );
		$post->load( $id );

		$acl		= DiscussHelper::getHelper( 'ACL' );
		$type		= $post->parent_id ? 'reply' : 'question';

		$isMine		= DiscussHelper::isMine( $post->user_id );
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin && !$acl->allowed( 'delete_' . $type, '0' ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_ENTRY_DELETE_NO_PERMISSION') , DISCUSS_QUEUE_ERROR );
			$mainframe->redirect( $url );
			return;
		}

		if( $post->islock && !$isAdmin() )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_ENTRY_DELETE_LOCKED') , DISCUSS_QUEUE_ERROR );
			$mainframe->redirect( $url );
			return;
		}

		// @trigger: onBeforeDelete
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentBeforeDelete('post', $post);

		if( !$post->delete() )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_ENTRY_DELETE_ERROR') , DISCUSS_QUEUE_ERROR );
			$mainframe->redirect( $url );
			return;
		}

		// @trigger: onAfterDelete
		DiscussEventsHelper::onContentAfterDelete('post', $post);

		// @rule: Process AUP integrations
		if( empty( $post->parent_id ) )
		{
			DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_DISCUSSION , $post->user_id , $post->title );
		}
		else
		{
			DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_REPLY , $post->user_id , $post->title );
		}

		if( $type == 'question' )
		{
			$model		= $this->getModel('Posts');
			$model->deleteAllReplies( $id );
			$url		= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false );

			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_ENTRY_DELETED' ) , DISCUSS_QUEUE_INFO );
		}
		else
		{
			//this is a reply delete. now we check if this reply get accepted previously or not.
			// if yes, then upload the parent post to unresolved.
			$answerRemoved  = false;

			if( $post->answered )
			{
				$parent	= DiscussHelper::getTable( 'Post' );
				$parent->load( $post->parent_id );
				$parent->isresolve = DISCUSS_ENTRY_UNRESOLVED;
				$parent->store();

				$answerRemoved  = true;
			}

			$msgText	= ( $answerRemoved ) ? JText::_( 'COM_EASYDISCUSS_REPLY_DELETED_AND_UNRESOLVED' ) : JText::_( 'COM_EASYDISCUSS_REPLY_DELETED' ) ;
			DiscussHelper::setMessageQueue( $msgText , DISCUSS_QUEUE_INFO );
		}

		$mainframe->redirect( $url );

		return;
	}

	/**
	 * Submit new posts
	 */
	function submit()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$config 	= DiscussHelper::getConfig();
		$my			= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$acl = DiscussHelper::getHelper( 'ACL' );

		if( empty($my->id) && !$config->get('main_allowguestpostquestion', 0))
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_POST_PLEASE_LOGIN' ) , DISCUSS_QUEUE_ERROR );
			$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss' , false ) );
			return;
		}
		else if( $my->id != 0 && !$acl->allowed('add_question', '0') )
		{
			$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss' , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') , DISCUSS_QUEUE_ERROR );
			return;
		}

		if( JRequest::getMethod() == 'POST' )
		{
			$user 		= JFactory::getUser();
			$date 		= JFactory::getDate();

			// get all forms value
			$data		= JRequest::get( 'post' );

			if(! $this->_fieldValidate($data))
			{
				DiscussHelper::storeSession($data, 'NEW_POST_TOKEN');
				$mainframe->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=ask' , false) );
			}

			// get id if available
			$id			= JRequest::getInt('id', 0);
			$recaptcha	= $config->get( 'antispam_recaptcha');
			$public		= $config->get( 'antispam_recaptcha_public');
			$private	= $config->get( 'antispam_recaptcha_private');

			// bind the table
			$post	= DiscussHelper::getTable( 'Post' );
			$post->load( $id );

			// set is new value
			$isNew		= !$post->id ? true : false;

			if( $recaptcha && $public && $private )
			{
				require_once( DISCUSS_CLASSES . DS .'recaptcha.php' );

				$obj = DiscussRecaptcha::recaptcha_check_answer( $private , $_SERVER['REMOTE_ADDR'] , $data['recaptcha_challenge_field'] , $data['recaptcha_response_field'] );

				if(!$obj->is_valid)
				{
					DiscussHelper::storeSession( $data , 'NEW_POST_TOKEN');
					DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_POST_INVALID_RECAPTCHA_RESPONSE' ) , DISCUSS_QUEUE_ERROR );
					$mainframe->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=ask' , false) );
					return;
				}
			}

			$previousTags = array();
			if(!$isNew)
			{
				//check if admin or is owner before allowing edit.
				$isMine		= DiscussHelper::isMine($post->user_id);
				$isAdmin	= DiscussHelper::isSiteAdmin();

				if ( empty($user->id) || (!$isMine && !$isAdmin) )
				{
					DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION' ) , DISCUSS_QUEUE_ERROR );
					$this->setRedirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id='.$id , false) );
					return;
				}

				//get previous tags first and then delete it to be added back in later.
				$postsTagsModel = $this->getModel('PostsTags');
				$tmppreviousTags = $postsTagsModel->getPostTags($id);
				if(!empty($tmppreviousTags))
				{
					foreach($tmppreviousTags as $previoustag)
					{
						$previousTags[] = $previoustag->id;
					}
				}

				if($acl->allowed('add_tag', '0'))
				{
					$postsTagsModel->deletePostTag( $id );
				}
			}

			// get post parent id
			$parent				= JRequest::getInt('parent_id', 0);

			// we need to get the raw content here.
			$data['dc_reply_content']	= JRequest::getVar( 'dc_reply_content', '', 'post', 'none' , JREQUEST_ALLOWRAW );

			// set alias
			$data['alias'] 		= DiscussHelper::getAlias( $data['title'] , 'post' , $post->id );
			$data['user_type']	= empty($user->id)? 'guest' : 'member';

			// @task: Akismet integrations here.
			if( $config->get( 'antispam_akismet' ) && ( $config->get('antispam_akismet_key') ) )
			{
				require_once( DISCUSS_CLASSES . DS . 'akismet.php' );

				$akismet = new Akismet( JURI::root() , $config->get( 'antispam_akismet_key' ) , array(
								'author'    => $user->name,
								'email'     => $user->email,
								'website'   => JURI::root() ,
								'body'      => $data['content'] ,
								'alias' => ''
								) );

				if( !$akismet->errorsExist() )
				{
					if( $akismet->isSpam() )
					{
						DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED' ) , DISCUSS_QUEUE_ERROR );
						$mainframe->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=ask' , false ) );
					}
				}
			}

			//get previous post status before binding.
			$prevPostStatus = $post->published;

			// If post is being edited, do not change the owner of the item.
			if( !$post->id )
			{
				//set post owner
				$data['user_id']	= empty($post->user_id)? $user->id : $post->user_id;
			}

			$post->bind( $data , true );

			if($config->get('main_moderatepost', 0))
			{
				$post->published	= DISCUSS_ID_PENDING;
			}
			else
			{
				$post->published	= DISCUSS_ID_PUBLISHED;
			}

			// hold last inserted ID in DB
			$lastId = null;

			// @rule: Bind parameters
			$post->bindParams( $data );

			// @rule: Check for maximum length if the category defines it.
			$category 		= DiscussHelper::getTable( 'Category' );
			$category->load( $post->category_id );

			if( $category->getParam( 'maxlength' ) )
			{
				$length 	= JString::strlen( $post->content );

				if( $length > $category->getParam( 'maxlength_size' , 1000 ) )
				{
					DiscussHelper::storeSession( $data , 'NEW_POST_TOKEN');
					DiscussHelper::setMessageQueue( JText::sprintf('COM_EASYDISCUSS_MAXIMUM_LENGTH_EXCEEDED' , $category->getParam( 'maxlength_size' , 1000 ) ) , DISCUSS_QUEUE_ERROR );
					$mainframe->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=ask' , false ) );
				}
			}

			// @trigger: onBeforeSave
			DiscussEventsHelper::importPlugin( 'content' );
			DiscussEventsHelper::onContentBeforeSave('post', $post, $isNew);

			if ( !$post->store() )
			{
				JError::raiseError(500, $post->getError() );
			}

			// @trigger: onAfterSave
			DiscussEventsHelper::onContentAfterSave('post', $post, $isNew);


			// @task: Process poll items
			$includePolls	= JRequest::getBool( 'polls' , false );

			if( !$isNew && !$includePolls )
			{
				// Delete polls if necessary since this post doesn't contain any polls.
				$post->removePoll();
			}

			if( $includePolls && $config->get( 'main_polls') )
			{
				$pollItems		= JRequest::getVar( 'pollitems' );

				if( $pollItems )
				{
					if( !$isNew )
					{
						// Try to detect which poll items needs to be removed.
						$remove	= JRequest::getVar( 'pollsremove' );

						if( !empty( $remove ) )
						{
							$remove	= explode( ',' , $remove );

							foreach( $remove as $id )
							{
								$id 	= (int) $id;
								$poll	= DiscussHelper::getTable( 'Poll' );
								$poll->load( $id );
								$poll->delete();
							}
						}
					}

					foreach( $pollItems as $item )
					{
						$value	= (string) $item;

						if( trim( $value ) == '' )
						    continue;

						$poll	= DiscussHelper::getTable( 'Poll' );

						if( !$poll->loadByValue( $value , $post->id ) )
						{
							$poll->set( 'value' 	, $value );
							$poll->set( 'post_id'	, $post->get( 'id' ) );

							$poll->store();
						}
					}
				}
			}

			$lastId	= $post->id;

			// Bind file attachments
			if( $acl->allowed( 'add_attachment' , '0' ) )
			{
				$post->bindAttachments();
			}

			if($config->get( 'notify_owner' ) && $isNew)
			{
				if( isset( $data['self_subscribe'] ) )
				{
					$subscription_info = array();
					$subscription_info['type'] = 'post';

					if(!empty($user->id))
					{
						$subscription_info['userid'] = $user->id;
						$subscription_info['email'] = $user->email;
						$subscription_info['name'] = $user->name;
						$subscription_info['member'] = '1';
					}
					else
					{
						$subscription_info['userid'] = '0';
						$subscription_info['email'] = $data['poster_email'];
						$subscription_info['name'] = $data['poster_name'];
						$subscription_info['member'] = '0';
					}

					$subscription_info['cid'] = $post->id;

					$subscription_info['interval'] = 'instant';

					$subscribeModel	= $this->getModel( 'Subscribe' );
					$subscribeModel->addSubscription($subscription_info);
				}
			}


			$isModerate = ($post->published == DISCUSS_ID_PENDING) ? true : false;

			// @rule: Process autopostings
			if( $post->published == DISCUSS_ID_PUBLISHED )
			{
				$callback	= DiscussRouter::getRoutedUrl( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id , false , true );

				$sites		= array( 'facebook' , 'twitter' );

				foreach( $sites as $site )
				{
					if( $config->get( 'main_autopost_' . $site ) )
					{
						$oauth	= DiscussHelper::getTable( 'Oauth' );
						$exists	= $oauth->loadByType( $site );

						$oauthPost	= DiscussHelper::getTable( 'OauthPosts' );

						if( $exists && !empty( $oauth->access_token ) && !$oauthPost->exists( $post->id , $oauth->id ) )
						{
							$consumer	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( $site , $config->get( 'main_autopost_' . $site . '_id') , $config->get( 'main_autopost_' . $site . '_secret') , $callback );
							$consumer->setAccess( $oauth->access_token );

							$consumer->share( $post );

							// @rule: Store this as sent
							$oauthPost->set( 'post_id' , $post->id );
							$oauthPost->set( 'oauth_id', $oauth->id );

							$oauthPost->store();
						}
					}
				}

				// @rule: Detect if any names are being mentioned in the post
				$names 			= DiscussHelper::getHelper( 'String' )->detectNames( $post->content );

				if( $names )
				{
					foreach( $names as $name )
					{
						$name			= JString::str_ireplace( '@' , '' , $name );
						$id 			= DiscussHelper::getUserId( $name );

						if( !$id || $id == $post->get( 'user_id') )
						{
							continue;
						}

						$notification	= DiscussHelper::getTable( 'Notifications' );

						$notification->bind( array(
								'title'		=> JText::sprintf( 'COM_EASYDISCUSS_MENTIONED_QUESTION_NOTIFICATION_TITLE' , $post->get( 'title' ) ),
								'cid'		=> $post->get( 'id' ),
								'type'		=> DISCUSS_NOTIFICATIONS_MENTIONED,
								'target'	=> $id,
								'author'	=> $post->get( 'user_id' ),
								'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
							) );
						$notification->store();
					}
				}
			}

			if( ( $isNew || $prevPostStatus == DISCUSS_ID_PENDING ) && $post->published == DISCUSS_ID_PUBLISHED )
			{
				if( $config->get( 'integration_pingomatic' ) )
				{
					$pingo        = DiscussHelper::getHelper( 'Pingomatic' );
					$pingo->ping( $post->title, DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id, true, true ) );
				}
			}

			$notify	= DiscussHelper::getNotification();


			// prepare email content and information.
			$profile 					= DiscussHelper::getTable( 'Profile' );
			$profile->load( $user->id );

			// badwords filtering for email data.
			$post->title 		= DiscussHelper::wordFilter( $post->title);
			$post->content 		= DiscussHelper::wordFilter( $post->content);

			// For use within the emails.
			$emailData					= array();
			$emailData['postTitle']		= $post->title;
			$emailData['postAuthor']	= $user->name;
			$emailData['postAuthorAvatar' ] = $profile->getAvatar();
			$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);
			$emailData['postContent' ]	= Parser::bbcode( $post->content );

			if($acl->allowed('add_tag', '0'))
			{
				//@task: Save tags
				$postTagModel	= $this->getModel( 'PostsTags' );
				$tags			= JRequest::getVar( 'tags' , '' , 'POST' );

				if( !empty( $tags ) )
				{
					$tagModel	= $this->getModel( 'Tags' );

					foreach ( $tags as $tag )
					{
						if ( !empty( $tag ) )
						{
							$tagTable	= DiscussHelper::getTable( 'Tags' );

							//@task: Only add tags if it doesn't exist.
							if( !$tagTable->exists( $tag ) )
							{
								$tagTable->set( 'title' 	, JString::trim( $tag ) );
								$tagTable->set( 'alias' 	, DiscussHelper::getAlias( $tag, 'tag' ) );
								$tagTable->set( 'created'	, $date->toMySQL() );
								$tagTable->set( 'published' , 1 );
								$tagTable->set( 'user_id'	, $user->id );

								$tagTable->store();
							}
							else
							{
								$tagTable->load( $tag , true );
							}

							$postTagInfo = array();

							//@task: Store in the post tag
							$postTagTable	= DiscussHelper::getTable( 'PostsTags' );
							$postTagInfo['post_id']	= $post->id;
							$postTagInfo['tag_id']	= $tagTable->id;

							$postTagTable->bind( $postTagInfo );
							$postTagTable->store();

							//send notification to all tag subscribers
							if($config->get('main_sitesubscription') && !in_array($postTagTable->id, $previousTags) && $post->published == DISCUSS_ID_PUBLISHED)
							{
								$modelSubscribe		= $this->getModel( 'Subscribe' );
								$subscribers        = $modelSubscribe->getTagSubscribers($tagTable->id);

								$emails = array();

								if(! empty($subscribers))
								{
									$notify 					= DiscussHelper::getNotification();
									$emailData['tagTitle']		= $tagTable->title;

									foreach($subscribers as $subscriber)
									{
										$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscriber, true, true);
										$notify->addQueue($subscriber->email, JText::sprintf('COM_EASYDISCUSS_SUBSCRIBE_SUBJECT_TAG', $tagTable->title), '', 'email.subscription.tag.new.php', $emailData);
									}
								}
							}
						}
					}
				}
			}

			$emailTemplate  = ( !$isModerate ) ? 'email.subscription.site.new.php' : 'email.subscription.site.moderate.php';
			$emailSubject  	= ( !$isModerate ) ? JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_ASKED', $post->id , $post->title) : JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_MODERATE', $post->id , $post->title);

			//get all admin emails
			$adminEmails = array();
			if($config->get( 'notify_admin' ) && ( $isNew || $prevPostStatus == DISCUSS_ID_PENDING ) )
			{
				$admins = $notify->getAdminEmails();

				if(! empty($admins))
				{
					foreach($admins as $admin)
					{
						$adminEmails[]   = $admin->email;
					}
				}
			}

			//get all site's subscribers email that want to receive notification immediately
			$subscriberEmails	= array();
			$subscribers		= array();

			if($config->get('main_sitesubscription') && ( $isNew || $prevPostStatus == DISCUSS_ID_PENDING ) && $post->published == DISCUSS_ID_PUBLISHED)
			{
				$modelSubscribe		= $this->getModel( 'Subscribe' );
				$subscribers        = $modelSubscribe->getSiteSubscribers('instant');

				if(! empty($subscribers))
				{
					foreach($subscribers as $subscriber)
					{
						$subscriberEmails[]   = $subscriber->email;
					}
				}
			}

			if(!empty($adminEmails) || !empty($subscriberEmails))
			{
				$emails 		= array_unique(array_merge($adminEmails, $subscriberEmails));
				$customEmails	= $config->get( 'notify_custom');

				if( !empty( $customEmails ) )
				{
					$customEmails 	= explode( ',' , $config->get( 'notify_custom') );
					$emails 		= array_merge( $emails ,$customEmails );

					// Ensure there are no duplicate emails.
					$emails 		= array_unique( $emails );
				}

				if( $isModerate )
				{
					// Generate hashkeys to map this current request
					$hashkey		= DiscussHelper::getTable( 'HashKeys' );
					$hashkey->uid	= $post->id;
					$hashkey->type	= DISCUSS_QUESTION_TYPE;
					$hashkey->store();

					require_once( DISCUSS_HELPERS . DS . 'router.php' );
					$approveURL		= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key );
					$rejectURL 		= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key );
					$emailData[ 'moderation' ]	= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_POST' ) . '</a>';
					$emailData[ 'moderation' ] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . '<a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';
				}

				//insert into mailqueue
				foreach ($emails as $email)
				{
					if ( in_array($email, $subscriberEmails) )
					{
						// these are subscribers
						if (!empty($subscribers))
						{
							foreach ($subscribers as $key => $value)
							{
								if ($value->email == $email)
								{
									$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscribers[$key], true, true);
									$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
								}
							}
						}
					}
					else
					{
						// non-subscribers will not get the unsubscribe link
						$notify->addQueue($emails, $emailSubject, '', $emailTemplate, $emailData);
					}
				}
			}


			// @rule: Jomsocial activity integrations & points & ranking
			if( ( $isNew || $prevPostStatus == DISCUSS_ID_PENDING ) && $post->published == DISCUSS_ID_PUBLISHED )
			{
				DiscussHelper::getHelper( 'jomsocial' )->addActivityQuestion( $post );

				// Add logging for user.
				DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.new.discussion' , $user->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_POST' , $post->title ) );

				DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.discussion' , $user->id );
				DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.discussion' , $user->id );

				// assign new ranks.
				DiscussHelper::getHelper( 'ranks' )->assignRank( $user->id );

				// aup
				DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_DISCUSSION , $user->id , $post->title );
			}

			$successmsg = ($isNew)? JText::_( 'COM_EASYDISCUSS_POST_STORED' ) : JText::_( 'COM_EASYDISCUSS_EDIT_SUCCESS' );

			if( $post->isPending() )
			{
				DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_NOTICE_POST_SUBMITTED_UNDER_MODERATION' ) , 'alert' );
			}
			else
			{
				DiscussHelper::setMessageQueue( $successmsg , 'info');
			}

			$this->setRedirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $lastId , false ) );
		}
	}

	function _fieldValidate($post)
	{
		$mainframe	= JFactory::getApplication();
		$valid		= true;
		$user		= JFactory::getUser();

		$message    = '<ul class="reset-ul">';

		if(JString::strlen($post['title']) == 0 || $post['title'] == JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE'))
		{
			$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY') . '</li>';
			$valid	= false;
		}

		if(JString::strlen($post['dc_reply_content']) == 0)
		{
			$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY') . '</li>';
			$valid	= false;
		}

		if(empty($post['category_id']))
		{
			$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_CATEGORY_IS_EMPTY') . '</li>';
			$valid	= false;
		}

		if(empty($user->id))
		{
			if(empty($post['poster_name']))
			{
				$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_NAME_IS_EMPTY') . '</li>';
				$valid	= false;
			}

			if(empty($post['poster_email']))
			{
				$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMAIL_IS_EMPTY') . '</li>';
				$valid	= false;
			}
			else
			{
				require_once( DISCUSS_HELPERS . DS . 'email.php' );

				if(!DiscussEmailHelper::isValidInetAddress($post['poster_email']))
				{
					$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMAIL_IS_INVALID') . '</li>';
					$valid	= false;
				}
			}
		}

		$message    .= '</ul>';

		DiscussHelper::setMessageQueue( $message , 'alert');

		return $valid;
	}
}
