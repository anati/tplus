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
$joomla_date	= '%Y-%m-%d %H:%M:%S';
?>
<form name="adminForm" id="adminForm" action="index.php?option=com_easydiscuss&controller=user" method="post" enctype="multipart/form-data">
<table class="noshow" width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top">
		<?php
		$pane	= JPane::getInstance('Tabs');

		echo $pane->startPane("subuser");
		echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_USER_TAB_ACCOUNT' ) , 'account');
		echo $this->loadTemplate( 'account' );
		echo $pane->endPanel();
		echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_USER_TAB_BADGES' ) , 'badges');
		echo $this->loadTemplate( 'badges' );
		echo $pane->endPanel();
		echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_USER_TAB_HISTORY' ) , 'history');
		echo $this->loadTemplate( 'history' );
		echo $pane->endPanel();
		echo $pane->endPane();
		?>
		</td>
	</tr>
</table>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->user->id;?>" />
</form>
