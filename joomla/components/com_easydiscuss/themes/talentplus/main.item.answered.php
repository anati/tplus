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

<?php if( count($reply) > 0) { ?>
	<?php
		foreach($reply as $row)
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load($row->user_id);

			$profileLink    	= $profile->getLink();
			$profileName    	= ($row->user_id) ? $profile->getName() : $row->poster_name;
			$profileAvatarSrc   = $profile->getAvatar();

	?>
	<span class="float-r float-span">
		<span class="reply-answer fs-11 mr-10"><?php echo JText::_('COM_EASYDISCUSS_PICKED_ANSWER_BY'); ?>:</span>
		<span class="reply-last">
		<?php if ( $row->user_id != 0 ) { ?>

			<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&id=' . $profile->id );?>" class="avatar has-tip atr">
				<img src="<?php echo $profileAvatarSrc; ?>" class="avatar" width="30" height="30" />
				<span class="basic-tip">
					<i class="ico btip"></i>
					<?php echo $profileName; ?>
				</span>
			</a>
		<?php } else { ?>
			<span class="avatar has-tip atr">
				<img src="<?php echo $profileAvatarSrc; ?>" class="avatar" width="30" height="30" />
				<span class="basic-tip">
					<i class="ico btip"></i>
					<?php echo $profileName; ?>
				</span>
			</span>
		<?php } ?>
		</span>
	</span>
	<?php } //end foreach?>
<?php } ?>
