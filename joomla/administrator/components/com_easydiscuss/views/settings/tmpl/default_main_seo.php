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

			<table class="noshow">
				<tr>
					<td width="98%" valign="top">
						<a name="main_config" id="main_config"></a>
						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SEO' ); ?></legend>
						<table class="admintable" cellspacing="1" width="100%">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_SEO_ALLOW_UNICODE_ALIAS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_sef_unicode' , $this->config->get( 'main_sef_unicode' ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">&nbsp;</td>
	</tr>
</table>
