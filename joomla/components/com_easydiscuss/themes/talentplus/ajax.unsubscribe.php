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
<h3><?php echo $subTitle; ?></h3>
<div id="subscription_container">
	<p><?php echo $subDescription; ?></p>
	<div id="dc_subscribe_notification"><div class="msg_in"></div></div>
	<div id="subscription_form">
		<form id="frmUnSubscribe" name="frmUnSubscribe" class="si_form">
		<input type="hidden" id="subscribe_id" name="subscribe_id" value="<?php echo $id; ?>">
		<div class="dialog-buttons">
			<input type="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_UNSUBSCRIBE');?>" class="si_btn" onclick="<?php echo $subCall; ?>" />
			<input type="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL');?>" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
			<span id="dialog_loading" class="float-r"></span>
		</div>
		</form>
	</div>
</div>