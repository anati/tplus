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
<ul class="list-items reset-ul">
<?php
	$count = 1;
	foreach( $users as $user )
	{
?>
	<li class="<?php if( $count % 2 ) { echo 'odd'; } else { echo 'even'; } ?> pv-15">
		<div class="clearfix">
			<a href="<?php echo $user->getLink();?>" class="float-l item-avatar"><img src="<?php echo $user->getAvatar();?>" class="avatar small" /></a>
			<div class="item-info">
				<div class="item-name clearfull">
					<a href="<?php echo $user->getURL();?>" class="mr-5"><?php echo $user->getName(); ?></a>
				</div>

				<?php if ( $user->get( 'description' ) ) { ?>
				<div class="item-description mt-5 mb-5"><?php echo $user->get( 'description' ); ?></div>
				<?php } ?>

				<ul class="item-status reset-ul float-li clearfix mt-5 fs-11 small ttu">
					<li>
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $user->id . '&viewtype=user-post' );?>"><?php echo $this->getNouns( 'COM_EASYDISCUSS_USERS_TOTAL_POST' , $user->getNumTopicPosted() , true );?></a>
					</li>
					<li>
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $user->id . '&viewtype=user-replies' );?>"><?php echo $this->getNouns( 'COM_EASYDISCUSS_USERS_TOTAL_ANSWER' , $user->getNumTopicAnswered() , true );?></a>
					</li>
					<?php if ( $user->getTotalBadges() ) { ?>
					<li>
						<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $user->id . '&viewtype=user-achievements' );?>"><?php echo $user->getTotalBadges(); ?><?php echo JText::_( 'COM_EASYDISCUSS_BADGES' ); ?></a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</li>
<?php
	$count++;
	}
?>
</ul>
