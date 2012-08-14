<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<h3><?php echo JText::_('COM_EASYDISCUSS_ENTRY_DELETE_TITLE'); ?></h3>
<form id="frmSubscribe" class="si_form" action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=posts&task=delete' );?>" method="post">
<p><?php echo JText::_('COM_EASYDISCUSS_ENTRY_DELETE_DESC'); ?></p>
<div class="dialog-buttons">
	<input type="hidden" name="id" value="<?php echo $id;?>" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />
	<input type="hidden" name="url" value="<?php echo $url; ?>" />
	<input type="submit" value="<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );?>" class="si_btn" />
	<input type="button" name="edialog-cancel" id="edialog-cancel" class="si_btn" value="<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );?>" />
	<?php echo JHTML::_( 'form.token' );?>
</div>
</form>
