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
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_DIGG_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_DIGG_ENABLE_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_DIGG_ENABLE_BUTTON_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_DIGG_ENABLE_BUTTON' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_digg' , $this->config->get( 'integration_digg' ) );?>
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
										<input type="radio" name="integration_digg_button" id="digg_compact" value="compact"<?php echo $this->config->get('integration_digg_button') == 'compact' ? ' checked="checked"' : '';?> />
										<label for="digg_compact"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_DIGG_BUTTON_COMPACT');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/digg/compact.png';?>" /></div>
								</td>
								<td valign="top">
									<div>
										<input type="radio" name="integration_digg_button" id="digg_medium" value="medium"<?php echo $this->config->get('integration_digg_button') == 'medium' ? ' checked="checked"' : '';?> />
										<label for="digg_medium"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_DIGG_BUTTON_MEDIUM');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/digg/medium.png';?>" /></div>
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