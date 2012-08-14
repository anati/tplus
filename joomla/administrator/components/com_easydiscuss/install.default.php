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

function getDiscussId()
{
	$db		= JFactory::getDBO();

	if( getJoomlaVersion() >= '1.6' )
	{
		$query 	= 'SELECT ' . $db->nameQuote( 'extension_id' ) . ' '
			. 'FROM ' . $db->nameQuote( '#__extensions' ) . ' '
			. 'WHERE `element`=' . $db->Quote( 'com_easydiscuss' ) . ' '
			. 'AND `type`=' . $db->Quote( 'component' ) . ' ';
	}
	else
	{
		$query 	= 'SELECT ' . $db->nameQuote( 'id' ) . ' '
			. 'FROM ' . $db->nameQuote( '#__components' ) . ' '
			. 'WHERE `option`=' . $db->Quote( 'com_easydiscuss' ) . ' '
			. 'AND `parent`=' . $db->Quote( '0');
	}

	$db->setQuery( $query );

	return $db->loadResult();
}

function menuExist()
{
	$db		= JFactory::getDBO();

	if( getJoomlaVersion() >= '1.6' ) {
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' .  $db->Quote( '%option=com_easydiscuss%') . ' '
			. 'AND `client_id`=' . $db->Quote( '0' ) . ' '
			. 'AND `type`=' . $db->Quote( 'component' ) . ' '
			. 'AND `menutype`=' . $db->Quote( 'mainmenu' );
	} else {
		$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' .  $db->Quote( '%option=com_easydiscuss%');
	}

	$db->setQuery( $query );

	$requiresUpdate	= ( $db->loadResult() >= 1 ) ? true : false;

	return $requiresUpdate;
}

/**
 * Method to update menu's component id.
 *
 * @return boolean true on success false on failure.
 */
function updateMenuItems()
{
	// Get new component id.
	$db 	= JFactory::getDBO();

	$cid = getDiscussId();

	if( !$cid )
		return false;

	$joomlaVersion = getJoomlaVersion();

	if( $joomlaVersion >= '1.6' )
	{
		$query 	= 'UPDATE ' . $db->nameQuote( '#__menu' ) . ' '
			. 'SET component_id=' . $db->Quote( $cid ) . ' '
			. 'WHERE link LIKE ' . $db->Quote('%option=com_easydiscuss%') . ' '
			. 'AND `type`=' . $db->Quote( 'component' ) . ' '
			. 'AND `menutype` = ' . $db->Quote( 'mainmenu' ) . ' '
			. 'AND `client_id`=' . $db->Quote( '0' );
	}
	else
	{
		// Update the existing menu items.
		$query 	= 'UPDATE ' . $db->nameQuote( '#__menu' ) . ' '
			. 'SET componentid=' . $db->Quote( $cid ) . ' '
			. 'WHERE link LIKE ' . $db->Quote('%option=com_easydiscuss%');
	}

	$db->setQuery( $query );
	$db->query();

	return true;
}

/**
 * Method to add menu's item.
 *
 * @return boolean true on success false on failure.
 */
