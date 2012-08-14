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

$itemCnt = 1;

?>
<script type="text/javascript">

var COM_EASYDISCUSS_RANKING_DELETE = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_DELETE'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ENTER_TITLE'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ONLY_NUMBER'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_GREATER_THAN_ZERO'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_END_CANNOT_SMALLER_THAN_START'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_CANNOT_HAVE_GAPS'); ?>';
var COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT = '<?php echo JText::_('COM_EASYDISCUSS_RANKING_ERR_ALL_VALUE_IS_CORRECT'); ?>';


function showDescription( id )
{
	Foundry( '.rule-description' ).hide();
	Foundry( '#rule-' + id ).show();
}

<?php if(DiscussHelper::getJoomlaVersion() >= 1.6){ ?>
	Joomla.submitbutton = function( action ) {
		if( action == 'cancel' )
		{
			redirectCancel();
			return;
		}
		Joomla.submitform( action );
	}
<?php } else { ?>
function submitbutton( action )
{
	if( action == 'cancel' )
	{
		redirectCancel();
		return;
	}

	submitform( action );
}

function redirectCancel()
{
	window.location.href	= 'index.php?option=com_easydiscuss&view=ranks';
}
<?php } ?>
</script>

<style type="text/css">
.input-error {border-color:red !important;}
</style>

<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
<table width="100%" cellspacing="0" cellpadding="5">
<tr>
	<td width="50%" valign="top">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_RANKING' );?></legend>
			
			<div style="padding-bottom: 10px;">
			<?php if(! $this->config->get('main_ranking') ) : ?>
			    <?php echo JText::_('COM_EASYDISCUSS_RANKING_DISABLED_BY_ADMIN'); ?>
			<?php else : ?>
			    <?php $rankingType  = ( $this->config->get('main_ranking_calc_type') == 'points' ) ? JText::_('COM_EASYDISCUSS_RANKING_POINTS') : JText::_('COM_EASYDISCUSS_RANKING_POSTS') ;  ?>
				<?php echo JText::sprintf('COM_EASYDISCUSS_RANKING_NOTE', $rankingType ); ?>
			<?php endif; ?>
			</div>
			
			
			
			<table class="admintable" padding="3">
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_RANKING_TITLE' );?></td>
					<td valign="top">
						<input type="text" class="input-full inputbox" id="newtitle" name="newtitle" value="" />
					</td>
					<td valign="top">
						<input type="button" onclick="admin.rank.add();" value="<?php echo JText::_('COM_EASYDISCUSS_RANKING_ADD'); ?>" />
					</td>
				</tr>
			</tbody>
			</table>
			<div style="padding-bottom: 15px;"></div>
			<div id="sys-msg" style="color:red;"></div>
			<div style="padding-bottom: 15px;"></div>
			<table class="adminlist" cellspacing="1">
			<thead>
				<tr>
					<th width="5"><?php echo JText::_( 'Num' ); ?></th>
					<th class="title" style="text-align: center;"><?php echo JText::_('COM_EASYDISCUSS_RANKING_TITLE'); ?></th>
					<th style="text-align: center;"><?php echo JText::_('COM_EASYDISCUSS_RANKING_START_POINT'); ?></th>
					<th style="text-align: center;"><?php echo JText::_('COM_EASYDISCUSS_RANKING_END_POINT'); ?></th>
					<th width="20" style="text-align: center;">&nbsp;</th>
				</tr>
			</thead>
			<tbody id="rank-list">
			<?php if( count( $this->ranks ) > 0 ) : ?>
				<?php
				    $i = 1;
					foreach( $this->ranks as $rank) { ?>
				<tr id="rank-<?php echo $rank->id; ?>">
					<td>
						<?php echo $i++; ?>
						<input type="hidden" name="id[]" value="<?php echo $rank->id; ?>" />
					</td>
					<td style="text-align: center;"><input onchange="admin.rank.checktitle(this)" type="text" name="title[]" value="<?php echo $rank->title; ?>" class="input-full inputbox"/></td>
					<td style="text-align: center;"><input onchange="admin.rank.checkvalue(this)" style="text-align: center;" type="text" name="start[]" value="<?php echo $rank->start; ?>" class="input-full inputbox"/></td>
					<td style="text-align: center;"><input onchange="admin.rank.checkvalue(this)" style="text-align: center;" type="text" name="end[]" value="<?php echo $rank->end; ?>" class="input-full inputbox"/></td>
					<td style="text-align: center;"><a href="javascript:void(0);" onclick="admin.rank.remove(<?php echo $rank->id; ?>)"><?php echo JText::_('COM_EASYDISCUSS_RANKING_DELETE'); ?></a></td>
				</tr>
				<?php
				        $itemCnt = $rank->id;
					}//end foreach
				?>
				
			<?php else : ?>
			<tr><td colspan="5"><?php echo JText::_('COM_EASYDISCUSS_RANKING_EMPTY_LIST'); ?></td></tr>
			<?php endif; ?>
			</tbody>
			</table>
		</fieldset>
	</td>
	<td width="50%" valign="top">
	</td>
</tr>
</table>
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="ranks" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" value="<?php echo ++$itemCnt; ?>" id="itemCnt" name="itemCnt" />
<input type="hidden" value="" id="itemRemove" name="itemRemove" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
