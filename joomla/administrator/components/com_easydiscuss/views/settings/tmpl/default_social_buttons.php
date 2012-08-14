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
		    <!-- left -->
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_PINGOMATIC_TITLE' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_PINGOMATIC_ENABLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_PINGOMATIC_ENABLE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_PINGOMATIC_ENABLE' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'integration_pingomatic' , $this->config->get( 'main_pingomatic' ) );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td>
		    <!-- right -->
		</td>
	</tr>
</table>