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

<div id="dc_vote_<?php echo $post->id; ?>" class="article-vote float-l mr-15">
	<div class="vote-points <?php if($post->totalVote < 0) { echo "low"; } else if($post->totalVote > 0) { echo "high"; } ?> pos-r" id="vote_total_<?php echo $post->id; ?>">
		<b><?php echo $post->totalVote; ?></b>
		<i></i>
		<u></u>
	</div>
	<div class="vote-option">
		<a class="<?php echo $post->voted == 1 ? ' voted': ''; ?>" href="javascript:void(0);" onclick="discuss.post.vote.add('<?php echo $post->id; ?>', '1', '<?php echo $canVote; ?>');">
			<u>&nbsp;</u>
		</a>
		<a class="<?php echo $post->voted == -1 ? ' voted': ''; ?>" href="javascript:void(0);" onclick="discuss.post.vote.add('<?php echo $post->id; ?>', '-1', '<?php echo $canVote; ?>');">
			<i>&nbsp;</i>
		</a>
	</div>
</div>
