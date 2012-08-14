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

require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'date.php' );
require_once( DISCUSS_HELPERS . DS . 'input.php' );
require_once( DISCUSS_HELPERS . DS . 'filter.php' );

class EasyDiscussControllerRules extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();
	}

	function remove()
	{
		// Request forgeries check
		JRequest::checkToken() or die( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$ids	= JRequest::getVar( 'cid' );

		// @task: Sanitize the id's to integer.
		foreach( $ids as $id )
		{
			$id		= (int) $id;

			$rule	= DiscussHelper::getTable( 'BadgesRules' );
			$rule->load( $id );
			$rule->delete();
		}

		$app->redirect( 'index.php?option=com_easydiscuss&view=rules' , JText::_( 'Rule is deleted from the system. If the rule is associated with any badges, the rule will also be removed from the badge.') );
	}


	function install()
	{
		// Request forgeries check
		JRequest::checkToken() or die( 'Invalid Token' );

		$file	= JRequest::getVar( 'rule' , '' , 'FILES' );
		$app	= JFactory::getApplication();

		// @task: If there's no tmp_name in the $file, we assume that the data sent is corrupted.
		if( !isset( $file[ 'tmp_name' ] ) )
		{
			$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' , JText::_( 'Sorry but the system did not provide us with the appropriate file data.') , 'error' );
			$app->close();
		}

		if( $file[ 'type' ] == 'application/zip')
		{
			$jConfig	= JFactory::getConfig();
			$path		= rtrim( $jConfig->getValue( 'tmp_path' ) , DS ) . DS . $file['name'];

			// @rule: Copy zip file to temporary location
			JFile::copy( $file[ 'tmp_name' ] , $path );

			jimport( 'joomla.filesystem.archive' );
			$tmp		= md5( JFactory::getDate()->toMysQL() );
			$dest		= rtrim( $jConfig->getValue( 'tmp_path' ) , '/' ) . DS . $tmp;

			JArchive::extract( $path , $dest );
			
			$files		= JFolder::files( $dest );

			if( empty( $files ) )
			{
				// Try to do a level deeper in case the zip is on the outer.
				$folder	= JFolder::folders( $dest );

				if( !empty( $folder ) )
				{
					$files	= JFolder::files( $dest . DS . $folder[0] );
					$dest	= $dest . DS . $folder[0];
				}
			}

			if( empty( $files ) )
			{
				$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' , JText::_( 'Sorry but we cannot find any .xml files in the zip archive.') , 'error' );
				$app->close();
			}
		}
		else
		{
			$files	= array( $file['tmp_name'] );
		}

		foreach( $files as $file )
		{
			$this->installXML( $dest . DS . $file );
		}

		$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' , JText::_( 'New rules installed successfully.') );
		$app->close();	
	}

	private function installXML( $path )
	{
		// @task: Try to read the temporary file.
		$contents	= JFile::read( $path );

		$parser		= JFactory::getXMLParser( 'Simple' );
		$parser->loadString( $contents );

		// @task: Test for appropriate manifest type
		if( $parser->document->name() != 'easydiscuss' )
		{
			$app->redirect( 'index.php?option=com_easydiscuss&view=rules&layout=install' , JText::_( 'Invalid manifest definition in xml file. File should be for "easydiscuss"') , 'error' );
			$app->close();
		}

		// @task: Bind appropriate values from the xml file into the database table.
		$rule		= DiscussHelper::getTable( 'Rules' );
		$elements	= $parser->document->children();

		foreach( $elements as $element )
		{
			$property	= $element->name();
			$rule->set( $property , $element->data() );
		}

		$rule->set( 'published' , 1 );
		$rule->set( 'created'	, JFactory::getDate()->toMySQL() );

		if( $rule->exists( $rule->get( 'command') ) )
		{
			return false;
		}

		return $rule->store();
	}
}
