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
require_once( DISCUSS_HELPERS . DS . 'router.php' );

// Don't panic, this is only used in AJAX calls, that is why it doesn't have
// a UL

?>
<?php if ( !empty( $posts ) ) : ?>
<?php foreach( $posts as $post ) : ?>
<li class="<?php echo (DiscussHelper::isMine( $post->user->id )) ? 'mypost' : ''; ?>">
<div class="clearfull">
	<?php if(empty($post->user->id)) { ?>
	<div class="avatar asker float-l has-tip">
		<img src="<?php echo $post->user->getAvatar(); ?>" width="40" height="40" class="avatar" />
	</div>
	<?php
	}
	else
	{
	?>
	<div class="avatar asker float-l">
		<a class="avatar float-l has-tip" href="<?php echo  $post->user->getLink(); ?>">
			<img src="<?php echo $post->user->getAvatar(); ?>" width="42" class="avatar" />
		</a>
		<?php if ($system->config->get( 'main_ranking' )) : ?>
		<div class="user-graph float-l width-full mt-5">
			<div style="background:#5AA427;height:5px">
				<div style="width:<?php echo DiscussHelper::getUserRankScore( $post->user->id ); ?>%;background:#0F6EAC;height:5px"></div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php } ?>

	<div class="discuss-story">
		<div class="discuss-intro ln-1 mb-5">
			<?php
				if(empty($post->user->id))
				{
					echo '<b>' . $post->poster_name . '</b>' . ' - ' . JText::_('COM_EASYDISCUSS_GUEST');
				}
				else
				{
			?>
					<a href="<?php echo  $post->user->getLink(); ?>" class="fwb fc-in"><?php echo $post->user->getName(); ?></a>
			<?php
				}
			?>
			
			<span class="fc-99">
				&ndash;
				<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIZED_IN' );?>
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index&category_id=' . $post->category_id );?>" class="fwb fc-in">
					<?php echo JText::_( $post->category ); ?>
				</a>
			</span>
		</div>
		
		<?php
		    $permalink	= '';
		    if( $post->itemtype == 'posts' )
		    {
		    	$permalink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);
		    }
		    else if( $post->itemtype == 'replies' )
		    {
		        $permalink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id);
		    }
		    else if($post->itemtype == 'category' )
		    {
		        $permalink	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index&category_id=' . $post->id );
		    }
		
			$isNew		= ($post->isnew) ? ' class="new"' : '';
			$title		= $post->title;
		?>

		<div class="discuss-pack">
			<div class="discuss-title">
				<?php if( $post->itemtype == 'posts' || $post->itemtype == 'replies') : ?>
					<?php if($post->isnew) : ?>
					<span class="discuss-new float-l">
						<?php echo JText::_('COM_EASYDISCUSS_STAT_NEW'); ?>
					</span>
					<?php endif; ?>
				<?php endif; ?>
				<a href="<?php echo $permalink; ?>" <?php echo $isNew; ?>><span><?php echo $title; ?></span></a>
			</div>
			
			<?php if ($system->config->get( 'layout_enableintrotext' )) : ?>
			<div class="discuss-introtext"><?php echo $post->content; ?></div>
			<?php endif; ?>
		</div>

		<div class="discuss-meta small fs-11 mt-5">
			<?php if( $post->itemtype == 'posts' || $post->itemtype == 'replies') : ?>
			<span class="discuss-posttime"><?php echo $post->duration; ?></span>
			<?php endif; ?>
		</div>	
	</div><!--end: .dc_topic-->
</div>
</li>
<?php endforeach; ?>
<?php else: ?>
<li>
	<div class="msg_in dc_alert"><?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND') ?></div>
</li>
<?php endif; ?>
