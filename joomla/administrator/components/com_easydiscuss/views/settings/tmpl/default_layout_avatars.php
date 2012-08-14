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
<table class="noshow">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_AVATARS' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AVATARS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_AVATARS_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AVATARS' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_avatar' , $this->config->get( 'layout_avatar' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_AVATAR_IN_POST_PAGE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SHOW_AVATAR_IN_POST_PAGE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_SHOW_AVATAR_IN_POST_PAGE' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_avatar_in_post' , $this->config->get( 'layout_avatar_in_post' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_SIZE_PIXELS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_AVATARS_SIZE_PIXELS_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_SIZE_PIXELS' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" class="inputbox" name="layout_avatarwidth" style="width: 50px;" value="<?php echo $this->config->get('layout_avatarwidth', '160' );?>" /> <span class="extra_text" style="margin-right: 5px;">x</span> <input type="text" class="inputbox" name="layout_avatarheight" style="width: 50px;" value="<?php echo $this->config->get('layout_avatarheight', '160' );?>" />
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_AVATARS_THUMBNAIL_SIZE_PIXELS' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" class="inputbox" name="layout_avatarthumbwidth" style="width: 50px;" value="<?php echo $this->config->get('layout_avatarthumbwidth', '60' );?>" /> <span class="extra_text" style="margin-right: 5px;">x</span> <input class="inputbox" type="text" name="layout_avatarthumbheight" style="width: 50px;" value="<?php echo $this->config->get('layout_avatarthumbheight', '60' );?>" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_MAX_UPLOAD_SIZE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_MAX_UPLOAD_SIZE_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_MAX_UPLOAD_SIZE' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="main_upload_maxsize" class="inputbox" style="width: 100px;" value="<?php echo $this->config->get('main_upload_maxsize', '0' );?>" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_PATH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_AVATAR_PATH_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_PATH' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="main_avatarpath" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_avatarpath', 'images/discuss_avatar/' );?>" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_INTEGRATION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_AVATAR_INTEGRATION_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_AVATAR_INTEGRATION' ); ?>
					</span>
					</td>
					<td valign="top">
					<?php
						$nameFormat = array();
						$avatarIntegration[] = JHTML::_('select.option', 'default', JText::_( 'Default' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'easyblog', JText::_( 'EasyBlog' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'communitybuilder', JText::_( 'Community Builder' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'gravatar', JText::_( 'Gravatar' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'jomsocial', JText::_( 'Jomsocial' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'kunena', JText::_( 'Kunena' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'phpbb', JText::_( 'PhpBB' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'anahita', JText::_( 'Anahita' ) );
						$avatarIntegration[] = JHTML::_('select.option', 'easyblog', JText::_( 'COM_EASYDISCUSSS_SETTINGS_LAYOUT_AVATAR_EASYBLOG' ) );
						$showdet = JHTML::_('select.genericlist', $avatarIntegration, 'layout_avatarIntegration', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_avatarIntegration' , 'default' ) );
						echo $showdet;
					?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_PATH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_PHPBB_PATH_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_PATH' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_phpbb_path" class="inputbox full-width" value="<?php echo $this->config->get('layout_phpbb_path', '' );?>" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_URL' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_PHPBB_URL_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_PHPBB_URL' ); ?>
						</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_phpbb_url" class="inputbox full-width" value="<?php echo $this->config->get('layout_phpbb_url', '' );?>" />
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
