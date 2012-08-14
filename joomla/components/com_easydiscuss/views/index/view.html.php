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

jimport( 'joomla.application.component.view');

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'views.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'filter.php' );
require_once( DISCUSS_HELPERS . DS . 'integrate.php' );

class EasyDiscussViewIndex extends EasyDiscussView
{
	function display($tpl = null)
	{
		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$config			= DiscussHelper::getConfig();
		$mainframe      = JFactory::getApplication();

		$acl = DiscussHelper::getHelper( 'ACL' );

		$filteractive	= JRequest::getString('filter', 'allposts');
		$query			= JRequest::getString('query', '');
		$sort			= JRequest::getString('sort', 'latest');

		if($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular'))
		{
			//reset the active to latest.
			$sort = 'latest';
		}

		$category		= JRequest::getInt( 'category_id' , 0 );
		$activeCategory = DiscussHelper::getTable( 'Category' );
		$activeCategory->load( $category );

		if( $activeCategory->id != 0 )
		{
			// @task: Get category pathways.
			$paths	= $activeCategory->getPathway();

			foreach( $paths as $path )
			{
				$this->setPathway( $path->title , $path->link );
			}

			//checkk if this user can access this category page or not.
		    if( !$activeCategory->canAccess() )
		    {
				$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index' , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
				return;
		    }
		}

		$postModel		= $this->getModel('Posts');

		// @task: Add view
		$this->logView();


		$featuredposts  		= '';
		$featuredpostsHTML   	= '';
		$showAllPosts           = 'all';

		$featuredposts	= $postModel->getData( false , $sort , null , $filteractive , $category, $config->get('layout_featuredpost_limit', '5') , true );
		$postModel->clearData();

		if( $config->get('layout_featuredpost_style') != '0' )
		{
			$showAllPosts           = false;

			if( count($featuredposts) > 0 )
			{
				$featuredposts	= DiscussHelper::formatPost($featuredposts);

				$featuredTpl			= new DiscussThemes();
				$featuredTpl->set('posts', $featuredposts);

				$featuredpostsHTML		= $featuredTpl->fetch( 'main.list.featured.php' );
			}
		}

		$posts			= $postModel->getData( true , $sort , null , $filteractive , $category, null, $showAllPosts);

		// todo: show the featurd count only when the featured section disabled.
		$featured		= $postModel->getFeaturedCount( $filteractive , $category );

		$unanswered		= $postModel->getUnansweredCount( $filteractive , $category, null, $showAllPosts );
		$new			= $postModel->getNewCount( $filteractive , $category, null, $showAllPosts );

		$pagination		= $postModel->getPagination( '0' , $sort, $filteractive , $category, $showAllPosts );
		$posts			= DiscussHelper::formatPost($posts);

		$catModel		= $this->getModel( 'Categories' );
		$categories		= $catModel->getCategories( $category );

		if( $config->get( 'main_rss') )
		{
			$concatCode		= JFactory::getConfig()->getValue( 'sef' ) ? '?' : '&';
			$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=index') . $concatCode . 'format=feed&type=rss' , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			$document->addHeadLink( JRoute::_( 'index.php?option=com_easydiscuss&view=index') . $concatCode . 'format=feed&type=atom' , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
		}
		
		$isSiteSubscribed = false;
		if( $user->id != 0 )
		{
			//logged user. check if this user subscribed to site subscription or not.
			//$isSiteSubscribed = DiscussHelper::isSiteSubscribed( $user->id );
		}

		$tpl			= new DiscussThemes();
		$tpl->set( 'rssLink'	, JRoute::_( 'index.php?option=com_easydiscuss&view=index&format=feed' ) );
		$tpl->set( 'showEmailSubscribe'	, $config->get( 'main_sitesubscription', 0) );
		$tpl->set( 'filter'				, $filteractive );
		$tpl->set( 'activeCategory'		, $activeCategory );
		$tpl->set( 'featured'			, $featured );
		$tpl->set( 'new'				, $new );
		$tpl->set( 'unanswered'			, $unanswered );
		//$tpl->set( 'issubscribed'		, $isSiteSubscribed );
		$filterBar		= $tpl->fetch( 'filter.php' );

		$tpl->set( 'acl'			, $acl );
		$tpl->set( 'posts'			, $posts );
		$tpl->set( 'featuredpostsHTML'	, $featuredpostsHTML );
		$tpl->set( 'categories'		, $categories );
		$tpl->set( 'activeCategory'	, $activeCategory );
		$tpl->set( 'paginationType'	, DISCUSS_QUESTION_TYPE );
		$tpl->set( 'parent_id'		, 0 );
		$tpl->set( 'pagination'		, $pagination );
		$tpl->set( 'user'			, $user );
		$tpl->set( 'sort'			, $sort );
		$tpl->set( 'filter'			, $filteractive );
		$tpl->set( 'filterbar'		, $filterBar );
		$tpl->set( 'query'			, $query );
		$tpl->set( 'config'			, $config );

		$filterArr  = array();
		$filterArr['filter'] 		= $filteractive;
		$filterArr['category_id'] 	= $activeCategory->id;
		$filterArr['sort'] 			= $sort;
		if( !empty($query) )
			$filterArr['query'] 		= $query;

		$tpl->set( 'filterArr'		, $filterArr );
		$tpl->set( 'page'		, 'index' );

		echo $tpl->fetch( 'main.list.php' );
	}
}
