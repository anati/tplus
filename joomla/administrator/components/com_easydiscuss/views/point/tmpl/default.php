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
	window.location.href	= 'index.php?option=com_easydiscuss&view=points';
}
<?php } ?>
</script>
<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data">
<table width="100%" cellspacing="0" cellpadding="5">
<tr>
	<td width="50%" valign="top">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' );?></legend>
			<table class="admintable" padding="3">
			<tbody>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_POINT_TITLE' );?></td>
					<td valign="top">
						<input type="text" class="input-full inputbox" name="title" value="<?php echo $this->point->get( 'title' );?>" />
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' );?></td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'published' , $this->point->get( 'published' , 1 ) );?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_CREATION_DATE' );?></td>
					<td valign="top">
						<?php echo JHTML::_('calendar', $this->point->created , "created", "created"); ?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_POINT_ACTION' );?></td>
					<td valign="top">
						<select name="rule_id" onchange="showDescription( this.value );" class="inputbox">
							<option value="0"<?php echo !$this->point->get( 'rule_id' ) ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SELECT_RULE' );?></option>
						<?php foreach( $this->rules as $rule ){ ?>
							<option value="<?php echo $rule->id;?>"<?php echo $this->point->get( 'rule_id' ) == $rule->id ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
						<?php } ?>
						</select>
						<?php foreach( $this->rules as $rule ){ ?>
						<div id="rule-<?php echo $rule->id;?>" class="rule-description" style="display:none;"><?php echo $rule->description;?></div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_POINTS_GIVEN' );?></td>
					<td valign="top">
						<input type="text" name="rule_limit" class="inputbox" size="8" style="text-align: center;" value="<?php echo $this->point->get( 'rule_limit'); ?>" />
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</td>
	<td width="50%" valign="top">&nbsp;</td>
</tr>
</table>
<input type="hidden" name="id" value="<?php echo $this->point->id; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="points" />
<input type="hidden" name="option" value="com_easydiscuss" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
