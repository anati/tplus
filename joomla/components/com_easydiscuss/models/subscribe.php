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

class EasyDiscussModelSubscribe extends JModel
{
	/**
	 * Post total
	 *
	 * @var integer
	 */
	var $_total 	= null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	function __construct()
	{
		parent::__construct();


		$mainframe	= JFactory::getApplication();

		$limit			= $mainframe->getUserStateFromRequest( 'com_easydiscuss.posts.limit', 'limit', DiscussHelper::getListLimit(), 'int');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function isSiteSubscribed($subscription_info)
	{
		$db	= $this->getDBO();

		$query  = 'SELECT `id`, `interval` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote($subscription_info['type']);
		$query  .= ' AND `email` = ' . $db->Quote($subscription_info['email']);
		$query	.= ' AND `cid` = ' . $db->quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	function isPostSubscribedEmail($subscription_info)
	{
		$db	= $this->getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('post');
		$query  .= ' AND `email` = ' . $db->Quote($subscription_info['email']);
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	function isPostSubscribedUser($subscription_info)
	{
		$db	= $this->getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('post');
		$query  .= ' AND (`userid` = ' . $db->Quote($subscription_info['userid']) . ' OR `email` = ' . $db->Quote($subscription_info['email']) . ')';
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	function isTagSubscribedEmail($subscription_info)
	{
		$db	= $this->getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('tag');
		$query  .= ' AND `email` = ' . $db->Quote($subscription_info['email']);
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	function isTagSubscribedUser($subscription_info)
	{
		$db	= $this->getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote('tag');
		$query  .= ' AND (`userid` = ' . $db->Quote($subscription_info['userid']) . ' OR `email` = ' . $db->Quote($subscription_info['email']) . ')';
		$query  .= ' AND `cid` = ' . $db->Quote($subscription_info['cid']);

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	function addSubscription($subscription_info)
	{
		$config	= DiscussHelper::getConfig();
		$my		= JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$date		= JFactory::getDate();
			$now		= $date->toMySQL();
			$subscriber	= DiscussHelper::getTable( 'Subscribe' );

			$subscriber->userid		= $subscription_info['userid'];
			$subscriber->member		= $subscription_info['member'];
			$subscriber->type		= $subscription_info['type'];
			$subscriber->cid		= $subscription_info['cid'];
			$subscriber->email		= $subscription_info['email'];
			$subscriber->fullname	= $subscription_info['name'];
			$subscriber->interval	= $subscription_info['interval'];
			$subscriber->created	= $now;
			$subscriber->sent_out	= $now;
			return $subscriber->store();
		}

		return false;
	}

	function updateSiteSubscription($sid, $subscription_info)
	{
		$config = DiscussHelper::getConfig();
		$my = JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$date		= JFactory::getDate();
			$subscriber	= DiscussHelper::getTable( 'Subscribe' );

			$subscriber->load($sid);
			$subscriber->userid		= $subscription_info['userid'];
			$subscriber->member		= $subscription_info['member'];
			$subscriber->cid		= $subscription_info['cid'];
			$subscriber->fullname	= $subscription_info['name'];
			$subscriber->interval	= $subscription_info['interval'];
			$subscriber->sent_out	= $date->toMySQL();
			return $subscriber->store();
		}

		return false;
	}

	function updatePostSubscription($sid, $subscription_info)
	{
		$config = DiscussHelper::getConfig();
		$my = JFactory::getUser();

		if($config->get('main_allowguestsubscribe') || ($my->id && !$config->get('main_allowguestsubscribe')))
		{
			$db	= $this->getDBO();

			$query  = 'DELETE FROM `#__discuss_subscription` '
					. ' WHERE `type` = ' . $db->Quote('post')
					. ' AND `cid` = ' . $db->Quote($subscription_info['cid'])
					. ' AND `email` = ' . $db->Quote($subscription_info['email'])
					. ' AND `id` != ' . $db->Quote($sid);

			$db->setQuery($query);
			$result = $db->query();

			if($result)
			{
				$date       = JFactory::getDate();
				$subscriber = DiscussHelper::getTable( 'Subscribe' );

				$subscriber->load($sid);
				$subscriber->userid		= $subscription_info['userid'];
				$subscriber->member		= $subscription_info['member'];
				$subscriber->cid		= $subscription_info['cid'];
				$subscriber->fullname   = $subscription_info['name'];
				$subscriber->interval   = $subscription_info['interval'];
				$subscriber->sent_out 	= $date->toMySQL();
				return $subscriber->store();
			}
		}

		return false;
	}

	function getPostSubscribers($postid='')
	{
		if(empty($postid))
		{
			//invalid post id
			return false;
		}

		$db = JFactory::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `type` = ' . $db->Quote('post')
				. ' AND `cid` = ' . $db->Quote($postid);

		$db->setQuery($query);

		$result			= $db->loadObjectList();
		$emails			= array();
		$subscribers	= array();

		foreach( $result as $row )
		{
			if( !in_array( $row->email , $emails ) )
			{
				$subscribers[]	= $row;
			}
			$emails[]	= $row->email;
		}
		return $subscribers;
	}

	function getSiteSubscribers($interval='daily', $now='')
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `interval` = ' . $db->Quote($interval)
				. ' AND `type` = ' . $db->Quote('site');

		if(!empty($now))
		{
			switch($interval)
			{
				case 'weekly':
					$days = '7';
					break;
				case 'monthly':
					$days = '30';
					break;
				case 'daily':
					$days = '1';
				default :
					break;
			}

			$query	.= ' AND DATEDIFF(' . $db->Quote($now) . ', `sent_out`) >= ' . $db->Quote($days);
		}

		$db->setQuery($query);

		$result = $db->loadObjectList();
		return $result;
	}

	function getTagSubscribers($tagid='')
	{
		if(empty($tagid))
		{
			//invalid tag id
			return false;
		}

		$db = JFactory::getDBO();

		$query  = 'SELECT * FROM `#__discuss_subscription` '
				. ' WHERE `type` = ' . $db->Quote('tag')
				. ' AND `cid` = ' . $db->Quote($tagid);

		$db->setQuery($query);

		$result			= $db->loadObjectList();
		$emails			= array();
		$subscribers	= array();

		foreach( $result as $row )
		{
			if( !in_array( $row->email , $emails ) )
			{
				$subscribers[]	= $row;
			}
			$emails[]	= $row->email;
		}
		return $subscribers;
	}

	function getCreatedPostByInterval($sent_out, $now='')
	{
		$db = JFactory::getDBO();

		if(empty($now))
		{
			$date 	= JFactory::getDate();
			$now 	= $date->toMySQL();
		}

		$query	= 'SELECT '
				. ' DATEDIFF(' . $db->Quote($now) . ', a.`created`) as `daydiff`, '
				. ' TIMEDIFF(' . $db->Quote($now). ', a.`created`) as `timediff`, a.* '
				. ' FROM `#__discuss_posts` as a '
				. ' WHERE a.`parent_id` = 0 AND ( a.`created` > ' . $db->Quote($sent_out) . ' AND a.`created` < ' . $db->Quote($now) . ')'
				. ' ORDER BY a.`created` ASC';

		$db->setQuery($query);

		$result = $db->loadAssocList();

		return $result;
	}

	function isMySubscription( $userid, $type, $subId )
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT `id` FROM `#__discuss_subscription`';
		$query  .= ' WHERE `type` = ' . $db->Quote( $type );
		$query  .= ' AND `id` = ' . $db->Quote( $subId );
		$query  .= ' AND `userid` = ' . $db->Quote( $userid );

		$db->setQuery( $query );
		$result = $db->loadResult();

		return ( empty($result) ) ? false : true;
	}

	public function getSubscriptions()
	{
		$db		= JFactory::getDBO();
		$date	= JFactory::getDate();
		$my		= JFactory::getUser();
		$userid	= $my->id;

		$email	= JRequest::getVar('email');
		$extra	= $email ? ' AND s.`email` = ' . $db->quote($email) : '';

		$query	= 'SELECT DATEDIFF('. $db->Quote($date->toMySQL()) . ', s.`created` ) AS `noofdays`,'
				. ' DATEDIFF(' . $db->Quote($date->toMySQL()) . ', IF(s.`sent_out` = '.$db->Quote('0000-00-00 00:00:00') . ', s.`created`, s.`sent_out`) ) AS `daydiff`, '
				. ' TIMEDIFF(' . $db->Quote($date->toMySQL()). ', IF(s.`sent_out` = '.$db->Quote('0000-00-00 00:00:00') . ', s.`created`, s.`sent_out`) ) AS `timediff`,'
				. ' IF(s.`sent_out` = '.$db->Quote('0000-00-00 00:00:00') . ', s.`created`, s.`sent_out`) as `lastsent`,'
				. ' s.*'
				. ' FROM `#__discuss_subscription` AS s'
				. ' WHERE s.`userid` = ' . $db->quote( (int) $userid )
				. $extra;

		$db->setQuery($query);

		$result	= $db->loadObjectList();

		$subscriptions	= array();

		foreach( $result as $row )
		{
			if( $row->type == 'post' )
			{
				// Test if the post still exists on the site.
				$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $row->cid );
				$db->setQuery( $query );
				$exists	= $db->loadResult();

				if( $exists )
				{
					$subscriptions[]	= $row;
				}
			}
			else
			{
				$subscriptions[]	= $row;
			}
		}
		return $subscriptions;
	}

	public function isSubscribed( $userid, $cid, $type = 'post' )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT `id` FROM `#__discuss_subscription`'
				. ' WHERE `type` = ' . $db->quote( $type )
				. ' AND `userid` = ' . $db->quote( $userid )
				. ' AND `cid` = ' . $db->quote( $cid );

		$db->setQuery( $query );
		return $db->loadResult();
	}
}
