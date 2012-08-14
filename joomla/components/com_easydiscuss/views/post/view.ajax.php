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

require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'input.php' );
require_once( DISCUSS_CLASSES . DS . 'themes.php' );
require_once( DISCUSS_HELPERS . DS . 'parser.php' );
require_once( DISCUSS_HELPERS . DS . 'string.php' );
require_once( DISCUSS_HELPERS . DS . 'filter.php' );
require_once( DISCUSS_HELPERS . DS . 'integrate.php' );
require_once( DISCUSS_HELPERS . DS . 'user.php');
require_once( DISCUSS_HELPERS . DS . 'router.php' );

class EasyDiscussViewPost extends EasyDiscussView
{
	var $err	= null;

	function similarQuestion()
	{
	    $ajax	= DiscussHelper::getHelper( 'Ajax' );
		$config 	= DiscussHelper::getConfig();

		// if enabled
		$html   = '';
		if( true )
		{
		    $query  = JRequest::getString( 'query' );
		    $posts  = DiscussHelper::getSimilarQuestion( $query );

		    if( count( $posts ) > 0 )
		    {
				foreach( $posts as &$post)
				{
				    $post->title    = DiscussHelper::wordFilter($post->title);
				}

                $theme		= new DiscussThemes();
                $theme->set( 'posts' , $posts );
                $html	= $theme->fetch( 'ajax.similar.question.list.php' );
			}

		}

	    //$ajax->Foundry('#div').html( 'value' );
	    $ajax->success( $html );
	}

	function checklogin()
	{
		$user	= JFactory::getUser();
		$ajax	= new Disjax();

		if(empty($user->id))
		{
			$config 	= DiscussHelper::getConfig();
			$tpl		= new DiscussThemes();
			$session	= JFactory::getSession();

			$defaultUserType = $config->get('main_allowguestpost')? 'guest' : 'member';
			$return		= DiscussRouter::_('index.php?option=com_easydiscuss&view=ask', false);
			$token		= JUtility::getToken();

			$guest = new stdClass();
			if($session->has( 'guest_reply_authentication', 'discuss' ))
			{
				$session_request	= JString::str_ireplace(',', "\r\n", $session->get('guest_reply_authentication', '', 'discuss'));
				$guest_session		= new JParameter( $session_request );

				$guest->email	= $guest_session->get('email', '');
				$guest->name	= $guest_session->get('name', '');
			}

			$twitter 	= '';
			if($config->get('integration_twitter_consumer_secret_key'))
			{
				require_once(DISCUSS_HELPERS . DS . 'twitter.php');
				$twitter = DiscussTwitterHelper::getAuthentication();
			}

			$tpl->set( 'return'		, base64_encode($return) );
			$tpl->set( 'config'		, $config );
			$tpl->set( 'token'		, $token );
			$tpl->set( 'guest'		, $guest );
			$tpl->set( 'twitter'	, $twitter );

			$html = $tpl->fetch( 'login.php' );
			$ajax->script( 'discuss.login.token = "'.$token.'";');

			$options = new stdClass();
			$options->content = $html;

			$ajax->dialog( $options );

			$ajax->script( 'discuss.login.showpane(\''.$defaultUserType.'\');');
		}
		else
		{
			$ajax->script( "Foundry( '#user_type' ).val( 'member' );" );
			$ajax->script( "discuss.reply.post();" );
		}
		$ajax->script( 'discuss.spinner.hide("reply_loading");');
		$ajax->send();
	}

	function deletePostForm( $id = null , $type = null , $url = null )
	{
		$ajax		= new Disjax();
		$theme		= new DiscussThemes();
		$theme->set( 'id'	, $id );
		$theme->set( 'type' , $type );
		$theme->set( 'url'	, base64_encode( $url ) );
		$content	= $theme->fetch( 'ajax.delete.php' );

		$options	= new stdClass();
		$options->content = $content;

		$ajax->dialog( $options );
		$ajax->send();
	}



	function ajaxRefreshTwitter()
	{
		require_once(DISCUSS_HELPERS.DS.'twitter.php');

		$disjax	= new Disjax();

		$header	= '<h1>'.JText::_('COM_EASYDISCUSS_TWITTER').'</h1>';
		$html	= trim(DiscussTwitterHelper::getAuthentication());

		$disjax->script('Foundry(\'#usertype_twitter_pane\').html(\''.$header.$html.'\');');

		$disjax->send();
	}

	function ajaxSignOutTwitter()
	{
		require_once(DISCUSS_HELPERS.DS.'twitter.php');

		$disjax	= new Disjax();
		$session = JFactory::getSession();

		if($session->has( 'twitter_oauth_access_token', 'discuss' ))
		{
			$session->clear( 'twitter_oauth_access_token', 'discuss' );
		}

		$header	= '<h1>'.JText::_('COM_EASYDISCUSS_TWITTER').'</h1>';
		$html	= trim(DiscussTwitterHelper::getAuthentication());

		$disjax->script('Foundry(\'#usertype_twitter_pane\').html(\''.$header.addslashes($html).'\');');

		$disjax->send();
	}

	function ajaxGuestReply($email = null, $name = null)
	{
		require_once(DISCUSS_HELPERS . DS . 'email.php');

		$disjax	= new Disjax();

		if(empty($email))
		{
			$disjax->script("Foundry('#usertype_status .msg_in').html('".JText::_('COM_EASYDISCUSS_PLEASE_INSERT_YOUR_EMAIL_ADDRESS_TO_PROCEED')."');");
			$disjax->script("Foundry('#usertype_status .msg_in').addClass('dc_error');");
			$disjax->script("Foundry('#edialog-guest-reply').attr('disabled', '');");
			$disjax->send();
			return false;
		}

		if(DiscussEmailHelper::isValidInetAddress($email)==false)
		{
			$disjax->script('Foundry(\'#usertype_status .msg_in\').html(\''.JText::_('COM_EASYDISCUSS_INVALID_EMAIL_ADDRESS').'\');');
			$disjax->script('Foundry(\'#usertype_status .msg_in\').addClass(\'dc_error\');');

			$disjax->script('Foundry(\'#edialog-guest-reply\').attr(\'disabled\', \'\');');
		}
		else
		{
			$session = JFactory::getSession();

			if($session->has( 'guest_reply_authentication', 'discuss' ))
			{
				$session->clear( 'guest_reply_authentication', 'discuss' );
			}

			$name = ($name)? $name : $email;

			$session->set('guest_reply_authentication', "email=".$email.",name=".$name."", 'discuss');


			$disjax->script('Foundry(\'#user_type\').val(\'guest\');');
			$disjax->script('Foundry(\'#poster_name\').val(Foundry(\'#discuss_usertype_guest_name\').val());');
			$disjax->script('Foundry(\'#poster_email\').val(Foundry(\'#discuss_usertype_guest_email\').val());');
			$disjax->script('disjax.closedlg();');
			$disjax->script( 'discuss.reply.submit();' );
		}

		$disjax->send();
	}

	function ajaxMemberReply($username = null, $password = null, $token = null)
	{
		$disjax		= new Disjax();
		$mainframe	= JFactory::getApplication();

		JRequest::setVar( $token, 1 );

		if(empty($username) || empty($password))
		{
			$disjax->script("Foundry('#usertype_status .msg_in').html('".JText::_('COM_EASYDISCUSS_PLEASE_INSERT_YOUR_USERNAME_AND_PASSWORD')."');");
			$disjax->script("Foundry('#usertype_status .msg_in').addClass('dc_error');");
			$disjax->script("Foundry('#edialog-member-reply').attr('disabled', '');");
			$disjax->send();
			return false;
		}

		// Check for request forgeries
		if(JRequest::checkToken('request'))
		{
			$credentials = array();

			$credentials['username'] = $username;
			$credentials['password'] = $password;

			$result = $mainframe->login($credentials);

			if (!JError::isError($result))
			{
				$token = JUtility::getToken();
				$disjax->script( 'Foundry(".easydiscuss-token").val("' . $token . '");');
				$disjax->script('disjax.closedlg();');
				$disjax->script( 'discuss.reply.submit();' );
			}
			else
			{
				$error = JError::getError();

				$disjax->script('Foundry(\'#usertype_status .msg_in\').html(\''.$error->message.'\');');
				$disjax->script('Foundry(\'#usertype_status .msg_in\').addClass(\'dc_error\');');
				$disjax->script('Foundry(\'#edialog-member-reply\').attr(\'disabled\', \'\');');
			}
		}
		else
		{
			$token = JUtility::getToken();
			$disjax->script( 'discuss.login.token = "'.$token.'";' );

			$disjax->script('Foundry(\'#usertype_status .msg_in\').html(\''.JText::_('COM_EASYDISCUSS_MEMBER_LOGIN_INVALID_TOKEN').'\');');
			$disjax->script('Foundry(\'#usertype_status .msg_in\').addClass(\'dc_error\');');

			$disjax->script( 'Foundry(\'#edialog-reply\').attr(\'disabled\', \'\');' );
		}

		$disjax->send();
	}

	function ajaxTwitterReply()
	{
		$disjax	= new Disjax();

		$twitterUserId				= '';
		$twitterScreenName			= '';
		$twitterOauthToken			= '';
		$twitterOauthTokenSecret	= '';

		$session = JFactory::getSession();

		if($session->has( 'twitter_oauth_access_token', 'discuss' ))
		{
			$session_request	= JString::str_ireplace(',', "\r\n", $session->get('twitter_oauth_access_token', '', 'discuss'));
			$access_token		= new JParameter( $session_request );

			$twitterUserId				= $access_token->get('user_id', '');
			$twitterScreenName			= $access_token->get('screen_name', '');
			$twitterOauthToken			= $access_token->get('oauth_token', '');
			$twitterOauthTokenSecret	= $access_token->get('oauth_token_secret', '');
		}

		if(empty($twitterUserId) || empty($twitterOauthToken) || empty($twitterOauthTokenSecret))
		{
			$disjax->script('Foundry(\'#usertype_status .msg_in\').html(\''.JText::_('COM_EASYDISCUSS_TWITTER_REQUIRES_AUTHENTICATION').'\');');
			$disjax->script('Foundry(\'#usertype_status .msg_in\').addClass(\'dc_error\');');
			$disjax->script('Foundry(\'#edialog-twitter-reply\').attr(\'disabled\', \'\');');
		}
		else
		{
			$screen_name = $twitterScreenName? $twitterScreenName : $twitterUserId;
			$disjax->script('Foundry(\'#user_type\').val(\'twitter\');');
			$disjax->script('Foundry(\'#poster_name\').val(\''.$screen_name.'\');');
			$disjax->script('Foundry(\'#poster_email\').val(\''.$twitterUserId.'\');');
			$disjax->script('disjax.closedlg();');
			$disjax->script( 'discuss.reply.submit();' );
		}

		$disjax->send();
	}

