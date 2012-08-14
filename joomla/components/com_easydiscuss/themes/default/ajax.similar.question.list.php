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
<div>
	<div class="fs-11 fc-99 ttu ml-5 mr-5 pb-5 clearfix">
		<?php echo JText::_('COM_EASYDISCUSS_SIMIMAR_QUESTION_IS_YOUR_QUESTION_SIMILAR_BELOW'); ?>
		<a href="javascript:void(0);" id="similar-question-close"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></a>
	</div>

	<ul id="dc_similar-list" class="similar-index reset-ul">
	    <?php foreach( $posts as $post ) :
			$permalink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);
		?>
	    <li>
	        <a href="<?php echo $permalink; ?>" target="_BLANK"><?php echo $post->title; ?></a>
		</li>
	    <?php endforeach; ?>
	</ul>
</div>
<?php } ?>