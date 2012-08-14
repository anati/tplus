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

class EasyDiscussControllerTags extends EasyDiscussController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
	}

	function save()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$post				= JRequest::get( 'post' );

			if(empty($post['title']))
			{
				$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_TAG'), 'error');

				$url  = 'index.php?option=com_easydiscuss&view=tags';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$user				= JFactory::getUser();
			$post['user_id']	= $user->id;
			$tagId				= JRequest::getVar( 'tagid' , '' );
			$tag				= JTable::getInstance( 'tags', 'Discuss' );

			if( !empty( $tagId ) )
			{
				$tag->load( $tagId );
			}
			else
			{
				$tagModel 	= $this->getModel( 'Tags' );
				$result 	= $tagModel->searchTag($tag->title);

				if(!empty($result))
				{
					$message	= JText::_('COM_EASYDISCUSS_TAG_EXISTS');
					$type		= 'error';
					$mainframe->redirect( 'index.php?option=com_easydiscuss&view=tags' , $message , $type );
				}
			}

			$tag->bind( $post );

			$tag->title = JString::trim($tag->title);
			$tag->alias = JString::trim($tag->alias);

			if (!$tag->store())
			{
	        	JError::raiseError(500, $tag->getError() );
			}
			else
			{
				$message	= JText::_( 'COM_EASYDISCUSS_TAG_SAVED' );
			}
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type		= 'error';
		}


		$saveNew 		= JRequest::getBool( 'savenew' , false );
		if( $saveNew )
		{
			$mainframe->redirect( 'index.php?option=com_easydiscuss&view=tag' , $message , $type );
			$mainframe->close();
		}

		$mainframe->redirect( 'index.php?option=com_easydiscuss&view=tags' , $message , $type );
	}

	function cancel()
	{
		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' );

		return;
	}

	function edit()
	{
		JRequest::setVar( 'view', 'tag' );
		JRequest::setVar( 'tagid' , JRequest::getVar( 'tagid' , '' , 'REQUEST' ) );

		parent::display();
	}

	function remove()
	{
		$tags	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( empty( $tags ) )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type		= 'error';
		}
		else
		{
			$table		= JTable::getInstance( 'Tags' , 'Discuss' );
			foreach( $tags as $tag )
			{
				$table->load( $tag );

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYDISCUSS_REMOVE_TAG_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' , $message , $type );
					return;
				}
			}

			$message	= JText::_('COM_EASYDISCUSS_TAG_DELETED');
		}

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' , $message , $type );
	}

	function publish()
	{
		$tags	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $tags ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Tags' );

			if( $model->publish( $tags , 1 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_PUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_PUBLISH_ERROR');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' , $message , $type );
	}

	function unpublish()
	{
		$tags	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $tags ) <= 0 )
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_TAG_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Tags' );

			if( $model->publish( $tags , 0 ) )
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYDISCUSS_TAG_UNPUBLISH_ERROR');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easydiscuss&view=tags' , $message , $type );
	}
}
