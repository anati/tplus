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
<div class="section-body">
	<h3><?php echo JText::_( 'COM_EASYDISCUSS_BADGES' );?></h3>
	<?php if( $badges ){ ?>
	<ul class="list-badge reset-ul float-li">
		<?php foreach( $badges as $badge ){ ?>
		<li>
			<a href="#" class="badge-icon float-l"><img src="<?php echo $badge->getAvatar();?>" width="48" /></a>
			<div class="badge-text">
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" class="badge-name"><?php echo $badge->get( 'title' );?></a>
				<div class="date-obtained small ttu"><?php echo JText::sprintf( 'COM_EASYDISCUSS_ACHIEVED_ON' , $badge->getAchievedDate( $profileId ) );?></div>
			</div>
		</li>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<div>
		<?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_YET' );?>
	</div>
	<?php } ?>
</div>