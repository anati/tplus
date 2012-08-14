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
	if(DiscussHelper::getJoomlaVersion() >= '1.6') 
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
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="adminform-body">
<div id="config-document">
	<div id="page-main" class="tab">
		<table class="noshow" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('main');?></td>
			</tr>
		</table>
	</div>
	<div id="page-acl" class="tab">
		<table class="noshow" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php echo $this->loadTemplate('acl');?></td>
			</tr>
		</table>
	</div>
</div>
<div class="clr"></div>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="category" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="catid" value="<?php echo $this->cat->id;?>" />
</form>