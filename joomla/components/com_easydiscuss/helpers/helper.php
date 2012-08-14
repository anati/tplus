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

jimport('joomla.filesystem.file' );
jimport('joomla.filesystem.folder' );
jimport('joomla.application.component.view');
jimport('joomla.html.parameter');
jimport('joomla.access.access');
jimport('joomla.application.component.model');

require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'constants.php' );
require_once( DISCUSS_ROOT . DS . 'views.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );
require_once( DISCUSS_HELPERS . DS . 'filter.php' );
require_once( DISCUSS_HELPERS . DS . 'parser.php' );
require_once( DISCUSS_HELPERS . DS . 'date.php' );
require_once( DISCUSS_HELPERS . DS . 'events.php' );
require_once( DISCUSS_HELPERS . DS . 'ranks.php' );
require_once( DISCUSS_CLASSES . DS . 'themes.php' );

class DiscussHelper
{
	/**
	 * Retrieve specific helper objects.
	 *
	 * @param	string	$helper	The helper class . Class name should be the same name as the file. e.g EasyDiscussXXXHelper
	 * @return	object	Helper object.
	 **/
	public static function getHelper( $helper )
	{
		static $obj	= array();

		if( !isset( $obj[ $helper ] ) )
		{
			$file	= DISCUSS_HELPERS . DS . JString::strtolower( $helper ) . '.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$class	= 'Discuss' . ucfirst( $helper ) . 'Helper';

				$obj[ $helper ]	= new $class();
			}
			else
			{
				$obj[ $helper ]	= false;
			}
		}

		return $obj[ $helper ];
	}

	/*
	 * type - string - info | warning | error
	 */

	public static function setMessageQueue($message, $type = 'info')
	{
		$session 	= JFactory::getSession();

		$msgObj = new stdClass();
		$msgObj->message    = $message;
		$msgObj->type       = strtolower($type);

		//save messsage into session
		$session->set('discuss.message.queue', $msgObj, 'DISCUSS.MESSAGE');

	}

	public static function getMessageQueue()
	{
		$session 	= JFactory::getSession();
		$msgObj 	= $session->get('discuss.message.queue', null, 'DISCUSS.MESSAGE');

		//clear messsage into session
		$session->set('discuss.message.queue', null, 'DISCUSS.MESSAGE');

		return $msgObj;
	}

	public static function getAlias( $title, $type='post', $id='0' )
	{
		$alias	= DiscussHelper::permalinkSlug($title);


		// Make sure no such alias exists.
		$i	= 1;
		while( DiscussRouter::_isAliasExists( $alias, $type, $id ) )
		{
			$alias	= DiscussHelper::permalinkSlug( $title ) . '-' . $i;
			$i++;
		}

		return $alias;
	}

	public static function permalinkSlug( $string )
	{
		$config		= DiscussHelper::getConfig();
		if ($config->get( 'main_sef_unicode' ))
		{
			//unicode support.
			$alias  = DiscussHelper::permalinkUnicodeSlug($string);
		}
		else
		{
	        // Replace accents to get accurate string
	        $alias  = DiscussRouter::replaceAccents( $string );

			$alias	= JFilterOutput::stringURLSafe( $alias );

			// check if anything return or not. If not, then we give a date as the alias.
			if(trim(str_replace('-','',$alias)) == '')
			{
				$datenow	= JFactory::getDate();
				$alias 		= $datenow->toFormat("%Y-%m-%d-%H-%M-%S");
			}
		}
		return $alias;
	}

	public static function permalinkUnicodeSlug( $string )
	{
		$slug	= '';
		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$slug	= JFilterOutput::stringURLUnicodeSlug($string);
		}
		else
		{
			//replace double byte whitespaces by single byte (Far-East languages)
			$slug = preg_replace('/\xE3\x80\x80/', ' ', $string);


			// remove any '-' from the string as they will be used as concatenator.
			// Would be great to let the spaces in but only Firefox is friendly with this

			$slug = str_replace('-', ' ', $slug);

			// replace forbidden characters by whitespaces
			$slug = preg_replace( '#[:\#\*"@+=;!&\.%()\]\/\'\\\\|\[]#',"\x20", $slug );

			//delete all '?'
			$slug = str_replace('?', '', $slug);

			//trim white spaces at beginning and end of alias, make lowercase
			$slug = trim(JString::strtolower($slug));

			// remove any duplicate whitespace and replace whitespaces by hyphens
			$slug =preg_replace('#\x20+#','-', $slug);
		}
		return $slug;
	}

	public static function getNotification()
	{
		static $notify = false;

		if( !$notify )
		{
			require_once( DISCUSS_CLASSES . DS . 'notification.php' );
			$notify	= new DNotification();
		}
		return $notify;

	}

	public static function getMailQueue()
	{
		static $mailq = false;

		if( !$mailq )
		{
			require_once( DISCUSS_CLASSES . DS . 'mailqueue.php' );

			$mailq	= new DMailQueue();
		}
		return $mailq;

	}

	public static function getSiteSubscriptionClass()
	{
		static $sitesubscriptionclass = false;

		if( !$sitesubscriptionclass )
		{
			require_once( DISCUSS_CLASSES . DS . 'subscription.php' );

			$sitesubscriptionclass	= new DiscussSubscription();
		}
		return $sitesubscriptionclass;
	}

	public static function getParser()
	{
		$parser		= JFactory::getXMLParser('Simple');

		$data		= new stdClass();

		// Get the xml file
		$site		= DISCUSS_UPDATES_SERVER;
		$xml		= 'stackideas.xml';
		$contents	= '';

		$handle		= @fsockopen( $site , 80, $errno, $errstr, 30);

		if( !$handle )
			return false;

		$out = "GET /$xml HTTP/1.1\r\n";
		$out .= "Host: $site\r\n";
		$out .= "Connection: Close\r\n\r\n";

		fwrite($handle, $out);

		$body		= false;

		while( !feof( $handle ) )
		{
			$return	= fgets( $handle , 1024 );

			if( $body )
			{
				$contents	.= $return;
			}

			if( $return == "\r\n" )
			{
				$body	= true;
			}
		}
		fclose($handle);

		$parser->loadString( $contents );

		return $parser;
	}

	public static function getLoginHTML( $returnURL )
	{
		$tpl	= new DiscussThemes();
		$tpl->set( 'return'	, base64_encode( $returnURL ) );

		return $tpl->fetch( 'ajax.login.php' );
	}

	public static function getLocalParser()
	{
		$parser		= JFactory::getXMLParser('Simple');

		$data		= new stdClass();

		$contents	= JFile::read( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_easydiscuss' . DS . 'easydiscuss.xml' );
		$parser->loadString( $contents );

		return $parser;
	}

	public static function getLocalVersion()
	{
		$parser	= DiscussHelper::getLocalParser();

		if( !$parser )
			return false;

		$element	= $parser->document->getElementByPath( 'version' );
		return $element->data();
	}

	public static function getVersion()
	{
		$parser	= DiscussHelper::getParser();

		if( !$parser )
			return false;

		$element	= $parser->document->getElementByPath( 'discuss/version' );
		return $element->data();
	}

	public static function getRecentNews()
	{
		$parser	= DiscussHelper::getParser();

		if( !$parser )
			return false;

		$items	= $parser->document->getElementByPath('discuss/news');

		$news	= array();

		foreach($items->children() as $item)
		{
			$element	= $item->getElementByPath( 'title' );
			$obj		= new stdClass();
			$obj->title	= $element->data();
			$element	= $item->getElementByPath( 'description' );
			$obj->desc	= $element->data();
			$element	= $item->getElementByPath( 'pubdate' );
			$obj->date	= $element->data();
			$news[]		= $obj;
		}

		return $news;
	}

	public static function getConfig()
	{
		static $config	= null;

		if( is_null( $config ) )
		{
			//load default ini data first
			$ini		= JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_easydiscuss' . DS . 'configuration.ini';
			$raw		= JFile::read($ini);
			$config	= new JParameter($raw);

			//get config stored in db
			$dbConfig	= DiscussHelper::getTable( 'Configs' );
			$dbConfig->load( 'config' );

			if( DiscussHelper::getJoomlaVersion() == '1.7' )
			{
				$config->bind( $dbConfig->params , 'ini' );
			}
			else
			{
				$config->bind( $dbConfig->params );
			}

		}

		return $config;
	}

	/*
	 * Method used to determine whether the user a guest or logged in user.
	 * return : boolean
	 */
	public static function isLoggedIn()
	{
		$my	= JFactory::getUser();
		$loggedIn	= (empty($my) || $my->id == 0) ? false : true;
		return $loggedIn;
	}

	public static function isSiteAdmin($userId = null)
	{
		$my	= JFactory::getUser( $userId );

		$admin  = false;
		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$admin	= $my->authorise('core.admin');
		}
		else
		{
			$admin	= $my->usertype == 'Super Administrator' || $my->usertype == 'Administrator' ? true : false;
		}
		return $admin;
	}

	public static function isMine($uid)
	{
		$my	= JFactory::getUser();

		if($my->id == 0)
			return false;

		if( empty($uid) )
			return false;

		$mine	= $my->id == $uid ? 1 : 0;
		return $mine;
	}


	public static function getUserId( $username )
	{
		static $userids = array();

		if( !isset( $userids[ $username ] ) || empty($userids[$username]) )
		{
			$db		= JFactory::getDBO();

			// first get from user alias
			$query	= 'SELECT `id` FROm `#__discuss_users` WHERE `alias` = ' . $db->quote( $username );
			$db->setQuery( $query );
			$userid	= $db->loadResult();

			// then get from user nickname
			if (!$userid)
			{
				$query	= 'SELECT `id` FROm `#__discuss_users` WHERE `nickname` = ' . $db->quote( $username );
				$db->setQuery( $query );
				$userid	= $db->loadResult();
			}

			// then get from username
			if (!$userid)
			{
				$query	= 'SELECT `id` FROM `#__users` WHERE `username`=' . $db->quote( $username );
				$db->setQuery( $query );

				$userid	= $db->loadResult();
			}

			$userids[$username] = $userid;
		}

		return $userids[$username];
	}

	public static function loadHeaders()
	{
		static $loaded = false;

		if( !$loaded )
		{
			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				$uri 		= JFactory::getURI();
				$language	= $uri->getVar( 'lang' , 'none' );

				$JFilter	= JFilterInput::getInstance();
				$language	= $JFilter->clean($language, 'CMD');

				$app		= JFactory::getApplication();
				$config		= JFactory::getConfig();
				$router		= $app->getRouter();
				$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easydiscuss&lang=' . $language;

				if( $router->getMode() == JROUTER_MODE_SEF && JPluginHelper::isEnabled("system","languagefilter") )
				{
					$rewrite	= $config->get('sef_rewrite');

					$base		= str_ireplace( JURI::root( true ) , '' , $uri->getPath() );
					$path		=  $rewrite ? $base : JString::substr( $base , 10 );
					$path		= JString::trim( $path , '/' );
					$parts		= explode( '/' , $path );

					if( $parts )
					{
						// First segment will always be the language filter.
						$language	= reset( $parts );
					}
					else
					{
						$language	= 'none';
					}

					if( $rewrite )
					{
						$url		= rtrim( JURI::root() , '/' ) . '/' . $language . '/?option=com_easydiscuss';
						$language	= 'none';
					}
					else
					{
						$url		= rtrim( JURI::root() , '/' ) . '/index.php/' . $language . '/?option=com_easydiscuss';
					}
				}
				// $url		.= '&' . JUtility::getToken() . '=1';
			}
			else
			{

				$url		= rtrim( JURI::root() , '/' ) . '/index.php?option=com_easydiscuss';
				// $url		.= '&' . JUtility::getToken() . '=1';
			}

			$config		= DiscussHelper::getConfig();

			$document	= JFactory::getDocument();
			$ajaxData	=
"/*<![CDATA[*/
	var discuss_site 	= '" . $url . "';
	var spinnerPath		= '" . DISCUSS_SPINNER . "';
	var lang_direction	= '" . $document->direction . "';
	var discuss_featured_style	= '" . $config->get('layout_featuredpost_style', 0) . "';
/*]]>*/";
			require_once( DISCUSS_HELPERS . DS . 'loader.php' );

			$document->addScriptDeclaration( $ajaxData );

			// TODO: Double check this because something is broken.
			// $debugjs = (bool) JRequest::getVar('discuss_debugjs', $config->get('discuss_debugjs'));
			$debugjs = true;

			ob_start();
				$discussMediaPath = JPATH_ROOT . DS . 'media' . DS . 'com_easydiscuss' . DS . 'js';
				include($discussMediaPath . DS . 'easydiscuss.js');
				$output = ob_get_contents();
			ob_end_clean();

			$document->addScriptDeclaration( "/*<![CDATA[*/ $output /*]]>*/ " );

			$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_easydiscuss/assets/css/common.css' );

			$loaded		= true;
		}
		return $loaded;
	}

	public static function loadEditor()
	{
		$config = DiscussHelper::getConfig();

		$out   = '<link rel="stylesheet" type="text/css" href="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/assets/vendors/markitup/skins/simple/style.css" />' . "\n";
		$out  .= '<link rel="stylesheet" type="text/css" href="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/assets/vendors/markitup/sets/bbcode/style.css" />' . "\n";

		if( $config->get( 'layout_editor' ) != 'bbcode' )
		{
			$out  .= '<link rel="stylesheet" type="text/css" href="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/assets/css/editor-mce.css" />' . "\n";
		}

		$out  .= '<script type="text/javascript" src="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/assets/vendors/markitup/jquery.markitup.pack.js"></script>' . "\n";
		$out  .= '<script type="text/javascript" src="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/assets/vendors/markitup/sets/bbcode/set.js"></script>' . "\n";

		$document	= JFactory::getDocument();
		if($document->_type == 'html')
		{
			$document->addCustomTag($out);
		}
	}


	public static function loadThemeCss($theme = 'default')
	{
		$app        = JFactory::getApplication();
		$override   = JPATH_ROOT . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . 'com_easydiscuss' . DS . 'css' . DS . 'style.css';
		$doc        = JFactory::getDocument();
		$config     = DiscussHelper::getConfig();
		$url        = '';

		if( JFile::exists( $override ) )
		{
			$url    = rtrim( JURI::root() , '/' ) . '/templates/' . $app->getTemplate() . '/html/com_easydiscuss/css/style.css';
		}
		else
		{
			$theme   = $config->get( 'layout_theme' );

			if( $theme != 'default' )
			{
				$url    = rtrim( JURI::root() , '/' ) . '/components/com_easydiscuss/themes/' . strtolower( $theme ) . '/css/style.css';
			}
		}

		if( $doc->getType() == 'html' )
		{
			// Always attach default css first.
			$doc->addStyleSheet( rtrim( JURI::root() , '/' ) . '/components/com_easydiscuss/themes/default/css/style.css' );

			if( !empty( $url ) )
			{
				$doc->addStyleSheet( $url );
			}

			return true;
		}
		return false;
	}


	public static function loadString( $view )
	{
		$document = JFactory::getDocument();

		$jView  = new JView();

		$string = '';

		switch( $view )
		{
			case 'post':
				$string = '
					var langEmptyTitle			= "'.$jView->escape(JText::_('COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY')).'";
					var langEmptyContent		= "'.$jView->escape(JText::_('COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY')).'";
					var langConfirmDeleteReply	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_REPLY')).'";
					var langConfirmDeleteReplyTitle	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_REPLY_TITLE')).'";

					var langConfirmDeleteComment		= "'.$jView->escape(JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_COMMENT')).'";
					var langConfirmDeleteCommentTitle	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_COMMENT_TITLE')).'";

					var langPostTitle	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE')).'";
					var langEmptyTag	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_POST_EMPTY_TAG_NOT_ALLOWED')).'";
					var langTagSepartor	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_POST_TAGS_SEPERATE')).'";
					var langTagAlreadyAdded	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_TAG_ALREADY_ADDED')).'";

					var langEmptyCategory	= "'.$jView->escape(JText::_('COM_EASYDISCUSS_POST_CATEGORY_IS_EMPTY')).'";
				';
		}

		$document->addScriptDeclaration($string);
	}


	public static function getDurationString( $dateTimeDiffObj )
	{
		$lang       = JFactory::getLanguage();
		$lang->load( 'com_easydiscuss' , JPATH_ROOT );

		$data 		= $dateTimeDiffObj;
		$returnStr  = '';

		if($data->daydiff <= 0)
		{
			//today. so we need to get the minutes
			// [0] == hours
			// [1] == minutes
			// [2] == seconds
// 			if( !empty( $data->timediff ) )
			{
				$timeDate   = explode(':', $data->timediff);

				if(intval($timeDate[0], 10) >= 1)
				{
					$returnStr  = JText::sprintf('COM_EASYDISCUSS_HOURS_AGO', intval($timeDate[0], 10));
				} else if(intval($timeDate[1], 10) >= 2) {
					$returnStr  = JText::sprintf('COM_EASYDISCUSS_MINUTES_AGO', intval($timeDate[1], 10));
				} else {
					$returnStr  = JText::_('COM_EASYDISCUSS_LESS_THAN_A_MINUTE_AGO');
				}
			}
		}
		else if(($data->daydiff >= 1) && ($data->daydiff < 7) )
		{
			$returnStr  = JText::sprintf('COM_EASYDISCUSS_DAYS_AGO', $data->daydiff);
		}
		else if($data->daydiff >= 7 && $data->daydiff <= 30)
		{
			$returnStr = (intval($data->daydiff/7, 10) == 1 ? JText::_('COM_EASYDISCUSS_ONE_WEEK_AGO') : JText::sprintf('COM_EASYDISCUSS_WEEKS_AGO', intval($data->daydiff/7, 10)));
		}
		else
		{
			$returnStr  = JText::_('COM_EASYDISCUSS_MORE_THAN_A_MONTH_AGO');
		}

		return $returnStr;
	}

	/*
	 * Function to determine a post should minimise or not.
	 * return true or false
	 */

	public static function toMinimizePost( $count )
	{
		$config 	= DiscussHelper::getConfig();

		$breakPoint = $config->get( 'layout_autominimisepost' );
		$minimize   = ($count <= $breakPoint && $breakPoint != 0 ) ? true : false;

		return $minimize;
	}

	public static function storeSession($data, $key, $ns = 'com_easydiscuss')
	{
		$mySess	= JFactory::getSession();
		$mySess->set($key, $data, $ns);
	}

	public static function getSession($key, $ns = 'com_easydiscuss')
	{
		$data   = null;

		$mySess = JFactory::getSession();
		if($mySess->has($key, $ns))
		{
			$data   = $mySess->get($key, '', $ns);
			$mySess->clear($key, $ns);
			return $data;
		}
		else
		{
			return $data;
		}
	}

	public static function addLikes($contentId, $type, $userId = '0')
	{
		if($userId == '0')
		{
			$user   = JFactory::getUser();
			$userId = $user->id;
		}

		$date   = JFactory::getDate();
		$likes	= DiscussHelper::getTable( 'Likes' );

		$params   = array();
		$params['type'] 		= $type;
		$params['content_id'] 	= $contentId;
		$params['created_by'] 	= $userId;
		$params['type'] 		= $type;
		$params['created'] 		= $date->toMySQL();

		$likes->bind($params);

		// check if the user already likes or not. if yes, then return the id.
		$id 	=  $likes->exists();
		if( $id !== false )
		{
			return $id;
		}

		$likes->store();

		if($type == 'post')
		{
			// now update the post
			$db     = JFactory::getDBO();
			$query  = 'UPDATE `#__discuss_posts` SET `num_likes` = `num_likes` + 1';
			$query  .= ' WHERE `id` = ' . $db->Quote($contentId);
			$db->setQuery($query);
			$db->query();
		}

		return $likes->id;
	}

	public static function removeLikes($likesId)
	{
		$likes	= DiscussHelper::getTable( 'Likes' );
		$likes->load($likesId);

		$contentId  = $likes->content_id;
		$type       = $likes->type;

		if($type == 'post')
		{
			// now update the post by decrement the count
			$db     = JFactory::getDBO();
			$query  = 'UPDATE `#__discuss_posts` SET `num_likes` = `num_likes` - 1';
			$query  .= ' WHERE `id` = ' . $db->Quote($contentId);
			$db->setQuery($query);
			$db->query();
		}

		$likes->delete();
		return true;
	}

	public static function getLikesAuthors($type, $contentId, $userId)
	{
		$db     = JFactory::getDBO();
		$config = DiscussHelper::getConfig();

		$displayFormat  = $config->get('layout_nameformat');
		$displayName    = '';

		switch($displayFormat){
			case "name" :
				$displayName = 'a.name';
				break;
			case "username" :
			default :
				$displayName = 'a.username';
				break;
		}

		$query	= 'select a.id as `user_id`, b.id, ' . $displayName . ' as `displayname`';
		$query	.= ' FROM ' . $db->nameQuote( '#__users' ) . ' as a';
		$query	.= '  inner join ' . $db->nameQuote( '#__discuss_likes' ) . ' as b';
		$query	.= '    on a.id = b.created_by';
		$query	.= ' where b.content_id = ' . $db->Quote($contentId);
		$query	.= ' and b.`type` = '. $db->Quote($type);
		$query  .= ' order by b.id desc';

		$db->setQuery($query);

		$list   = $db->loadObjectList();

		if(count($list) <= 0)
		{
			return '';
		}

		$names  = array();

		for($i = 0; $i < count($list); $i++)
		{

			if($list[$i]->user_id == $userId)
			{
				array_unshift($names, JText::_('COM_EASYDISCUSS_YOU') );
			}
			else
			{
				$names[]    = '<a href="' . DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $list[$i]->user_id ) . '">' . $list[$i]->displayname . '</a>';
			}
		}

		$max    = 3;
		$total  = count($names);
		$break	= 0;

		if($total == 1)
		{
			$break  = $total;
		}
		else
		{
			if($max >= $total)
			{
				$break  = $total - 1;
			}
			else if($max < $total)
			{
				$break  = $max;
			}
		}

		$main   = array_slice($names, 0, $break);
		$remain = array_slice($names, $break);

		$stringFront    = implode(", ", $main);
		$returnString   = '';

		if(count($remain) > 1)
		{
			$returnString   = JText::sprintf('COM_EASYDISCUSS_AND_OTHERS_LIKE_THIS', $stringFront, count($remain));
		}
		else if(count($remain) == 1)
		{
			$returnString   = JText::sprintf('COM_EASYDISCUSS_AND_LIKE_THIS', $stringFront, $remain[0]);
		}
		else
		{
			if( $list[0]->user_id == JFactory::getUser()->id )
			{
				$returnString   = JText::sprintf('COM_EASYDISCUSS_LIKE_THIS', $stringFront);
			}
			else
			{
				$returnString   = JText::sprintf('COM_EASYDISCUSS_LIKES_THIS', $stringFront);
			}
		}

		return '<i></i>' . $returnString;
	}

	public static function isNew( $noofdays )
	{
		$config = DiscussHelper::getConfig();
		$isNew  = ($noofdays <= $config->get('layout_daystostaynew', 7)) ? true : false;

		return $isNew;
	}

	public static function getExternalLink($link)
	{
		$uri	= JURI::getInstance();
		$domain	= $uri->toString( array('scheme', 'host', 'port'));

		return $domain . '/' . ltrim(DiscussRouter::_( $link, false ), '/');
	}

	public static function uploadAvatar( $profile, $isFromBackend = false )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my 		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		$avatar_config_path = $config->get('main_avatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = JString::str_ireplace('/', DS, $avatar_config_path);

		$upload_path		= JPATH_ROOT.DS.$avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;
		$file 				= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER' ) , 'error');
					$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false) );
				}
				else
				{
					//from backend
					$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::_( 'COM_EASYDISCUSS_FAILED_TO_CREATE_UPLOAD_FOLDER' ), 'error' );
				}
				return;
			}
		}

		//makesafe on the file
		$date           = JFactory::getDate();
		$file_ext       = DiscussImageHelper::getFileExtention($file['name']);
		$file['name']	= $my->id . '_' . JFile::makeSafe(md5($file['name'].$date->toMySQL())) . '.' . $file_ext;


		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path.DS.$file['name'];
			$target_file 			= JPath::clean($target_file_path . DS. JFile::makeSafe($file['name']));
			$original 				= JPath::clean($target_file_path . DS. 'original_' . JFile::makeSafe($file['name']));

			$isNew                  = false;

			//include_once(JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'helpers'.DS.'image.php');
			require_once( DISCUSS_HELPERS . DS . 'image.php' );
			require_once( DISCUSS_CLASSES . DS . 'simpleimage.php' );

			if (! DiscussImageHelper::canUpload( $file, $err ))
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_( $err ) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( $file['error'] , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar 	= $profile->avatar;
			$tempAvatar	= '';
			if( $oldAvatar != 'default.png')
			{
				$session   = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt 	= JFile::getExt(JPath::clean($target_file_path.DS.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DS . $sessionId . '.' . $fileExt);

				// Test if old original file exists.
				if( JFile::exists( $target_file_path . DS . 'original_' . $oldAvatar) )
				{
					JFile::delete( $target_file_path . DS . 'original_' . $oldAvatar );
				}

				JFile::move($target_file_path.DS.$oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != 'default.png')
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DS.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != 'default.png')
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DS.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=profile', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=users', false), JText::sprintf('COM_EASYDISCUSS_FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth  = $config->get('layout_avatarwidth', 160);
			$configImageHeight = $config->get('layout_avatarheight', 160);

			$originalImageWidth		= $config->get( 'layout_originalavatarwidth' , 400 );
			$originalImageHeight 	= $config->get( 'layout_originalavatarheight' , 400 );

			// Copy the original image files over
			$image = new SimpleImage();
			$image->load($file['tmp_name']);
			$image->resizeToFill( $originalImageWidth , $originalImageHeight );
			$image->save($original, $image->image_type);
			unset( $image );

			$image = new SimpleImage();
			$image->load($file['tmp_name']);
			$image->resizeToFill( $configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != 'default.png')
			{
				//if(JFile::exists( JPATH_ROOT.DS.$oldAvatar ))
				if(JFile::exists( $tempAvatar ))
				{
					//JFile::delete( JPATH_ROOT.DS.$oldAvatar );
					JFile::delete( $tempAvatar );
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return 'default.png';
		}

	}

	public static function uploadCategoryAvatar( $category, $isFromBackend = false )
	{
		return DiscussHelper::uploadMediaAvatar( 'category', $category, $isFromBackend);
	}

	public static function uploadMediaAvatar( $mediaType, $mediaTable, $isFromBackend = false )
	{
		jimport('joomla.utilities.error');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$my 		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();

		//$acl		= DiscussACLHelper::getRuleSet();


		// required params
		$layout_type    = ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$view_type   	= ($mediaType == 'category') ? 'categories' : 'teamblogs';
		$default_avatar_type   	= ($mediaType == 'category') ? 'default_category.png' : 'default_team.png';



		if(! $isFromBackend && $mediaType == 'category')
		{
			$url  = 'index.php?option=com_easydiscuss&view=categories';
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_UPLOAD_AVATAR') , 'warning');
			$mainframe->redirect(DiscussRouter::_($url, false));
		}

		$avatar_config_path = ($mediaType == 'category') ? $config->get('main_categoryavatarpath') : $config->get('main_teamavatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = str_replace('/', DS, $avatar_config_path);

		$upload_path		= JPATH_ROOT.DS.$avatar_config_path;
		$rel_upload_path	= $avatar_config_path;

		$err				= null;
		$file 				= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		//check whether the upload folder exist or not. if not create it.
		if(! JFolder::exists($upload_path))
		{
			if(! JFolder::create( $upload_path ))
			{
				// Redirect
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER') , 'error');
					$this->setRedirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false) );
				}
				else
				{
					//from backend
					$this->setRedirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), JText::_('COM_EASYDISCUSS_IMAGE_UPLOADER_FAILED_TO_CREATE_UPLOAD_FOLDER'), 'error' );
				}
				return;
			}
			else
			{
				// folder created. now copy index.html into this folder.
				if(! JFile::exists( $upload_path . DS . 'index.html' ) )
				{
					$targetFile = JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'index.html';
					$destFile   = $upload_path . DS .'index.html';

					if( JFile::exists( $targetFile ) )
						JFile::copy( $targetFile, $destFile );
				}
			}
		}

		//makesafe on the file
		$file['name']	= $mediaTable->id . '_' . JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			$target_file_path		= $upload_path;
			$relative_target_file	= $rel_upload_path.DS.$file['name'];
			$target_file 			= JPath::clean($target_file_path . DS. JFile::makeSafe($file['name']));
			$isNew                  = false;

			//include_once(JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'helpers'.DS.'image.php');
			require_once( DISCUSS_HELPERS . DS . 'image.php' );
			require_once( DISCUSS_CLASSES . DS . 'simpleimage.php' );

			if (! DiscussImageHelper::canUpload( $file, $err ))
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::_( $err ) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories'), JText::_( $err ), 'error');
				}
				return;
			}

			if (0 != (int)$file['error'])
			{
				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( $file['error'] , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), $file['error'], 'error');
				}
				return;
			}

			//rename the file 1st.
			$oldAvatar 	= (empty($mediaTable->avatar)) ? $default_avatar_type : $mediaTable->avatar;
			$tempAvatar	= '';
			if( $oldAvatar != $default_avatar_type)
			{
				$session   = JFactory::getSession();
				$sessionId = $session->getToken();

				$fileExt 	= JFile::getExt(JPath::clean($target_file_path.DS.$oldAvatar));
				$tempAvatar	= JPath::clean($target_file_path . DS . $sessionId . '.' . $fileExt);

				JFile::move($target_file_path.DS.$oldAvatar, $tempAvatar);
			}
			else
			{
				$isNew  = true;
			}

			if (JFile::exists($target_file))
			{
				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DS.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					DiscussHelper::setMessageQueue( JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			if (JFolder::exists($target_file))
			{

				if( $oldAvatar != $default_avatar_type)
				{
					//rename back to the previous one.
					JFile::move($tempAvatar, $target_file_path.DS.$oldAvatar);
				}

				if(! $isFromBackend)
				{
					//JError::raiseNotice(100, JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS',$relative_target_file));
					DiscussHelper::setMessageQueue( JText::sprintf('ERROR.FOLDER_ALREADY_EXISTS', $relative_target_file) , 'error');
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false));
				}
				else
				{
					//from backend
					$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=categories', false), JText::sprintf('ERROR.FILE_ALREADY_EXISTS', $relative_target_file), 'error');
				}
				return;
			}

			$configImageWidth  = DISCUSS_AVATAR_LARGE_WIDTH;
			$configImageHeight = DISCUSS_AVATAR_LARGE_HEIGHT;

			$image = new SimpleImage();
			$image->load($file['tmp_name']);
			$image->resize($configImageWidth, $configImageHeight);
			$image->save($target_file, $image->image_type);

			//now we update the user avatar. If needed, we remove the old avatar.
			if( $oldAvatar != $default_avatar_type)
			{
				if(JFile::exists( $tempAvatar ))
				{
					JFile::delete( $tempAvatar );
				}
			}

			return JFile::makeSafe( $file['name'] );
		}
		else
		{
			return $default_avatar_type;
		}

	}

	public static function wordFilter( $text )
	{
	    $config         = DiscussHelper::getConfig();

	    if( empty( $text ) )
			return $text;

		if( trim($text) == '')
			return $text;

		if($config->get('main_filterbadword', 1) && $config->get('main_filtertext', '') != '')
		{
			require_once( DISCUSS_HELPERS . DS . 'filter.php' );
			// filter out bad words.
			$bwFilter   	= new BadWFilter();
			$textToBeFilter = explode(',', $config->get('main_filtertext'));

			// lets do some AI here. for each string, if there is a space,
			// remove the space and make it as a new filter text.
			if( count($textToBeFilter) > 0 )
			{
			    $newFilterSet   = array();
				foreach( $textToBeFilter as $item)
				{
				    if( JString::stristr($item, ' ') !== false )
				    {
				        $newKeyWord 	= JString::str_ireplace(' ', '', $item);
				        $newFilterSet[] = $newKeyWord;
				    }
				} // foreach

				if( count($newFilterSet) > 0 )
				{
				    $tmpNewFitler   	= array_merge($textToBeFilter, $newFilterSet);
				    $textToBeFilter  	= array_unique($tmpNewFitler);
				}

			}//end if

			$bwFilter->strings	= $textToBeFilter;

			//to be filtered text
			$bwFilter->text     = $text;
			$new_text		 	= $bwFilter->filter();

			$text         		= $new_text;
		}

		return $text;
	}

	public static function formatPost($posts, $isSearch = false)
	{
		$config         = DiscussHelper::getConfig();

		if(! empty($posts) > 0)
		{
			$postModel		= JModel::getInstance( 'Posts' , 'EasyDiscussModel' );

			for($i = 0; $i < count($posts); $i++)
			{
				$row = $posts[$i];

				// set post owner
				$owner	= DiscussHelper::getTable( 'Profile' );
				$owner->load($row->user_id);

				if ( $row->user_id == 0 )
				{
					$owner->id		= 0;
					$owner->name	= 'Guest';
				}
				else
				{
					$owner->id		= $row->user_id;
					$owner->name	= $owner->getName();
				}

				$row->user		= $owner;
				$row->title		= self::wordFilter( $row->title );
				$row->content	= self::wordFilter( $row->content );

				// get total replies
				//$totalReplies = $postModel->getTotalReplies( $row->id );
				//$totalReplies 			= (!$isSearch) ? $row->num_replies : '0';
				$totalReplies 			= ( isset( $row->num_replies ) ) ? $row->num_replies : '0';
				$row->totalreplies		= $totalReplies;

				if ( $totalReplies > 0 )
				{
					// get last reply
					$lastReply	= $postModel->getLastReply( $row->id );
					if ( !empty( $lastReply ) )
					{
						$replier	= DiscussHelper::getTable( 'Profile' );
						$replier->load( $lastReply->user_id );

						$replier->poster_name	= ($lastReply->user_id) ? $replier->getName() : $lastReply->poster_name;
						$replier->poster_email  = ($lastReply->user_id) ? $replier->user->email : $lastReply->poster_email;

						$row->reply = $replier;
					}
				}


				//check whether the post is still withing the 'new' duration.
				$row->isnew = DiscussHelper::isNew($row->noofdays);

				//get post duration so far.
				$durationObj    = new stdClass();
				$durationObj->daydiff   = $row->daydiff;
				$durationObj->timediff   = $row->timediff;

				$row->duration  = DiscussHelper::getDurationString($durationObj);

				if( !$isSearch )
				{

					// get post tags
					if( !class_exists( 'EasyDiscussModelPostsTags' ) )
					{
						JLoader::import( 'poststags' , DISCUSS_ROOT . DS . 'models' );
					}
					$postsTagsModel	= JModel::getInstance( 'PostsTags' , 'EasyDiscussModel' );

					$tags = $postsTagsModel->getPostTags( $row->id );
					$row->tags = $tags;

					$row->polls			= $postModel->hasPolls( $row->id );
					$row->attachments	= $postModel->hasAttachments( $row->id , DISCUSS_QUESTION_TYPE );
				}
				else
				{
					$row->tags  		= '';
					$row->polls  		= '';
					$row->attachments  	= '';
				}

				if( !empty( $row->password ) && !DiscussHelper::hasPassword( $row ) )
				{
				    $tpl	= new DiscussThemes();
					$tpl->set( 'post' , $row );
					$row->content = $tpl->fetch( 'entry.password.php' );
				}
			}

		}

		return $posts;

	}

	public static function formatReplies( $result )
	{
		$config		= DiscussHelper::getConfig();

		if( !$result )
		{
			return $result;
		}
		$my			= JFactory::getUser();
		$replies    = array();

		foreach( $result as $row )
		{
			$response   = new stdClass();
			$reply		= DiscussHelper::getTable( 'Post' );
			$reply->bind( $row );

			if($row->user_id != 0)
			{
				$replier		 = JFactory::getUser( $row->user_id );
				$response->id	 = $replier->id;
				$response->name	 = $replier->name;
			}
			else
			{
				$response->id	 = '0';
				$response->name	 = 'Guest'; // TODO: user the poster_name
			}

			//load porfile info and auto save into table if user is not already exist in discuss's user table.
			$creator = DiscussHelper::getTable( 'Profile' );
			$creator->load( $response->id);

			$reply->user 			= $creator;
			$reply->content_raw		= $row->content;
			$reply->isVoted     	= $row->isVoted;
			$reply->total_vote_cnt  = $row->total_vote_cnt;

			$reply->title 			= DiscussHelper::wordFilter( $reply->title);
			$reply->content 		= DiscussHelper::wordFilter( $reply->content);

			$reply->content			= DiscussHelper::getHelper( 'String' )->escape( $reply->content );
			$reply->content			= Parser::bbcode( $reply->content );

			// Parse @username links.
			$reply->content 		= DiscussHelper::getHelper( 'String' )->nameToLink( $reply->content );

			// set for vote status
			$reply->voted			= $reply->hasVoted();

			// get total vote for this reply
			$reply->totalVote		= $reply->sum_totalvote;

			// get the 5 latest voters
			$voters					= DiscussHelper::getVoters($row->id);
			$reply->voters			= $voters->voters;
			$reply->shownVoterCount = $voters->shownVoterCount;

			// format created date by adding offset if any
			$reply->created			= DiscussDateHelper::getDate( $row->created )->toFormat();
			$reply->minimize    	= DiscussHelper::toMinimizePost($row->sum_totalvote);
			$reply->likesAuthor 	= DiscussHelper::getLikesAuthors('post', $row->id, $my->id);
			$reply->isLike   		= $reply->hasLiked( 'post' , $my->id );

			//get reply comments
			$reply->comments    	= '';
			$comments				= $reply->getComments();

			if($config->get('main_comment', 1) && count( $comments ) > 0 )
			{
				foreach( $comments as $comment )
				{
					$duration			= new StdClass();
					$duration->daydiff	= $comment->daydiff;
					$duration->timediff	= $comment->timediff;

					$comment->duration  = DiscussHelper::getDurationString( $duration );

					$creator = DiscussHelper::getTable( 'Profile' );
					$creator->load( $comment->user_id);

					$comment->creator	= $creator;

					if ( $config->get( 'main_content_trigger_comments' ) )
					{
						// process content plugins
						$comment->content	= $comment->comment;

						DiscussEventsHelper::importPlugin( 'content' );
						DiscussEventsHelper::onContentPrepare('comment', $comment);

						$comment->event = new stdClass();

						$results	= DiscussEventsHelper::onContentBeforeDisplay('comment', $comment);
						$comment->event->beforeDisplayContent	= trim(implode("\n", $results));

						$results	= DiscussEventsHelper::onContentAfterDisplay('comment', $comment);
						$comment->event->afterDisplayContent	= trim(implode("\n", $results));

						$comment->comment	= $comment->content;
						unset($comment->content);

						$comment->comment = DiscussHelper::wordFilter( $comment->comment );
					}
				}

				$template	= new DiscussThemes();
				$template->set( 'comments' , $comments );
				$template->set( 'isAdmin'	, DiscussHelper::isSiteAdmin() );
				$reply->comments	= $template->fetch( 'comments.php' );
			}

			// @rule: Check for url references
			$reply->references  = $reply->getReferences();

			if ( $config->get( 'main_content_trigger_replies' ) )
			{
				// process content plugins
				DiscussEventsHelper::importPlugin( 'content' );
				DiscussEventsHelper::onContentPrepare('reply', $reply);

				$reply->event = new stdClass();

				$results	= DiscussEventsHelper::onContentBeforeDisplay('reply', $reply);
				$reply->event->beforeDisplayContent	= trim(implode("\n", $results));

				$results	= DiscussEventsHelper::onContentAfterDisplay('reply', $reply);
				$reply->event->afterDisplayContent	= trim(implode("\n", $results));
			}

			$replies[]          = $reply;
		}

		return $replies;
	}

	public static function formatUsers( $result )
	{
		if( !$result )
		{
			return $result;
		}

		$total  = count( $result );
		$users  = array();
		for( $i =0 ; $i < $total; $i++ )
		{
			$row    = $result[ $i ];

			$user	= DiscussHelper::getTable( 'Profile' );
			$user->bind( $row );

			$users[]    = $user;
		}

		return $users;
	}

	public static function getVoters($id, $limit='5')
	{
		$config 		= DiscussHelper::getConfig();

		$table			= DiscussHelper::getTable( 'Post' );
		$voters 		= $table->getVoters($id, $limit);

		$data					= new stdClass();
		$data->voters			= '';
		$data->shownVoterCount	= '';

		if(!empty($voters))
		{
			$data->shownVoterCount = count($voters);

			foreach($voters as $voter)
			{
				$displayname = $config->get('layout_nameformat');

				switch($displayname)
				{
					case "name" :
						$votername = $voter->name;
						break;
					case "username" :
						$votername = $voter->username;
						break;
					case "nickname" :
					default :
						$votername = (empty($voter->nickname)) ? $voter->name : $voter->nickname;
						break;
				}

				if(!empty($data->voters))
				{
					$data->voters .= ', ';
				}

				$data->voters .= '<a href="' . DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $voter->user_id ) . '">' . $votername . '</a>';
			}
		}

		return $data;
	}

	public static function getJoomlaVersion()
	{
		$jVerArr   = explode('.', JVERSION);
		$jVersion  = $jVerArr[0] . '.' . $jVerArr[1];

		return $jVersion;
	}

	public static function getDefaultSAIds()
	{
		$saUserId	= '62';

		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsers    = DiscussHelper::getSAUsersIds();
			$saUserId   = $saUsers[0];
		}

		return $saUserId;
	}

	/**
	 * Used in J1.6!. To retrieve list of superadmin users's id.
	 * array
	 */
	public static function getSAUsersIds()
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

	/**
	 * parentId - if this option spcified, it will list the parent and all its childs categories.
	 * userId - if this option specified, it only return categories created by this userId
	 * outType - the output type. Currently supported links and drop down selection
	 * eleName - the element name of this populated categeries provided the outType os dropdown selection.
	 * default - the default value. If given, it used at dropdown selection (auto select)
	 * isWrite - determine whether the categories list used in write new page or not.
	 * isPublishedOnly - if this option is true, only published categories will fetched.
	 */

	public static function populateCategories($parentId, $userId, $outType, $eleName, $default = false, $isWrite = false, $isPublishedOnly = false, $showPrivateCat = true)
	{
		JModel::addIncludePath(JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'models');

		if(!class_exists('EasyDiscussModelCategories'))
		{
			require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'models'.DS.'categories.php' );
		}
		$catModel   = new EasyDiscussModelCategories();

		$parentCat	= null;

		if(! empty($userId))
		{
			$parentCat  = $catModel->getParentCategories($userId, 'poster', $isPublishedOnly, $showPrivateCat);
		}
		else if(! empty($parentId))
		{
			$parentCat  = $catModel->getParentCategories($parentId, 'category', $isPublishedOnly, $showPrivateCat);
		}
		else
		{
			$parentCat  = $catModel->getParentCategories('', 'all', $isPublishedOnly, $showPrivateCat);
		}

		$ignorePrivate  = false;

		switch($outType)
		{
			case 'link' :
				$ignorePrivate  = false;
				break;
			case 'select':
			default:
				$ignorePrivate  = true;
				break;
		}

		if(! empty($parentCat))
		{
			for($i = 0; $i < count($parentCat); $i++)
			{
				$parent = $parentCat[$i];

				//reset
				$parent->childs = null;

				DiscussHelper::buildNestedCategories($parent->id, $parent, $ignorePrivate, $isPublishedOnly, $showPrivateCat);
			}//for $i
		}//end if !empty $parentCat

		//get all admin emails
		$adminEmails = array();
		$ownerEmails = array();

		$formEle    = '';
		foreach($parentCat as $category)
		{
			$selected   = ($category->id == $default) ? ' selected="selected"' : '';

			if( $default === false )
			{
				$selected   = $category->default ? ' selected="selected"' : '';
			}

			$formEle   .= '<option value="'.$category->id.'" ' . $selected. '>' . JText::_( $category->title ) . '</option>';

			DiscussHelper::accessNestedCategories($category, $formEle, '0', $default, $outType);
		}

		$selected = empty($default) ? ' selected="selected"' : '';

		$html   = '';
		$html	.= '<select name="' . $eleName . '" id="' . $eleName .'" class="inputbox">';
		if(! $isWrite)
			$html	.=	'<option value="0">' . JText::_('COM_EASYDISCUSS_SELECT_PARENT_CATEGORY') . '</option>';
		else
			$html	.= '<option value="0" ' . $selected . '>' . JText::_('COM_EASYDISCUSS_SELECT_CATEGORY') . '</option>';
		$html	.=	$formEle;
		$html	.= '</select>';

		return $html;
	}

	public static function buildNestedCategories($parentId, $parent, $ignorePrivate = false, $isPublishedOnly = false, $showPrivate = true )
	{
		$my     = JFactory::getUser();

		JModel::addIncludePath(JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'models');
		if(!class_exists('EasyDiscussModelCategories'))
		{
			require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'models'.DS.'categories.php' );
		}

		if(!class_exists('EasyDiscussModelCategory'))
		{
			require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'models'.DS.'category.php' );
		}

		$catsModel  = new EasyDiscussModelCategories();
		$catModel   = new EasyDiscussModelCategory();
		$childs 	= $catsModel->getChildCategories($parentId, $isPublishedOnly, $showPrivate);

		$accessibleCatsIds 	= DiscussHelper::getAccessibleCategories( $parentId );

		if(! empty($childs))
		{
			for($j = 0; $j < count($childs); $j++)
			{
				$child  = $childs[$j];
				$child->count	= $catModel->getTotalPostCount($child->id);
				$child->childs	= null;

				if(! $ignorePrivate)
				{
					if( count( $accessibleCatsIds ) > 0)
					{
					    $access = false;
					    foreach( $accessibleCatsIds as $canAccess)
					    {
					        if( $canAccess->id == $child->id)
					        {
					            $access = true;
					        }
					    }

						if( !$access )
							continue;

					}
					else
					{
					    continue;
					}
				}

				if(! DiscussHelper::buildNestedCategories($child->id, $child, $ignorePrivate, $isPublishedOnly, $showPrivate))
				{
					$parent->childs[]   = $child;
				}
			}// for $j
		}
		else
		{
			return false;
		}
	}

	public static function accessNestedCategories($arr, &$html, $deep='0', $default='0', $type='select', $linkDelimiter = '')
	{
		if(isset($arr->childs) && is_array($arr->childs))
		{
			$sup    = '<sup>|_</sup>';
			$space  = '';
			$ld     = (empty($linkDelimiter)) ? '>' : $linkDelimiter;

			if($type == 'select' || $type == 'list')
			{
				$deep++;
				for($d=0; $d < $deep; $d++)
				{
					$space .= '&nbsp;&nbsp;&nbsp;';
				}
			}

			if($type == 'list' && !empty($arr->childs))
			{
				$html .= '<ul>';
			}

			for($j	= 0; $j < count($arr->childs); $j++)
			{
				$child  = $arr->childs[$j];

				switch($type)
				{
					case 'select':
						$selected    = ($child->id == $default) ? ' selected="selected"' : '';

						if( !$default )
						{
							$selected   = $child->default ? ' selected="selected"' : '';
						}

						$html   	.= '<option value="'.$child->id.'" ' . $selected . '>' . $space . $sup . $child->title . '</option>';
						break;
					case 'list':
						$expand 	= !empty($child->childs)? '<span onclick="Foundry(this).parents(\'li:first\').toggleClass(\'expand\');">[+] </span>' : '';
						$html 		.= '<li><div>' . $space . $sup . $expand . '<a href="' . DiscussRouter::_('index.php?option=com_easydiscuss&view=index&category_id=' . $child->id) . '">' . $child->title . '</a> <b>(' . $child->count . ')</b></div>';
						break;
					default:
						$str    	 = '<a href="' . DiscussRouter::_('index.php?option=com_easydiscuss&view=categories&layout=listings&id='.$child->id) . '">' . $child->title . '</a>';
						$html   	.= (empty($html)) ? $str : '&nbsp;' . $ld . '&nbsp;' . $str;
				}

				DiscussHelper::accessNestedCategories($child, $html, $deep, $default, $type, $linkDelimiter);

				if($type == 'list')
				{
					$html .= '</li>';
				}
			}

			if($type == 'list' && !empty($arr->childs))
			{
				$html .= '</ul>';
			}
		}
		else
		{
			return false;
		}
	}

	public static function accessNestedCategoriesId($arr, &$newArr)
	{
		if(isset($arr->childs) && is_array($arr->childs))
		{
			$modelSubscribe		= JModel::getInstance( 'Subscribe' , 'EasyDiscussModel' );
			$subscribers        = $modelSubscribe->getSiteSubscribers('instant');

			for($j	= 0; $j < count($arr->childs); $j++)
			{
				$child  = $arr->childs[$j];

				$newArr[]   = $child->id;
				DiscussHelper::accessNestedCategoriesId($child, $newArr);
			}
		}
		else
		{
			return false;
		}
	}

	/**
	 * function to retrieve the linkage backward from a child id.
	 * return the full linkage from child up to parent
	 */

	public static function populateCategoryLinkage($childId)
	{
		$arr        = array();
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load($childId);

		$obj    = new stdClass();
		$obj->id 	= $category->id;
		$obj->title = $category->title;
		$obj->alias = $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			DiscussHelper::accessCategoryLinkage($category->parent_id, $arr);
		}

		$arr    = array_reverse($arr);
		return $arr;

	}

	public static function accessCategoryLinkage($childId, &$arr)
	{
		$category	= DiscussHelper::getTable( 'Category' );

		$category->load($childId);

		$obj    = new stdClass();
		$obj->id 	= $category->id;
		$obj->title = $category->title;
		$obj->alias = $category->alias;

		$arr[]  = $obj;

		if((!empty($category->parent_id)))
		{
			DiscussHelper::accessCategoryLinkage($category->parent_id, $arr);
		}
		else
		{
			return false;
		}
	}

	public static function showSocialButtons( $post, $position = 'vertical' )
	{
		require_once( DISCUSS_CLASSES . DS .'google.php' );
		require_once( DISCUSS_CLASSES . DS .'twitter.php' );
		require_once( DISCUSS_CLASSES . DS .'facebook.php' );
		require_once( DISCUSS_CLASSES . DS .'digg.php' );
		require_once( DISCUSS_CLASSES . DS .'linkedin.php' );

		$config 	= DiscussHelper::getConfig();
		$document   = JFactory::getDocument();

		$googlebuzz 	= '';
		$twitterbutton  = '';

		if( $position == 'vertical' )
		{
			$googlebuzz 	= DiscussGoogleBuzz::getButtonHTML( $post );
		}

		$twitterbutton 	= DiscussTwitter::getButtonHTML( $post, $position );
		$googleone 		= DiscussGoogleOne::getButtonHTML( $post, $position );
		$facebookLikes	= DiscussFacebook::getLikeHTML( $post, $position );
		$digg			= DiscussDigg::getButtonHTML( $post , $position );
		$linkedIn		= DiscussLinkedIn::getButtonHTML( $post , $position );

		$float  = ($position == 'vertical') ? 'class="float-r"' : 'class="clearfull"';

		$socialButtons = '';

		$socialButtonsHere = $digg . $linkedIn . $googlebuzz . $googleone . $twitterbutton . $facebookLikes;

		if( !empty($socialButtonsHere) )
		{
			$socialButtons  = '<div id="dc_share" '.$float.'>' . $digg . $linkedIn . $googlebuzz . $googleone . $twitterbutton . $facebookLikes . '</div>';
		}
		echo $socialButtons;
	}

	/**
	 * $post - post jtable object
	 * $parent - post's parent id.
	 * $isNew - indicate this is a new post or not.
	 */

	public static function sendNotification( $post, $parent = 0, $isNew, $postOwner, $prevPostStatus)
	{
		$config = DiscussHelper::getConfig();
		$notify	= DiscussHelper::getNotification();

		$user   = '';
		if( empty( $postOwner ) )
			$user 	= JFactory::getUser();
		else
			$user 	= JFactory::getUser( $postOwner );

		$emailPostTitle = $post->title;

		//get all admin emails
		$adminEmails = array();
		$ownerEmails = array();

		if( empty( $parent ) )
		{
			// only new post we notify admin.
			if($config->get( 'notify_admin' ))
			{
				$admins = $notify->getAdminEmails();

				if(! empty($admins))
				{
					foreach($admins as $admin)
					{
						$adminEmails[]   = $admin->email;
					}
				}
			}
		}
		else
		{
			// if this is a new reply, notify post owner.
			$parentTable		= DiscussHelper::getTable( 'Post' );
			$parentTable->load( $parent );

			$emailPostTitle = $parentTable->title;

			$oriPostAuthor  = $parentTable->user_id;
			$oriPostUser    = JFactory::getUser( $oriPostAuthor );
			$ownerEmails[]  = $oriPostUser->email;
		}

		$emailSubject  	= ( empty( $parent ) ) ? JText::sprintf('COM_EASYDISCUSS_NEW_POST_ADDED', $emailPostTitle) : JText::sprintf( 'COM_EASYDISCUSS_NEW_REPLY_ADDED', $emailPostTitle );
		$emailTemplate  = ( empty( $parent ) ) ? 'email.subscription.site.new.php' : 'email.post.reply.new.php';

		//get all site's subscribers email that want to receive notification immediately
		$subscriberEmails	= array();
		$subscribers		= array();

		if( $config->get('main_sitesubscription') && ($isNew || $prevPostStatus == DISCUSS_ID_PENDING) )
		{
			$modelSubscribe		= self::getModel( 'Subscribe' );
			$subscribers        = $modelSubscribe->getSiteSubscribers('instant');

			if(! empty($subscribers))
			{
				foreach($subscribers as $subscriber)
				{
					$subscriberEmails[]   = $subscriber->email;
				}
			}
		}

		if( !empty( $adminEmails ) || !empty( $subscriberEmails ) || !empty( $ownerEmails ) )
		{
			$emails = array_unique(array_merge($adminEmails, $subscriberEmails, $ownerEmails));

			// prepare email content and information.
			$emailData					= array();
			$postAuthor                 = ( $post->user_id ) ? $user->name : $post->poster_name;
			$postAuthor                 = ( !empty($postAuthor) ) ? $postAuthor : JText::_('COM_EASYDISCUSS_GUEST');

			$emailData['postTitle']		= $emailPostTitle;
			$emailData['postAuthor']	= $postAuthor;
			$emailData['comment']		= $post->content;
			$emailData['commentAuthor']	= $postAuthor;

			// get the correct post id in url, the parent post id should take precedence
			$postId	= empty( $parent ) ? $post->id : $parentTable->id;

			$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $posdId, false, true);

			//insert into mailqueue
			foreach ($emails as $email)
			{
				if ( in_array($email, $subscriberEmails) )
				{
					// these are subscribers
					if (!empty($subscribers))
					{
						foreach ($subscribers as $key => $value)
						{
							if ($value->email == $email)
							{
								$emailData['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscribers[$key], true, true);
								$notify->addQueue($email, $emailSubject, '', $emailTemplate, $emailData);
							}
						}
					}
				}
				else
				{
					// non-subscribers will not get the unsubscribe link
					$notify->addQueue($emails, $emailSubject, '', $emailTemplate, $emailData);
				}
			}
		}
	}

	public static function getUserRepliesHTML( $postId, $excludeLastReplyUser	= false)
	{
		$model		= JModel::getInstance( 'Posts' , 'EasyDiscussModel' );
		$replies    = $model->getUserReplies($postId, $excludeLastReplyUser);

		$html   = '';
		if( ! empty( $replies ) )
		{
			$tpl	= new DiscussThemes();
			$tpl->set( 'replies'	, $replies );
			$html	=  $tpl->fetch( 'main.item.replies.php' );
		}

		return $html;
	}

	public static function getUserAcceptedReplyHTML( $postId )
	{
		$model		= JModel::getInstance( 'Posts' , 'EasyDiscussModel' );
		$reply    = $model->getAcceptedReply( $postId );

		$html   = '';
		if( ! empty( $reply ) )
		{
			$tpl	= new DiscussThemes();
			$tpl->set( 'reply'	, $reply );
			$html	=  $tpl->fetch( 'main.item.answered.php' );
		}

		return $html;
	}

	public static function isSiteSubscribed( $userId )
	{
		if( !class_exists( 'EasyDiscussModelSubscribe') )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'subscribe' , DISCUSS_ROOT . DS . 'models' );
		}
		$model		= JModel::getInstance( 'Subscribe' , 'EasyDiscussModel' );

		$user       = JFactory::getUser( $userId );

		$subscription   = array();
		$subscription['type']	= 'site';
		$subscription['email']	= $user->email;
		$subscription['cid']	= 0;

		$result = $model->isSiteSubscribed( $subscription );

		return ( !isset($result['id']) ) ? '0' : $result['id'];
	}

	public static function isPostSubscribed( $userId, $postId )
	{
		$model		= JModel::getInstance( 'Subscribe' , 'EasyDiscussModel' );

		$user       = JFactory::getUser( $userId );

		$subscription   = array();
		$subscription['type'] 	= 'post';
		$subscription['userid'] = $user->id;
		$subscription['email'] 	= $user->email;
		$subscription['cid'] 	= $postId;

		$result = $model->isPostSubscribedEmail( $subscription );

		return ( !isset($result['id']) ) ? '0' : $result['id'];
	}

	public static function isMySubscription( $userid, $type, $subId)
	{
		$model		= JModel::getInstance( 'Subscribe' , 'EasyDiscussModel' );
		return $model->isMySubscription($userid, $type, $subId);
	}

	public static function hasPassword( $post )
	{
		$session	= JFactory::getSession();
		$password	= $session->get( 'DISCUSSPASSWORD_' . $post->id , '' , 'com_easydiscuss' );

		if( $password == $post->password )
		{
			return true;
		}
		return false;
	}

	public static function getUserComponent()
	{
		return ( DiscussHelper::getJoomlaVersion() >= '1.6' ) ? 'com_users' : 'com_user';
	}

	public static function getUserComponentLoginTask()
	{
		return ( DiscussHelper::getJoomlaVersion() >= '1.6' ) ? 'user.login' : 'login';
	}

	public static function getAccessibleCategories( $parentId = 0, $type = DISCUSS_CATEGORY_ACL_ACTION_VIEW )
	{
		$db 			= JFactory::getDBO();
		$my 			= JFactory::getUser();

		$gids   	= '';
		$catQuery	= 	'select distinct a.`id`, a.`private`';
		$catQuery	.=  ' from `#__discuss_category` as a';
		$catQuery	.=  ' where (a.`private` = ' . $db->Quote('0');


		$gid    = array();
		$gids   = '';

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
		    $gid    = array();
		    if( $my->id == 0 )
		    {
				$gid 	= JAccess::getGroupsByUser(0, false);
			}
			else
			{
				$gid 	= JAccess::getGroupsByUser($my->id, false);
			}
		}
		else
		{
			$gid	= DiscussHelper::getUserGids();
		}


		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}

			$catQuery   .=	'  OR a.`id` IN (';
			$catQuery .= '		select b.`category_id` from `#__discuss_category_acl_map` as b';
			$catQuery .= '			where b.`category_id` = a.`id` and b.`acl_id` = '. $db->Quote( $type );
			$catQuery .= '			and b.`type` = ' . $db->Quote('group');
			$catQuery .= '			and b.`content_id` IN (' . $gids . ')';

			//logged in user
			if( $my->id != 0 )
			{
				$catQuery .= '			union ';
				$catQuery .= '			select b.`category_id` from `#__discuss_category_acl_map` as b';
				$catQuery .= '				where b.`category_id` = a.`id` and b.`acl_id` = ' . $db->Quote( $type );
				$catQuery .= '				and b.`type` = ' . $db->Quote('user');
				$catQuery .= '				and b.`content_id` = ' . $db->Quote( $my->id );
			}
			$catQuery   .= ')';

		}

		$catQuery   .= ')';
		$catQuery   .= ' AND a.parent_id = ' . $db->Quote($parentId);

		// echo $catQuery;exit;

		$db->setQuery($catQuery);
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getPrivateCategories( $acltype = DISCUSS_CATEGORY_ACL_ACTION_VIEW )
	{
		$db 			= JFactory::getDBO();
		$my 			= JFactory::getUser();
		$excludeCats	= array();

		if($my->id == 0)
		{
			$catQuery	= 	'select distinct a.`id`, a.`private`';
			$catQuery	.=  ' from `#__discuss_category` as a';
			$catQuery	.=	' 	left join `#__discuss_category_acl_map` as b on a.`id` = b.`category_id`';
			$catQuery	.=	' 		and b.`acl_id` = ' . $db->Quote( $acltype );
			$catQuery	.=	' 		and b.`type` = ' . $db->Quote( 'group' );
			$catQuery	.=  ' where a.`private` != ' . $db->Quote('0');

			$gid    = array();
			$gids   = '';


			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				$gid 	= JAccess::getGroupsByUser(0, false);
			}
			else
			{
			    $gid 	= DiscussHelper::getUserGids();
			}

			if( count( $gid ) > 0 )
			{
				foreach( $gid as $id)
				{
					$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
				}
				$catQuery   .= ' and a.`id` NOT IN (';
				$catQuery   .= '     SELECT c.category_id FROM `#__discuss_category_acl_map` as c ';
				$catQuery   .= '        WHERE c.acl_id = ' .$db->Quote( $acltype );
				$catQuery   .= '        AND c.type = ' . $db->Quote('group');
				$catQuery   .= '        AND c.content_id IN (' . $gids . ') )';
			}

			// echo $catQuery;exit;

			$db->setQuery($catQuery);
			$result = $db->loadObjectList();
		}
		else
		{
			$result = self::getAclCategories ( $acltype, $my->id );
		}

		for($i=0; $i < count($result); $i++)
		{
			$item   = $result[$i];
			$item->childs = null;

			DiscussHelper::buildNestedCategories($item->id, $item, true);

			$catIds     = array();
			$catIds[]   = $item->id;
			DiscussHelper::accessNestedCategoriesId($item, $catIds);

			$excludeCats    = array_merge($excludeCats, $catIds);
		}

		//echo 'test';
		//var_dump($excludeCats);

		return $excludeCats;
	}

	public static function getAclCategories ( $type = DISCUSS_CATEGORY_ACL_ACTION_VIEW, $userId = '', $parentId = false )
	{
		$db 	= JFactory::getDBO();

		$gid    = '';
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			if( $userId == '' )
			{
				$gid 	= JAccess::getGroupsByUser(0, false);
			}
			else
			{
				$gid	= DiscussHelper::getUserGids( $userId );
			}
		}
		else
		{
			$gid	= DiscussHelper::getUserGids( $userId );
		}

		$gids   = '';
		if( count( $gid ) > 0 )
		{
			foreach( $gid as $id)
			{
				$gids   .= ( empty($gids) ) ? $db->Quote( $id ) : ',' . $db->Quote( $id );
			}
		}

		$query = 'select c.`id` from `#__discuss_category` as c';
		$query .= ' where not exists (';
		$query .= '		select b.`category_id` from `#__discuss_category_acl_map` as b';
		$query .= '			where b.`category_id` = c.`id` and b.`acl_id` = '. $db->Quote( $type );
		$query .= '			and b.`type` = ' . $db->Quote('group');
		$query .= '			and b.`content_id` IN (' . $gids . ')';

		//logged in user
		if(! empty($userId) )
		{
			$query .= '			union ';
			$query .= '			select b.`category_id` from `#__discuss_category_acl_map` as b';
			$query .= '				where b.`category_id` = c.`id` and b.`acl_id` = ' . $db->Quote( $type );
			$query .= '				and b.`type` = ' . $db->Quote('user');
			$query .= '				and b.`content_id` = ' . $db->Quote( $userId );
		}

		$query .= '      )';
		$query .= ' and c.`private` = ' . $db->Quote( DISCUSS_PRIVACY_ACL );
		if( $parentId !== false )
			$query .= ' and c.`parent_id` = ' . $db->Quote($parentId);

		$db->setQuery($query);

		$result = $db->loadObjectList();

		return $result;
	}

	public static function getTable( $tableName , $prefix = 'Discuss' , $config = array() )
	{
		JTable::addIncludePath( DISCUSS_TABLES );
		$table	= JTable::getInstance( $tableName , $prefix , $config );

		return $table;
	}

	public static function getModel( $name )
	{
		static $model = array();

		if( !isset( $model[ $name ] ) )
		{
			$file	= JString::strtolower( $name );
			$path	= JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'models' . DS . $file . '.php';

			jimport('joomla.filesystem.path');
			if ( JFolder::exists( $path ))
			{
				JError::raiseWarning( 0, 'Model file not found.' );
			}

			$modelClass		= 'EasyDiscussModel' . ucfirst( $name );

			if( !class_exists( $modelClass ) )
				require_once( $path );


			$model[ $name ] = new $modelClass();
		}

		return $model[ $name ];
	}

	public static function getModel2( $name, $prefix = 'EasyDiscussModel', $config = array() )
	{
		JModel::addIncludePath( DISCUSS_ROOT . DS . 'models', $prefix );
		$model	= JModel::getInstance( $name, $prefix, $config );

		return $model;
	}

	public static function getPagination($total, $limitstart, $limit, $prefix = '')
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		$signature = serialize(array($total, $limitstart, $limit, $prefix));

		if (empty($instances[$signature]))
		{
			require_once( DISCUSS_CLASSES . DS . 'pagination.php' );
			$pagination	= new DiscussPagination($total, $limitstart, $limit, $prefix);

			$instances[$signature] = &$pagination;
		}

		return $instances[$signature];
	}

	/**
	 * Retrieve @JUser object based on the given email address.
	 *
	 * @access	public
	 * @param	string $email	The user's email address.
	 * @return	JUser			@JUser object.
	 **/
	public static function getUserByEmail( $email )
	{
		$email	= strtolower( $email );

		$db		= JFactory::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'id' ) . ' FROM '
				. $db->nameQuote( '#__users' ) . ' '
				. 'WHERE LOWER(' . $db->nameQuote( 'email' ) . ') = ' . $db->Quote( $email );
		$db->setQuery( $query );
		$id		= $db->loadResult();

		if( !$id )
		{
			return false;
		}

		return JFactory::getUser( $id );
	}

	public static function getUserGids( $userId = '' )
	{
		$user   = '';

		if( empty($userId) )
		{
			$user   = JFactory::getUser();
		}
		else
		{
			$user   = JFactory::getUser($userId);
		}

		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			$groupIds = $user->groups;

			$grpId  = array();

			foreach($groupIds as $key => $val)
			{
				$grpId[] = $val;
			}

			return $grpId;
		}
		else
		{
			return array( $user->gid );
		}
	}

	public static function getUserRankScore( $userId, $percentage = true)
	{
		return DiscussHelper::getHelper( 'Ranks' )->getScore( $userId , $percentage );
	}

	public static function getUserRanks( $userId )
	{
		return DiscussHelper::getHelper( 'Ranks' )->getRank( $userId );
	}

	public static function getJoomlaUserGroups( $cid = '' )
	{
		$db = JFactory::getDBO();

		if(self::getJoomlaVersion() >= '1.6')
		{
			$query = 'SELECT a.id, a.title AS `name`, COUNT(DISTINCT b.id) AS level';
			$query .= ' , GROUP_CONCAT(b.id SEPARATOR \',\') AS parents';
			$query .= ' FROM #__usergroups AS a';
			$query .= ' LEFT JOIN `#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt';
		}
		else
		{
			$query	= 'SELECT `id`, `name`, 0 as `level` FROM ' . $db->nameQuote('#__core_acl_aro_groups') . ' a ';
		}

		// condition
		$where  = array();

		// we need to filter out the ROOT and USER dummy records.
		if(self::getJoomlaVersion() < '1.6')
		{
			$where[] = '(a.`id` > 17 AND a.`id` < 26)';
		}

		if( !empty( $cid ) )
		{
			$where[] = ' a.`id` = ' . $db->quote($cid);
		}
		$where = ( count( $where ) ? ' WHERE ' .implode( ' AND ', $where ) : '' );

		$query  .= $where;

		// grouping and ordering
		if( self::getJoomlaVersion() >= '1.6' )
		{
			$query	.= ' GROUP BY a.id';
			$query	.= ' ORDER BY a.lft ASC';
		}
		else
		{
			$query 	.= ' ORDER BY a.id';
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();

		return $result;
	}

	public static function getSubscriptionHTML( $userid, $cid = 0, $type = 'post', $class = '', $simpleText = true )
	{
		if( !class_exists( 'EasyDiscussModelSubscribe') )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'subscribe' , DISCUSS_ROOT . DS . 'models' );
		}

		$model			= JModel::getInstance( 'Subscribe' , 'EasyDiscussModel' );
		$type			= ($type == 'index') ? 'site' : $type;

		$isSubscribed	= $model->isSubscribed( $userid, $cid, $type );
		$sid			= $isSubscribed ? $isSubscribed : 0;

		$tpl	= new DiscussThemes();
		$tpl->set( 'isSubscribed', $isSubscribed );
		$tpl->set( 'type', $type );
		$tpl->set( 'cid', $cid );
		$tpl->set( 'sid', $sid );
		$tpl->set( 'simple', $simpleText );
		$tpl->set( 'class', $class );

		return $tpl->fetch( 'subscription.php' );
	}

	public static function getUnsubscribeLink($subdata, $external = false, $html = false)
	{
		$unsubdata	= base64_encode("type=".$subdata->type."\r\nsid=".$subdata->id."\r\nuid=".$subdata->userid."\r\ntoken=".md5($subdata->id.$subdata->created));

		$link		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&controller=subscription&task=unsubscribe&data='.$unsubdata, false, $external);

		return $link;
		// $return		= $html ? JText::sprintf('COM_EASYDISCUSS_EMAIL_UNSUBSCRIBE_INFO' , '<a href="'.$link.'">'.JText::_('COM_EASYDISCUSS_URL_HERE').'</a>') : $link;

		// return $return;
	}

	/*
	 * Return class name according to user's group.
	 * e.g. 'reply-usergroup-1 reply-usergroup-2'
	 *
	 */
	public static function userToClassname($jUserObj, $classPrefix = 'reply', $delimiter = '-')
	{
		if (is_numeric($jUserObj))
		{
			$jUserObj	= JFactory::getUser($jUserObj);
		}

		if( !$jUserObj instanceof JUser )
		{
			return '';
		}

		static $classNames;

		if (!isset($classNames))
		{
			$classNames = array();
		}

		$signature = serialize(array($jUserObj->id, $classPrefix, $delimiter));

		if (!isset($classNames[$signature]))
		{
			$classes	= array();

			$classes[]	= $classPrefix . $delimiter . 'user' . $delimiter . $jUserObj->id;

			if (property_exists($jUserObj, 'gid'))
			{
				$classes[]	= $classPrefix . $delimiter . 'usergroup' . $delimiter . $jUserObj->get( 'gid' );
			}
			else
			{
				$groups		= $jUserObj->getAuthorisedGroups();

				foreach($groups as $id)
				{
					$classes[] = $classPrefix . $delimiter . 'usergroup' . $delimiter . $id;
				}
			}

			$classNames[$signature] = implode(' ', $classes);
		}

		return $classNames[$signature];
	}

	/**
	 * Retrieve similar question based on the keywords
	 *
	 * @access	public
	 * @param	string	$keywords
	 */
	public static function getSimilarQuestion( $text = '' )
	{
	    if( empty( $text ) )
	        return '';

		$config = Discusshelper::getConfig();

		if(! $config->get( 'main_similartopic', 0 ) )
		{
	        return '';
		}

        // $text   = 'how to configure facebook integration?';
		$itemLimit  = $config->get('main_similartopic_limit', '5');
		$db = JFactory::getDBO();

		// remove punctuation from the string.
		$text = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $text);

		// lets get the tags match the keywords
		$tagkeywords    = explode(' ', $text);
		for($i = 0; $i < count( $tagkeywords ); $i++ )
		{
		    if( JString::strlen($tagkeywords[$i]) > 3 )
		    {
		        $tagkeywords[$i] = $tagkeywords[$i] . '*';
		    }
		    else
		    {
		    	$tagkeywords[$i] = $tagkeywords[$i];
		    }
		}
		$tagkeywords   = implode(' ', $tagkeywords);

		$query	= 'select `id` FROM `#__discuss_tags`';
		$query	.= ' WHERE MATCH(`title`) AGAINST (' . $db->Quote($tagkeywords) . ' IN BOOLEAN MODE)';

		$db->setQuery( $query );

		$tagResults = $db->loadResultArray();

		$queryExclude   = '';
		if( ! $config->get( 'main_similartopic_privatepost', 0 ) )
		{
	        $excludeCats    = DiscussHelper::getPrivateCategories();
			if(! empty($excludeCats))
			{
			    $queryExclude .= ' AND a.`category_id` NOT IN (' . implode(',', $excludeCats) . ')';
			}
		}

		// now try to get the main topic
		$query = 'select a.`id`,  a.`title`, MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote( $text ) . ') AS score';
		$query .= ' FROM `#__discuss_posts` as a';
		$query .= ' WHERE MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote( $text ) . ')';
		$query .= ' AND a.`published` = ' . $db->Quote('1');
		$query .= ' AND a.`parent_id` = ' . $db->Quote('0');
		$query .= $queryExclude;

		$tagQuery   = '';
		if( count( $tagResults ) > 0 )
		{
			$tagQuery = 'select a.`id`,  a.`title`, MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote( $text ) . ') AS score';
			$tagQuery .= ' FROM `#__discuss_posts` as a';
			$tagQuery .= ' 	INNER JOIN `#__discuss_posts_tags` as b ON a.id = b.post_id';
			$tagQuery .= ' WHERE MATCH(a.`title`,a.`content`) AGAINST (' . $db->Quote( $text ) . ')';
			$tagQuery .= ' AND a.`published` = ' . $db->Quote('1');
			$tagQuery .= ' AND a.`parent_id` = ' . $db->Quote('0');
			$tagQuery .= ' AND b.`tag_id` IN (' . implode( ',', $tagResults) . ')';
			$tagQuery .= $queryExclude;

		    $query  = 'SELECT * FROM (' . $query . ' UNION ' . $tagQuery . ') AS x LIMIT ' . $itemLimit;
		}
		else
		{
		    $query  .= ' LIMIT ' . $itemLimit;
		}

		$db->setQuery( $query );
		$result = $db->loadObjectList();
		return $result;

	}

	/**
	 * Retrieve the html block for who's viewing this page.
	 *
	 * @access	public
	 * @param	string	$url
	 */
	public static function getWhosOnline( $uri = '' )
	{
		$config		= DiscussHelper::getConfig();
		$enabled	= $config->get( 'main_viewingpage' );

		if( !$enabled )
		{
			return '';
		}

		if( !empty($uri) )
		{
			$url 	= md5( $uri );
		}
		else
		{
			$url	= md5( JRequest::getURI() );
		}

		$jConfig    = JFactory::getConfig();
		$lifespan	= $jConfig->getValue('lifetime');
		$online     = time() - ($lifespan * 60);

		$db		= JFactory::getDBO();
		$query	= 'SELECT a.* FROM ' . $db->nameQuote( '#__discuss_views' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__users' ) . ' AS b '
				. 'ON a.`user_id`=b.`id`'
				. 'INNER JOIN ' . $db->nameQuote( '#__session' ) . ' AS c '
				. 'ON c.`userid`=b.`id` '
				. 'WHERE ' . $db->nameQuote( 'hash' ) . '=' . $db->Quote( $url ) . ' '
				. 'AND a.`user_id` !=' . $db->Quote( 0 )
				. 'AND c.`time` >= ' . $db->Quote( $online ) . ' '
				. 'AND c.`client_id` = ' . $db->Quote('0') . ' '
				. 'GROUP BY a.`user_id`';

		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		$users	= array();

		foreach( $result as $res )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $res->user_id );
			$users[]	= $profile;
		}

		require_once( DISCUSS_CLASSES . DS . 'themes.php' );

		$theme	= new DiscussThemes();
		$theme->set( 'users' , $users );
		return $theme->fetch( 'users.online.php' );
	}

	public static function getListLimit()
	{
		$app		= JFactory::getApplication();
		$default	= JFactory::getConfig()->getValue( 'list_limit' );

		if( $app->isAdmin() )
		{
			return $default;
		}

		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		$limit  = -2;

		if( is_object( $menu ) )
		{
		    $params 	= new JParameter( $menu->params );
		    $limit      = $params->get( 'limit' , '-2' );
		}

	    if( $limit == '-2' )
	    {
	        // Use default configurations.
	        $config		= DiscussHelper::getConfig();
			$limit      = $config->get( 'layout_list_limit', '-2' );
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if( $limit == '0' || $limit == '-1' || $limit == '-2' )
		{
			$limit		= $default;
		}

		return $limit;
	}

	public static function getRegistrationLink()
	{
		$config 	= DiscussHelper::getConfig();

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'joomla':
			case 'cb':
				if( DiscussHelper::getJoomlaVersion() >= '1.6' )
				{
					$link	= JRoute::_( 'index.php?option=com_users&view=registration' );
				}
				else
				{
					$link	= JRoute::_( 'index.php?option=com_user&view=register' );
				}
			break;

			case 'jomsocial':
				$link	= JRoute::_( 'index.php?option=com_community&view=register' );
			break;
		}

		return $link;
	}

	public static function getResetPasswordLink()
	{
		$config 	= DiscussHelper::getConfig();

		switch( $config->get( 'main_login_provider' ) )
		{
			case 'joomla':
			case 'cb':
				if( DiscussHelper::getJoomlaVersion() >= '1.6' )
				{
					$link	= JRoute::_( 'index.php?option=com_users&view=reset' );
				}
				else
				{
					$link	= JRoute::_( 'index.php?option=com_user&view=reset' );
				}
			break;

			case 'jomsocial':
				$link	= JRoute::_( 'index.php?option=com_user&view=reset' );
			break;
		}

		return $link;
	}

	public static function getDefaultRepliesSorting()
	{
		$config 		= DiscussHelper::getConfig();
		$defaultFilter  = $config->get( 'layout_replies_sorting' );

		switch( $defaultFilter )
		{
			case 'voted':
				if( ! $config->get( 'main_allowvote') )
				{
					$defaultFilter  = 'replylatest';
				}
			break;
			case 'likes':
				if( ! $config->get( 'main_likes_replies') )
				{
					$defaultFilter  = 'replylatest';
				}
			break;
			case 'latest':
			default:
				$defaultFilter  = 'replylatest';
			break;
		}

		return $defaultFilter;
	}
}
