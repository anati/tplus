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
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SITE_SUBSCRIPTION' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_sitesubscription' , $this->config->get( 'main_sitesubscription' ) );?>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_SUBSCRIPTION' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_postsubscription' , $this->config->get( 'main_postsubscription' ) );?>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTO_POST_SUBSCRIPTION' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_autopostsubscription' , $this->config->get( 'main_autopostsubscription' ) );?>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_GUEST_SUBSCRIPTION' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_allowguestsubscribe' , $this->config->get( 'main_allowguestsubscribe' ) );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_SPOOL' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_SEND_EMAIL_ON_PAGE_LOAD' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_mailqueueonpageload' , $this->config->get( 'main_mailqueueonpageload' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_HTML_FORMAT' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'notify_html_format' , $this->config->get( 'notify_html_format' ) );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
