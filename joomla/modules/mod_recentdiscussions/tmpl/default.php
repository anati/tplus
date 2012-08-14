<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="discuss-mod recent-discussions<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<?php if( $posts ){ ?>
	<div class="list-item">
		<?php foreach( $posts as $post ){ ?>
		<div class="item">
			<div class="story">
				<div class="item-user small">
					<?php if( $params->get( 'show_avatar' , 1 ) ){ ?>
					<a class="item-avatar float-l" href="<?php echo $post->profile->getLink(); ?>">
						<img class="avatar" src="<?php echo $post->profile->getAvatar(); ?>" height="<?php echo $params->get( 'avatar_size', 48 ); ?>" width="<?php echo $params->get( 'avatar_size', 48 ); ?>" title="<?php echo JText::sprintf( 'MOD_RECENTDISCUSSIONS_STARTED_BY', $post->profile->getName() ); ?>" />
					</a>
					<?php } ?>
					<?php echo JText::sprintf( 'MOD_RECENTDISCUSSIONS_STARTED_BY' , '<a href="' . DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $post->user_id ) . '">' . $post->profile->getName() . '</a>' );?>
				</div>

				<a class="item-title bold" href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id .'&Itemid='.$itemid );?>">
					<?php echo $post->title;?>
				</a>

				<?php if ($params->get( 'show_content' )) { ?>
				<div class="mod-story">
					<?php $post->content = strip_tags($post->content); ?>
					<?php echo JString::substr(DiscussStringHelper::escape($post->content), 0, $params->get( 'max_content' )); ?>...
				</div>
				<?php } ?>

				<?php if ($params->get('show_footer')) { ?>
				<div class="item-info push-top small">
					<span>
						<img src="<?php echo JURI::root(); ?>modules/mod_recentdiscussions/assets/clock.png">
						<?php echo DiscussDateHelper::getLapsedTime($post->created); ?>
					</span>
					<span title="<?php echo $post->num_replies . ' ' . JText::_( 'MOD_RECENTDISCUSSIONS_REPLIES' );?>">
						<img src="<?php echo JURI::root(); ?>modules/mod_recentdiscussions/assets/replies.png">
						<?php echo $post->num_replies;?>
					</span>
					<span title="<?php echo $post->hits . ' ' . JText::_('MOD_RECENTDISCUSSIONS_VIEWS' ); ?>">
						<img src="<?php echo JURI::root(); ?>modules/mod_recentdiscussions/assets/views.png"> 
						<?php echo $post->hits;?>
					</span>
				</div>
				<?php } ?>

			</div>
			<div class="both"></div>
		</div>
		<?php } ?>
	</div>
<?php } else { ?>
	<div class="no-item">
		<?php echo JText::_('MOD_RECENTDISCUSSIONS_NO_ENTRIES'); ?>
	</div>
<?php } ?>
</div>
