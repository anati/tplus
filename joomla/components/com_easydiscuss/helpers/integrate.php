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

require_once( DISCUSS_HELPERS . DS . 'image.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );

class DiscussIntegrate
{
	/**
	 * Get the profile and avatar link of a user.
	 *
	 * @param	object	$profile	The JUser object, defaults to null.
	 *
	 * @return	array	$field		An array consists of key avatarLink and profileLink
	 */
	public function getField( $profile = null )
	{
		//@rule: For guest, we use default avatar.
		if( is_null($profile) || !is_object($profile) || !isset($profile->id) || $profile->id == 0 )
		{
			$field['avatarLink'] 	= JURI::root() . 'components/com_easydiscuss/assets/images/default.png';
			$field['profileLink']	= '#';

			return $field;
		}

		static $field;

		if (!isset( $field[$profile->id] ))
		{
			$config 		= DiscussHelper::getConfig();
			$integration	= strtolower($config->get( 'layout_avatarIntegration', 'default' ));

			switch ($integration)
			{
				case 'jomsocial' :
					$socialFields = self::jomsocial( $profile );
					break;
				case 'kunena' :
					$socialFields = self::kunena( $profile );
					break;
				case 'communitybuilder' :
					$socialFields = self::communitybuilder( $profile );
					break;
				case 'gravatar' :
					$socialFields = self::gravatar( $profile );
					break;
				case 'phpbb' :
					$socialFields = self::phpbb( $profile );
					break;
				case 'anahita':
					$socialFields	= self::anahita( $profile );
					break;
				case 'easyblog' :
					$socialFields = self::easyblog( $profile );
					break;
				case 'easydiscuss' :
				default :
					$socialFields = self::easydiscuss( $profile );
					break;
			}

			if (empty($socialFields) || empty($socialFields[0]) || empty($socialFields[1]))
			{
				$socialFields = self::easydiscuss( $profile );
			}

			$field[$profile->id]	= array_combine(array('avatarLink', 'profileLink'), $socialFields);
		}

		return $field[$profile->id];
	}

	private static function easydiscuss( $profile )
	{
		$legacy			= ($profile->avatar == 'default.png' || $profile->avatar == 'components/com_easydiscuss/assets/images/default.png' || empty($profile->avatar));
		$avatarLink		= $legacy ? 'components/com_easydiscuss/assets/images/default.png' : DiscussImageHelper::getAvatarRelativePath() . '/' . $profile->avatar;
		$avatarLink		= JURI::root() . $avatarLink;
		$profileLink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&id='.$profile->id, false);

		return array( $avatarLink, $profileLink );
	}

	private static function anahita( $profile )
	{
		if( !class_exists( 'KFactory' ) )
		{
			return false;
		}

		$person			= KFactory::get( 'lib.anahita.se.person.helper' )->getPerson( $profile->id );
		$profileLink	= JRoute::_( 'index.php?option=com_socialengine&view=person&id=' . $profile->id );

		return array( $person->getAvatar()->getURL( AnSeAvatar::SIZE_MEDIUM ) , $profileLink );
	}

	private static function jomsocial( $profile )
	{
		$file			= JPATH_ROOT . DS . 'components' . DS. 'com_community' . DS . 'libraries' . DS . 'core.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );

		$user			= CFactory::getUser( $profile->id );
		$avatarLink		= $user->getThumbAvatar();

		$profileLink	= CRoute::_('index.php?option=com_community&view=profile&userid=' . $profile->id );

		return array( $avatarLink, $profileLink );
	}

	private static function kunena( $profile )
	{
		// $files			= JPATH_ROOT . DS . 'components' . DS. 'com_kunena' . DS . 'class.kunena.php';
		// if (!JFile::exists($files)) return false;
		// require_once( $files );
//
// 		$db				= JFactory::getDBO();
// 		//$db->setQuery( 'SELECT a.*, b.* FROM `#__fb_users` AS a INNER JOIN `#__users` AS b ON b.id=a.userid WHERE a.userid='.$db->quote($profile->id) );
//     	$user 			= $db->loadObject();
//     	$source			= empty($user->avatar)? 'nophoto.jpg' : str_ireplace( '{', '', $user->avatar);
//     	$avatarLink		= JURI::root() . 'images/fbfiles/avatars' . $source;
//
//     	$profileLink	= JRoute::_('index.php?option=com_kunena&func=fbprofile&userid='.$profile->id, false);

		// as for kunena 1.6.4
		// $avatarLink	= '';
		// $profileLink = '';

		// $db			= JFactory::getDBO();
		// $query		= 'SELECT `avatar` FROM `#__kunena_users` WHERE `userid` = ' . $db->quote( $profile->id );
		// $db->setQuery( $query );
		// $kavatar		= $db->loadResult();

		// $avatarPath		= JPATH_ROOT.DS.'media'.DS.'kunena'.DS.'avatars'.DS.$kavatar;
		// if (JFile::exists($avatarPath))
		// {
		// 	$avatarLink		= JURI::root().'media/kunena/avatars/'.$kavatar;
		// }
		//$profileLink	= JRoute::_('index.php?option=com_kunena&func=profile&userid='.$profile->id);

		//

		$userKNN		= KunenaFactory::getUser($profile->id);
		$avatarLink		= $userKNN->getAvatarURL('kavatar');

		$profileKNN		= KunenaFactory::getProfile($profile->id);
		$profileLink	= $profileKNN->getProfileURL($profile->id, '');

		//$avatarLink	= KunenaAvatarKunena::_getURL($profile->id)
		//$profileLink	= KunenaProfileKunena::getProfileURL($profile->id);

		return array( $avatarLink, $profileLink );
	}

	private static function communitybuilder( $profile )
	{
		$files = JPATH_ROOT . DS . 'administrator' . DS . 'components' .DS. 'com_comprofiler' .DS. 'plugin.foundation.php';
		if (!JFile::exists($files)) return false;
		require_once( $files );
		cbimport('cb.database');
		cbimport('cb.tables');
		cbimport('cb.tabs');

		$user			= CBuser::getInstance( $profile->id );
		$user			= ($user) ? $user : CBuser::getInstance( null );
		$field			= $user->getField( 'avatar', null, 'php', 'none', 'list' );
		$avatarLink		= $field['avatar'];

		//$cbItemid		= getCBprofileItemid();
		//$profileLink	= JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$profile->id.'&Itemid='.$cbItemid, false);\
		$profileLink	= cbSef( 'index.php?option=com_comprofiler&amp;task=userProfile&amp;user='. $profile->id );

		return array( $avatarLink, $profileLink );
	}

	private static function gravatar( $profile )
	{
		$user			= JFactory::getUser($profile->id);

		$avatarLink		= 'http://www.gravatar.com/avatar/' . md5($user->email) . '?s=160';
		$avatarLink		= $avatarLink.'&d=wavatar';

		$profileLink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=profile&id='.$profile->id, false);

		return array( $avatarLink, $profileLink );
	}

	private static function phpbb( $profile )
	{
		$config 		= DiscussHelper::getConfig();
		$phpbbpath		= $config->get( 'layout_phpbb_path' );
		$phpbburl		= $config->get( 'layout_phpbb_url' );

		$phpbbDB		= $this->_getPhpbbDBO( $phpbbpath );
		$phpbbConfig	= $this->_getPhpbbConfig();
		$phpbbuserid	= 0;

		if(empty($phpbbConfig))
		{
			return false;
		}

		$juser	= JFactory::getUser( $profile->id );

		$sql	= 'SELECT '.$phpbbDB->nameQuote('user_id').', '.$phpbbDB->nameQuote('username').', '.$phpbbDB->nameQuote('user_avatar').', '.$phpbbDB->nameQuote('user_avatar_type').' '
				. 'FROM '.$phpbbDB->nameQuote('#__users').' WHERE '.$phpbbDB->nameQuote('username').' = '.$phpbbDB->quote($juser->username).' '
				. 'LIMIT 1';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObject();

		$phpbbuserid = empty($result->user_id)? '0' : $result->user_id;

		if(!empty($result->user_avatar))
		{
			switch($result->user_avatar_type)
			{
				case '1':
					$subpath	= $phpbbConfig->avatar_upload_path;
					$phpEx 		= JFile::getExt(__FILE__);
					$source		= $phpbburl.'/download/file.'.$phpEx.'?avatar='.$result->user_avatar;
					break;
				case '2':
					$source		= $result->user_avatar;
					break;
				case '3':
					$subpath	= $phpbbConfig->avatar_gallery_path;
					$source		= $phpbburl.'/'.$subpath.'/'.$result->user_avatar;
					break;
				default:
					$subpath 	= '';
					$source		= '';
			}
		}
		else
		{
			$sql	= 'SELECT '.$phpbbDB->nameQuote('theme_name').' '
					. 'FROM '.$phpbbDB->nameQuote('#__styles_theme').' '
					. 'WHERE '.$phpbbDB->nameQuote('theme_id').' = '.$phpbbDB->quote($phpbbConfig->default_style);
			$phpbbDB->setQuery($sql);
			$theme = $phpbbDB->loadObject();

			$defaultPath	= 'styles/'.$theme->theme_name.'/theme/images/no_avatar.gif';
			$source			= $phpbburl.'/'.$defaultPath;
		}

		$avatarLink		= $source;

		$profileLink	= $phpbburl.'/memberlist.php?mode=viewprofile&u='.$phpbbuserid;

		return array( $avatarLink, $profileLink );
	}

	private static function _getPhpbbDBO( $phpbbpath = null )
	{
		static $phpbbDB = null;

		if($phpbbDB == null)
		{
			$files			= JPATH_ROOT . DS . $phpbbpath . DS . 'config.php';

			if (!JFile::exists($files)) {
				$files	= $phpbbpath . DS . 'config.php';
				if (!JFile::exists($files)) {
					return false;
				}
			} else {
				return false;
			}

			require_once( $files );

			$host		= $dbhost;
			$user		= $dbuser;
			$password	= $dbpasswd;
			$database	= $dbname;
			$prefix		= $table_prefix;
			$driver		= $dbms;
			$debug		= 0;

			$options = array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

			$phpbbDB = JDatabase::getInstance( $options );
		}

		return $phpbbDB;
	}

	private static function _getPhpbbConfig()
	{
		$phpbbDB = $this->_getPhpbbDBO();

		if (!$phpbbDB)
		{
			return false;
		}

		$sql	= 'SELECT '.$phpbbDB->nameQuote('config_name').', '.$phpbbDB->nameQuote('config_value').' '
				. 'FROM '.$phpbbDB->nameQuote('#__config') . ' '
				. 'WHERE '.$phpbbDB->nameQuote('config_name').' IN ('.$phpbbDB->quote('avatar_gallery_path').', '.$phpbbDB->quote('avatar_path').', '.$phpbbDB->quote('default_style').')';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObjectList();

		if(empty($result))
		{
			return false;
		}

		$phpbbConfig = new stdClass();
		$phpbbConfig->avatar_gallery_path	= null;
		$phpbbConfig->avatar_upload_path	= null;
		$phpbbConfig->default_style			= 1;

		foreach($result as $row)
		{
			switch($row->config_name)
			{
				case 'avatar_gallery_path':
					$phpbbConfig->avatar_gallery_path = $row->config_value;
					break;
				case 'avatar_path':
					$phpbbConfig->avatar_upload_path = $row->config_value;
					break;
				case 'default_style':
					$phpbbConfig->default_style = $row->config_value;
					break;
			}
		}

		return $phpbbConfig;
	}

	private static function easyblog( $profile )
	{
		$file	= JPATH_ROOT . DS . 'components' . DS. 'com_easyblog' . DS . 'helpers' . DS . 'helper.php';

		jimport( 'joomla.filesystem.file' );

		if( !JFile::exists( $file ) )
		{
			return false;
		}
		require_once( $file );

		$profileEB	= EasyBlogHelper::getTable( 'Profile','Table' );
		$profileEB->load( $profile->id );

		return array( $profileEB->getAvatar() , $profileEB->getLink() );
	}
}
