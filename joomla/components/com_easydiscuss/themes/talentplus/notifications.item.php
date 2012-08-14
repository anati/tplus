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
<?php if( $notifications ){ ?>
	<?php foreach($notifications as $notification){ ?>
	<li class="item dis-<?php echo $notification->type;?><?php echo $notification->state == DISCUSS_NOTIFICATION_READ ? ' read' : ' new';?>">
		<i></i>
		<a href="<?php echo DiscussRouter::_( $notification->permalink );?>#<?php echo $notification->type;?>-<?php echo $notification->cid;?>">

		<?php echo $notification->title;?>
		<br />
		<small><?php echo $notification->touched; ?></small>
		</a>
	</li>
	<?php } ?>
	<li class="item dis-all">
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=notifications' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_ALL_NOTIFICATIONS' );?></a>
	</li>
<?php } else { ?>
	<li class="item dis-none">
		<div class="pa-10 tac"><?php echo JText::_( 'COM_EASYDISCUSS_NO_NEW_NOTIFICATIONS_YET' ); ?></div>
	</li>
<?php } ?>