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
						<a name="main_attachments" id="main_attachments"></a>
						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ATTACHMENTS' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS_DESC'); ?>">
										<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS' ); ?>
									</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'attachment_questions' , $this->config->get( 'attachment_questions' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE_DESC'); ?>">
										<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE' ); ?>
									</span>
								</td>
								<td valign="top">
									<input type="text" name="attachment_maxsize" class="inputbox" style="text-align: center;width: 30px;" value="<?php echo $this->config->get('attachment_maxsize' );?>" />&nbsp;<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE_MEGABYTES' );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
									<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH_DESC'); ?>">
										<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH' ); ?>
									</span>
								</td>
								<td valign="top">
									<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH_INFO' );?><input type="text" name="attachment_path" class="inputbox" style="width: 100px;" value="<?php echo $this->config->get('attachment_path' );?>" />
								</td>
							</tr>
							<tr>
								<td width="300" class="key" style="vertical-align:top;">
									<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION_DESC'); ?>">
										<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION' ); ?>
									</span>
								</td>
								<td valign="top">
									<textarea name="main_attachment_extension" class="inputbox full-width" cols="65" rows="5"><?php echo $this->config->get( 'main_attachment_extension' ); ?></textarea>
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
