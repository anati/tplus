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
?>

<h3><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_TO_POST'); ?></h3>
<div id="subscription_container">
	<p><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_POST_DESCRIPTION');?></p>
	<div id="dc_subscribe_notification"><div class="msg_in"></div></div>
	<div id="subscription_form">
	<form id="frmSubscribe" name="frmSubscribe">

		<?php if($system->my->id){ ?>
			<div style="float:left; width:100px; margin-bottom:10px"><?php echo JText::_('COM_EASYDISCUSS_EMAIL');?></div>
			<div>
				<span class="dc_ico email"><?php echo $system->my->email; ?></span>
				<input type="hidden" id="subscribe_email" name="subscribe_email" value="<?php echo $system->my->email; ?>">
			</div>
			<div style="clear:both;"></div>
			<input type="hidden" id="subscribe_name" name="subscribe_name" value="<?php echo $system->my->name; ?>">
		<?php } else { ?>
			<div style="float:left; width:100px; margin-bottom:10px"><?php echo JText::_('COM_EASYDISCUSS_EMAIL');?></div>
			<div><input type="text" id="subscribe_email" name="subscribe_email" value=""></div>
			<div style="clear:both;"></div>
			<div style="float:left; width:100px; margin-bottom:10px"><?php echo JText::_('COM_EASYDISCUSS_NAME');?></div>
			<div><input type="text" id="subscribe_name" name="subscribe_name" value=""></div>
			<div style="clear:both;"></div>
		<?php } ?>

		<div>
			<div><input type="hidden" name="subscription_interval" value="instant"></div>
		</div>
		<div style="clear:both;"></div>
	</form>
	</div>
</div>

<div class="dialog-buttons">
    <input type="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBSCRIBE'); ?>" class="si_btn" onclick="discuss.subscribe.post('<?php echo $cid ;?>')"/>
    <input type="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?>" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
    <span id="dialog_loading"></span>
</div>
