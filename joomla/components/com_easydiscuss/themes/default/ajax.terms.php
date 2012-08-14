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
<h3><?php echo JText::_('COM_EASYDISCUSS_TERMS_AND_CONDITIONS'); ?></h3>
<p style="text-align:left;"><?php echo nl2br($system->config->get('main_comment_tnctext')); ?></p>
<div class="dialog-buttons">
	<input type="button" value="Ok" class="si_btn" id="edialog-submit" name="edialog-submit" />
</div>
