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

$ordering 		= ($this->order == 'lft');
$saveOrder 		= ($this->order == 'lft' && $this->orderDirection == 'asc');
$originalOrders = array();
?>

<script type="text/javascript">
<?php if(DiscussHelper::getJoomlaVersion() >= 1.6) : ?>
	Joomla.submitbutton = function( action ) {
		if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_POSTS', true); ?>')) {
			Joomla.submitform( action );
		}
	}
<?php else : ?>
function submitbutton( action )
{
	if ( action != 'remove' || confirm('<?php echo JText::_('COM_EASYDISCUSS_CONFIRM_DELETE_POSTS', true); ?>')) {
		submitform( action );
	}
}
<?php endif; ?>
</script>

<script type="text/javascript">
<?php if(DiscussHelper::getJoomlaVersion() >= 1.6) : ?>
Joomla.submitbutton = function( action ) {
    if( action == 'purge')
    {
		if( !purgeConfirm() )
		{
			return false;
		}
	}

	if( action == 'remove' )
	{
		if( !deleteConfirm() )
		{
			return false;
		}
	}
	Joomla.submitform( action );
}
<?php else : ?>
function submitbutton( action )
{
    if( action == 'purge')
    {
		if( !purgeConfirm() )
		{
			return false;
		}
	}

	if( action == 'remove' )
	{
		if( !deleteConfirm() )
		{
			return false;
		}
	}
	submitform( action );
}
<?php endif; ?>

function deleteConfirm()
{
	if( confirm( '<?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_CONFIRM_DELETE');?>' ) )
	{
		return true;
	}
	return false;
}

function purgeConfirm()
{
	if( confirm( '<?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_CONFIRM_PURGE');?>' ) )
	{
		return true;
	}
	return false;
}
</script>
<form action="index.php" method="post" name="adminForm">
<div class="adminform-head">
    <table class="adminform">
    	<tr>
    		<td width="50%">
    		  	<label><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH' ); ?> :</label>
    			<input type="text" name="search" id="search" value="<?php echo $this->search; ?>" class="inputbox" onchange="document.adminForm.submit();" />
    			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_SUBMIT_BUTTON' ); ?></button>
    			<button onclick="this.form.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'COM_EASYDISCUSS_RESET_BUTTON' ); ?></button>
    		</td>
    		<td width="50%" style="text-align: right;">
                <label><?php echo JText::_( 'COM_EASYDISCUSS_FILTER_BY' ); ?> :</label>
                <?php echo $this->state; ?>
    		</td>
    	</tr>
    </table>
</div>
<div class="adminform-body">
<p class="small"><?php echo JText::_( 'COM_EASYDISCUSS_SPOOLS_CRON_TIPS' ); ?> <a href="http://stackideas.com/docs/easydiscuss/cronjobs" target="_blank"><?php echo JText::_( 'COM_EASYDISCUSS_HOW_TO_SETUP_CRON' );?></a></p>
<table class="adminlist" cellspacing="1">
<thead>
	<tr>
		<th width="1%"><?php echo JText::_( 'Num' ); ?></th>
		<th width="1%"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->mails ); ?>);" /></th>
		<th class="title" style="text-align: left;" width="10%"><?php echo JText::_( 'COM_EASYDISCUSS_RECIPIENT' ); ?></th>
		<th><?php echo JText::_( 'COM_EASYDISCUSS_SUBJECT' ); ?></th>
		<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_STATE' ); ?></th>
		<th width="10%" nowrap="nowrap"><?php echo JText::_( 'COM_EASYDISCUSS_CREATED' ); ?></th>
		<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_ID' ); ?></th>
	</tr>
</thead>
<tbody>
<?php
if( $this->mails )
{

	$k = 0;
	$x = 0;
	for ($i=0, $n=count($this->mails); $i < $n; $i++)
	{
		$row		= $this->mails[$i];
	?>
	<tr class="<?php echo "row$k"; ?>">
		<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
		<td><?php echo JHTML::_('grid.id', $x++, $row->id); ?></td>
		<td><?php echo $row->recipient;?></td>
		<td>
			<?php echo $row->subject;?>
		</td>
		<td style="text-align:center;">
			<?php if( $row->status ){ ?>
				<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/assets/images/tick.png" title="<?php echo JText::_( 'COM_EASYDISCUSS_SENT' );?>">
			<?php } else { ?>
				<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/assets/images/schedule.png" title="<?php echo JText::_( 'COM_EASYDISCUSS_PENDING' );?>">
			<?php } ?>
		</td>
		<td style="text-align:center;"><?php echo $row->created; ?></td>
		<td align="center"><?php echo $row->id;?></td>
	</tr>
	<?php $k = 1 - $k; } ?>
<?php
}
else
{
?>
	<tr>
		<td colspan="7" align="center">
			<?php echo JText::_('COM_EASYDISCUSS_NO_MAILS');?>
		</td>
	</tr>
<?php
}
?>
</tbody>

<tfoot>
	<tr>
		<td colspan="7">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
</tfoot>
</table>
</div>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="view" value="spools" />
<input type="hidden" name="controller" value="spools" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
