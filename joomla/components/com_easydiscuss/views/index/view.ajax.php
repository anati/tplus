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

class EasyDiscussViewIndex extends EasyDiscussView
{
	function ajaxUnSubscribe( $type = null, $subscribeId = null )
	{
		$ajax				= new disjax();
		$mainframe			= JFactory::getApplication();
		$my					= JFactory::getUser();

		$theme				= new DiscussThemes();
		$theme->set('id', $subscribeId);

		$subTitle			= JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_FROM_' . strtoupper($type) . '_DISCUSSION' );
		$subDescription		= JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_' . strtoupper($type) . '_DESCRIPTION' );
		$subCall			= 'discuss.unsubscribe.' . strtolower($type) . '();';

		$theme->set('subTitle', $subTitle);
		$theme->set('subDescription', $subDescription);
		$theme->set('subCall', $subCall);

		$options			= new stdClass();
		$options->content	= $theme->fetch( 'ajax.unsubscribe.php' );

		$ajax->dialog( $options );
		$ajax->send();
	}

	function ajaxRemoveSubscription( $type = null, $subscribeId = null )
	{
		$ajax		= new disjax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();

		if( $my->id == 0)
		{
			$ajax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$ajax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE') );
			$ajax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$subcription	= DiscussHelper::getTable( 'Subscribe' );
		$subcription->load( $subscribeId );

		if( empty($subcription->type) )
		{
			$ajax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$ajax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_FAILED_NO_RECORD_FOUND') );
			$ajax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		//check if the id belong to the user or not.
		if( ! DiscussHelper::isMySubscription( $my->id, $type, $subscribeId) )
		{
			$ajax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$ajax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_NOT_ALLOWED_HERE') );
			$ajax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$ajax->send();
			return;
		}

		$msgClass	= 'dc_success';
		$msg		= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_SITE_SUCCESS');

		switch($type)
		{
			case 'post';
				$msg		= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_POST_SUCCESS');
				break;
			case 'site':
				$msg		= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_SITE_SUCCESS');
				break;
			case 'category':
				$msg		= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_CATEGORY_SUCCESS');
				break;
			case 'user':
				$msg		= JText::_('COM_EASYDISCUSS_UNSUBSCRIPTION_USER_SUCCESS');
				break;
			default:
				break;
		}

		//perform the unsubcribe
		$subcription->delete();

		$ajax->script( 'discuss.spinner.hide( "dialog_loading" );' );
		$ajax->assign( 'dc_subscribe_notification .msg_in' , $msg );
		$ajax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
		$ajax->script( 'Foundry( ".dialog-buttons .si_btn" ).hide();' );
		$ajax->send();

		return;
	}


	function ajaxSubscribe( $type = 'site', $cid = 0 )
	{
		$ajax		= new disjax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();

		$theme			= new DiscussThemes();
		$options		= new stdClass();

		$theme->set( 'cid', $cid );
		$theme->set( 'type', $type );

		if ($type == 'post')
		{
			$options->content	= $theme->fetch( 'ajax.subscribe.post.php' );
		}
		else
		{
			$options->content	= $theme->fetch( 'ajax.subscribe.php' );
		}

		$ajax->dialog( $options );
		$ajax->send();
	}

	function ajaxAddSubscription($type = null, $email = null, $name = null, $interval = null, $cid = '0')
	{
		$disjax		= new Disjax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$config		= DiscussHelper::getConfig();
		$msg		= '';
		$msgClass	= 'dc_success';

		$JFilter	= JFilterInput::getInstance();
		$name		= $JFilter->clean($name, 'STRING');

		jimport( 'joomla.mail.helper' );
		
		if( !JMailHelper::isEmailAddress($email) )
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_INVALID_EMAIL') );
			$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		$subscription_info = array();
		$subscription_info['type']		= $type;
		$subscription_info['userid']	= $my->id;
		$subscription_info['email']		= $email;
		$subscription_info['cid']		= $cid;
		$subscription_info['member']	= ($my->id)? '1':'0';
		$subscription_info['name']		= ($my->id)? $my->name : $name;
		$subscription_info['interval']	= $interval;

		//validation
		if(JString::trim($subscription_info['email']) == '')
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_EMAIL_IS_EMPTY') );
			$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		if(JString::trim($subscription_info['name']) == '')
		{
			$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
			$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_NAME_IS_EMPTY') );
			$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_error" );' );
			$disjax->send();
			return;
		}

		$model	= $this->getModel( 'Subscribe' );
		$sid	= '';

		$subRecord = $model->isSiteSubscribed($subscription_info);
		if(!empty($subRecord))
		{
			if($subRecord['interval'] == $subscription_info['interval'])
			{
				$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
				$disjax->assign( 'dc_subscribe_notification .msg_in' , JText::sprintf('COM_EASYDISCUSS_ALREADY_SUBSCRIBED_TO_' . strtoupper($type) . '_DISCUSSION_INTERVAL', JText::_('COM_EASYDISCUSS_SUBSCRIBE_'.$subscription_info['interval'])) );
				$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "dc_alert" );' );
				$disjax->send();


				return;
			}
			else
			{
				if($model->updateSiteSubscription($subRecord['id'], $subscription_info))
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_UPDATED_SUBSCRIPTION_INTERVAL', JText::_('COM_EASYDISCUSS_SUBSCRIBE_'.$subRecord['interval']), JText::_('COM_EASYDISCUSS_SUBSCRIBE_'.$subscription_info['interval']));
				}
				else
				{
					$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
					$msgClass = 'dc_error';
				}
			}
		}
		else
		{
			if(!$model->addSubscription($subscription_info))
			{
				$msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
				$msgClass = 'dc_error';
			}
		}

		$msg = empty($msg)? JText::_('COM_EASYDISCUSS_SUBSCRIPTION_SUCCESS') : $msg;

		$disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
		$disjax->assign( 'dc_subscribe_notification .msg_in' , $msg );
		$disjax->script( 'Foundry( "#dc_subscribe_notification .msg_in" ).addClass( "'.$msgClass.'" );' );

		$disjax->send();
		return;
	}