function createMenuItems()
{
	// Get new component id.
	$db 	= JFactory::getDBO();

	$cid = getDiscussId();

	if( !$cid )
		return false;

	$query 	= 'SELECT ' . $db->nameQuote( 'ordering' ) . ' '
			. 'FROM ' . $db->nameQuote( '#__menu' ) . ' '
			. 'ORDER BY ' . $db->nameQuote( 'ordering' ) . ' DESC LIMIT 1';
	$db->setQuery( $query );
	$order 	= $db->loadResult() + 1;

	$status = true;
	if( getJoomlaVersion() >= '1.6' )
	{
		$table = JTable::getInstance('Menu', 'JTable', array());

		$table->menutype		= 'mainmenu';
		$table->title 			= 'Discussions';
		$table->alias 			= 'discussions';
		$table->path 			= 'discussions';
		$table->link 			= 'index.php?option=com_easydiscuss&view=index';
		$table->type 			= 'component';
		$table->published 		= '1';
		$table->parent_id 		= '1';
		$table->component_id	= $cid;
		$table->ordering 		= $order;
		$table->client_id 		= '0';
		$table->language 		= '*';

		$table->setLocation('1', 'last-child');

		if(!$table->store()){
			$status = false;
		}
	}
	else
	{
		// Update the existing menu items.
		$query 	= 'INSERT INTO ' . $db->nameQuote( '#__menu' )
			. '('
				. $db->nameQuote( 'menutype' ) . ', '
				. $db->nameQuote( 'name' ) . ', '
				. $db->nameQuote( 'alias' ) . ', '
				. $db->nameQuote( 'link' ) . ', '
				. $db->nameQuote( 'type' ) . ', '
				. $db->nameQuote( 'published' ) . ', '
				. $db->nameQuote( 'parent' ) . ', '
				. $db->nameQuote( 'componentid' ) . ', '
				. $db->nameQuote( 'sublevel' ) . ', '
				. $db->nameQuote( 'ordering' ) . ' '
			. ') '
			. 'VALUES('
				. $db->quote( 'mainmenu' ) . ', '
				. $db->quote( 'Discussions' ) . ', '
				. $db->quote( 'discussions' ) . ', '
				. $db->quote( 'index.php?option=com_easydiscuss&view=index' ) . ', '
				. $db->quote( 'component' ) . ', '
				. $db->quote( '1' ) . ', '
				. $db->quote( '0' ) . ', '
				. $db->quote( $cid ) . ', '
				. $db->quote( '0' ) . ', '
				. $db->quote( $order ) . ' '
			. ') ';

		$db->setQuery( $query );
		$db->query();

		if($db->getErrorNum())
		{
			$status = false;
		}
	}

	return $status;
}

function _getSuperAdminId()
{
	$db = JFactory::getDBO();

	if( getJoomlaVersion() >= '1.6' )
	{
		$saUsers	= getSAUsersIds();

		$result = '42';
		if(count($saUsers) > 0)
		{
		    $result = $saUsers['0'];
		}
	}
	else
	{
		$query  = 'SELECT `id` FROM `#__users`';
		$query  .= ' WHERE (LOWER( usertype ) = ' . $db->Quote('super administrator');
		$query  .= ' OR `gid` = ' . $db->Quote('25') . ')';
		$query  .= ' ORDER BY `id` ASC';
		$query  .= ' LIMIT 1';

		$db->setQuery($query);
		$result = $db->loadResult();

		$result = (empty($result)) ? '62' : $result;
	}

	return $result;
}

function getSAUsersIds()
{
	$db = JFactory::getDBO();

	$query  = 'SELECT a.`id`, a.`title`';
	$query	.= ' FROM `#__usergroups` AS a';
	$query	.= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
	$query	.= ' GROUP BY a.id';
	$query	.= ' ORDER BY a.lft ASC';

	$db->setQuery($query);
	$result = $db->loadObjectList();

	$saGroup    = array();
	foreach($result as $group)
	{
	    if(JAccess::checkGroup($group->id, 'core.admin'))
	    {
	        $saGroup[]  = $group;
	    }
	}


	//now we got all the SA groups. Time to get the users
	$saUsers    = array();
	if(count($saGroup) > 0)
	{
	    foreach($saGroup as $sag)
	    {
              $userArr	= JAccess::getUsersByGroup($sag->id);
              if(count($userArr) > 0)
              {
                  foreach($userArr as $user)
                  {
                      $saUsers[]    = $user;
                  }
              }
	    }
	}

	return $saUsers;
}

function defaultTagExists()
{
	$db		= JFactory::getDBO();

	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__discuss_tags' );
	$db->setQuery( $query );

	$exist	= ( $db->loadResult() > 0 ) ? true : false;

	return $exist;
}

function createDefaultTags()
{
	$db 	= JFactory::getDBO();

	$suAdmin    = _getSuperAdminId();
	$query 		= 'INSERT INTO `#__discuss_tags` ( `title`, `alias`, `created`, `published`, `user_id`) '
				. 'VALUES ( "General", "general", now(), 1, ' . $db->Quote($suAdmin) .' ), '
				. '( "Automotive", "automotive", now(), 1, ' . $db->Quote($suAdmin) .' ), '
				. '( "Sharing", "sharing", now(), 1, ' . $db->Quote($suAdmin) .' ), '
				. '( "Info", "info", now(), 1, ' . $db->Quote($suAdmin) .' ), '
				. '( "Discussions" , "discussions" , now() , 1 , ' . $db->Quote( $suAdmin ) . ')';

	$db->setQuery( $query );
	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}
	return true;
}

