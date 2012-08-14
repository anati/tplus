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
<div class="adminform-body">
<fieldset class="adminform">
	<legend><?php echo JText::_( 'COM_EASYDISCUSS_USER_BADGES' );?></legend>
	<?php if( $this->badges ){ ?>
	<ul class="user-badges reset-ul">
		<?php foreach( $this->badges as $badge ){ ?>
			<li>
				<img src="<?php echo $badge->getAvatar(); ?>" width="48" style="float:left;display:inline-block;margin:0 10px 0 0" />
				<div style="line-height:48px;height:48px;">
					<b><?php echo $badge->get( 'title' ); ?></b>
					<span class="small hide">
						-
						<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=users&task=removeBadge&id=' . $badge->get('id') . '&userid=' . $this->profile->id );?>">
							<?php echo JText::_( 'COM_EASYDISCUSS_REMOVE_BADGE' );?>
						</a>
					</span>
				</div>
			</li>
		<?php } ?>
	</ul>
	<?php } else { ?>
	<div class="small">
		<?php echo JText::_( 'COM_EASYDISCUSS_USER_NO_BADGES_YET' ); ?>
	</div>
	<?php } ?>
</fieldset>
</div>