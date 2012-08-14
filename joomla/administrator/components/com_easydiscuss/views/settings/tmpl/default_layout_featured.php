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
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_FRONTPAGE_LISTING' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>
				<tr>
					<td class="key" rowspan="3">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY' ); ?>
						</span>
					</td>
					<td style="width:10px" class="td-radio">
						<input id="layout-style-1" type="radio" name="layout_featuredpost_style" value="0" <?php echo ( $this->config->get('layout_featuredpost_style') == '0' ) ? 'checked="checked"' : '' ;?> /> 
					</td>
					<td class="td-radio">
						<label for="layout-style-1">
							<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY_STYLE0' ); ?>
							<br />
							<span class="small" style="margin-top:3px">
								<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY_STYLE0_DESC' ); ?>
							</span>
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<input id="layout-style-2" type="radio" name="layout_featuredpost_style" value="1" <?php echo ( $this->config->get('layout_featuredpost_style') == '1' ) ? 'checked="checked"' : '' ;?> />
					</td>
					<td>
						<label for="layout-style-2">
						 	<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY_STYLE1' ); ?>
						 	<br />
							<span class="small" style="margin-top:3px">
								<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY_STYLE1_DESC' ); ?>
							</span>
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<input id="layout-style-3" type="radio" name="layout_featuredpost_style" value="2" <?php echo ( $this->config->get('layout_featuredpost_style') == '2' ) ? 'checked="checked"' : '' ;?> />
					</td>
					<td>
						<label for="layout-style-3">
							<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY_STYLE2' ); ?>
							<br />
							<span class="small" style="margin-top:3px">
							<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_DISPLAY_STYLE2_DESC' ); ?>
							</span>
						</label>
					</td>
				</tr>
				</tbody>
			</table>
			<table class="admintable">
				<tbody>	
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_LIMIT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS_LIMIT_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_POSTS_LIMIT' ); ?>
						</span>
					</td>
					<td valign="top" class="td-radio">
						<div style="margin-bottom:5px">
							<?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS_LIMIT_NOTE'); ?>
						</div>
						<input type="input" class="inputbox" name="layout_featuredpost_limit" value="<?php echo $this->config->get('layout_featuredpost_limit', '5' );?>" />
					</td>
				</tr>
				
				<tr>
					<td width="300" class="key">
						<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_SORTING' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_FEATURED_SORTING_DESC'); ?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_SORTING' ); ?>
						</span>
					</td>
					<td valign="top">
					<?php
						$featuredOrdering = array();
						$featuredOrdering[] = JHTML::_('select.option', 'date_latest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_LATEST' ) );
						$featuredOrdering[] = JHTML::_('select.option', 'date_oldest', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_DATE_OLDEST' ) );
						$featuredOrdering[] = JHTML::_('select.option', 'order_asc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_ASC' ) );
						$featuredOrdering[] = JHTML::_('select.option', 'order_desc', JText::_( 'COM_EASYDISCUSS_FEATURED_ORDER_ORDER_DESC' ) );
						$showdet = JHTML::_('select.genericlist', $featuredOrdering, 'layout_featuredpost_sort', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('layout_featuredpost_sort' , 'date_latest' ) );
						echo $showdet;
					?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
