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
	?>
    <span class="reply-by">
		<a href="<?php echo $profile->getLink(); ?>" class="avatar has-tip">
            <img src="<?php echo $profile->getAvatar(); ?>" width="30" class="avatar mini" />
            <span class="basic-tip">
                <i class="ico btip"></i>
                <?php echo $profile->getName(); ?>
            </span>
        </a>
    </span>
	<?php
		}
	?>
<?php } ?>
