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

if( !$system->config->get( 'attachment_questions' ) || !$system->acl->allowed('add_attachment', '0'))
{
	return;
}
?>
<li><a href="javascript:void(0);" onclick="discuss.tabs.show(this,'attachments');" id="tab-attachments"><?php echo JText::_( 'COM_EASYDISCUSS_UPLOAD_ATTACHMENTS' ); ?></a></li>