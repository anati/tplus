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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'views.php' );

class EasyDiscussViewTags extends EasyDiscussView
{
	function display( $tmpl = null )
	{	
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		
		$id 		= JRequest::getInt( 'id' );

		if( $id )
		{
			return $this->tag( $tmpl );
		}

		$model		= $this->getModel( 'Tags' );
		$tagCloud   = $model->getTagCloud( '', '' , '' );
		

		$this->setPathway( JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS') );
		
		$tpl	= new DiscussThemes();
		$tpl->set( 'tagCloud', $tagCloud );
		$tpl->set( 'user', $user );
		
		echo $tpl->fetch( 'tags.list.php' );
	}

	function tag( $tmpl = null )
	{
		//initialise variables
		$mainframe		= JFactory::getApplication();
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$config			= DiscussHelper::getConfig();
		$tag			= JRequest::getInt( 'id' , 0 );

		if( empty($tag) )
		{
			$mainframe->enqueueMessage(JText::_('COM_EASYDISCUSS_INVALID_TAG'), 'error');
			$mainframe->redirect(DiscussRouter::_('index.php?option=com_easydiscuss&view=index'));
		}

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_TAGS' ) , DiscussRouter::_( 'index.php?option=com_easydiscuss&view=tags' ) );

		$table			= DiscussHelper::getTable( 'Tags' );
		$table->load( $tag );

		$this->setPathway( JText::_( $table->title ) );
		$concatCode		= JFactory::getConfig()->getValue( 'sef' ) ? '?' : '&';
		$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag ) . $concatCode . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
		$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag ) . $concatCode . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );

		$filteractive	= JRequest::getString('filter', 'allposts');
		$sort			= JRequest::getString('sort', 'latest');

		if($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular'))
		{
			//reset the active to latest.
			$sort = 'latest';
		}

		$postModel		= $this->getModel('Posts');
		$posts			= $postModel->getTaggedPost($tag, $sort, $filteractive);
		$pagination		= $postModel->getPagination($sort, $filteractive);
		$posts			= DiscussHelper::formatPost($posts);
		$tagModel		= $this->getModel('Tags');
		$currentTag 	= $tagModel->getTagName($tag);

		$tpl			= new DiscussThemes();
		$tpl->set( 'rssLink'	, JRoute::_( 'index.php?option=com_easydiscuss&view=tags&id=' . $tag . '&format=feed' ) );
		$tpl->set( 'posts'	, $posts );
		$tpl->set( 'paginationType'	, DISCUSS_TAGS_TYPE );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'sort'		, $sort );
		$tpl->set( 'filter'		, $filteractive );
		$tpl->set( 'showEmailSubscribe'	, true );
		$tpl->set( 'currentTag'	, $currentTag );
		$tpl->set( 'parent_id'	, $tag );
		$tpl->set( 'config'	, $config );

		echo $tpl->fetch( 'tag.php' );
	}
}