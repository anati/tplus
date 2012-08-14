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
jimport('joomla.filesystem.file');
require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'constants.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );

class EasyDiscussControllerAttachment extends JController
{
	function displayFile()
	{
		$id		= JRequest::getVar('id', '', 'GET');
		$config	= DiscussHelper::getConfig();
		
		if(empty($id))
		{
			return false;	
		}
		
		$attachment	= JTable::getInstance( 'Attachments' , 'Discuss' );
		if(!$attachment->load( $id ))
		{
			return false;
		}
		
		$path = $config->get( 'attachment_path' ) . DS;
		$file = JPATH_ROOT . DS . 'media' . DS . 'com_easydiscuss' . DS . $path . $attachment->path;
		if (!JFile::exists($file))
		{
		    return false;
		}
				
		header('Content-Description: File Transfer');
	    header('Content-Type: ' . $attachement->mime);
		header('Content-Disposition: inline');		
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    ob_clean();
	    flush();
	    readfile($file);
	    exit;
	}
	
	function getFile()
	{
		$id		= JRequest::getVar('id', '', 'GET');
		$config	= DiscussHelper::getConfig();
		
		if(empty($id))
		{
			return false;	
		}
		
		$attachment	= JTable::getInstance( 'Attachments' , 'Discuss' );
		if(!$attachment->load( $id ))
		{
			return false;
		}
		
		$path = $config->get( 'attachment_path' ) . DS;
		$file = JPATH_ROOT . DS . 'media' . DS . 'com_easydiscuss' . DS . $path . $attachment->path;
		if (!JFile::exists($file))
		{
		    return false;
		}
		
		$type = explode("/", $attachment->mime);
		
		
		header('Content-Description: File Transfer');
	    header('Content-Type: ' . $attachement->mime);
		header("Content-Disposition: attachment; filename=\"".basename($attachment->title)."\";" ); 	
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize($file));
	    ob_clean();
	    flush();
	    readfile($file);
	    exit;
	}
	
	function deleteFile($id)
	{
		$config	= DiscussHelper::getConfig();
	
		if(empty($id))
		{
			return false;	
		}
		
		$attachment	= JTable::getInstance( 'Attachments' , 'Discuss' );
		if(!$attachment->load( $id ))
		{
			return false;
		}
		
		$path = $config->get( 'attachment_path' ) . DS;
		$file = JPATH_ROOT . DS . 'media' . DS . 'com_easydiscuss' . DS . $path . $attachment->path;
		if (JFile::exists($file))
		{
		    if (!JFile::delete($file))
			{
			    return false;
			}
		}
		
		return $attachment->delete();
	}
}