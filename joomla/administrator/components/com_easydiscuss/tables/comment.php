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

require_once( DISCUSS_HELPERS . DS . 'string.php' );

class DiscussComment extends JTable
{
	/*
	 * The id of the comment
	 * @var int
	 */
	var $id 						= null;

	/*
	 * The id of the blog
	 * @var int
	 */
	var $post_id					= null;

	/*
	 * The comment
	 * @var string
	 */
	var $comment					= null;

	/*
	 * The name of the commenter
	 * @var string
	 */
	var $name					= null;

	/*
	 * The title of the comment
	 * optional
	 * @var string
	 */
	var $title					= null;

	/*
	 * The email of the commenter
	 * optional
	 * @var string
	 */
	var $email					= null;

	/*
	 * The website of the commenter
	 * optional
	 * @var string
	 */
	var $url					= null;

	/*
	 * The ip of the visitor
	 * optional
	 * @var string
	 */
	var $ip					= null;


	/*
	 * Created datetime of the comment
	 * @var datetime
	 */
	var $created				= null;

	/*
	 * modified datetime of the comment
	 * optional
	 * @var datetime
	 */
	var $modified				= null;

	/*
	 * Tag publishing status
	 * @var int
	 */
	var $published		= null;


	/*
	 * Comment ordering
	 * @var int
	 */
	var $ordering			= null;

	/*
	 * user id
	 * @var int
	 */
	var $user_id			= null;


	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__discuss_comments' , 'id' , $db );
	}

   	function load($keys = null, $reset = true){
		return parent::load($keys = null, $reset = true);
   	}

	/**
	 *
	 *
	 */
	function bind($post, $isPost = false)
	{
	    parent::bind( $post );

		if($isPost)
		{
		    $date   		= JFactory::getDate();
		    jimport( 'joomla.filter.filterinput' );
			$filter			= JFilterInput::getInstance();

			//replace a url to link
			$comment			= $filter->clean( $post[ 'comment' ] );
			$comment    		= DiscussStringHelper::url2link( $comment );
			$this->comment		= $comment;

			$this->name			= $filter->clean($post['name']);
			$this->email		= $filter->clean($post['email']);

			$this->created		= $date->toMySQL();
			$this->modified		= $date->toMySQL();
			$this->published	= '1';
		}

		return true;
	}
}
