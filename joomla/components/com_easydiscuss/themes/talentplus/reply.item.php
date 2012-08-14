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
	<?php $i = 1; ?>
	<?php foreach ( $replies as $reply ) :?>
		<a name="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $reply->id;?>">&nbsp;</a>
		<li id="dc_reply_container_<?php echo $reply->id;?>" class="<?php echo DiscussHelper::userToClassname($reply->user->user, 'reply' ); ?>">
			<div id="reply_minimize_msg_<?php echo $reply->id;?>" class="reply-minimized" style="display:none;">
				<?php /** respond avatar **/ ?>
				<?php if($reply->user->id != 0) : ?>
				<a id="avatar-<?php echo $reply->id; ?>" href="<?php echo $reply->user->getLink(); ?>" class="avatar float-r">
					<img title="<?php echo $this->escape( $reply->user->getName() );?>" src="<?php echo $reply->user->getAvatar();?>" width="40" height="40" class="avatar" />
				</a>
				<?php else : ?>
				<div id="avatar-<?php echo $reply->id; ?>" class="avatar float-r">
					<img src="<?php echo $reply->user->getAvatar();?>" width="48" class="avatar" />
				</div>
				<?php endif; ?>

				<?php if( $reply->minimize ){ ?>
					<b><?php echo JText::_('COM_EASYDISCUSS_REPLY_MINIMIZE');?></b>
				<?php } else { ?>
					<b><?php echo JText::_('COM_EASYDISCUSS_MINIMIZED_REPLY');?></b>
				<?php } ?>
				<a href="javascript:void(0);" class="hide-respond float-r" onclick="discuss.reply.maximize('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_SHOW');?></a>
			</div>
			<div id="dc_reply_<?php echo $reply->id; ?>" class="clearfix">
				<?php if ($system->config->get( 'main_allowvote' )) : ?>
				<?php echo DiscussVoteHelper::getHTML( $reply, array('isMainLocked' => $isMainLocked, 'my' => $system->my, 'config' => $system->config) ); ?>
				<?php endif; ?>

				<div class="discuss-reply">
					<div id="reports-msg-<?php echo $reply->id; ?>">
						<div class="msg_in"></div>
					</div>

					<?php /** respond avatar **/ ?>
					<div id="avatar-<?php echo $reply->id; ?>" class="avatar respondee float-r ml-10">
					<?php if($reply->user->id != 0) : ?>
					
						<a id="avatar-<?php echo $reply->id; ?>" href="<?php echo $reply->user->getLink(); ?>" class="avatar respondee">
							<img title="<?php echo $this->escape( $reply->user->getName() );?>" src="<?php echo $reply->user->getAvatar();?>" class="avatar small" />
						</a>
						<?php if ($system->config->get( 'main_ranking' )) : ?>
						<div class="user-graph float-l width-full">
							<div class="rank-bar mini" title="<?php echo DiscussHelper::getUserRanks( $reply->user->id ); ?>">
								<div class="rank-progress" style="width: <?php echo DiscussHelper::getUserRankScore( $reply->user->id ); ?>%" ></div>
							</div>
						</div>
						<?php endif; ?>
					
					<?php else : ?>
					
						<img src="<?php echo $reply->user->getAvatar();?>" class="avatar small" />
					
					<?php endif; ?>
					</div>

					<div class="respond-content">

						<?php /** respond authors **/ ?>
						<div class="respond-author small mb-5 clearfull">
							<?php if ( ( $system->my->id == $reply->user_id && $system->my->id != 0 ) || $isAdmin || $system->acl->allowed('edit_reply', '0') || $canDeleteReply ){ ?>
							<div class="discuss-admin for-respond float-r pos-r">
								<a class="ir" href="javascript:void(0);">#</a>
								<ul class="discuss-control reset-ul fs-11 pos-a">
								<?php if ( ( $system->my->id == $reply->user_id && $system->my->id != 0 ) || $isAdmin || $system->acl->allowed('edit_reply', '0') ) : ?>
									<li class="post_edit_link">
										<a class="post_edit_link" href="javascript:void(0);" onclick="discuss.post.editReply('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_REPLIES_EDIT'); ?></a>
									</li>
								<?php endif; ?>

								<?php if($canDeleteReply) : ?>
									<li>
									<a class="post_delete_link" href="javascript:void(0);" onclick="discuss.post.del('<?php echo $reply->id; ?>', 'reply' , '<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $reply->parent_id );?>');" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>"><?php echo JText::_('COM_EASYDISCUSS_REPLIES_DELETE'); ?></a>
									</li>
								<?php endif; ?>
								</ul>
							</div>
							<?php } ?>

							<span>
							<?php if($reply->user->id != 0) : ?>
								<?php echo JText::sprintf('COM_EASYDISCUSS_REPLIED_BY', $reply->user->getLink() , $this->escape( $reply->user->getName() ), $this->escape( $reply->user->getName() ) ); ?>
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
							
							<b>&middot;</b>
							<a href="javascript:void(0);" onclick="discuss.reply.minimize('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_HIDE'); ?></a>

							<b>&middot;</b>
							<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $reply->parent_id) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $reply->id;?>" title="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK_TO'); ?>">#<?php echo $i; $i++; ?></a>

							<?php if($system->my->id != '0' && $system->config->get('main_report', 0)){ ?>
							<b>&middot;</b>
							<a href="javascript:void(0)" onclick="discuss.reports.add('<?php echo $reply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_REPORT_ABUSE'); ?></a>
							<?php } ?>
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


					<div class="respond-micro clearfull small mt-5">
						<?php if( $system->config->get( 'main_likes_replies' ) ){ ?>
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
						
						<?php } ?>

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
						<div class="msg_in"></div>
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

					<?php if( $reply->user->getSignature() != '' ){ ?>
					<div class="discuss-signature mt-10 pt-5 bt-sd">
						<?php echo $reply->user->getSignature(); ?>
					</div>
					<?php } ?>

				</div>
			</div>
		</li>

		<?php if($reply->minimize) : ?>
			<script language="javascript" type="text/javascript">
				discuss.reply.minimize('<?php echo $reply->id; ?>');
			</script>
		<?php endif; ?>
	<?php endforeach; ?>
<?php else: ?>
	<li class="no-replies"><?php echo JText::_( 'COM_EASYDISCUSS_NO_REPLIES_YET' );?></li>
<?php endif; ?>
