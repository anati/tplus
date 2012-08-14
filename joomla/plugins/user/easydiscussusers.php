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

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');

class plgUserEasyDiscussUsers extends JPlugin
{
	function plgUserEasyDiscussUsers(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}


	function onUserBeforeDelete($user)
	{
	    $this->onBeforeDeleteUser($user);
	}

	function onBeforeDeleteUser($user)
	{
		if(JFile::exists(JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'helpers'.DS.'helper.php'))
		{
			require_once (JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'helpers'.DS.'helper.php');

			$mainframe	=& JFactory::getApplication();

			$userId     	= $user['id'];
			$newOwnerShip   = $this->_getnewOwnerShip( $userId );

			//transfer ownership
			$this->ownerTransferTags( $userId, $newOwnerShip );
			$this->ownerTransferPosts( $userId, $newOwnerShip );
			$this->onwerTransferComments( $userId, $newOwnerShip );

			//remove user and his related daata that cannot be transferred.
			$this->removeLikes( $userId );
			$this->removeSubscription( $userId );
			$this->removeVotes( $userId );
			$this->removeEasyDiscussUser( $userId );
		}
	}

	function _getnewOwnerShip( $curUserId )
	{
	    $econfig     	=& DiscussHelper::getConfig();

	    // this should get from backend. If backend not defined, get the default superadmin.
	    $defaultSAid    = DiscussHelper::getDefaultSAIds();
	    $newOwnerShipId	= $econfig->get('main_orphanitem_ownership', $defaultSAid);
	    
	    /**
	     * we check if the tobe deleted user is the same user id as the saved user id in config.
	     * 		 if yes, we try to get a next SA id.
	     */

	    if( $curUserId == $newOwnerShip)
	    {
	        // this is no no a big no! try to get the next admin.
			if(DiscussHelper::getJoomlaVersion() >= '1.6')
			{
	            $saUsersId  = DiscussHelper::getSAUsersIds();
	            if( count($saUsersId) > 0 )
	            {
					for($i = 0; $i < count($saUsersId); $i++)
					{
					    if( $saUsersId[$i] != $curUserId )
					    {
					        $newOwnerShip = $saUsersId[$i];
					        break;
					    }
					}
				}
			}
			else
			{
			    $newOwnerShip = $this->_getSuperAdminId( $curUserId );
			}
	    }
	    
	    $newOwnerShipId	= $this->_verifyOnwerShip($newOwnerShipId);

	    $db =& JFactory::getDBO();

	    $query	= 'SELECT a.`id`, a.`name`, a.`username`, b.`nickname`, a.`email` '
				. ' FROM ' . $db->nameQuote('#__users') . ' as a '
		  		. ' INNER JOIN ' . $db->nameQuote('#__discuss_users') . ' as b on a.`id` = b.`id` '
		  		. ' WHERE a.`id` = ' . $db->Quote($newOwnerShipId);

		$db->setQuery($query);
		$result = $db->loadAssoc();

	    $displayFormat  = $econfig->get('layout_nameformat', 'name');
		$displayName    = '';

		switch($displayFormat)
		{
			case "name" :
				$displayName = $result['name'];
				break;
			case "username" :
				$displayName = $result['username'];
				break;
			case "nickname" :
			default :
				$displayName = (empty($result['nickname'])) ? $result['name'] : $result['nickname'];
				break;
		}

	    $newOwnerShip			= new stdClass();
	    $newOwnerShip->id		= $result['id'];
	    $newOwnerShip->name		= $displayName;
	    $newOwnerShip->email	= $result['email'];

	    return $newOwnerShip;
	}

	function _verifyOnwerShip( $id )
	{
	    $db =& JFactory::getDBO();

	    $query  = 'SELECT `id` FROM `#__users` WHERE `id` = ' . $db->Quote($id);
	    $db->setQuery($query);
	    $result = $db->loadResult();

	    if(empty($result))
	    {
	        if(DiscussHelper::getJoomlaVersion() >= '1.6')
	        {
	            $saUsersId  = DiscussHelper::getSAUsersIds();
	            $result     = $saUsersId[0];
	        }
	        else
	        {
	        	$result = $this->_getSuperAdminId();
	        }
	    }

	    return $result;
	}

