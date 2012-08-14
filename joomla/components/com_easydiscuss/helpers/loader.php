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

require_once( DISCUSS_HELPERS . DS . 'helper.php' );

class DisjaxLoader
{
	/**
	 * Function to add js file, js script block and css file
	 * to HEAD section
	 */	 	 	
	public static function _( $list, $type='js', $location='assets' )
	{
		$document	= JFactory::getDocument();
		$config		= DiscussHelper::getConfig();
		
		// Always load mootools first so it will not conflict.
		JHTML::_('behavior.mootools');
		
		// get file list
		$files 	= explode( ',', $list );
		
		// Compatibility fix
		if ($type=='js' && $location=='assets') $location = 'media';

		switch($location)
		{
		    case 'admin.assets':
		        $dir    = JURI::root() . 'administrator/components/com_easydiscuss/assets';
		        break;
			case 'assets':
				$dir = JURI::root() . 'components/com_easydiscuss/assets';
				break;
			case 'foundry':
				$dir = JURI::root() . 'media/foundry';
				break;
			case 'media':
				$dir = JURI::root() . 'media/com_easydiscuss';
				break;
			default:
				$theme = $config->get( 'layout_theme' );
				$dir = JURI::root() . 'components/com_easydiscuss/themes/' . $theme;
				break;
		}

		foreach( $files as $file )
		{
			if ( $type == 'js' )
			{
				$document->addScript( $dir . '/js/' . $file . '.js' );
			}
			elseif ( $type == 'css' )
			{
				$document->addStylesheet( $dir . '/css/' . $file . '.css');
			}
		}	
	}
}