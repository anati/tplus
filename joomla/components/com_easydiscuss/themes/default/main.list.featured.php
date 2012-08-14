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
<?php if( ! empty($posts )) { ?>
<div id="dc_featured-list">
	<div class="section-label fs-11 ttu small pt-20">
		<?php echo JText::_('COM_EASYDISCUSS_FEATURED_POSTS'); ?>
		<?php if( count( $posts ) > 0) : ?>
		-
		<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=featured'); ?>"><?php echo JText::_('COM_EASYDISCUSS_VIEW_ALL_FEATURED_POSTS'); ?></a>
		<?php endif; ?>
	</div>
	<ul class="discuss-index reset-ul mt-15">
		<?php echo $this->loadTemplate( 'main.item.php' ); ?>
	</ul>
</div>
<?php } ?>