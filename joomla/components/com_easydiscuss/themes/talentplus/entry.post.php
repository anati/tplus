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
<?php
	/** Show post title **/
	$className  = 'contentheading';

	if($post->islock)
	{
		$className  .= ' locked';
	}

	if($post->isresolve)
	{
		$className  .= ' resolve';
	}

	$view			= JRequest::getString('view', 'index');
?>
<div id="dc_main_notifications"><div class="msg_in"></div></div>

<div class="discuss-content">
	<?php if($post->featured == 1) : ?>
		<div class="ribbon-featured in-block pos-r mb-15 float-l">
			<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' ); ?>
			<i class="pos-a"></i>
		</div>
		<div class="clear"></div>
	<?php endif; ?>

	<div class="discuss-head">
		<h2 id="title_<?php echo $post->id; ?>" class="discuss-title reset-h mb-15 fwb"><?php echo $post->title; ?></h2>

		<div class="discuss-meta clearfix">
			<?php if ( ($isMine || $isAdmin || $acl->allowed('edit_question', '0') || $canDeletePost || $acl->allowed('lock_discussion', '0') ) && !$isModerate) : ?>
			<div class="discuss-admin float-l pos-r mr-5">
				<a href="javascript:void(0);" class="ir">#</a>
				<ul class="discuss-control reset-ul fs-11 pos-a">
					<?php if ( $acl->allowed('edit_question', '0') || $isAdmin || $isMine ){ ?>
					<li>
						<a class="control-edit" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=ask&id='.$post->id );?>">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_EDIT'); ?>
						</a>
					</li>
					<?php } ?>

					<?php if($isAdmin){ ?>
					<li>
						<a class="control-feature" href="javascript:void(0);" onclick="discuss.post.featured('<?php echo $post->id; ?>', '1');" style="<?php echo ($post->featured) ? 'display:none' : '';?>">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_FEATURE_THIS');?>
						</a>
						<a class="control-unfeature" href="javascript:void(0);" onclick="discuss.post.featured('<?php echo $post->id; ?>', '0');" style="<?php echo ($post->featured) ? '' : 'display:none';?>">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNFEATURE_THIS');?>
						</a>
					</li>
					<?php } ?>

					<?php if( $canDeletePost ){ ?>
					<li>
						<a class="control-delete" href="javascript:void(0);" onclick="discuss.post.del('<?php echo $post->id; ?>', 'post' , '<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' );?>' );">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_DELETE'); ?>
						</a>
					</li>
					<?php } ?>

					<?php if( $isMine || $isAdmin ){ ?>
					<li>
						<a id="post_unresolve_link" href="javascript:void(0);" onclick="discuss.post.unresolve('<?php echo $post->id; ?>');" style="<?php echo $post->isresolve == 0 ? 'display: none;' : ''; ?>">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_UNRESOLVED'); ?>
						</a>
						<a id="post_resolve_link" href="javascript:void(0);" onclick="discuss.post.resolve('<?php echo $post->id; ?>');" style="<?php echo $post->isresolve == 1 ? 'display: none;' : ''; ?>">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_RESOLVED'); ?>
						</a>
					</li>
					<?php } ?>

					<?php if ( $acl->allowed('lock_discussion', '0') || $isAdmin  ){ ?>
					<li>
						<a id="post_unlock_link" href="javascript:void(0);" onclick="discuss.post.unlock('<?php echo $post->id; ?>');" style="<?php echo $post->islock == 0 ? 'display: none;' : ''; ?>">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNLOCK'); ?>
						</a>
						<a id="post_lock_link" href="javascript:void(0);" onclick="discuss.post.lock('<?php echo $post->id; ?>');" style="<?php echo $post->islock == 1 ? 'display: none;' : ''; ?>">
							<?php echo JText::_('COM_EASYDISCUSS_ENTRY_LOCK'); ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<?php endif; ?>
			
			<?php
			if(empty($post->user->id))
			{
			?>
				<a class="avatar float-l mr-10" href="javascript:void(0);">
					<img src="<?php echo $creator->getAvatar(); ?>" class="avatar small" />
				</a>
			<?php
			}
			else
			{
			?>
				<a class="avatar float-l mr-10" href="<?php echo  $creator->getLink(); ?>">
					<img src="<?php echo $creator->getAvatar(); ?>" class="avatar small" title="<?php echo $this->escape( $post->user->name ); ?>" />
				</a>
			<?php
			}
			?>
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

				<?php if($my->id != '0' && $config->get('main_report', 0) && !$isMine ) : ?>
					<li>
					<span class="reports" id="report-<?php echo $post->id; ?>">
						<a href="javascript:void(0)" onclick="discuss.reports.add('<?php echo $post->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_REPORT_ABUSE'); ?></a>
					</span>
					<div id="report-container-<?php echo $post->id; ?>" style="display:none;"></div>
					</li>
				<?php endif; ?>

				<?php if( !$isMine && $system->config->get( 'main_postsubscription', 0)  ){ ?>
				<li>
					<?php echo DiscussHelper::getSubscriptionHTML($system->my->id, $post->id, 'post'); ?>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div id="post_content_<?php echo $post->id; ?>" class="discuss-content mt-10 mb-10">
		<?php if ($config->get( 'main_allowvote' ) && $config->get( 'main_allowquestionvote' )) : ?>
			<?php DiscussHelper::getHelper( 'vote' ); ?>
			<?php echo DiscussVoteHelper::getHTML( $post, array('my' => $system->my, 'config' => $config, 'tmpl' => 'vote.post.php') ); ?>
		<?php endif; ?>

		<?php echo DiscussHelper::showSocialButtons( $post, 'vertical' ); ?>

		<?php echo $post->content; ?>

		<div class="clearfix"></div>
		<?php
		// Get fields output if necessary
		echo $this->getFieldHTML( true , $post );
		?>
	</div>


	<?php if( $polls ){ ?>
	<div class="discuss-poll mt-15">
		<?php echo $this->loadTemplate( 'poll.item.php' );?>
	</div>
	<?php }?>

	<?php echo DiscussHelper::showSocialButtons( $post, 'horizontal' ); ?>

	<?php if( $system->config->get( 'main_tags' ) ){ ?>
	<div class="discuss-tags fs-11 mt-15">
		<?php if ( !empty( $tags ) ) { ?>
			<span class="tag-label"><strong><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?>:</strong></span>
			<?php $tagCount = count( $tags); $x=1;?>
			<?php foreach( $tags as $tag ){ ?>
				<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=tags&id=' . $tag->id); ?>">#<?php echo $tag->title; ?></a>
			<?php } ?>
		<?php } ?>
	</div>
	<?php } ?>

	<div class="clearfix"></div>

	<div class="discuss-like clearfull mt-20">
		<?php
		if( $post->isresolve )
		{
		?>
		<span class="discuss-status float-r has-tip atr0 ml-5">
			<?php if( $isMine || $isAdmin ) { ?>
			<a id="post_unresolve_link" href="javascript:void(0);" onclick="discuss.post.unresolve('<?php echo $post->id; ?>');Foundry(this).parents('.discuss-status').hide();" style="<?php echo $post->isresolve == 0 ? 'display: none;' : ''; ?>" class="resolved-button float-r">
				<?php echo JText::_('COM_EASYDISCUSS_RESOLVED'); ?>
			</a>
			<?php } else { ?>
			<span class="resolved-button float-r">
				<?php echo JText::_('COM_EASYDISCUSS_RESOLVED'); ?>
			</span>
			<?php } ?>
			<div class="basic-tip">
				<i class="ico btip"></i>
				<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_RESOLVED' );?>
				<?php if( $isMine || $isAdmin ) { ?>
				<?php 	echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_UNRESOLVED'); ?>?
				<?php } ?>
			</div>
		</span>
		<?php
		}
		?>

		<?php
		if( $post->islock )
		{
		?>
		<span class="discuss-status float-r has-tip atr0">
			<?php if( $isAdmin ) { ?>
			<a id="post_unlock_link" href="javascript:void(0);" onclick="discuss.post.unlock('<?php echo $post->id; ?>');Foundry(this).parents('.discuss-status').hide();" style="<?php echo $post->islock == 0 ? 'display: none;' : ''; ?>" class="locked-button float-r">
				<?php echo JText::_('COM_EASYDISCUSS_LOCKED'); ?>
			</a>
			<?php } else { ?>
			<span class="locked-button float-r">
				<?php echo JText::_('COM_EASYDISCUSS_LOCKED'); ?>
			</span>
			<?php }?>
			<div class="basic-tip">
				<i class="ico btip"></i>
				<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_LOCKED' );?>
				<?php if( $isAdmin ) { ?>
				<?php 	echo JText::_('COM_EASYDISCUSS_ENTRY_UNLOCK'); ?>?
				<?php }?>
			</div>
		</span>
		<?php
		}
		?>

		<?php if( ( ($my->id != 0 && !$isModerate) || (!empty($post->likesAuthor) ) ) && $system->config->get( 'main_likes_discussions' ) ) : ?>
		<span class="like-button float-l mr-10" id="likes-button-<?php echo $post->id;?>">
			<?php if($my->id != 0 && !$isModerate && !$post->islock) : ?>
			<?php 	if(empty($post->isLike)) : ?>
				<a href="javascript:void(0);" onclick="discuss.post.likes('<?php echo $post->id; ?>', '1', '0');" class="like">
					<?php echo JText::_('COM_EASYDISCUSS_LIKES');?>
				</a>
			<?php 	else : ?>
				<a href="javascript:void(0);" onclick="discuss.post.likes('<?php echo $post->id; ?>', '0', '<?php echo $post->isLike;?>');" class="unlike">
					<?php echo JText::_('COM_EASYDISCUSS_UNLIKE');?>
				</a>
			<?php 	endif; ?>
			<?php else : ?>
				<a href="javascript:void(0);" class="off">&nbsp;</a>
			<?php endif; ?>
		</span>

		<span class="like-container float-l<?php echo empty( $post->likesAuthor ) ? ' empty-likes' : '';?>" id="likes-container-<?php echo $post->id;?>"><i></i><?php echo (empty($post->likesAuthor)) ? '' : $post->likesAuthor;?></span>
		<?php endif; ?>
	</div>

	<?php if ( $creator->getSignature() ) { ?>
	<div class="discuss-signature mt-10 pt-5 bt-sd">
		<?php echo $creator->getSignature();?>
	</div>
	<?php } ?>
	
	<div id="report-container-<?php echo $post->id; ?>" style="display:none;"></div>
</div>

