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

jimport( 'joomla.application.component.view');

class EasyDiscussViewPost extends EasyDiscussView
{
	var $err	= null;
	
	function deleteAttachment( $id )
	{
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'controllers' . DS . 'attachment.php');
	
		$disjax		= new Disjax();
		
		$controller	= new EasyDiscussControllerAttachment();
		
		$msg		= JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_FAILED');
		$msgClass	= 'dc_error';
		if($controller->deleteFile($id))
		{
			$msg		= JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_SUCCESS');
			$msgClass	= 'dc_success';
			$disjax->script( 'Foundry( "#dc-attachments-'.$id.'" ).remove();' );
		}
		
		$disjax->assign( 'dc_post_notification .msg_in' , $msg );
		$disjax->script( 'Foundry( "#dc_post_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
		$disjax->script( 'Foundry( "#button-delete-att-'.$id.'" ).attr("disabled", "");' );
		
		$disjax->send();
	}
}