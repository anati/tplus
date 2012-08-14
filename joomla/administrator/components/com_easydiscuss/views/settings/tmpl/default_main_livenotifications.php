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
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">
			<table class="noshow">
				<tr>
					<td width="98%" valign="top">
						<a name="main_notifications" id="notifications_config"></a>
						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS');?></legend>
						<table class="admintable">
							<tbody>
								<tr>
									<td width="300" class="key">
										<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_NOTIFICATIONS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_NOTIFICATIONS_DESC'); ?>">
											<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_NOTIFICATIONS' ); ?>
										</span>
									</td>
									<td valign="top">
										<?php echo $this->renderCheckbox( 'main_notifications' , $this->config->get( 'main_notifications' ) );?>
									</td>
								</tr>
								<tr>
									<td width="300" class="key">
										<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_LIMIT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_LIMIT_DESC'); ?>">
											<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_LIMIT' ); ?>
										</span>
									</td>
									<td valign="top">
										<input type="text" name="main_notifications_limit" size="5" style="text-align:center;" value="<?php echo $this->config->get( 'main_notifications_limit' );?>" />
										<?php echo JText::_( 'COM_EASYDISCUSS_ITEMS' ); ?>
									</td>
								</tr>
								<tr>
									<td width="300" class="key">
										<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL_DESC'); ?>">
											<?php echo JText::_( 'COM_EASYDISCUSS_NOTIFICATIONS_INTERVAL' ); ?>
										</span>
									</td>
									<td valign="top">
										<input type="text" size="5" style="text-align: center;" name="main_notifications_interval" value="<?php echo $this->config->get( 'main_notifications_interval' );?>" />
										<?php echo JText::_( 'COM_EASYDISCUSS_SECONDS' ); ?>
									</td>
								</tr>
							</tbody>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table class="noshow">
				<tr>
					<td>
						<a name="main_config" id="comment_config"></a>
						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_RULES' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_REPLY' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_notifications_reply' , $this->config->get( 'main_notifications_reply' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_LOCK' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_notifications_locked' , $this->config->get( 'main_notifications_locked' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_RESOLVED' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_notifications_resolved' , $this->config->get( 'main_notifications_resolved' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_ACCEPTED_ANSWER' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_notifications_accepted' , $this->config->get( 'main_notifications_accepted' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_LIVE_NOTIFICATIONS_FOR_COMMENTS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_notifications_comments' , $this->config->get( 'main_notifications_comments' ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
