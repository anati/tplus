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

class EasyDiscussControllerAutoposting extends EasyDiscussController
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
		$current	= JRequest::getInt( 'step' );
		$layout		= JRequest::getString( 'layout' );
		$result		= $this->_store();
		$step		= $current + 1;

		$oauth		= DiscussHelper::getTable( 'Oauth' );
		$signedOn	= $oauth->loadByType( $layout );

		if( $result['type'] == 'completed' && !$signedOn )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=autoposting&layout=' . $layout . '&step=' . $current , JText::sprintf( 'COM_EASYDISCUSS_AUTOPOST_LOGIN_SIGNIN_BUTTON' , $layout ) , 'error');
			$mainframe->close();
		}

		if( $result['type'] == 'completed' )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=autoposting' , $result['message'] );
			$mainframe->close();
		}
		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=autoposting&layout=' . $layout . '&step=' . $step );
	}

	function _store()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$model		= $this->getModel( 'Settings' );
			$post		= JRequest::get( 'post' );

			$postArray	= JRequest::get( 'post' );
			$saveData	= array();
			$layout		= $postArray[ 'layout' ];
			$step		= $postArray[ 'step' ];

			// Unset unecessary data.
			unset( $postArray['task'] );
			unset( $postArray['option'] );
			unset( $postArray['layout'] );
			unset( $postArray['controller'] );
			unset( $postArray['step'] );

			if( !isset( $postArray[ 'main_autopost_' . $layout . '_page_id'] ) )
			{
				$postArray[ 'main_autopost_' . $layout . '_page_id' ] = '';
			}

			if( empty($postArray ) )
			{
				// Nothing else to be configured. Assuming that this is the final step.
				return array( 'message' => JText::sprintf( 'COM_EASYDISCUSS_AUTOPOST_LINKED_SUCCESSFULLY' , ucfirst( $layout ) ) , 'type' => 'completed' );
			}

			foreach( $postArray as $index => $value )
			{
				$saveData[ $index ]	= $value;
			}

			if( $model->save( $saveData ) )
			{
				$message	= JText::_( 'COM_EASYDISCUSS_CONFIGURATION_SAVED' );

				if( $step == 2 || $step == 'completed')
				{
					return array( 'message' => JText::sprintf( 'COM_EASYDISCUSS_AUTOPOST_SETTING_SAVED_SUCCESSFULLY' , ucfirst( $layout ) ) , 'type' => 'completed' );
				}
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

	public function request()
	{
		$config		= DiscussHelper::getConfig();
		$type		= JRequest::getCmd( 'type' );
		$step		= JRequest::getInt( 'step' );
		$key		= $config->get( 'main_autopost_' . $type . '_id' );
		$secret		= $config->get( 'main_autopost_' . $type . '_secret' );

		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=' . $type;
		$consumer	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( $type , $key , $secret , $callback );
		$request	= $consumer->getRequestToken();
		$redirect	= JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=' . $type . '&step=' . $step , false );

		if( empty( $request->token ) || empty( $request->secret ) )
		{
			$this->setRedirect( $redirect , JText::_( 'COM_EASYDISCUSS_AUTOPOST_INVALID_OAUTH_KEY') );
			return;
		}

		$oauth				= DiscussHelper::getTable( 'Oauth' );
		$oauth->type		= $type;

		// Bind the request tokens
		$param				= new JParameter('');
		$param->set( 'token' , $request->token );
		$param->set( 'secret' , $request->secret );

		$oauth->request_token	= $param->toString();

		$oauth->store();

		$this->setRedirect( $consumer->getAuthorizationURL( $request->token , false ) );
	}


	public function revoke()
	{
		$mainframe	= JFactory::getApplication();
		$type		= JRequest::getCmd( 'type' );
		$config		= DiscussHelper::getConfig();

		$oauth	= DiscussHelper::getTable( 'Oauth' );
		$oauth->loadByType( $type );

		// Revoke the access through the respective client first.
		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=' . $type;
		$key		= $config->get( 'main_autopost_' . $type . '_id' );
		$secret		= $config->get( 'main_autopost_' . $type . '_secret' );

		$consumer	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( $type , $key , $secret , $callback );
		$consumer->setAccess( $oauth->access_token );


		$redirect	= JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=' . $type . '&step=1', false );

		if( !$consumer->revokeApp() )
		{
			$this->setRedirect( $redirect , JText::_( 'There was an error when trying to revoke your app.') );
			return;
		}
		$oauth->delete();

		$this->setRedirect( $redirect, JText::_('Application revoked successfully.') );
	}


	public function grant()
	{
		$type		= JRequest::getCmd( 'type' );
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();
		$key		= $config->get( 'main_autopost_' . $type . '_id' );
		$secret		= $config->get( 'main_autopost_' . $type . '_secret' );
		$my			= JFactory::getUser();

		$oauth		= DiscussHelper::getTable( 'Oauth' );
		$loaded		= $oauth->loadByType( $type );
		$denied     = JRequest::getVar( 'denied' , '' );
		$redirect	= JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=' . $type . '&step=2' , false );

		if( !empty( $denied ) )
		{
		    $oauth->delete();

		    $this->setRedirect( $redirect , JText::sprintf( 'Denied by %1s' , $type ) , 'error');
			return;
		}

		if( !$loaded )
		{
			JError::raiseError( 500 , JText::_( 'COM_EASYDISCUSS_AUTOPOST_UNABLE_LOCATE_REQUEST_TOKEN' ) );
		}

		$request	= new JParameter( $oauth->request_token );
		$callback	= rtrim( JURI::root() , '/' ) . '/administrator/index.php?option=com_easydiscuss&controller=autoposting&task=grant&type=' . $type;
		$consumer	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( $type , $key , $secret , $callback );
		$verifier	= $consumer->getVerifier();

		if( empty( $verifier ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			JError::raiseError( 500 , JText::_( 'COM_EASYDISCUSS_AUTOPOST_INVALID_VERIFIER_CODE' ) );
		}

		$access		= $consumer->getAccess( $request->get( 'token' ) , $request->get( 'secret' ) , $verifier );

		if( !$access || empty( $access->token ) || empty( $access->secret ) )
		{
			// Since there is a problem with the oauth authentication, we need to delete the existing record.
			$oauth->delete();

			$this->setRedirect( $redirect , JText::_( 'COM_EASYDISCUSS_AUTOPOST_ERROR_RETRIEVE_ACCESS' ) , 'error' );
			return;
		}

		$param		= new JParameter('');
		$param->set( 'token' 	, $access->token );
		$param->set( 'secret'	, $access->secret );

		$oauth->access_token	= $param->toString();
		$oauth->params			= $access->params;
		$oauth->store();

		$this->setRedirect( $redirect , JText::_( 'COM_EASYDISCUSS_AUTOPOST_ACCOUNT_ASSOCIATED_SUCCESSFULLY') );
		return;
	}
}
