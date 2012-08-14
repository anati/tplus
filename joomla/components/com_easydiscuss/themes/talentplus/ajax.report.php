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
<h3><?php echo JText::_('COM_EASYDISCUSS_REPORT_ABUSE'); ?></h3>
<form id="frmReport" class="si_form">
<div id="report-form" class="report-form">
    <p><?php echo JText::_('COM_EASYDISCUSS_REPORTING_SUBMIT_REPORT_DESC'); ?></p>
    <div class="input-wrap">
        <textarea id="reporttext" name="reporttext" class="textarea" rows="5"></textarea>
    </div>
    <input type="hidden" name="report_post_id" id="report_post_id" value="<?php echo $postId; ?>" />
</div>
<div id="reports-msg"><div class="msg_in"></div></div>
<div class="dialog-buttons">
	<input type="button" class="dialog-button" id="discuss-submit" onclick="discuss.reports.submit();" value="<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_SUBMIT' );?>" />
	<input type="button" name="edialog-cancel" id="edialog-cancel" class="dialog-button" value="<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );?>" />
	<input type="button" name="edialog-close" onclick="disjax.closedlg();" id="edialog-close" class="dialog-button" value="<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );?>" style="display:none;"/>
</div>
<?php echo JHTML::_( 'form.token' );?>
</form>