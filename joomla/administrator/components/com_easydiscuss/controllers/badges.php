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

class EasyDiscussControllerBadges extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'publish' , 'unpublish' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'badge' );
		JRequest::setVar( 'id' , JRequest::getInt( 'id' , '' , 'REQUEST' ) );

		parent::display();
	}

	function add()
	{
		$mainframe	= JFactory::getApplication();

		$mainframe->redirect( 'index.php?option=com_easydiscuss&controller=badges&task=edit' );
	}

	public function remove()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$ids		= JRequest::getVar( 'cid' );

		foreach( $ids as $id )
		{
			$badge	= DiscussHelper::getTable( 'Badges' );
			$badge->load( $id );
			$badge->delete();
		}

		$app	= JFactory::getApplication();
		$app->redirect( 'index.php?option=com_easydiscuss&view=badges' , JText::_( 'COM_EASYDISCUSS_BADGES_DELETED' ) );
	}

	public function unpublish()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$badge	= DiscussHelper::getTable( 'Badges' );
		$ids	= JRequest::getVar( 'cid' );
		$state	= JRequest::getVar( 'task' ) == 'publish' ? 1 : 0;

		foreach( $ids as $id )
		{
			$id	= (int) $id;
			$badge->load( $id );
			$badge->set( 'published' , $state );
			$badge->store();
		}
		$message	= $state ? JText::_( 'COM_EASYDISCUSS_BADGES_PUBLISHED' ) : JText::_( 'COM_EASYDISCUSS_BADGES_UNPUBLISHED' );
		$app->redirect( 'index.php?option=com_easydiscuss&view=badges' , $message );
	}

	public function save()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$app	= JFactory::getApplication();
		$badge	= DiscussHelper::getTable( 'Badges' );
		$id		= JRequest::getInt( 'id' );

		$badge->load( $id );

		$oldTitle	= $badge->title;

		$post	= JRequest::get( 'POST' );
		$badge->bind( $post );
		$badge->set( 'published' , 1 );

		// Store the badge
		if ($badge->title != $oldTitle || $oldTitle == '')
		{
			$badge->alias	= DiscussHelper::getAlias($badge->title);
		}

		$badge->store();

		$message	= !empty( $id ) ? JText::_( 'COM_EASYDISCUSS_BADGE_UPDATED' ) : JText::_( 'COM_EASYDISCUSS_BADGE_CREATED' );
		$app->redirect( 'index.php?option=com_easydiscuss&view=badges' , $message );
	}
}