function configExist()
{
	$db		= JFactory::getDBO();

	$query	= 'SELECT COUNT(*) FROM '
			. $db->nameQuote( '#__discuss_configs' ) . ' '
			. 'WHERE ' . $db->nameQuote( 'name' ) . '=' . $db->Quote( 'config' );
	$db->setQuery( $query );

	$exist	= ( $db->loadResult() > 0 ) ? true : false;

	return $exist;
}

function createConfig()
{
	$db			= JFactory::getDBO();

	$config		= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_easydiscuss' . DS . 'configuration.ini';
	$registry	= JRegistry::getInstance( 'easydiscuss' );
	$registry->loadFile( $config , 'INI' , 'easydiscuss' );

	$obj			= new stdClass();
	$obj->name		= 'config';
	$obj->params	= $registry->toString( 'INI' , 'easydiscuss' );

	return $db->insertObject( '#__discuss_configs' , $obj );
}

function postExist()
{
	$db		= JFactory::getDBO();

	$query	= 'SELECT COUNT(1) FROM '
			. $db->nameQuote( '#__discuss_posts' ) . ' '
			. 'LIMIT 1';
	$db->setQuery( $query );

	$result = $db->loadResult();

	$exist	= ( ! empty($result) ) ? true : false;

	return $exist;
}

function createSamplePost()
{
	$db 	= JFactory::getDBO();

	$suAdmin    = _getSuperAdminId();

	$content = array();
	$content['thankyou'] = 'Thank you for choosing EasyDiscuss as your preferred discussion tool for your Joomla! website. We hope you find it useful in achieving your needs.';
	$content['congratulation'] = 'Congratulations! You have successfully installed EasyDiscuss and ready to post your first question!';

	$query 		= 'INSERT IGNORE INTO `#__discuss_posts` ( `id`, `title`, `alias`, `created`, `modified`, `content`, `published`, `featured`, `isresolve`, `user_id`, `parent_id`, `user_type`) '
				. 'VALUES ( "1", "Thank you for choosing EasyDiscuss", "thank-you-for-choosing-easydiscuss", now(), now(), ' . $db->Quote($content['congratulation']) . ', 1, 1, 1,' . $db->Quote($suAdmin) .', 0, "member" ), '
				. '( "2", "Congratulations! You have successfully installed EasyDiscuss", "congratulations-succesfully-installed-easydiscuss", now(), now(), ' . $db->Quote($content['thankyou']) . ', 1, 0, 1,' . $db->Quote($suAdmin) .', 0, "member" ) ';
	$db->setQuery( $query );
	$db->query();

	//create tag for sample post
	$query 		= 'INSERT IGNORE INTO `#__discuss_tags` ( `id`, `title`, `alias`, `created`, `published`, `user_id`) '
				. 'VALUES ( "6", "Thank You", "thank-you", now(), 1, ' . $db->Quote($suAdmin) .' ), '
				. '( "7", "Congratulations", "congratulations", now(), 1, ' . $db->Quote($suAdmin) .' ) ';
	$db->setQuery( $query );
	$db->query();

	//create posts tags records
	$query 		= 'INSERT INTO `#__discuss_posts_tags` ( `post_id`, `tag_id`) '
				. 'VALUES ( "1", "6" ), '
				. '( "2", "7" ) ';
	$db->setQuery( $query );
	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}
	return true;
}

function blogCategoryExist()
{
	$db		= JFactory::getDBO();

	$query	= 'SELECT COUNT(*) FROM ' . $db->nameQuote( '#__discuss_category' );
	$db->setQuery( $query );

	$result = $db->loadResult();

	$exist	= ( ! empty($result) ) ? true : false;

	return $exist;
}

function createBlogCategory()
{
	$db 	= JFactory::getDBO();

	$suAdmin    = _getSuperAdminId();

	$query 	= "INSERT IGNORE INTO `#__discuss_category` (`id`, `created_by`, `title`, `alias`, `created`, `status`, `published`, `ordering`, `private`, `default`, `level`, `lft`, `rgt`)";
	$query	.= " VALUES ('1', " . $db->Quote($suAdmin) .", 'Uncategorized', 'uncategorized', now(), 0, 1, 0, 0, 1, 0, 1, 2)";


	$db->setQuery( $query );

	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}

	return true;
}

