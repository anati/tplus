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

class EasyDiscussControllerSettings extends EasyDiscussController
{
	function apply()
	{
		$mainframe	= JFactory::getApplication();
		$result		= $this->_store();
		$active		= JRequest::getString( 'active' , '' );

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=settings&active=' . $active , $result['message'] , $result['type'] );
	}

	function save()
	{
		$mainframe	= JFactory::getApplication();
		$result		= $this->_store();

		$mainframe->redirect( 'index.php?option=com_easydiscuss' , $result['message'] , $result['type'] );
	}

	function _store()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$model		= $this->getModel( 'Settings' );

			$postArray	= JRequest::get( 'post' );
			$saveData	= array();

			// Unset unecessary data.
			unset( $postArray['task'] );
			unset( $postArray['option'] );
			unset( $postArray['c'] );

			foreach( $postArray as $index => $value )
			{
				if( $index == 'integration_google_adsense_code' )
				{
					$value	= str_ireplace( ';"' , ';' , $value );
				}

				if( $index != 'task' );
				{
					$saveData[ $index ]	= $value;
				}

			}

			if( $model->save( $saveData ) )
			{
				$message	= JText::_( 'COM_EASYDISCUSS_CONFIGURATION_SAVED' );
			}
			else
			{
				$message	= JText::_( 'COM_EASYDISCUSS_CONFIGURATION_SAVE_ERROR' );
				$type		= 'error';
			}
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type		= 'error';
		}

		return array( 'message' => $message , 'type' => $type);
	}

	/**
	* Save the Email Template.
	*/
	function saveEmailTemplate()
	{
		$mainframe 	= JFactory::getApplication();
		$file 		= JRequest::getVar('file', '', 'POST' );
		$filepath	= JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'themes'.DS.'default'.DS.$file;
		$content	= JRequest::getVar( 'content' , '' , 'POST' , '' , JREQUEST_ALLOWRAW );
		$msg		= '';
		$msgType	= '';

		$status 	= JFile::write($filepath, $content);
		if(!empty($status))
		{
			$msg = JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_SUCCESS');
			$msgType = 'info';
		}
		else
		{
			$msg = JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_FAIL');
			$msgType = 'error';
		}

		$mainframe->enqueueMessage($msg);
		$mainframe->redirect('index.php?option=com_easydiscuss&view=settings&layout=editEmailTemplate&file='.$file.'&msg='.$msg.'&msgtype='.$msgType.'&tmpl=component&browse=1');
	}
}
