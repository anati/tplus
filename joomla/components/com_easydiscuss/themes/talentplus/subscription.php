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
<?php if( $system->config->get( 'main_postsubscription', 0)  ): ?>
	<?php if( $isSubscribed && $system->my->id != 0 ) : ?>
		<a id="unsubscribe-<?php echo $sid; ?>" class="cancel-email has-tip atr<?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.load('index', 'ajaxUnSubscribe', '<?php echo $type; ?>', '<?php echo $isSubscribed; ?>');">
			<?php echo ($simple) ? JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE' ) : JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_TO_' . strtoupper($type) ); ?>
			<span class="basic-tip">
				<i class="ico btip"></i>
				<?php echo JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?>
			</span>
		</a>
	<?php else: ?>
		<a id="subscribe-<?php echo $type.'-'.$cid; ?>" class="via-email has-tip atr<?php echo ($class) ? ' '.$class : ''; ?>" href="javascript:void(0);" onclick="disjax.load('index', 'ajaxSubscribe', '<?php echo $type; ?>', '<?php echo $cid; ?>');">
			<?php echo ($simple) ? JText::_( 'COM_EASYDISCUSS_SUBSCRIBE' ) : JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_TO_' . strtoupper($type) ); ?>
			<span class="basic-tip">
				<i class="ico btip"></i>
				<?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_VIAEMAIL_'.strtoupper($type) ); ?>
			</span>
		</a>
	<?php endif; ?>
<?php endif; ?>
