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

class EasyDiscussControllerPoints extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'publish'	, 'unpublish' );
		$this->registerTask( 'saveNew'	, 'save' );
	}

	function edit()
	{
		JRequest::setVar( 'view', 'point' );
		JRequest::setVar( 'id' , JRequest::getInt( 'id' , '' , 'REQUEST' ) );

		parent::display();
	}

	function add()
	{
		$mainframe	= JFactory::getApplication();

		$mainframe->redirect( 'index.php?option=com_easydiscuss&controller=points&task=edit' );
	}

	public function remove()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );
		$ids		= JRequest::getVar( 'cid' );
		
		foreach( $ids as $id )
		{
			$point	= DiscussHelper::getTable( 'Points' );
			$point->load( $id );
			$point->delete();
		}

		$app	= JFactory::getApplication();
		$app->redirect( 'index.php?option=com_easydiscuss&view=points' , JText::_( 'COM_EASYDISCUSS_BADGES_DELETED' ) );
	}

	public function unpublish()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$point	= DiscussHelper::getTable( 'Points' );
		$ids	= JRequest::getVar( 'cid' );
		$state	= JRequest::getVar( 'task' ) == 'publish' ? 1 : 0;

		foreach( $ids as $id )
		{
			$id	= (int) $id;
			$point->load( $id );
			$point->set( 'published' , $state );
			$point->store();
		}
		$message	= $state ? JText::_( 'COM_EASYDISCUSS_POINTS_PUBLISHED' ) : JText::_( 'COM_EASYDISCUSS_POINTS_UNPUBLISHED' );
		$app->redirect( 'index.php?option=com_easydiscuss&view=points' , $message );
	}

	public function save()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app	= JFactory::getApplication();
		$point	= DiscussHelper::getTable( 'Points' );
		$id		= JRequest::getInt( 'id' );

		$point->load( $id );

		$post	= JRequest::get( 'POST' );
		$point->bind( $post );

		// Store the badge
		$point->store();

		$message	= !empty( $id ) ? JText::_( 'COM_EASYDISCUSS_POINTS_UPDATED' ) : JText::_( 'COM_EASYDISCUSS_POINTS_CREATED' );

		$url		= 'index.php?option=com_easydiscuss&view=points';

		if( JRequest::getVar( 'task' ) == 'saveNew' )
		{
			$url	= 'index.php?option=com_easydiscuss&controller=points&task=edit';
		}
		$app->redirect( $url , $message );
	}
}
