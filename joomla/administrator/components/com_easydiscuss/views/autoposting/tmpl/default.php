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
<ul class="autoposting-p1 reset-ul">
	<li>
		<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/assets/images/facebook_setup.png" style="float:left;margin-right:20px;" />
		<h3 class="head-3"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEEBOOK');?></h3>
		<p>
			<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_DESC' );?>
		</p>
		<?php if( !$this->facebookSetup ){ ?>
			<div><a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=facebook&step=1' );?>" class="button social facebook"><?php echo JText::_( 'Setup autoposting for Facebook' );?></a></div>
		<?php } else { ?>
			<div><a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=form&type=facebook' );?>" class="button social facebook"><?php echo JText::_( 'Configure Facebook Autoposting' );?></a></div>
		<?php } ?>
		<div style="clear:both;"></div>
	</li>
	<li>
		<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/assets/images/twitter_setup.png" style="float:left;margin-right:20px;" />
		<h3 class="head-3"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER');?></h3>
		<p>
			<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_DESC' );?>
		</p>
		<?php if( !$this->twitterSetup ){ ?>
			<div><a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=twitter&step=1' );?>" class="button social twitter"><?php echo JText::_( 'Setup autoposting for Twitter' );?></a></div>
		<?php } else { ?>
			<div><a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=form&type=twitter' );?>" class="button social twitter"><?php echo JText::_( 'Configure Twitter Autoposting' );?></a></div>
		<?php } ?>
		<div style="clear:both;"></div>
	</li>
</ul>