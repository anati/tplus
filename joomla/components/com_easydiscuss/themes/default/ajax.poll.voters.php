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
<h3><?php echo JText::_( 'COM_EASYDISCUSS_USERS_WHO_VOTED_THIS_POLL' ); ?></h3>
<div style="max-height:650px;overflow-y:auto;margin-top:10px">
<?php
if( $voters )
{
	foreach( $voters as $voter )
	{
?>
	<div style="display:inline-block;width:100%;line-height:40px;margin:5px 0">
		<a href="<?php echo $voter->getLink();?>">
			<img src="<?php echo $voter->getAvatar();?>" width="40" style="float:left;display:inline-block;margin-right:10px;border-radius:2px;-moz-border-radius:2px;-webkit-border-radius:2px" /><?php echo $voter->getName();?>
		</a>
	</div>
<?php
	}
}
else
{
?>
<span class="small"><?php echo JText::_( 'COM_EASYDISCUSS_POLLS_NO_USER_VOTED_YET' ); ?></span>
<?php
}
?>
</div>
