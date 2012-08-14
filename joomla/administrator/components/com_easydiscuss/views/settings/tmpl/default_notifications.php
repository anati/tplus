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
<div class="adminform-body">
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_CONFIGURATIONS' );?></legend>
				<table class="admintable" cellspacing="1">
					<tr>
						<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL_DESC'); ?>">
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_EMAIL' ); ?>
							</span>
						</td>
						<td valign="top">
							<input type="text" value="<?php echo $this->config->get( 'notification_sender_email' , JFactory::getConfig()->getValue( 'mailfrom') );?>" name="notification_sender_email" class="inputbox" style="width:300px;" />
						</td>
					</tr>
					<tr>
						<td width="300" class="key">
							<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME_DESC'); ?>">
								<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_SENDER_NAME' ); ?>
							</span>
						</td>
						<td valign="top">
							<input type="text" value="<?php echo $this->config->get( 'notification_sender_name' , JFactory::getConfig()->getValue( 'fromname') );?>" name="notification_sender_name" class="inputbox" style="width:300px;" />
						</td>
					</tr>
				</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_CUSTOM_EMAIL_ADDRESS' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" value="<?php echo $this->config->get( 'notify_custom' );?>" name="notify_custom" class="inputbox" style="width:300px;" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_ADMINS_ON_NEW_POST' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'notify_admin' , $this->config->get( 'notify_admin' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_ON_NEW_REPLY' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'notify_owner' , $this->config->get( 'notify_owner' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_SUBSCRIBER_ON_NEW_REPLY' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'notify_subscriber' , $this->config->get( 'notify_subscriber' ) );?>
					</td>
				</tr>
				
				
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFY_OWNER_WHEN_REPLY_ACCEPTED_OR_UNACCEPT' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'notify_owner_answer' , $this->config->get( 'notify_owner_answer' ) );?>
					</td>
				</tr>
				
				</tbody>
			</table>
			</fieldset>
		</td>
		<td>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key" valign="top">
						<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TITLE' ); ?>
					</td>
					<td>
						<input type="text" class="inputbox" name="notify_email_title" style="width: 250px" value="<?php echo $this->config->get( 'notify_email_title' );?>" />
					</td>
				</tr>
				<tr>
					<td class="key" valign="top" style="vertical-align: top !important;">
						<label for="email_templates"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_FILENAME'); ?></label>
					</td>
					<td valign="top">
						<?php echo $this->getEmailsTemplate(); ?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
</div>