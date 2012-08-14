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
require_once( DISCUSS_HELPERS . DS . 'router.php' );

?>
<script type="text/javascript">
EasyDiscuss.ready(function($){

	$('#new_tags').keypress(function(event) {
		if(event.which == '13'){
			discuss.post.tags.add();
		}
	});

	<?php if( $system->config->get( 'layout_editor') == 'bbcode' ) { ?>
	$( '#dc_reply_content' ).markItUp( $.extend({}, mySettings, {
		onResizeUp: function(height) {
			this.autogrow({minHeight: height, lineBleed: 1});
		}
	}) );
	<?php } ?>

	<?php if( $config->get( 'main_similartopic' ) ) : ?>

	var textField = $('input#ez-title');
	var queryJob = null;
	var menuLock = false;
	textField.keydown(function( e )
	{
	    var keynum; // set the variable that will hold the number of the key that has been pressed.

	    //now, set keynum = the keystroke that we determined just happened...
	    if(window.event)// (IE)
	    {
			keynum = e.keyCode;
		}
	    else if(e.which) // (other browsers)
	    {
			keynum = e.which;
		}
	    else
		{ // something funky is happening and no keycode can be determined...
	        keynum = 0;
	    }

	    if( keynum == 9 || keynum == 27)
	    {
	        $('#dc_similar-questions').hide();
	        return;
	    }

	    clearTimeout(queryJob);

	    // Start this job after 1 second
	    queryJob = setTimeout(function()
	    {

		    if( textField.val().length <= 3 )
		        return;

			//show loading icon
		    $('#dc-search-loader').show();

			var params	= { query: textField.val() };

			params[ $( '.easydiscuss-token' ).val() ]	= 1;

			EasyDiscuss.ajax('site.views.post.similarQuestion', params ,
   			 function(data){
                //hide loading icon
   			    $('#dc-search-loader').hide();
   			    if( data != '' )
   			    {
					// Do whatever you like with the data returned from server.
					$('#dc_similar-questions').html(data);
					$('#dc_similar-questions').show();

					$('#similar-question-close').click( function()
					{
						$('#dc_similar-questions').hide();
						return;
					});
				}
	         });
	     }, 1500);
	});

	$('#dc_similar-questions')
	.bind('mousemove click', function(){
		textField.focus();
		menuLock = true;
	})
	.mouseout(function(){
	 	menuLock = false;
	});

	textField.blur( function()
	{
		if (menuLock) return;

	    $('#dc_similar-questions').hide();
	    return;
	});

	<?php endif; ?>

	// Try to test if there is a 'default' class in all of the tabs
	if( Foundry( 'ul.form-tab' ).children().find( '.default' ).html() != null )
	{
		var id 	= $( 'ul.form-tab' ).children().find( '.default' ).attr( 'id' );
		var tab = id.substr( id.indexOf( '-' ) + 1 , id.length );

		$( 'ul.form-tab' ).children().find( '.default' ).parent().addClass( 'active' );

		$( 'div.form-tab-contents' ).children().hide();
		$( '.tab-' + tab ).show();
	}
	else
	{
		// First tab always gets the active class.
		$( 'ul.form-tab' ).children( ':first' ).addClass( 'active' );
		$( 'div.form-tab-contents' ).children().hide();
		$( 'div.form-tab-contents' ).children( ':first' ).show();
	}


	<?php if( $system->config->get( 'layout_editor') == 'bbcode' ) { ?>

	var autogrowTimer;
    var autogrow = function(){
        clearTimeout(autogrowTimer);
        if ($.fn.autogrow) {
           $( '#dc_reply_content' ).autogrow({lineBleed: 1});
        } else {
           autogrowTimer = setTimeout(autogrow, 500);
        }
    }
	autogrowTimer = setTimeout(autogrow, 500);

	<?php } ?>

});
</script>

<?php if($isEditMode == true) : ?>
	<h2 class="component-head reset-h"><?php echo JText::_( 'COM_EASYDISCUSS_ENTRY_EDITING_TITLE');?></h2>
<?php else : ?>
	<h2 class="component-head reset-h"><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?></h2>