	function _getSuperAdminId( $curUserId = '' )
	{
		$db =& JFactory::getDBO();

		$query  = 'SELECT `id` FROM `#__users`';
		$query  .= ' WHERE (LOWER( usertype ) = ' . $db->Quote('super administrator');
		$query  .= ' OR `gid` = ' . $db->Quote('25') . ')';
		
		if(! empty($curUserId) )
		{
		    $query  .= ' AND `id` != ' . $db->Quote( $curUserId );
		}
		
		
		$query  .= ' ORDER BY `id` ASC';
		$query  .= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		$result = (empty($result)) ? '62' : $result;
		return $result;
	}

	function ownerTransferTags( $userId, $newOwnerShip )
	{
	    $db =& JFactory::getDBO();

	    $query  = 'UPDATE `#__discuss_tags`';
	    $query  .= ' SET `user_id` = ' . $db->Quote($newOwnerShip->id);
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function ownerTransferPosts( $userId, $newOwnerShip )
	{
	    $db =& JFactory::getDBO();

	    $query  = 'UPDATE `#__discuss_posts`';
	    $query  .= ' SET `user_id` = ' . $db->Quote($newOwnerShip->id) . ' ';
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function onwerTransferComments( $userId, $newOwnerShip )
	{
	    $db =& JFactory::getDBO();

	    $query  = 'UPDATE `#__discuss_comments`';
	    $query  .= ' SET `user_id` = ' . $db->Quote($newOwnerShip->id) . ', ';
	    $query  .= ' `name` = ' . $db->Quote($newOwnerShip->name) . ', ';
	    $query  .= ' `email` = ' . $db->Quote($newOwnerShip->email) . ' ';
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeLikes( $userId )
	{
	    $db =& JFactory::getDBO();

		$query  = 'SELECT `content_id`, `type` FROM `#__discuss_likes`';
	    $query  .= ' WHERE `created_by` = ' . $db->Quote($userId) . ' ';
		$db->setQuery( $query );
		$likes = $db->loadObjectList();

		if(!empty($likes))
		{
			foreach($likes as $like)
			{
				switch($like->type)
				{
					case 'post':
					default:
						$query  = 'UPDATE `#__discuss_posts` ';
	   					$query  .= ' SET `num_likes` = `num_likes` - 1 ';
	   					$query  .= ' WHERE `id` = ' . $db->Quote($like->content_id);
						$db->setQuery( $query );
						$db->query();
						break;
				}
			}
		}

	    $query  = 'DELETE FROM `#__discuss_likes`';
	    $query  .= ' WHERE `created_by` = ' . $db->Quote($userId);
		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeSubscription( $userId )
	{
	    $db =& JFactory::getDBO();

	    $query  = 'DELETE FROM `#__discuss_subscription`';
	    $query  .= ' WHERE `userid` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeVotes( $userId )
	{
	    $db =& JFactory::getDBO();

		$query  = 'SELECT `post_id`, `value` FROM `#__discuss_votes`';
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId) . ' ';
		$db->setQuery( $query );
		$votes = $db->loadObjectList();

		if(!empty($votes))
		{
			foreach($votes as $vote)
			{
				$query  = 'UPDATE `#__discuss_posts` ';
 					$query  .= ' SET `num_votes` = `num_votes` + ' . $db->Quote($vote->value);
 					$query  .= ' WHERE `id` = ' . $db->Quote($vote->post_id);
				$db->setQuery( $query );
				$db->query();
				break;
			}
		}

	    $query  = 'DELETE FROM `#__discuss_votes`';
	    $query  .= ' WHERE `user_id` = ' . $db->Quote($userId);
		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}

	function removeEasyDiscussUser( $userId )
	{
		$db =& JFactory::getDBO();

		$query  = 'SELECT `avatar` FROM `#__discuss_users`';
	    $query  .= ' WHERE `id` = ' . $db->Quote($userId) . ' ';
		$db->setQuery( $query );
		$user = $db->loadAssoc();

		if(!empty($user) && $user['avatar']!='default.png')
		{
			DiscussHelper::getHelper( 'image' );

			$avatar_link	= DiscussImageHelper::getAvatarRelativePath() . '/' . $user['avatar'];
			$imagePath		= str_replace('/', DS, $avatar_link);

			if( JFile::exists( $imagePath ) )
			{
				JFile::delete( $imagePath );
			}
		}

	    $query  = 'DELETE FROM `#__discuss_users`';
	    $query  .= ' WHERE `id` = ' . $db->Quote($userId);

		$db->setQuery( $query );
		$db->query();
		if($db->getErrorNum())
		{
			JError::raiseError( 500, $db->stderr());
		}
	}
}
