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
<script type="text/javascript">

EasyDiscuss.ready(function($){

	discuss.attachments.initGallery({
		type: 'image',
		helpers : {
			overlay : null
		}
	});

	<?php if( $system->config->get( 'main_syntax_highlighter') ){ ?>
	SyntaxHighlighter.autoloader(
			"js jscript javascript  <?php echo rtrim( JURI::root() , '/' ); ?>/media/com_easydiscuss/js/syntax/shBrushJScript.js",
			"php  <?php echo rtrim( JURI::root() , '/' ); ?>/media/com_easydiscuss/js/syntax/shBrushPhp.js",
			"html xml xhtml  <?php echo rtrim( JURI::root() , '/' ); ?>/media/com_easydiscuss/js/syntax/shBrushXml.js",
			"perl pl	<?php echo rtrim( JURI::root() , '/' ); ?>/media/com_easydiscuss/js/syntax/shBrushPerl.js",
			"java		<?php echo rtrim( JURI::root() , '/' ); ?>/media/com_easydiscuss/js/syntax/shBrushJava.js",
			"bash		<?php echo rtrim( JURI::root() , '/' ); ?>/media/com_easydiscuss/js/syntax/shBrushBash.js",
			"css		<?php echo rtrim( JURI::root() , '/' ); ?>/media/com_easydiscuss/js/syntax/shBrushCss.js"
	);

	SyntaxHighlighter.all();
	<?php } ?>

	<?php if( ( ($system->my->id == 0 && $config->get('main_allowguestpost', 0)) || ($system->my->id != 0) ) && !$isModerate ) : ?>
	$( '#dc_reply_content' ).markItUp( mySettings );
	<?php endif; ?>
});

</script>
<?php echo $googleAdsense->header; ?>
<div id="dc_post" class="mt-15">
	<div id="dc_main_post" class="discuss-story" <?php echo ($isModerate) ? 'style="background-color:#ffeeee;border:1px solid #CD8C8C"' : ''; ?> >
		<?php echo $questionHTML; ?>
	</div>

	<?php if( $isModerate ) : ?>
	<div class="msg_in dc_alert"><?php echo JText::_( 'COM_EASYDISCUSS_POST_UNDER_MODERATE' ); ?></div>
	<?php endif; ?>
</div>
<?php echo DiscussHelper::getWhosOnline();?>
<?php if( $acceptedReply ){ ?>
<div id="dc_answer" class="mt-15 bt-sd">
	<h3 class="section-head mb-10 mt-10"><?php echo JText::_('COM_EASYDISCUSS_ENTRY_ACCEPTED_ANSWER'); ?></h3>
	<ul class="discuss-responds reset-sul mb-15">
		<?php echo $this->loadTemplate( 'reply.item.accepted.php' );?>
	</ul>
</div>
<?php } ?>
<?php echo $googleAdsense->beforereplies; ?>
<?php if( !$isModerate ) : ?>
	<?php if($config->get('main_comment', 1)) : ?>
	<div id="comment-separator"></div>
	<div id="comment-wrapper">
		<div id="comment-form" class="comment-form" style="display: none;">
			<form id="frmComment">
				<?php if($system->my->id != 0) : ?>
				<div>
				<a class="avatar float-l" href="<?php echo $system->profile->getLink(); ?>">
						<img src="<?php echo $system->profile->getAvatar(); ?>" width="35" height="35" class="avatar" />
					</a>
				</div>
				<?php endif; ?>

				<?php if($system->my->id != 0) : ?>
				<div class="dc_comment_in">
				<?php endif; ?>

					<div class="clearfull">
						<div class="textarea_wrap">
							<textarea id="comment" name="comment" class="textarea inputbox"></textarea>
						</div>
					</div>
					<?php if($config->get('main_comment_tnc', 1)) : ?>
					<div class="clearfull mt-5">
						<label><input type="checkbox" name="tnc" id="tnc" value="y" /> <?php echo JText::sprintf('COM_EASYDISCUSS_TERMS_CHECKBOX', 'javascript: disjax.load(\'post\', \'ajaxShowTnc\');'); ?></label>
					</div>
					<?php endif; ?>
					<div class="clearfull mt-5">
						<input type="hidden" name="post_id" id="post_id" value="" />
						<button id="btnSubmit" class="button" onclick="discuss.comment.save();return false;"><span></span><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT') ; ?></button>
						<button id="btnCancel" class="button" onclick="discuss.comment.cancel();return false;"><span></span><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL') ; ?></button>
						<span id="discussSubmitWait" class="float-r"></span>
					</div>
				<?php if($system->my->id != 0) : ?>
				</div>
				<div class="clr"></div>
				<?php endif; ?>
			</form>
		</div><!--end:#comment-form-->
		<div id="comment-err-msg"><div class="msg_in"></div></div>
	</div>
	<?php endif; ?>


	<div id="dc_reply" class="bt-dd mt-20">
		<?php echo $filterbar; ?>
		<div class="discuss-respond mt-20">
			<div class="in">
				<ul class="discuss-responds reset-sul mt-10">
					<?php echo $this->loadTemplate( 'reply.item.php' );?>
				</ul>
			</div>
		</div>
	</div><!--end: #dc_reply-->

	<?php echo $this->loadTemplate( 'reply.form.php' ); ?>

<?php endif; //!$isModerate ?>
<?php echo $googleAdsense->footer; ?>
