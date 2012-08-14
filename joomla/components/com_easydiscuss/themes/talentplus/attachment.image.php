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

$mainframe  = JFactory::getApplication();
$isBackend  = ( $mainframe->isAdmin() ) ? true : false;
?>

<a class="attachments-image" title="<?php echo $attachment->title;?>" href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&id=' . $attachment->id ); ?>">
	<img src="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=attachment&task=displayFile&tmpl=component&id=' . $attachment->id ); ?>" style="display:none;" />
    <?php echo $attachment->title;?>
</a>

<?php if( $system->acl->allowed( 'delete_attachment' , 0 ) && !$isBackend ) : ?>
- <a onclick="discuss.attachments.removeItem(this,'<?php echo $attachment->id;?>');" href="javascript:void(0);"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' );?></a>
<?php endif; ?>
