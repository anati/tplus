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

class EasyDiscussViewPolls extends EasyDiscussView
{
	public function vote( $id )
	{
		$my				= JFactory::getUser();
		$ajax			= new Disjax();
		$config			= DiscussHelper::getConfig();
		$poll			= DiscussHelper::getTable( 'Poll' );
		$poll->load( (int) $id );

		if( !$config->get( 'main_polls') || !$poll->id || $my->id <= 0 )
		{
			echo JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' );
			exit;
		}

		// @task: Test if user has voted before. If they have already voted on this item before, we need to update the counts.
		if( $poll->hasVotedPoll( $my->id ) )
		{
			// Remove existing vote
			$poll->removeExistingVote( $my->id , $poll->get( 'post_id' ) );
		}

		// @task: Add a new vote now
		$pollUser	= DiscussHelper::getTable( 'PollUser' );
		$pollUser->set( 'poll_id'	, $id );
		$pollUser->set( 'user_id'	, $my->id );
		$pollUser->store();

		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $poll->get( 'post_id') );
		$post->updatePollsCount();

		$polls	= $post->getPolls();

		// Update all the counts
		foreach( $polls as $pollItem )
		{
			$string		= '<a href="javascript:void(0);" onclick="discuss.polls.showVoters(\'' . $pollItem->id . '\');" id="poll-count-' . $pollItem->id . '">';
			$string		.= DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_POLLS_VOTE' , $pollItem->get( 'count' ) , true );
			$string		.= '</a>';

			$ajax->assign( 'poll-count-' . $pollItem->get( 'id' ) , $string );

			$theme		= new DiscussThemes();
			$theme->set( 'voters' 		, $pollItem->getVoters() );
			$theme->set( 'percentage'	, $pollItem->getPercentage() );
			$html 		= $theme->fetch( 'poll.voters.php' );

			$ajax->assign( 'poll-voters-' . $pollItem->get( 'id' ) , $html );
		}

		// Assign the unvote poll link
		$theme	= new DiscussThemes();
		$theme->set( 'poll' , $poll );
		$theme->set( 'post'	, $post );
		$unvote		= $theme->fetch( 'poll.unvote.php' );

		$ajax->script( 'Foundry("#poll-unvote").remove();' );
		$ajax->after( 'discuss-poll' , $unvote );

		$ajax->send();
	}

	public function unvote( $postId )
	{
		$my				= JFactory::getUser();
		$ajax			= new Disjax();
		$config			= DiscussHelper::getConfig();

		if( !$config->get( 'main_polls') || !$postId )
		{
			echo JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' );
			exit;
		}

		$post		= DiscussHelper::getTable( 'Post' );
		$post->load( $postId );

		// @task: Test if user has voted before. If they have not voted, we shouldn't allow them in here
		if( !$post->hasVotedPoll( $my->id  ) )
		{
			echo JText::_( 'COM_EASYDISCUSS_NOT_ALLOWED' );
			exit;
		}
		// @task: Remove user's vote
		$post->removePollVote( $my->id );

		// @task: Update the views.
		$polls	= $post->getPolls();

		// Update all the counts
		foreach( $polls as $poll )
		{
			$string		= DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_POLLS_VOTE' , $poll->get( 'count' ) , true );
			$ajax->assign( 'poll-count-' . $poll->get( 'id' ) , $string );

			$theme		= new DiscussThemes();
			$theme->set( 'voters' , $poll->getVoters() );
			$theme->set( 'percentage'	, $poll->getPercentage() );
			$html 		= $theme->fetch( 'poll.voters.php' );

			$ajax->assign( 'poll-voters-' . $poll->get( 'id' ) , $html );
			$ajax->script( 'Foundry("#poll-unvote").hide();' );
		}
		$ajax->send();
	}

	public function getVoters( $pollId )
	{
		$ajax 	= new Disjax();

		$poll	= DiscussHelper::getTable( 'Poll' );
		$poll->load( $pollId );

		$voters		= $poll->getVoters();

		$template	= new DiscussThemes();
		$template->set( 'voters' , $voters );

		$option				= new stdClass();
		$option->content	= $template->fetch( 'ajax.poll.voters.php' );

		$ajax->dialog( $option );

		$ajax->send();
	}
}
