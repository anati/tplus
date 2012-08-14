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

jimport( 'joomla.application.component.view');

require_once( DISCUSS_HELPERS . DS . 'date.php' );

class EasyDiscussViewCategories extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		DiscussEventsHelper::importPlugin( 'content' );
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config 	= DiscussHelper::getConfig();
		$my         = JFactory::getUser();

		$sortConfig = $config->get('layout_sorting_category','latest');
		$sort		= JRequest::getCmd('sort',$sortConfig);

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_CATEGORIES' ) );

		// @task: Add view
		$this->logView();

		$modelP				= $this->getModel( 'Posts' );
		$categoryModel		= $this->getModel( 'Categories' );

		$hideEmptyPost		= false;
		$categories			= $categoryModel->getCategoryTree();

		$theme	= new DiscussThemes();
		$theme->set( 'categories', $categories );

		echo $theme->fetch( 'categories.php' );
	}


	/*
	 * Show all the blogs in this category
	 */
	function listings()
	{
		DiscussEventsHelper::importPlugin( 'content' );
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config 	= DiscussHelper::getConfig();
		$my			= JFactory::getUser();
		$sort		= JRequest::getCmd('sort', $config->get( 'layout_postorder' ) );
		$catId		= JRequest::getCmd('id','0');

		$categoryModel      = $this->getModel('Category');

		$category = DiscussHelper::getTable( 'Category' );
		$category->load($catId);

		if($category->id == 0)
		{
			$category->title	= JText::_('COM_EASYDISCUSS_UNCATEGORIZED');
		}

		//setting pathway
		$pathway	= $mainframe->getPathway();
		$pathway->addItem( JText::_('COM_EASYDISCUSS_CATEGORIES_BREADCRUMB') , DiscussRouter::_('index.php?option=com_easydiscuss&view=categories') );

		// @task: Add view
		$this->logView();

		$addRSS = true;
		if($category->private)
		{
			if( $my->id == 0 && !$config->get('main_allowguestsubscribe'))
			{
				$addRSS = false;
			}
		}

		if( $addRSS )
		{
			// Add rss feed link
			$document->addHeadLink( $category->getRSS() , 'alternate' , 'rel' , array('type' => 'application/rss+xml', 'title' => 'RSS 2.0') );
			$document->addHeadLink( $category->getAtom() , 'alternate' , 'rel' , array('type' => 'application/atom+xml', 'title' => 'Atom 1.0') );
		}


		$catIds     = array();
		$catIds[]   = $category->id;
		DiscussHelper::accessNestedCategoriesId($category, $catIds);

		$category->cnt	= $categoryModel->getTotalPostCount( $category->id );
		$category->childs = null;

		//get the nested categories
		$catListhtml = '';

		if($config->get('layout_category_showtree', true))
		{
			DiscussHelper::buildNestedCategories($category->id, $category, false, true);

			$expand = !empty($category->childs)? '<span onclick="Foundry(this).parents(\'li:first\').toggleClass(\'expand\');">[+] </span>' : '';
			$catListhtml   .= '<li><div>' . $expand . '<a href="' . DiscussRouter::_('index.php?option=com_easydiscuss&view=categories&layout=listings&id=' . $category->id) . '">'. JText::_( $category->title ) . '</a> <b>(' . $category->cnt . ')</b></div>';
			DiscussHelper::accessNestedCategories($category, $catListhtml, '0', '0', 'list');
			$catListhtml   .= '</li>';
		}
		else
		{
			DiscussHelper::buildNestedCategories($category->id, $category);

			$linkage   = '';
			DiscussHelper::accessNestedCategories($category, $linkage, '0', '', 'link', '|');

			$category->nestedLink    = $linkage;
		}

		$model		= $this->getModel( 'Posts' );
		$data		= $model->getPostsBy('category', $catIds, $sort);
		$pagination	= $model->getPagination();

		//for trigger
		$params		= $mainframe->getParams('com_easydiscuss');
		$limitstart	= JRequest::getVar('limitstart', 0, '', 'int');

		if(! empty($data))
		{
			$data	= DiscussHelper::formatPost( $data );

			if($config->get('layout_showcomment', false))
			{
				for($i = 0; $i < count($data); $i++)
				{
					$row   = $data[$i];

					$maxComment = $config->get('layout_showcommentcount', 3);
					$comments	= DiscussHelper::getHelper( 'Comment' )->getBlogComment( $row->id, $maxComment , 'desc' );
					$comments   = DiscussHelper::formatBlogCommentsLite($comments);
					$row->comments = $comments;
				}
			}
		}

		$categoryModel		= $this->getModel( 'Categories' );
		$categories			= $categoryModel->getCategories($catId);

		$themes	= new DiscussThemes();

		$themes->set( 'categories', $categories );
		$themes->set( 'category', $category );
		$themes->set( 'catListhtml', $catListhtml );
		$themes->set( 'sort', $sort );
		$themes->set( 'posts', $data );
		$themes->set( 'siteadmin', DiscussHelper::isSiteAdmin() );
		$themes->set( 'currentURL' , 'index.php?option=com_easydiscuss&view=categories&layout=listings&id=' . $category->id );
		$themes->set( 'pagination', $pagination->getPagesLinks());
		$themes->set( 'config', $config );
		$themes->set( 'my', $my );

		echo $themes->fetch( 'category.php' );
	}
}
