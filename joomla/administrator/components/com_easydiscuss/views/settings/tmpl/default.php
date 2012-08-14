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
(function($){
	<?php
	if($this->joomlaversion >= '1.6') 
	{
	?>
	
	Joomla.submitbutton = function(task){
		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});
		Joomla.submitform(task);
	}
	
	<?php
	}
	else
	{
	?>
	
	function submitbutton( action )
	{
		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});
		
		submitform( action );
	}
	
	<?php
	}
	?>
})(Foundry);
</script>
<form action="index.php" method="post" name="adminForm">
<div id="config-document">
	<div id="page-main" class="tab">
		<table class="noshow" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('main');?></td>
			</tr>
		</table>
	</div>
	<div id="page-antispam" class="tab">
		<table class="noshow" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('antispam');?></td>
			</tr>
		</table>
	</div>
	<div id="page-discusslayout" class="tab">
		<table class="noshow" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('layout');?></td>
			</tr>
		</table>
	</div>
	<div id="page-notifications" class="tab">
		<table class="noshow" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('notifications');?></td>
			</tr>
		</table>
	</div>
	<div id="page-integrations" class="tab">
		<table class="noshow" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('integrations');?></td>
			</tr>
		</table>
	</div>
	<div id="page-social" class="tab">
		<table class="noshow" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('social');?></td>
			</tr>
		</table>
	</div>
</div>
<div class="clr"></div>
<input type="hidden" name="active" id="active" value="" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="settings" />
</form>