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
<li class="<?php echo $post->isresolve == 1 ? 'resolve ' : ''; ?><?php echo (DiscussHelper::isMine( $post->user->id )) ? 'mypost' : ''; ?>">
<div class="clearfull">
	<div class="discuss-statistic float-r">
		<div class="this-reply float-l">
			<a class="<?php echo !empty($post->reply) ? 'link-box replied' : 'link-box';?><?php echo ($post->isresolve) ? ' resolved' : '';?>" href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id . '#reply'); ?>">
				<div class="this-total tac"><?php echo $replies = !empty( $post->reply ) ? $post->totalreplies : 0; ?></div>
				<div class="this-text tac"><?php echo $this->getNouns('COM_EASYDISCUSS_REPLIES', $replies); ?></div>
			</a>
		</div>

		<div class="this-view float-l">
			<div class="this-total tac"><?php echo $post->hits; ?></div>
			<div class="this-text tac"><?php echo $this->getNouns('COM_EASYDISCUSS_VIEWS', $post->hits); ?></div>
		</div>
		<div class="this-vote float-l ml-5">
			<div class="this-total tac">
				<?php if( $post->sum_totalvote > 0 ) { ?><span style="font-size:10px;font-weight:normal">+</span><?php } ?> <?php echo $post->sum_totalvote; ?>
			</div>
			<div class="this-text tac"><?php echo $this->getNouns('COM_EASYDISCUSS_POST_TOTAL_VOTES', $post->sum_totalvote); ?></div>
		</div>
	</div><!--end: .discuss-statistic-->

	<?php if(empty($post->user->id)) { ?>
	<div class="avatar asker float-l has-tip">
		<img src="<?php echo $post->user->getAvatar(); ?>" class="avatar small" />
	</div>
	<?php
	}
	else
	{
	?>
	<div class="avatar asker float-l">
		<a class="avatar float-l has-tip" href="<?php echo  $post->user->getLink(); ?>">
			<img src="<?php echo $post->user->getAvatar(); ?>" class="avatar small" />
		</a>
		<?php if ($system->config->get( 'main_ranking' )) : ?>
		<div class="user-graph float-l width-full">
			<div class="rank-bar mini" title="<?php echo DiscussHelper::getUserRanks( $post->user->id ); ?>">
				<div class="rank-progress" style="width: <?php echo DiscussHelper::getUserRankScore( $post->user->id ); ?>%" ></div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<?php } ?>

	<div class="discuss-story">
		<?php
			$permalink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);
			$isNew		= ($post->isnew) ? ' class="new"' : '';
			$title		= $post->title;
			
			$introtext  = '';
			if( !empty( $post->password ) && !DiscussHelper::hasPassword( $post ) )
			{
				$introtext	= $post->content; //display password input form.
			}
			else
			{
				$introtext	= preg_replace( '/\s+/', ' ', strip_tags(Parser::bbcode($post->content)) ); // clean it to 1 liner
				$introtext	= JString::substr($introtext, 0, $system->config->get( 'layout_introtextlength' ));
			}
		?>
		<div class="discuss-intro ln-1 mb-5">
			<?php if($post->isnew) : ?>
			<span class="discuss-new float-l br-2 has-tip">
				<?php echo JText::_('COM_EASYDISCUSS_STAT_NEW'); ?>
			</span>
			<?php endif; ?>
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
			
		</div>

		<div class="discuss-pack">
			<?php if($post->isresolve == 1) : ?>
			<div class="discuss-resolved has-tip br-2 float-l mr-5">
				
				<span class="basic-tip" style="text-indent:0;left:-2px">
		            <i class="ico btip"></i>
		            <?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' ); ?>
	        	</span>
			</div>
			<?php endif; ?>
			<div class="discuss-title">
				<a href="<?php echo $permalink; ?>" <?php echo $isNew; ?>><?php echo $title; ?></a>
			</div>
			<?php if ($system->config->get( 'layout_enableintrotext' )) : ?>
			<div class="discuss-introtext mt-5">
				<?php echo $introtext; ?>
			</div>
			<?php endif; ?>

			<?php 
				if( $system->config->get( 'main_tags' ) ){
					if( $post->tags )
					{ 
			?>
			<div class="discuss-tags fs-11 mt-5">
				<?php
						for( $i = 0; $i < count( $post->tags ); $i++ )
						{
							$tag	= $post->tags[ $i ];
				?>
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id); ?>">#<?php echo $tag->title; ?></a>
				<?php
						}
				?>
			</div>
			<?php
					} 
				} 
			?>
		</div>
		
		<div class="discuss-action-options ln-1 small fs-11 mt-5 clearfull">
			<?php if ( !empty( $post->reply ) ) { ?>
				<span class="small fs-11 float-r">
			    <span class="float-l"><?php echo JText::_('COM_EASYDISCUSS_LAST_REPLIED_BY');?> :</span>
				<?php if ( $post->reply->id != 0 ) { ?>
					<a href="<?php echo $post->reply->getLink();?>" class="avatar has-tip atr0 float-l">
						<img src="<?php echo $post->reply->getAvatar(); ?>" class="avatar mini ml-5" />
						<span class="basic-tip">
				            <i class="ico btip"></i>
				            <?php echo $post->reply->getName(); ?>
				        </span>
					</a>
				<?php } else { ?>
				    <div class="avatar has-tip float-l">
						<img src="<?php echo $post->reply->getAvatar(); ?>" class="avatar mini ml-5" />
						<span class="basic-tip">
				            <i class="ico btip"></i>
				            <?php echo $post->reply->poster_name; ?>
				        </span>
			        </div>
				<?php } ?>
				</span>
			<?php } ?>
					
			<span class="discuss-clock<?php if( $post->islock ) { ?> locked has-tip<?php } ?>">
				<?php echo $post->duration; ?>
				<?php if( $post->islock ) { ?>
				<span class="basic-tip">
		            <i class="ico btip"></i>
		            <?php echo JText::_( 'COM_EASYDISCUSS_LOCKED' );?>
	        	</span>
	        	<?php } ?>
			</span>

			<?php
				if( $post->polls && !$post->attachments )
				{ 
			?>
				<span class="with-polls"><?php echo JText::_( 'COM_EASYDISCUSS_WITH_POLLS' );?></span>
			<?php
				}  
				else if( $post->attachments && !$post->polls )
				{ 
			?>
				<span class="with-attachments"><?php echo JText::_( 'COM_EASYDISCUSS_WITH_ATTACHMENTS' );?></span>
			<?php 
				}
				else if( $post->polls && $post->attachments )
				{ 
			?>
				<span class="with-polls-attachments"><?php echo JText::_( 'COM_EASYDISCUSS_WITH_POLLS_AND_ATTACHMENTS' );?></span>
			<?php } ?>
		</div>

	</div><!--end: .dc_topic-->

</div>
</li>
<?php endforeach; ?>
<?php else: ?>
<li>
	<div class="no-discussion pa-15 bg-f5 b-sd tac" style="padding:50px 0"><?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND') ?></div>
</li>
<?php endif; ?>
