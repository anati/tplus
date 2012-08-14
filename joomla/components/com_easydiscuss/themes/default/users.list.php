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
$sort	= JRequest::getCmd('sort', 'name');
?>
<h2 class="component-head reset-h"><?php echo JText::_('COM_EASYDISCUSS_MEMBERS'); ?></h2>

<ul class="list-tab reset-ul float-li clearfix bb-sd">
	<li class="user-l-name<?php echo ($sort == 'name') ? ' active' : ''; ?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users&sort=name'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_NAME_ASC'); ?></a></li>
	<li class="user-l-visit<?php echo ($sort == 'lastvisit') ? ' active' : ''; ?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users&sort=lastvisit'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_VISITDATE_DESC'); ?></a></li>
	<li class="user-l-joined<?php echo ($sort == 'latest') ? ' active' : ''; ?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users&sort=latest'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_REGISTERDATE_DESC'); ?></a></li>
	<li class="user-m-active<?php echo ($sort == 'postcount') ? ' active' : ''; ?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users&sort=postcount'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_POSTCOUNT_DESC'); ?></a></li>
	<li class="user-l-active<?php echo ($sort == 'lastactive') ? ' active' : ''; ?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users&sort=lastactive'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_LASTPOST_DESC'); ?></a></li>
</ul>

<div id="dc_users-list" class="mt-15">
	<?php echo $this->loadTemplate( 'users.item.php' ); ?>
</div>
<div id="dc_pagination">
	<?php echo $pagination->getPagesLinks();?>
</div>
