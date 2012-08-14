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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'views.php' );

class EasyDiscussViewAttachments extends EasyDiscussView
{
	public function delete( $id )
	{
	    $ajax		= new Disjax();
	    $user       = JFactory::getUser();

	    // @rule: Do not allow empty id or guests to delete files.
		if( empty( $id ) || empty( $user->id ) )
		{
		    return false;
		}

		$table      = DiscussHelper::getTable( 'Attachments' );
		$table->load( $id );

		if( !$table->delete() )
		{
		    return false;
		}

		$ajax->script( "Foundry( '#post_content_edit_" . $table->uid . " .discuss-attachments .upload-queue' ).find( '#attachment-" . $table->id . "' ).parent().remove();" );
		$ajax->send();
	}
}
