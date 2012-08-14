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

require_once( DISCUSS_HELPERS . DS . 'date.php' );
require_once( DISCUSS_HELPERS . DS . 'input.php' );

class DiscussPost extends JTable
{
	var $id 				= null;
	var $title				= null;
	var $modified			= null;
	var $created			= null;
	var $replied			= null;
	var $alias				= null;
	var $content			= null;
	var $published			= null;
	var $ordering			= null;
	var $vote				= null;
	var $islock				= null;
	var $featured			= null;
	var $isresolve			= null;
	var $hits				= null;
	var $user_id			= null;
	var $category_id		= null;
	var $parent_id			= null;
	var $user_type			= null;
	var $poster_name		= null;
	var $poster_email		= null;
	var $num_likes			= null;
	var $num_negvote		= null;
	var $sum_totalvote		= null;
	var $params             = null;
	var $answered           = null;
	var $password			= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__discuss_posts' , 'id' , $db );
	}


	public function load( $key = null , $alias = false )
	{
		if( !$alias )
		{
			return parent::load( $key );
		}

		$db		= $this->getDBO();

		$query	= 'SELECT id FROM ' . $this->_tbl . ' '
				. 'WHERE ' . $db->nameQuote('alias') . ' = ' . $db->Quote( $key );
		$db->setQuery( $query );

		$id		= $db->loadResult();

		// Try replacing ':' to '-' since Joomla replaces it
		if( !$id )
		{
			$query	= 'SELECT id FROM ' . $this->_tbl . ' '
					. 'WHERE ' . $db->nameQuote('alias') . ' = ' . $db->Quote( JString::str_ireplace( ':' , '-' , $key ) );
			$db->setQuery( $query );

			$id		= $db->loadResult();
		}
		return parent::load( $db->loadResult() );
	}


    /**
     * Must only be bind when using POST data
     **/
    function bind( $data , $post = false )
    {
    	parent::bind( $data );

    	if( $post )
    	{
	    	$my			= JFactory::getUser();

	    	// Some properties needs to be overriden.
	    	//$content	= $data['content'];

			if ( $this->id == 0 )
			{
			    // this is to check if superadmin assign blog author during blog creation.
			    if(empty($this->user_id))
					$this->user_id	= $my->id;
			}

			$created_date   = '';
			$tzoffset       = DiscussDateHelper::getOffSet();

			//default joomla date obj
			$date		= JFactory::getDate();
			$now		= $date->toMySQL();
			$config             = DiscussHelper::getConfig();
			$allowedTags    	= explode( ',' , $config->get( 'main_allowed_tags' ) );
			$allowedAttributes  = explode( ',' , $config->get( 'main_allowed_attr' ) );
			$input      		= JFilterInput::getInstance( $allowedTags , $allowedAttributes );

// 			$this->title		= $input->clean($data['title']);
// 			$this->content		= $input->clean($data['dc_reply_content']);

			$this->title        = $input->clean( $data[ 'title'] );
			$this->content      = $data[ 'dc_reply_content' ];
			$this->created 		= !empty( $this->created ) ? $this->created : $now;
			$this->replied 		= !empty( $this->replied ) ? $this->replied : $now;
			$this->modified		= $now;

			//default values to 0
			$this->num_likes		= 0;
			$this->num_negvote		= 0;
			$this->sum_totalvote	= 0;
		}
		return true;
	}


	/**
	 * Method to update parent total replies count and last reply time.
	 */
	function addParentRepliesCount($parentId, $val)
	{
	    $db = JFactory::getDBO();

	    if(empty($parentId))
	        return false;

	    $query  = 'UPDATE `#__discuss_posts` SET `num_replies` = `num_replies` + ' . $db->Quote($val);

		if($val > 0)
			$query  .= ', `replied` = ' . $db->Quote($this->created);

		$query  .= ' WHERE `id` = ' . $db->Quote($parentId);
		$db->setQuery($query);
		$db->query();

		return true;
	}

	function hasVoted( $userId = null )
	{
		$user	 	= JFactory::getUser( $userId );
		$db 		= JFactory::getDBO();

		$query  = 'SELECT `value` FROM `#__discuss_votes` WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($user->id) . ' AND ' . $db->nameQuote('post_id') . ' = ' . $db->Quote( $this->id );

		$db->setQuery($query);
		$result	= $db->loadResult();

		return $result;
	}

	/**
	 * Override parent's behavior as we need to assign badge history when a post is being read.
	 *
	 **/
	public function hit( $pk = null )
	{
		$ip			= JRequest::getVar( 'REMOTE_ADDR' , '' , 'SERVER' );
		$my			= JFactory::getUser();

		if( !empty( $ip ) && !empty($this->id) )
		{
			$token		= md5( $ip . $this->id );

			$session	= JFactory::getSession();
			$exists		= $session->get( $token , false );

			if( $exists )
			{
				return true;
			}

			$session->set( $token , 1 );
		}

		$state	= parent::hit();

		// @task: Assign badge
		if( $this->published == DISCUSS_ID_PUBLISHED && $my->id != $this->user_id )
		{
			DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.read.discussion' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_READ_POST' , $this->title ) );
		}

		return $state;
	}

	function hasLiked( $type, $userId )
	{
	    $db = JFactory::getDBO();

	    $query  = 'SELECT `id` FROM `#__discuss_likes`';
	    $query  .= ' WHERE `type` = ' . $db->Quote($type);
	    $query  .= ' AND `content_id` = ' . $db->Quote( $this->id );
	    $query  .= ' AND `created_by` = ' . $db->Quote($userId);

	    $db->setQuery($query);
	    $result = $db->loadResult();
	    return $result;
	}

	function getComments()
	{
	    $db 	= JFactory::getDBO();

	    $date   = JFactory::getDate();

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', a.`created`) as `noofdays`, ';
		$query	.= ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', a.`created`) as `daydiff`, TIMEDIFF(' . $db->Quote($date->toMySQL()). ', a.`created`) as `timediff`,';
	    $query  .= ' a.* FROM `#__discuss_comments` AS a WHERE a.`post_id` = ' . $db->Quote( $this->id );
	    $query  .= ' ORDER BY a.`created` ASC';
	    $db->setQuery($query);

	    $result = $db->loadObjectList();

	    return $result;
	}

	public function getTotalVotes()
	{
		$db 	= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_votes' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		
		$db->setQuery( $query );
		$votes	= $db->loadResult();
		
		return $votes;
	}

	function getVoters($postid, $limit='5')
	{
		$db 	= JFactory::getDBO();

		$query	= 'SELECT a.`user_id`, b.`name`, b.`username`, c.`nickname` '
				. ' FROM ' . $db->nameQuote('#__discuss_votes') . ' as a '
				. ' INNER JOIN ' . $db->nameQuote('#__users') . ' as b on a.`user_id` = b.`id` '
		  		. ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' as c on a.`user_id` = c.`id` '
		  		. ' WHERE a.`post_id` = ' . $db->Quote($postid) . ' '
		  		. ' ORDER BY a.`created` DESC'
		  		. ' LIMIT 0, ' . $limit;

	    $db->setQuery($query);

	    $result = $db->loadObjectList();

	    return $result;
	}

	public function getTags()
	{
		$db		= JFactory::getDBO();

		// get post tags
		if( !class_exists( 'EasyDiscussModelPostsTags' ) )
		{
			JLoader::import( 'poststags' , DISCUSS_ROOT . DS . 'models' );
		}

		$model	= JModel::getInstance( 'PostsTags' , 'EasyDiscussModel' );

		$tags	= $model->getPostTags( $this->id );

		return $tags;
	}

	public function getLikeAuthors()
	{
		static $html = null;

		if( !$html )
		{
			$my 	= JFactory::getUser();
			$html   = DiscussHelper::getLikesAuthors( 'post' , $this->id, $my->id);
		}

		return $html;
	}

	public function getTitle()
	{
        return $this->title;
	}

	public function getAttachments()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_attachments' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'uid' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $this->getType() ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		$attachments	= array();

		foreach( $result as $row )
		{
			$table	= JTable::getInstance( 'Attachments' , 'Discuss' );
			$table->bind( $row );

			$type = explode("/", $row->mime);
			$table->attachmentType = $type[0];

			$attachments[]	= $table;
		}
		return $attachments;
	}

	/*
	 * Binds specific parameters which can be used by the caller.
	 */
	public function bindParams( $post )
	{
	    $params     = new JParameter( '' );

	    foreach( $post as $key => $value )
	    {
			if( preg_match( '/params\_.*/i' , $key ) )
			{
				if( is_array( $value ) )
				{
					$total  = count( $value );
					$key    = str_ireplace( '[]' , '' , $key );

					for( $i = 0;$i < $total;$i++ )
					{
					    if( !empty( $value[ $i ] ) )
					    {
					    	// Strip off all html tags from the input since we don't want to allow them to embed html codes in the fields.
					    	$value[$i]	= strip_tags( $value[ $i ] );

					    	$params->set( $key . $i , $value[ $i ] );
						}
					}
				}
				else
				{
				    $params->set( $key , $value );
				}
			}
		}

		$this->params   = $params->toString( 'INI' );
	}

	public function bindAttachments()
	{
	    $mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		// @task: Do not allow file attachments if its disabled.
		if( !$config->get( 'attachment_questions' ) )
		{
			return false;
		}

		$allowed    = explode( ',' , $config->get( 'main_attachment_extension' ) );
		$files		= JRequest::getVar( 'filedata' , array() , 'FILES');

		if( empty( $files ) )
		{
			return false;
		}
		
		$total      = count( $files[ 'name' ] );

		// @rule: Handle empty files.
		if( empty( $files['name'][0] ) )
		{
		    $total  = 0;
		}

		if( $total < 1 )
		{
			return false;
		}


		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.utilities.utility' );

		// @rule: Create default media path
		$path   = DISCUSS_MEDIA_PATH . DS . trim( $config->get( 'attachment_path' ) , DS );

		if( !JFolder::exists( $path ) )
		{
			JFolder::create( $path );
			JFile::copy( DISCUSS_ROOT . DS . 'index.html' , $path . DS . 'index.html' );
		}

		$maxSize	= (int) $config->get( 'attachment_maxsize' ) * 1024 * 1024;

		for( $i = 0; $i < $total; $i++ )
		{
			$extension  = JFile::getExt( $files[ 'name' ][ $i ] );

			// Skip empty data's.
			if( !$extension )
			{
			    continue;
			}

			// @rule: Check for allowed extensions
			if( !isset( $extension ) || !in_array( $extension , $allowed ) )
			{
			    $mainframe->enqueueMessage( JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_INVALID_EXTENSION', $files[ 'name' ][ $i ]) , 'error' );
			}
			else
			{
			    $size   = $files[ 'size' ][ $i ];

			    // @rule: File size should not exceed maximum allowed size
				if( !empty( $size ) && $size < $maxSize )
				{
					$name			= JUtility::getHash( $files[ 'name' ][ $i ] . JFactory::getDate()->toMySQL() );
					$attachment		= JTable::getInstance( 'Attachments' , 'Discuss' );

					$attachment->set( 'path'		, $name );
					$attachment->set( 'title'		, $files[ 'name' ][ $i ] );
					$attachment->set( 'uid' 		, $this->id );
					$attachment->set( 'type'		, $this->getType() );
					$attachment->set( 'created'		, JFactory::getDate()->toMySQL() );
					$attachment->set( 'published'	, true );
					$attachment->set( 'mime'		, $files[ 'type' ][ $i ] );
					$attachment->set( 'size'		, $size );

					JFile::copy( $files[ 'tmp_name' ][ $i ] , $path . DS . $name );
					$attachment->store();
				}
				else
				{
				    $mainframe->enqueueMessage( JText::sprintf('COM_EASYDISCUSS_FILE_ATTACHMENTS_INVALID_EXTENSION', $files[ 'name' ][ $i ]) , 'error' );
				}
			}
		}
	}

	/*
	 * Returns the permalink of the current post data.
	 */
	public function getPermalink( $external = false )
	{
	    return DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $this->id );
	}

	/*
	 * Determines whether the current post is pending or not.
	 *
	 * @params  null
	 * @return  boolean     True if pending false otherwise.
	 */
	public function isPending()
	{
	    return $this->published == DISCUSS_ID_PENDING;
	}

	public function getParams( $key )
	{
	    $result     = array();
		$pattern    = '/params_' . $key . '[0-9]=(.*)/i';
		preg_match_all( $pattern , $this->params , $matches );

		if( !empty( $matches[1] ) )
		{
		    foreach( $matches[1] as $match )
		    {
		        $result[]   = $match;
			}
		}
		return $result;
	}

	public function getReferences()
	{
	    $references = array();
		$pattern    = '/params_references[0-9]=(.*)/i';
		preg_match_all( $pattern , $this->params , $matches );

		if( !empty( $matches[1] ) )
		{
		    foreach( $matches[1] as $reference )
		    {
                $reference      = JString::str_ireplace('"', '', $reference);
		        $reference		= JString::stristr( $reference , 'http' ) === false ? 'http://' . $reference : $reference;
				$references[]	= $reference;
			}
		}

		return $references;
	}

	public function clearAccpetedReply()
	{
	    $db		= JFactory::getDBO();

	    $query  = 'UPDATE `#__discuss_posts` set `answered` = ' . $db->Quote( '0' );
	    $query  .= ' WHERE `parent_id` = ' . $db->Quote( $this->id );

	    $db->setQuery( $query );
	    $db->query();
	}

	/*
	 * Returns the type of this post
	 *
	 * @param   null
	 * @return  string  questions
	 */
	public function getType()
	{
	    if( $this->parent_id )
	    {
	        return DISCUSS_REPLY_TYPE;
		}

		return DISCUSS_QUESTION_TYPE;
	}

	public function delete( $pk = null )
	{
	    // @rule: Delete attachments associated with this post.
	    $attachments	= $this->getAttachments();

	    if( !empty( $attachments ) )
	    {
			$total          = count( $attachments );

			for( $i = 0 ; $i < $total; $i++ )
			{
			    $attachments[ $i ]->delete();
			}
		}

		// @rule: Delete any tags associated with this post.
		$this->deleteTags();

		return parent::delete();
	}

	public function deleteTags()
	{
	    $db     = JFactory::getDBO();

	    $query  = 'DELETE FROM ' . $db->nameQuote( '#__discuss_posts_tags' ) . ' '
	            . 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );

		$db->Query();

		return true;
	}

	/*
	 * Retrieve the post creator's avatar
	 */
	public function getPosterAvatar()
	{
	    $user   = JTable::getInstance( 'Profile' , 'Discuss' );
	    $user->load( $this->user_id );

		return $user->getAvatar();
	}

	public function store( $updateNulls = false )
	{
		$date   = JFactory::getDate();
		$this->modified			= $date->toMySQL();

		if( $this->published == 1 && !empty($this->parent_id) )
		{
		    $this->updateParentLastRepliedDate();
		}

		return parent::store();
	}


	public function updateParentLastRepliedDate()
	{
	    $db = JFactory::getDBO();

	    if( !empty($this->parent_id) )
	    {
	        $query  = 'UPDATE `#__discuss_posts` SET `replied` = ' . $db->Quote( $this->created );
	        $query  .= ' WHERE `id` = ' . $db->Quote( $this->parent_id );

	        $db->setQuery( $query );
	        $db->query();
	    }

	    return true;
	}

	/**
	 * Tests if the user has already voted for this discussion's poll before.
	 *
	 * @access	public
	 * @param	int $userId		The user id to check for.
	 * @return	boolean			True if voted, false otherwise.
	 */
	public function hasVotedPoll( $userId )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_polls_users' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId ) . ' '
			. 'AND ' . $db->nameQuote( 'poll_id' ) . ' IN('
			. 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id )
			. ')';
		$db->setQuery( $query );
		$voted	= $db->loadResult();

		return $voted > 0;
	}

	/**
	 * Return a list of polls for this discussion
	 *
	 **/
	public function getPolls()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$items	= $db->loadObjectList();

		if( !$items )
		{
			return $items;
		}

		$polls	= array();
		foreach( $items as $item )
		{
			$poll	= DiscussHelper::getTable( 'Poll' );
			$poll->bind( $item );

			$polls[]	= $poll;
		}

		return $polls;
	}

	public function removePoll()
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_polls' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $this->id );
		
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();

		if( !$rows )
		{
			return false;
		}

		if( $rows )
		{
			foreach( $rows as $row )
			{
				$poll	= DiscussHelper::getTable( 'Poll' );
				$poll->bind( $row );

				$poll->delete();
			}
		}
		return true;
	}

	public function removePollVote( $userId )
	{
		$polls 	= $this->getPolls();

		foreach( $polls as $poll )
		{
			$poll->removeExistingVote( $userId , $this->id );
		}
		$this->updatePollsCount();
	}

	/**
	 * Recalculates all votes for the particular vote items.
	 */
	public function updatePollsCount()
	{
		$db		= JFactory::getDBO();

		$polls	= $this->getPolls();

		foreach( $polls as $poll )
		{
			$poll->updateCount();
		}
	}

	/**
	 * Retrieve total number of replies for this particular discussion
	 *
	 **/
	public function getReplyCount()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery( $query );
		$count	= $db->loadResult();

		return $count;
	}

	public function getReplies( $limit = 10 , $limitstart = 0 )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT *, count(b.id) as `total_vote_cnt` FROM ' . $db->nameQuote( $this->_tbl ) . ' '
				. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_votes' ) . ' AS `b` '
				. 'ON a.' . $db->nameQuote( 'id' ) . '=b.' . $db->nameQuote( 'post_id' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 ) . ' '
				. 'LIMIT ' . $limitstart . ',' . $limit;
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		$replies	= array();
		foreach( $result as $res )
		{
			$post	= DiscussHelper::getTable( 'Post' );
			$post->bind( $res );

			$replies[]	= $post;
		}
		return $replies;
	}
}
