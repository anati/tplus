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
?>
<script type="text/javascript">
(function($){
	$(window).load(function(){
	<?php
		if(!empty($active))
		{
			?>$$('ul#submenu li a#main').fireEvent('click');<?php
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
				<ul id="submenu" class="category">
					<li><a onclick="return false;" id="main" class="active"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_GENERAL' ); ?></a></li>
					<li><a onclick="return false;" id="acl"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_PERMISSIONS' ); ?></a></li>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>