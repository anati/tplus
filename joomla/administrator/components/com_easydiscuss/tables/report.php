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

class DiscussReport extends JTable
{
	/*
	 * The id of the report
	 * @var int
	 */
	var $id 						= null;

	/*
	 * The id of the blog
	 * @var int
	 */
	var $post_id					= null;

	/*
	 * The reason
	 * @var string
	 */
	var $reason						= null;


	/*
	 * The id of the creator
	 * @var int
	 */
	var $created_by					= null;

	/*
	 * Created datetime of the report
	 * @var datetime
	 */
	var $created					= null;



	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__discuss_reports' , 'id' , $db );
	}

   	function load($id, $reset = true){
		return parent::load($id, $reset);
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
		    $date   = JFactory::getDate();

			//replace a url to link
			$reason        	= $post['reporttext'];

			$JFilter 			= JFilterInput::getInstance();
			$this->reason	= $JFilter->clean($reason);

			$this->created		= $date->toMySQL();
			$this->created_by	= $post['created_by'];
		}

		return true;
	}

	function getReportCount()
	{
	    $db = JFactory::getDBO();

	    $query  = 'SELECT COUNT(1) FROM `#__discuss_reports` WHERE `post_id` = ' . $db->Quote($this->post_id);
	    $db->setQuery($query);

	    $result = $db->loadResult();

	    return (empty($result)) ? 0 : $result;
	}

	function markPostReport()
	{
	    $db = JFactory::getDBO();

	    $query  = 'UPDATE `#__discuss_posts` SET `isreport` = ' . $db->Quote('1');
	    $query  .= ' WHERE `id` = ' . $db->Quote($this->post_id);

	    $db->setQuery($query);
	    $db->query();

	    return true;
	}

	function isPostReported()
	{
	    $db = JFactory::getDBO();

	    $query  = 'SELECT `isreport` FROM `#__discuss_posts`';
	    $query  .= ' WHERE `id` = ' . $db->Quote($this->post_id);

	    $db->setQuery($query);
	    $result = $db->loadResult();

	    return (empty($result)) ? 0 : $result;
	}

}
