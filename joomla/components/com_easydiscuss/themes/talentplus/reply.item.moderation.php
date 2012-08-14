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
DiscussHelper::getHelper( 'vote' );
?>
<?php if ( !empty( $replies ) ) : ?>
	<?php foreach ( $replies as $reply ) : ?>
		<a name="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $reply->id;?>">&nbsp;</a>
		<li id="dc_reply_container_<?php echo $reply->id;?>">
			<div id="reply_minimize_msg_<?php echo $reply->id;?>" class="dc_alert msg_in" style="display:none;" width="100%">
				<?php if( $reply->minimize ){ ?>
					<?php echo JText::_('COM_EASYDISCUSS_REPLY_MINIMIZE');?>
				<?php } else { ?>
					<?php echo JText::_('COM_EASYDISCUSS_MINIMIZED_REPLY');?>
				<?php } ?>
				<a href="javascript:void(0);" class="dc_expand float-r" onclick="discuss.reply.maximize('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_SHOW');?></a>
			</div>
			<div id="dc_reply_<?php echo $reply->id; ?>" class="clearfix">
				<?php if ($config->get( 'main_allowvote' )) : ?>
				<?php echo DiscussVoteHelper::getHTML( $reply, array('isMainLocked' => $isMainLocked, 'my' => $system->my, 'config' => $config) ); ?>
				<?php endif; ?>


				<div class="discuss-reply">
					<div id="reports-msg-<?php echo $reply->id; ?>">
						<div class="msg_in"></div>
					</div>

					<?php /** respond avatar **/ ?>
					<?php if($reply->user->id != 0) : ?>
					<a href="<?php echo $reply->user->getLink(); ?>" class="avatar float-r ml-10">
						<img title="<?php echo $reply->user->getName();?>" src="<?php echo $reply->user->getAvatar();?>" width="40" height="40" class="avatar" />
					</a>
					<?php else : ?>
					<div class="avatar float-r">
						<img src="<?php echo $reply->user->getAvatar();?>" width="48" class="avatar" />
					</div>
					<?php endif; ?>

					<div class="respond-content">

						<?php /** respond authors **/ ?>
						<div class="respond-author small mb-5">
							<span>
							<?php if($reply->user->id != 0) : ?>
								<?php echo JText::sprintf('COM_EASYDISCUSS_REPLIED_BY', $reply->user->getLink() , $reply->user->getName(), $reply->user->getName()); ?>
							<?php else : ?>
								<?php if( $reply->user_type == 'twitter' ): ?>
									<?php echo JText::sprintf('COM_EASYDISCUSS_ENTRY_REPLIED_BY_GUEST_TWITTER', '<span class="dc_ico twitter-icon"><a href="http://twitter.com/' . $reply->poster_name . '" target="_blank">@' . $reply->poster_name . '</a></span>' ); ?>
								<?php else: ?>
									<?php echo JText::sprintf('COM_EASYDISCUSS_ENTRY_REPLIED_BY_GUEST', $reply->poster_name); ?>
								<?php endif; ?>
							<?php endif; ?>

							<?php echo JText::sprintf('COM_EASYDISCUSS_POST_SUBMITTED_ON', $this->formatDate( $system->config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p'), $reply->created)); ?>

							<?php if( $reply->user_type == 'twitter' ): ?>
								<span class="via_twitter"><?php echo JText::_( 'COM_EASYDISCUSS_ENTRY_POSTED_VIA_TWITTER' );?></span>
							<?php endif; ?>
							</span>

							<?php /** respond reporting **/ ?>
							<?php if($system->my->id != '0' && $system->config->get('main_report', 0)) : ?>
								<b>&middot;</b>
								<a href="javascript:void(0)" onclick="discuss.reports.add('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_REPORT_ABUSE'); ?></a>
							<?php endif; ?>
							<?php if ( $isMine || $isAdmin || $system->acl->allowed('edit_reply', '0') ) : ?>
								<b>&middot;</b>
								<a class="post_edit_link" href="javascript:void(0);" onclick="discuss.post.editReply('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_REPLIES_EDIT'); ?></a>
							<?php endif; ?>
							<?php if($canDeleteReply) : ?>
									<b>&middot;</b>
									<a class="post_delete_link" href="javascript:void(0);" onclick="discuss.post.del('<?php echo $reply->id; ?>', 'reply' , '<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $reply->parent_id );?>');" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>"><?php echo JText::_('COM_EASYDISCUSS_REPLIES_DELETE'); ?></a>
							<?php endif; ?>
						</div>

						<?php /** Show respond text **/ ?>
						<div id="post_content_<?php echo $reply->id; ?>" class="respond-text">
							<?php echo $reply->content; ?>

							<?php
							// Get fields output if necessary
							echo $this->getFieldHTML( false , $reply );
							?>
						</div>
					</div>
					<?php /** End .respond-content **/ ?>

					<?php echo $this->set( 'reply' , $reply ); ?>
					<?php echo $this->loadTemplate( 'reply.edit.php' ); ?>

					<div id="reply-notification-<?php echo $reply->id; ?>">
						<div class="msg_in"></div>
					</div>


					<div class="respond-voters small mt-10">
						<span id="dc_reply_total_votes_<?php echo $reply->id; ?>">
							<?php if( $reply->total_vote_cnt > 0 ): ?>
							<?php $noun = ($reply->total_vote_cnt=='1')? 'SINGULAR' : 'PLURAL'; echo JText::sprintf('COM_EASYDISCUSS_VOTES_'.$noun, $reply->total_vote_cnt); ?>
							<?php endif; ?>
						</span>
						<span id="dc_reply_voters_<?php echo $reply->id; ?>">
							<?php
							if(!empty($reply->total_vote_cnt))
							{
								echo JText::sprintf('COM_EASYDISCUSS_VOTES_BY', $reply->voters);

								if($reply->shownVoterCount < $reply->total_vote_cnt)
								{
							?>
									[<a href="javascript:void(0);" onclick="disjax.load('post', 'getMoreVoters', '<?php echo $reply->id; ?>', '10');"><?php echo JText::_('COM_EASYDISCUSS_MORE'); ?></a>]
							<?php
								}
							}
							?>
						</span>
					</div>
					<?php /** End .respond-voters **/ ?>


					<?php
					/**
					 * ----------------------------------------------------------------------------------------------------------
					 * Discussion's likes & comment links
					 * ----------------------------------------------------------------------------------------------------------
					 */
					?>
					<div class="respond-micro clearfull small mt-5">
						<a href="javascript:void(0);" class="hide-respond float-r" onclick="discuss.reply.minimize('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_HIDE'); ?></a>
						<span id="dc_likes">
							<span class="likes-container" id="likes-container-<?php echo $reply->id;?>"><?php echo $reply->likesAuthor;?></span>
							<?php if($system->my->id != 0) : ?>
							<span class="likes" id="likes-button-<?php echo $reply->id;?>"<?php echo $reply->islock ? ' style="display: none;"' : '';?>>
								<?php if(empty($reply->isLike)) : ?>
									<a href="javascript:void(0);" onclick="discuss.post.likes('<?php echo $reply->id; ?>', '1', '0');" class="disucss-like"><?php echo JText::_('COM_EASYDISCUSS_LIKES');?></a>
								<?php else : ?>
									<b>&middot;</b>
									<a href="javascript:void(0);" onclick="discuss.post.likes('<?php echo $reply->id; ?>', '0', '<?php echo $reply->isLike;?>');" class="discuss-unlike"><?php echo JText::_('COM_EASYDISCUSS_UNLIKE');?></a>
								<?php endif; ?>
							</span>
							<?php endif; ?>
						</span>

						<?php if($system->my->id != 0) : ?>
							<?php if($system->config->get('main_comment', 1)) : ?>
							<span id="comments-button-<?php echo $reply->id;?>" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>">
								<b>&middot;</b>
								<a href="javascript:void(0);" onclick="discuss.comment.add('<?php echo $reply->id; ?>');" class="discuss-comment"><?php echo JText::_('COM_EASYDISCUSS_ADD_COMMENT');?></a>
							</span>
							<?php endif; ?>
						<?php endif; ?>
						<?php if( !$question->isresolve && ( $system->acl->allowed('mark_answered', '0') || $isAdmin || $isMine) ) : ?>
							<span id="accept-button-<?php echo $reply->id;?>" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>">
								<b>&middot;</b>
								<a href="javascript:void(0);" onclick="discuss.reply.accept('<?php echo $reply->id; ?>');" class="discuss-accept"><?php echo JText::_('COM_EASYDISCUSS_REPLY_ACCEPT');?></a>
							</span>
						<?php else: ?>
							<?php if( $reply->answered && ( $system->acl->allowed('mark_answered', '0') || $isAdmin || $isMine) ) : ?>
								<span id="reject-button-<?php echo $reply->id;?>" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>">
									<b>&middot;</b>
									<a href="javascript:void(0);" onclick="discuss.reply.reject('<?php echo $reply->id; ?>');" class="discuss-reject"><?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?></a>
								</span>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<div id="comment-notification-<?php echo $reply->id; ?>">
						<div class="msg_in" style="background-color:#ffeeee;border:1px solid #CD8C8C;padding: 5px"><?php echo JText::_( 'COM_EASYDISCUSS_REPLY_UNDER_MODERATE' ); ?></div>
					</div>

					<!-- comment form here -->
					<div class="small comment-container" id="comment-action-container-<?php echo $reply->id;?>" style="display:none;"></div>
					<div id="report-container-<?php echo $reply->id; ?>" style="display:none;"></div>

					<!-- comments -->
					<?php if($system->config->get('main_comment', 1)) : ?>
					<ul class="respond-comments reset-ul mt-10" id="comments-wrapper-<?php echo $reply->id;?>" style="display: <?php echo (empty($reply->comments)) ? 'none' : 'block';?>;">
						<?php echo (! empty($reply->comments)) ? $reply->comments : '';?>
					</ul>
					<?php endif; ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>
<?php endif; ?>
