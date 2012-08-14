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
<?php if ( !empty( $acceptedReply ) ) : ?>
		<li id="dc_reply_container_<?php echo $acceptedReply->id;?>">
			<div id="dc_reply_<?php echo $acceptedReply->id; ?>" class="clearfix">
				<?php if ($config->get( 'main_allowvote' )) : ?>
				<?php echo DiscussVoteHelper::getHTML( $acceptedReply, array('isMainLocked' => $isMainLocked, 'my' => $system->my, 'config' => $config) ); ?>
				<?php endif; ?>

				<div class="discuss-reply">
					<div id="reports-msg-<?php echo $acceptedReply->id; ?>">
						<div class="msg_in"></div>
					</div>
					<?php
					/**
					 * ----------------------------------------------------------------------------------------------------------
					 * Reply Avatar
					 * ----------------------------------------------------------------------------------------------------------
					 */
					?>
					<?php if($acceptedReply->user->id != 0) : ?>
					<a href="<?php echo $acceptedReply->user->getLink(); ?>" class="avatar float-r ml-10 mb-10">
						<img title="<?php echo $this->escape( $acceptedReply->user->getName() );?>" src="<?php echo $acceptedReply->user->getAvatar();?>" width="48" height="48" class="avatar" >
					</a>
					<?php else : ?>
					<div class="avatar float-r">
						<img src="<?php echo $acceptedReply->user->getAvatar();?>" width="48" class="avatar" >
					</div>
					<?php endif; ?>

					<?php
					/**
					 * ----------------------------------------------------------------------------------------------------------
					 * Reply's author
					 * ----------------------------------------------------------------------------------------------------------
					 */
					 //var_dump($reply);
					?>
					<div class="respond-author small mb-5">
						<span>
						<?php if($acceptedReply->user->id != 0) : ?>
							<?php echo JText::sprintf('COM_EASYDISCUSS_REPLIED_BY', $acceptedReply->user->getLink() ,$this->escape( $acceptedReply->user->getName() ), $this->escape( $acceptedReply->user->getName() ) ); ?>
						<?php else : ?>
							<?php if( $acceptedReply->user_type == 'twitter' ): ?>
								<?php echo JText::sprintf('COM_EASYDISCUSS_ENTRY_REPLIED_BY_GUEST_TWITTER', '<span class="dc_ico twitter-icon"><a href="http://twitter.com/' . $acceptedReply->poster_name . '" target="_blank">@' . $acceptedReply->poster_name . '</a></span>' ); ?>
							<?php else: ?>
								<?php echo JText::sprintf('COM_EASYDISCUSS_ENTRY_REPLIED_BY_GUEST', $acceptedReply->poster_name); ?>
							<?php endif; ?>
						<?php endif; ?>

						<?php echo JText::sprintf('COM_EASYDISCUSS_POST_SUBMITTED_ON', $this->formatDate( $config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p'), $acceptedReply->created)); ?>

						<?php if( $acceptedReply->user_type == 'twitter' ): ?>
							<span class="via_twitter"><?php echo JText::_( 'COM_EASYDISCUSS_ENTRY_POSTED_VIA_TWITTER' );?></span>
						<?php endif; ?>
						</span>

						<!-- report feature -->
						<?php if($system->my->id != '0' && $config->get('main_report', 0)) : ?>
							<b>&middot;</b>
							<a href="javascript:void(0)" onclick="discuss.reports.add('<?php echo $acceptedReply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_REPORT_ABUSE'); ?></a>
						<?php endif; ?>
						<?php if ( ( $system->my->id == $acceptedReply->user_id && $system->my->id != 0 ) || $isAdmin || $system->acl->allowed('edit_reply', '0') ) : ?>
							<b>&middot;</b>
							<a class="post_edit_link" href="javascript:void(0);" onclick="discuss.post.editReply('<?php echo $acceptedReply->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_REPLIES_EDIT'); ?></a>
						<?php endif; ?>
						<?php if($canDeleteReply) : ?>
							  <b>&middot;</b>
							<a class="post_delete_link" href="javascript:void(0);" onclick="discuss.post.del('<?php echo $acceptedReply->id; ?>', 'reply', '<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $acceptedReply->parent_id );?>');" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>"><?php echo JText::_('COM_EASYDISCUSS_REPLIES_DELETE'); ?></a>
						<?php endif; ?>
					</div>


					<?php /** Show post content **/ ?>
					<div id="post_content_<?php echo $acceptedReply->id; ?>" class="respond-text">
						<?php echo $acceptedReply->content; ?>
						<?php
						// Get fields output if necessary
						echo $this->getFieldHTML( false , $acceptedReply );
						?>
						<p class="respond-voters small">
							<span id="dc_reply_total_votes_<?php echo $acceptedReply->id; ?>">
								<?php if( $acceptedReply->total_vote_cnt > 0 ): ?>
								<?php $noun = ($acceptedReply->total_vote_cnt=='1')? 'SINGULAR' : 'PLURAL'; echo JText::sprintf('COM_EASYDISCUSS_VOTES_'.$noun, $acceptedReply->total_vote_cnt); ?>
								<?php endif; ?>
							</span>
							<span id="dc_reply_voters_<?php echo $acceptedReply->id; ?>">
								<?php
								if(!empty($acceptedReply->total_vote_cnt))
								{
									echo JText::sprintf('COM_EASYDISCUSS_VOTES_BY', $acceptedReply->voters);

									if($acceptedReply->shownVoterCount < $acceptedReply->total_vote_cnt)
									{
								?>
										[<a href="javascript:void(0);" onclick="disjax.load('post', 'getMoreVoters', '<?php echo $acceptedReply->id; ?>', '10');"><?php echo JText::_('COM_EASYDISCUSS_MORE'); ?></a>]
								<?php
									}
								}
								?>
							</span>
						</p>
					</div>
					<?php /** End show post content **/ ?>
					
					<?php echo $this->set( 'reply' , $acceptedReply ); ?>
					<?php echo $this->loadTemplate( 'reply.edit.php' ); ?>

					<?php if ( $isMine || $isAdmin ) : ?>
					<form id="form_edit_<?php echo $acceptedReply->id; ?>" name="dc_submit" action="#" method="post">
					<div id="post_content_edit_<?php echo $acceptedReply->id; ?>" class="respond-editor" style="display: none;">
						<div class="respond-markitup mr-10">
						<textarea id="reply_content_<?php echo $acceptedReply->id; ?>" name="reply_content_<?php echo $acceptedReply->id; ?>" class="textarea fullwidth"><?php echo $this->escape( $acceptedReply->content_raw ); ?></textarea>
						</div>

						<?php if(! empty($recaptcha)) { ?>
						<div id="reply_edit_antispam_<?php echo $acceptedReply->id;?>" class="respond-recaptcha">
							<?php echo $recaptcha; ?>
						</div>
						<?php } ?>

						<div class="respond-editor-end">
							<input type="button" id="mypost_save_<?php echo $acceptedReply->id; ?>" class="button" onclick="discuss.post.save('<?php echo $acceptedReply->id; ?>');" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT'); ?>" />
							<input type="button" id="mypost_cancel_<?php echo $acceptedReply->id; ?>" class="button" onclick="discuss.post.cancel('<?php echo $acceptedReply->id; ?>');" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?>" />
							<div class="float-r" style="display: block;" id="reply_edit_loading"></div>
						</div>
					</div>
					</form>
					<?php endif; ?>

					<div id="reply-notification-<?php echo $acceptedReply->id; ?>">
						<div class="msg_in"></div>
					</div>

					<?php
					/**
					 * ----------------------------------------------------------------------------------------------------------
					 * Discussion's likes & comment links
					 * ----------------------------------------------------------------------------------------------------------
					 */
					?>
					<div class="respond-micro clear-float small">
						<span id="dc_likes">
							<span class="likes-container" id="likes-container-<?php echo $acceptedReply->id;?>"><?php echo $acceptedReply->likesAuthor;?></span>
							<?php if($system->my->id != 0) : ?>
							<span class="likes" id="likes-button-<?php echo $acceptedReply->id;?>" style="display:<?php echo ($acceptedReply->islock) ? 'none' : '';?>">
								<?php if(empty($acceptedReply->isLike)) : ?>
									<a href="javascript:void(0);" onclick="discuss.post.likes('<?php echo $acceptedReply->id; ?>', '1', '0');" class="disucss-like"><?php echo JText::_('COM_EASYDISCUSS_LIKES');?></a>
								<?php else : ?>
									<b>&middot;</b>
									<a href="javascript:void(0);" onclick="discuss.post.likes('<?php echo $acceptedReply->id; ?>', '0', '<?php echo $acceptedReply->isLike;?>');" class="discuss-unlike"><?php echo JText::_('COM_EASYDISCUSS_UNLIKE');?></a>
								<?php endif; ?>
							</span>
							<?php endif; ?>
						</span>

						<?php if($system->my->id != 0) : ?>
							<?php if($config->get('main_comment', 1)) : ?>
							<span id="comments-button-<?php echo $acceptedReply->id;?>" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>">
								<b>&middot;</b>
								<a href="javascript:void(0);" onclick="discuss.comment.add('<?php echo $acceptedReply->id; ?>');" class="discuss-comment"><?php echo JText::_('COM_EASYDISCUSS_ADD_COMMENT');?></a>
							</span>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( $isMine || $isAdmin || $acl->allowed('mark_answered', '0')) : ?>
							<span id="reject-button-<?php echo $acceptedReply->id;?>" style="display:<?php echo ($isMainLocked) ? 'none' : '';?>">
								<b>&middot;</b>
								  <a href="javascript:void(0);" onclick="discuss.reply.reject('<?php echo $acceptedReply->id; ?>');" class="discuss-reject"><?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?></a>
							</span>
						<?php endif; ?>
					</div>


					<div id="comment-notification-<?php echo $acceptedReply->id; ?>">
						<div class="msg_in"></div>
					</div>

					<!-- comment form here -->
					<div class="small comment-container" id="comment-action-container-<?php echo $acceptedReply->id;?>" style="display:none;"></div>
					<div id="report-container-<?php echo $acceptedReply->id; ?>" style="display:none;"></div>

					<!-- comments -->
					<?php if($config->get('main_comment', 1)) : ?>
					<ul class="respond-comments reset-ul mt-10" id="comments-wrapper-<?php echo $acceptedReply->id;?>" style="display: <?php echo (empty($acceptedReply->comments)) ? 'none' : 'block';?>;">
					<?php echo (! empty($acceptedReply->comments)) ? $acceptedReply->comments : '';?>
					</ul>
					<?php endif; ?>

					<?php if( $acceptedReply->user->getSignature() != '' ){ ?>
					<div class="discuss-signature mt-10 pt-5 bt-sd">
						<?php echo $acceptedReply->user->getSignature(); ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</li>
<?php endif; ?>
