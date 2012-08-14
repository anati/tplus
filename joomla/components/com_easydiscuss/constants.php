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

// Root path
define( 'DISCUSS_ROOT' , JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' );

// Backend path
define( 'DISCUSS_ADMIN_ROOT' , JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_easydiscuss' );

// Assets path
define( 'DISCUSS_ASSETS' , DISCUSS_ROOT . DS . 'assets' );

// Assets path
define( 'DISCUSS_HELPERS' , DISCUSS_ROOT . DS . 'helpers' );

// Controllers path
define( 'DISCUSS_CONTROLLERS' , DISCUSS_ROOT . DS . 'controllers' );

// Libraries path
define( 'DISCUSS_CLASSES' , DISCUSS_ROOT . DS . 'classes' );

// Models path
define( 'DISCUSS_MODELS' , DISCUSS_ROOT . DS . 'models' );

// Tables path
define( 'DISCUSS_TABLES' , DISCUSS_ADMIN_ROOT . DS . 'tables' );

// Themes path
define( 'DISCUSS_THEMES' , DISCUSS_ROOT . DS . 'themes' );

// Admistrator path
define( 'DISCUSS_ADMIN' , JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_easydiscuss' );

// Toolbars path
define( 'DISCUSS_TOOLBARS' , DISCUSS_ADMIN_ROOT . DS . 'assets' . DS . 'images' . DS . 'toolbar' );

// Spinner path
define( 'DISCUSS_SPINNER' , rtrim(JURI::root(), '/') . '/components/com_easydiscuss/assets/images/loading.gif' );

// Asset path
define( 'DISCUSS_ASSETS_PATH' , rtrim(JURI::root(), '/') . '/components/com_easydiscuss/assets/' );
define( 'DISCUSS_ADMIN_ASSETS_PATH' , rtrim(JURI::root(), '/') . '/administrator/components/com_easydiscuss/assets/' );

// Updates server
define( 'DISCUSS_UPDATES_SERVER' , 'stackideas.com' );
define( 'DISCUSS_POWERED_BY' , '<div style="text-align: center; padding: 20px 0;"><a href="http://stackideas.com">Powered by EasyDiscuss for Joomla!</a></div>');

// Privacy
define( 'DISCUSS_PRIVACY_PUBLIC'		, '0');
define( 'DISCUSS_PRIVACY_PRIVATE'		, '1');
define( 'DISCUSS_PRIVACY_ACL'			, '2');

// Filters
define( 'DISCUSS_FILTER_ALL' 			, 'all' );
define( 'DISCUSS_FILTER_PUBLISHED' 		, 'published' );
define( 'DISCUSS_FILTER_UNPUBLISHED' 	, 'unpublished' );

// Featured posts
define( 'DISCUSS_MAX_FEATURED_POST' 	, '3' );

// Discussion types
define( 'DISCUSS_QUESTION_TYPE' 	, 'questions' );
define( 'DISCUSS_REPLY_TYPE' 		, 'replies' );
define( 'DISCUSS_USERQUESTIONS_TYPE', 'userquestions' );
define( 'DISCUSS_TAGS_TYPE'			, 'tags' );
define( 'DISCUSS_SEARCH_TYPE'		, 'search' );

// Post resolve status
define( 'DISCUSS_ENTRY_RESOLVED'	, 1 );
define( 'DISCUSS_ENTRY_UNRESOLVED'	, 0 );

// Notification queue types
define( 'DISCUSS_QUEUE_INFO'	, 'info' );
define( 'DISCUSS_QUEUE_ERROR'	, 'error' );

// Path for media's
define( 'DISCUSS_MEDIA_PATH'	, JPATH_ROOT . DS . 'media' . DS . 'com_easydiscuss' );

// post status ID
define( 'DISCUSS_ID_UNPUBLISHED'	, 0 );
define( 'DISCUSS_ID_PUBLISHED'		, 1 );
define( 'DISCUSS_ID_SCHEDULED'		, 2 );
define( 'DISCUSS_ID_DRAFT'			, 3 );
define( 'DISCUSS_ID_PENDING'		, 4 );

// Avatar sizes
define( 'DISCUSS_AVATAR_LARGE_WIDTH' , 160 );
define( 'DISCUSS_AVATAR_LARGE_HEIGHT' , 160 );
define( 'DISCUSS_AVATAR_THUMB_WIDTH' , 60 );
define( 'DISCUSS_AVATAR_THUMB_HEIGHT' , 60 );

// Category
define( 'DISCUSS_CATEGORY_PARENT' , 0 );
define( 'DISCUSS_CATEGORY_ACL_ACTION_SELECT' , 1 );
define( 'DISCUSS_CATEGORY_ACL_ACTION_VIEW' , 2 );
define( 'DISCUSS_CATEGORY_ACL_ACTION_REPLY' , 3 );


// Notifications constants
define( 'DISCUSS_NOTIFICATIONS_MENTIONED' , 'mention' );
define( 'DISCUSS_NOTIFICATIONS_REPLY'	, 'reply' );
define( 'DISCUSS_NOTIFICATIONS_RESOLVED' , 'resolved' );
define( 'DISCUSS_NOTIFICATIONS_ACCEPTED' , 'accepted' );
define( 'DISCUSS_NOTIFICATIONS_FEATURED' , 'featured' );
define( 'DISCUSS_NOTIFICATIONS_COMMENT'	, 'comment' );
define( 'DISCUSS_NOTIFICATIONS_PROFILE'	, 'profile' );
define( 'DISCUSS_NOTIFICATIONS_BADGE'	, 'badge' );
define( 'DISCUSS_NOTIFICATIONS_LOCKED'		, 'locked' );
define( 'DISCUSS_NOTIFICATIONS_UNLOCKED'	, 'unlocked' );
define( 'DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION'	, 'likes-discussion' );
define( 'DISCUSS_NOTIFICATIONS_LIKES_REPLIES'		, 'likes-replies' );
define( 'DISCUSS_NOTIFICATION_READ' , 0 );
define( 'DISCUSS_NOTIFICATION_NEW'	, 1 );

// Point systems
define( 'DISCUSS_POINTS_NEW_DISCUSSION'		, 'discussion.new' );
define( 'DISCUSS_POINTS_DELETE_DISCUSSION'	, 'discussion.delete' );
define( 'DISCUSS_POINTS_NEW_AVATAR'			, 'avatar.new' );
define( 'DISCUSS_POINTS_UPDATE_AVATAR'		, 'avatar.update' );
define( 'DISCUSS_POINTS_NEW_REPLY'			, 'reply.new' );
define( 'DISCUSS_POINTS_DELETE_REPLY'		, 'reply.delete' );
define( 'DISCUSS_POINTS_NEW_COMMENT'		, 'comment.new' );
define( 'DISCUSS_POINTS_DELETE_COMMENT'		, 'comment.delete' );

// Badges
define( 'DISCUSS_BADGES_PATH'		, JPATH_ROOT . DS . 'media' . DS . 'com_easydiscuss' . DS . 'badges' );
define( 'DISCUSS_BADGES_URI'		, rtrim( JURI::root() , '/') . '/media/com_easydiscuss/badges' );
define( 'DISCUSS_BADGES_DEFAULT'	, DISCUSS_BADGES_PATH . DS . 'default' );
define( 'DISCUSS_BADGES_UPLOADED'	, DISCUSS_BADGES_PATH . DS . 'uploaded' );
define( 'DISCUSS_BADGES_FAVICON_WIDTH' , 16 );
define( 'DISCUSS_BADGES_FAVICON_HEIGHT', 16 );

//
define( 'DISCUSS_HISTORY_BADGES' , 'badges' );
define( 'DISCUSS_HISTORY_POINTS' , 'points' );

define( 'DISCUSS_POSTER_GUEST' , 'guest' );
define( 'DISCUSS_POSTER_MEMBER', 'member' );