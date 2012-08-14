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
<script type="text/javascript">
function showPageForm()
{
	Foundry( '#page-form' ).toggle();
}
</script>
<div class="pa-15">
	<h3 class="head-3"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_STEP_2'); ?></h3>
	<form name="facebook" action="index.php" method="post">
	<ul class="list-instruction reset-ul pa-15">
		<li>
			<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_STEP_2_SIGN_IN_WITH_TWITTER'); ?> 
			<?php if( $this->associated ){ ?>
			<div>
				<?php echo JText::_( 'Completed' );?>
			</div>
			<?php } else { ?>
			<div>
				<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=twitter');?>"><img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/assets/images/twitter_signon.png" /></a>
			</div>
			<?php } ?>
		</li>
		<li>
			<div>
				<label for="post-message"><?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE' ); ?></label>
			</div>
			<div class="mini-form">
				<textarea name="main_autopost_twitter_message" class="input textarea" style="width:400px;height:100px;padding:5px"><?php echo $this->config->get( 'main_autopost_twitter_message' );?></textarea>
			<div>
		</li>
	</ul>
	<input type="submit" class="button social twitter" value="<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_COMPLETE' );?>" />
	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="twitter" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	</form>
</div>
