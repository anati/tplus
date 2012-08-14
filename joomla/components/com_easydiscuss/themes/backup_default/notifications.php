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
<h2 class="component-head reset-h bb-sd">
	<?php echo JText::_( 'COM_EASYDISCUSS_ALL_NOTIFICATIONS' );?>
</h2>
<div class="clearfix">
	<a class="float-r pt-5" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=notification&task=markreadall' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MARK_ALL_AS_READ' ); ?></a>
</div>

<?php foreach( $notifications as $day => $data ){ ?>
<div class="notification-day">
<div class="day-seperator small fc-9 mb-10 ttu"><?php echo $day; ?></div>
<ul class="notify-items reset-ul">
	<?php foreach( $data as $item ){ ?>
		<li class="item dis-<?php echo $item->type;?><?php echo $item->state == DISCUSS_NOTIFICATION_READ ? ' read' : ' new';?>">
		<i></i>
		<div>
			<?php if( $item->state != DISCUSS_NOTIFICATION_READ){ ?>
			<span class="new-notification"><?php echo JText::_( 'COM_EASYDISCUSS_NEW' );?></span>
			<?php } ?>

			<?php echo $item->title;?>
			<span class="small"> 
				- <a href="<?php echo DiscussRouter::_( $item->permalink );?>#<?php echo $item->type;?>-<?php echo $item->cid;?>"><?php echo $item->touched; ?></a>
			</span>

			<?php if( $item->state != DISCUSS_NOTIFICATION_READ){ ?>
			&middot;
			<span class="small">
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=notification&task=markread&id=' . $item->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MARK_AS_READ' );?></a>
			</span>
			<?php } ?>
		</div>
	<?php } ?>
</ul>
</div>
<?php } ?>
