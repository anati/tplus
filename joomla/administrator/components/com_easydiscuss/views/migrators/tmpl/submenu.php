<?php
/**
* @package      EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$active	= JRequest::getString( 'active' , '' );
?>

<script type="text/javascript">
(function($){
	$(window).load(function(){
	<?php
		if(!empty($active))
		{
			?>$$('ul#submenu li a#<?php echo $active; ?>').fireEvent('click');<?php
		}
	?>
	});
})(Foundry);
</script>

<div id="submenu-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">
		<div class="submenu-box">
			<div class="submenu-pad">
				<ul id="submenu">
					<?php if(DiscussHelper::getJoomlaVersion() <= '1.5') : ?>
					<li><a id="home" class="goback" href="<?php echo JRoute::_('index.php?option=com_easydiscuss');?>">&laquo; <?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SUBMENU_BACK' ); ?></a></li>
					<?php endif; ?>
					<li><a id="main"<?php echo $active == 'main' || $active == '' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA' ); ?></a></li>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

