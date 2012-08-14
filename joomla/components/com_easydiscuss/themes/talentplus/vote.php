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
<div id="dc_vote_<?php echo $post->id; ?>" class="discuss-vote float-l br-2">
	<?php if ( $canVote && !$isLock) : ?>
		<div class="vote-up pos-r" id="voteup_<?php echo $post->id;?>">
			<a class="ir<?php echo $post->voted == 1 ? ' voted': ''; ?>" href="javascript:void(0);" onclick="discuss.post.vote.add('<?php echo $post->id; ?>', '1', '<?php echo $canVote; ?>');">
				<span class="irs"><?php echo JText::_('COM_EASYDISCUSS_VOTE_UP'); ?></span>
			</a>
		</div>
	<?php endif; ?>

	<div class="vote-total <?php if($post->totalVote < 0) { echo "low"; } else if($post->totalVote > 0) { echo "high"; } ?> pos-r" id="vote_total_<?php echo $post->id; ?>">
		<span><?php echo $post->totalVote; ?></span>
		<!--
		<div class="pos-a fs-11">
			<b>14 votes</b> by
			<a href="/index.php?option=com_easydiscuss&amp;view=profile&amp;id=67&amp;Itemid=54">Kate Wilson</a>,
			<a href="/index.php?option=com_easydiscuss&amp;view=profile&amp;id=62&amp;Itemid=54">Danny Miller</a>
		</div>
		-->
	</div>

	<?php if( $canVote && !$isLock) : ?>
		<div class="vote-down pos-r" id="votedown_<?php echo $post->id;?>">
			<a class="ir<?php echo $post->voted == -1 ? ' voted': ''; ?>" href="javascript:void(0);" onclick="discuss.post.vote.add('<?php echo $post->id; ?>', '-1', '<?php echo $canVote; ?>');">
				<span class="irs"><?php echo JText::_('COM_EASYDISCUSS_VOTE_DOWN'); ?></span>
			</a>
		</div>
	<?php endif; ?>
</div>
