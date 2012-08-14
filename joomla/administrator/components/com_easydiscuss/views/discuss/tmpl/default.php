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
<form name="adminForm" method="post" action="index.php">
<div class="pa-15">
	<table id="easydiscuss_panel" width="100%">
		<tr>
			<td valign="top" width="65%">
				<ul id="easydiscuss-items" class="reset-ul">
					<?php echo $this->addButton( JRoute::_('index.php?option=com_easydiscuss&view=settings') , 'settings.png' , JText::_('COM_EASYDISCUSS_SETTINGS') , JText::_('COM_EASYDISCUSS_SETTINGS_DESCRIPTION')); ?>
					<?php echo $this->addButton( JRoute::_('index.php?option=com_easydiscuss&view=autoposting') , 'autoposting.png' , JText::_('COM_EASYDISCUSS_AUTOPOST') , JText::_('COM_EASYDISCUSS_AUTOPOST_DESCRIPTION')); ?>
					<?php echo $this->addButton( JRoute::_('index.php?option=com_easydiscuss&view=categories') , 'categories.png' , JText::_('COM_EASYDISCUSS_HOME_CATEGORIES') , JText::_('COM_EASYDISCUSS_HOME_CATEGORIES_DESC')); ?>
					<?php echo $this->addButton( JRoute::_('index.php?option=com_easydiscuss&view=posts') , 'discussions.png' , JText::_('COM_EASYDISCUSS_DISCUSSIONS') , JText::_('COM_EASYDISCUSS_DISCUSSIONS_DESCRIPTION')); ?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=tags' ) , 'tags.png' , JText::_( 'COM_EASYDISCUSS_TAGS' ) , JText::_( 'COM_EASYDISCUSS_TAGS_DESCRIPTION' ) ); ?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=users' ) , 'users.png' , JText::_( 'COM_EASYDISCUSS_USERS' ) , JText::_( 'COM_EASYDISCUSS_USERS_DESCRIPTION' ) ); ?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=acls' ) , 'acl.png' , JText::_( 'COM_EASYDISCUSS_HOME_ACL' ), JText::_( 'COM_EASYDISCUSS_HOME_ACL_DESC' ) );?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=reports' ) , 'reports.png' , JText::_( 'COM_EASYDISCUSS_REPORTS' ), JText::_( 'COM_EASYDISCUSS_REPORTS_DESCRIPTION' ) );?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=subscription' ) , 'subscription.png' , JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION' ), JText::_( 'COM_EASYDISCUSS_SUBSCRIPTION_DESCRIPTION' ) );?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=badges' ) , 'badges.png' , JText::_( 'COM_EASYDISCUSS_BADGES' ), JText::_( 'COM_EASYDISCUSS_BADGES_DESCRIPTION' ) );?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=points' ) , 'points.png' , JText::_( 'COM_EASYDISCUSS_POINTS' ), JText::_( 'COM_EASYDISCUSS_POINTS_DESCRIPTION' ) );?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=ranks' ) , 'ranks.png' , JText::_( 'COM_EASYDISCUSS_RANKING' ), JText::_( 'COM_EASYDISCUSS_RANKING_DESCRIPTION' ) );?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=spools' ) , 'spools.png' , JText::_( 'COM_EASYDISCUSS_MAIL_SPOOLS' ), JText::_( 'COM_EASYDISCUSS_MAIL_SPOOLS_DESCRIPTION' ) );?>
					<?php echo $this->addButton( JRoute::_( 'index.php?option=com_easydiscuss&view=migrators' ) , 'migrators.png' , JText::_( 'COM_EASYDISCUSS_MIGRATORS' ), JText::_( 'COM_EASYDISCUSS_MIGRATORS_DESCRIPTION' ) );?>
				</ul>
				<div class="clr"></div>	
			</td>
			<td valign="top">
				<?php echo $this->loadTemplate( 'right' ); ?>
			</td>
		</tr>
	</table>
	<div style="text-align: right;margin: 10px 5px 0 0;">
		<?php echo JText::_('EasyDiscuss is a product of <a href="http://stackideas.com" target="_blank">StackIdeas</a>');?>
	</div>
</div>
</form>