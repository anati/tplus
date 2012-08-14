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

jimport('joomla.application.component.controller');

class EasyDiscussControllerSpools extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();
	}

	public function purge()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$db 	= JFactory::getDBO();
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_mailq' );

		$db->setQuery( $query );
		$db->Query();

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=spools' , JText::_( 'COM_EASYDISCUSS_MAILS_PURGED' ) , 'info' );
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mails		= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'info';

		if( empty( $mails ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_NO_MAIL_ID_PROVIDED');
			$type		= 'error';
		}
		else
		{
			$table		= DiscussHelper::getTable( 'MailQueue' );

			foreach( $mails as $id )
			{
				$table->load( $id );

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_SPOOLS_DELETE_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=spools' , $message , $type );
					return;
				}
			}
			$message	= JText::_('COM_EASYDISCUSS_SPOOLS_EMAILS_DELETED');
		}

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=spools' , $message , $type );
	}
}
