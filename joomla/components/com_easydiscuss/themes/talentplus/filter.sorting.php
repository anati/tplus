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
<ul id="sort-links" class="list-tab reset-ul float-r float-li">
	<?php
	/*****************
	INDEX AND TAG LISTING
	******************/
	?>
	<?php if($view == 'index' || $view == 'tag') : ?>
		<li class="latest <?php echo ($active == 'latest') ? 'active' : '';?>">
			<a href="javascript:void(0);" onclick="discuss.sort( 'latest' , '<?php echo $filter;?>','<?php echo $activeCategory->id;?>');">
				<?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST'); ?>
			</a>
		</li>
		<?php if($filter == 'allposts' || $filter == 'featured') : ?>
			<li class="popular<?php echo ($active == 'popular') ? ' active' : '';?>">
				<a href="javascript:void(0);" onclick="discuss.sort( 'popular' , '<?php echo $filter;?>','<?php echo $activeCategory->id;?>');">
					<?php echo JText::_('COM_EASYDISCUSS_SORT_POPULAR'); ?>
				</a>
			</li>
		<?php endif; ?>
		<li class="subscribe">
			<?php if( $system->config->get( 'main_rss') ){ ?>
			<a href="<?php echo $rssLink;?>" class="via-feed has-tip atr">
				<?php echo JText::_('COM_EASYDISCUSS_RSS'); ?>
				<span class="basic-tip">
					<i class="ico btip"></i>
					<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_RSS_TITLE' );?>
				</span>
			</a>
			<?php } ?>
			
			<?php if( $showEmailSubscribe ): ?>
				<?php
					$cid		= 0;
					$cat_id		= JRequest::getInt( 'category_id' );
					if ($cat_id)
					{
						$view	= 'category';
						$cid	= $cat_id;
					}
					$tag_id		= JRequest::getInt( 'id' );
					if ($view == 'tag' && $tag_id)
					{
						$cid	= $tag_id;
					}
				?>
				<?php echo DiscussHelper::getSubscriptionHTML( $system->my->id, $cid, $view  ); ?>
			<?php endif; ?>
		</li>
	<?php endif; ?>
	<?php
	/*****************
	POST VIEW
	aka the replies sorting
	******************/
	?>
	<?php if($view == 'post') :
		$postId = JRequest::getInt('id', '0'); ?>
		<li class="<?php echo ($active == 'latest') ? 'active' : '';?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $postId . '&sort=latest#filter-top'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST'); ?><i class="ico indicator"></i></a></li>
		<?php if( $system->config->get( 'main_allowvote') ){ ?>
			<li class="<?php echo ($active == 'voted') ? 'active' : '';?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $postId . '&sort=voted#filter-top'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_HIGHEST_VOTE'); ?><i class="ico indicator"></i></a></li>
		<?php } ?>
		<?php if( $system->config->get( 'main_likes_replies') ){ ?>
			<li class="<?php echo ($active == 'likes') ? 'active' : '';?>"><a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $postId . '&sort=likes#filter-top'); ?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_LIKED_MOST'); ?><i class="ico indicator"></i></a></li>
		<?php } ?>
	<?php endif; ?>
</ul>