/**
 * Method to extract archive
 *
 * @returns	boolean	True on success false if fail.
 **/
function extractArchive( $source , $destination )
{
	// Cleanup path
	$destination	= JPath::clean( $destination );
	$source			= JPath::clean( $source );

	return JArchive::extract( $source , $destination );
}

function hasAnyRules()
{
	$db 	= JFactory::getDBO();
	$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_rules' );

	$db->setQuery( $query );
	$total	= $db->loadResult();

	return $total > 0;
}

function installDefaultRules()
{
	$db 	= JFactory::getDBO();
	$query	= "
INSERT INTO `#__discuss_rules`(`id`, `command`, `title`, `description`, `callback`, `created`, `published`) VALUES
('1', 'easydiscuss.vote.reply', 'Vote a reply', 'This rule allows you to assign a badge for a user when they vote a reply.', '', NOW(), 1),
('2', 'easydiscuss.answer.reply', 'Reply accepted as answer', 'This rule allows you to assign a badge for a user when their reply is accepted as an answer.', '', NOW(), 1),
('3', 'easydiscuss.like.discussion', 'Like a discussion', 'This rule allows you to assign a badge for a user when they like a discussion.', '', NOW(), 1),
('4', 'easydiscuss.like.reply', 'Like a reply', 'This rule allows you to assign a badge for a user when they like a reply.', '', NOW(), 1),
('5', 'easydiscuss.new.avatar', 'Updates profile picture', 'This rule allows you to assign a badge for a user when they upload a profile picture.', '', NOW(), 1),
('6', 'easydiscuss.new.discussion', 'New Discussion', 'This rule allows you to assign a badge for a user when they create a new discussion.', '', NOW(), 1),
('7', 'easydiscuss.new.reply', 'New Reply', 'This rule allows you to assign a badge for a user when they reply to discussion.', '', NOW(), 1),
('8', 'easydiscuss.read.discussion', 'Read a discusison', 'This rule allows you to assign a badge for a user when they read a discussion.', '', NOW(), 1),
('9', 'easydiscuss.resolved.discussion', 'Update discussion to resolved', 'This rule allows you to assign a badge for a user when they mark their discussion as resolved.', '', NOW(), 1),
('10', 'easydiscuss.update.profile', 'Updates profile', 'This rule allows you to assign a badge for a user when they update their profile.', '', NOW(), 1),
('11', 'easydiscuss.new.comment', 'New Comment', 'This rule allows you to assign a badge for a user when they create a new comment.', '', NOW(), 1),
('12', 'easydiscuss.unlike.discussion', 'Unlike a discussion', 'This rule allows you to deduct points for a user when they unlike a discussion.', '', NOW(), 1),
('13', 'easydiscuss.unlike.reply', 'Unlike a reply', 'This rule allows you to deduct points for a user when they unlike a reply.', '', NOW(), 1);";

	$db->setQuery( $query );
	$db->Query();

	return true;
}

function installDefaultRulesBadges()
{
    $db 	= JFactory::getDBO();

	$query	= "
INSERT INTO `#__discuss_badges` (`id`, `rule_id`, `title`, `description`, `avatar`, `created`, `published`, `rule_limit`, `alias` ) VALUES
('1', '1', 'Motivator', 'Voted replies 100 times.', 'motivator.png', NOW(), '1', '100', 'motivator'),
('2', '2', 'Hole-in-One', 'Accepted 50 replies as answers.', 'hole-in-one.png', NOW(), '1', '50', 'hole-in-one'),
('3', '3', 'Smile Seeker', 'Liked 100 discussions.', 'busybody.png', NOW(), '1', '100', 'busybody'),
('4', '4', 'Love Fool', 'Liked 100 replies.', 'love-fool.png', NOW(), '1', '100', 'love-fool'),
('5', '5', 'Vanity Monster', 'Updated 5 avatars in profile.', 'vanity-monster.png', NOW(), '1', '5', 'vanity-monster'),
('6', '6', 'Sherlock Holmes', 'Started 10 discussions.', 'sherlock-holmes.png', NOW(), '1', '10', 'sherlock-holmes'),
('7', '7', 'The Voice', 'Posted 100 replies.', 'the-voice.png', NOW(), '1', '100', 'the-voice'),
('8', '8', 'Bookworm', 'Read 50 discussions.', 'bookworm.png', NOW(), '1', '50', 'bookworm'),
('9', '9', 'Peacemaker', 'Updated 50 discussions to resolved.', 'peacemaker.png', NOW(), '1', '50', 'peacemaker'),
('10', '10', 'Attention!', 'Updated profile 50 times.', 'attention.png', NOW(), '1', '50', 'attention'),
('11', '11', 'Firestarter', 'Posted 100 comments.', 'firestarter.png', NOW(), '1', '100', 'firestarter');";


	$db->setQuery( $query );
	$db->Query();

	return true;
}


