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


<?php if( isset( $activeCategory ) && $activeCategory->id ) { ?>
<h2 class="component-head reset-h bb-sd">
	<img src="<?php echo $activeCategory->getAvatar(); ?>" class="avatar float-l mr-10" width="48" />
	<?php echo $activeCategory->getTitle();?>
</h2>
<?php if( $activeCategory->getParam( 'show_description')){ ?>
<div class="discussion-category mt-15 clearfix">
	<div class="mt-10">
		<?php if ( $activeCategory->description ) { ?>
		<p><?php echo $activeCategory->description; ?></p>
		<?php } ?>
		<p class="fwb">
			<?php echo JText::sprintf( 'COM_EASYDISCUSS_DISCUSSIONS_IN_CATEGORY' , $activeCategory->getPostCount() ); ?>
			<?php if( $system->config->get( 'main_rss' ) ){ ?>
			&middot;
			<a href="<?php echo $activeCategory->getRSS(); ?>" class="inl-ico rss"><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_RSS' );?></a>
			<?php } ?>
		</p>
	</div>
</div>
<?php } ?>
<?php } ?>

<?php 	if( isset( $categories ) ) { ?>
<?php 		echo $this->loadTemplate( 'main.categories.php' ); ?>
<?php 	} ?>

<div id="item-wrapper">
    <?php if( $system->config->get('layout_featuredpost_style') != '1' ) : ?>
		<?php echo $filterbar; ?>
		<div id="index-loading" class="bg-f5 b-sd tac pa-15 mt-15" style="display: none;"></div>
	<?php endif; ?>

	<div id="dc_index">
		<div class="in">
			<?php  echo $featuredpostsHTML; ?>
			<div id="dc_index-list">
				<div class="section-label fs-11 ttu small pt-15"><?php echo JText::_('COM_EASYDISCUSS_RECENT_POSTS'); ?></div>

	            <?php if( $system->config->get('layout_featuredpost_style') == '1' ) : ?>
					<?php echo $filterbar; ?>
					<div id="index-loading" class="bg-f5 b-sd tac pa-15 mt-15" style="display: none;"></div>
				<?php endif; ?>

				<ul id="dc_list" class="discuss-index reset-ul mt-15">
					<?php echo $this->loadTemplate( 'main.item.php' ); ?>
				</ul>
			</div>
		</div>
	</div>
	<?php echo $this->loadTemplate( 'pagination.php' );?>
</div>

<?php echo DiscussHelper::getWhosOnline();?>