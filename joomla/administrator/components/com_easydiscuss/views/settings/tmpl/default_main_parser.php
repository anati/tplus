<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function testParser()
{
	var server		= Foundry('input[name=main_email_parser_server]').val();
	var port		= Foundry('input[name=main_email_parser_port]').val();
	var service		= Foundry('#main_email_parser_service').val();
	var ssl			= Foundry('input[name=main_email_parser_ssl]').val();
	var user 		= Foundry('input[name=main_email_parser_username]').val();
	var pass 		= Foundry('input[name=main_email_parser_password]').val();
	var validate	= Foundry('input[name=main_email_parser_validate]').val();

	disjax.load( 'settings' , 'testParser' , server , port , service , ssl , user , pass , validate );
}
</script>
<table cellpadding="0" cellspacing="0" width="100%" id="parser-form">
	<tr>
		<td valign="top" width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER' ); ?></legend>
			<p class="small"><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_DESC' );?></p>
			<div class="notice">
				<?php echo JText::_( 'COM_EASYDISCUSS_CRONJOB_INFO' );?> <a href="http://stackideas.com/docs/easydiscuss/cronjobs/" target="_blank">http://stackideas.com/docs/easydiscuss/cronjobs/</a>
			</div>
			<table class="admintable" cellspacing="1" width="100%">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TEST_EMAIL_PARSER' ); ?>
					</span>
					</td>
					<td valign="top">
						<button type="button" onclick="testParser();"><?php echo JText::_( 'COM_EASYDISCUSS_TEST_CONNECTION_BUTTON');?></button>
						<span id="test-result"></span>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_ALLOW_EMAIL_PARSER' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_email_parser' , $this->config->get( 'main_email_parser' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_ADDRESS' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="main_email_parser_server" value="<?php echo $this->config->get( 'main_email_parser_server' );?>" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_PORT' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="main_email_parser_port" value="<?php echo $this->config->get( 'main_email_parser_port' );?>" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVICE_TYPE' ); ?>
					</span>
					</td>
					<td valign="top">
						<select name="main_email_parser_service" id="main_email_parser_service">
							<option value="imap"><?php echo JText::_( 'IMAP' );?></option>
							<option value="pop3"><?php echo JText::_( 'POP3' );?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SERVER_SSL' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_email_parser_ssl' , $this->config->get( 'main_email_parser_ssl' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_VALIDATE' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_email_parser_validate' , $this->config->get( 'main_email_parser_validate' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_USERNAME' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_USERNAME_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_USERNAME' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="main_email_parser_username" value="<?php echo $this->config->get( 'main_email_parser_username' );?>" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PASSWORD' ); ?>
					</span>
					</td>
					<td valign="top">
						<input name="main_email_parser_password" value="<?php echo $this->config->get( 'main_email_parser_password' );?>" type="password" autocomplete="off" />
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PROCESS_LIMIT' ); ?>
					</span>
					</td>
					<td valign="top">
						<input type="text" name="main_email_parser_limit" value="<?php echo $this->config->get( 'main_email_parser_limit' );?>" style="text-align:center;width:50px;" />
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAILS' );?>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIL_PARSER_PUBLISHING' ); ?></legend>
			<table class="admintable" cellspacing="1" width="100%">
				<tbody>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_SEND_RECEIPT' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_email_parser_receipt' , $this->config->get( 'main_email_parser_receipt' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_ALLOW_REPLIES' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_email_parser_replies' , $this->config->get( 'main_email_parser_replies' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_MODERATE_POSTS' ); ?>
					</span>
					</td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'main_email_parser_moderation' , $this->config->get( 'main_email_parser_moderation' ) );?>
					</td>
				</tr>
				<tr>
					<td width="300" class="key">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY_DESC'); ?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_CATEGORY' ); ?>
					</span>
					</td>
					<td valign="top">
						<select name="main_email_parser_category">
							<?php foreach( $this->getCategories() as $category ){ ?>
							<option value="<?php echo $category->id; ?>"<?php echo $this->config->get( 'main_email_parser_category' ) == $category->id ? ' selected="selected"' : '';?>><?php echo $category->title; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
	</tr>
</table>
