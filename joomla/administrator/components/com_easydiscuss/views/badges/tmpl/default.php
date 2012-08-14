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
<?php if(DiscussHelper::getJoomlaVersion() >= 1.6){ ?>
	Joomla.submitbutton = function( action ) {
		if( action == 'rules' )
		{
			redirectRules();
			return;
		}
		Joomla.submitform( action );
	}
<?php } else { ?>
function submitbutton( action )
{
	if( action == 'rules' )
	{
		redirectRules();
		return;
	}
	
	submitform( action );
}
<?php } ?>
function redirectRules()
{
	window.location.href	= 'index.php?option=com_easydiscuss&view=rules&from=badges';
}

</script>

<form action="index.php" method="post" name="adminForm">
	<div class="adminform-head">
		<table class="adminform">
			<tr>
				<td width="40">
				  	<?php echo JText::_( 'SEARCH' ); ?>
				</td>
				<td width="200">
					<input type="text" name="search" id="search" value="<?php echo $this->search; ?>" class="inputbox" onchange="document.adminForm.submit();" style="width: 200px;" />
				</td>
				<td>
					<button onclick="this.form.submit();"><?php echo JText::_( 'SEARCH' ); ?></button>
					<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'RESET' ); ?></button>
				</td>
				<td width="200" nowrap="nowrap" style="text-align: right;">
				  <?php echo $this->state; ?>
				</td>
			</tr>
		</table>
	</div>

	<div class="adminform-head">
		<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_( 'Num' ); ?>
				</th>
				<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->badges ); ?>);" /></th>
				<th class="title" style="text-align: left;"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $this->orderDirection, $this->order ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_ACHIEVERS' ); ?></th>
				<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_THUMBNAIL' ); ?></th>
				<th width="1%" nowrap="nowrap"><?php echo JText::_( 'Published' ); ?></th>
				<th width="10%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'Date', 'a.created', $this->orderDirection, $this->order ); ?></th>
				<th width="20" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		if( $this->badges )
		{
			$k = 0;
			$x = 0;
			$config	= JFactory::getConfig();
			for ($i=0, $n = count( $this->badges ); $i < $n; $i++)
			{
				$row 	= $this->badges[$i];
				$date	= JFactory::getDate( $row->created );
				$date->setOffset(  $config->getValue('offset')  );

				$editLink	= JRoute::_( 'index.php?option=com_easydiscuss&view=badge&id=' . $row->id );
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center">
					<?php echo $this->pagination->getRowOffset( $i ); ?>
				</td>
				<td width="7">
					<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
				</td>
				<td align="left">
					<a href="<?php echo $editLink; ?>"><?php echo $row->title; ?></a>
				</td>
				<td align="center">
					<?php echo $this->getTotalUsers( $row->id ); ?>
				</td>
				<td align="center">
					<img src="<?php echo JURI::root();?>/media/com_easydiscuss/badges/<?php echo $row->avatar;?>" width="16" />
				</td>
				<td align="center">
					<?php echo JHTML::_('grid.published', $row, $i ); ?>
				</td>
				<td align="center">
					<?php echo $date->toMySQL( true );?>
				</td>
				<td align="center"><?php echo $row->id; ?></td>
			</tr>
			<?php $k = 1 - $k; } ?>
		<?php
		}
		else
		{
		?>
			<tr>
			<td colspan="8" align="center">
					<?php echo JText::_('COM_EASYDISCUSS_NO_BADGES_YET');?>
				</td>
			</tr>
		<?php
		}
		?>
		</tbody>

		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="badges" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="badges" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
