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
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_googleone' , $this->config->get( 'integration_googleone' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key" style="vertical-align: top;">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_STYLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_STYLE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_STYLE' ); ?>
						</span>
					</td>
				    <td>
						<table width="70%">
							<tr>
								<td valign="top">
									<div>
										<input type="radio" name="integration_googleone_layout" id="medium" value="medium"<?php echo $this->config->get('integration_googleone_layout') == 'medium' ? ' checked="checked"' : '';?> />
										<label for="small"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_SMALL');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/google/small.png';?>" /></div>
								</td>
							</tr>
							<tr>
								<td valign="top">
									<div>
										<input type="radio" name="integration_googleone_layout" id="tall" value="tall"<?php echo $this->config->get('integration_googleone_layout') == 'tall' ? ' checked="checked"' : '';?> />
										<label for="large"><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_BUTTON_LARGE');?></label>
									</div>
									<div><img src="<?php echo JURI::root() . 'administrator/components/com_easydiscuss/assets/images/google/large.png';?>" /></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top" width="50%">&nbsp;</td>
	</tr>
</table>	