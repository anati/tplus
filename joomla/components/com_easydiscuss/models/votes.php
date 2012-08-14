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

jimport('joomla.application.component.model');

class EasyDiscussModelVotes extends JModel
{
	function checkUserVote( $post_id )
	{
		$user	 	= JFactory::getUser();
		$db 		= JFactory::getDBO();
		
		$query  = 'SELECT `id` FROM `#__discuss_votes` WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($user->id) . ' AND ' . $db->nameQuote('post_id') . ' = ' . $db->Quote($post_id);
		
		$db->setQuery($query);
		$result	= $db->loadObject();
		
		if ( $result ) 
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
	
	function getVoteType( $post_id , $userId = null )
	{
		$user	 	= JFactory::getUser( $userId );
		$db 		= JFactory::getDBO();
		
		$query  = 'SELECT `value` FROM `#__discuss_votes` WHERE ' . $db->nameQuote('user_id') . ' = ' . $db->Quote($user->id) . ' AND ' . $db->nameQuote('post_id') . ' = ' . $db->Quote($post_id);
		
		$db->setQuery($query);
		$result	= $db->loadResult();
		
		return $result;
	}
	
	function sumPostVotes( $post_id )
	{
		$db = JFactory::getDBO();
		
		$query  = 'SELECT SUM('.$db->nameQuote('value').') AS '.$db->nameQuote('total').' FROM #__discuss_votes WHERE ' . $db->nameQuote('post_id') . ' = ' . $db->Quote($post_id);
		
		$db->setQuery($query);
		$result	= $db->loadResult();
		
		if ( $result )
		{
			return $result;
		}
		else
		{
			return 0;
		}	
	}
	
	function getTotalVoteCount( $post_id )
	{
		$db 		= JFactory::getDBO();
		
		$query  = 'SELECT COUNT(1) FROM `#__discuss_votes` WHERE ' . $db->nameQuote('post_id') . ' = ' . $db->Quote($post_id);
		
		$db->setQuery($query);
		$result	= $db->loadResult();
		
		return $result;
	}
}