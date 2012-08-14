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

jimport( 'joomla.filter.filteroutput');
jimport( 'joomla.application.router');

class DiscussRouter extends JRouter
{
	public static function _($url, $xhtml = true, $ssl = null)
	{
		static $eUri = array();

		// to test if the Itemid is there or not.
		// $jURL	= JRoute::_($url, false);
		$jURL   = $url;

		// convert the string to variable so that we can access it.
		parse_str($jURL);

		if(empty($Itemid))
		{
			$tmpId  = '';

			if(empty($view))
				$view   = 'index';

			// make compatible with version 1.1.x where new question will be view=post&layout=submit
			if( $view == 'post')
			{
			    $tmpView = ( empty($layout) ) ? $view : 'newquestion';
				if(empty($eUri[$tmpView]))
				{
					$tmpId			= ( !empty($layout) ) ? DiscussRouter::getItemId($view, $layout) :  DiscussRouter::getItemId($view);
					$eUri[$tmpView]	= $tmpId;
				}
				else
				{
					$tmpId = $eUri[$tmpView];
				}
			}
			else
			{
				if(empty($eUri[$view]))
				{
					$tmpId			= DiscussRouter::getItemId($view);
					$eUri[$view]	= $tmpId;
				}
				else
				{
					$tmpId = $eUri[$view];
				}
			}

			//check if there is any anchor in the link or not.
			$pos = JString::strpos($url, '#');
			if ($pos === false)
			{
				$url .= '&Itemid='.$tmpId;
			}
			else
			{
				$url = JString::str_ireplace('#', '&Itemid='.$tmpId.'#', $url);
			}

			return JRoute::_($url, $xhtml, $ssl);
		}
		else
		{
			//Itemid exists. Just use it.
			return JRoute::_($url, $xhtml, $ssl);
		}
	}

	public static function isSefEnabled()
	{
		$jConfig	= JFactory::getConfig();
		$isSef      = false;

		//check if sh404sef enabled or not.
		if(JFile::exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sh404sef'.DS.'sh404sef.class.php'))
		{
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_sh404sef'.DS.'sh404sef.class.php');
			if( class_exists( 'shRouter' ) )
			{
				$sefConfig = shRouter::shGetConfig();

				if ($sefConfig->Enabled)
					$isSef  = true;
			}
		}

		// if sh404sef not enabled, we check on joomla
		if(! $isSef)
		{
			$isSef = $jConfig->getValue( 'sef' );
		}

		return $isSef;
	}

	public static function getCategoryAliases( $categoryId )
	{
		$table	= DiscussHelper::getTable( 'Category' );
		$table->load( $categoryId );

		$items		= array();
		self::recurseCategories( $categoryId , $items );

		$items		= array_reverse( $items );

		return $items;
	}

	public static function recurseCategories( $currentId , &$items )
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'alias' ) . ',' . $db->nameQuote( 'parent_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_category' ) . ' WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $currentId );
		$db->setQuery( $query );
		$result	= $db->loadObject();

		if( !$result )
		{
			return;
		}

		$items[]	= $result->alias;

