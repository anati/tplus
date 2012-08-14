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

<?php if( ! empty($replies )) { ?>
	<?php
		foreach($replies as $row)
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load($row->user_id);

			$profileLink		= $profile->getURL();
			$profileName		= ($row->user_id) ? $profile->getName() : $row->poster_name;
			$profileAvatarSrc	= $profile->getAvatar();

	?>
	<span class="reply-by">
	<?php if( $row->user_id ) : ?>
		<a href="<?php echo $profileLink; ?>" class="avatar has-tip">
			<img src="<?php echo $profileAvatarSrc; ?>" width="30" class="avatar mini" />
			<span class="basic-tip">
				<i class="ico btip"></i>
				<?php echo $profileName; ?>
			</span>
		</a>
	<?php else : ?>
		<a href="javascript:void(0)" class="avatar has-tip">
			<img src="<?php echo $profileAvatarSrc; ?>" width="30" class="avatar mini" />
			<span class="basic-tip">
				<i class="ico btip"></i>
				<?php echo $profileName; ?>
			</span>
		</a>
	<?php endif; ?>
	</span>
	<?php
		}
	?>
<?php } ?>
