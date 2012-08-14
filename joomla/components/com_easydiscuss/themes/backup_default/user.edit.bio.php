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
	$( '#signature' ).markItUp( mySettings );
});
</script>
<div class="tab-item user-bio" style="display:none">
	<div class="form-row">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FULLNAME'); ?></div>
		<div class="input-wrap"><input type="textbox" value="<?php echo $this->escape( $user->name ); ?>" name="fullname" class="input width-350"></div>
	</div>
	<div class="form-row">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_NICKNAME'); ?></div>
		<div class="input-wrap mrm"><input type="textbox" value="<?php echo $this->escape( $profile->nickname ); ?>" name="nickname" class="input width-250"></div>
	</div>
	<div class="form-row">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_DESCRIPTION'); ?></div>
		<div class="input-wrap"><textarea name="description" class="input width-400" rows="5"><?php echo $profile->description; ?></textarea></div>
	</div>
	<div class="form-row">
		<div class="input-label pb-10"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SIGNATURE'); ?> <span><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SIGNATURE_INFO'); ?></span></div>
		<div class="input-wrap"><textarea name="signature" id="signature" class="input"><?php echo $profile->getSignature( true ); ?></textarea></div>
	</div>
	<div class="form-row">
		<div class="input-label facebook pb-10"><?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?></div>
		<div class="input-wrap"><input type="textbox" value="<?php echo $userparams->get( 'facebook' ) ?>" name="facebook" class="input width-350"></div>
		<div class="input-wrap mt-10">
			<input type="checkbox" value="1" id="show_facebook" name="show_facebook" class="input float-l"<?php echo $userparams->get( 'show_facebook' ) ? ' checked="1"' : ''; ?>>
			<label for="show_facebook"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
		</div>
	</div>
	<div class="form-row">
		<div class="input-label twitter pb-10"><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></div>
		<div class="input-wrap"><input type="textbox" value="<?php echo $userparams->get( 'twitter' ) ?>" name="twitter" class="input width-350"></div>
		<div class="input-wrap mt-10">
			<input type="checkbox" value="1" id="show_twitter" name="show_twitter" class="input float-l"<?php echo $userparams->get( 'show_twitter' ) ? ' checked="1"' : ''; ?>>
			<label for="show_twitter"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
		</div>
	</div>
	<div class="form-row">
		<div class="input-label linkedin pb-10"><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?></div>
		<div class="input-wrap"><input type="textbox" value="<?php echo $userparams->get( 'linkedin' ) ?>" name="linkedin" class="input width-350"></div>
		<div class="input-wrap mt-10">
			<input type="checkbox" value="1" id="show_linkedin" name="show_linkedin" class="input float-l"<?php echo $userparams->get( 'show_linkedin' ) ? ' checked="1"' : ''; ?>>
			<label for="show_linkedin"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
		</div>
	</div>
</div>
