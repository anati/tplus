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
	function __construct($config = array())
	{
		// Include the tables in path
		JTable::addIncludePath( DISCUSS_TABLES );

		$document	= JFactory::getDocument();

		$toolbar	= JToolbar::getInstance( 'toolbar' );
		$toolbar->addButtonPath( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_easydiscuss' . DS . 'assets' . DS . 'images');

		$ajaxData	=  "/*<![CDATA[*/
	var discuss_site = '" . rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easydiscuss' ."';
	var lang_direction	= '" . $document->direction . "';
	var discuss_auth	= '" . JUtility::getToken() . "';
/*]]>*/";
		$document->addScriptDeclaration( $ajaxData );

		require_once( DISCUSS_HELPERS . DS . 'loader.php' );

		$config		= DiscussHelper::getConfig();

		DisjaxLoader::_('admin' , 'js' , 'admin.assets' );

		$debugjs = (bool) JRequest::getVar('discuss_debugjs', $config->get('discuss_debugjs'));

		ob_start();
			$discussMediaPath = JPATH_ROOT . DS . 'media' . DS . 'com_easydiscuss' . DS . 'js';
			include($discussMediaPath . DS . 'easydiscuss.js');
			$output = ob_get_contents();
		ob_end_clean();

		$document->addScriptDeclaration( "/*<![CDATA[*/ $output /*]]>*/ " );


		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/administrator/components/com_easydiscuss/assets/css/reset.css' );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/administrator/components/com_easydiscuss/assets/css/style.css' );
		$document->addStyleSheet( rtrim( JURI::root() , '/' ) . '/components/com_easydiscuss/assets/css/common.css' );

		// For the sake of loading the core.js in Joomla 1.6 (1.6.2 onwards)
		if( DiscussHelper::getJoomlaVersion() >= '1.6' )
		{
			JHTML::_('behavior.framework');
		}

		parent::__construct($config);
	}

	/**
	 * Override parent's display method
	 *
	 * @since 0.1
	 */
	function display( $cachable = false, $urlparams = false )
	{
		$document	= JFactory::getDocument();

		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view'	, 'discuss' );
		$viewLayout	= JRequest::getCmd( 'layout', 'default' );
		$view		= $this->getView( $viewName, $viewType, '' );

 		// Set the layout
 		$view->setLayout($viewLayout);

 		$format		= JRequest::getCmd( 'format' , 'html' );

 		// Test if the call is for Ajax
 		if( !empty( $format ) && $format == 'ajax' )
 		{
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
				$disjax	= new Disjax();
				$disjax->script( 'alert("' . JText::sprintf( 'Method %1$s does not exists in this context' , $viewLayout ) . '");');
				$disjax->send();

				return;
			}

			// Execute method
			call_user_func_array( array( $view , $viewLayout ) , $arguments );
		}
		else
		{
			// Non ajax calls.
			// Get/Create the model
			if ($model = $this->getModel($viewName))
			{
				// Push the model into the view (as default)
				$view->setModel($model, true);
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
						// @todo: Display error about unknown layout.
						$view->$viewLayout();
					}
				}
			}
			else
			{
				$view->display();
			}


			// Add necessary buttons to the site.
			if( method_exists( $view , 'registerToolbar' ) )
			{
				$view->registerToolbar();
			}

			// Override submenu if needed
			if( method_exists( $view , 'registerSubmenu' ) )
			{
				$this->_loadSubmenu( $view->getName() , $view->registerSubmenu() );
			}
		}
	}

	/**
	 * Overrides parent method
	 **/
	public static function getInstance( $controllerName, $config = array() )
	{
		static $instances;

		if( !$instances )
		{
			$instances	= array();
		}

		// Set the controller name
		$className	= 'EasyDiscussController' . ucfirst( $controllerName );

		if( !isset( $instances[ $className ] ) )
		{
			if( !class_exists( $className ) )
			{
				jimport( 'joomla.filesystem.file' );
				$controllerFile	= DISCUSS_CONTROLLERS . DS . JString::strtolower( $controllerName ) . '.php';

				if( JFile::exists( $controllerFile ) )
				{
					require_once( $controllerFile );

					if( !class_exists( $className ) )
					{
						// Controller does not exists, throw some error.
						JError::raiseError( '500' , JText::sprintf('Controller %1$s not found' , $className ) );
					}
				}
				else
				{
					// File does not exists, throw some error.
					JError::raiseError( '500' , JText::sprintf('Controller %1$s.php not found' , $controllerName ) );
				}
			}

			$instances[ $className ]	= new $className();
		}
		return $instances[ $className ];
	}

	function _loadSubmenu( $viewName , $path = 'submenu.php' )
	{
		JHTML::_('behavior.switcher');

		//Build submenu
		$contents = '';
		ob_start();
		require_once( DISCUSS_ADMIN_ROOT . DS . 'views' . DS . $viewName . DS . 'tmpl' . DS . $path );

		$contents = ob_get_contents();
		ob_end_clean();

		$document	= JFactory::getDocument();

		$document->setBuffer($contents, 'modules', 'submenu');
	}

	function ajaxGetSystemString()
	{
	    $data = JRequest::getVar('data');
	    echo JText::_(strtoupper($data));
	}
}
