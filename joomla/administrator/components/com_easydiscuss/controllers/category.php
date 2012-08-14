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

class EasyDiscussControllerCategory extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
	}

	function orderdown()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

	    EasyDiscussControllerCategory::orderCategory(1);
	}

	function orderup()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

	    EasyDiscussControllerCategory::orderCategory(-1);
	}

	function orderCategory( $direction )
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe  = JFactory::getApplication();

		// Initialize variables
		$db		= JFactory::getDBO();
		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		if (isset( $cid[0] ))
		{
			$row = JTable::getInstance('Category', 'Discuss');
			$row->load( (int) $cid[0] );
			$row->move($direction);
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories');
		exit;
	}

	function saveOrder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

	    $mainframe  = JFactory::getApplication();

		$row = JTable::getInstance('Category', 'Discuss');
		$row->rebuildOrdering();

		$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_ORDERING_SAVED');
		$type       = 'message';

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
		exit;
	}

	public function removeAvatar()
	{
		// Check for request forgeries
		JRequest::checkToken( 'get' ) or jexit( 'Invalid Token' );

		$id			= JRequest::getInt( 'id' );
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( $id );

		$state		= $category->removeAvatar( true );


		JFactory::getApplication()->redirect( 'index.php?option=com_easydiscuss&view=category&catid=' . $category->id , JText::_( 'COM_EASYDISCUSS_CATEGORY_AVATAR_REMOVED') );
	}

	function saveOrderOri()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

	    $mainframe  = JFactory::getApplication();
		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
		$total		= count($cid);
		$conditions	= array ();
		$groupings	= array();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		$row = JTable::getInstance('Category', 'Discuss');

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			$row->load( (int) $cid[$i] );

			$groupings[] = $row->parent_id;

			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					JError::raiseError( 500, $db->getErrorMsg() );
					return false;
				}
				// remember to updateOrder this group
				$condition = 'id = '.(int) $row->id;
				$found = false;
				foreach ($conditions as $cond)
					if ($cond[1] == $condition) {
						$found = true;
						break;
					}
				if (!$found)
					$conditions[] = array ($row->id, $condition);
			}
		}

		// execute updateOrder for each group
		foreach ($conditions as $cond)
		{
			$row->load($cond[0]);
			$row->reorder($cond[1]);
		}

		$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_ORDERING_SAVED');
		$type       = 'message';

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
		exit;
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$post				= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY'), 'error');

				$url  = 'index.php?option=com_easydiscuss&view=categories';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$category			= JTable::getInstance( 'Category', 'Discuss' );
			$user				= JFactory::getUser();
			$post['created_by']	= $user->id;
			$catId				= JRequest::getVar( 'catid' , '' );

			$isNew				= (empty($catId)) ? true : false;
			$alterOrdering      = true;

			if( !empty( $catId ) )
			{
				$category->load( $catId );
				$newParentId  = $post['parent_id'];

				if( $category->parent_id != $newParentId)
				{
				    $alterOrdering  = true;
				}
				else
				{
				    $alterOrdering  = false;
				}
			}

			$category->bind( $post );

			// Bind params
			$params 			= new JParameter('');
			$params->set( 'show_description' , $post['show_description'] );
			$params->set( 'maxlength' , $post['maxlength'] );
			$params->set( 'maxlength_size' , $post['maxlength_size'] );
			$category->params 	= $params->toString();

			if (!$category->store( $alterOrdering ))
			{
	        	JError::raiseError(500, $category->getError() );
	        	exit;
			}

		    //save the category acl
			$category->deleteACL();
			if($category->private == '2')
			{
				$category->saveACL( $post );
			}

			$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );
			if(! empty($file['name']))
			{
				$newAvatar  		= DiscussHelper::uploadCategoryAvatar($category, true);
				$category->avatar   = $newAvatar;
				$category->store(); //now update the avatar.
			}

			$message	= JText::_( 'COM_EASYDISCUSS_CATEGORIES_SAVED_SUCCESS' );
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_REQUEST');
			$type		= 'error';
		}

		$saveNew 		= JRequest::getBool( 'savenew' , false );

		if( $saveNew )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=category' , $message , $type );
			$mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
		$mainframe->close();
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' );

		return;
	}

	function edit()
	{
		JRequest::setVar( 'view', 'category' );
		JRequest::setVar( 'catid' , JRequest::getVar( 'catid' , '' , 'REQUEST' ) );

		parent::display();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$categories	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'info';

		if( empty( $categories ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$table		= JTable::getInstance( 'Category' , 'Discuss' );
			foreach( $categories as $category )
			{
				$table->load( $category );

				if($table->getPostCount())
				{
					$message	= JText::sprintf('COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR_POST_NOT_EMPTY', $table->title);
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
					return;
				}

				if($table->getChildCount())
				{
					$message	= JText::sprintf('COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR_CHILD_NOT_EMPTY', $table->title);
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
					return;
				}

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_CATEGORIES_DELETE_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
					return;
				}
			}
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_DELETE_SUCCESS');
		}

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$categories	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $categories ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Categories' );

			if( $model->publish( $categories , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLISHED_SUCCESS');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLISHED_ERROR');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$categories	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $categories ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_INVALID_CATEGORY');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Categories' );

			if( $model->publish( $categories , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_UNPUBLISHED_SUCCESS');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_CATEGORIES_UNPUBLISHED_ERROR');
				$type		= 'error';
			}
		}

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , $message , $type );
	}

	/*
	 * Logic to make a category as default.
	 */
	public function makeDefault()
	{
	    $cid    = JRequest::getVar( 'cid' );

		if( is_array( $cid ) )
		{
		    $cid    = (int) $cid[0];
		}

		$model  = $this->getModel( 'Categories' );
		$model->updateDefault( $cid );

	    $this->setRedirect( 'index.php?option=com_easydiscuss&view=categories' , JText::_( 'Category is marked as default' ) );
	}
}
