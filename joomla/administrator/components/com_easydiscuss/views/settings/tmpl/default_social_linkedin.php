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
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_ENABLE_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_ENABLE_BUTTON_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_ENABLE_BUTTON' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_linkedin' , $this->config->get( 'integration_linkedin' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top !important;">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_LAYOUT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_LAYOUT_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_LAYOUT' ); ?>
						</span>
					</td>
					<td valign="top">
						<table width="70%">
							<tr>
								<td valign="top">
									<div>
										<input type="radio" name="integration_linkedin_button" id="linkedin_standard" value="standard"<?php echo $this->config->get('integration_linkedin_button') == 'standard' ? ' checked="checked"' : '';?> />
										<label for="linkedin_standard"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_BUTTON_STANDARD');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/linkedin/button.png';?>" /></div>
								</td>
								<td valign="top">
									<div>
										<input type="radio" name="integration_linkedin_button" id="linkedin_horizontal" value="horizontal"<?php echo $this->config->get('integration_linkedin_button') == 'horizontal' ? ' checked="checked"' : '';?> />
										<label for="linkedin_horizontal"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_BUTTON_HORIZONTAL');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/linkedin/button_horizontal.png';?>" /></div>
								</td>
								<td valign="top">
									<div>
										<input type="radio" name="integration_linkedin_button" id="linkedin_vertical" value="vertical"<?php echo $this->config->get('integration_linkedin_button') == 'vertical' ? ' checked="checked"' : '';?> />
										<label for="linkedin_vertical"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_LINKEDIN_BUTTON_VERTICAL');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/linkedin/button_vertical.png';?>" /></div>
								</td>
							</tr>
						</table>
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