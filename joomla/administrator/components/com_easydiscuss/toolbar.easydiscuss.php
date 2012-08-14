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

require_once( JPATH_ROOT . DS . 'administrator' . DS . 'includes' . DS . 'toolbar.php' );

$submenus	= array(
						'discuss'		=> JText::_('COM_EASYDISCUSS_TOOLBAR_HOME'),
						'settings'		=> JText::_('COM_EASYDISCUSS_TOOLBAR_SETTINGS'),
						'tags'			=> JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS'),
						'reports'		=> JText::_('COM_EASYDISCUSS_TOOLBAR_REPORTS'),
						'users'			=> JText::_('COM_EASYDISCUSS_TOOLBAR_USERS'),
						'categories'	=> JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES'),
						'posts'			=> JText::_('COM_EASYDISCUSS_TOOLBAR_DISCUSSIONS'),
						'acls'			=> JText::_('COM_EASYDISCUSS_TOOLBAR_ACL'),
						'subscription'	=> JText::_('COM_EASYDISCUSS_TOOLBAR_SUBSCRIPTION'),
						'badges'		=> JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES' ),
						'points'		=> JText::_('COM_EASYDISCUSS_TOOLBAR_POINTS' ),
						'ranks'			=> JText::_('COM_EASYDISCUSS_TOOLBAR_RANKING' ),
						'spools'		=> JText::_('COM_EASYDISCUSS_TOOLBAR_SPOOLS' )
					);

$current	= JRequest::getVar( 'view' , 'discuss' );
$controller	= JRequest::getWord( 'c' );

// @task: For the frontpage, we just show the the icons.
if( $current == 'discuss' )
{
	$submenus	= array( 'discuss'	=> JText::_('COM_EASYDISCUSS_TOOLBAR_HOME') );	
}

foreach( $submenus as $view => $title )
{
	$isActive	= ( $current == $view );
	
	if( $current == 'post' && $view == 'posts' )
	{
		$isActive	= true;
	}
	
	if( $current == 'user' && $view == 'users' )
	{
		$isActive	= true;
	}

	if( $current == 'badge' && $view == 'badges' )
	{
		$isActive	= true;
	}

	if( $current == 'point' && $view == 'points' )
	{
		$isActive	= true;
	}
 	JSubMenuHelper::addEntry( $title , 'index.php?option=com_easydiscuss&view=' . $view , $isActive );
}