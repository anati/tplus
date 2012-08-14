<?php
/**
* @package		Discuss
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
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_AKISMET_INTEGRATIONS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_INTEGRATIONS' ); ?></span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'antispam_akismet' , $this->config->get( 'antispam_akismet' ) );?>
					</td>
				</tr>
				
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_API_KEY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_AKISMET_API_KEY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_AKISMET_API_KEY' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" class="inputbox" name="antispam_akismet_key" value="<?php echo $this->config->get('antispam_akismet_key');?>" size="60" />
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>

			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_FILTERING' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_BAD_WORDS_FILTER' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_filterbadword' , $this->config->get( 'main_filterbadword' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_BAD_WORDS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_BAD_WORDS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_BAD_WORDS' ); ?>
					</span>
					</td>
					<td valign="top">
						<textarea name="main_filtertext" rows="5" class="inputbox  full-width textarea" cols="35"><?php echo $this->config->get('main_filtertext');?></textarea>
						<BR />(<?php echo JText::_( 'COM_EASYDISCUSS_USE_COMMA_TO_SEPARATE_EACH_WORD' ); ?>)
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">

			<!-- recaptcha -->
			<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS'); ?></legend>
			<p><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS_DESC');?></p>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS'); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_RECAPTCHA'); ?>">
							<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_INTEGRATIONS'); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'antispam_recaptcha' , $this->config->get( 'antispam_recaptcha' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_USE_SSL' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_USE_SSL_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_USE_SSL' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'antispam_recaptcha_ssl' , $this->config->get( 'antispam_recaptcha_ssl' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PUBLIC_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" class="inputbox" name="antispam_recaptcha_public" value="<?php echo $this->config->get('antispam_recaptcha_public');?>" size="60" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_PRIVATE_KEY' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" class="inputbox" name="antispam_recaptcha_private" value="<?php echo $this->config->get('antispam_recaptcha_private');?>" size="60" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_THEME' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_THEME' ); ?>
						</span>
					</td>
					<td valign="top">
						<select name="antispam_recaptcha_theme">
							<option value="clean"<?php echo $this->config->get('antispam_recaptcha_theme') == 'clean' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_CLEAN');?></option>
							<option value="white"<?php echo $this->config->get('antispam_recaptcha_theme') == 'white' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_WHITE');?></option>
							<option value="red"<?php echo $this->config->get('antispam_recaptcha_theme') == 'red' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_RED');?></option>
							<option value="blackglass"<?php echo $this->config->get('antispam_recaptcha_theme') == 'blackglass' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_THEME_BLACKGLASS');?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_LANGUAGE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_RECAPTCHA_LANGUAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<select name="antispam_recaptcha_lang">
							<option value="en"<?php echo $this->config->get('antispam_recaptcha_lang') == 'en' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_ENGLISH');?></option>
							<option value="ru"<?php echo $this->config->get('antispam_recaptcha_lang') == 'ru' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_RUSSIAN');?></option>
							<option value="fr"<?php echo $this->config->get('antispam_recaptcha_lang') == 'fr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_FRENCH');?></option>
							<option value="de"<?php echo $this->config->get('antispam_recaptcha_lang') == 'de' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_GERMAN');?></option>
							<option value="nl"<?php echo $this->config->get('antispam_recaptcha_lang') == 'nl' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_DUTCH');?></option>
							<option value="pt"<?php echo $this->config->get('antispam_recaptcha_lang') == 'pt' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_PORTUGUESE');?></option>
							<option value="tr"<?php echo $this->config->get('antispam_recaptcha_lang') == 'tr' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_TURKISH');?></option>
							<option value="es"<?php echo $this->config->get('antispam_recaptcha_lang') == 'es' ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_RECAPTCHA_LANGUAGE_SPANISH');?></option>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>


		</td>
	</tr>
</table>
</div>