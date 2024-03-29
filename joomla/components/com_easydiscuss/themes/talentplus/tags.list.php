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
<h2 class="component-head reset-h"><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?></h2>


<?php if ( !empty( $tagCloud ) ) { ?>
<div id="dc_tag-all" class="component-page">
    <div class="in">
        <ul class="discuss-tag-list reset-ul float-li clearfull">
        <?php echo $this->loadTemplate( 'tags.item.php' );?>
        </ul>
    </div>
</div>

<?php } else { ?>
<div class="dc_alert msg_in"><?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND'); ?></div>
<?php } ?>