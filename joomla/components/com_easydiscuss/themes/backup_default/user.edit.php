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
$currentTagId = JRequest::getInt('id');
?>
<script type="text/javascript">
EasyDiscuss.ready(function( $ ){

	// Bind all click events
	$( '.user-tabs .list-tab' ).children().each( function(){
		
		var element		= $(this);

		$( element ).find( 'a' ).click(function(){
			var tabName	= 'user-' + $( element ).attr( 'id' );

			discuss.user.tabs.show( $( this ) , tabName );
		});
	});

	$( '.user-tabs .list-tab' ).children( ':first' ).addClass('active').find( 'a' ).click();
});
</script>
<h2 class="component-head reset-h"><?php echo JText::_('COM_EASYDISCUSS_EDIT_PROFILE'); ?></h2>
<div id="dc_profile">
<form id="dashboard" name="dashboard" enctype="multipart/form-data" method="post" action="">
	<div class="user-tabs pos-r mb-20">
	
		<ul class="list-tab reset-ul float-li clearfix bb-sc">
			<?php if( $system->config->get( 'layout_avatarIntegration') == 'default' || $system->config->get( 'layout_avatarIntegration') == 'gravatar'  ){ ?>
			<li id="photo">
				<a href="javascript:void(0);">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_PICTURE' );?></b>
				</a>
			</li>
			<?php } ?>
			<li id="bio">
				<a href="javascript:void(0);">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_BIOGRAPHY' );?></b>
				</a>
			</li>
			<li id="post">
				<a href="javascript:void(0);">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_ACCOUNT' ); ?></b>
				</a>
			</li>
			<li id="location">
				<a href="javascript:void(0);">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_LOCATION' ); ?></b>
				</a>
			</li>
			<li id="alias">
				<a href="javascript:void(0);">
					<b><?php echo JText::_( 'COM_EASYDISCUSS_PROFILE_URL' );?></b>
				</a>
			</li>
		</ul>
	</div>

	<div class="user-content pb-20">
		<?php if( $system->config->get( 'layout_avatarIntegration') == 'default' || $system->config->get( 'layout_avatarIntegration') == 'gravatar'  ){ ?>
			<?php echo $this->loadTemplate( 'user.edit.photo.php' ); ?>
		<?php } ?>

		<?php echo $this->loadTemplate( 'user.edit.bio.php' ); ?>

		<?php echo $this->loadTemplate( 'user.edit.location.php' ); ?>

		<?php echo $this->loadTemplate( 'user.edit.account.php' ); ?>
	</div>
	<div class="form-submit bg-f5 bt-sc pa-10 mt-10">
		<div class="clearfix">
			<input type="submit" class="button-submit" name="save" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SAVE'); ?>" />
		</div>
	</div>

	<input type="hidden" name="controller" value="profile" />
	<input type="hidden" name="task" value="saveProfile" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div><!--end:#dc_profile-->
