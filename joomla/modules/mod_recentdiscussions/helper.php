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

class modRecentDiscussionsHelper
{
	public static function getData( $params )
	{
		$db		= JFactory::getDBO();
		$limit	= (int) $params->get( 'count', 10 );
		$catid	= intval($params->get( 'category', 0));
		$catfil	= (int) $params->get( 'category_option', 0 );

		if ($limit == 0)
		{
			$limit = '';
		} else {
			$limit = 'LIMIT 0,' . $limit;
		}

		if (!$catfil || $catid == 0)
		{
			$catid = '';
		} else
		{
			$catid = ' AND a.`category_id` = '.$db->quote($catid) . ' ';
		}

		$query	= 'SELECT a.* , COUNT(c.id) AS `num_replies` FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' AS a '
				. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_posts' ) . ' AS c '
				. 'ON a.`id`=c.`parent_id` '
				. 'AND c.`published`=' . $db->Quote( 1 ) . ' '
				. 'WHERE a.`published`=' . $db->Quote( 1 ) . ' '
				. 'AND a.`parent_id`=' . $db->Quote( 0 ) . ' '
				. $catid
				. 'GROUP BY a.`id` '
				. 'ORDER BY a.`created` DESC '
				. $limit;

		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		$posts	= array();

		require_once( DISCUSS_HELPERS . DS . 'parser.php' );
		foreach( $result as $row )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $row->user_id );

			$row->profile	= $profile;
			$row->content	= Parser::bbcode( $row->content );

			$row->title		= DiscussHelper::wordFilter( $row->title );
			$row->content	= DiscussHelper::wordFilter( $row->content );

			// Process bbcode
			$row->content 	= Parser::bbcode( $row->content );

			$posts[]		= $row;
		}

		// Append profile objects to the result
		return $posts;
	}
}
