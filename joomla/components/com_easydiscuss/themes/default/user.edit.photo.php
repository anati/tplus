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
<link rel="stylesheet" type="text/css" href="<?php echo rtrim( JURI::root() , '/' ); ?>/components/com_easydiscuss/assets/css/imgareaselect/imgareaselect.css" />
<script type="text/javascript">
EasyDiscuss.require( ['jquery.imgareaselect'] , function(){

	Foundry( 'a#crop-image' ).click(function(){

		// Hide link to crop image
		Foundry( 'a#crop-image' ).hide();

		Foundry( 'span#save-image' ).show();

		// Clear out existing messages
		Foundry( '#discuss-message' ).html('').hide();

		Foundry( 'img#original-photo' ).imgAreaSelect({
			disable: false,
			handles: true,
			resizable: false,
			minWidth: 160,
			minHeight: 160,
			onSelectEnd: function( img , selection ){

				Foundry( '#preview-avatar img' ).attr( 'src' , '<?php echo $profile->getOriginalAvatar();?>' );

				Foundry( '#preview-avatar img').css({
					marginLeft: '-' + selection.x1 + 'px',
					marginTop: '-' + selection.y1 + 'px'
				});

				Foundry( '#x' ).val( selection.x1 );
				Foundry( '#y' ).val( selection.y1 );
			}
		});
	});

	Foundry( 'a#cancel-crop' ).click(function(){
		// Hide save image
		Foundry( 'span#save-image' ).hide();

		// Show crop image
		Foundry( 'a#crop-image' ).show();

		Foundry( 'img#original-photo' ).imgAreaSelect({
			hide: true,
			disable: true
		});

	});

	Foundry( '#update-crop' ).click( function(){

		var x = Foundry( '#x' ).val();
		var y = Foundry( '#y' ).val();

		// Don't run anything if nothing has been selected.
		if( x == '' || y == '' )
		{
			Foundry( 'a#cancel-crop' ).click();

			return false;
		}

		var params	= { args: [ x, y ] };

		params[ $( '.easydiscuss-token' ).val() ]	= 1;
		
		EasyDiscuss.ajax( 'site.views.profile.cropphoto' , params ,
		function(){
			// Do something after save
			Foundry( 'a#cancel-crop' ).click();

			Foundry( '#discuss-message' ).addClass( 'info' )
				.css(
					{
						'border': '0',
						'background-color': 'transparent'
					})
				.html( 'Your profile picture is updated.' )
				.show();
		});
	});
});

EasyDiscuss.ready(function($){
	$( '#preview-avatar').css({
		float: 'left',
		position: 'relative',
		overflow: 'hidden',
		width: '160px',
		height: '160px',
		border: '1px solid #ccc'
	});
});
</script>
<div class="tab-item user-photo">
	<?php if( $avatarIntegration == 'gravatar' ){ ?>
	<p>
		<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_INTEGRATED_WITH');?> <a href="http://gravatar.com" target="_blank"><?php echo JText::_( 'COM_EASYDISCUSS_GRAVATAR' );?></a>.<br />
		<?php echo JText::_( 'COM_EASYDISCUSS_GRAVATAR_EMAIL' );?> <strong><?php echo $user->get( 'email' ); ?></strong>
	</p>
	<?php } ?>
	
	<?php
	if($config->get('layout_avatar') && $avatarIntegration == 'default' )
	{
	?>
	<p> <?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_AVATAR_DESC'); ?></p>
	<div class="form-row">
		<div id="file-upload-area">
			<input id="file-upload" type="file" name="Filedata" size="25"/>
		</div>
		<span><?php echo JText::sprintf( 'COM_EASYDISCUSS_PROFILE_AVATAR_CONDITION' , $configMaxSize, $size ); ?></span>
	</div>
	<?php
		if($config->get('layout_avatar'))
		{
			$original 	= $profile->getOriginalAvatar();

			if( $original !== false && $croppable )
			{
		?>
		<div>
			<p>
				<?php echo JText::_( 'COM_EASYDISCUSS_ORIGINAL_IMAGE' );?> <a href="javascript:void(0);" id="crop-image"><?php echo JText::_( 'COM_EASYDISCUSS_CROP_IMAGE' );?></a>
				<span id="save-image" style="display:none;">
					<input type="button" id="update-crop" class="button-submit" value="<?php echo JText::_( 'COM_EASYDISCUSS_UPDATE_BUTTON');?>" />
					<?php echo JText::_( 'COM_EASYDISCUSS_OR' ); ?>
					<a href="javascript:void(0);" id="cancel-crop"><?php echo JText::_( 'COM_EASYDISCUSS_CANCEL' ); ?></a>
				</span>
			</p>
			<div id="discuss-message"></div>
			<img src="<?php echo $original; ?>" id="original-photo"/>
			<input type="hidden" name="x" id="x" value="" />
			<input type="hidden" name="y" id="y" value="" />
		</div>
			<?php } ?>
		<div class="clearfix">
			<div class="float-l avatar-item">
				<p><?php echo JText::_( 'COM_EASYDISCUSS_YOUR_PICTURE' );?></p>
				<div id="preview-avatar">
					<img src="<?php echo $profile->getAvatar(false); ?>"/>
				</div>
			</div>
		</div>
		<?php
		}
		else
		{
			echo JText::_('COM_EASYDISCUSS_PROFILE_AVATAR_DISABLED');
		}
	?>
	<?php
	}
	?>


</div>
