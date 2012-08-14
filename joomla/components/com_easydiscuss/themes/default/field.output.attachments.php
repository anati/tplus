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

$attachments 	= $postObj->getAttachments();
?>
<?php if( $attachments ) { ?>
<div class="discuss-attachments mt-15">
    <p class="attachment-title"><strong><?php echo JText::_( 'COM_EASYDISCUSS_ATTACHMENTS' ); ?>:</strong></p>
    <ul class="attachments-list attach-list for-file reset-ul">
    <?php foreach( $attachments as $attachment ) { ?>
    	<li class="attachment-<?php echo $attachment->attachmentType; ?>"><?php echo $attachment->toHTML(); ?></li>
    <?php } ?>
    </ul>
</div>
<?php } ?>