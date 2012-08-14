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

if( !$system->config->get( 'attachment_questions' ) )
{
	return;
}

$attachments 	= false;

if( $postObj )
{
	$attachments 	= $postObj->getAttachments();
}

?>
<?php if( $system->config->get( 'attachment_questions' ) && $acl->allowed('add_attachment', '0')){ ?>
	<div class="form-tab-item tab-attachments" style="display:none">
		<div class="form-row discuss-attachments">
			<?php if( $system->my->id != 0 && $system->acl->allowed( 'add_attachment' , 0 ) && $system->config->get( 'attachment_questions') ){ ?>
			<div class="field-attachment discuss-attachments-upload">
			    <ul class="upload-queue attach-list for-file reset-ul">
			    <?php if( isset( $attachments ) && !empty( $attachments ) ){ ?>
			        <?php for($i = 0; $i < count( $attachments ); $i++ ){ ?>
			            <li class="attachments-<?php echo $attachments[ $i ]->getType(); ?>">
							<?php echo $attachments[ $i ]->title;?>
							<?php if( $system->acl->allowed( 'delete_attachment' , 0 ) ) : ?>
							 - <a onclick="discuss.attachments.removeItem(this,'<?php echo $attachments[ $i ]->id;?>');" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' );?></a>
							 <?php endif; ?>
							<input type="hidden" id="attachment-<?php echo $attachments[ $i ]->id; ?>" />
						</li>
			        <?php } ?>
			    <?php } ?>
				</ul>

			    <div class="attach-input">
					<input type="file" name="filedata[]" size="50" onchange="discuss.attachments.addQueue( this );" />
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>