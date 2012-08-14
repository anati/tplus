<?php
/**
* @package      EasyDiscuss
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
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_TWITTER_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_ENABLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_TWITTER_ENABLE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_twitter_enable' , $this->config->get( 'integration_twitter_enable' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_CONSUMER_KEY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_TWITTER_CONSUMER_KEY_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_CONSUMER_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="integration_twitter_consumer_key" class="inputbox" value="<?php echo $this->config->get('integration_twitter_consumer_key');?>" size="60" />
						<a href="http://stackideas.com/docs/easydiscuss/twitter/obtaining-your-twitter-application-settings.html" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?></a>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_CONSUMER_SECRET_KEY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_TWITTER_CONSUMER_SECRET_KEY_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_CONSUMER_SECRET_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="integration_twitter_consumer_secret_key" class="inputbox" value="<?php echo $this->config->get('integration_twitter_consumer_secret_key');?>" size="60" />
						<a href="http://stackideas.com/docs/easydiscuss/twitter/obtaining-your-twitter-application-settings.html" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?></a>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_twitter_button' , $this->config->get( 'integration_twitter_button' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_STYLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_STYLE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_STYLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<table width="70%">
							<tr>
								<td valign="top">
									<div>
										<input type="radio" name="integration_twitter_button_style" id="tweet_vertical" value="vertical"<?php echo $this->config->get('integration_twitter_button_style') == 'vertical' ? ' checked="checked"' : '';?> />
										<label for="tweet_vertical"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_VERTICAL');?></label>
									</div>
									<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/tweet/button_vertical.png';?>" /></div>
								</td>
								<td valign="top">
									<div>
										<input type="radio" name="integration_twitter_button_style" id="tweet_horizontal" value="horizontal"<?php echo $this->config->get('integration_twitter_button_style') == 'horizontal' ? ' checked="checked"' : '';?> />
										<label for="tweet_horizontal"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_HORIZONTAL');?></label>
									</div>
									<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/tweet/button_horizontal.png';?>" /></div>
								</td>
								<td valign="top">
									<div>
										<input type="radio" name="integration_twitter_button_style" id="tweet_button" value="none"<?php echo $this->config->get('integration_twitter_button_style') == 'none' ? ' checked="checked"' : '';?> />
										<label for="tweet_button"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_TWITTER_BUTTON_NOCOUNT');?></label>
									</div>
									<div style="text-align: center;margin-top: 5px;"><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/tweet/button.png';?>" /></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top" width="50%">
		</td>
	</tr>
</table>	