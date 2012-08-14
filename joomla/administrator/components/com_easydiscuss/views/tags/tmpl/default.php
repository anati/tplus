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
				<input type="text" name="search" id="search" value="<?php echo $this->escape( $this->search ); ?>" class="inputbox" style="width:200px;" onchange="document.adminForm.submit();" />
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
	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="1%">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th width="1%"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->tags ); ?>);" /></th>
			<th class="title" style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_('Title') , 'title', $this->orderDirection, $this->order ); ?></th>
			<th width="5%" nowrap="nowrap"><?php echo JText::_( 'Entries' ); ?></th>
			<th class="title" width="10%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_TAGS_AUTHOR' ) , 'user_id', $this->orderDirection, $this->order ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'Published' ); ?></th>
			<th class="title" width="1%"><?php echo JText::_('Preview');?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->tags )
	{
		$k = 0;
		$x = 0;
		for ($i=0, $n=count($this->tags); $i < $n; $i++)
		{
			$row 	= $this->tags[$i];
			$user	= JFactory::getUser( $row->user_id );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
			</td>
			<td align="left">
				<span class="editlinktip hasTip">
					<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&amp;controller=tags&amp;task=edit&amp;tagid='. $row->id); ?>"><?php echo $row->title; ?></a>
				</span>
			</td>
			<td align="center">
				<?php echo $row->count;?>
			</td>
			<td align="center">
				<span class="editlinktip hasTip" title="Creator::<?php echo $user->name; ?>">
					<?php echo $user->name; ?>
				</span>
			</td>
			<td align="center">
				<?php echo JHTML::_('grid.published', $row, $i ); ?>
			</td>
			<td align="center">
				<a href="<?php echo JURI::root() . 'index.php?option=com_easydiscuss&amp;view=tag&id=' . $row->id; ?>" target="_blank" class="preview"><?php echo JText::_('Preview');?></a>
			</td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php
	}
	else
	{
	?>
		<tr>
			<td colspan="8" align="center">
				<?php echo JText::_('No tags created yet.');?>
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
<input type="hidden" name="view" value="tags" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="tags" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
