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
<h2 class="component-head reset-h"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_PAGE_HEADING'); ?></h2>

<div id="dc_categories-all" class="component-page">
	<div class="in">
		<ul class="list-categories reset-ul">
			<?php foreach( $categories as $category ){ ?>
			<li style="margin-left: <?php echo $category->depth * 25;?>px" class="<?php echo ( $category->depth ) ? 'child' : 'parent'; ?> category-<?php echo $category->id;?>">
				<div class="clearfull">
					<a class="avatar float-l" href="<?php echo $category->getPermalink();?>">
						<img width="35" src="<?php echo $category->getAvatar();?>" class="avatar" />
					</a>
					<div class="category-story">
						<h3 class="category-name reset-h">
							<a href="<?php echo $category->getPermalink();?>"><?php echo $category->getTitle();?></a>
						</h3>

						<?php if( $category->getParam( 'show_description') && !$system->config->get( 'layout_category_description_hidden' ) ) { ?>
						<div class="mt-5">
							<?php echo DiscussStringHelper::escape($category->description) ; ?>
						</div>
						<?php } ?>
						
						<ul class="category-status reset-ul float-li in-block small fs-11 mt-5">
							<?php if( $system->config->get( 'main_rss' ) ){ ?>
							<li><a href="<?php echo $category->getRSSPermalink();?>" class="link-rss"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_RSS_FEED');?></a></li>
							<?php } ?>
							
							<li class="ttu"><?php echo $this->getNouns('COM_EASYDISCUSS_ENTRY_COUNT' , $category->getPostCount() , true );?></li>
						</ul>
						<div class="total-discuss"></div>
					</div>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
<?php if(count($categories) <= 0) { ?>
	<div class="dc_alert msg_in"><?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND'); ?></div>
<?php } ?>