	/**
	 * Ajax call which is responsible to output more entries
	 * on the front listings.
	 **/
	function ajaxReadmore( $limitstart = null, $sorting = null, $type = 'questions' , $parentId = 0, $filter = '', $category = '', $query = '' )
	{
		$func	= 'ajaxReadmore' . ucfirst( $type );
		if( $type == 'questions')
			$this->$func( $limitstart , $sorting , $type , $parentId, $filter, $category, $query );
		else
			$this->$func( $limitstart , $sorting , $type , $parentId, $filter, $category );
	}

	function ajaxReadmoreSearch( $limitstart = null, $sorting = null, $type = null, $parentId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		$query  	= $parentId;
		$posts		= $model->getPostsBy( 'search' , '0' , 'latest' , $limitstart, '', $query);
		$pagination	= $model->getPagination( '0' , $sorting );
		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'Foundry("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}


	function ajaxReadmoreQuestions( $limitstart = null, $sorting = null, $type = null, $parentId = null, $filter = null, $category = '', $query = '' )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		if( !empty($query))
			JRequest::setVar('query', $query);

		$pagination	= $model->getPagination( '0' , $sorting, $filter, $category );
		$posts		= $model->getData( true , $sorting , $limitstart, $filter, $category );
		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'Foundry("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}

	function ajaxReadmoreTags( $limitstart = null, $sorting = null, $type = null, $uniqueId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		$posts		= $model->getTaggedPost( $uniqueId , $sorting, $filter, $limitstart );
		$pagination	= $model->getPagination( '0' , $sorting, $filter );
		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'Foundry("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}

	function ajaxReadmoreUserQuestions( $limitstart = null, $sorting = null, $type = null, $uniqueId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();

		$posts		= $model->getPostsBy( 'user' , $uniqueId , 'latest' , $limitstart );
		$pagination	= $model->getPagination( '0' , $sorting );

		$posts		= DiscussHelper::formatPost($posts);
		$template	= new DiscussThemes();
		$template->set( 'posts'	, $posts );

		$html		= $template->fetch( 'main.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'Foundry("#dc_list").children( ":last" ).addClass( "separator" );');
		$ajax->append( 'dc_list' , $html );
		$ajax->send();
	}

	function ajaxReadmoreReplies( $limitstart = null, $sorting = null, $type = 'questions' , $parentId = null, $filter = null, $category = null )
	{
		$ajax		= new Disjax();
		$model		= $this->getModel( 'Posts' );
		$limitstart	= (int) $limitstart;
		$mainframe	= JFactory::getApplication();
		$config		= DiscussHelper::getConfig();
		$posts		= $model->getReplies( $parentId , $sorting , $limitstart );
		$pagination	= $model->getPagination( $parentId , $sorting );
		$my			= JFactory::getUser();
		$posts		= DiscussHelper::formatPost($posts);
		$parent		= DiscussHelper::getTable( 'Post' );
		$parent->load( $parentId );


		//check all the 'can' or 'canot' here.
		$isMainLocked	= ( $parent->islock) ? true : false;
		$canDelete	= false;
		$canTag		= false;
		$canReply	= (($my->id != 0) || ($my->id == 0 && $config->get('main_allowguestpost', 0))) ? true : false;

		if($config->get('main_allowdelete', 2) == 2)
		{
			$canDelete	= ($isSiteAdmin) ? true : false;
		}
		else if($config->get('main_allowdelete', 2) == 1)
		{
		    $acl 		= DiscussHelper::getHelper( 'ACL' );
			$canDelete	= ($my->id != 0 && $acl->allowed('delete_reply', '0') ) ? true : false;
		}

		$posts		= DiscussHelper::formatReplies( $posts );
		$template	= new DiscussThemes();
		$template->set( 'replies'	, $posts );
		$template->set( 'config'	, $config );
		$template->set( 'canReply', $canReply );
		$template->set( 'canDelete'	, $canDelete );
		$template->set( 'isMainLocked'	, $isMainLocked );
		//$template->set( 'isMine'		, false );
		//$template->set( 'isAdmin'		, false );

		$template->set( 'isMine'		, DiscussHelper::isMine($parent->user_id) );
		$template->set( 'isAdmin'	, DiscussHelper::isSiteAdmin() );

		$html		= $template->fetch( 'reply.item.php' );

		$nextLimit	= $limitstart + DiscussHelper::getListLimit();

		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination a' );
		}

		$ajax->value( 'pagination-start' , $nextLimit );
		$ajax->script( 'Foundry("#dc_response").children().children( ":last").addClass( "separator" );');
		$ajax->append( 'dc_response tbody' , $html );

		$ajax->send();
	}

	public function filter( $type = null, $categoryId = null, $view = 'index')
	{
		$mainframe      = JFactory::getApplication();
		$user			= JFactory::getUser();
		$config         = DiscussHelper::getConfig();
		$acl			= DiscussHelper::getHelper( 'ACL' );
		$activeCategory = DiscussHelper::getTable( 'Category' );
		$activeCategory->load( $categoryId );
		$sort			= JRequest::getString('sort', 'latest');

		$showAllPosts           = 'all';

		if( $config->get('layout_featuredpost_style') == '1')
		{
			$showAllPosts           = false;
		}

		$postModel		= $this->getModel('Posts');
		$posts			= $postModel->getData( true , $sort , null , $type , $categoryId, null, $showAllPosts );
		$pagination		= $postModel->getPagination( '0' , $sort, $type, $categoryId, $showAllPosts );
		$posts          = DiscussHelper::formatPost($posts);
		$nextLimit		= DiscussHelper::getListLimit();

		$ajax 			= DiscussHelper::getHelper( 'Ajax' );

		if( $pagination->total > 0 )
		{
			if( $nextLimit >= $pagination->total )
			{
				$ajax->Foundry( '#dc_pagination .pagination-wrap' )->remove();
			}
		}

		$isSiteSubscribed = false;
		if( $user->id != 0 )
		{
			//logged user. check if this user subscribed to site subscription or not.
			$isSiteSubscribed = DiscussHelper::isSiteSubscribed( $user->id );
		}

		// Replace contents of sortings
		$tpl			= new DiscussThemes();
		$tpl->set( 'rssLink'			, JRoute::_( 'index.php?option=com_easydiscuss&view=index&format=feed' ) );
		$tpl->set( 'showEmailSubscribe'	, $config->get( 'main_sitesubscription', 0) );
		$tpl->set( 'filter'				, $type );
		$tpl->set( 'activeCategory'		, $activeCategory );
		$tpl->set( 'active'				, 'latest' );
		$tpl->set( 'view'				, $view );
		$tpl->set( 'issubscribed'		, $isSiteSubscribed );
		$sorting	= $tpl->fetch( 'filter.sorting.php' );

		$tpl			= new DiscussThemes();
		$tpl->set( 'posts'		, $posts );
		$content	= $tpl->fetch( 'main.item.php' );

		$paginationContent 	= '';
		if( $nextLimit < $pagination->total )
		{
			$filterArr  = array();
			$filterArr['filter'] 		= $type;
			$filterArr['category_id'] 	= $categoryId;
			$paginationContent 			= $pagination->getPagesLinks('index', $filterArr, true);
		}

		$showFeaturedList 	= true;

		if( $config->get('layout_featuredpost_style') != '0')
		{
		    if( $config->get('layout_featuredpost_style') == '2' && $type != 'allposts')
		    {
		    	$showFeaturedList 	= false;
			}
			else
			{
				$showFeaturedList 	= true;
			}
		}

		$ajax->success( $showFeaturedList , $content , $sorting , $type , $nextLimit , $paginationContent );

		$ajax->send();
	}

	public function sort( $sort = null, $filter = null, $categoryId = null )
	{
		$ajax       	= new Disjax();
		$mainframe      = JFactory::getApplication();
		$user			= JFactory::getUser();
		$config         = DiscussHelper::getConfig();
		$acl			= DiscussHelper::getHelper( 'ACL' );
		$activeCategory = DiscussHelper::getTable( 'Category' );
		$activeCategory->load( $categoryId );

		//todo: check against the config
		$showAllPosts           = 'all';
		if( $config->get('layout_featuredpost_style') == '1')
			$showAllPosts           = false;

		$postModel		= $this->getModel('Posts');
		$posts			= $postModel->getData( true , $sort , null , $filter , $categoryId, null, $showAllPosts );
		$pagination		= $postModel->getPagination( '0' , $sort, $filter, $categoryId, $showAllPosts );
		$posts          = DiscussHelper::formatPost($posts);

		$nextLimit		= DiscussHelper::getListLimit();
		if( $nextLimit >= $pagination->total )
		{
			$ajax->remove( 'dc_pagination .pagination-wrap' );
		}


		$tpl			= new DiscussThemes();
		$tpl->set( 'posts'		, $posts );
		$content	= $tpl->fetch( 'main.item.php' );

		//reset the next start limi
		$ajax->value( 'pagination-start' , $nextLimit );

		if( $nextLimit < $pagination->total )
		{

			$filterArr  = array();
			$filterArr['filter'] 		= $filter;
			$filterArr['category_id'] 	= $categoryId;
			$filterArr['sort'] 			= $sort;
			$ajax->assign( 'dc_pagination', $pagination->getPagesLinks('index', $filterArr, true) );

		}

		$ajax->script( 'discuss.spinner.hide( "index-loading" );' );
		$ajax->script( 'Foundry("#pagination-sorting").val("'.$sort.'");');

		if( $config->get('layout_featuredpost_style') != '0')
		{
		    if( $config->get('layout_featuredpost_style') == '2' && $filter != 'allposts')
		    {
				$ajax->script( 'Foundry("#dc_featured-list").hide();');
			}
			else
			{
			    $ajax->script( 'Foundry("#dc_featured-list").show();');
			}
		}

		$ajax->assign( 'dc_list' , $content );
		$ajax->script( 'Foundry("#dc_list").show();');
		$ajax->script( 'Foundry("#dc_pagination").show();');

		$ajax->send();
	}

	public function getTemplate( $name = null, $vars = array() )
	{
		$theme	= new DiscussThemes();

		if( !empty( $vars ) )
		{
			foreach( $vars as $key => $value )
			{
				$theme->set( $key , $value );
			}
		}

		$ajax	= new Disjax();
		$option	= new stdClass();
		$option->content	= $theme->fetch( $name );

		$ajax->dialog( $option );
		$ajax->send();
	}
}
