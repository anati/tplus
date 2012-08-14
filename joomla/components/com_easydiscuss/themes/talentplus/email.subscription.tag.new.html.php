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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_NEWSLETTER_TITLE'); ?></title>
</head>

<body style="margin:0;padding:0">
<div style="width:100%;background:#ddd;margin:0;padding:50px 0 80px;color:#798796;font-family:'Lucida Grande',Tahoma,Arial;font-size:11px;">
	<center>
		<table cellpadding="0" cellspacing="0" border="0" style="width:720px;background:#fff;border:1px solid #b5bbc1;border-bottom-color:#9ba3ab;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;">
			<tbody>
				<tr>
					<td style="padding:20px;border-bottom:1px solid #b5bbc1;;background:#f5f5f5;border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;-webkit-border-radius:3px 3px 0 0;"><b style="font-family:Arial;font-size:17px;font-weight:bold;color:#333;display:inline-block;"><?php echo $siteName;?></b></td>
				</tr>
				<tr>
					<td style="padding:15px 20px;line-height:19px;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:left">
						<b><?php echo $postAuthor; ?></b> <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CREATED_NEW_DISCUSSION' );?> <b><?php echo $postTitle; ?></b> <?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_WITH_TAG');?>
						<b><?php echo $tagTitle;?></b>
						<br />
						<hr style="clear:both;margin:10px 0 15px;padding:0;border:0;border-top:1px solid #ddd" />
						<img src="<?php echo $postAuthorAvatar; ?>" width="80" alt="<?php echo $postAuthor; ?>" style="width:80px;height:80px;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;float:left;margin:0 15px 0 0" />
							<?php echo $postContent; ?>
						<br style="clear:both" />
						<br />
						<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_VIEW_DISCUSSION_LINK_BELOW' );?>
					</td>
				</tr>
				<tr>
					<td style="padding:20px;border-top:1px solid #ccc;padding:20px;line-height:19px;color:#555;font-family:'Lucida Grande',Tahoma,Arial;font-size:12px;text-align:left">
						<a href="<?php echo $postLink;?>" target="_blank" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_READ_THIS_DISCUSSION' );?> &nbsp; &raquo;</a>
					</td>
				</tr>
			</tbody>
		</table>

		<p>
		<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_SUBSCRIPTION_STATEMENT' ); ?><br />
		<?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_TO_UNSUBSCRIBE' );?><a href="<?php echo $unsubscribeLink; ?>" style="color:#477fda;"><?php echo JText::_( 'COM_EASYDISCUSS_EMAILTEMPLATE_CLICK_HERE' );?></a>.
		</p>
	</center>
</div>

</body>
</html>
