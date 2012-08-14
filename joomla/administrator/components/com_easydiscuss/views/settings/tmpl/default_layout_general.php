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
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_DISPLAY' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_THEME' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_THEME_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_THEME' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->getThemes( $this->config->get('layout_theme', 'default') ); ?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_EDITOR' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_EDITOR_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_EDITOR' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->getEditorList( $this->config->get('layout_editor') ); ?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LIST_LIMIT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LIST_LIMIT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_LIST_LIMIT' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_list_limit" value="<?php echo $this->config->get('layout_list_limit' );?>" size="5" style="text-align:center;"/>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_HEADERS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_HEADERS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_HEADERS' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_headers' , $this->config->get( 'layout_headers' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ZERO_AS_PLURAL' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_zero_as_plural' , $this->config->get( 'layout_zero_as_plural' ) );?>
					</td>
				</tr>

				<tr>
					<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_DATE_FORMAT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_DATE_FORMAT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_DATE_FORMAT' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_dateformat" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('layout_dateformat' , '%b %d, %Y' );?>" />
						<a href="http://my.php.net/manual/en/function.strftime.php" target="_blank" class="extra_text"><?php echo JText::_('COM_EASYDISCUSS_DATE_FORMAT'); ?></a>
					</td>
				</tr>

				<tr>
					<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_MINIMISE_POST_IF_HIT_MINIMUM_VOTE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_AUTO_MINIMISE_POST_IF_HIT_MINIMUM_VOTE_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_AUTO_MINIMISE_POST_IF_HIT_MINIMUM_VOTE' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_autominimisepost" class="inputbox" style="width: 80px;" value="<?php echo $this->config->get('layout_autominimisepost' , '5' );?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_NUMBER_OF_DAYS_A_POST_STAY_AS_NEW' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_daystostaynew" class="inputbox" style="width: 80px;" value="<?php echo $this->config->get('layout_daystostaynew' , '7' );?>" />
					</td>
				</tr>

				<tr>
					<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT' ); ?>
					</span>
					</td>
					<td valign="top">
					<?php
						$nameFormat = array();
						$nameFormat[] = JHTML::_('select.option', 'name', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_REAL_NAME' ) );
						$nameFormat[] = JHTML::_('select.option', 'username', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_USERNAME' ) );
						$nameFormat[] = JHTML::_('select.option', 'nickname', JText::_( 'COM_EASYDISCUSS_DISPLAY_NAME_FORMAT_NICKNAME' ) );
						$showdet = JHTML::_('select.genericlist', $nameFormat, 'layout_nameformat', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_nameformat' , 'name' ) );
						echo $showdet;
					?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_INTROTEXT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_INTROTEXT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_INTROTEXT' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_enableintrotext' , $this->config->get( 'layout_enableintrotext' ) );?>
					</td>
				</tr>
				<tr>
					<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_INTROTEXT_LENGTH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_INTROTEXT_LENGTH_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_INTROTEXT_LENGTH' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_introtextlength" class="inputbox" style="width: 80px;" value="<?php echo $this->config->get('layout_introtextlength' , '200' );?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_WRAPPERCLASS_SFX' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_WRAPPERCLASS_SFX_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_WRAPPERCLASS_SFX' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="layout_wrapper_sfx" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('layout_wrapper_sfx' , '' );?>" />
					</td>
				</tr>


				<tr>
					<td class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_TAB' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_REPLIES_SORTING_TAB_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_TAB' ); ?>
					</span>
					</td>
					<td valign="top">
					<?php
						$filterFormat = array();
						$filterFormat[] = JHTML::_('select.option', 'latest', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_LATEST' ) );
						$filterFormat[] = JHTML::_('select.option', 'voted', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_VOTED' ) );
						$filterFormat[] = JHTML::_('select.option', 'likes', JText::_( 'COM_EASYDISCUSS_REPLIES_SORTING_BY_LIKES' ) );
						$showdet = JHTML::_('select.genericlist', $filterFormat, 'layout_replies_sorting', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_replies_sorting' , 'latest' ) );
						echo $showdet;
					?>
					</td>
				</tr>


				</tbody>
			</table>
			</fieldset>
		</td>
		<td valing="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TOOLBAR' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TOOLBAR_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TOOLBAR' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_enabletoolbar' , $this->config->get( 'layout_enabletoolbar' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CREATE_NEW_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_CREATE_NEW_BUTTON_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CREATE_NEW_BUTTON' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbarcreate' , $this->config->get( 'layout_toolbarcreate' ) );?>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SEARCH_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_SEARCH_BUTTON_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SEARCH_BUTTON' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbarsearch' , $this->config->get( 'layout_toolbarsearch' ) );?>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TAGS_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TAGS_BUTTON_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TAGS_BUTTON' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbartags' , $this->config->get( 'layout_toolbartags' ) );?>
					</td>
				</tr>

				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_CATEGORIES_BUTTON' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbarcategories' , $this->config->get( 'layout_toolbarcategories' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_DISCUSSION_BUTTON' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbardiscussion' , $this->config->get( 'layout_toolbardiscussion' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_PROFILE_BUTTON' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbarprofile' , $this->config->get( 'layout_toolbarprofile' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_USERS_BUTTON' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_USERS_BUTTON_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_USERS_BUTTON' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbarusers' , $this->config->get( 'layout_toolbarusers' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_LOGIN' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbarlogin' , $this->config->get( 'layout_toolbarlogin' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_LAYOUT_TOOLBAR_BADGES' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_toolbarbadges' , $this->config->get( 'layout_toolbarbadges' ) );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
