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
<h2 class="component-head reset-h"><?php echo JText::_('COM_EASYDISCUSS_BADGES'); ?></h2>
<ul class="list-tab reset-ul float-li clearfix bb-sd">
	<li class="badge-all<?php echo $active == 'all' ? ' active' :'';?>"><a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_ALL_BADGES' );?></a></li>
	<?php if( $system->my->id > 0 ){ ?>
	<li class="badge-mine<?php echo $active == 'mybadges' ? ' active' :'';?>"><a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=mybadges' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_MY_BADGES' );?></a></li>
	<?php } ?>
</ul>
<div id="dc_badges-list" class="mt-15 clearfix">
	<?php if( $badges ){ ?>
		<div class="list-items">
		<?php 
			$count = 1;
			foreach( $badges as $badge ){ 
		?>
			<div class="badge-item float-l width-half mv-15">
				<div class="<?php if( $count % 2 ) { echo 'mr-15'; } else { echo 'ml-15'; } ?>">
					<div class="item-avatar float-l"><img src="<?php echo $badge->getAvatar();?>" width="48" height="48" class="float-l" /></div>
					<div class="item-info badge">
						<div class="item-name in-block pos-r pr-20">
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" ><?php echo $badge->get( 'title' );?></a>
							<?php if( $badge->achieved( $system->my->id ) ){ ?>
							<i class="checked pos-a atr" title="<?php echo JText::_( 'COM_EASYDISCUSS_ACHIEVED');?>"></i>
							<?php }?>
						</div>
						<div class="item-description mt-5 mb-5"><?php echo $badge->get( 'description' );?></div>
						<ul class="item-status reset-ul float-li clearfix mt-5 fs-11 small ttu">
							<li><?php echo JText::sprintf( 'COM_EASYDISCUSS_BADGE_CREATION_DATE' , $this->formatDate( '%B %e, %Y', $badge->get( 'created' ) ) );?></li>
							<li>
								<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->id ); ?>">
									<?php echo JText::sprintf( 'COM_EASYDISCUSS_BADGE_TOTAL_ACHIEVERS' , $badge->getTotalAchievers() );?>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		<?php
				$count ++;
		?>
		<?php
				if( $count % 2 ) {
		?>
		<div class="clear"></div>
		<?php
				} // close if($count % 2)
			} // close foreach( $badges as $badge )
		?>
		</div>
	<?php } else { ?>
		<?php if( $active == 'all'){ ?>
			<div class="small"><?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_CREATED' ); ?></div>
		<?php } else { ?>
			<div class="small"><?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_ACHIEVED' ); ?></div>
		<?php } ?>
	<?php } ?>
</div>