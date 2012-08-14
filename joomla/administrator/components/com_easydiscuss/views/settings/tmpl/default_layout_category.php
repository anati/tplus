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
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_SHOWTREE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_SHOWTREE_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_SHOWTREE' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_category_showtree' , $this->config->get( 'layout_category_showtree' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ALWAYS_HIDE_CATEGORY_DESCRIPTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ALWAYS_HIDE_CATEGORY_DESCRIPTION_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_ALWAYS_HIDE_CATEGORY_DESCRIPTION' ); ?>
						</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'layout_category_description_hidden' , $this->config->get( 'layout_category_description_hidden' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_SORTING' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_SORTING_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_SORTING' ); ?>
						</span>
					</td>
					<td valign="top">
					<?php
						$sortingType = array();
						$sortingType[] = JHTML::_('select.option', 'alphabet', JText::_( 'Alphabetical' ) );
						$sortingType[] = JHTML::_('select.option', 'latest', JText::_( 'Latest' ) );
						$sortingType[] = JHTML::_('select.option', 'ordering', JText::_( 'Ordering' ) );
						$categorySortHTML = JHTML::_('select.genericlist', $sortingType, 'layout_sorting_category', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_sorting_category' , 'ordering' ) );
						echo $categorySortHTML;
					?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_PATH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_CATEGORY_PATH_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_PATH' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="main_categoryavatarpath" class="inputbox" style="width: 260px;" value="<?php echo $this->config->get('main_categoryavatarpath', 'images/eblog_cavatar/' );?>" />
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
