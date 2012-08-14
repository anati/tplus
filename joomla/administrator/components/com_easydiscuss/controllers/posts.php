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

class EasyDiscussControllerPosts extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'unfeature' , 'toggleFeatured' );
		$this->registerTask( 'feature' , 'toggleFeatured' );
	}

	function toggleFeatured()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();
		$records	= JRequest::getVar( 'cid' , '' );
		$message	= '';
		$task		= JRequest::getVar( 'task' );

		foreach( $records as $record )
		{
			$post       = JTable::getInstance( 'Posts' , 'Discuss' );
			$post->load( $record );

			$post->featured	= $task == 'feature';

			$post->store();
		}

		$message    = JText::_( 'COM_EASYDISCUSS_DISCUSSIONS_FEATURED' );

		if( $task == 'unfeature' )
		{
			$message    = JText::_( 'COM_EASYDISCUSS_DISCUSSIONS_UNFEATURED' );
		}

		$mainframe->enqueueMessage( $message , 'message' );
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=posts' );
		$mainframe->close();
	}

	function publish()
	{
		$config = DiscussHelper::getConfig();
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$pid	= JRequest::getString( 'pid' , '' , 'POST' );


		$message	= '';
		$type		= 'message';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			//send notification:
			//so we are publising posts.
			foreach( $post as $postId)
			{
				$item	= JTable::getInstance( 'posts', 'Discuss' );
				$item->load( $postId );

				if( $item->published == DISCUSS_ID_PENDING)
				{
					//now we know is from pending to publish. we need to send out notification.
					DiscussHelper::sendNotification( $item , $item->parent_id, true, $item->user_id, $item->published);

					$susbcribeModel	= JModel::getInstance( 'Subscribe' , 'EasyDiscussModel' );

					// auto subscription
					if( $config->get('main_autopostsubscription') && $config->get('main_postsubscription') && $item->user_type != 'twitter' && !empty($item->parent_id))
					{
						// process only if this is a reply
						//automatically subscribe this user into this reply
						$replier    = JFactory::getUser($item->user_id);


						$subscription_info = array();
						$subscription_info['type'] 		= 'post';
						$subscription_info['userid'] 	= ( !empty($item->user_id) ) ? $item->user_id : '0';
						$subscription_info['email'] 	= ( !empty($item->user_id) ) ? $replier->email : $item->poster_email;;
						$subscription_info['cid'] 		= $item->parent_id;
						$subscription_info['member'] 	= ( !empty($item->user_id) ) ? '1':'0';
						$subscription_info['name'] 		= ( !empty($item->user_id) ) ? $replier->name : $item->poster_name;
						$subscription_info['interval'] 	= 'instant';


						$sid    = '';
						if( $subscription_info['userid'] == 0)
						{
							$sid = $susbcribeModel->isPostSubscribedEmail($subscription_info);
							if( empty( $sid ) )
							{
								$susbcribeModel->addSubscription($subscription_info);
							}
						}
						else
						{
							$sid = $susbcribeModel->isPostSubscribedUser($subscription_info);
							if( empty( $sid['id'] ))
							{
								//add new subscription.
								$susbcribeModel->addSubscription($subscription_info);
							}
						}
					}

					// send notification to post subscriber.
					if( $config->get('notify_subscriber') )
					{
						$subscribers        = $susbcribeModel->getPostSubscribers( $item->id );

						$author             = JFactory::getUser( $item->user_id );

						$emailData['postTitle']		= $item->title;
						$emailData['comment']		= $item->content;
						$emailData['postAuthor']	= $author->name;
						$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $item->id, false, true);

						$emails = array();
						if(! empty($subscribers))
						{
						    foreach($subscribers as $subscriber)
						    {
						    	if( empty($user->id) || $user->id != $subscriber->userid)
						    	{
									$emails[]   = $subscriber->email;
								}
						    }
						    $notify	= DiscussHelper::getNotification();
							$notify->addQueue($emails, JText::sprintf('COM_EASYDISCUSS_NEW_POST_ADDED', $item->title), '', 'email.subscription.site.new.php', $emailData);
						}
					}

					// only if the post is a discussion
					if( $config->get( 'integration_pingomatic' ) && empty( $item->parent_id ) )
					{
						$pingo        = DiscussHelper::getHelper( 'Pingomatic' );
						$pingo->ping( $item->title, DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $item->id, true, true ) );
					}

				}
			}

			$model		= $this->getModel( 'Posts' );

			if( $model->publish( $post , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POSTS_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_PUBLISHING');
				$type		= 'error';
			}

		}

		$pidLink    = '';
		if(! empty($pid))
			$pidLink    = '&pid=' . $pid;

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' . $pidLink , $message , $type );
	}

	function unpublish()
	{
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$pid	= JRequest::getString( 'pid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Posts' );

			if( $model->publish( $post , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_POSTS_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_ERROR_UNPUBLISHING');
				$type		= 'error';
			}

		}

		$pidLink    = '';
		if(! empty($pid))
			$pidLink    = '&pid=' . $pid;

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' . $pidLink , $message , $type );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'post' );
		JRequest::setVar( 'id' , JRequest::getVar( 'id' , '' , 'REQUEST' ) );
		JRequest::setVar( 'pid' , JRequest::getVar( 'pid' , '' , 'REQUEST' ) );
		JRequest::setVar( 'source' , 'posts' );

		parent::display();
	}

	function remove()
	{
		$post	= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$pid	= JRequest::getString( 'pid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $post ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Posts' );

			//check if any of the 'to be' remove entry was a answered reply.
			// If yes, revert the main post to unresolved.
			if( ! empty( $pid ) )
			{
				// we knwo this is the replies.
				$model->revertAnwered( $post );
			}

			if( $model->delete( $post ) )
			{
				$message	= ( empty( $pid ) ) ? JText::_('COM_EASYDISCUSS_POSTS_DELETED') : JText::_('COM_EASYDISCUSS_REPLIES_DELETED');

				// @rule: Trigger AUP points
				if( !empty( $pid ) )
				{
					DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_DISCUSSION , $post->user_id , $post->title );
				}
			}
			else
			{
				$message	= ( empty( $pid ) ) ? JText::_('COM_EASYDISCUSS_ERROR_DELETING_POST') : JText::_('COM_EASYDISCUSS_ERROR_DELETING_REPLY');
				$type		= 'error';
			}

		}

		$pidLink    = '';
		if(! empty($pid))
			$pidLink    = '&pid=' . $pid;

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=posts' . $pidLink , $message , $type );
	}

	function add()
	{
		$mainframe	= JFactory::getApplication();

		$mainframe->redirect( 'index.php?option=com_easydiscuss&controller=posts&task=edit' );
	}

	function cancelSubmit()
	{
		$source = JRequest::getVar('source', 'posts');
		$pid	= JRequest::getString( 'parent_id' , '' , 'POST' );

		$pidLink    = '';
		if(! empty($pid))
			$pidLink    = '&pid=' . $pid;

		$this->setRedirect( JRoute::_('index.php?option=com_easydiscuss&view=' . $source . $pidLink, false), '');
	}

	/**
	 * update posts
	 */
	function submit()
	{
		if( JRequest::getMethod() == 'POST' )
		{
			$user = JFactory::getUser();

			// get all forms value
			$post	= JRequest::get( 'post' );

			// get id if available
			$id		= JRequest::getInt('id', 0);

			// get post parent id
			$parent = JRequest::getInt('parent_id', 0);

			// the source where page come from
			$source = JRequest::getVar('source', 'posts');

			// get config
			$config = DiscussHelper::getConfig();

			$post['alias'] 	= (empty($post['alias']))? DiscussHelper::getAlias( $post['title'], 'post', $id) : DiscussHelper::getAlias( $post['alias'], 'post', $id );

			//clear tags if editing a post.
			$previousTags = array();
			if(!empty($id))
			{
				$postsTagsModel = $this->getModel('PostsTags');

				$tmppreviousTags = $postsTagsModel->getPostTags( $id );
				if(!empty($tmppreviousTags))
				{
					foreach($tmppreviousTags as $previoustag)
					{
						$previousTags[] = $previoustag->id;
					}
				}

				$postsTagsModel->deletePostTag( $id );
			}

			// bind the table
			$postTable		= JTable::getInstance( 'posts', 'Discuss' );
			$postTable->load( $id );

			//get previous post status before binding.
			$prevPostStatus = $postTable->published;

			$postTable->bind( $post , true );

			// hold last inserted ID in DB
			$lastId = null;

			// @trigger: onBeforeSave
			$isNew	= (bool) $postTable->id;
			DiscussEventsHelper::importPlugin( 'content' );
			DiscussEventsHelper::onContentBeforeSave('post', $post, $isNew);

			if ( !$postTable->store() )
			{
				JError::raiseError(500, $postTable->getError() );
			}

			// @trigger: onAfterSave
			DiscussEventsHelper::onContentAfterSave('post', $post, $isNew);

			$lastId		= $postTable->id;

			// Bind file attachments
			$postTable->bindAttachments();

			$message	= JText::_( 'COM_EASYDISCUSS_POST_SAVED' );

			if($config->get( 'notify_owner' ) && empty($id))
			{
				if($post['self_subscribe'])
				{
					$subscription_info = array();
					$subscription_info['type'] = 'post';
					$subscription_info['userid'] = $user->id;
					$subscription_info['email'] = $user->email;
					$subscription_info['cid'] = $postTable->id;
					$subscription_info['member'] = '1';
					$subscription_info['name'] = $user->name;
					$subscription_info['interval'] = 'instant';

					$subscribeModel	= $this->getModel( 'Subscribe' );
					$subscribeModel->addSubscription($subscription_info);
				}
			}

			$date = JFactory::getDate();

			//@task: Save tags
			$tags			= JRequest::getVar( 'tags' , '' , 'POST' );

			if( !empty( $tags ) )
			{
				$tagModel	= $this->getModel( 'Tags' );

				foreach ( $tags as $tag )
				{
					if ( !empty( $tag ) )
					{
						$tagTable	= JTable::getInstance( 'Tags' , 'Discuss' );

						//@task: Only add tags if it doesn't exist.
						if( !$tagTable->exists( $tag ) )
						{
							$tagInfo['title'] 		= JString::trim( $tag );
							$tagInfo['alias'] 		= DiscussHelper::getAlias( $tag, 'tag' );
							$tagInfo['created']		= $date->toMySQL();
							$tagInfo['published']	= 1;
							$tagInfo['user_id']		= $user->id;

							$tagTable->bind($tagInfo);
							$tagTable->store();

						}
						else
						{
							$tagTable->load( $tag , true );
						}

						$postTagInfo = array();

						//@task: Store in the post tag
						$postTagTable	= JTable::getInstance( 'PostsTags' , 'Discuss' );
						$postTagInfo['post_id']	= $postTable->id;
						$postTagInfo['tag_id']	= $tagTable->id;

						$postTagTable->bind( $postTagInfo );
						$postTagTable->store();

						//send notification to all tag subscribers
						if($config->get('main_sitesubscription') && !in_array($tagTable->id, $previousTags) && $postTable->published == DISCUSS_ID_PUBLISHED)
						{
							$modelSubscribe		= $this->getModel( 'Subscribe' );
							$subscribers        = $modelSubscribe->getTagSubscribers($tagTable->id);

							$emails = array();
							if(! empty($subscribers))
							{
								$notify	= DiscussHelper::getNotification();

								$emailData['postTitle']		= $postTable->title;
								$emailData['tagTitle']		= $tagTable->title;
								$emailData['postAuthor']	= $user->name;
								$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $postTable->id, false, true);


								foreach($subscribers as $subscriber)
								{
									$emails[]   = $subscriber->email;
								}
								$notify->addQueue($emails, JText::sprintf('COM_EASYDISCUSS_SUBSCRIBE_SUBJECT_TAG', $tagTable->title), '', 'email.subscription.tag.new.php', $emailData);
							}
						}

					}
				}
			}

			$isNew  = ( empty($id) ) ? true : false;
			if( ( $isNew || $prevPostStatus == DISCUSS_ID_PENDING ) && $postTable->published == DISCUSS_ID_PUBLISHED )
			{
				$owner = ( $isNew ) ? $user->id : $postTable->user_id;
				DiscussHelper::sendNotification( $postTable , $parent, $isNew, $owner, $prevPostStatus);

				// auto subscription
				if( $config->get('main_autopostsubscription') && $config->get('main_postsubscription') && $postTable->user_type != 'twitter' && !empty($postTable->parent_id))
				{
					// process only if this is a reply
					//automatically subscribe this user into this reply
					$replier    = JFactory::getUser($postTable->user_id);

					$subscription_info = array();
					$subscription_info['type'] 		= 'post';
					$subscription_info['userid'] 	= ( !empty($postTable->user_id) ) ? $postTable->user_id : '0';
					$subscription_info['email'] 	= ( !empty($postTable->user_id) ) ? $replier->email : $postTable->poster_email;;
					$subscription_info['cid'] 		= $postTable->parent_id;
					$subscription_info['member'] 	= ( !empty($postTable->user_id) ) ? '1':'0';
					$subscription_info['name'] 		= ( !empty($postTable->user_id) ) ? $replier->name : $postTable->poster_name;
					$subscription_info['interval'] 	= 'instant';


					//get frontend subscribe table
					$susbcribeModel	= JModel::getInstance( 'Subscribe' , 'EasyDiscussModel' );
					$sid    = '';
					if( $subscription_info['userid'] == 0)
					{
						$sid = $susbcribeModel->isPostSubscribedEmail($subscription_info);
						if( empty( $sid ) )
						{
							$susbcribeModel->addSubscription($subscription_info);
						}
					}
					else
					{
						$sid = $susbcribeModel->isPostSubscribedUser($subscription_info);
						if( empty( $sid['id'] ))
						{
							//add new subscription.
							$susbcribeModel->addSubscription($subscription_info);
						}
					}
				}

				// only if the post is a discussion
				if( $config->get( 'integration_pingomatic' ) && empty( $postTable->parent_id ) )
				{
					$pingo      = DiscussHelper::getHelper( 'Pingomatic' );
					$urls   	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $postTable->id, true, true );
					$pingo->ping( $postTable->title,  $urls);
				}
			}

			$pid    = '';
			if(! empty($parent))
				$pid    = '&pid=' . $parent;

			$this->setRedirect( JRoute::_('index.php?option=com_easydiscuss&view=' . $source . $pid, false), JText::_('COM_EASYDISCUSS_POST_SAVED') );
		}
	}
}
