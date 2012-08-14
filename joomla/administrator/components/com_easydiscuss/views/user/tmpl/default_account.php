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
$joomla_date    = '%Y-%m-%d %H:%M:%S';
?>
<script type="text/javascript">
EasyDiscuss.ready(function($){
	$( '#signature' ).markItUp( mySettings );
});
</script>
<style>
body .key{width:200px !important;}
</style>
<form name="adminForm" id="adminForm" class="adminform-body" action="index.php?option=com_easydiscuss&controller=user" method="post" enctype="multipart/form-data">
<div class="adminform-body">
<table width="100%">
	<tr>
		<td width="50%">
			<fieldset>
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_ACCOUNT_DETAILS' ); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
					    <td valign="top" class="key" style="width:200px">
							<label><?php echo JText::_('COM_EASYDISCUSS_AVATAR'); ?> :</label>
							<div class="item_content_lite">
								<?php
								if($this->config->get('layout_avatar'))
								{
									if(! $this->avatarIntegration=='default')
									{
										echo JText::sprintf('COM_EASYDISCUSS_INTEGRATED_WITH', $this->avatarIntegration);
									}
								}
								?>
							</div>
						</td>
					    <td>
					    	<?php
							if($this->config->get('layout_avatar'))
							{
							    $maxSize 		= (int) $this->config->get( 'main_upload_maxsize', 0 );
							    $maxSizeInMB    = $maxSize / (1000 * 1000);

							?>
								<img style="border-style:solid;" src="<?php echo $this->profile->getAvatar(); ?>"/>
								<div id="avatar-upload-form" style="margin: 20px 0px 10px 0px;">
									<div>
										<?php echo JText::sprintf( 'COM_EASYDISCUSS_AVATAR_UPLOAD_CONDITION', $maxSizeInMB, $this->config->get( 'layout_avatarwidth' ), $this->config->get( 'layout_avatarheight' ) ); ?>
									</div>

									<div>
										<input id="file-upload" type="file" name="Filedata" size="65"/>
									</div>
									<div>
										<span id="upload-clear"/>
									</div>
								</div>
							<?php
							}
							else
							{
								echo JText::_('COM_EASYDISCUSS_AVATAR_DISABLE_BY_ADMINISTRATOR');
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_USERNAME'); ?> :</td>
						<td class="td-text"><?php echo $this->user->username; ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_USER_POINTS'); ?> :</td>
						<td class="td-text"><input type="textbox" value="<?php echo $this->profile->points; ?>" name="points" class="input" style="width:50px;text-align:center;" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_FULL_NAME'); ?> :</td>
						<td><input type="textbox" value="<?php echo $this->escape( $this->user->name ); ?>" name="fullname" class="input" style="width:300px" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_NICK_NAME'); ?> :</td>
						<td><input type="textbox" value="<?php echo $this->escape( $this->profile->nickname ); ?>" name="nickname" class="input" style="width:200px" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_NICK_EMAIL'); ?> :</td>
						<td class="td-text"><?php echo $this->user->email; ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SIGNATURE'); ?> :</td>
						<td class="td-text">
							<textarea name="signature" id="signature" class="inputbox"><?php echo $this->profile->getSignature( true ); ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?> :</td>
						<td><input type="textbox" value="<?php echo $this->userparams->get( 'facebook' ); ?>" name="facebook" class="input" style="width:200px" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?> :</td>
						<td><input type="textbox" value="<?php echo $this->userparams->get( 'twitter' ); ?>" name="twitter" class="input" style="width:200px" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?> :</td>
						<td><input type="textbox" value="<?php echo $this->userparams->get( 'linkedin' ); ?>" name="linkedin" class="input" style="width:200px" /></td>
					</tr>
				</tbody>
			</table>
			</fieldset>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
</div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->user->id;?>" />
</form>