<?php endif; ?>
<div id="dc_post_notification"><div class="msg_in"></div></div>
<div id="dc_write">
<form id="dc_submit" name="dc_submit" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=submit'); ?>" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend style="font-size:12px"><?php echo JText::_('COM_EASYDISCUSS_PICK_CATEGORY'); ?></legend>
		<div class="form-row discuss-category">
			<div>
				<?php echo $nestedCategories; ?>
			</div>
		</div>
	</fieldset>

		<div class="form-row discuss-title mt-15 pos-r">
			<div class="input-wrap mr-10">
				<input type="text" autocomplete="off" class="input full-width for-title" id="ez-title" name="title" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE'); ?>';}" onfocus="if (this.value == '<?php echo JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE'); ?>') {this.value = '';}" value="<?php echo (empty($post->title)) ? JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE') : $this->escape( $post->title ); ?>"/>
			</div>
			<img id="dc-search-loader" src="<?php echo DISCUSS_SPINNER; ?>" class="pos-a" style="top:11px;right:11px;display:none;" >
			<div id="dc_similar-questions" style="display:none">
			</div>
		</div>

		<div class="form-row discuss-editor mt-5">
			<?php if( $system->config->get( 'layout_editor') == 'bbcode' ) { ?>
			<textarea id="dc_reply_content" name="dc_reply_content" class="textarea fullwidth"><?php echo $this->escape( $post->content ); ?></textarea>
			<?php } else { ?>
			<?php echo $editor->display( 'dc_reply_content', $post->content, '100%', '350', '10', '10' , array( 'pagebreak' , 'readmore', 'image' ) ); ?>
			<?php } ?>
		</div>

		<?php if($config->get('notify_owner', 1)) : ?>
		<div class="form-row">
			<div class="bt-0 bb-0 b-sc pa-10 bg-ff">
				<div class="clearfix">
					<input type="checkbox" value="1" name="self_subscribe" class="input float-l" id="subscribe" checked="checked" />
					<label class="fs-11 float-l ml-5" for="subscribe"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_TO_POST'); ?></label>
				</div>
			</div>
		</div>
		<?php endif;?>

		<?php if( $system->config->get( 'main_polls') ){ ?>
		<div class="form-row discuss-poll form-polls">
			<?php echo $this->loadTemplate( 'field.polls.php' ); ?>
		</div>
		<?php }?>


		<div class="form-tabs mt-15">
			<ul class="form-tab reset-ul float-li in-block">
				<?php if( $system->config->get( 'main_tags' ) ){ ?>
				<li><a href="javascript:void(0);" onclick="discuss.tabs.show(this,'tags');"><?php echo JText::_('COM_EASYDISCUSS_POST_CREATE_TAGS'); ?></a></li>
				<?php } ?>

				<?php echo $this->getFieldTabs( true , $post );?>

				<?php if( $system->config->get( 'main_password_protection' ) ){ ?>
				<li><a href="javascript:void(0);" onclick="discuss.tabs.show(this,'password');"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD_TITLE' ); ?></a></li>
				<?php } ?>
			</ul>
		</div>


		<div class="form-tab-contents">
			<?php if( $system->config->get( 'main_tags' ) ){ ?>
			<div class="form-tab-item tab-tags">
				<?php echo $this->loadTemplate( 'field.tags.php' ); ?>
			</div>
			<?php } ?>

			<?php echo $this->getFieldForms( true , $post ); ?>

			<?php if( $system->config->get( 'main_password_protection') ){ ?>
			<div class="form-tab-item tab-password" style="display:none">
				<?php echo $this->loadTemplate( 'field.password.php' ); ?>
			</div>
			<?php } ?>
		</div>



		<?php if(empty($user->id) && $system->config->get('main_allowguestpostquestion', 0)) { ?>
		<div class="form-tab-item discuss-author mt-15 clearfix">
			<div class="float-l mr-15">
				<label for="poster_name" class="float-l fs-11 mr-10"><?php echo JText::_('COM_EASYDISCUSS_NAME'); ?> :</label>
				<div class="input-wrap mr-10">
					<input class="input width-200" type="text" id="poster_name" name="poster_name" value="<?php echo empty($post->poster_name) ? '' : $post->poster_name; ?>"/>
				</div>
			</div>
			<div class="float-l">
				<label for="poster_email" class="float-l fs-11 mr-10"><?php echo JText::_('COM_EASYDISCUSS_EMAIL'); ?> :</label>
				<div class="input-wrap mr-10">
					<input class="input width-200" type="text" id="poster_email" name="poster_email" value="<?php echo empty($post->poster_email) ? '' : $post->poster_email; ?>"/>
				</div>
			</div>
		</div>
		<?php } ?>

		<?php if(! empty($recaptcha)) { ?>
		<div class="form-row discuss-recaptcha">
			<div id="post_new_antispam"><?php echo $recaptcha; ?></div>
		</div>
		<?php } ?>

		<div class="form-row discuss-submit">
			<div class="clearfix mt-10">
				<div class="discuss-button float-r">
				<?php if($isEditMode == true) : ?>
					<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
					<input type="hidden" name="parent_id" id="parent_id" value="0" />
					<input type="hidden" name="isresolve" id="isresolve" value="<?php echo $post->isresolve; ?>" />
					<input type="button" name="cancel" id="Cancel" class="button-cancel" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?>" onclick="history.back()" />
					<input type="button" name="editpost" id="editpost" class="button-submit" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT'); ?>" onclick="discuss.post.submit();" />
				<?php else : ?>
					<input type="button" name="createpost" id="createpost" class="button-submit" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT'); ?>" onclick="discuss.post.submit();" />
					<input type="hidden" name="id" id="id" value="" />
					<input type="hidden" name="parent_id" id="parent_id" value="0" />
				<?php endif; ?>
				</div>
			</div>
		</div>

<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
