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
<h2 class="component-head reset-h badges clearfix">
	<img src="<?php echo $badge->getAvatar();?>" border="0" class="float-l mr-10" />
	<?php echo $badge->get( 'title' );?>
</h2>
<div class="pb-15 mb-10 bb-sd">
	<?php echo $badge->get('description');?>
</div>

<h3><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_ACHIEVERS' );?></h3>
<?php if( $users ){ ?>
<ul class="list-badge achievers reset-ul float-li clearfix">
	<?php foreach( $users as $user ){ ?>
	<li>
		<div class="clearfix">
			<a href="#">
				<img src="<?php echo $user->getAvatar();?>" border="0" width="40" class="float-l mr-10"/>
			</a>
			<a href="#" class="badge-name"><?php echo $user->getName();?></a>
			<div class="date-obtained small ttu"><div class="date-obtained small ttu"><?php echo JText::sprintf( 'COM_EASYDISCUSS_ACHIEVED_ON' , $badge->getAchievedDate( $user->id ) );?></div></div>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div>
	<?php echo JText::_( 'COM_EASYDISCUSS_BADGES_NO_USERS' ); ?>
</div>
<?php } ?>
