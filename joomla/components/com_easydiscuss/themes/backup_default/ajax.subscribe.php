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
<h3><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_TO_' . strtoupper($type) . '_DISCUSSION' ); ?></h3>
<div id="subscription_container">
	<p><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_' . strtoupper($type) . '_DESCRIPTION');?></p>
	<div id="dc_subscribe_notification"><div class="msg_in"></div></div>
	<div id="subscription_form">
		<form id="frmSubscribe" name="frmSubscribe" class="si_form">
		<table>
			<tbody>
				<?php if($system->my->id): ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_YOUR_EMAIL');?></td>
					<td class="value">
						<span class="dc_ico email"><b><?php echo $system->my->email; ?></b></span>
						<input type="hidden" id="subscribe_email" name="subscribe_email" value="<?php echo $system->my->email; ?>">
						<input type="hidden" id="subscribe_name" name="subscribe_name" value="<?php echo $system->my->name; ?>">
					</td>
				</tr>
				<?php else: ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_YOUR_EMAIL');?></td>
					<td class="value">
						<div class="dc_input_wrap">
							  <input type="text" id="subscribe_email" name="subscribe_email" value="">
						</div>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EASYDISCUSS_NAME');?></td>
					<td class="value">
						<div class="dc_input_wrap">
							<input type="text" id="subscribe_name" name="subscribe_name" value="">
						</div>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td class="key"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INTERVAL');?></td>
					<td class="value">
						<input type="radio" name="subscription_interval" value="instant" id="subscription_instant">
						<label for="subscription_instant"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INSTANT');?></label>
						<input type="radio" name="subscription_interval" value="daily" checked id="subscription_daily">
						<label for="subscription_daily"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_DAILY');?></label>
						<input type="radio" name="subscription_interval" value="weekly" id="subscription_weekly">
						<label for="subscription_weekly"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_WEEKLY');?></label>
						<input type="radio" name="subscription_interval" value="monthly" id="subscription_monthly">
						<label for="subscription_monthly"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_MONTHLY');?></label>
					</td>
				</tr>
				</tbody>
		</table>
		<div class="dialog-buttons">
			<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
			<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<input type="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL');?>" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
			<input type="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBSCRIBE');?>" class="si_btn" onclick="discuss.subscribe.<?php echo $type; ?>(<?php echo $cid; ?>);" />
			<span id="dialog_loading" class="float-r"></span>
		</div>
		</form>
	</div>
</div>