		if( $result->parent_id != 0 )
		{
			self::recurseCategories( $result->parent_id , $items );
		}
	}

	public static function getAlias( $tableName , $key )
	{
		$table	= DiscussHelper::getTable( $tableName );
		$table->load( $key );

		if( isset( $table->alias ) )
		{
			return $table->alias;
		}

		return $table->alias;
	}

	public static function replaceAccents( $string )
	{
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		return str_replace($a, $b, $string);
	}

	public static function getPostAlias( $id , $external = false )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM #__discuss_posts WHERE id=' . $db->Quote( $id );
		$db->setQuery( $query );
		$data	= $db->loadObject();
		$config	= DiscussHelper::getConfig();

		// Empty alias needs to be regenerated.
		if( empty($data->alias) )
		{
			$data->alias	= JFilterOutput::stringURLSafe( $data->title );
			$i			= 1;

			while( DiscussRouter::_isAliasExists( $data->alias, 'post' , $id ) )
			{
				$data->alias	= JFilterOutput::stringURLSafe( $data->title ) . '-' . $i;
				$i++;
			}

			$query	= 'UPDATE #__discuss_posts SET alias=' . $db->Quote( $data->alias ) . ' '
					. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
			$db->setQuery( $query );
			$db->Query();
		}

		if( $external )
		{
			$uri		= JURI::getInstance();
			return $uri->toString( array('scheme', 'host', 'port')) . '/' . $data->alias;
		}

		return $data->alias;
	}

	public static function getTagAlias( $id )
	{
		$table	= DiscussHelper::getTable( 'Tags' );
		$table->load( $id );
		return $table->alias;
	}

	public static function getUserAlias( $id )
	{
		$config = DiscussHelper::getConfig();
		$profile	= DiscussHelper::getTable( 'Profile' );
		$profile->load($id);

		$urlname    	= (empty($profile->alias)) ? $profile->user->username : $profile->alias;

		return JFilterOutput::stringURLSafe( $urlname );
	}

	public static function getRoutedURL( $url , $xhtml = false , $external = false )
	{
		if( !$external )
		{
			return DiscussRouter::_( $url , $xhtml );
		}

		$mainframe 	= JFactory::getApplication();
		$uri		= JURI::getInstance( JURI::base() );

		//To fix 1.6 Jroute issue as it will include the administrator into the url path.
		$url 	= str_replace('/administrator/', '/', DiscussRouter::_( $url  , $xhtml ));

		if( $mainframe->isAdmin() && DiscussRouter::isSefEnabled() )
		{
			if( DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				JFactory::$application = JApplication::getInstance('site');
			}

			jimport( 'joomla.application.router' );
			require_once (JPATH_ROOT . DS . 'includes' . DS . 'router.php');
			require_once (JPATH_ROOT . DS . 'includes' . DS . 'application.php');

			$router = new JRouterSite( array('mode'=>JROUTER_MODE_SEF) );
			$urls	= $router->build($url)->toString(array('path', 'query', 'fragment'));
			$urls	= rtrim( JURI::root(), '/') . '/' . ltrim( str_replace('/administrator/', '/', $urls) , '/' );

			$container  = explode('/', $urls);
			$container	= array_unique($container);
			$urls = implode('/', $container);

			if( DiscussHelper::getJoomlaVersion() >= '1.6')
			{
				JFactory::$application = JApplication::getInstance('administrator');
			}

			return $urls;
		}
		else
		{
			$url	= rtrim($uri->toString( array('scheme', 'host', 'port', 'path')), '/' ) . '/' . ltrim( $url , '/' );
			$url	= str_replace('/administrator/', '/', $url);

			if( DiscussRouter::isSefEnabled() )
			{
				$container  = explode('/', $url);
				$container	= array_unique($container);
				$url = implode('/', $container);
			}

			return $url;
		}
	}

	public static function _isAliasExists( $alias, $type='post', $id='0')
	{
		$db		= JFactory::getDBO();

		switch($type)
		{
			case 'badge':
				$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_badges' ) . ' '
					. 'WHERE ' . $db->namequote( 'alias' ) . '=' . $db->Quote( $alias );
				break;
			case 'tag':
				$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_tags' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $alias );
				break;
			case 'post':
			default:
				$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__discuss_posts' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'alias' ) . '=' . $db->Quote( $alias ) . ' '
						. 'AND ' . $db->nameQuote( 'id' ) . '!=' . $db->Quote( $id );
				break;
		}

		$db->setQuery( $query );

		$result = $db->loadAssocList();
		$count	= count($result);

		if( $count == '1' && !empty($id))
		{
			return ($id == $result['0']['id'])? false : true;
		}
		else
		{
			return ($count > 0) ? true : false;
		}
	}

	function getEntryRoute( $id )
	{
		$url	= 'index.php?option=com_easydiscuss&view=posts&id=' . $id;
		$url	.= '&Itemid=' . DiscussRouter::getItemId('entry');

		return $url;
	}

	public static function getItemId( $view='', $layout='' )
	{
		$db	= JFactory::getDBO();

		switch($view)
		{
			case 'categories':
				$view = 'categories';
				break;
			case 'profile':
				$view='profile';
				break;
			case 'post':
				$view='post';
				break;
			case 'ask':
				$view='ask';
				break;
			case 'tags':
				$view = 'tags';
				break;
			case 'notification':
				$view = 'notification';
				break;
			case 'subscriptions':
				$view = 'subscriptions';
				break;
			case 'search':
			case 'index':
			default:
				$view = 'index';
				break;
		}

		$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easydiscuss&view='.$view ) . ' '
				. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
		$db->setQuery( $query );
		$itemid = $db->loadResult();

		if( empty( $itemid ) && $view == 'post')
		{
			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' );

			if( empty( $layout ) )
				$query	.= ' WHERE ' . $db->nameQuote( 'link' ) . ' = ' . $db->Quote( 'index.php?option=com_easydiscuss&view=' . $view );
			else
			    $query	.= ' WHERE ' . $db->nameQuote( 'link' ) . ' = ' . $db->Quote( 'index.php?option=com_easydiscuss&view=' . $view . '&layout=' . $layout  );

			$query	.= ' AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';


			// echo var_dump( $layout ); exit;

			$db->setQuery( $query );
			$itemid = $db->loadResult();
		}

		// @rule: Try to fetch based on the current view.
		if( empty( $itemid ) && $view != 'post')
		{
		    //post view wil be abit special bcos of its layout 'submit'

			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'link' ) . ' LIKE ' . $db->Quote( 'index.php?option=com_easydiscuss&view=' . $view . '%' ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
			$db->setQuery( $query );
			$itemid = $db->loadResult();
		}

		if(empty($itemid))
		{
			$query	= 'SELECT ' . $db->nameQuote('id') . ' FROM ' . $db->nameQuote( '#__menu' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'link' ) . '=' . $db->Quote( 'index.php?option=com_easydiscuss&view=index' ) . ' '
					. 'AND ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( '1' ) . ' LIMIT 1';
			$db->setQuery( $query );
			$itemid = $db->loadResult();
		}

		return !empty($itemid)? $itemid : 1;
	}

	public static function encodeSegments($segments)
	{
        return JFactory::getApplication()->getRouter()->_encodeSegments($segments);
    }

}
