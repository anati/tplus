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

$enableRecaptcha	= $config->get('antispam_recaptcha');
$publicKey			= $config->get('antispam_recaptcha_public');

if(  $enableRecaptcha && !empty( $publicKey ) )
{
	require_once( DISCUSS_CLASSES . DS . 'recaptcha.php' );
	$recaptcha	= getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl') , 'new-reply-recaptcha');
}
?>
<script type="text/javascript">

EasyDiscuss.ready(function($){
	$( '#reply-field-tabs' ).children( ':first' ).addClass( 'active' );

	$( '#reply-field-forms' ).children( ':first' ).css( 'display' , 'block' );

	// Hide all other fields
	$( '#reply-field-forms' ).children( ':first' ).siblings().css( 'display' , 'none' );

	var autogrowTimer;
    var autogrow = function(){
        clearTimeout(autogrowTimer);
        if ($.fn.autogrow) {
           $( '#dc_reply_content' ).autogrow({
           		lineBleed: 1
           });
        } else {
           autogrowTimer = setTimeout(autogrow, 500);
        }
    }

	autogrowTimer = setTimeout(autogrow, 500);
});

</script>
<div id="dc_main_reply_lock" class="msg_in dc_alert" style="display:<?php echo (!$isMainLocked) ? 'none' : 'block';?>"><?php echo JText::_('COM_EASYDISCUSS_POST_LOCKED'); ?></div>
<?php if ( !$isMainLocked ) { ?>
<div id="dc_user_reply" class="mt-15 bt-dd" style="display:<?php echo (!$isMainLocked) ? 'block' : 'none';?>" >
	<a name="add-respond">&nbsp;</a>
	<div class="section-head fwb pt-15 pb-15">
		<?php echo JText::_('COM_EASYDISCUSS_ENTRY_YOUR_RESPONSE'); ?>

	</div>
		<a name="reply"></a>
		<?php if(! $canReply) { ?>
		<div class="msg_in dc_alert">
		<?php
			if(empty($system->my->id))
			{
				echo JText::_('COM_EASYDISCUSS_ENTRY_REQUIRED_LOGIN_TO_REPLY');
			}
			else
			{
				echo JText::_('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY');
			}
		?>
		</div>
		<?php } else { ?>
			<div id="dc_main_reply_form">
				<div id="dc_notification"><div class="msg_in"></div></div>
				<div id="dc_post" class="discuss-story">
					<div class="discuss-content">
						<form id="dc_submit" name="dc_submit" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=reply'); ?>" method="post">
							<?php /* markitup editor */ ?>
							<div class="form-row discuss-editor">
								<textarea id="dc_reply_content" name="dc_reply_content" class="textarea fullwidth"></textarea>
							</div>

							<div class="form-tabs mt-10">
								<ul id="reply-field-tabs" class="form-tab reset-ul float-li in-block">
									<?php echo $this->getFieldTabs( false );?>
								</ul>
							</div>

							<div id="reply-field-forms">
								<?php echo $this->getFieldForms( false );?>
							</div>

							<?php /* editor's recaptcha*/ ?>
							<?php if(! empty($recaptcha)) { ?>
							<div id="reply_new_antispam" class="respond-recaptcha mt-10"><?php echo $recaptcha; ?></div>
							<?php } ?>

							<div class="form-row editor-submit pt-10">
								<input type="hidden" id="title" name="title" value="Re: <?php echo DiscussStringHelper::escape($parent_title); ?>" />
								<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $parent_id; ?>" />
								<input type="hidden" name="parent_catid" id="parent_catid" value="<?php echo $parent_catid; ?>" />


								<input type="hidden" name="user_type" id="user_type" value="" />
								<input type="hidden" name="poster_name" id="poster_name" value="" />
								<input type="hidden" name="poster_email" id="poster_email" value="" />
								<?php if( $system->my->id == 0 ): ?>
									<input type="button" name="submit-reply" id="submit-reply" class="button-submit float-l" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT_RESPONSE'); ?>" onclick="discuss.reply.verify();return false;" />
								<?php else: ?>
									<input type="button" name="submit-reply" id="submit-reply" class="button-submit float-l" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT_RESPONSE'); ?>" onclick="discuss.reply.submit();return false;" />
								<?php endif; ?>
								<div class="float-r" id="reply_loading"></div>
							</div>
						</form>
					</div><!---end:dc_post_in-->
				</div><!---end:dc_post-->
			</div><!---end:dc_main_reply_form-->
		<?php } //end if else ?>
		<div class="clr"></div>
</div><!---end:dc_ur_reply-->
<?php } ?>
