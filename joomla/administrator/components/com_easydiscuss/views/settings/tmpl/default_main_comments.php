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
					<td>
						<a name="main_config" id="comment_config"></a>
						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_COMMENT' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_COMMENT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_COMMENT_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_COMMENT' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_comment' , $this->config->get( 'main_comment' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_COMMENT_TNC_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_comment_tnc' , $this->config->get( 'main_comment_tnc' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key" valign="top">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC_TITLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_COMMENT_TNC_TITLE_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC_TITLE' ); ?>
								</span>
								</td>
								<td valign="top">
									<textarea name="main_comment_tnctext" class="inputbox full-width" cols="65" rows="15"><?php echo str_replace('<br />', "\n", $this->config->get('main_comment_tnctext' )); ?></textarea>
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
