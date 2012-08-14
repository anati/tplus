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

require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'constants.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'image.php' );
require_once( DISCUSS_HELPERS . DS . 'date.php' );
require_once( DISCUSS_HELPERS . DS . 'integrate.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );

class DiscussProfile extends JTable
{
	var $id         	= null;
	var $nickname   	= null;
	var $avatar      	= null;
	var $description 	= null;
	var $url         	= null;
	var $params      	= null;
	var $user			= null;
	var $alias			= null;
	var $points			= null;
	var $latitude		= null;
	var $longitude		= null;
	var $location 		= null;
	var $signature		= null;

	/*
	 * Below attribute are the virtual which created when user is being loaded.
	 *
	 * numPostCreated
	 * numPostAnswered
	 * created
	 */

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__discuss_users' , 'id' , $db );

		$this->numPostCreated	= 0;
		$this->numPostAnswered	= 0;
		$this->profileLink      = '';
		$this->avatarLink		= '';

	}

	public function bind( $data , $ignore = array() )
	{
		parent::bind( $data );

		$this->url	= $this->_appendHTTP( $this->url );

		$this->user	= JFactory::getUser($this->id);

		//default to nickname for blogger alias if empty
		if(empty($this->alias))
		{
			$this->alias	= $this->nickname;
		}

		if ( empty($this->alias) )
		{
			$this->alias	= $this->user->username;
		}

		$this->alias	= DiscussHelper::permalinkSlug($this->alias);

		return true;
	}

	function _createDefault( $id )
	{
		$db	= $this->getDBO();

		$user	= JFactory::getUser($id);
		$date   = JFactory::getDate();

		$obj				= new stdClass();
		$obj->id 			= $user->id;
		$obj->nickname		= $user->name;
		$obj->avatar		= 'default.png';
		$obj->description 	= '';
		$obj->url			= '';
		$obj->params		= '';

		//default to username for blogger alias
		$obj->alias		= $user->username;

		$db->insertObject('#__discuss_users', $obj);
	}

	/**
	 * override load method.
	 * if user record not found in eblog_profile, create one record.
	 *
	 */
	function load( $id = null , $reset = true )
	{
		static $users = null;

		if( !isset( $users[ $id ] ) )
		{
			if((! parent::load($id)) && ($id != 0))
			{
				$this->_createDefault($id);
			}
			parent::load( $id );

			$this->numPostCreated	= $this->getNumTopicPosted();
			$this->numPostAnswered	= $this->getNumTopicAnswered();

			$user	= JFactory::getUser($id);
			$this->user	= $user;

			$users[ $id ] = $this;
		}
		else
		{
			$this->bind( $users[ $id ] );
		}

		return $users[ $id ];
	}

	function store( $updateNulls = false )
	{
		$tmpNumPostCreated   = $this->numPostCreated;
		$tmpNumPostAnswered  = $this->numPostAnswered;
		$tmpProfileLink  	 = $this->profileLink;
		unset($this->numPostCreated);
		unset($this->numPostAnswered);
		unset($this->profileLink);
		unset($this->avatarLink);

		$result	= parent::store();

		if($result)
		{
			$this->numPostCreated	= $tmpNumPostCreated;
			$this->numPostAnswered	= $tmpNumPostAnswered;
			$this->profileLink		= $tmpProfileLink;
		}

		return $result;
	}

	function setUser( $my )
	{
		$this->load( $my->id );
		$this->user = $my;
	}

	function getLink()
	{
		if(!isset($this->profileLink) || empty($this->profileLink))
		{
			$integrate	= new DiscussIntegrate;
			$field		= $integrate->getField($this);
			$this->profileLink	=  $field[ 'profileLink' ];
		}

		return $this->profileLink;
	}

	function getLinkHTML( $defaultGuestName = '' )
	{
		if ($this->id == 0)
		{
			return $this->getName($defaultGuestName);
		}
		return '<a href="'.$this->getLink().'" title="'.$this->getName().'">'.$this->getName().'</a>';
	}

	public function addPoint( $point )
	{
		$this->points	+= $point;
	}

	function getName( $default = '' )
	{
		if($this->id == 0)
		{
			return $default ? $default : JText::_('COM_EASYDISCUSS_GUEST');
		}

		$config = DiscussHelper::getConfig();
		$displayname    = $config->get('layout_nameformat');

		switch($displayname)
		{
			case "name" :
				$name = $this->user->name;
				break;
			case "username" :
				$name = $this->user->username;
				break;
			case "nickname" :
			default :
				$name = (empty($this->nickname)) ? $this->user->name : $this->nickname;
				break;
		}
		return $name;
	}

	function getId(){
		return $this->id;
	}

	public function getOriginalAvatar()
	{
		jimport( 'joomla.filesystem.file' );
		$config 	= DiscussHelper::getConfig();

		if( $config->get( 'layout_avatarIntegration') != 'default' )
		{
			return false;
		}

		$path 	= JPATH_ROOT . DS . trim( $config->get( 'main_avatarpath' ) , DS );

		// If original image doesn't exist, skip this
		if( !JFile::exists( $path . DS . 'original_' . $this->avatar ) )
		{
			return false;
		}

		$path 	= trim( $config->get( 'main_avatarpath') , '/' ) . '/' . 'original_' . $this->avatar;
		$uri 	= rtrim( JURI::root() , '/' );
		$uri   .= '/' . $path;
		return $uri;
	}

	function getAvatar($isThumb = true)
	{
		if(!isset($this->avatarLink) || empty($this->avatarLink))
		{
			$integrate	= new DiscussIntegrate;
			$field		= $integrate->getField($this);
			$this->avatarLink	=  $field[ 'avatarLink' ];
		}

		return $this->avatarLink;
	}

	function getDescription(){
		return $this->description;
	}

	function getWebsite(){
		return $this->url;
	}

	function getParams(){
		return $this->params;
	}

	function getUserType(){
		return $this->user->usertype;
	}

	function _appendHTTP($url)
	{
		$returnStr	= '';
		$regex = '/^(http|https|ftp):\/\/*?/i';
		if (preg_match($regex, trim($url), $matches)) {
			$returnStr	= $url;
		} else {
			$returnStr	= 'http://' . $url;
		}

		return $returnStr;
	}

	function getRSS()
	{
		return DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=profile&id=' . $this->id );
	}

	function getAtom()
	{
		return DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=profile&id=' . $this->id, true );
	}

	function getNumTopicPosted()
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT COUNT(1) AS CNT FROM `#__discuss_posts`';
		$query  .= ' WHERE `user_id` = ' . $db->Quote($this->id);
		$query  .= ' AND `parent_id` = 0';
		$query	.= ' AND `published` = 1';

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Retrieve the number of replies the user has posted
	 **/
	function getNumTopicAnswered()
	{
		$db = JFactory::getDBO();

		$query  = 'SELECT COUNT(a.`id`) AS CNT FROM `#__discuss_posts` AS a ';
		$query	.= ' INNER JOIN #__discuss_posts AS b ';
		$query	.= ' ON a.`parent_id`=b.`id`';
		$query  .= ' AND a.`parent_id` != 0';
		$query  .= ' WHERE a.`user_id` = ' . $db->Quote($this->id);
		$query	.= ' AND a.`published` = 1';
		$query	.= ' AND b.`published` = 1';

		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Retrieve the total number of tags created by the user
	 **/
	public function getTotalTags()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_tags' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$total	= $db->loadResult();

		return $total;
	}

	function getDateJoined()
	{
		$config	= DiscussHelper::getConfig();

		$date   = DiscussDateHelper::getDate($this->user->registerDate);
		return $date->toFormat( '%d/%m/%Y');
	}

	function getLastOnline()
	{
		$config	= DiscussHelper::getConfig();

		$date   = DiscussDateHelper::getDate($this->user->lastvisitDate);
		return $date->toFormat( '%d/%m/%Y');
	}

	function getURL( $raw = false , $xhtml = false )
	{
		$url	= 'index.php?option=com_easydiscuss&view=profile&id=' . $this->id;
		$url	= $raw ? $url : DiscussRouter::_( $url , $xhtml );

		return $url;
	}

	function isOnline()
	{
		static	$loaded	= array();

		if( !isset( $loaded[ $this->id ] ) )
		{
			$db		= JFactory::getDBO();
			$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__session' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'userid' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . $db->nameQuote( 'client_id') . '<>' . $db->Quote( 1 );
			$db->setQuery( $query );

			$loaded[ $this->id ]	= $db->loadResult() > 0 ? true : false;
		}
		return $loaded[ $this->id ];
	}

	/**
	 * Get a list of badges for this user.
	 *
	 * @access	public
	 * @return	Array	An array of DiscussTableBadges
	 **/
	public function getBadges()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_badges' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'badge_id' ) . '=b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );

		$result	= $db->loadObjectList();
		$badges	= array();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $res )
		{
			$badge	= DiscussHelper::getTable( 'Badges' );
			$badge->bind( $res );

			$badges[]	= $badge;
		}

		return $badges;
	}

	public function getTotalBadges()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_badges_users' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_badges' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'badge_id' ) . '=b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $this->id ) . ' '
				. 'AND b.' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function updatePoints()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'points' ) . ' FROM '
				. $db->nameQuote( '#__discuss_users' ) . ' WHERE '
				. $db->nameQuote( 'id' ) . '=' . $db->Quote( $this->id );
		$db->setQuery($query);

		$this->points	= $db->loadResult();
	}

	public function getSignature( $raw = false )
	{
		if( $raw )
		{
			return $this->signature;
		}

		return Parser::bbcode( $this->signature );
	}

	public function getPoints()
	{
		$config		= DiscussHelper::getConfig();

		if( $config->get( 'integration_aup' ) )
		{
			return DiscussHelper::getHelper( 'aup' )->getUserPoints( $this->id );
		}

		return $this->points;
	}
}
