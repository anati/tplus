<?php
/**
* @package		EasyDiscuss
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
<?php if(DiscussHelper::getJoomlaVersion() >= 1.6) : ?>
	Joomla.submitbutton = function( action ) {
		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
			Joomla.submitform( action );
		}
	}
<?php else : ?>
function submitbutton( action )
{
	if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_ARE_YOU_SURE_CONFIRM_DELETE', true); ?>')) {
		submitform( action );
	}
}
<?php endif; ?>
</script>

<form action="index.php" method="post" name="adminForm">
<div class="adminform-head">
	<table class="adminform">
		<tr>
			<td width="40">
				<?php echo JText::_( 'Search' ); ?>
			</td>
			<td width="200">
				<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" onchange="document.adminForm.submit();" />
			</td>
			<td>
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td width="200" nowrap="nowrap">
			  <?php echo $this->state; ?>
			</td>
		</tr>
	</table>
</div>

<div class="adminform-body">
	<?php if( $this->users ) : ?>
	<div class="notice" style="text-align: left !important;"><?php echo JText::_('COM_EASYDISCUSS_USERS_MANAGEMENT_DELETE_NOTICE');?></div>
	<?php endif; ?>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<?php if(empty($this->browse)) : ?>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->users ); ?>);" /></th>
			<?php endif; ?>
			<th width="50px" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
			<th style="text-align: left;"><?php echo JHTML::_('grid.sort', 'Name', 'a.name', $this->orderDirection, $this->order ); ?></th>
			<th style="text-align: left;"><?php echo JHTML::_('grid.sort', 'Username', 'a.username', $this->orderDirection, $this->order ); ?></th>
			<th style="text-align: left;"><?php echo JHTML::_('grid.sort', 'Group', 'a.usertype', $this->orderDirection, $this->order ); ?></th>
			<th style="text-align: left;"><?php echo JHTML::_('grid.sort', 'Email', 'a.email', $this->orderDirection, $this->order ); ?></th>
			<th width="50px" nowrap="nowrap"><?php echo JText::_( 'Enabled' ); ?></th>
			<th width="50px" nowrap="nowrap"><?php echo JText::_( '# Topic Created' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->users )
	{
		$k = 0;
		$x = 0;
		for ($i=0, $n=count($this->users); $i < $n; $i++)
		{
			$row = $this->users[$i];

			$img 			= $row->block ? 'publish_x.png' : 'tick.png';
			$alt 			= $row->block ? JText::_( 'Blocked' ) : JText::_( 'Enabled' );

			$editLink 		= 'index.php?option=com_easydiscuss&amp;controller=blogs&amp;task=edit&amp;blogid=' . $row->id;
			$previewLink	= rtrim( JURI::root() , "/" ) . "/" . JRoute::_("index.php?option=com_easydiscuss&view=entry&id=" . $row->id);
			$preview 	= '<a href="' . $previewLink .'" target="_blank"><img src="'.JURI::base().'/images/preview_f2.png"/ style="width:20px; height:20px; "></a>';
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<?php if(empty($this->browse)) : ?>
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
			</td>
			<?php endif; ?>
			<td width="7" align="center">
				<?php echo $row->id;?>
			</td>
			<td>
			<?php
			if( $this->browse )
			{
			?>
				<a href="javascript:void(0);" onclick="parent.<?php echo $this->browsefunction; ?>('<?php echo $row->id;?>','<?php echo $this->escape($row->name);?>');"><?php echo $row->name;?></a>
			<?php
			}
			else
			{
			?>
				<a href="index.php?option=com_easydiscuss&controller=user&id=<?php echo $row->id;?>&task=edit"><?php echo $row->name;?></a>
			<?php
			}
			?>
			</td>
			<td>
				<?php echo $row->username;?>
			</td>
			<td>
				<?php echo (DiscussHelper::getJoomlaVersion() >= '1.6') ? $row->usergroups : $row->usertype;?>
			</td>
			<td>
				<?php echo $row->email;?>
			</td>
			<td align="center">
				<img src="<?php echo DISCUSS_ADMIN_ASSETS_PATH . 'images/' . $img ?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /></a>
			</td>
			<td align="center">
				<?php echo $this->getTotalTopicCreated( $row->id );?>
			</td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php
	}
	else
	{
	?>
		<tr>
			<td colspan="9" align="center">
				<?php echo JText::_('No user created yet.');?>
			</td>
		</tr>
	<?php
	}
	?>
	</tbody>

	<tfoot>
		<tr>
			<td colspan="11">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	</table>
</div>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="users" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="users" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
