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
<?php if( $system->config->get( 'layout_category_showtree' ) ){ ?>
<?php if( $categories ){ ?>
<div id="dc_categories-front" class="mt-15">
	<ul class="discussion-categories reset-ul float-li bb-sd clearfix">
		<?php foreach( $categories as $category){ ?>
		<li class="list-item">
			<div>
				<?php if( $system->config->get( 'main_rss' ) ){ ?>
				<a class="category-rss" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index&format=feed&category_id=' . $category->id );?>"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_RSS_FEED');?></a>
				<?php } ?>
				
				<a class="category-name fsg fwb" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index&category_id=' . $category->id );?>"><?php echo $category->getTitle();?></a>
				<span class="small fs11">- <?php echo $this->getNouns( 'COM_EASYDISCUSS_ENTRY_COUNT' , $category->getPostCount() , true );?></span>
			</div>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } ?>
<?php } ?>