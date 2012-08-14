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
<?php
if( $voters )
{
	foreach( $voters as $voter )
	{
?>
	<span class="has-tip">
		<?php 
		if ($voter->user->id) 
		{ 
		?>
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $voter->user->id );?>" class="fwb">
		<?php 
		} 
		?>

			<span class="basic-tip">
		        <i class="ico btip"></i>
		        <?php echo $voter->getName();?>
		    </span>
			<img src="<?php echo $voter->getAvatar();?>" width="30" />
			
		<?php if ($voter->user->id) 
		{ 
		?>
		</a>
		<?php 
		} 
		?>
	</span>
<?php
	}	
}
?>
<u class="poll-graph" style="width:<?php echo $percentage;?>%"></u>
