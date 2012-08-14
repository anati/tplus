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

$ordering 	= ($this->order == 'lft');
$saveOrder 	= ($this->order == 'lft' && $this->orderDirection == 'asc');
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
					<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_SEARCH' ); ?>
				</td>
				<td width="200">
					<input type="text" name="search" id="search" value="<?php echo $this->search; ?>" class="inputbox full-width" onchange="document.adminForm.submit();" />
				</td>
				<td>
					<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_SUBMIT_BUTTON' ); ?></button>
					<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_RESET_BUTTON' ); ?></button>
				</td>
				<td width="200" style="text-align: right;">
					<?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_FILTER_BY' ); ?>
				  <?php echo $this->state; ?>
				</td>
			</tr>
		</table>
	</div>

	<div class="adminform-body">
		<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="1%"><?php echo JText::_( 'Num' ); ?></th>
				<th width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->categories ); ?>);" /></th>
				<th class="title" style="text-align: left;"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_CATEGORIES_CATEGORY_TITLE' ) , 'title', $this->orderDirection, $this->order ); ?></th>
				<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_PRIVACY' ); ?></th>
				<th width="5%"><?php echo JText::_( 'COM_EASYDSCUSS_CATEGORIES_DEFAULT' ); ?></th>
				<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_PUBLISHED' ); ?></th>
				<th width="8%">
					<?php echo JHTML::_('grid.sort',   JText::_('COM_EASYDISCUSS_ORDER'), 'lft', 'desc', $this->order ); ?>
					<?php echo JHTML::_('grid.order',  $this->categories ); ?>
				</th>
				<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_ENTRIES' ); ?></th>
				<th width="5%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_CHILD_COUNT' ); ?></th>
				<th class="title" width="8%"><?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_CATEGORIES_AUTHOR' ) , 'created_by', $this->orderDirection, $this->order ); ?></th>
				<th class="title" width="1%"><?php echo JText::_('COM_EASYDISCUSS_PREVIEW');?></th>
				<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_ID' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( $this->categories )
		{

			$k = 0;
			$x = 0;
			$rows   = $this->categories;
			$originalOrders = array();
			for ($i=0, $n=count($rows); $i < $n; $i++)
			{
				$row = $this->categories[$i];

				$link 			= 'index.php?option=com_easydiscuss&amp;controller=category&amp;task=edit&amp;catid='. $row->id;
				$previewLink	= JURI::root() . 'index.php?option=com_easydiscuss&amp;view=categories&layout=listing&id=' . $row->id;
				$published 	= JHTML::_('grid.published', $row, $i );
				$user		= JFactory::getUser( $row->created_by );

				$orderkey	= array_search($row->id, $this->ordering[$row->parent_id]);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
				<td><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
				<td align="left">
					<?php echo str_repeat( '|&mdash;' , $row->depth ); ?>
					<span class="editlinktip hasTip"><a href="<?php echo $link; ?>"><?php echo $row->title; ?></a></span>
				</td>
				<td align="center">
					<?php
					    if( $row->private == '2' )
					        echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL');
					    else if( $row->private == '1' )
					    	echo JText::_('COM_EASYDISCUSS_CATEGORIES_PRIVATE');
					    else
					        echo JText::_('COM_EASYDISCUSS_CATEGORIES_PUBLIC');
					?>
				</td>
				<td align="center">
					<?php if( $row->default ){ ?>
						<img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easydiscuss/assets/images/default.png" />
					<?php } else { ?>
						<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=category&task=makeDefault&cid=' . $row->id );?>"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easydiscuss/assets/images/nodefault.png" /></a>
					<?php } ?>
				</td>
				<td align="center">
					<?php echo $published; ?>
				</td>
				<td class="order">

					<?php if ($saveOrder) : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$row->parent_id][$orderkey - 1]), 'orderup', 'Move Up', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$row->parent_id][$orderkey + 1]), 'orderdown', 'Move Down', $ordering); ?></span>
					<?php endif; ?>


					<?php $disabled = 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text_area" style="text-align: center" />
					<?php $originalOrders[] = $orderkey + 1; ?>
				</td>
				<td align="center">
					<?php echo $row->count;?>
				</td>
				<td align="center">
					<?php echo $row->child_count; ?>
				</td>
				<td align="center">
					<span class="editlinktip hasTip" title="<?php echo JText::_( 'Edit category' );?>::<?php echo $row->title; ?>">
						<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo $user->name; ?></a>
					</span>
				</td>
				<td align="center"><a href="<?php echo $previewLink; ?>" target="_blank" class="preview"><?php echo JText::_('COM_EASYDISCUSS_PREVIEW');?></a></td>
				<td align="center"><?php echo $row->id;?></td>
			</tr>
			<?php $k = 1 - $k; } ?>
		<?php
		}
		else
		{
		?>
			<tr>
				<td colspan="12" align="center">
					<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_NO_CATEGORY_CREATED_YET');?>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="categories" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="category" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->orderDirection; ?>" />
	<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
