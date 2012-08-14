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
<table class="adminlist">
	<tbody>
	<tr>
		<td width="70%"><?php echo JText::_('COM_EASYDISCUSS_STATS_TOTAL_DISCUSSIONS'); ?></td>
		<td><strong><?php echo $this->getTotalPosts(); ?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_EASYDISCUSS_STATS_TOTAL_RESOLVED'); ?></td>
		<td><strong><?php echo $this->getTotalSolved(); ?></strong></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_EASYDISCUSS_STATS_TOTAL_REPLIES'); ?></td>
		<td><strong><?php echo $this->getTotalReplies(); ?></strong></td>
	</tr>
	</tbody>
</table>