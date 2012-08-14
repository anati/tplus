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

// Load constants
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'constants.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );

// Require the base controller
require_once( JPATH_COMPONENT . DS . 'controllers' . DS . 'controller.php' );

// Call ajax library
require_once( DISCUSS_CLASSES . DS . 'disjax.php' );
require_once( DISCUSS_CLASSES . DS . 'themes.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );

// Process AJAX calls here
DiscussHelper::getHelper( 'Ajax' )->process();

require_once( JPATH_ROOT . DS . 'media' . DS . 'foundry' . DS . 'foundry.php' );

$config = DiscussHelper::getConfig();

/*
 * Check if there url calling 'cron' or not.
 */
if (JRequest::getCmd('task', '', 'GET') == 'cron')
{
	$mailq	= DiscussHelper::getMailQueue();

	if(JRequest::getCmd('job', '', 'GET') == 'subscription' && $config->get('main_sitesubscription'))
	{
		//process the site subscription
		//daily - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=daily
		//weekly - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=weekly
		//monthly - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=monthly
		//all - index.php?option=com_easydiscuss&task=cron&job=subscription&interval=all

		$interval	= JRequest::getCmd('interval', 'daily', 'GET');

		$subs = DiscussHelper::getSiteSubscriptionClass();

		if($interval == 'all')
		{
			$processIntervals = array('daily', 'weekly', 'monthly');

			foreach($processIntervals as $processInterval)
			{
				$subs->interval = $processInterval;
				$subs->process();
			}
		}
		else
		{
			$subs->interval = $interval;
			$subs->process();
		}

		echo ucfirst($interval).' subscription processed.';
	}
	else
	{
		$mailq->sendOnPageLoad();

		echo 'Email batch process finished.';
	}

	// @rule: Process incoming email rules
	$mailq->parseEmails();
	exit;
}


/*
 * Processing email batch sending.
 */
if ($config->get('main_mailqueueonpageload'))
{
	$mailq	= DiscussHelper::getMailQueue();
	$mailq->sendOnPageLoad();
}


// Get the task
$task	= JRequest::getCmd( 'task' , 'display' );

// We treat the view as the controller. Load other controller if there is any.
$controller	= JRequest::getWord( 'controller' , '' );

if( !empty( $controller ) )
{
	$controller	= JString::strtolower( $controller );
	$path		= JPATH_COMPONENT . DS . 'controllers' . DS . $controller . '.php';

	jimport( 'joomla.filesystem.file' );

	// Test if the controller really exists
	if( JFile::exists( $path ) )
	{
		require_once( $path );
	}
	else
	{
		JError::raiseError( 500 , JText::_( 'Invalid Controller name "' . $controller . '".<br /> File "' . $path . '" does not exists in this context.' ) );
	}
}

$class	= 'EasyDiscussController' . JString::ucfirst( $controller );

// Test if the object really exists in the current context
if( class_exists( $class ) )
{
	$controller	= new $class();
}
else
{
	JError::raiseError( 500 , 'Invalid Controller Object. Class definition does not exists in this context.' );
}

// Task's are methods of the controller. Perform the Request task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();