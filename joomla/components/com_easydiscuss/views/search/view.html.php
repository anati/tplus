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

class EasyDiscussViewSearch extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();
		$user		= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		
		$filteractive	= JRequest::getString('filter', 'allposts');
		$sort			= JRequest::getString('sort', 'latest');
		$category		= JRequest::getInt( 'category_id' , 0 );

		if($filteractive == 'unanswered' && ($sort == 'active' || $sort == 'popular'))
		{
			//reset the active to latest.
			$sort = 'latest';
		}

		$this->setPathway( JText::_('COM_EASYDISCUSS_SEARCH') );
		
		$query		= JRequest::getString( 'query' , '' );
		$limitstart	= null;
		$posts  	= null;
		$pagination = null;
		
		if(! empty($query))
		{
			$postModel		= $this->getModel( 'Search' );
			$posts			= $postModel->getData( true , $sort , null , $filteractive , $category);
			$pagination		= $postModel->getPagination( '0' , $sort, $filteractive , $category);
			$posts          = DiscussHelper::formatPost($posts, true);
			
			if( count($posts) > 0 )
			{
			    //$searchwords[]  = explode(' ', $query);

				$searchworda = preg_replace('#\xE3\x80\x80#s', ' ', $query);
				$searchwords = preg_split("/\s+/u", $searchworda);
				$needle = $searchwords[0];
				$searchwords = array_unique($searchwords);
			
				for($i = 0; $i < count($posts); $i++ )
				{
				    $row    = $posts[$i];
				    
					$introtext	= preg_replace( '/\s+/', ' ', strip_tags(Parser::bbcode($row->content)) ); // clean it to 1 liner
					$introtext	= JString::substr($introtext, 0, $config->get( 'layout_introtextlength' ));

					$searchRegex = '#(';
					$x = 0;

					foreach ($searchwords as $k => $hlword)
					{
						$searchRegex .= ($x == 0 ? '' : '|');
						$searchRegex .= preg_quote($hlword, '#');
						$x++;
					}
					$searchRegex .= ')#iu';

					$row->title 	= preg_replace($searchRegex, '<span class="highlight">\0</span>', $row->title);
					
					//display password input form.
					if( !empty( $row->password ) && !DiscussHelper::hasPassword( $row ) )
					{
					    $row->content   = $row->content;
					}
					else
					{
					    $row->content 	= preg_replace($searchRegex, '<span class="highlight">\0</span>', $introtext);
					}
					
					
				}
			}
		}

		$tpl			= new DiscussThemes();
		$tpl->set( 'query'			, $query );
		$tpl->set( 'posts'			, $posts );
		$tpl->set( 'paginationType'	, DISCUSS_SEARCH_TYPE );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'sort'		, $sort );
		$tpl->set( 'filter'		, $filteractive );
		$tpl->set( 'parent_id'	, $query );
		
// 		$filterArr  = array();
// 		if( !empty($query) )
// 			$filterArr['query'] 		= $query;
//
// 		$tpl->set( 'filterArr'		, $filterArr );
// 		$tpl->set( 'page'		, 'search' );
		
		echo $tpl->fetch( 'search.php' );
	}
}