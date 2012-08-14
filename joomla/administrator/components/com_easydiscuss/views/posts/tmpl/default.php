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

	<div class="adminform-body">

	<?php if(! empty($this->parentId)) { ?>
	<div><h2><?php echo JText::sprintf( 'COM_EASYDISCUSS_DISCUSSION_PARENT_POST_TITLE', $this->parentTitle ); ?></h2></div>
	<?php } else { ?>
		<div class="notice" style="text-align: left !important;"><?php echo JText::_('COM_EASYDISCUSS_DISCUSSION_EDIT_NOTICE');?></div>
	<?php } ?>

	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5">
				<?php echo JText::_( 'Num' ); ?>
			</th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->posts ); ?>);" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $this->orderDirection, $this->order ); ?></th>
			<th width="1%"><?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'Published' ); ?></th>
			<th width="3%" nowrap="nowrap"><?php echo JText::_( 'Hits' );?></th>
			<th width="10%" nowrap="nowrap"><?php echo JText::_( 'User' ); ?></th>
			<?php if(empty($this->parentId)) : ?>
			<th width="10%" nowrap="nowrap"><?php echo JText::_( 'Replies' ); ?></th>
			<?php endif; ?>
			<th width="10%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('Date'), 'a.created', $this->orderDirection, $this->order ); ?></th>
			<th width="20" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $this->orderDirection, $this->order ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	if( $this->posts )
	{
		$k = 0;
		$x = 0;
		$config	= JFactory::getConfig();
		for ($i=0, $n = count( $this->posts ); $i < $n; $i++)
		{
			$row 			= $this->posts[$i];
			$creatorName    = '';

			if($row->user_id == '0')
			{
			    $creatorName = $row->poster_name;
			}
			else
			{
			    $user		 = JFactory::getUser( $row->user_id );
			    $creatorName = $user->name;
			}

			$pid    = '';
			if(!empty($this->parentId))
			{
			    $pid = '&pid=' . $this->parentId;
			}

			// frontend link
			$editLink	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=ask&id='.$row->id, false, true);

			$date		= JFactory::getDate( $row->created );

			$date->setOffset(  $config->getValue('offset')  );

			// display only safe content.
			$row->content   = strip_tags( $row->content );
		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo $this->pagination->getRowOffset( $i ); ?>
			</td>
			<td width="7">
				<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
			</td>
			<td align="left">
				<span class="editlinktip">
					<?php if( empty( $this->parentId ) ) { ?>
						<a href="<?php echo $editLink; ?>" target="_BLANK"><?php echo $row->title; ?></a>
					<?php } else { ?>
						<?php echo $row->title; ?>
					<?php } ?>
					<?php if( !empty( $this->parentId ) ) echo '<br/><br/>' . $row->content; ?>
				</span>
			</td>
			<td align="center">
				<?php if( DiscussHelper::getJoomlaVersion() <= '1.5' ){ ?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo ( $row->featured ) ? 'unfeature' : 'feature';?>')">
						<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/assets/images/<?php echo ( $row->featured ) ? 'small_default.png' : 'small_default-x.png';?>" width="16" height="16" border="0" />
					</a>
				<?php } else { ?>
					<?php echo JHTML::_( 'grid.boolean' , $i , $row->featured , 'feature' , 'unfeature' ); ?>
				<?php } ?>
			</td>
			<td align="center">
				<?php if( $row->published == DISCUSS_ID_PENDING){ ?>
				    <a href="javascript:void(0);" onclick="admin.post.moderate.dialog('<?php echo $row->id;?>');"><img src="<?php echo rtrim( JURI::root() , '/' );?>/administrator/components/com_easydiscuss/assets/images/moderate.png" /></a>
				<?php } else { ?>
				    <?php echo JHTML::_('grid.published', $row, $i ); ?>
				<?php } ?>
			</td>
			<td align="center">
				<?php echo $row->hits; ?>
			</td>
			<td align="center">
				<span class="editlinktip hasTip">
					<!-- a href="<?php echo JRoute::_('index.php?option=com_users&cid[]=' . $row->user_id . '&task=edit'); ?>"></a -->
					<?php echo $creatorName ?>
				</span>
			</td>
			<?php if(empty($this->parentId)) : ?>
				<td align="center">
				<?php if($row->cnt > 0) : ?>
				    <a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=posts&pid=' . $row->id ); ?>">
						<?php echo DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_POSTS_REPLY' , $row->cnt , true ); ?>
						<?php if( $row->pendingcnt > 0) : ?>
							( <?php echo DiscussHelper::getHelper( 'String' )->getNoun( 'COM_EASYDISCUSS_POSTS_PENDING_REPLY' , $row->pendingcnt , true ); ?> )
						<?php endif; ?>
					</a>
				<?php else : ?>
				    <?php echo $row->cnt; ?>
				<?php endif; ?>
				</td>
			<?php endif; ?>
			<td align="center">
				<?php echo $date->toMySQL( true );?>
			</td>
			<td align="center">
				<?php echo $row->id; ?>
			</td>
		</tr>
		<?php $k = 1 - $k; } ?>
	<?php
	}
	else
	{
	?>
		<tr>
			<td colspan="10" align="center">
				<?php echo JText::_('COM_EASYDISCUSS_NO_DISCUSSIONS_YET');?>
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
	<input type="hidden" name="view" value="posts" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="posts" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<input type="hidden" name="pid" value="<?php echo $this->parentId; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
