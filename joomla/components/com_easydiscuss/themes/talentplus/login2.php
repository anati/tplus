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
<h3><?php echo JText::_('COM_EASYDISCUSS_PLEASE_SELECT_A_USER_TYPE');  ?></h3>
<div id="usertype_status"></div>

<div id="usertype_pane_container">

	<ul id="usertype_pane_left" class="resetList">
        <?php if($config->get('main_allowguestpost')) { ?>
        <li id="usertype_guest">
            <a href="javascript:void(0); " onclick="discuss.login.showpane('guest'); "><?php echo JText::_('COM_EASYDISCUSS_GUEST');  ?></a>
        </li>
        <?php } ?>

        <li id="usertype_member">
            <a href="javascript:void(0); " onclick="discuss.login.showpane('member'); "><?php echo JText::_('COM_EASYDISCUSS_MEMBER');  ?></a>
        </li>

        <?php if($config->get('integration_twitter_enable') && ($config->get('integration_twitter_consumer_key') && $config->get('integration_twitter_consumer_secret_key'))) { ?>
        <li id="usertype_twitter">
            <a href="javascript:void(0); " onclick="discuss.login.showpane('twitter'); "><?php echo JText::_('COM_EASYDISCUSS_TWITTER');  ?></a>
        </li>
        <?php } ?>
	</ul>


	<div id="usertype_pane_right">
	<?php if($config->get('main_allowguestpost')) { ?>
		<div id="usertype_guest_pane" style="display:none;">
			<form action="<?php echo DiscussRouter::_( 'index.php', true);  ?>" method="" name="discuss-guest-login" id="discuss-guest-login">
    			<h1><?php echo JText::_('COM_EASYDISCUSS_GUEST_SIGN_IN'); ?></h1>
                <p id="discuss_usertype_guest_email">
        			<label for="discuss_usertype_guest_email"><?php echo JText::_( 'COM_EASYDISCUSS_GUEST_EMAIL' ); ?></label>
        			<input type="text" name="discuss_usertype_guest_email" id="discuss_usertype_guest_email" value="<?php echo empty($guest->email)? '':$guest->email; ?>" onkeyup="discuss.login.getGuestDefaultName(); ">
                </p>
                <p id="discuss_usertype_guest_name">
        			<label for="discuss_usertype_guest_name">Name</label>
        			<input type="text" name="discuss_usertype_guest_name" id="discuss_usertype_guest_name" value="<?php echo empty($guest->name)? '':$guest->name; ?>">
                </p>
                <div>
                    <input type="button" value="Reply" class="si_btn" id="edialog-reply" name="edialog-reply" onclick=""/>
                    <input type="button" value="Cancel" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
                </div>
			</form>
		</div>
	<?php } ?>


		<div id="usertype_member_pane" style="display:none;">
			<form action="<?php echo DiscussRouter::_( 'index.php', true);  ?>" method="post" name="member-form" id="member-form-login" >
			<h1><?php echo JText::_('COM_EASYDISCUSS_MEMBER_SIGN_IN'); ?></h1>
			<p><?php echo JText::_( 'COM_EASYDISCUSS_MEMBER_SIGN_IN_DESC' ); ?></p>
			<p id="member-form-login-username">
				<label for="discuss_member_username"><?php echo JText::_('COM_EASYDISCUSS_MEMBER_USERNAME') ?></label>
				<input id="discuss_usertype_member_username" type="text" name="discuss_usertype_member_username" class="inputbox" alt="username" size="18" />
			</p>
			<p id="member-form-login-password">
				<label for="discuss_member_passwd"><?php echo JText::_('COM_EASYDISCUSS_MEMBER_PASSWORD') ?></label></label>
				<input id="discuss_usertype_member_password" type="discuss_usertype_member_password" name="passwd" class="inputbox" size="18" alt="password" />
			</p>
            <div>
                <input type="button" value="Reply" class="si_btn" id="edialog-reply" name="edialog-reply" onclick=""/>
                <input type="button" value="Cancel" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
            </div>
			<input type="hidden" name="option" value="<?php echo DiscussHelper::getUserComponent(); ?>" />
			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
		</form>
		</div>

	<?php if($config->get('integration_twitter_enable') && ($config->get('integration_twitter_consumer_key') && $config->get('integration_twitter_consumer_secret_key'))) { ?>
		<div id="usertype_twitter_pane" style="display:none;">
			<h1><?php echo JText::_('COM_EASYDISCUSS_TWITTER_SIGN_IN'); ?></h1>
			<?php echo $twitter; ?>
		</div>
	<?php } ?>
		<div id="usertype_loading_pane" style="align:center;display:none;">
			<img src="<?php echo JURI::root().'components/com_easydiscuss/assets/images/loading.gif';  ?>" alt="Loading">
		</div>
	</div>
</div>
