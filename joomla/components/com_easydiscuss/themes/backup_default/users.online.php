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
<div id="dc_members-view" class="bt-sd mt-15 pt-15">
	<div class="section-label fs-11 mb-5 ttu small"><?php echo JText::_( 'COM_EASYDISCUSS_VIEWERS_ON_PAGE' );?></div>
	<div class="section-body clearfix mt-10">
	<?php foreach( $users as $user ){ ?>
	<a class="float-l mr-10 mb-10 has-tip" href="<?php echo $user->getLink();?>" style="line-height:30px">
		<span class="basic-tip">
            <i class="ico btip"></i>
            <?php echo $user->getName();?>
        </span>
		<img src="<?php echo $user->getAvatar();?>" class="avatar small" />
	</a>
	<?php } ?>
	</div>
</div>