function updateEasyDiscussDBColumns()
{
	$db		= JFactory::getDBO();

	if(! _isColumnExists( '#__discuss_posts' , 'category_id' ) )
	{
		$query = 'ALTER TABLE `#__discuss_posts` ADD `category_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT 1 AFTER `content` ';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__discuss_posts' , 'answered' ) )
	{
		$query = 'ALTER TABLE `#__discuss_posts` ADD `answered` TINYINT( 1 ) NULL DEFAULT 0';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__discuss_posts` ADD INDEX `discuss_post_answered` ( `answered` )';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__discuss_posts' , 'params' ) )
	{
		$query = 'ALTER TABLE `#__discuss_posts` ADD `params` TEXT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if( !_isColumnExists( '#__discuss_users' , 'latitude' ) )
	{
		$query 	= 'ALTER TABLE `#__discuss_users` ADD `latitude` VARCHAR(255) NULL DEFAULT NULL';
		$db->setQuery( $query );
		$db->Query();
	}

	if( !_isColumnExists( '#__discuss_users' , 'longitude' ) )
	{
		$query 	= 'ALTER TABLE `#__discuss_users` ADD `longitude` VARCHAR(255) NULL DEFAULT NULL';
		$db->setQuery( $query );
		$db->Query();
	}

	if( !_isColumnExists( '#__discuss_users' , 'location' ) )
	{
		$query 	= 'ALTER TABLE `#__discuss_users` ADD `location` TEXT NOT NULL';
		$db->setQuery( $query );
		$db->Query();
	}

	if(! _isColumnExists( '#__discuss_mailq' , 'ashtml' ) )
	{
		$query = 'ALTER TABLE `#__discuss_mailq` ADD `ashtml` tinyint(1) NOT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isIndexKeyExists('#__discuss_posts', 'discuss_post_category') )
	{
	    //if this index key is not present, then its an upgrade from 1.1.1866

		$query = 'alter table `#__discuss_posts` add index `discuss_post_category` (`category_id`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_posts` add index `discuss_post_query1` (`published`, `parent_id`, `answered`, `id`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_posts` add index `discuss_post_query2` (`published`, `parent_id`, `answered`, `replied`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_posts` add index `discuss_post_query3` (`published`, `parent_id`, `category_id`, `created`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_posts` add index `discuss_post_query4` (`published`, `parent_id`, `category_id`, `id`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_posts` add index `discuss_post_query5` (`published`, `parent_id`, `created`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_posts` add index `discuss_post_query6` (`published`, `parent_id`, `id`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_votes` add index `discuss_user_id` (`user_id`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_category` add index `discuss_cat_mod_categories1` (`published`, `private`, `id`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_category` add index `discuss_cat_mod_categories2` (`published`, `private`, `ordering`)';
		$db->setQuery($query);
		$db->query();

		$query = 'alter table `#__discuss_tags` add index `discuss_tags_query1` (`published`, `id`)';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__discuss_users' , 'points' ) )
	{
		$query = 'ALTER TABLE `#__discuss_users` ADD `points` BIGINT DEFAULT 0 NOT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isColumnExists( '#__discuss_users' , 'signature' ) )
	{
		$query = 'ALTER TABLE `#__discuss_users` ADD `signature` TEXT NOT NULL ';
		$db->setQuery($query);
		$db->query();
	}

	if( !_isColumnExists( '#__discuss_category' , 'params' ) )
	{
		$query 	= 'ALTER TABLE `#__discuss_category` ADD `params` TEXT NOT NULL';
		$db->setQuery( $query );
		$db->Query();
	}

	if(! _isColumnExists( '#__discuss_posts' , 'password' ) )
	{
		$query = 'ALTER TABLE `#__discuss_posts` ADD `password` TEXT NULL';
		$db->setQuery($query);
		$db->query();
	}

	if(! _isIndexKeyExists('#__discuss_posts', 'discuss_post_titlecontent') )
	{
		$query = 'ALTER TABLE `#__discuss_posts` ADD FULLTEXT `discuss_post_titlecontent` (`title`, `content`)';
		$db->setQuery($query);
		$db->query();

		$query = 'ALTER TABLE `#__discuss_tags` ADD FULLTEXT `discuss_tags_title` (`title`)';
		$db->setQuery($query);
		$db->query();
	}

	return true;
}

function _isColumnExists($tbName, $colName)
{
	$db		= JFactory::getDBO();

	$query	= 'SHOW FIELDS FROM ' . $db->nameQuote( $tbName );
	$db->setQuery( $query );

	$fields	= $db->loadObjectList();

	$result = array();
	foreach( $fields as $field )
	{
		$result[ $field->Field ]	= preg_replace( '/[(0-9)]/' , '' , $field->Type );
	}

	if(array_key_exists($colName, $result))
	{
		return true;
	}

	return false;
}

function _isIndexKeyExists($tbName, $indexName)
{
	$db		= JFactory::getDBO();

	$query	= 'SHOW INDEX FROM ' . $db->nameQuote( $tbName );
	$db->setQuery( $query );
	$indexes	= $db->loadObjectList();

	$result = array();
	foreach( $indexes as $index )
	{
		$result[ $index->Key_name ]	= preg_replace( '/[(0-9)]/' , '' , $index->Column_name );
	}

	if(array_key_exists($indexName, $result))
	{
		return true;
	}

	return false;
}

function _isTableExists($tbName)
{
	$db		= JFactory::getDBO();
	$query	= 'SHOW TABLES LIKE ' . $db->quote($tbName);
	$db->setQuery($query);

	return (boolean) $db->loadResult();
}

function getJoomlaVersion()
{
    $jVerArr   = explode('.', JVERSION);
    $jVersion  = $jVerArr[0] . '.' . $jVerArr[1];

	return $jVersion;
}

function installDefaultPlugin( $sourcePath )
{
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');

	$db 			= JFactory::getDBO();
	$pluginFolder	= $sourcePath . DS . 'default_plugin';
	$plugins		= new stdClass();

	$joomlaVersion 	= getJoomlaVersion();

	//set plugin details
	$plugins->deleteuser			= new stdClass();
	$plugins->deleteuser->zip  		= $pluginFolder . DS . 'plg_easydiscussusers.zip';

	if($joomlaVersion >= '1.6'){
		$plugins->deleteuser->path 	= JPATH_ROOT . DS . 'plugins' . DS . 'user' . DS . 'easydiscussusers';
	} else {
		$plugins->deleteuser->path 	= JPATH_ROOT . DS . 'plugins' . DS . 'user';
	}

	$plugins->deleteuser->name 		= 'User - EasyDiscuss Users';
	$plugins->deleteuser->element 	= 'easydiscussusers';
	$plugins->deleteuser->folder 	= 'user';
	$plugins->deleteuser->params 	= '';
	$plugins->deleteuser->lang 		= '';

	foreach($plugins as $plugin)
	{
		if(!JFolder::exists($plugin->path))
		{
			JFolder::create($plugin->path);
		}

		if( extractArchive($plugin->zip, $plugin->path) )
		{
			if( $joomlaVersion >= '1.6' ) {
				//delete old plugin entry before install
				$sql = 'DELETE FROM '
					 			. $db->nameQuote('#__extensions') . ' '
					 . 'WHERE ' . $db->nameQuote('element') . '=' . $db->quote($plugin->element) . ' AND '
					 		    . $db->nameQuote('folder') . '=' . $db->quote($plugin->folder) . ' AND '
								. $db->nameQuote('type') . '=' . $db->quote('plugin') . ' ';
				$db->setQuery($sql);
				$db->Query();

				//insert plugin again
				$sql 	= 'INSERT INTO ' . $db->nameQuote( '#__extensions' )
						. '('
							. $db->nameQuote( 'name' ) . ', '
							. $db->nameQuote( 'type' ) . ', '
							. $db->nameQuote( 'element' ) . ', '
							. $db->nameQuote( 'folder' ) . ', '
							. $db->nameQuote( 'client_id' ) . ', '
							. $db->nameQuote( 'enabled' ) . ', '
							. $db->nameQuote( 'access' ) . ', '
							. $db->nameQuote( 'protected' ) . ', '
							. $db->nameQuote( 'params' ) . ', '
							. $db->nameQuote( 'ordering' ) . ' '
						. ') '
						. 'VALUES('
							. $db->quote( $plugin->name ) . ', '
							. $db->quote( 'plugin' ) . ', '
							. $db->quote( $plugin->element ) . ', '
							. $db->quote( $plugin->folder ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '1' ) . ', '
							. $db->quote( '1' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( $plugin->params ) . ', '
							. $db->quote( '0' ) . ' '
						. ') ';
			} else {
				//delete old plugin entry before install
				$sql = 'DELETE FROM '
					 			. $db->nameQuote('#__plugins') . ' '
					 . 'WHERE ' . $db->nameQuote('element') . '=' . $db->quote($plugin->element) . ' AND '
					 		    . $db->nameQuote('folder') . '=' . $db->quote($plugin->folder);
				$db->setQuery($sql);
				$db->Query();

				//insert plugin again
				$sql 	= 'INSERT INTO ' . $db->nameQuote( '#__plugins' )
						. '('
							. $db->nameQuote( 'name' ) . ', '
							. $db->nameQuote( 'element' ) . ', '
							. $db->nameQuote( 'folder' ) . ', '
							. $db->nameQuote( 'access' ) . ', '
							. $db->nameQuote( 'ordering' ) . ', '
							. $db->nameQuote( 'published' ) . ', '
							. $db->nameQuote( 'iscore' ) . ', '
							. $db->nameQuote( 'client_id' ) . ', '
							. $db->nameQuote( 'params' ) . ' '
						. ') '
						. 'VALUES('
							. $db->quote( $plugin->name ) . ', '
							. $db->quote( $plugin->element ) . ', '
							. $db->quote( $plugin->folder ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '1' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( '0' ) . ', '
							. $db->quote( $plugin->params ) . ' '
						. ') ';
			}

			$db->setQuery($sql);
			$db->Query();

			if($db->getErrorNum()){
				JError::raiseError( 500, $db->stderr());
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return false;
		}
	}
}

function removeAdminMenu()
{
	$db		= JFactory::getDBO();

	$query  = '	DELETE FROM `#__menu` WHERE link LIKE \'%com_easydiscuss%\' AND client_id = \'1\'';

	$db->setQuery($query);
	$db->query();
}

function updateACLRules()
{
	$db 	= JFactory::getDBO();

	$query 	= "INSERT INTO `#__discuss_acl` (`id`, `action`, `default`, `description`, `published`, `ordering`) VALUES
				(1, 'add_reply', 1, 'Determines whether the user is allowed to post a new reply on a discussion.', 1, 0),
				(2, 'add_question', 1, 'Determines whether the user is allowed to post a new discussion.', 1, 0),
				(3, 'add_attachment', 1, 'Determines whether the user is allowed to upload attachments.', 1, 0),
				(4, 'add_tag', 1, 'Determines whether the user is allowed to insert tags into their discussions.', 1, 0),
				(5, 'edit_reply', 0, 'Determines whether the user is allowed to edit replies.', 1, 0),
				(6, 'delete_reply', 0, 'Determines whether the user is allowed to delete replies.', 1, 0),
				(7, 'mark_answered', 0, 'Determines whether the user is allowed to select an answer for the discussion.', 1, 0),
				(8, 'lock_discussion', 0, 'Determines whether the user is allowed to lock or unlock a discussion.', 1, 0),
				(9, 'edit_question', 0, 'Determines whether the user is allowed to edit an existing discussion.', 1, 0),
				(10, 'delete_question', 0, 'Determines whether the user is allowed to delete an existing discussion.', 1, 0),
				(11, 'delete_attachment', '0', 'Allows user to remove a file attachment from the reply or questions.', 1, 0);";

	$db->setQuery( $query );
	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}

	return true;
}

function updateGroupACLRules()
{
	$db 		= JFactory::getDBO();

	$userGroup  = array();

	if( getJoomlaVersion() >= '1.6' ) {
		//get all user group for 1.6
		$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
		$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
		$query .= ' FROM #__usergroups AS a';
		$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		$query .= ' GROUP BY a.id';
		$query .= ' ORDER BY a.lft ASC';

		$db->setQuery($query);
		$userGroups = $db->loadAssocList();

		$defaultAcl = array(1, 2, 3, 4, 5);

		if(!empty($userGroups))
		{
			foreach($userGroups as $value)
			{
				switch($value['id'])
				{
					case '1':
						//default guest group in joomla 1.6
						$userGroup[$value['id']] = array();
						break;
					case '7':
						//default administrator group in joomla 1.6
						$userGroup[$value['id']]  = 'all';
					case '8':
						//default super user group in joomla 1.6
						$userGroup[$value['id']]  = 'all';
						break;
					default:
						//every other group
						$userGroup[$value['id']]  = $defaultAcl;
				}
			}
		}
	} else {
		$defaultAcl = array(1, 2, 3, 4, 5);

		//28 Public frontend
	    $userGroup[29]  = $defaultAcl;

		//18 registered
	    $userGroup[18]  = $defaultAcl;

	    //19 author
	    $userGroup[19]  = $defaultAcl;

	    //20 editor
	    $userGroup[20]  = $defaultAcl;

	    //21 publisher
	    $userGroup[21]  = $defaultAcl;

	    //23 manager
	    $userGroup[23]  = $defaultAcl;

	    //24 administrator
	    $userGroup[24]  = 'all';

	    //25 super administrator
	    $userGroup[25]  = 'all';
	}

	//getting all acl rules.
	$query  = 'SELECT `id` FROM `#__discuss_acl` ORDER BY `id` ASC';
	$db->setQuery($query);
	$aclTemp  	= $db->loadResultArray();

	$aclRules   			= array();
	$aclRulesAllEnabled   	= array();
	//do not use array_fill_keys for lower php compatibility. use old-fashion way. sigh.
	foreach($aclTemp as $item)
	{
	    $aclRules[$item] 			= 0;
	    $aclRulesAllEnabled[$item]	= 1;
	}

	$mainQuery  = array();
	foreach($userGroup as $uKey => $uGroup)
	{
	    $query  = 'SELECT COUNT(1) FROM `#__discuss_acl_group` WHERE `content_id` = ' . $db->Quote($uKey);
	    $query  .= ' AND `type` = ' . $db->Quote('group');

	    $db->setQuery($query);
	    $result = $db->loadResult();

	    if(empty($result))
	    {
	        $udAcls  = array();

	        if( is_array($uGroup))
	        {
	            $udAcls	= $aclRules;

	            foreach($uGroup as $uAcl)
	            {
	                $udAcls[$uAcl] = 1;
	            }
	        }
	        else if($uGroup == 'all')
	        {
	            $udAcls = $aclRulesAllEnabled;
	        }

	        foreach($udAcls as $key	=> $value)
	        {
	            $str    		= '(' . $db->Quote($uKey) . ', ' . $db->Quote($key) . ', ' . $db->Quote($value) . ', ' . $db->Quote('group') .')';
	            $mainQuery[]    = $str;
	        }
	    }//end if empty
	}//end foreach usergroup

	if(! empty($mainQuery))
	{
		$query  = 'INSERT INTO `#__discuss_acl_group` (`content_id`, `acl_id`, `status`, `type`) VALUES ';
		$query  .= implode(',', $mainQuery);

		$db->setQuery($query);
		$db->query();

		if($db->getErrorNum())
		{
			return false;
		}
	}

	return true;
}

function truncateACLTable()
{
	$db	= JFactory::getDBO();

	$query 	= "TRUNCATE TABLE " . $db->nameQuote('#__discuss_acl');
	$db->setQuery( $query );
	$db->query();

	if($db->getErrorNum())
	{
		return false;
	}

	return true;
}

function copyMediaFiles($sourcePath)
{
	jimport('joomla.filesystem.file');
	jimport('joomla.filesystem.folder');

	$mediaSource	= $sourcePath.DS.'media';
	$mediaDestina	= JPATH_ROOT.DS.'media';

	if (! JFolder::copy($mediaSource, $mediaDestina, '', true) )
	{
		return false;
	}

	return true;
}
