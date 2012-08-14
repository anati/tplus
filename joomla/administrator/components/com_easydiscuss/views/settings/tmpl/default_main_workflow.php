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
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td valign="top" width="50%">

			<table class="noshow">
				<tr>
					<td width="98%" valign="top">
						<a name="main_config" id="main_config"></a>
						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_MAIN' ); ?></legend>
						<table class="admintable" cellspacing="1" width="100%">
							<tbody>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TITLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_MAIN_TITLE_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_TITLE' ); ?>
								</span>
								</td>
								<td valign="top">
									<input type="text" name="main_title" class="inputbox" size="60" value="<?php echo $this->config->get('main_title' );?>" />
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_DESCRIPTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_MAIN_DESCRIPTION_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_MAIN_DESCRIPTION' ); ?>
								</span>
								</td>
								<td valign="top">
									<textarea name="main_description" class="inputbox full-width" cols="65" rows="5"><?php echo $this->config->get( 'main_description' ); ?></textarea>
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_POST_QUESTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ALLOW_GUEST_TO_POST_QUESTION_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_POST_QUESTION' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_allowguestpostquestion' , $this->config->get( 'main_allowguestpostquestion' ) );?>
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_POST' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ALLOW_GUEST_TO_POST_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_POST' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_allowguestpost' , $this->config->get( 'main_allowguestpost' ) );?>
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_MODERATE_NEW_POST' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_MODERATE_NEW_POST_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_MODERATE_NEW_POST' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_moderatepost' , $this->config->get( 'main_moderatepost' ) );?>
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_REGISTERED_USER_CREATE_TAG' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ALLOW_REGISTERED_USER_CREATE_TAG_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_REGISTERED_USER_CREATE_TAG' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_allowcreatetag' , $this->config->get( 'main_allowcreatetag' ) );?>
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_STICKY' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POST_STICKY_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_STICKY' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_allowsticky' , $this->config->get( 'main_allowsticky' ) );?>
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_DELETE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POST_DELETE_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_DELETE' ); ?>
								</span>
								</td>
								<td valign="top">
								<?php
									$Option = array();
									$Option[] = JHTML::_('select.option', '0', JText::_( 'NO' ) );
									$Option[] = JHTML::_('select.option', '1', JText::_( 'YES' ) );
									$Option[] = JHTML::_('select.option', '2', JText::_( 'COM_EASYDISCUSS_SETTINGS_SITE_ADMIN_ONLY' ) );
									$showdet = JHTML::_('select.genericlist', $Option, 'main_allowdelete', 'size="1" class="inputbox"', 'value', 'text', $this->config->get('main_allowdelete' , '1' ) );
									echo $showdet;
								?>
								</td>
							</tr>

							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS' ); ?>
								</span>
								</td>
								<td valign="top">
									<input type="text" name="main_orphanitem_ownership" class="inputbox" style="width: 50px;" maxlength="2" value="<?php echo $this->config->get('main_orphanitem_ownership', $this->defaultSAId );?>" />
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_FIELDS_URL_REFERENCES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_FIELDS_URL_REFERENCES_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_FIELDS_URL_REFERENCES' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'reply_field_references' , $this->config->get( 'reply_field_references' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SYNTAX_HIGHLIGHTER' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_syntax_highlighter' , $this->config->get( 'main_syntax_highlighter' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_PASSWORD_PROTECTION' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_password_protection' , $this->config->get( 'main_password_protection' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_RSS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_RSS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_RSS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_rss' , $this->config->get( 'main_rss' ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>

						<fieldset class="adminform">
							<legend><?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION' ); ?></legend>
							<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_ENABLE' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_similartopic' , $this->config->get( 'main_similartopic' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_INCLUDE_PRIVATE_POSTS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_similartopic_privatepost' , $this->config->get( 'main_similartopic_privatepost' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_SIMILAR_QUESTION_SEARCH_LIMIT' ); ?>
								</span>
								</td>
								<td valign="top">
									<input type="text" name="main_similartopic_limit" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('main_similartopic_limit' , '5' );?>" />
								</td>
							</tr>
							</tbody>
							</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_WHOS_VIEWING' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_WHOS_VIEWING' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_WHOS_VIEWING_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_WHOS_VIEWING' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_viewingpage' , $this->config->get( 'main_viewingpage' ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_EMBEDDING' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_WIDTH' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_VIDEO_WIDTH_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_WIDTH' ); ?>
								</span>
								</td>
								<td valign="top">
									<input type="text" name="bbcode_video_width" value="<?php echo $this->config->get( 'bbcode_video_width' );?>" size="5" style="text-align:center;" /> 
									<span class="small"><?php echo JText::_( 'COM_EASYDISCUSS_PIXELS' ); ?></span>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_HEIGHT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_VIDEO_HEIGHT_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_HEIGHT' ); ?>
								</span>
								</td>
								<td valign="top">
									<input type="text" name="bbcode_video_height" value="<?php echo $this->config->get( 'bbcode_video_height' );?>" size="5" style="text-align:center;" /> 
									<span class="small"><?php echo JText::_( 'COM_EASYDISCUSS_PIXELS' ); ?></span>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<table class="noshow">
				<tr>
					<td>
						<fieldset class="adminform">
							<legend><?php echo JText::_( 'COM_EASYDISCUSS_VOTING' ); ?></legend>
							<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_allowselfvote' , $this->config->get( 'main_allowselfvote' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_VOTE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POST_VOTE_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_VOTE' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_allowvote' , $this->config->get( 'main_allowvote' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_allowquestionvote' , $this->config->get( 'main_allowquestionvote' ) );?>
								</td>
							</tr>
							</tbody>
							</table>
						</fieldset>
						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_POLLS' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POLLS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_polls' , $this->config->get( 'main_polls' ) );?>
								</td>
							</tr>
						</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_LIKES' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_DISCUSSIONS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_likes_discussions' , $this->config->get( 'main_likes_discussions' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_REPLIES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_LIKES_REPLIES_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LIKES_REPLIES' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_likes_replies' , $this->config->get( 'main_likes_replies' ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_RANKING' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_RANKING' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_RANKING_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_RANKING' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_ranking' , $this->config->get( 'main_ranking' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_RANKING_CALCULATION' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_RANKING_CALCULATION_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_RANKING_CALCULATION' ); ?>
								</span>
								</td>
								<td valign="top">
									<select name="main_ranking_calc_type" id="main_ranking_calc_type" class="inputbox">
									    <option value="posts" <?php echo ($this->config->get( 'main_ranking_calc_type' ) == 'posts') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_EASYDISCUSS_RANKING_TYPE_POSTS'); ?></option>
									    <option value="points" <?php echo ($this->config->get( 'main_ranking_calc_type' ) == 'points') ? 'selected="selected"' : '' ?>><?php echo JText::_('COM_EASYDISCUSS_RANKING_TYPE_POINTS'); ?></option>
									</select>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_LOGIN_PROVIDER' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_SELECT_LOGIN_PROVIDER' ); ?>
								</span>
								</td>
								<td valign="top">
									<select name="main_login_provider" class="inputbox">
										<option value="joomla"<?php echo $this->config->get( 'main_login_provider' ) == 'joomla' ? ' selected="selected"' : '';?>><?php echo JText::_( 'Joomla' );?></option>
										<option value="jomsocial"<?php echo $this->config->get( 'main_login_provider' ) == 'jomsocial' ? ' selected="selected"' : '';?>><?php echo JText::_( 'JomSocial' );?></option>
										<option value="cb"<?php echo $this->config->get( 'main_login_provider' ) == 'cb' ? ' selected="selected"' : '';?>><?php echo JText::_( 'Community Builder' );?></option>
									</select>
								</td>
							</tr>
						</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_REPORTS' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_REPORT' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_REPORT_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_REPORT' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_report' , $this->config->get( 'main_report' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_REPORT_THRESHOLD' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_REPORT_THRESHOLD_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_REPORT_THRESHOLD' ); ?>
								</span>
								</td>
								<td valign="top">
									<input type="text" name="main_reportthreshold" class="inputbox" style="width: 150px;" value="<?php echo $this->config->get('main_reportthreshold' , '0' );?>" />
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_TAGS' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TAGS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TAGS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TAGS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_tags' , $this->config->get( 'main_tags' ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>

						<fieldset class="adminform">
						<legend><?php echo JText::_( 'COM_EASYDISCUSS_EVENT_TRIGGERS' ); ?></legend>
						<table class="admintable" cellspacing="1">
							<tbody>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_content_trigger_posts' , $this->config->get( 'main_content_trigger_posts' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_content_trigger_replies' , $this->config->get( 'main_content_trigger_replies' ) );?>
								</td>
							</tr>
							<tr>
								<td width="300" class="key">
								<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS' ); ?>::<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS_DESC'); ?>">
									<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS' ); ?>
								</span>
								</td>
								<td valign="top">
									<?php echo $this->renderCheckbox( 'main_content_trigger_comments' , $this->config->get( 'main_content_trigger_comments' ) );?>
								</td>
							</tr>
							</tbody>
						</table>
						</fieldset>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
