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

require( DISCUSS_ADMIN_ROOT . DS . 'views.php');
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'parser.php' );

jimport('joomla.html.pane');

class EasyDiscussViewPost extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$document	= JFactory::getDocument();
		$document->addStyleSheet( JURI::root() . 'components/com_easydiscuss/assets/css/common.css' );
		$document->addScript( JURI::root() . 'administrator/components/com_easydiscuss/assets/js/admin.js' );

		$postId   = JRequest::getInt('id', 0);
		$parentId = JRequest::getString('pid', '');
		$source = JRequest::getVar('source', 'posts');

		$post		= JTable::getInstance( 'Posts' , 'Discuss' );
		$post->load($postId);

		//get post's tags
		$postModel	= $this->getModel( 'Posts' );
		$post->tags	= $postModel->getPostTags( $post->id );
		$post->content  = Parser::html2bbcode( $post->content );

		// select top 20 tags.
		$tagmodel		= $this->getModel( 'Tags' );
		$populartags   	= $tagmodel->getTagCloud('20','post_count','DESC');

		$repliesCnt = $postModel->getPostRepliesCount( $post->id );

		$nestedCategories = DiscussHelper::populateCategories('', '', 'select', 'category_id', $post->category_id, true, true);

		$this->assignRef( 'post' 			, $post );
		$this->assignRef( 'populartags' 	, $populartags );
		$this->assignRef( 'repliesCnt' 		, $repliesCnt );
		$this->assignRef( 'source' 			, $source );
		$this->assignRef( 'parentId' 		, $parentId );
		$this->assignRef( 'nestedCategories' 		, $nestedCategories );
		$this->assignRef( 'joomlaversion' , DiscussHelper::getJoomlaVersion() );

		//load require javascript string
		DiscussHelper::loadString( JRequest::getVar('view') );

		//load the bbcode editor
		DiscussHelper::loadEditor();

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'Editing post' ), 'discussions' );

		JToolBarHelper::back();
		JToolBarHelper::divider();
		//JToolBarHelper::custom('submit','save.png','save_f2.png', JText::_('SAVE'), false);
		JToolBarHelper::save('submit');
		JToolBarHelper::cancel();
	}
}