	/**
	 * Ajax Call
	 * Submit user vote
	 */
	function ajaxAddVote( $postId = null, $numVal = null )
	{
		$ajax   = new Disjax();

		$config = DiscussHelper::getConfig();
		$my 	= JFactory::getUser();
		$date	= JFactory::getDate();

		if(!$config->get( 'main_allowvote'))
		{
			// Since someone tries to hack the system, we just ignore this.
			// We do not need to display friendly messages to users that try
			// to hack the system.
			return;
		}

		$postTable 			= DiscussHelper::getTable( 'Post' );
		$postTable->load( $postId );

		if(empty($my->id))
		{
			$options = new stdClass();
			$options->content = DiscussHelper::getLoginHTML( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $postTable->parent_id , false ) );

			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		if ( !$config->get( 'main_allowselfvote') && ($my->id == $postTable->user_id) )
		{
			$options = new stdClass();
			$options->content = '<div>'.JText::_('COM_EASYDISCUSS_SELF_VOTE_DENIED').'</div>';

			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		$post   = array();

		$post['post_id']	= $postId;
		$post['value']		= (empty($numVal)) ? '0' : $numVal;
		$post['user_id']	= $my->id;
		$post['created']	= $date->toMySQL();

		// load model
		$voteModel	= $this->getModel('votes');
		$voted		= $voteModel->checkUserVote( $post['post_id'] );

		$vote		= DiscussHelper::getTable( 'Votes' );
		$vote->bind( $post );

		$result = array();

		if( $voted )
		{

			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_YOU_ALRREADY_VOTED_FOR_THIS_POST');
			$ajax->dialog( $options );
			$ajax->send();
			return;
		}
		else
		{
			if ( !$vote->store() )
			{
				$options = new stdClass();
				$options->content = $vote->getError();
				$ajax->dialog( $options );
				$ajax->send();
				return;
			}
			else
			{
				// sum up the post's vote
				$vote->sumPostVote($postId, $numVal);

				// @rule: Badges
				$question	= DiscussHelper::getTable( 'Post' );
				$question->load( $postTable->parent_id );

				// @task: Only give badge when the user vote on another user's content.
				if( $my->id != $postTable->user_id )
				{
					// Add logging for user.
					DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.vote.reply' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_REPLY' , $question->title ) );

					DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.vote.reply' , $my->id );
					DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.vote.reply' , $my->id );
				}

				$result['status']	= 'success';
				$result['title']	= JText::_('COM_EASYDISCUSS_SUCCESS_SUBMIT_VOTE');
				$result['message']	= JText::_('COM_EASYDISCUSS_VOTE_SUBMITTED');

				$totalVote = $voteModel->sumPostVotes( $post['post_id'] );

				$voteType			= $numVal == -1 ? 'votedown' : 'voteup';

				if( $postTable->getType() == DISCUSS_QUESTION_TYPE )
				{
					// Update front end vote number for main question.
					$ajax->assign( 'vote_total_' . $post[ 'post_id' ] . ' b' , $totalVote );
				}
				else
				{
					// Update front end vote number for replies.
					$ajax->assign( 'vote_total_' . $post[ 'post_id' ] . ' span' , $totalVote );
				}

				$ajax->script( 'Foundry("#' . $voteType .'_' . $post[ 'post_id' ] . '").addClass( "voted" );' );

				$totalvotecount	= $voteModel->getTotalVoteCount( $postId );
				if(!empty($totalvotecount))
				{
					$voters	= DiscussHelper::getVoters($postId);

					$noun = ($totalvotecount=='1')? 'SINGULAR' : 'PLURAL';
					$totalVoteHTML = JText::sprintf('COM_EASYDISCUSS_VOTES_'.$noun, $totalvotecount);

					$ajax->assign( 'dc_reply_total_votes_'.$postId , $totalVoteHTML );

					$voternameHTML = JText::sprintf('COM_EASYDISCUSS_VOTES_BY', $voters->voters);
					if($voters->shownVoterCount < $totalvotecount)
					{
						$voternameHTML .= '[<a href="javascript:void(0);" onclick="disjax.load(\'post\', \'getMoreVoters\', \''.$postId.'\', \''.$postTable->totalVote.'\', \'5\');">'.JText::_('COM_EASYDISCUSS_MORE').'</a>]';
					}

					$ajax->assign( 'dc_reply_voters_'.$postId , $voternameHTML );
				}
			}
		}

