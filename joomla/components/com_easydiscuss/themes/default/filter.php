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

$view			= JRequest::getString('view', 'index');
$active         = '';

if( $view   == 'post')
{
	$active 		= JRequest::getString('sort', DiscussHelper::getDefaultRepliesSorting());
	$active 		= ($active == 'replylatest') ? 'latest': $active;
}
else
{
	$active 		= JRequest::getString('sort', 'latest');
}

if( $filter == 'unanswered' && ($active == 'active' || $active == 'popular'))
{
	//reset the active to latest.
	$active = 'latest';
}

$fCount     = 0;
$destView   = $view;
if($view == 'tag')
{
	$tag        = JRequest::getInt('id', '0');
    $destView   = 'tag&id='.$tag;
    
    $fCount		= $this->getFeaturedCount($tag);
    $uCount		= $this->getUnansweredCount($tag);
}
else
{
	$fCount		= $this->getFeaturedCount();
	$uCount		= $this->getUnansweredCount();
}

$this->set( 'view' , $view );
$this->set( 'active', $active );
$this->set( 'uCount' , $uCount );
$this->set( 'fCount' , $fCount );
?>
<div class="discuss-filter clearfix mt-20 bb-sc pos-r">
    <?php if( $view == 'post' ): ?>
    <div class="section-head fwb float-l">
    	<?php echo ( $totalReplies > 1 ) ? JText::sprintf('COM_EASYDISCUSS_TOTAL_RESPONSES', $totalReplies) : JText::sprintf('COM_EASYDISCUSS_TOTAL_RESPONSE', $totalReplies); ?>
    	<a href="#add-respond" class="add-respond br-2"><?php echo JText::_( 'COM_EASYDISCUSS_ADD_YOURS' );?></a>
    </div>
    <?php endif; ?>
    <div id="sort-wrapper">
		<?php echo $this->loadTemplate( 'filter.sorting.php' ); ?>
	</div>
	<?php echo $this->loadTemplate( 'filter.filters.php' );?>
</div>