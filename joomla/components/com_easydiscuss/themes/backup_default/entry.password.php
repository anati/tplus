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
<?php echo (isset($googleAdsense)) ? $googleAdsense->header : ''; ?>

<?php if( isset( $postview ) ) : ?>
<div class="mt-15 mb-15">
	<div class="discuss-head">
		<h2 id="title_<?php echo $post->id; ?>" class="discuss-title reset-h mb-15 fwb"><?php echo $post->title; ?></h2>
		<div class="discuss-meta clearfix">
			<?php if(empty($post->user->id)) { ?>
				<div class="avatar float-l mr-10">
					<img src="<?php echo $creator->getAvatar(); ?>" width="40" class="avatar" />
				</div>
			<?php } else { ?>
				<a class="avatar float-l mr-10" href="<?php echo  $creator->getLink(); ?>">
					<img src="<?php echo $creator->getAvatar(); ?>" width="40" class="avatar" title="<?php echo $post->user->name; ?>" />
				</a>
			<?php } ?>
			<div class="discuss-author fwb">
				<?php
				if(empty($post->user->id))
				{
					echo $post->poster_name . ' (' . JText::_('COM_EASYDISCUSS_GUEST') . ')';
				}
				else
				{
				?>
					<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $post->user->id );?>" class="fwb"><?php echo $post->user->name; ?></a>
				<?php
				}
				?>

				<?php echo JText::sprintf('COM_EASYDISCUSS_POST_SUBMITTED_ON', $this->formatDate( $config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p') , $post->created)); ?>
			</div>
			<ul class="discuss-brief small reset-ul float-li clearfix">
				<li><?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' );?> <a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index&category_id=' . $activeCategory->id );?>"><?php echo $activeCategory->getTitle();?></a></li>
			</ul>
		</div>
	</div>
</div>
<?php endif; ?>

<div id="dc_post-protected">
	<div class="protected-title fwb mb-5 pt-20"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD_FORM_TITLE' );?></div>
	<p class="small"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD_FORM_TIPS' ); ?></p>
	<form name="discussion-protected" action="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&controller=posts&task=setPassword' );?>" method="post">
		<div class="form-row discuss-password">
			<!--<label for="password-post"><?php echo JText::_( 'COM_EASYDISCUSS_ENTER_PASSWORD' ); ?></label>-->
			<input type="password" name="discusspassword" id="password-post" class="input width-300" autocomplete="off" />
			
			<div class="mt-5 mb-5">
			<input type="submit" value="<?php echo JText::_( 'COM_EASYDISCUSS_ENTER_BUTTON' );?>" class="button" />
			</div>
			<input type="hidden" name="id" value="<?php echo $post->id;?>" />
			<input type="hidden" name="return" value="<?php echo base64_encode( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id ); ?>" />
		</div>
	</form>
</div>

<?php echo (isset($googleAdsense)) ? $googleAdsense->beforereplies : ''; ?>
<?php echo (isset($googleAdsense)) ? $googleAdsense->footer : ''; ?>