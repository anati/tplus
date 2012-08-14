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
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ADMIN_ID' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="integration_facebook_like_admin" class="inputbox" value="<?php echo $this->config->get('integration_facebook_like_admin');?>" size="60" />
						<a href="http://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-account-id.html" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?></a>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_APP_ID' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="integration_facebook_like_appid" class="inputbox" value="<?php echo $this->config->get('integration_facebook_like_appid');?>" size="60" />
						<a href="http://stackideas.com/docs/easydiscuss/facebook/obtaining-your-facebook-application-settings.html" target="_blank" style="margin-left:5px;"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS'); ?></a>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_SCRIPTS' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_facebook_scripts' , $this->config->get( 'integration_facebook_scripts' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_ENABLE_LIKES' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_facebook_like' , $this->config->get( 'integration_facebook_like' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_SEND' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_facebook_like_send' , $this->config->get( 'integration_facebook_like_send' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_SHOW_FACES' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_facebook_like_faces' , $this->config->get( 'integration_facebook_like_faces' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_LAYOUT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_LAYOUT_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_LAYOUT' ); ?>
						</span>
					</td>
					<td valign="top">
						<table width="70%">
							<tr>
								<td valign="top">
									<div>
										<input type="radio" name="integration_facebook_like_layout" id="standard" value="standard"<?php echo $this->config->get('integration_facebook_like_layout') == 'standard' ? ' checked="checked"' : '';?> />
										<label for="standard"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_BUTTON_STANDARD');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/facebook/standard.png';?>" /></div>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<div>
										<input type="radio" name="integration_facebook_like_layout" id="button_count" value="button_count"<?php echo $this->config->get('integration_facebook_like_layout') == 'button_count' ? ' checked="checked"' : '';?> />
										<label for="button_count"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_BUTTON_COUNT');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/facebook/button_count.png';?>" /></div>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<div>
										<input type="radio" name="integration_facebook_like_layout" id="box_count" value="box_count"<?php echo $this->config->get('integration_facebook_like_layout') == 'box_count' ? ' checked="checked"' : '';?> />
										<label for="box_count"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_BUTTON_BOX_COUNT');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/facebook/box_count.png';?>" /></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_WIDTH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_WIDTH_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_WIDTH' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="integration_facebook_like_width" class="inputbox" style="width: 50px;" value="<?php echo $this->config->get('integration_facebook_like_width');?>" size="5" /> <span class="extra-text"><?php echo JText::_('COM_EASYDISCUSS_PIXELS');?></span>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB' ); ?>
						</span>
					</td>
					<td valign="top">
						<select name="integration_facebook_like_verb" class="inputbox">
							<option<?php echo $this->config->get( 'integration_facebook_like_verb' ) == 'like' ? ' selected="selected"' : ''; ?> value="like"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_LIKES');?></option>
							<option<?php echo $this->config->get( 'integration_facebook_like_verb' ) == 'recommend' ? ' selected="selected"' : ''; ?> value="recommend"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_VERB_RECOMMENDS');?></option>
						</select>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES' ); ?>
						</span>
					</td>
					<td valign="top">
						<select name="integration_facebook_like_theme" class="inputbox">
							<option<?php echo $this->config->get( 'integration_facebook_like_theme' ) == 'light' ? ' selected="selected"' : ''; ?> value="light"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_LIGHT');?></option>
							<option<?php echo $this->config->get( 'integration_facebook_like_theme' ) == 'dark' ? ' selected="selected"' : ''; ?> value="dark"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_FACEBOOK_LIKE_THEMES_DARK');?></option>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td width="50%" valign="top">
			<!-- right -->
		</td>
	</tr>
</table>
