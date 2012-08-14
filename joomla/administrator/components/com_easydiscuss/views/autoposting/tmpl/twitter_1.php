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
<div class="pa-15">
	<h3 class="head-3"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER' );?> - <?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_STEP_1'); ?></h3>
	<form name="facebook" action="index.php" method="post">
	<ul class="list-instruction reset-ul pa-15">
		<li>
			<?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_STEP_1_DESC'); ?> <a href="http://dev.twitter.com" target="_blank">http://dev.twitter.com</a>
		</li>
		<li>
			<div><?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_STEP_1_COPY_APP_ID'); ?></div>
			<div class="mini-form">
				<label for="main_autopost_twitter_id"><?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_APP_ID' );?>:</label>
				<input type="text" name="main_autopost_twitter_id" id="main_autopost_twitter_id" value="<?php echo $this->config->get( 'main_autopost_twitter_id' );?>" class="input" style="width:200px" />
			</div>
		</li>
		<li>
			<div><?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_STEP_1_COPY_APP_SECRET'); ?></div>
			<div class="mini-form">
				<label for="main_autopost_twitter_secret"><?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_APP_SECRET' );?>:</label>
				<input type="text" name="main_autopost_twitter_secret" id="main_autopost_twitter_secret" value="<?php echo $this->config->get( 'main_autopost_twitter_secret' );?>" class="input" style="width:200px" />
			</div>
		</li>
	</ul>
	<input type="submit" class="button social twitter" value="<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_NEXT_STEP' );?>" />
	<input type="hidden" name="main_autopost_twitter" value="1" />
	<input type="hidden" name="step" value="1" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="twitter" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	</form>
</div>
