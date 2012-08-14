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
<?php if($view == 'index' || $view == 'tag') : ?>
<ul id="filter-links" class="list-tab reset-ul float-li clearfix">
    <li class="allposts<?php echo ($filter == 'allposts') ? ' active' : '';?>">
		<a href="javascript:void(0);" onclick="discuss.filter( 'allposts' , '<?php echo $activeCategory->id; ?>');"><?php echo JText::_('COM_EASYDISCUSS_FILTER_ALL_POSTS'); ?><i class="ico indicator"></i></a>
	</li>
	
	<?php if( $system->config->get('layout_featuredpost_style') != '1' ) : ?>
    <li class="featured<?php echo ($filter == 'featured') ? ' active' : '';?>">
		<a href="javascript:void(0);" onclick="discuss.filter( 'featured' , '<?php echo $activeCategory->id; ?>');">
			<?php echo ($featured > 0) ? '<span>' . $featured . '</span>' : ''; ?>
			<?php echo JText::_('COM_EASYDISCUSS_FILTER_FEATURED'); ?>
		</a>
	</li>
	<?php endif; ?>
	
	<li class="new<?php echo ($filter == 'new' ) ? ' active' : '';?>">
	    <a href="javascript:void(0);" onclick="discuss.filter( 'new' , '<?php echo $activeCategory->id;?>');">
	    	<?php echo ($new > 0 ) ? '<span>' . $new . '</span>' : '' ?>
	    	<?php echo JText::_( 'COM_EASYDISCUSS_NEW_STATUS' );?>
	    </a>
	</li>
    <li class="unanswered<?php echo ($filter == 'unanswered') ? ' active' : '';?>">
		<a href="javascript:void(0);" onclick="discuss.filter('unanswered' , '<?php echo $activeCategory->id; ?>');">
			<?php echo ($unanswered > 0) ? '<span>' . $unanswered . '</span>' : ''; ?>	
			<?php echo JText::_('COM_EASYDISCUSS_FILTER_UNANSWERED'); ?>
		</a>
	</li>
</ul>
<?php endif; ?>