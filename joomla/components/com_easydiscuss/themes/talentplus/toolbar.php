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
<?php if( $user->id > 0 ){ ?>

<script type="text/javascript">
EasyDiscuss.ready(function()
{
	discuss.notifications.interval = <?php echo $system->config->get( 'main_notifications_interval' ) * 1000 ?>;
	discuss.notifications.startMonitor();
});
</script>

<?php } ?>

<?php if( $config->get('layout_headers') ){ ?>
<h1 class="rip"><?php echo $headers->title; ?></h1>
<p class="fs11 rip mt-5 mb-10"><?php echo $headers->desc; ?></p>
<?php } ?>

<?php if($config->get('layout_enabletoolbar')) : ?>
<div id="dc_toolbar" class="br-3">
	<ul class="discuss-toolbar reset-ul float-li clearfix">
		<?php if($config->get('layout_toolbardiscussion', 1)) : ?>
		<li class="to_discuss<?php echo $views->index;?>">
			<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=index'); ?>">
				<span class="ico go-discuss"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_HOME'); ?></span>
			</a>
			<div class="toolbar-note pos-a br-2">
				<i></i>
				<div>
					<b><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_DISCUSSIONS' );?></b>
					<div><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_DISCUSSIONS_DESC' );?></div>
				</div>
			</div>
		</li>
		<?php endif; ?>

		<?php if($config->get('layout_toolbartags', 1)) : ?>
		<li class="to_tags<?php echo $views->tags;?>">
			<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=tags'); ?>">
				<span class="ico go-tags"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS'); ?></span>
			</a>
			<div class="toolbar-note pos-a br-2">
				<i></i>
				<div>
					<b><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_TAGS' );?></b>
					<div><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_TAGS_DESC' );?></div>
				</div>
			</div>
		</li>
		<?php endif; ?>

		<?php if($config->get('layout_toolbarcategories', 1)) : ?>
		<li class="to_categories<?php echo $views->categories;?>">
			<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=categories'); ?>">
				<span class="ico go-category"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES'); ?></span>
			</a>
			<div class="toolbar-note pos-a br-2">
				<i></i>
				<div>
					<b><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_CATEGORIES' );?></b>
					<div><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_CATEGORIES_DESC' );?></div>
				</div>
			</div>
		</li>
		<?php endif; ?>

		<?php if($config->get('layout_toolbarusers', 1)) : ?>
		<li class="to_members<?php echo $views->users;?>">
			<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users'); ?>">
				<span class="ico go-members"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS'); ?></span>
			</a>
			<div class="toolbar-note pos-a br-2">
				<i></i>
				<div>
					<b><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_MEMBERS' );?></b>
					<div><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_MEMBERS_DESC' );?></div>
				</div>
			</div>
		</li>
		<?php endif; ?>

		<?php if( $config->get( 'layout_toolbarbadges' ) ){ ?>
		<li class="to_badges<?php echo $views->badges;?>">
			<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=badges'); ?>">
				<span class="ico go-badges"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES'); ?></span>
			</a>
			<div class="toolbar-note pos-a br-2">
				<i></i>
				<div>
					<b><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_BADGES' );?></b>
					<div><?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_BADGES_DESC' );?></div>
				</div>
			</div>
		</li>
		<?php } ?>

		<?php if($config->get('layout_toolbarsearch', 1)) : ?>
		<li class="to_search<?php echo $views->search;?>">
			<form name="discuss-search" method="GET" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=search'); ?>">
				<input type="text" onblur="if (this.value == '') {this.value = '<?php echo JText::_('COM_EASYDISCUSS_SEARCH_WITH_THREE_DOTS'); ?>';}" onfocus="if( this.value == '<?php echo JText::_('COM_EASYDISCUSS_SEARCH_WITH_THREE_DOTS'); ?>' ){ this.value =''; }" value="<?php echo ( !empty($query ) ) ? $this->escape( $query )  : JText::_('COM_EASYDISCUSS_SEARCH_WITH_THREE_DOTS'); ?>" name="query" class="dc_toolsearch">
				<input type="hidden" name="option" value="com_easydiscuss" />
				<input type="hidden" name="view" value="search" />
				<input type="hidden" name="Itemid" value="<?php echo DiscussRouter::getItemId('search'); ?>" />
			</form>
		</li>
		<?php endif; ?>

	<?php if( ($user->id != 0 && $acl->allowed('add_question', '0') ) || ( $user->id == 0 && $config->get('main_allowguestpostquestion', 0) ) ) { ?>

		<?php if($config->get('layout_toolbarcreate', 1)) : ?>
		<li class="to_create float-r<?php echo $views->create;?> not-login">
			<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=ask' . $category); ?>">
				<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION'); ?></span>
			</a>
		</li>
		<?php endif; ?>

	<?php } ?>

	<?php if( $user->id <= 0 && $system->config->get( 'layout_toolbarlogin') ){ ?>
		<li class="to_login float-r">
			<a href="javascript:void(0);" onclick="discuss.toolbar.login();">
				<span><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGIN'); ?></span>
			</a>
			<div class="toolbar-note pos-a br-2">
				<div class="toolbar-login">
					<form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
						<label class="float-l width-full rip" for="username">
							<a href="<?php echo DiscussHelper::getRegistrationLink();?>" class="float-r"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER' );?></a>
							<span class="trait"><?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' );?></span>
							<input type="text" alt="username" class="input text clear" name="username" id="username">
						</label>
						<label class="float-l width-full rip mt-10" for="passwd">
							<a href="<?php echo DiscussHelper::getResetPasswordLink();?>" class="float-r"><?php echo JText::_( 'COM_EASYDISCUSS_FORGOT_PASSWORD' );?></a>
							<span class="trait"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD' );?></span>
							<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
								<input type="password" id="passwd" class="input text clear" name="password" />
							<?php } else { ?>
								<input type="password" id="passwd" class="input text clear" name="passwd" />
							<?php } ?>
						</label>
						<div class="mt-10 mb-10 float-l width-full">
							<label class="remember float-l" for="remember">
								<input type="checkbox" class="rip" alt="<?php echo JText::_( 'COM_EASYDISCUSS_REMEMBER_ME' );?>" value="yes" name="remember" id="remember">
								<span><?php echo JText::_( 'COM_EASYDISCUSS_REMEMBER_ME' );?></span>
							</label>
							<button type="submit" class="button submit float-r"><?php echo JText::_( 'COM_EASYDISCUSS_LOGIN' );?></button>
						</div>
						<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
						<input type="hidden" value="com_users"  name="option">
						<input type="hidden" value="user.login" name="task">
						<input type="hidden" name="return" value="<?php echo $return; ?>" />
						<?php } else { ?>
						<input type="hidden" value="com_user"  name="option">
						<input type="hidden" value="login" name="task">
						<input type="hidden" name="return" value="<?php echo $return; ?>" />
						<?php } ?>
						<?php echo JHTML::_( 'form.token' ); ?>
					</form>
				</div>
			</div>
		</li>
        <li class="float-r">
            <a href="/index.php?option=com_user&view=register">
                <span>Register New Account</span>
            </a>
        </li>
	<?php } ?>
	<?php if( $user->id != 0 ) { ?>
		<?php if($user->id != 0 ) { ?>
		<li class="to_notification float-r">
			<a href="javascript:void(0);" onclick="discuss.notifications.load();">
				<span id="notification-count"<?php echo !$notifications ? ' class="empty-notification"' : '';?>><?php echo $notifications;?></span>
			</a>
			<div class="toolbar-note pos-a br-2" style="display:none;">
				<ul class="notify-items reset-ul" id="notification-items"></ul>
			</div>
		</li>
		<?php } ?>

		<?php if($config->get('layout_toolbarprofile', 1)) : ?>
		<li class="to_profile float-r<?php echo $views->profile;?>">
			<a href="<?php echo $system->profile->getLink(); ?>">
				<span class="ico go-profile"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_PROFILE'); ?></span>
			</a>
		</li>
		<?php endif; ?>

	<?php } ?>

	</ul>
</div>
<?php endif; ?>
<?php
	$msgObject	= DiscussHelper::getMessageQueue();

	if(! empty($msgObject))
	{
?>
	<div id="discuss-message" class="<?php echo $msgObject->type;?>">
		<div><?php echo $msgObject->message;?></div>
	</div>
<?php
	}
?>