		$ajax->send();
		return;
	}


	/**
	 * Ajax Call
	 * Sum all votes
	 */
	function ajaxSumVote( $postId = null )
	{
		$djax   = new Disjax();

		// load model
		$voteModel = $this->getModel('votes');
		$total = $voteModel->sumPostVotes($postId);

		//$djax->script('console.log(\'' . $total . '\');');
		$djax->send();
		return;
	}

	/**
	 * Ajax Call
	 * Set as locked
	 */
	function ajaxLockPost( $postId = null )
	{
		$ajax   = new Disjax();

		if(empty($postId))
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		$isMine		= DiscussHelper::isMine($post->user_id);
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		//update isresolve flag
		$post->islock   = 1;

		if ( !$post->store() )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , $post->getError() );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		// @rule: Add notifications for the thread starter
		$my		= JFactory::getUser();
		if( $post->get( 'user_id') != $my->id )
		{
			$notification	= DiscussHelper::getTable( 'Notifications' );
			$notification->bind( array(
					'title'	=> JText::sprintf( 'COM_EASYDISCUSS_LOCKED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
					'cid'	=> $post->get( 'id' ),
					'type'	=> DISCUSS_NOTIFICATIONS_LOCKED,
					'target'	=> $post->get( 'user_id' ),
					'author'	=> $my->id,
					'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
				) );
			$notification->store();
		}

		$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_POST_LOCKED') );
		$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_success" );' );
		$ajax->script( 'Foundry( "#title_' . $postId . '").addClass("locked");' );
		$ajax->script( 'Foundry( "#post_lock_link").hide();' );
		$ajax->script( 'Foundry( "#post_unlock_link").show();' );
		$ajax->script( 'Foundry( "div.post_locked" ).show();' );
		$ajax->script( 'Foundry( "div#dc_main_reply_lock" ).show();' );
		$ajax->script( 'discuss.post.toggleTools( 0 , "' . $postId . '" , "' . $isAdmin . '" );' );
		$ajax->send();
		return;
	}

	/**
	 * Ajax Call
	 * Set as locked
	 */
	function ajaxUnlockPost( $postId = null )
	{
		$ajax   = new Disjax();

		if(empty($postId))
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$post 			= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		$isMine		= DiscussHelper::isMine($post->user_id);
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		//update isresolve flag
		$post->islock   = 0;

		if ( !$post->store() )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , $post->getError() );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		// @rule: Add notifications for the thread starter
		$my		= JFactory::getUser();
		if( $post->get( 'user_id') != $my->id )
		{
			$notification	= DiscussHelper::getTable( 'Notifications' );
			$notification->bind( array(
					'title'	=> JText::sprintf( 'COM_EASYDISCUSS_UNLOCKED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
					'cid'	=> $post->get( 'id' ),
					'type'	=> DISCUSS_NOTIFICATIONS_UNLOCKED,
					'target'	=> $post->get( 'user_id' ),
					'author'	=> $my->id,
					'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
				) );
			$notification->store();
		}

		$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_POST_UNLOCKED') );
		$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_success" );' );
		$ajax->script( 'Foundry( "#title_' . $postId . '").removeClass("locked");' );
		$ajax->script( 'Foundry( "#post_lock_link").show();' );
		$ajax->script( 'Foundry( "#post_unlock_link").hide();' );
		$ajax->script( 'Foundry( "div.post_locked" ).hide();' );
		$ajax->script( 'Foundry( "div#dc_main_reply_lock" ).hide();' );
		$ajax->script( 'discuss.post.toggleTools( 1 , "' . $postId . '" , "' . $isAdmin . '" );' );
		$ajax->send();
		return;
	}

	/**
	 * Ajax Call
	 * Set as resolve
	 */
	function resolve( $postId = null )
	{
		$ajax   = new Disjax();

		if(empty($postId))
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		$isMine		= DiscussHelper::isMine($post->user_id);
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$post->isresolve   = DISCUSS_ENTRY_RESOLVED;

		if ( !$post->store() )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , $post->getError() );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$my		= JFactory::getUser();

		// @rule: Badges only applicable when they resolve their own post.
		if( $post->get( 'user_id') == $my->id )
		{
			// Add logging for user.
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.resolved.discussion' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_RESOLVED_OWN_DISCUSSION' , $post->title ) );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.resolved.discussion' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.resolved.discussion' , $my->id );
		}

		// @rule: Add notifications for the thread starter
		$my		= JFactory::getUser();
		if( $post->get( 'user_id') != $my->id )
		{
			$notification	= DiscussHelper::getTable( 'Notifications' );
			$notification->bind( array(
					'title'	=> JText::sprintf( 'COM_EASYDISCUSS_RESOLVED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
					'cid'	=> $post->get( 'id' ),
					'type'	=> DISCUSS_NOTIFICATIONS_RESOLVED,
					'target'	=> $post->get( 'user_id' ),
					'author'	=> $my->id,
					'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
				) );
			$notification->store();
		}

		$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_ENTRY_RESOLVED') );
		$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_success" );' );
		$ajax->script('Foundry("#post_resolve_link").hide();');
		$ajax->script('Foundry("#post_unresolve_link").show();');
		$ajax->send();
		return;
	}


	/**
	 * Ajax Call
	 * Set as unresolve
	 */
	function unresolve( $postId = null )
	{
		$ajax   = new Disjax();

		if(empty($postId))
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
			$ajax->script( 'Foundry( "#reports-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$post	= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		$isMine		= DiscussHelper::isMine($post->user_id);
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		//update isresolve flag
		$post->isresolve   = DISCUSS_ENTRY_UNRESOLVED;


		if ( !$post->store() )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , $post->getError() );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		// now we clear off all the accepted answers.
		$post->clearAccpetedReply();

		$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_ENTRY_UNRESOLVED') );
		$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_success" );' );
		$ajax->script( 'Foundry( "#title_' . $postId . ' span.resolved" ).remove();' );
		$ajax->script('Foundry("#post_resolve_link").show();');
		$ajax->script('Foundry("#post_unresolve_link").hide();');

		$ajax->send();
		return;
	}


	/**
	 * Ajax Call
	 * Get raw content from db
	 */
	function ajaxGetRawContent( $postId = null )
	{
		$djax   = new Disjax();

		if(! empty($postId))
		{
			$postTable 			= DiscussHelper::getTable( 'Post' );
			$postTable->load( $postId );

			$djax->value('reply_content_' . $postId, $postTable->content);
		}

		$djax->send();
		return;
	}



	/**
	 * Ajax Call
	 * Save new raw content to db
	 */
	function ajaxSaveContent()
	{
		$config 	= DiscussHelper::getConfig();
		$djax		= new Disjax();
		$post		= JRequest::get( 'POST' );

		$output     	= array();
		$output['id']   = $post[ 'post_id' ];
		$acl        = DiscussHelper::getHelper( 'ACL' );
		$my         = JFactory::getUser();


		// do checking here!
		if( empty( $post[ 'content' ] ) )
		{

			// Append result
			$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY');
			$output[ 'type' ]       = 'error';

			echo $this->outputJson( $output );
			return false;
		}

		// Rebind the post data
		$post[ 'content' ] = JRequest::getVar( 'content', '', 'post', 'none' , JREQUEST_ALLOWRAW );

		$postTable 			= DiscussHelper::getTable( 'Post' );
		$postTable->load( $post[ 'post_id' ] );
		$postTable->bind( $post );

		if( $postTable->id )
		{
			// Do not allow unauthorized access
			if( !$acl->allowed('edit_reply', '0') && !DiscussHelper::isSiteAdmin() && $postTable->user_id != $my->id )
			{
				// Append result
				$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS');
				$output[ 'type' ]       = 'error';

				echo $this->outputJson( $output );
				return false;
			}
		}

		// set new content
		$postTable->content	= $post['content'];

		$recaptcha	= $config->get( 'antispam_recaptcha');
		$public		= $config->get( 'antispam_recaptcha_public');
		$private	= $config->get( 'antispam_recaptcha_private');

		if( $recaptcha && $public && $private )
		{
			require_once( DISCUSS_CLASSES . DS .'recaptcha.php' );

			$obj = DiscussRecaptcha::recaptcha_check_answer( $private , $_SERVER['REMOTE_ADDR'] , $post['recaptcha_challenge_field'] , $post['recaptcha_response_field'] );

			if(!$obj->is_valid)
			{
				$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_POST_INVALID_RECAPTCHA_RESPONSE');
				$output[ 'type' ]       = 'error.captcha';

				echo $this->outputJson( $output );
				return false;
			}
		}

		// @rule: Bind parameters
		if( $config->get( 'reply_field_references' ) )
		{
			$postTable->bindParams( $post );
		}

		// Bind file attachments
		if( $acl->allowed( 'add_attachment' , '0' ) )
		{
			$postTable->bindAttachments();
		}

		$isNew 	= false;

		// @trigger: onBeforeSave
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentBeforeSave('post', $postTable, $isNew);

		if ( !$postTable->store() )
		{
			// Append result
			$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ERROR');
			$output[ 'type' ]       = 'error';

			echo $this->outputJson( $output );
			return false;
		}

		// @trigger: onAfterSave
		DiscussEventsHelper::onContentAfterSave('post', $postTable, $isNew);

		$result['status']	= 'success';
		$result['title']	= JText::_('COM_EASYDISCUSS_SUCCESS_SAVE_CONTENT');
		$result['message']	= JText::_('COM_EASYDISCUSS_SUCCESS_EDIT_REPLY');

		//get parent post
		$parentId		= $postTable->parent_id;
		$parentTable 	= DiscussHelper::getTable( 'Post' );
		$parentTable->load($parentId);

		// filtering badwords
		$postTable->title 			= DiscussHelper::wordFilter( $postTable->title);
		$postTable->content 		= DiscussHelper::wordFilter( $postTable->content);

		// show inline successful message.
		$output[ 'message' ]    = $result['message'];
		$output[ 'type' ]       = 'success';

		//all access control goes here.
		$canDelete  	= false;

		if( DiscussHelper::isSiteAdmin() || $acl->allowed('delete_reply', '0') || $postTable->user_id == $user->id )
		{
			$canDelete  = true;
		}

		// @rule: URL References
		$postTable->references  = $postTable->getReferences();

		// set for vote status
		$voteModel				= $this->getModel( 'Votes' );
		$postTable->voted		= $voteModel->checkUserVote( $postTable->id );

		// get total vote for this reply
		$postTable->totalVote	= $postTable->sum_totalvote;

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$creator = DiscussHelper::getTable( 'Profile' );
		$creator->load( $postTable->user_id );

		$postTable->user = $creator;

		// clean up bad code
		$JFilter 			= JFilterInput::getInstance();
		$postTable->content 	= $JFilter->clean($postTable->content);
		$postTable->content_raw	= $postTable->content;
		$postTable->content		= Parser::bbcode( $postTable->content );

		//default value
		$postTable->isVoted 		= 0;
		$postTable->total_vote_cnt	= 0;
		$postTable->likesAuthor     = '';
		$postTable->minimize    	= 0;

		if ( $config->get( 'main_content_trigger_replies' ) )
		{
			// process content plugins
			DiscussEventsHelper::importPlugin( 'content' );
			DiscussEventsHelper::onContentPrepare('reply', $postTable);

			$postTable->event = new stdClass();

			$results	= DiscussEventsHelper::onContentBeforeDisplay('reply', $postTable);
			$postTable->event->beforeDisplayContent	= trim(implode("\n", $results));

			$results	= DiscussEventsHelper::onContentAfterDisplay('reply', $postTable);
			$postTable->event->afterDisplayContent	= trim(implode("\n", $results));
		}

		$theme		= new DiscussThemes();
		$question	= DiscussHelper::getTable( 'Post' );
		$question->load( $postTable->parent_id );

		$recaptcha	= '';
		$enableRecaptcha	= $config->get('antispam_recaptcha');
		$publicKey			= $config->get('antispam_recaptcha_public');

		if( $enableRecaptcha && !empty( $publicKey ) )
		{
			require_once( DISCUSS_CLASSES . DS . 'recaptcha.php' );
			$recaptcha	= getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl'), 'edit-reply-recaptcha' .  $postTable->id);
		}

		$theme->set( 'replies'		, array($postTable) );
		$theme->set( 'isMine'		, DiscussHelper::isMine( $postTable->user_id) );
		$theme->set( 'isAdmin'		, DiscussHelper::isSiteAdmin() );
		$theme->set( 'canDeleteReply'	, $canDelete);
		$theme->set( 'isMainLocked'	, $question->islock );
		$theme->set( 'question'		, $question );
		$theme->set( 'recaptcha'	, $recaptcha );

		$html	= $theme->fetch( 'reply.item.php' );

		$output[ 'content']		= $html;

		if( $recaptcha && $public && $private )
		{
			$output[ 'type' ]	= 'success.captcha';
		}

		if(! $parentTable->islock)
		{
			$output[ 'type' ]	= 'locked';
		}

		echo $this->outputJson( $output );
		exit;
	}

	public function pluginQuickReply($parentId )
	{
		$ajax 	= DiscussHelper::getHelper( 'Ajax' );
		$config = DiscussHelper::getConfig();
		$acl 	= DiscussHelper::getHelper( 'ACL' );
		$my 	= JFactory::getUser();

		if( !$config->get('main_allowguestpost') && $my->id == 0 )
		{
			$ajax->fail( JText::_('COM_EASYDISCUSS_PLEASE_KINDLY_LOGIN_INORDER_TO_REPLY') );
			return $ajax->send();
		}

		if( !$acl->allowed('add_reply', '0') )
		{
			$ajax->fail( JText::_('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY') );
			return $ajax->send();
		}

		$content 	= JRequest::getVar( 'content', '', 'post', 'none' , JREQUEST_ALLOWRAW );

		if( empty( $content ) )
		{
			$ajax->fail( JText::_( 'COM_EASYDISCUSS_PLUGIN_PLEASE_ENTER_CONTENT' ) );
			return $ajax->send();
		}



		$replier    = new stdClass();

		if($my->id != 0)
		{
			$replier->id	 = $my->id;
			$replier->name	 = $my->name;
		}
		else
		{
			$replier->id	 = 0;
			$replier->name	 = JText::_('COM_EASYDISCUSS_GUEST'); // TODO: user the poster_name
		}

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$creator = DiscussHelper::getTable( 'Profile' );
		$creator->load( $replier->id);

		$now 		= JFactory::getDate()->toMySQL();
		$post 		= DiscussHelper::getTable( 'Post' );
		$post->set( 'parent_id' , $parentId );
		$post->set( 'content'	, $content );
		$post->set( 'published'	, DISCUSS_ID_PUBLISHED );
		$post->set( 'user_type' , DISCUSS_POSTER_MEMBER );
		$post->set( 'user_id'	, $my->id );
		$post->set( 'created'	, $now );
		$post->set( 'replied'	, $now );

		if($config->get('main_moderatepost', 0))
		{
			$table->published	= DISCUSS_ID_PENDING;
		}

		if( $config->get( 'antispam_akismet' ) && ( $config->get('antispam_akismet_key') ) )
		{
			require_once( DISCUSS_CLASSES . DS . 'akismet.php' );

			$data = array(
							'author'    => $my->name,
							'email'     => $my->email,
							'website'   => JURI::root() ,
							'body'      => $content,
							'alias' => ''
						);

			$akismet = new Akismet( JURI::root() , $config->get( 'antispam_akismet_key' ) , $data );

			if( !$akismet->errorsExist() )
			{
				if( $akismet->isSpam() )
				{
					$ajax->fail( JText::_( 'COM_EASYDISCUSS_AKISMET_SPAM_DETECTED' ) );
					return $ajax->send();
				}
			}
		}

		// @trigger: onBeforeSave
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentBeforeSave( 'reply' , $post , true );

		if ( !$post->store() )
		{
			$ajax->error( JText::_('COM_EASYDISCUSS_ERROR_SUBMIT_REPLY') );
			return $ajax->send();
		}

		// @trigger: onAfterSave
		DiscussEventsHelper::onContentAfterSave('reply', $post, true );

		$question		= DiscussHelper::getTable( 'Post' );
		$question->load( $post->parent_id );

		// @rule: Add notifications for the thread starter
		if( $post->published && $config->get( 'main_notifications_reply') )
		{
			// Get all users that are subscribed to this post
			$model			= $this->getModel( 'Posts' );
			$participants	= $model->getParticipants( $post->parent_id );

			// Add the thread starter into the list of participants.
			$participants[]	= $question->get( 'user_id' );

			// Notify all subscribers
			foreach( $participants as $participant )
			{
				if( $participant != $my->id )
				{
					$notification	= DiscussHelper::getTable( 'Notifications' );

					$notification->bind( array(
							'title'	=> JText::sprintf( 'COM_EASYDISCUSS_REPLY_DISCUSSION_NOTIFICATION_TITLE' , $question->get( 'title' ) ),
							'cid'	=> $question->get( 'id' ),
							'type'	=> DISCUSS_NOTIFICATIONS_REPLY,
							'target'	=> $participant,
							'author'	=> $post->get( 'user_id' ),
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $question->get( 'id' )
						) );
					$notification->store();
				}
			}

		}

		if( $post->published )
		{
			// @rule: Badges
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.reply' , $post->user_id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_REPLY' , $question->title ) );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.reply' , $post->user_id );

			// @rule: AUP integrations
			DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_REPLY , $post->user_id , $question->title );
		}

		$post->title 		= DiscussHelper::wordFilter( $post->title);
		$post->content 		= DiscussHelper::wordFilter( $post->content);

		//send notification to all comment's subscribers that want to receive notification immediately
		$notify	= DiscussHelper::getNotification();

		$emailData['postTitle']		= $question->title;
		$emailData['comment']		= $post->content;
		$emailData['commentAuthor']	= ($creator->id) ? $creator->getName() : JText::_('COM_EASYDISCUSS_GUEST');
		$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $question->id, false, true);
		$emailData['replyContent']	= Parser::bbcode( $post->content );
		$emailData['replyAuthor' ]	= ($creator->id) ? $creator->getName() : JText::_('COM_EASYDISCUSS_GUEST');
		$emailData['replyAuthorAvatar' ] = $creator->getAvatar();

		if( ($config->get('main_sitesubscription') ||  $config->get('main_postsubscription') ) && $config->get('notify_subscriber') && $post->published == DISCUSS_ID_PUBLISHED)
		{
			$modelSubscribe		= $this->getModel( 'Subscribe' );
			$subscribers        = $modelSubscribe->getPostSubscribers($post->parent_id);

			$emails = array();
			if(! empty($subscribers))
			{
				foreach($subscribers as $subscriber)
				{
					$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscriber, true, true);

					if( !empty($my->id) && ($my->id != $subscriber->userid) )
					{
						$notify->addQueue($subscriber->email, JText::sprintf('COM_EASYDISCUSS_NEW_COMMENT_ADDED', $question->title), '', 'email.subscription.comment.new.php', $emailData);
					}
				}
			}
		}

		//notify post owner.
		if( $config->get( 'notify_owner' ) && $post->published	== DISCUSS_ID_PUBLISHED && ($my->id != $replier->id) )
		{
			$postOwner      = JFactory::getUser( $question->user_id );

			$notify->addQueue( array($postOwner->email) , JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $question->id , $question->title), '', 'email.post.reply.new.php', $emailData);
		}

		//if reply under moderation, send owner a notification.
		if( $post->published == DISCUSS_ID_PENDING )
		{
			//send to admin.
			$adminEmails = array();
			$admins = $notify->getAdminEmails();

			if(! empty($admins))
			{
				foreach($admins as $admin)
				{
					$adminEmails[]   = $admin->email;
				}

				// Generate hashkeys to map this current request
				$hashkey		= DiscussHelper::getTable( 'Hashkeys' );
				$hashkey->uid	= $post->id;
				$hashkey->type	= DISCUSS_REPLY_TYPE;
				$hashkey->store();

				require_once( DISCUSS_HELPERS . DS . 'router.php' );
				$approveURL		= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key );
				$rejectURL 		= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key );
				$emailData[ 'moderation' ]	= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_REPLY' ) . '</a>';
				$emailData[ 'moderation' ] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . '<a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';

				$notify->addQueue( $adminEmails , JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_MODERATE', $question->title), '', 'email.post.reply.moderation.php', $emailData);
			}
		}

		// @rule: Jomsocial activity integrations
		if( $post->published == DISCUSS_ID_PUBLISHED )
		{
			DiscussHelper::getHelper( 'jomsocial' )->addActivityReply( $post );
		}

		$autoSubscribed = false;
		if( $post->published == DISCUSS_ID_PUBLISHED && $config->get('main_autopostsubscription') && $config->get('main_postsubscription') && $post->user_type != 'twitter')
		{
			//automatically subscribe this user into this post.
			$subscription_info = array();
			$subscription_info['type'] 		= 'post';
			$subscription_info['userid'] 	= ( !empty($post->user_id) ) ? $post->user_id : '0';
			$subscription_info['email'] 	= ( !empty($post->user_id) ) ? $my->email : $post->poster_email;;
			$subscription_info['cid'] 		= $question->id;
			$subscription_info['member'] 	= ( !empty($post->user_id) ) ? '1':'0';
			$subscription_info['name'] 		= ( !empty($post->user_id) ) ? $creator->getName() : $post->poster_name;
			$subscription_info['interval'] 	= 'instant';

			$model	= $this->getModel( 'Subscribe' );
			$sid    = '';

			if( $subscription_info['userid'] == 0)
			{
				$sid = $model->isPostSubscribedEmail($subscription_info);
				if( empty( $sid ) )
				{
					if( $model->addSubscription($subscription_info))
					{
						$autoSubscribed = true;
					}
				}
			}
			else
			{
				$sid = $model->isPostSubscribedUser($subscription_info);
				if( empty( $sid['id'] ))
				{
					//add new subscription.
					if( $model->addSubscription($subscription_info) )
					{
						$autoSubscribed = true;
					}
				}
			}
		}

		// Update the results to be sent back to the ajax success method.
		$content 	= Parser::bbcode( $post->content );
		$config 	= DiscussHelper::getConfig();
		$reply 		= $post;

		//default value
		$reply->isVoted 		= 0;
		$reply->total_vote_cnt	= 0;
		$reply->likesAuthor     = '';
		$reply->minimize    	= 0;

		$voteModel			= $this->getModel('votes');
		$reply->voted		= $voteModel->checkUserVote( $reply->id );
		$reply->totalVote	= 0;

		$sub 				= DiscussHelper::getJoomlaVersion() >= '1.6' ? DS . 'easydiscuss' : '';
		require_once( DISCUSS_HELPERS . DS . 'vote.php' );
		ob_start();
		include_once( JPATH_ROOT . DS . 'plugins' . DS . 'content' . DS . 'easydiscuss' . $sub . DS . 'tmpl' . DS . 'list.item.php' );
		$html 		= ob_get_contents();
		ob_end_clean();

		$ajax->success( JText::_('COM_EASYDISCUSS_PLUGIN_REPLY_SUBMITTED') , $html );
		$ajax->send();
	}

	/*
	 * Process new reply submission
	 *	called via*an iframe.
	 */
	function ajaxSubmitReply()
	{
		$user 	= JFactory::getUser();
		$config = DiscussHelper::getConfig();
		$ajax	= new Disjax();
		$acl	= DiscussHelper::getHelper( 'ACL' );
		$post	= JRequest::get( 'POST' );

		// @task: User needs to be logged in, in order to submit a new reply.
		if( !$config->get('main_allowguestpost') && $user->id == 0 )
		{
			// Append result
			$output     = array();
			$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_PLEASE_KINDLY_LOGIN_INORDER_TO_REPLY');
			$output[ 'type' ]       = 'error';

			echo $this->outputJson( $output );
			return false;
		}

		if( !$acl->allowed('add_reply', '0') )
		{
			// Append result
			$output     = array();
			$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY');
			$output[ 'type' ]       = 'error';

			echo $this->outputJson( $output );
			return false;
		}



  		//now we need to check the category acl reply
  		$parentCatId  = $post['parent_catid'];
  		if( ! empty($parentCatId) )
  		{
  		    $parentCat	= DiscussHelper::getTable( 'Category' );
  		    $parentCat->load($parentCatId);

  		    if(! $parentCat->canReply() )
  		    {
				// Append result
				$output     = array();
				$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY');
				$output[ 'type' ]       = 'error';

				echo $this->outputJson( $output );
				return false;
  		    }
  		}

		if( empty( $post[ 'dc_reply_content' ] ) )
		{
			// Append result
			$output     = array();
			$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY');
			$output[ 'type' ]       = 'error';

			echo $this->outputJson( $output );
			return false;
		}

		if( empty($user->id) )
		{
			if(empty($post['user_type']))
			{
				// Append result
				$output     = array();
				$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_INVALID_USER_TYPE');
				$output[ 'type' ]       = 'error';

				echo $this->outputJson( $output );
				return false;
			}

			if(!DiscussUserHelper::validateUserType($post['user_type']))
			{
				$output     = array();
				$output[ 'message' ]    = JText::sprintf('COM_EASYDISCUSS_THIS_USERTYPE_HAD_BEEN_DISABLED', $post['user_type']);
				$output[ 'type' ]       = 'error';

				echo $this->outputJson( $output );
				return false;
			}
		}
		else
		{
			$post['user_type'] 		= 'member';
			$post['poster_name'] 	= '';
			$post['poster_email'] 	= '';
		}

		// get id if available
		$id		= 0;

		// set alias
		$post['alias'] 	= DiscussHelper::getAlias( $post['title'], 'post' );

		// set post owner
		$post['user_id']			= $user->id;

		// Rebind the post data
		$post[ 'dc_reply_content' ] = JRequest::getVar( 'dc_reply_content', '', 'post', 'none' , JREQUEST_ALLOWRAW );

		// bind the table
		$table		= DiscussHelper::getTable( 'Post' );
		$table->bind( $post , true );


		if($config->get('main_moderatepost', 0))
		{
			$table->published	= DISCUSS_ID_PENDING;
		}
		else
		{
			$table->published	= DISCUSS_ID_PUBLISHED;
		}

		//recaptcha integration
		$recaptcha	= $config->get('antispam_recaptcha');
		$public		= $config->get('antispam_recaptcha_public');
		$private	= $config->get('antispam_recaptcha_private');

		if( $recaptcha && $public && $private )
		{
			require_once( DISCUSS_CLASSES . DS .'recaptcha.php' );
			$obj = DiscussRecaptcha::recaptcha_check_answer( $private , $_SERVER['REMOTE_ADDR'] , $post['recaptcha_challenge_field'] , $post['recaptcha_response_field'] );

			if(!$obj->is_valid)
			{
				$output     = array();
				$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_POST_INVALID_RECAPTCHA_RESPONSE');
				$output[ 'type' ]       = 'error.captcha';

				echo $this->outputJson( $output );
				return false;
			}
		}

		if( $config->get( 'antispam_akismet' ) && ( $config->get('antispam_akismet_key') ) )
		{
			require_once( DISCUSS_CLASSES . DS . 'akismet.php' );

			$data = array(
							'author'    => $user->name,
							'email'     => $user->email,
							'website'   => JURI::root() ,
							'body'      => $post['dc_reply_content'] ,
							'alias' => ''
						);

			$akismet = new Akismet( JURI::root() , $config->get( 'antispam_akismet_key' ) , $data );

			if( !$akismet->errorsExist() )
			{
				if( $akismet->isSpam() )
				{
					$output     = array();
					$output[ 'message' ]    = JText::_('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED');
					$output[ 'type' ]       = 'error';

					echo $this->outputJson( $output );
					return false;
				}
			}
		}

		// hold last inserted ID in DB
		$lastId = null;

		// @rule: Bind parameters
		if( $config->get( 'reply_field_references' ) )
		{
			$table->bindParams( $post );
		}

		$isNew	= true;

		// @trigger: onBeforeSave
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentBeforeSave('reply', $table , $isNew);

		if ( !$table->store() )
		{
			$output					= array();
			$output[ 'message' ]	= JText::_('COM_EASYDISCUSS_ERROR_SUBMIT_REPLY');
			$output[ 'type' ]		= 'error';

			echo $this->outputJson( $output );
			return false;
		}

		// @trigger: onAfterSave
		DiscussEventsHelper::onContentAfterSave('reply', $table , $isNew);

		$question		= DiscussHelper::getTable( 'Post' );
		$question->load( $table->parent_id );

		// @rule: Add notifications for the thread starter
		if( $table->published && $config->get( 'main_notifications_reply') )
		{
			// Get all users that are subscribed to this post
			$model			= $this->getModel( 'Posts' );
			$participants	= $model->getParticipants( $table->parent_id );

			// Add the thread starter into the list of participants.
			$participants[]	= $question->get( 'user_id' );

			// Notify all subscribers
			foreach( $participants as $participant )
			{
				if( $participant != $user->id )
				{
					$notification	= DiscussHelper::getTable( 'Notifications' );

					$notification->bind( array(
							'title'	=> JText::sprintf( 'COM_EASYDISCUSS_REPLY_DISCUSSION_NOTIFICATION_TITLE' , $question->get( 'title' ) ),
							'cid'	=> $question->get( 'id' ),
							'type'	=> DISCUSS_NOTIFICATIONS_REPLY,
							'target'	=> $participant,
							'author'	=> $table->get( 'user_id' ),
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $question->get( 'id' )
						) );
					$notification->store();
				}
			}

			// @rule: Detect if any names are being mentioned in the post
			$names 			= DiscussHelper::getHelper( 'String' )->detectNames( $table->content );

			if( $names )
			{
				foreach( $names as $name )
				{
					$name			= JString::str_ireplace( '@' , '' , $name );
					$id 			= DiscussHelper::getUserId( $name );

					if( !$id || $id == $table->get( 'user_id') )
					{
						continue;
					}

					$notification	= DiscussHelper::getTable( 'Notifications' );

					$notification->bind( array(
							'title'		=> JText::sprintf( 'COM_EASYDISCUSS_MENTIONED_REPLY_NOTIFICATION_TITLE' , $question->get( 'title' ) ),
							'cid'		=> $question->get( 'id' ),
							'type'		=> DISCUSS_NOTIFICATIONS_MENTIONED,
							'target'	=> $id,
							'author'	=> $table->get( 'user_id' ),
							'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $question->get( 'id' )
						) );
					$notification->store();
				}
			}
		}

		if( $table->published )
		{
			// @rule: Badges
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.reply' , $table->user_id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_REPLY' , $question->title ) );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.reply' , $table->user_id );

			// @rule: AUP integrations
			DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_REPLY , $table->user_id , $question->title );

			// @rule: ranking
			DiscussHelper::getHelper( 'ranks' )->assignRank( $table->user_id );
		}

		// Bind file attachments
		if( $acl->allowed( 'add_attachment' , '0' ) )
		{
			$table->bindAttachments();
		}

		$replier    = new stdClass();

		if($user->id != 0)
		{
			$replier->id	 = $user->id;
			$replier->name	 = $user->name;
		}
		else
		{
			$replier->id	 = 0;
			$replier->name	 = JText::_('COM_EASYDISCUSS_GUEST'); // TODO: user the poster_name
		}

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$creator = DiscussHelper::getTable( 'Profile' );
		$creator->load( $replier->id);

		$table->user = $creator;

		$voteModel = $this->getModel('votes');

		// clean up bad code
		$table->content_raw	= $table->content;
		$table->content		= Parser::bbcode( $table->content );

		// @rule: URL References
		$table->references  = $table->getReferences();

		// set for vote status
		$table->voted		= $voteModel->checkUserVote( $table->id );

		// get total vote for this reply
		$table->totalVote	= $table->sum_totalvote;

		// format created date by adding offset if any
		$table->created		= DiscussDateHelper::getDate( $table->created )->toFormat();

		$result['status']	= 'success';
		$result['title']	= JText::_('COM_EASYDISCUSS_SUCCESS_SUBMIT_REPLY');
		$result['id']		= $table->id;
		$result['message']	= JText::_('COM_EASYDISCUSS_REPLY_SAVED');


		$table->title 			= DiscussHelper::wordFilter( $table->title);
		$table->content 		= DiscussHelper::wordFilter( $table->content);

		$result['content']	= Parser::bbcode( $table->content );


		//all access control goes here.
		$canDelete		= false;
		$isMainLocked	= false;

		if( DiscussHelper::isSiteAdmin() || $acl->allowed('delete_reply', '0') || $table->user_id == $user->id )
		{
			$canDelete  = true;
		}

		$parent			= DiscussHelper::getTable( 'Post' );
		$parent->load( $table->parent_id );

		$isMainLocked	= $parent->islock;

		//default value
		$table->isVoted 		= 0;
		$table->total_vote_cnt	= 0;
		$table->likesAuthor		= '';
		$table->minimize		= 0;


		if ( $config->get( 'main_content_trigger_replies' ) )
		{
			// process content plugins
			DiscussEventsHelper::importPlugin( 'content' );
			DiscussEventsHelper::onContentPrepare('reply', $table);

			$table->event = new stdClass();

			$results	= DiscussEventsHelper::onContentBeforeDisplay('reply', $table);
			$table->event->beforeDisplayContent	= trim(implode("\n", $results));

			$results	= DiscussEventsHelper::onContentAfterDisplay('reply', $table);
			$table->event->afterDisplayContent	= trim(implode("\n", $results));
		}

		$tpl	= new DiscussThemes();

		$tpl->set( 'replies'		, array($table) );
		$tpl->set( 'isMine'			, DiscussHelper::isMine( $parent->user_id) );
		$tpl->set( 'isAdmin'		, DiscussHelper::isSiteAdmin() );
		$tpl->set( 'canDeleteReply'	, $canDelete);
		$tpl->set( 'isMainLocked'	, $isMainLocked);
		$tpl->set( 'config'			, $config);
		$tpl->set( 'my'				, $user);
		$tpl->set( 'question'		, $question );

		$recaptcha	= '';
		$enableRecaptcha	= $config->get('antispam_recaptcha', 0);
		$publicKey			= $config->get('antispam_recaptcha_public');


		$html	= ( $table->published == DISCUSS_ID_PENDING ) ? $tpl->fetch( 'reply.item.moderation.php' ) : $tpl->fetch( 'reply.item.php' );

		//send notification to all comment's subscribers that want to receive notification immediately
		$notify	= DiscussHelper::getNotification();

		$emailData['postTitle']		= $parent->title;
		$emailData['comment']		= $table->content;
		$emailData['commentAuthor']	= ($user->id) ? $user->name : JText::_('COM_EASYDISCUSS_GUEST');
		$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $parent->id, false, true);
		$emailData['replyContent']	= $table->content;
		$emailData['replyAuthor' ]	= ($user->id) ? $user->name : JText::_('COM_EASYDISCUSS_GUEST');
		$emailData['replyAuthorAvatar' ] = $creator->getAvatar();

		$subscriberEmails 			= array();

		if( ($config->get('main_sitesubscription') ||  $config->get('main_postsubscription') ) && $config->get('notify_subscriber') && $table->published == DISCUSS_ID_PUBLISHED)
		{
			$modelSubscribe		= $this->getModel( 'Subscribe' );
			$subscribers        = $modelSubscribe->getPostSubscribers($table->parent_id);

			$emails				= array();
			if(! empty($subscribers))
			{
				foreach($subscribers as $subscriber)
				{
					$subscriberEmails[]		= $subscriber->email;

					$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscriber, true, true);

					if( !empty($user->id) && ($user->id != $subscriber->userid) )
					{
						$notify->addQueue($subscriber->email, JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title), '', 'email.subscription.reply.new.php', $emailData);
					}
				}
			}
		}

		//notify post owner.
		$postOwnerId  	= $parent->user_id;
		$postOwner      = JFactory::getUser( $postOwnerId );
		$ownerEmail		= $postOwner->email;

		if( $parent->user_type != 'member' )
		{
			$ownerEmail 	= $parent->poster_email;
		}

		if( $config->get( 'notify_owner' ) && $table->published	== DISCUSS_ID_PUBLISHED && ($postOwnerId != $replier->id) && !in_array( $ownerEmail , $subscriberEmails ) && !empty( $ownerEmail ) )
		{
			$notify->addQueue( array($ownerEmail) , JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title), '', 'email.post.reply.new.php', $emailData);
		}

		//if reply under moderation, send owner a notification.
		if( $table->published == DISCUSS_ID_PENDING )
		{
			//send to admin.
			$adminEmails = array();
			$admins = $notify->getAdminEmails();

			if(! empty($admins))
			{
				foreach($admins as $admin)
				{
					$adminEmails[]   = $admin->email;
				}

				// Generate hashkeys to map this current request
				$hashkey		= DiscussHelper::getTable( 'Hashkeys' );
				$hashkey->uid	= $table->id;
				$hashkey->type	= DISCUSS_REPLY_TYPE;
				$hashkey->store();

				require_once( DISCUSS_HELPERS . DS . 'router.php' );
				$approveURL		= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key );
				$rejectURL 		= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key );
				$emailData[ 'moderation' ]	= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_REPLY' ) . '</a>';
				$emailData[ 'moderation' ] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . '<a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';

				$notify->addQueue( $adminEmails , JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_MODERATE', $parent->title), '', 'email.post.reply.moderation.php', $emailData);
			}
		}

		// @rule: Jomsocial activity integrations
		if( $table->published == DISCUSS_ID_PUBLISHED )
		{
			DiscussHelper::getHelper( 'jomsocial' )->addActivityReply( $table );
		}

		$autoSubscribed = false;
		if( $table->published == DISCUSS_ID_PUBLISHED && $config->get('main_autopostsubscription') && $config->get('main_postsubscription') && $table->user_type != 'twitter')
		{
			//automatically subscribe this user into this post.
			$subscription_info = array();
			$subscription_info['type'] 		= 'post';
			$subscription_info['userid'] 	= ( !empty($table->user_id) ) ? $table->user_id : '0';
			$subscription_info['email'] 	= ( !empty($table->user_id) ) ? $user->email : $table->poster_email;;
			$subscription_info['cid'] 		= $parent->id;
			$subscription_info['member'] 	= ( !empty($table->user_id) ) ? '1':'0';
			$subscription_info['name'] 		= ( !empty($table->user_id) ) ? $user->name : $table->poster_name;
			$subscription_info['interval'] 	= 'instant';

			$model	= $this->getModel( 'Subscribe' );
			$sid    = '';

			if( $subscription_info['userid'] == 0)
			{
				$sid = $model->isPostSubscribedEmail($subscription_info);
				if( empty( $sid ) )
				{
					if( $model->addSubscription($subscription_info))
					{
						$autoSubscribed = true;
					}
				}
			}
			else
			{
				$sid = $model->isPostSubscribedUser($subscription_info);
				if( empty( $sid['id'] ))
				{
					//add new subscription.
					if( $model->addSubscription($subscription_info) )
					{
						$autoSubscribed = true;
					}
				}
			}
		}

		// Append result
		$output     = array();
		$output[ 'message' ]    = ($autoSubscribed) ? JText::_( 'COM_EASYDISCUSS_SUCCESS_REPLY_POSTED_AND_SUBSCRIBED' ) : JText::_( 'COM_EASYDISCUSS_SUCCESS_REPLY_POSTED' );
		$output[ 'type' ]       = 'success';
		$output[ 'html' ]       = $html;


		if(  $enableRecaptcha && !empty( $publicKey ) )
		{
			$output[ 'type' ]       = 'success.captcha';
		}

		if( $config->get( 'main_syntax_highlighter' ) )
		{
			$output[ 'script' ]     = 'SyntaxHighlighter.highlight();';
		}

		echo $this->outputJson( $output );
	}

	private function outputJson( $output = null )
	{
		return '<script type="text/json" id="ajaxResponse">' . $this->json_encode( $output ) . '</script>';
	}

	/**
	 * Delete post
	 * and delete all reply as well
	 */
	function ajaxDeleteReply( $postId = null )
	{
		$djax	= new Disjax();
		$my		= JFactory::getUser();

		if(empty($postId))
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_MISSING_POST_ID');
			$djax->dialog( $options );
			$djax->send();
			return;
		}

		// bind the table
		$postTable		= DiscussHelper::getTable( 'Post' );
		$postTable->load( $postId );

		$isMine		= DiscussHelper::isMine($postTable->user_id);
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin )
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_DELETE');
			$djax->dialog( $options );
			$djax->send();
			return;
		}

		//chekc if the parent being locked. if yes, do not allow delete.
		$parentId		= $postTable->parent_id;
		$parentTable	= DiscussHelper::getTable( 'Post' );
		$parentTable->load( $parentId );

		if($parentTable->islock)
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_MAIN_POST_BEING_LOCKED');
			$djax->dialog( $options );
			$djax->send();
			return;
		}

		// @trigger: onBeforeDelete
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentBeforeDelete('reply', $postTable);

		if( !$postTable->delete() )
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_ERROR_DELETE_REPLY');
			$djax->dialog( $options );
			$djax->send();
			return;
		}
		else
		{
			// @trigger: onAfterDelete
			DiscussEventsHelper::onContentAfterDelete('reply', $postTable);

			// @rule: Process AUP integrations
			DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_REPLY , $postTable->user_id , $parentTable->title );

			$djax->script('Foundry("#dc_reply_' . + $postId .'").fadeOut(\'500\');');
			$djax->script('Foundry("#dc_reply_' . + $postId .'").remove();');

			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_SUCCESS_DELETE_REPLY');
			$djax->dialog( $options );
			$djax->send();
		}

		$djax->send();
		return;
	}


	/**
	 * Get edit form with all details
	 */
	function ajaxGetEditForm( $postId = null )
	{
		$config = DiscussHelper::getConfig();
		$djax   = new Disjax();

		$id 	= $postId;

		$postTable 			= DiscussHelper::getTable( 'Post' );
		$postTable->load( $id );

		$isMine		= DiscussHelper::isMine($postTable->user_id);
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin )
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION');
			$djax->dialog( $options );
			$djax->send();
			return;
		}

		if ( empty($id) )
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_ERROR_LOAD_POST');
			$djax->dialog( $options );
			$djax->send();
			return;
		}
		else
		{
			// get post tags
			$postsTagsModel	= $this->getModel('PostsTags');

			$tags = $postsTagsModel->getPostTags( $id );

			// clean up bad code
			$postTable->tags	= $tags;

			$result['status']	= 'success';
			$result['id']		= $postTable->id;

			// select top 20 tags.
			$tagmodel	= $this->getModel( 'Tags' );
			$tags   	= $tagmodel->getTagCloud('20','post_count','DESC');

			//recaptcha integration
			$recaptcha	= '';
			$enableRecaptcha	= $config->get('antispam_recaptcha');
			$publicKey			= $config->get('antispam_recaptcha_public');

			if(  $enableRecaptcha && !empty( $publicKey ) )
			{
				require_once( DISCUSS_CLASSES . DS . 'recaptcha.php' );
				$recaptcha	= getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl') );
			}

			$tpl	= new DiscussThemes();
			$tpl->set( 'post'		, $postTable );
			$tpl->set( 'config'		, $config );
			$tpl->set( 'tags'		, $tags );
			$tpl->set( 'recaptcha'	, $recaptcha );
			$tpl->set( 'isEditMode'	, true );

			$result['output']	= $tpl->fetch('new.post.php');

			$djax->assign('dc_main_post_edit', $result['output']);
			$djax->script('Foundry("#dc_main_post_edit").slideDown(\'fast\');');
			$djax->script('Foundry("#edit_content").markItUp(mySettings);');

		}

		$djax->send();
		return;
	}


	/**
	 * Ajax Call
	 * Submit new reply
	 */
	function ajaxSaveEditForm( $data = null )
	{
		$config 	= DiscussHelper::getConfig();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();

		$djax   = new Disjax();

		$result = array();

		if( !$config->get('main_allowguestpost') && $my->id == 0 )
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_ERROR_GUEST_NOT_ALLOW_EDIT');
			$djax->dialog( $options );
			$djax->send();
			return;
		}

		// get all forms value
		$post	= DiscussStringHelper::ajaxPostToArray($data);
		$valid  = $this->_fieldValidate($post);
		if(! $valid[0])
		{
			$djax->assign('dc_post_notification .msg_in', $valid[1]);
			$djax->script('Foundry("#dc_post_notification .msg_in").addClass("dc_error")');
			$djax->script('Foundry("#submit").removeAttr("disabled");');
			$djax->send();
		}

		// validate recaptcha if enabled
		$recaptcha	= $config->get( 'antispam_recaptcha');
		$public		= $config->get( 'antispam_recaptcha_public');
		$private	= $config->get( 'antispam_recaptcha_private');

		if( $recaptcha && $public && $private )
		{
			require_once( DISCUSS_CLASSES . DS .'recaptcha.php' );

			$obj = DiscussRecaptcha::recaptcha_check_answer( $private , $_SERVER['REMOTE_ADDR'] , $post['recaptcha_challenge_field'] , $post['recaptcha_response_field'] );

			if(!$obj->is_valid)
			{
				$djax	= new Disjax();
				$djax->assign('dc_post_notification .msg_in', JText::_('COM_EASYDISCUSS_POST_INVALID_RECAPTCHA_RESPONSE') );
				$djax->script('Foundry("#dc_post_notification .msg_in").addClass("dc_error")');
				$djax->script('Foundry("#submit").removeAttr("disabled");');
				$djax->script('Recaptcha.reload();');

				$djax->send();
				return false;
			}
		}


		// get id if available
		$id		= (isset($post['id'])) ? $post['id'] : 0;

		// get post parent id
		$parent	= (isset($post['parent_id'])) ? $post['parent_id'] : 0;


		// bind the table
		$postTable		= DiscussHelper::getTable( 'Post' );
		$postTable->load( $id );

		$isNew		= (bool) $postTable->id;

		$isMine		= DiscussHelper::isMine($postTable->user_id);
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin )
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION');
			$djax->dialog( $options );
			$djax->send();
			return;
		}

		$postTable->bind( $post , true );

		// hold last inserted ID in DB
		$lastId = null;

		$postType	= ($postTable->parent_id == 0) ? 'post' : 'reply';

		// @trigger: onBeforeSave
		DiscussEventsHelper::importPlugin( 'content' );
		DiscussEventsHelper::onContentBeforeSave($postType, $postTable, $isNew);

		if ( !$postTable->store() )
		{
			//JError::raiseError(500, $postTable->getError() );

			//turn off edit form
			$djax->script('Foundry("#dc_main_post_edit").remove();');
			$djax->script('Foundry("#dc_main_post").slideDown(\'fast\');');

			$djax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_EDIT_FAILED') );
			$djax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$djax->send();
			return;
		}

		// @trigger: onAfterSave
		DiscussEventsHelper::onContentAfterSave($postType, $postTable, $isNew);

		$lastId		= $postTable->id;
		$message	= JText::_( 'COM_EASYDISCUSS_POST_SAVED' );

		// Bind file attachments
		$postTable->bindAttachments();

		$date = JFactory::getDate();

		$postsTagsModel = $this->getModel('PostsTags');

		$previousTags = array();
		$tmppreviousTags = $postsTagsModel->getPostTags($postTable->id);
		if(!empty($tmppreviousTags))
		{
			foreach($tmppreviousTags as $previoustag)
			{
				$previousTags[] = $previoustag->id;
			}
		}

		$postsTagsModel->deletePostTag( $postTable->id );

		//@task: Save tags
		$tags			= array();

		if(isset($post['tags[]']))
		{
			$tagList   = $post['tags[]'];
			if(is_array($tagList))
			{
				$tags   = $tagList;
			}
			else
			{
				$tags[] = $tagList;
			}
		}

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
						$tagInfo['title'] 		= JString::trim( $tag );
						$tagInfo['alias'] 		= DiscussHelper::getAlias( $tag, 'tag' );
						$tagInfo['created']		= $date->toMySQL();
						$tagInfo['published']	= 1;
						$tagInfo['user_id']		= $my->id;

						$tagTable->bind($tagInfo);
						$tagTable->store();
					}
					else
					{
						$tagTable->load( $tag , true );
					}

					$postTagInfo = array();

					//@task: Store in the post tag
					$postTagTable	= DiscussHelper::getTable( 'PostsTags' );
					$postTagInfo['post_id']	= $postTable->id;
					$postTagInfo['tag_id']	= $tagTable->id;

					$postTagTable->bind( $postTagInfo );
					$postTagTable->store();

					//send notification to all tag subscribers
					if($config->get('main_sitesubscription') && !in_array($tagTable->id, $previousTags))
					{
						$modelSubscribe		= $this->getModel( 'Subscribe' );
						$subscribers        = $modelSubscribe->getTagSubscribers($tagTable->id);

						$emails = array();
						if(! empty($subscribers))
						{
							$notify	= DiscussHelper::getNotification();

							$emailData['postTitle']		= $postTable->title;
							$emailData['tagTitle']		= $tagTable->title;
							$emailData['postAuthor']	= $my->name;
							$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $postTable->id, false, true);

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

		$tags = $postsTagsModel->getPostTags( $postTable->id );

		// reformat content.
		$postTable->title 			= DiscussHelper::wordFilter( $postTable->title);
		$postTable->content 		= DiscussHelper::wordFilter( $postTable->content);

		// parse BBCode to display

		$postTable->content_raw	= $postTable->content;
		$postTable->content	= Parser::bbcode( $postTable->content );

		$voteModel = $this->getModel('votes');

		// set for vote status
		$postTable->voted	= $voteModel->checkUserVote( $postTable->id );

		// get total votes for this post
		$postTable->totalVote = $postTable->sum_totalvote;

		// format created date by adding offset if any
		$newDate    		= DiscussDateHelper::getDate( $postTable->created );
		$postTable->created	= $newDate->toFormat();

		if ( $postTable->user_id == 0 )
		{
			$owner->id			= 0;
			$owner->name		= 'Guest';
			$postTable->user	= $owner;
		}
		else
		{
			$poster	= JFactory::getUser( $postTable->user_id );
			$owner->id		= $postTable->user_id;
			$owner->name	= $poster->name;
			$postTable->user		= $owner;
		}

		$canDelete  = false;
		if($config->get('main_allowdelete', 2) == 2)
		{
			$canDelete  = (DiscussHelper::isSiteAdmin()) ? true : false;
		}
		else if($config->get('main_allowdelete', 2) == 1)
		{
		    $acl 		= DiscussHelper::getHelper( 'ACL' );
			$canDelete	= ($my->id != 0 && $acl->allowed('delete_reply', '0') ) ? true : false;
		}

		//load porfile info and auto save into table if user is not already exist in discuss's user table.
		$creator = DiscussHelper::getTable( 'Profile' );
		$creator->load( $owner->id);

		$tplA	= new DiscussThemes();
		$tplA->set( 'isMine'	, DiscussHelper::isMine($postTable->user_id) );
		$tplA->set( 'isAdmin'	, DiscussHelper::isSiteAdmin() );
		$tplA->set( 'post'		, $postTable );
		$tplA->set( 'tags'		, $tags );
		$tplA->set( 'config'	, $config );
		$tplA->set( 'canDelete'	, $canDelete );
		$tplA->set( 'creator'	, $creator );
		$tplA->set( 'my'		, $my );

		$mainpost = $tplA->fetch( 'entry.post.php' );

		//turn off edit form
		$djax->script('Foundry("#dc_main_post_edit").remove();');

		//turn on main post content
		$djax->assign('dc_main_post', $mainpost);

		$djax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_EDIT_SUCCESS') );
		$djax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_success" );' );

		$djax->script('Foundry("#dc_main_post").slideDown(\'fast\');');
		//$djax->script('effect.highlight("#dc_main_post");');

		$djax->script('discuss.post.cancelEditPost();');

		$djax->send();
		return;
	}

	function ajaxReloadRecaptcha($divId = null, $reId = 'recaptcha-image')
	{
		$config 	= DiscussHelper::getConfig();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$djax   = new Disjax();

		//recaptcha integration
		$recaptcha	= '';
		$enableRecaptcha	= $config->get('antispam_recaptcha', 0);
		$publicKey			= $config->get('antispam_recaptcha_public');

		if(  $enableRecaptcha && !empty( $publicKey ) )
		{
			require_once( DISCUSS_CLASSES . DS . 'recaptcha.php' );
			$recaptcha	= getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl'), $reId );

			$djax->assign($divId, $recaptcha);
		}
		else
		{
			//somehow ajax must return something.
			$djax->assign($divId, '');
		}

		$djax->send();
		return;
	}

	function ajaxLikes( $contentId = null, $status = null, $likesId = null)
	{
		$my			= JFactory::getUser();
		$ajax		= new Disjax();
		$jsLink 	= '';
		$config		= DiscussHelper::getConfig();
		$post       = DiscussHelper::getTable( 'Post' );
		$post->load( $contentId );

		// @rule: Respect like settings
		if( $post->getType() == DISCUSS_QUESTION_TYPE && !$config->get( 'main_likes_discussions' ) )
		{
			return false;
		}

		if( $post->getType() == DISCUSS_REPLY_TYPE && !$config->get( 'main_likes_discussions' ) )
		{
			return false;
		}

		if($status)
		{
			// add likes
			$id	= DiscussHelper::addLikes($contentId, 'post', $my->id);
			$jsLink = "<a href=\"javascript:void(0);\" onclick=\"discuss.post.likes('" . $contentId . "', '0', '" . $id . "');\" class=\"unlike\">" . JText::_('COM_EASYDISCUSS_UNLIKE') . "</a>";
		}
		else
		{
			// remove likes
			DiscussHelper::removeLikes($likesId);
			$jsLink = "<a href=\"javascript:void(0);\" onclick=\"discuss.post.likes('" . $contentId . "', '1', '0');\" class=\"like\">" . JText::_('COM_EASYDISCUSS_LIKES') . "</a>";
		}


		$question	= DiscussHelper::getTable( 'Post' );
		$question->load( $post->parent_id );

		// @rule: Jomsocial activity integrations
		if( $post->published == DISCUSS_ID_PUBLISHED && !$post->parent_id && $status )
		{
			DiscussHelper::getHelper( 'jomsocial' )->addActivityLikes( $post );
		}

		// @rule: Add badge for the user when they like a question
		if( $post->getType() == DISCUSS_QUESTION_TYPE && $status && $my->id != $post->user_id )
		{
			// Add logging for user.
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.like.discussion' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_LIKE_DISCUSSION' , $post->title ) );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.like.discussion' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.like.discussion' , $my->id );
		}
		else
		{
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.unlike.discussion' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unlike.discussion' , $my->id );
		}


		// @rule: Add badge for the user when they like a reply
		if( $post->getType() == DISCUSS_REPLY_TYPE && $status && $my->id != $post->user_id )
		{
			// Add logging for user.
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.like.reply' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_LIKE_REPLY' , $post->title ) );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.like.reply' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.like.reply' , $my->id );
		}
		else
		{
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.unlike.reply' , $my->id );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.unlike.reply' , $my->id );
		}

		// @rule: Add notifications for the thread starter
		if( $post->get( 'user_id') != $my->id )
		{
			$notification	= DiscussHelper::getTable( 'Notifications' );

			$text			= $post->getType() == DISCUSS_QUESTION_TYPE ? 'COM_EASYDISCUSS_LIKE_DISCUSSION_NOTIFICATION_TITLE' : 'COM_EASYDISCUSS_LIKE_REPLY_NOTIFICATION_TITLE';
			$title			= $post->title;
			$likeType		= $post->getType() == DISCUSS_QUESTION_TYPE ? DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION : DISCUSS_NOTIFICATIONS_LIKES_REPLIES;
			$id				= $post->get( 'id' );

			if( $post->getType() == DISCUSS_REPLY_TYPE )
			{
				$parent		= DiscussHelper::getTable( 'Post' );
				$parent->load( $post->parent_id );
				$title		= $parent->title;
				$id			= $parent->get( 'id' );
			}

			$notification->bind( array(
					'title'	=> JText::sprintf( $text , $title ),
					'cid'	=> $id,
					'type'	=> $likeType,
					'target'	=> $post->get( 'user_id' ),
					'author'	=> $my->id,
					'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $id
				) );
			$notification->store();
		}

		//now reformat the likes authors
		$authors	= DiscussHelper::getLikesAuthors('post', $contentId, $my->id );

		$ajax->assign('likes-button-'.$contentId, $jsLink);
		$ajax->assign('likes-container-'.$contentId, $authors);

		// If someone liked this item, we need to remove the empty-likes container since the item is being liked now.
		if( $status )
		{
			$ajax->script( 'Foundry("#likes-container-' . $contentId . '").removeClass("empty-likes");');
		}
		else
		{
			$ajax->script( 'Foundry("#likes-container-' . $contentId . '").addClass("empty-likes");');
		}
		$ajax->send();
		return;
	}

	function ajaxFeatured( $contentId = null, $status = null )
	{
		$ajax		= new Disjax();
		$my			= JFactory::getUser();

		if(empty($contentId))
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		if ( !DiscussHelper::isSiteAdmin() )
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION');
			$ajax->dialog( $options );
			$ajax->send();
			return;
		}

		$post	= DiscussHelper::getTable( 'Post' );
		$post->load( $contentId );
		$post->featured = $status;
		$post->store();

		$theme	= new DiscussThemes();
		$theme->set( 'contentId'	, $contentId );

		if( $status )
		{
			$content	= $theme->fetch( 'ajax.feature.php' );
			$ajax->script('Foundry("#dc_admin .sticky").hide();');
			$ajax->script('Foundry("#dc_admin .unsticky").show();');
		}
		else
		{
			$content	= $theme->fetch( 'ajax.unfeature.php' );
			$ajax->script('Foundry("#dc_admin .unsticky").hide();');
			$ajax->script('Foundry("#dc_meta .featured").hide();');
			$ajax->script('Foundry("#dc_admin .sticky").show();');
		}

		// @rule: Add notifications for the thread starter
		if( $post->get( 'user_id') != $my->id )
		{
			$notification	= DiscussHelper::getTable( 'Notifications' );
			$notification->bind( array(
					'title'	=> JText::sprintf( 'COM_EASYDISCUSS_FEATURED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
					'cid'	=> $post->get( 'id' ),
					'type'	=> DISCUSS_NOTIFICATIONS_FEATURED,
					'target'	=> $post->get( 'user_id' ),
					'author'	=> $my->id,
					'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
				) );
			$notification->store();
		}

		$options = new stdClass();
		$options->content = $content;

		$ajax->dialog( $options );

		$ajax->send();
		return;
	}

	function ajaxSubmitComment( $data = null )
	{
		$config 	= DiscussHelper::getConfig();
		$ajax		= new Disjax();
		$post		= DiscussStringHelper::ajaxPostToArray($data);

		$my         = JFactory::getUser();

		if($my->id == 0)
		{
			$ajax->assign( 'comment-err-msg .msg_in' , JText::_('COM_EASYDISCUSS_COMMENTS_NOT_ALLOWED') );
			$ajax->script( 'Foundry( "#comment-err-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->script('discuss.spinner.hide("discussSubmitWait")');
			$ajax->send();
			return;
		}

		if(empty($post['post_id']))
		{
			$ajax->assign( 'comment-err-msg .msg_in' , JText::_('COM_EASYDISCUSS_COMMNETS_INVALID_ID') );
			$ajax->script( 'Foundry( "#comment-err-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->script('discuss.spinner.hide("discussSubmitWait")');
			$ajax->send();
			return;
		}


		$post['user_id']    = $my->id;
		$post['name']    	= $my->name;
		$post['email']    	= $my->email;

		array_walk($post, array($this, '_trim') );

		if(! $this->_validateCommentFields($post))
		{
			$ajax->assign( 'comment-err-msg .msg_in' , $this->err[0] );
			$ajax->script( 'Foundry( "#comment-err-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->script('discuss.spinner.hide("discussSubmitWait")');
			$ajax->send();
			return;
		}

		//if akismet enabled, then we send to akismet for validation.
		if( $config->get( 'antispam_akismet' ) && ( $config->get('antispam_akismet_key') ) )
		{
			require_once( DISCUSS_CLASSES . DS . 'akismet.php' );

			$data = array(
					'author'    => $my->name,
					'email'     => $my->email,
					'website'   => JURI::root() ,
					'body'      => $post['comment'] ,
					'alias' => ''
				);

			$akismet = new Akismet( JURI::root() , $config->get( 'antispam_akismet_key' ) , $data );

			if( !$akismet->errorsExist() )
			{
				if( $akismet->isSpam() )
				{
					$ajax->assign( 'comment-err-msg .msg_in' , JText::_('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED') );
					$ajax->script( 'Foundry( "#comment-err-msg .msg_in" ).addClass( "dc_error" );' );
					$ajax->script('discuss.spinner.hide("discussSubmitWait")');
					$ajax->send();
					return;
				}
			}
		}

		$commentTbl = DiscussHelper::getTable( 'Comment' );
		$commentTbl->bind($post, true);

		if($commentTbl->store())
		{
			//get post duration so far.
			$durationObj    = new stdClass();
			$durationObj->daydiff   = '0';
			$durationObj->timediff  = '00:00:01';

			$commentTbl->duration  = DiscussHelper::getDurationString($durationObj);

			//load porfile info and auto save into table if user is not already exist in discuss's user table.
			$creator = DiscussHelper::getTable( 'Profile' );
			$creator->load( $my->id);

			$commentTbl->creator = $creator;

			// @rule: AUP integrations
			if( $commentTbl->published )
			{
				DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_COMMENT , $commentTbl->user_id , '' );
			}


			$reply		= DiscussHelper::getTable( 'Post' );
			$reply->load( $commentTbl->post_id );

			$question	= DiscussHelper::getTable( 'Post' );
			$question->load( $reply->parent_id );

			// @task: Only give badge when the user comment's on another user's reply
			if( $my->id != $reply->user_id )
			{
				// Add logging for user.
				DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.new.comment' , $my->id , JText::_( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_COMMENT') );

				DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.comment' , $my->id );
				DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.comment' , $my->id );
			}


			// apply bad word filtering
			$commentTbl->comment = DiscussHelper::wordFilter( $commentTbl->comment );


			// @rule: Send comment notifications
			$creator 	= DiscussHelper::getTable( 'Profile' );
			$creator->load( $my->id );

			$emailData                  = array();
			$emailData['commentContent']		= $commentTbl->comment;
			$emailData['commentAuthor']			= $creator->getName();
			$emailData['commentAuthorAvatar'] 	= $creator->getAvatar();
			$emailData['postTitle']				= $question->title;
			$emailData['postLink']				= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $question->id, false, true);

			$emails		= array();

			// @rule: Get the main reply's email and we don't want to send notifications to the user who has actually posted the reply.
			if( $reply->user_id != 0 && $reply->user_id != $my->id )
			{
				$replier	= DiscussHelper::getTable( 'Profile' );
				$replier->load( $reply->user_id );
				$emails[]	= $replier->user->email;
			}

			// @rule: Get all user emails from the list of commentor's
			$comments	= $reply->getComments();

			if( $comments )
			{
				foreach( $comments as $comment )
				{
					if( $comment->user_id != 0 && $comment->user_id != $my->id )
					{
						$commentor	= DiscussHelper::getTable( 'Profile' );
						$commentor->load( $comment->user_id );

						$emails[]	= $commentor->user->email;
					}
				}
			}


			if( !empty( $emails ) )
			{
				$notify		= DiscussHelper::getNotification();
				$notify->addQueue( $emails, JText::sprintf( 'COM_EASYDISCUSS_EMAIL_TITLE_NEW_COMMENT' , JString::substr($question->content, 0, 15) ) . '...' , '', 'email.post.comment.new.php', $emailData);
			}

			// @rule: Add notifications for the thread starter
			if( $reply->get( 'user_id') != $my->id && $commentTbl->published && $config->get( 'main_notifications_comments' ) )
			{
				$notification	= DiscussHelper::getTable( 'Notifications' );
				$notification->bind( array(
						'title'		=> JText::sprintf( 'COM_EASYDISCUSS_COMMENT_REPLY_NOTIFICATION_TITLE' , $question->title ),
						'cid'		=> $question->get( 'id' ),
						'type'		=> DISCUSS_NOTIFICATIONS_COMMENT,
						'target'	=> $reply->get( 'user_id' ),
						'author'	=> $my->id,
						'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $question->get( 'id' )
					) );
				$notification->store();
			}


			//revert the comment form
			$ajax->script('discuss.comment.cancel()');

			if ( $config->get( 'main_content_trigger_comments' ) )
			{
				$commentTbl->content	= $commentTbl->comment;

				// process content plugins
				DiscussEventsHelper::importPlugin( 'content' );
				DiscussEventsHelper::onContentPrepare('comment', $commentTbl);

				$commentTbl->event = new stdClass();

				$results	= DiscussEventsHelper::onContentBeforeDisplay('comment', $commentTbl);
				$commentTbl->event->beforeDisplayContent	= trim(implode("\n", $results));

				$results	= DiscussEventsHelper::onContentAfterDisplay('comment', $commentTbl);
				$commentTbl->event->afterDisplayContent	= trim(implode("\n", $results));

				$commentTbl->comment	= $commentTbl->content;
			}

			//show the new added comment.
			$tpl	= new DiscussThemes();
			$tpl->set( 'comments'	, array($commentTbl) );
			$tpl->set( 'isAdmin'	, DiscussHelper::isSiteAdmin());

			$htmlContent	= $tpl->fetch('comments.php');

			$ajax->append('comments-wrapper-' . $commentTbl->post_id, $htmlContent);
			$ajax->script('Foundry("#comments-wrapper-' . $commentTbl->post_id.'").show()');
			$ajax->script('effect.highlight("#comment-' . $commentTbl->id . '")');

			$ajax->assign( 'comment-notification-'.$post['post_id'].' .msg_in' , JText::_('COM_EASYDISCUSS_COMMENT_SUCESSFULLY_ADDED') );
			$ajax->script( 'Foundry( "#comment-notification-'.$post['post_id'].' .msg_in" ).addClass( "dc_success" );' );
			//$ajax->script( 'discuss.spinner.hide( "reply_edit_loading" );');
		}
		else
		{
			$ajax->script('Foundry("#comment-err-msg .msg_in").addClass("dc_error")');
			$ajax->assign('comment-err-msg .msg_in', JText::_('COM_EASYDISCUSS_COMMENT_SAVE_FAILED'));
		}

		$ajax->script('discuss.spinner.hide("discussSubmitWait")');
		$ajax->send();
		return;
	}

	function ajaxCommentDelete( $commentId = null )
	{
		$djax		= new Disjax();


		$isSiteAdmin	= DiscussHelper::isSiteAdmin();

		if(! $isSiteAdmin)
		{
			$options = new stdClass();
			$options->content = JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_DELETE_COMMENT');
			$djax->dialog( $options );
			$djax->send();
			return;
		}

		$commentTbl = DiscussHelper::getTable( 'Comment' );
		$commentTbl->load($commentId);

		if($commentTbl->delete())
		{
			// @rule: AUP integrations
			DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_COMMENT , $commentTbl->user_id , '' );

			$djax->assign('comment-' . $commentId, '<span class="article_separator">&nbsp;</span>' . JText::_('COM_EASYDISCUSS_COMMENT_SUCESSFULLY_DELETED'));
			$djax->script('discuss.comment.removeEntry(\'' . $commentId . '\');');
		}
		else
		{
			$options = new stdClass();
			$options->content = JText::sprintf('COM_EASYDISCUSS_FAILED_TO_DELETE_COMMENT', $commentTbl->getError());
			$djax->dialog( $options );
		}

		$djax->send();
		return;
	}

	function ajaxShowTnc()
	{
		$config = DiscussHelper::getConfig();

		$disjax  = new Disjax();

		$themes		= new DiscussThemes();
		$content	= $themes->fetch( 'ajax.terms.php' );

		$options = new stdClass();
		$options->content = $content;

		$disjax->dialog( $options );
		$disjax->send();
		return;
	}

	function reportForm( $id = null )
	{
		$config = DiscussHelper::getConfig();
		$my     = JFactory::getUser();
		$disjax	= new Disjax();

		$template	= new DiscussThemes();
		$template->set( 'postId' , $id );
		$html		= $template->fetch( 'ajax.report.php' );

		$options = new stdClass();
		$options->content = $html;

		$disjax->dialog($options);

		$disjax->send();
	}

	function ajaxSubmitReport( $data = null )
	{
		$ajax	= new Disjax();
		$config = DiscussHelper::getConfig();
		$my     = JFactory::getUser();

		if($my->id == 0)
		{
			$ajax->assign( 'reports-msg .msg_in' , JText::_('COM_EASYDISCUSS_YOU_DO_NOT_HAVE_PERMISION_TO_SUBMIT_REPORT') );
			$ajax->script( 'Foundry( "#reports-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		if(! $config->get('main_report', 0))
		{
			$ajax->assign( 'reports-msg .msg_in' , JText::_('COM_EASYDISCUSS_REPORT_HAS_BEEN_DISABLED_BY_ADMINISTRATOR') );
			$ajax->script( 'Foundry( "#reports-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$post		= DiscussStringHelper::ajaxPostToArray($data);

		if(empty($post['report_post_id']))
		{
			$ajax->assign( 'reports-msg .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
			$ajax->script( 'Foundry( "#reports-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		if(empty($post['reporttext']))
		{
			$ajax->assign( 'reports-msg .msg_in' , JText::_('COM_EASYDISCUSS_REPORT_EMPTY_TEXT') );
			$ajax->script( 'Foundry( "#reports-msg .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$post['post_id'] = $post['report_post_id'];
		unset($post['report_post_id']);

		$post['created_by'] = $my->id;

		$reportTbl = DiscussHelper::getTable( 'Report' );
		$reportTbl->bind($post, true);

		if($reportTbl->store())
		{
			//mark the post as reported.
			$reportTbl->markPostReport();

			$reportThreshold    = $config->get('main_reportthreshold', 15);
			$isMarked      		= false;

			// Now we need to check the number of report for this post.
			$numOfReports   = $reportTbl->getReportCount();
			if($numOfReports >= $reportThreshold)
			{
				//check if post rech the report threshold. If yes, then we report to admin.

				// prepare email data
				$postTbl = DiscussHelper::getTable( 'Post' );
				$postTbl->load($reportTbl->post_id);

				$creator	= DiscussHelper::getTable('Profile' );
				$creator->load( $postTbl->user_id );

				$date       = new JDate($postTbl->created);

				$emailData                  = array();
				$emailData['postContent']	= $postTbl->content;
				$emailData['postAuthor']	= $creator->getName();
				$emailData['postAuthorAvatar'] = $creator->getAvatar();
				$emailData['postDate']		= $date->toFormat();
				$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $postTbl->id, false, true);

				if(! empty($postTbl->parent_id))
				{
					$parentTbl = DiscussHelper::getTable( 'Post' );
					$parentTbl->load($postTbl->parent_id);

					$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $parentTbl->id, false, true);
				}

				$noti   = DiscussHelper::getNotification();
				$noti->addQueue('', 'admin', JText::sprintf('COM_EASYDISCUSS_REPORT_REQUIRED_YOUR_ATTENTION', JString::substr($postTbl->content, 0, 15) ) . '...', '', 'email.post.attention.php', $emailData);

				$isMarked  = true;
			}

			$msg    = JText::_('COM_EASYDISCUSS_REPORT_SUBMITTED');

			if($isMarked)
			{
				$msg    = JText::_('COM_EASYDISCUSS_REPORT_SUBMITTED_BUT_POST_MARKED_AS_REPORT');
			}

			//$ajax->assign( 'reports-msg-'.$reportTbl->post_id.' .msg_in' , $msg );
			//$ajax->script( 'Foundry( "#reports-msg-'.$reportTbl->post_id.' .msg_in" ).addClass( "dc_success" );' );
			$ajax->assign( 'reports-msg .msg_in' , $msg );
			// $ajax->script( 'Foundry( "#reports-msg .msg_in" ).addClass( "dc_success" );' );
			$ajax->script( 'Foundry( ".dialog-buttons #discuss-submit").hide();' );

			$ajax->script( 'Foundry("#report-form").hide();');
			$ajax->script( 'Foundry(".dialog-buttons #edialog-cancel").hide();');
			$ajax->script( 'Foundry( "#edialog-close" ).show()');
		}
		else
		{
			$ajax->assign( 'reports-msg .msg_in' , JText::sprintf('COM_EASYDISCUSS_FAILED_TO_SUBMIT_REPORT', $reportTbl->getError()) );
			$ajax->script( 'Foundry( "#reports-msg .msg_in" ).addClass( "dc_error" );' );

// 			$ajax->assign( 'reports-msg-'.$reportTbl->post_id.' .msg_in' , JText::sprintf('FAILED TO SUBMIT REPORT', $reportTbl->getError()) );
// 			$ajax->script( 'Foundry( "#reports-msg-'.$reportTbl->post_id.' .msg_in" ).addClass( "dc_error" );' );
		}

		$ajax->send();
		return;
	}

	function _fieldValidate($post = null)
	{

		$mainframe	= JFactory::getApplication();
		$valid		= true;

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

		$tags			= '';
		if(! isset($post['tags[]']))
		{
			$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMPTY_TAG') . '</li>';
			$valid	= false;
		}
		else
		{
			$tags			= $post['tags[]'];
			if(empty($tags))
			{
				$message    .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMPTY_TAG') . '</li>';
				$valid	= false;
			}
		}

		$message    .= '</ul>';

		$returnVal  = array();

		$returnVal[]    = $valid;
		$returnVal[]    = $message;

		return $returnVal;
	}


	function _validateCommentFields($post = null)
	{
		$config = DiscussHelper::getConfig();

		if(JString::strlen($post['comment']) == 0)
		{
			$this->err[0]	= JText::_( 'COM_EASYDISCUSS_COMMENT_IS_EMPTY' );
			$this->err[1]	= 'comment';
			return false;
		}

		if($config->get('main_comment_tnc') == true)
		{
			if(empty($post['tnc']))
			{
				$this->err[0]	= JText::_( 'COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT' );
				$this->err[1]	= 'tnc';
				return false;
			}
		}

		return true;
	}

	function _trim(&$text = null)
	{
		$text = JString::trim($text);
	}

	function ajaxSubscribe($id = null)
	{
		$disjax		= new disjax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$sitename	= $mainframe->getCfg('sitename');

		$tpl	= new DiscussThemes();
		$tpl->set( 'id', $id );
		$tpl->set( 'my', $my );
		$content	= $tpl->fetch( 'ajax.subscribe.post.php' );

		$options = new stdClass();
		$options->content = $content;

		$disjax->dialog($options);

		$disjax->send();
	}

	function ajaxAddSubscription($type = 'post', $email = null, $name = null, $interval = null, $cid = '0')
	{
		$disjax		= new Disjax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$config 	= DiscussHelper::getConfig();
		$msg		= '';
		$msgClass	= 'dc_success';

		$JFilter	= JFilterInput::getInstance();
		$name		= $JFilter->clean($name, 'STRING');

		jimport( 'joomla.mail.helper' );

		if( !JMailHelper::isEmailAddress($email) )
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_INVALID_EMAIL') );
			$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		$subscription_info = array();
		$subscription_info['type'] = $type;
		$subscription_info['userid'] = $my->id;
		$subscription_info['email'] = $email;
		$subscription_info['cid'] = $cid;
		$subscription_info['member'] = ($my->id)? '1':'0';
		$subscription_info['name'] = ($my->id)? $my->name : $name;
		$subscription_info['interval'] = $interval;

		//validation
		if(JString::trim($subscription_info['email']) == '')
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_EMAIL_IS_EMPTY') );
			$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		if(JString::trim($subscription_info['name']) == '')
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_NAME_IS_EMPTY') );
			$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		$model	= $this->getModel( 'Subscribe' );
		$sid    = '';


		if($my->id == 0)
		{
			$sid = $model->isPostSubscribedEmail($subscription_info);
			if($sid != '')
			{
				//user found.
				// show message.
				$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
				$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_ALREADY_SUBSCRIBED_TO_POST') );
				$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_alert" );' );
				$disjax->send();
				return;

			}
			else
			{
				if(!$model->addSubscription($subscription_info))
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
					$msgClass = 'dc_error';
				}
			}
		}
		else
		{
			$sid = $model->isPostSubscribedUser($subscription_info);

			if($sid['id'] != '')
			{
				// user found.
				// update the email address
				if(!$model->updatePostSubscription($sid['id'], $subscription_info))
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
					$msgClass = 'dc_error';
				}
			}
			else
			{
				//add new subscription.
				if(!$model->addSubscription($subscription_info))
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
					$msgClass = 'dc_error';
				}
			}
		}

		$msg = empty($msg)? JText::_('COM_EASYDISCUSS_SUBSCRIPTION_SUCCESS') : $msg;

		$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
		$disjax->assign( 'dc_subscribe_notification .msg_in' , $msg );
		$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
		$disjax->script( 'Foundry( ".dialog-buttons .si_btn" ).hide();' );
		$disjax->send();
		return;
	}

	function getMoreVoters($postid = null, $limit = null)
	{
		$disjax		= new disjax();

		$voteModel	= $this->getModel('votes');
		$total 		= $voteModel->getTotalVoteCount( $postid );

		if(!empty($total))
		{
			$voters	= DiscussHelper::getVoters($postid, $limit);
			$msg	= JText::sprintf('COM_EASYDISCUSS_VOTES_BY', $voters->voters);

			if($voters->shownVoterCount < $total)
			{
				$limit += '5';

				$msg .= '[<a href="javascript:void(0);" onclick="disjax.load(\'post\', \'getMoreVoters\', \''.$postid.'\', \''.$limit.'\');">'.JText::_('COM_EASYDISCUSS_MORE').'</a>]';
			}

			$disjax->assign( 'dc_reply_voters_'.$postid , $msg );
		}

		$disjax->send();
		return;
	}

	function deleteAttachment( $id = null )
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'controllers' . DS . 'attachment.php');

		$disjax		= new Disjax();

		$controller	= new EasyDiscussControllerAttachment();

		$msg		= JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_FAILED');
		$msgClass	= 'dc_error';
		if($controller->deleteFile($id))
		{
			$msg		= JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_SUCCESS');
			$msgClass	= 'dc_success';
			$disjax->script( 'Foundry( "#dc-attachments-'.$id.'" ).remove();' );
		}

		$disjax->assign( 'dc_post_notification .msg_in' , $msg );
		$disjax->script( 'Foundry( "#dc_post_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
		$disjax->script( 'Foundry( "#button-delete-att-'.$id.'" ).attr("disabled", "");' );

		$disjax->send();
	}

	function acceptReply( $id = null )
	{
		$ajax		= new Disjax();

		if(empty( $id ))
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$config     = DiscussHelper::getConfig();
		$acl		= DiscussHelper::getHelper( 'ACL' );

		$reply  = DiscussHelper::getTable( 'Post' );
		$reply->load( $id );

		$parentPost = DiscussHelper::getTable( 'Post' );
		$parentPost->load( $reply->parent_id );

		$isMine		= DiscussHelper::isMine( $parentPost->user_id );
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin && !$acl->allowed( 'mark_answered', '0') )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$reply->answered    = '1';
		$reply->store();

		$parentPost = DiscussHelper::getTable( 'Post' );
		$parentPost->load( $reply->parent_id );
		$parentPost->isresolve = DISCUSS_ENTRY_RESOLVED;
		$parentPost->store();

		if( $config->get( 'notify_owner_answer' ) )
		{
			$replyUser 					= DiscussHelper::getTable( 'Profile' );
			$replyUser->load( $reply->user_id );

			$emailData					= array();
			$emailData['postTitle']		= $parentPost->title;
			$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $parentPost->id, false, true);
			$emailData['replyAuthor']	= $replyUser->getName();
			$emailData['replyAuthorAvatar'] = $replyUser->getAvatar();
			$emailData['replyContent']	= Parser::bbcode( $reply->content );

			//now send notification.
			$notify	= DiscussHelper::getNotification();

			// prepare email content and information.
			$emailSubject   = JText::sprintf('COM_EASYDISCUSS_REPLY_NOW_ACCEPTED', $parentPost->title );
			$emailTemplate  = 'email.reply.answered.php';

			//get owner email.
			$email  = '';
			if( !empty( $parentPost->user_id ) )
			{
				$ownerUser  = JFactory::getUser( $parentPost->user_id );
				$email      = $ownerUser->email;
			}

			if( !empty($email) )
			{
				$notify->addQueue( $email, $emailSubject, '', $emailTemplate, $emailData);
			}
		}

		$my		= JFactory::getUser();

		if( $reply->get( 'user_id') != $my->id )
		{
			// @rule: Add badges
			DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.answer.reply' , $reply->get( 'user_id' ) , JText::sprintf( 'COM_EASYDISCUSS_HISTORY_ACCEPTED_REPLY' , $parentPost->title ) );

			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.answer.reply' , $reply->get( 'user_id' ) );
			DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.answer.reply' , $reply->get( 'user_id' ) );

			// @rule: Add notifications for the thread starter
			$notification	= DiscussHelper::getTable( 'Notifications' );
			$notification->bind( array(
					'title'	=> JText::sprintf( 'COM_EASYDISCUSS_ACCEPT_ANSWER_DISCUSSION_NOTIFICATION_TITLE' , $parentPost->title ),
					'cid'	=> $parentPost->get( 'id' ),
					'type'	=> DISCUSS_NOTIFICATIONS_ACCEPTED,
					'target'	=> $reply->get( 'user_id' ),
					'author'	=> $my->id,
					'permalink'	=> 'index.php?option=com_easydiscuss&view=post&id=' . $parentPost->get( 'id' ) . '#answer'
				) );
			$notification->store();
		}

		$ajax->assign( 'reply-notification-'.$id.' .msg_in' , JText::_('COM_EASYDISCUSS_REPLY_NOW_ACCEPTED_AND_RESOLVED') );
		$ajax->script( 'Foundry( "#reply-notification-'.$id.' .msg_in" ).addClass( "dc_success" );' );
		$ajax->script('Foundry("a.discuss-accept").hide();');
		$ajax->script( 'Foundry( "#title_' . $parentPost->id . '" ).append( "<span class=\"dc_ico resolved\">' . JText::_( 'COM_EASYDISCUSS_RESOLVED' ) . '</span>");' );
		$ajax->send();
		return;
	}

	function rejectReply( $id = null )
	{
		$ajax		= new Disjax();

		if(empty( $id ))
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$config		= DiscussHelper::getConfig();
		$acl		= DiscussHelper::getHelper( 'ACL' );

		$reply		= DiscussHelper::getTable( 'Post' );
		$reply->load( $id );

		$parentPost	= DiscussHelper::getTable( 'Post' );
		$parentPost->load( $reply->parent_id );

		$isMine		= DiscussHelper::isMine( $parentPost->user_id );
		$isAdmin	= DiscussHelper::isSiteAdmin();

		if ( !$isMine && !$isAdmin && !$acl->allowed( 'mark_answered', '0') )
		{
			$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$ajax->script( 'Foundry( "#dc_main_notifications .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$reply->answered    = '0';
		$reply->store();

		$parentPost = DiscussHelper::getTable( 'Post' );
		$parentPost->load( $reply->parent_id );
		$parentPost->isresolve = DISCUSS_ENTRY_UNRESOLVED;
		$parentPost->store();

		if( $config->get( 'notify_owner_answer' ) )
		{
			//now send notification.
			$notify	= DiscussHelper::getNotification();

			// prepare email content and information.
			$emailSubject   = JText::sprintf('COM_EASYDISCUSS_REPLY_NOW_UNACCEPTED', $parentPost->title);
			$emailTemplate  = 'email.reply.unanswered.php';

			$emailData					= array();
			$emailData['postTitle']		= $parentPost->title;
			$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $parentPost->id, false, true);

			//get owner email.
			$email  = '';
			if( !empty( $parentPost->user_id ) )
			{
				$ownerUser  = JFactory::getUser( $parentPost->user_id );
				$email      = $ownerUser->email;
			}

			if( !empty($email) )
			{
				$notify->addQueue( $email, $emailSubject, '', $emailTemplate, $emailData);
			}
		}


		$ajax->assign( 'reply-notification-'.$id.' .msg_in' , JText::_('COM_EASYDISCUSS_REPLY_NOW_UNACCEPTED_AND_UNRESOLVED') );
		$ajax->script( 'Foundry( "#reply-notification-'.$id.' .msg_in" ).addClass( "dc_success" );' );
		$ajax->script('Foundry("a.discuss-reject").hide();');
		$ajax->script( 'Foundry( "#title_' . $parentPost->id . ' span.resolved" ).remove();' );

		$ajax->send();
		return;
	}

	public function nameSuggest( $part )
	{
		$ajax		= DiscussHelper::getHelper( 'Ajax' );
		$db 		= JFactory::getDBO();
		$config		= DiscussHelper::getConfig();
		$property 	= $config->get( 'layout_nameformat' );

		$query		= 'SELECT a.`id`,a.`' . $property . '` AS title FROM '
					. $db->nameQuote( '#__users' ) . ' AS a '
					. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_users' ) . ' AS b '
					. 'ON a.`id`=b.`id`';

		if( $property == 'nickname' )
		{
			$query	.= ' WHERE b.' . $db->nameQuote( $property ) . ' LIKE ' . $db->Quote( '%' . $part . '%' );
		}
		else
		{
			$query	.= ' WHERE a.' . $db->nameQuote( $property ) . ' LIKE ' . $db->Quote( '%' . $part . '%' );
		}

		$db->setQuery( $query );
		$names 		= $db->loadObjectList();

		require_once( DISCUSS_CLASSES . DS. 'json.php' );
		$json		= new Services_JSON();
		$ajax->success( $json->encode( $names ) );
		$ajax->send();
	}
}
