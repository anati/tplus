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
?>
<?php if ( ( $system->my->id == $reply->user_id && $system->my->id != 0 ) || $isMine || $isAdmin || $system->acl->allowed('edit_reply', '0') ) : ?>
<form id="form_edit_<?php echo $reply->id;?>" name="dc_submit" action="#" method="post">
<div id="post_content_edit_<?php echo $reply->id; ?>" class="respond-editor" style="display: none;">
	<div class="form-row discuss-editor">
		<textarea id="reply_content_<?php echo $reply->id; ?>" name="content" class="textarea fullwidth"><?php echo $this->escape( $reply->content_raw ); ?></textarea>
	</div>

    <div class="form-tabs mt-10">
		<ul class="reply-edit-field-tabs form-tab reset-ul float-li in-block">
			<?php echo $this->getFieldTabs( false );?>
		</ul>
	</div>

	<div class="reply-edit-field-forms">
		<?php echo $this->getFieldForms( false , $reply );?>
	</div>

	<?php if(! empty($recaptcha)) { ?>
	<div class="form-row discuss-recaptcha">
		<div id="reply_edit_antispam_<?php echo $reply->id;?>" class="respond-recaptcha"><?php echo $recaptcha; ?></div>
	</div>
	<?php } ?>
    
    <div class="form-row discuss-button pt-10">
		<div class="value">
			<input type="button" id="mypost_save_<?php echo $reply->id; ?>" class="button-submit float-l" onclick="discuss.post.save('<?php echo $reply->id; ?>');" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT'); ?>" />
			<input type="button" id="mypost_cancel_<?php echo $reply->id; ?>" class="button-cancel float-l" onclick="discuss.post.cancel('<?php echo $reply->id; ?>');" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?>" />
			<div class="float-r" style="display: block;" id="reply_edit_loading"></div>
		</div>
	</div>
</div>
<input type="hidden" name="post_id" value="<?php echo $reply->id;?>" />
</form>
<?php endif; ?>
