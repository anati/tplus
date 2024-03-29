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
<div class="si_tips-in">
    <div class="text"><?php echo JText::_('COM_EASYDISCUSS_TOOLTIPS_PEOPLE_WHO_REPLIED'); ?></div>
        <div class="replies">
<?php
		    foreach($replies as $row)
		    {
		    	$profile	= DiscussHelper::getTable( 'Profile' );
		    	$profile->load($row->user_id);
?>
			<a href="<?php echo $profile->getLink(); ?>"><img src="<?php echo $profile->getAvatar(); ?>" width="24" /></a>
<?php
		    }
?>
    	</div>
	</div>
</div>
