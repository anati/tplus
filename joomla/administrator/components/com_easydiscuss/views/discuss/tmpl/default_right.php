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
<script>
EasyDiscuss.ready(function($){

	$(".si_accordion > h3:first").addClass("active");
	//$(".si_accordion h3 > div:not(:first)").hide();
	$(".si_accordion > h3").siblings("div").hide();
	$(".si_accordion > h3:first + div").show();


	$(".si_accordion > h3").click(function(){

	$(this).next("div").toggle().siblings("div").hide();
	$(this).toggleClass("active");
	$(this).siblings("h3").removeClass("active");

	});

	disjax.load( 'discuss' , 'getUpdates' );
});
</script>

<div class="si_accordion">
	<h3><div><?php echo JText::_('COM_EASYDISCUSS_SLIDER_STATISTICS'); ?></div></h3>
	<div class="user-guide">
		<?php echo $this->loadTemplate( 'stats' );?>
	</div>

	<h3><div><?php echo JText::_('COM_EASYDISCUSS_SLIDER_ABOUT'); ?></div></h3>
	<div class="user-guide">
		<?php echo $this->loadTemplate( 'about' );?>
	</div>

	<h3><div class="discuss-news"><?php echo JText::_('COM_EASYDISCUSS_SLIDER_NEWS'); ?></div></h3>
	<div class="user-guide">
		<?php echo $this->loadTemplate( 'news' );?>
	</div>
</div>
