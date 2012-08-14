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

class EasyDiscussViewPosts extends EasyDiscussView
{
	var $err	= null;

	public function showApproveDialog( $id )
	{
	    $ajax   = new Disjax();
		$options = new stdClass();
		$options->content 	= '
		
		<h3>' . JText::_( 'COM_EASYDISCUSS_DIALOG_MODERATE_TITLE' ) . '</h3>
		<p>' . JText::_( 'COM_EASYDISCUSS_DIALOG_MODERATE_CONTENT' ) . '</p>
		
		<div class="si_pop_btn">
		
		<form id="moderate-form" name="moderate" method="post">
		    <input type="button" class="si_btn" value="' . JText::_( 'COM_EASYDISCUSS_APPROVE_BUTTON' ) . '" onclick="admin.post.moderate.publish();" />
		    <input type="button" class="si_btn" value="' . JText::_( 'COM_EASYDISCUSS_REJECT_BUTTON' ) . '" onclick="admin.post.moderate.unpublish();" />
			<span class="float-r" id="dialog_loading"></span>
			<input type="hidden" name="option" value="com_easydiscuss" />
			<input type="hidden" name="controller" value="posts" />
			<input type="hidden" name="cid[]" value="' . $id . '" />
			<input type="hidden" id="moderate-task" name="task" value="" />
		</form>
		</div>
';
		$ajax->dialog( $options );
		$ajax->send();
	}
}