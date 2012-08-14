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

class EasyDiscussControllerSubscription extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();
	}

	function remove()
	{
		$subs		= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $subs ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type		= 'error';
		}
		else
		{

			$table		= JTable::getInstance( 'Subscribe' , 'Discuss' );
			foreach( $subs as $sub )
			{
				$table->load( $sub );

				if( ! $table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_REMOVING_SUBSCRIPTION_PLEASE_TRY_AGAIN_LATER' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=subscription' , $message , $type );
					return;
				}
			}

			$message	= JText::_('COM_EASYDISCUSS_SUBSCRIPTION_DELETED');

		}


		$this->setRedirect( 'index.php?option=com_easydiscuss&view=subscription' , $message , $type );
	}

}
