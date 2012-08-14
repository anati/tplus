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
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>


<div id="dc_profile" class="mt-20">

	<div class="user-profile clearfix">
		<div class="user-sidebar float-l">
			<div class="user-avatar mb-15">
				<img src="<?php echo $profile->getAvatar(false);?>" width="160" class="avatar" />
			</div>

			<?php if( $system->my->id == $profile->id ){ ?>
			<div class="widget user-menu">
				<div class="widget-body">
					<ul class="user-menu reset-ul">

						<li>
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' );?>">
								<span><?php echo JText::_( 'COM_EASYDISCUSS_USER_EDIT_PROFILE');?></span>
							</a>
						</li>
						<li>
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=subscriptions' );?>">
								<span><?php echo JText::_( 'COM_EASYDISCUSS_USER_EDIT_SUBSCRIPTIONS');?></span>
							</a>
						</li>

					</ul>
				</div>
			</div>
			<?php } ?>

			<?php if ($config->get( 'main_ranking' )) : ?>
			<div class="widget user-rank">
				<div class="widget-head small"><?php echo JText::_('COM_EASYDISCUSS_RANK'); ?></div>
				<div class="widget-body">
					<span class="rank-value"><?php echo DiscussHelper::getUserRanks( $profile->id ); ?></span>
					<div class="rank-bar">
						<div class="rank-progress" style="width: <?php echo DiscussHelper::getUserRankScore( $profile->id ); ?>%"></div>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<div class="widget user-point">
				<div class="widget-head small"><?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?></div>
				<div class="widget-body">
					<span class="point-value mt-5"><?php echo $profile->getPoints(); ?></span>
					<small><?php echo JText::_('COM_EASYDISCUSS_POINTS'); ?></small>
				</div>
			</div>
			<?php echo $this->loadTemplate( 'users.achievements.list.php' ); ?>
		</div>
		<div class="user-content">

			<div class="user-name clearfull">
				<h2 class="user-name reset-h mr-5"><?php echo DiscussStringHelper::escape($profile->getName());?></h2>
				<?php if( $system->config->get( 'main_rss' ) ){ ?>
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $profile->id . '&format=feed' );?>" class="ir rss" title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_SUBSCRIBE_RSS' );?>"><?php echo JText::_( 'COM_EASYDISCUSS_USER_SUBSCRIBE_RSS' );?></a>
				<?php } ?>
			</div>

			<div class="user-intro mv-5"><?php echo $profile->get( 'description' );?></div>

			<?php if( $userparams->get( 'show_facebook' ) || $userparams->get( 'show_twitter' ) || $userparams->get( 'show_linkedin' ) ){ ?>
			<div class="user-social mv-10 pt-10 bt-dd">
				<ul class="user-social reset-ul float-li in-block">
					<?php if ($userparams->get( 'show_facebook' )) { ?>
					<li class="facebook"><a href="<?php echo DiscussStringHelper::escape($userparams->get( 'facebook' )); ?>" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?></a></li>
					<?php } ?>
					<?php if ($userparams->get( 'show_twitter' )) { ?>
					<li class="twitter"><a href="<?php echo DiscussStringHelper::escape($userparams->get( 'twitter' )); ?>" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></a></li>
					<?php } ?>
					<?php if ($userparams->get( 'show_linkedin' )) { ?>
					<li class="linkedin"><a href="<?php echo DiscussStringHelper::escape($userparams->get( 'linkedin' )); ?>" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?></a></li>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>

			<div class="user-brief small pv-10 mt-10 bt-sd ttu">
				<?php echo JText::_( 'COM_EASYDISCUSS_REGISTERED_ON' );?> - <?php echo $profile->getDateJoined(); ?>
				<b>&middot;</b>
				<?php echo JText::_( 'COM_EASYDISCUSS_LAST_SEEN_ON' );?> - <?php echo $profile->getLastOnline(); ?>
			</div>

			<?php if( !empty( $profile->latitude) && !empty( $profile->longitude) ){ ?>
			<div class="user-map mb-15 bg-f5 b-sc">
				<script type="text/javascript">
				EasyDiscuss.ready(function($){
					discuss.map.render( '<?php echo $this->escape($profile->location);?>' , '<?php echo $profile->latitude;?>' , '<?php echo $profile->longitude;?>' , 'user-map-area' );
				});
				</script>
				<div id="user-map-area" style="width: 100% !important;height: 130px !important;"></div>
			</div>
			<?php } ?>

			<div class="user-tab clearfull">
				<div class="user-tabs pos-r pt-5 mb-20">
					<ul class="list-tab reset-ul float-li bb-sc clearfix">
						<li <?php echo ( $viewType == 'user-post' ) ? ' class="active"' : ''; ?> >
							<a href="javascript:void(0);" onclick="discuss.user.tabs.show( this , 'user-post' , true );">
								<span><?php echo $profile->getNumTopicPosted(); ?></span>
								<b><?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_POSTED' ); ?></b>
							</a>
						</li>
						<li <?php echo ( $viewType == 'user-replies' ) ? ' class="active"' : ''; ?> >
							<a href="javascript:void(0);" onclick="discuss.user.tabs.show( this , 'user-replies' , true );">
								<span><?php echo $profile->getNumTopicAnswered(); ?></span>
								<b><?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSION_REPLIED' ); ?></b>
							</a>
						</li>
						<li <?php echo ( $viewType == 'user-tags' ) ? ' class="active"' : ''; ?> >
							<a href="javascript:void(0);" onclick="discuss.user.tabs.show( this , 'user-tags' , true );">
								<span><?php echo $profile->getTotalTags(); ?></span>
								<b><?php echo JText::_( 'COM_EASYDISCUSS_TAGS_CREATED' ); ?></b>
							</a>
						</li>
						<!--
						<li <?php echo ( $viewType == 'user-achievements' ) ? ' class="active"' : ''; ?> >
							<a href="javascript:void(0);" onclick="discuss.user.tabs.show( this , 'user-achievements' , true );">
								<span><?php echo $profile->getTotalBadges(); ?></span>
								<b><?php echo JText::_( 'COM_EASYDISCUSS_BADGES' ); ?></b>
							</a>
						</li>
						-->
					</ul>
				</div>
			</div>

			<div id="profile-loading" class="bg-f5 b-sd tac pa-15" style="display: none;"></div>

			<div id="user-post" class="user-post clearfull mb-15 tab-item" <?php echo ( $viewType != 'user-post' ) ? ' style="display:none"' : ''; ?> >
				<div class="section-body">
					<ul id="dc_list" class="discuss-index reset-ul">
						<?php echo $this->loadTemplate( 'main.item.php' );?>
					</ul>
					<?php echo $this->loadTemplate( 'pagination.php' ); ?>
				</div>
			</div><!-- end .user-post -->

			<div id="user-replies" class="user-replies clearfull mb-15 tab-item" <?php echo ( $viewType != 'user-replies' ) ? ' style="display:none"' : ''; ?> >
				<div class="section-body">
					<ul id="dc_list" class="discuss-index reset-ul">
						<?php echo $this->set( 'posts' , $replies ); ?>
						<?php echo $this->loadTemplate( 'main.item.php' );?>
					</ul>
					<?php echo $this->loadTemplate( 'pagination.php' ); ?>
				</div>
			</div><!-- end .user-replies -->

			<div id="user-tags" class="user-tags clearfull mb-15 tab-item" <?php echo ( $viewType != 'user-tags' ) ? ' style="display:none"' : ''; ?> >
				<div class="section-body">
					<ul id="discuss-tag-list" class="discuss-tag-list reset-ul float-li clearfull">
						<?php if ( !empty( $tagCloud ) ) : ?>
						<?php echo $this->loadTemplate( 'tags.item.php' );?>
						<?php endif; ?>
					</ul>
				</div>
			</div><!-- end .user-tags -->
		</div>
	</div>
</div>
<input id="profile-id" value="<?php echo $profile->id; ?>" type="hidden" />
