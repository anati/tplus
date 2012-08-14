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

class EasyDiscussControllerSubscription extends JController
{
	var $err	= null;

	function display()
	{
		parent::display();
	}

	function unsubscribe()
	{
		$my = JFactory::getUser();

		$redirectLInk = 'index.php?option=com_easydiscuss&view=subscriptions';
		if( $my->id == 0)
		{
			$redirectLInk = 'index.php?option=com_easydiscuss&view=index';
		}


		//type=site - subscription type
		//sid=1 - subscription id
		//uid=42 - user id
		//token=0fd690b25dd9e4d2dc47a252d025dff4 - md5 subid.subdate
		$data = base64_decode(JRequest::getVar('data', ''));

		$param = new JParameter($data);
		$param->type	= $param->get('type', '');
		$param->sid		= $param->get('sid', '');
		$param->uid		= $param->get('uid', '');
		$param->token	= $param->get('token', '');

		$subtable = DiscussHelper::getTable( 'Subscribe' );
		$subtable->load($param->sid);

		$token		= md5($subtable->id.$subtable->created);
		$paramToken = md5($param->sid.$subtable->created);

		if (empty($subtable->id))
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_NOT_FOUND') , 'error');
			$this->setRedirect(DiscussRouter::_($redirectLInk, false));
			return false;
		}

		if($token != $paramToken)
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED') , 'error');
			$this->setRedirect(DiscussRouter::_($redirectLInk, false));
			return false;
		}

		if(!$subtable->delete($param->sid))
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_FAILED_ERROR_DELETING_RECORDS') , 'error');
			$this->setRedirect(DiscussRouter::_($redirectLInk, false));
			return false;
		}


		DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SUBSCRIPTION_UNSUBSCRIBE_SUCCESS') );
		$this->setRedirect(DiscussRouter::_($redirectLInk, false));
		return true;
	}
}
