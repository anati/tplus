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

<div id="dc_search_result">
<?php if(empty($query)) : ?>
	<div class="msg_in"></div>
<?php endif; ?>

<?php if ( !empty( $posts ) ) : ?>
<div class="msg_in dc_success"><?php echo JText::sprintf('COM_EASYDISCUSS_SEARCH_RESULT_FOUND' , $pagination->total , $query ); ?></div>
    <ul id="dc_list" class="discuss-index for-search reset-ul mt-15">
    	<?php echo $this->loadTemplate( 'search.item.php' ); ?>
    </ul>
    
    <?php echo $this->loadTemplate( 'pagination.php' );?>
    
<?php else: ?>
	<?php if( !empty($query) ): ?>
	<div class="msg_in dc_alert"><?php echo JText::sprintf( 'COM_EASYDISCUSS_SEARCH_NO_RESULT' , $query ) ?></div>
	<?php endif; ?>
<?php endif; ?>
</div>