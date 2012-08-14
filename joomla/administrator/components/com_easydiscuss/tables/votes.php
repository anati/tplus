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

class DiscussVotes extends JTable
{
	var $id 			= null;
	var $user_id		= null;	
	var $post_id		= null;	
	var $created		= null;
	var $ipaddress		= null;	
	var $value			= null;	

	/**
	 * Constructor for this class.
	 * 
	 * @return 
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__discuss_votes' , 'id' , $db );
	}
	
// 	function load($user_id, $post_id)
// 	{
// 
// 	}

	/**
	 * Method to update posts total neg vote count.
	 */
	function addNegVoteCount($postId)
	{
	    $db		= JFactory::getDBO();
	    $val    = 1;

	    if(empty($postId))
	        return false;

	    $query  = 'UPDATE `#__discuss_posts` SET `num_negvote` = `num_negvote` + ' . $db->Quote($val);
	    $query  .= ' WHERE `id` = ' . $db->Quote($postId);
		$db->setQuery($query);
		$db->query();

		return true;
	}
	
	function sumPostVote($postId, $val)
	{
	    $db		= JFactory::getDBO();

	    if(empty($postId))
	        return false;

	    $query  = 'UPDATE `#__discuss_posts` SET `sum_totalvote` = `sum_totalvote` + ' . $db->Quote($val);
		if($val < 0)
		{
			$query  .= ' ,`num_negvote` = `num_negvote` + 1';
		}
	    $query  .= ' WHERE `id` = ' . $db->Quote($postId);
	    
		$db->setQuery($query);
		$db->query();

		return true;
	}

}