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

$currentTagId = JRequest::getInt('id');
?>
<h2 class="component-head reset-h"><?php echo JText::sprintf('COM_EASYDISCUSS_TAG', $currentTag ); ?></h2>

<ul id="dc_list" class="discuss-index reset-ul mt-15">
	<?php echo $this->loadTemplate( 'main.item.php' ); ?>
</ul>
<?php echo $this->loadTemplate( 'pagination.php' ); ?>