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

jimport('joomla.application.component.controller');

class EasyDiscussController extends JController
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct()
	{
		// Load necessary css and javascript files.
		DiscussHelper::loadHeaders();

		//load the content plugins so that the content trigger will work.
		DiscussEventsHelper::importPlugin( 'content' );

		parent::__construct();
	}


	/**
	 * Override parent's display method
	 *
	 * @since 0.1
	 */
	function display( $cachable = false , $urlparams = false )
	{
		// echo 'debug';exit;
		$document	= JFactory::getDocument();

		$viewName	= JRequest::getCmd( 'view'		, 'index' );
		$viewLayout	= JRequest::getCmd( 'layout'	, 'default' );
		$view		= $this->getView( $viewName	, $document->getType() , '' );
		$format		= JRequest::getCmd( 'format' 	, 'html' );
		$tmpl		= JRequest::getCmd( 'tmpl' 		, 'html' );

		if( !empty( $format ) && $format == 'ajax' )
		{
			if( !JRequest::checkToken() && !JRequest::checkToken( 'get' ) )
			{
				echo 'Invalid token';
				exit;
			}

			$data		= JRequest::get( 'POST' );
			$arguments	= array();

			foreach( $data as $key => $value )
			{
				if( JString::substr( $key , 0 , 5 ) == 'value' )
				{
					if(is_array($value))
					{
						$arrVal    = array();
						foreach($value as $val)
						{
							$item   = $val;
							$item   = stripslashes($item);
							$item   = rawurldecode($item);
							$arrVal[]   = $item;
						}

						$arguments[]	= $arrVal;
					}
					else
					{
						$val			= stripslashes( $value );
						$val			= rawurldecode( $val );
						$arguments[]	= $val;
					}
				}
			}

			if(!method_exists( $view , $viewLayout ) )
			{
				$ajax	= new Disjax();
				$ajax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
				$ajax->send();

				return;
			}

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{
			$config = DiscussHelper::getConfig();
			$theme = $config->get( 'layout_theme' );

			$document->addStyleSheet( rtrim(JURI::root(), '/') . '/components/com_easydiscuss/assets/css/common.css' );

			$enableRecaptcha	= $config->get('antispam_recaptcha');
			$publicKey			= $config->get('antispam_recaptcha_public');

			if(  $enableRecaptcha && !empty( $publicKey ) )
			{
				$document->addScript("http://www.google.com/recaptcha/api/js/recaptcha_ajax.js");
			}

			// load theme css
			DiscussHelper::loadThemeCss();


			jimport( 'joomla.filesystem.file' );


			$ie_css = '';

			$mainframe = JFactory::getApplication();
			// load IE css file
			if ( JFile::exists( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . $theme . DS . 'css' . DS . 'ie.css' ) ) {
				$ie_css = '
	<!--[if IE]>
	<link rel="stylesheet" href="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/themes/'.$theme.'/css/ie.css" type="text/css" />
	<![endif]-->';
				$mainframe->addCustomHeadTag($ie_css);
			}

			// load IE7 specific file
			if ( JFile::exists( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . $theme . DS . 'css' . DS . 'ie7.css' ) ) {
				$ie_css = '
	<!--[if IE 7]>
	<link rel="stylesheet" href="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/themes/'.$theme.'/css/ie7.css" type="text/css" />
	<![endif]-->';
				$mainframe->addCustomHeadTag($ie_css);
			}

			// load IE8 specific file
			if ( JFile::exists( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . $theme . DS . 'css' . DS . 'ie8.css' ) ) {
				$ie_css = '
	<!--[if gt IE 7]>
	<link rel="stylesheet" href="'.rtrim(JURI::root(), '/').'/components/com_easydiscuss/themes/'.$theme.'/css/ie8.css" type="text/css" />
	<![endif]-->';
				$mainframe->addCustomHeadTag($ie_css);
			}

			// Non ajax calls.
			require_once( DISCUSS_CLASSES . DS . 'themes.php' );
			require_once( DISCUSS_HELPERS . DS . 'helper.php' );

			// Prepare class names for wrapper
			$cat_id			= JRequest::getInt( 'category_id', '', 'GET' );
			$cat_cls_name	= $cat_id ? ' category-' . $cat_id : '';
			$wrapper_sfx	= htmlspecialchars($config->get( 'layout_wrapper_sfx', '' ));

			// Set the wrapper.
			echo '<div id="discuss-wrapper" class="discuss-wrap'.$wrapper_sfx.$cat_cls_name.'">';

			$print = JRequest::getBool('print');

			// We allow 3rd party to show jomsocial's toolbar even if integrations are disabled.
			$showJomsocial	= JRequest::getBool( 'showJomsocialToolbar' , true );



			if( $config->get( 'integrations_jomsocial_toolbar' ) && $format != 'pdf' && $format != 'phocapdf' && $tmpl != 'component' || $showJomsocial )
			{
				if(JFile::exists(JPATH_ROOT . DS . 'components' . DS. 'com_community' . DS . 'libraries' . DS .'core.php'))
				{
					require_once( JPATH_ROOT . DS . 'components' . DS. 'com_community' . DS . 'libraries' . DS .'core.php' );
					require_once( JPATH_ROOT . DS . 'components' . DS. 'com_community' . DS . 'libraries' . DS .'toolbar.php' );

					$appsLib	= CAppPlugins::getInstance();
					$appsLib->loadApplications();

					$appsLib->triggerEvent( 'onSystemStart' , array() );

					if( class_exists( 'CToolbarLibrary' ) )
					{
						echo '<div id="community-wrap">';
						if( method_exists( 'CToolbarLibrary' , 'getInstance' ) )
						{
							$jsToolbar  = CToolbarLibrary::getInstance();
							echo $jsToolbar->getHTML();
						}
						else
						{
						    echo CToolbarLibrary::getHTML();
						}
						echo '</div>';
					}
				}
			}

			// Allow 3rd party to hide our headers
			$hideToolbar	= JRequest::getBool( 'hideToolbar' , false );

			if(!$print && $format != 'pdf' && !$hideToolbar )
			{
				echo $this->getToolbar( $view->getName() , $view->getLayout() );
			}

			if( $viewLayout != 'default' )
			{
				if( $cachable )
				{
					$cache	= JFactory::getCache( 'com_easydiscuss' , 'view' );
					$cache->get( $view , $viewLayout );
				}
				else
				{
					if( !method_exists( $view , $viewLayout ) )
					{
						$view->display();
					}
					else
					{
						$view->$viewLayout();
					}
				}
			}
			else
			{
				$view->display();
			}

			// Powered by link
			

			echo '<input type="hidden" class="easydiscuss-token" value="' . JUtility::getToken() . '" />';

			// End wrapper.
			echo '</div>';
		}
	}

	public function getToolbar( $currentView )
	{
		$template	= new DiscussThemes();
		$user		= JFactory::getUser();
		$config     = DiscussHelper::getConfig();

		$acl = DiscussHelper::getHelper( 'ACL' );

		// Set active menu.
		$views	= array( 'index' => '' , 'tags' => '', 'categories'=>'', 'search' => '', 'profile' => '', 'create' => '' , 'users' => '' , 'badges' => '');
		$views	= (object) $views;

		// search query
		$query	= JRequest::getString( 'query' , '' );


		// @rule: If a user is viewing a specific category, we need to ensure that it's setting the correct active menu
		if( JRequest::getInt( 'category_id' , 0 ) !== 0 )
		{
			$currentView	= 'categories';
		}

		if( isset( $views->$currentView ) )
		{
			$my		= JFactory::getUser();

			if( $currentView == 'profile' )
			{
				if( $my->id == JRequest::getInt( 'id' ) || JRequest::getInt( 'id' , 0 ) == 0 )
				{
					$views->$currentView	= ' active';
				}
				else
				{

					$views->index	= ' active';
				}
			}
			else
			{
				$views->$currentView	= ' active';
			}
		}
		else
		{
			// View does not exist, so we set the default 'latest' to be active.
			if( JRequest::getVar( 'layout' ) == 'submit' && $currentView == 'post' )
			{
				$views->create	= ' active';
			}
			elseif( $currentView == 'tag' )
			{
				$views->tags	= ' active';
			}
			elseif( $currentView == 'categories' )
			{
				$views->tags	= ' active';
			}
			elseif( $currentView == 'users' )
			{
				$views->tags	= ' active';
			}
			else
			{
				$views->index		= ' active';
			}
		}

		$category   = '';

		$category   = JRequest::getInt( 'category_id' , 0 );
		$category   = '&category=' . $category;

		$headers	= new JObject();
		$headers->title	= $config->get( 'main_title' );
		$headers->desc	= $config->get( 'main_description' );

		$model			= $this->getModel( 'Notification' );
		$notifications	= $model->getTotalNotifications( $user->id );

		$return			= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false );
		$return			= base64_encode( $return );


		$template->set( 'notifications' , $notifications );
		$template->set( 'return'		, $return );
		$template->set( 'category'	, $category );
		$template->set( 'acl'	, $acl );
		$template->set( 'user' 	, $user );
		$template->set( 'views' , $views );
		$template->set( 'config' , $config );
		$template->set( 'headers', $headers );
		$template->set( 'query', $query );

		echo $template->fetch( 'toolbar.php' );
	}
}
