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
require_once( DISCUSS_HELPERS . DS . 'date.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'parser.php' );
require_once( DISCUSS_HELPERS . DS . 'filter.php' );
require_once( DISCUSS_HELPERS . DS . 'integrate.php' );
require_once( DISCUSS_CLASSES . DS . 'adsense.php' );

class EasyDiscussViewAsk extends EasyDiscussView
{
	/**
	 *	Method is called when the new form is called.
	 **/
	function display($tpl = null)
	{
		// load language strings
		DiscussHelper::loadString( JRequest::getVar('view') );

		// load editor
		DiscussHelper::loadEditor();

		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config 	= DiscussHelper::getConfig();
		$my			= JFactory::getUser();
		$post		= DiscussHelper::getTable( 'Post' );
		$postid		= JRequest::getVar('id', '');
		$acl 		= DiscussHelper::getHelper( 'ACL' );

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_ASK') );

		//set page title
		$document->setTitle( JText::_( 'COM_EASYDISCUSS_TITLE_ASK') );

		if( empty($my->id) && !$config->get('main_allowguestpostquestion', 0))
		{
			$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index' , false ) , JText::_('COM_EASYDISCUSS_PLEASE_KINDLY_LOGIN_TO_CREATE_A_POST') );
			return;
		}
		else if( $my->id != 0 && !$acl->allowed('add_question', '0') )
		{
			$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index' , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			return;
		}

		$tpl	= new DiscussThemes();
		$tpl->set( 'config'	, $config );
		$attachments    = '';

		if(!empty($postid))
		{
			$post->load( $postid );

			$isMine		= DiscussHelper::isMine($post->user_id);
			$isAdmin	= DiscussHelper::isSiteAdmin();

			if ( !$isMine && !$isAdmin && !$acl->allowed('edit_question' , 0 ) )
			{
				$mainframe->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id='.$postid , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
				return;
			}

			$isEditMode = true;

			$postsTagsModel	= $this->getModel('PostsTags');
			$post->tags = $postsTagsModel->getPostTags( $postid );

			$attachments    = $post->getAttachments();
		}
		else
		{
			// get all forms value
			$data	= DiscussHelper::getSession('NEW_POST_TOKEN');

			$post->tags = array();

			if(! empty($data))
			{
				$post->bind($data, true);

				if(isset($data['tags']))
				{
					$tags   = array();
					foreach($data['tags'] as $tag)
					{
						$obj = new stdClass();
						$obj->title = $tag;
						$tags[] = $obj;
					}
					$post->tags = $tags;
				}

				$post->bindParams( $data );
			}

			$isEditMode = false;
		}

		// select top 20 tags.
		$tagmodel	= $this->getModel( 'Tags' );
		$tags   	= $tagmodel->getTagCloud('20','post_count','DESC');

		//recaptcha integration
		$recaptcha			= '';
		$enableRecaptcha	= $config->get('antispam_recaptcha');
		$publicKey			= $config->get('antispam_recaptcha_public');

		if(  $enableRecaptcha && !empty( $publicKey ) )
		{
			require_once( DISCUSS_CLASSES . DS . 'recaptcha.php' );
			$recaptcha	= getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl') );
		}

		$onlyPublished  = ( empty( $postid ) ) ? true : false;

		// @rule: If there is a category id passed through the query, respect it first.
		$showPrivateCat     = ( empty($postid) && $my->id == 0 ) ? false : true;

		$categoryModel		= $this->getModel( 'Category' );
		$defaultCategory	= $categoryModel->getDefaultCategory();

		$category           = JRequest::getInt( 'category' , $post->category_id );

		if( $category == 0 && $defaultCategory !== false )
		{
			$category 		= $defaultCategory->id;
		}

		$nestedCategories	= DiscussHelper::populateCategories('', '', 'select', 'category_id', $category , true, $onlyPublished, $showPrivateCat);

		if( $config->get( 'layout_editor' ) == 'bbcode' )
		{
			$post->content	= Parser::html2bbcode( $post->content );
		}
		else
		{
			$post->content	= Parser::bbcode( $post->content );
		}

		$editor = '';
		if( $config->get('layout_editor' ) != 'bbcode' )
		{
			$editor	= JFactory::getEditor( $config->get('layout_editor' ) );
		}

		$tpl->set( 'acl', $acl );
		$tpl->set( 'isEditMode', $isEditMode );
		$tpl->set( 'post', $post );
		$tpl->set( 'polls' , $post->getPolls() );
		$tpl->set( 'references' , $post->getReferences( 'references' ) );
		$tpl->set( 'recaptcha', $recaptcha );
		$tpl->set( 'user', $my );
		$tpl->set( 'tags', $tags );
		$tpl->set( 'nestedCategories', $nestedCategories );
		$tpl->set( 'attachments', $attachments );
		$tpl->set( 'editor', $editor );

		echo $tpl->fetch( 'new.post.php' );
	}
}
