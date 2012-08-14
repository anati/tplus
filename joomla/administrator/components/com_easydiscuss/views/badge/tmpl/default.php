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

function redirectCancel()
{
	window.location.href	= 'index.php?option=com_easydiscuss&view=badges';
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
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_TITLE' );?></td>
					<td valign="top">
						<input type="text" class="input-full inputbox" name="title" value="<?php echo $this->badge->get( 'title' );?>" />
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_DESCRIPTION' );?></td>
					<td valign="top">
						<textarea class="inputbox input-full" name="description"><?php echo $this->badge->get( 'description' );?></textarea>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_PUBLISHED' );?></td>
					<td valign="top">
						<?php echo $this->renderCheckbox( 'published' , $this->badge->get( 'published' ) );?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_CREATED' );?></td>
					<td valign="top">
						<?php echo JHTML::_('calendar', $this->badge->created , "created", "created"); ?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_ACTION' );?></td>
					<td valign="top">
						<select name="rule_id" onchange="showDescription( this.value );" class="inputbox">
							<option value="0"<?php echo !$this->badge->get( 'rule_id' ) ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SELECT_RULE' );?></option>
						<?php foreach( $this->rules as $rule ){ ?>
							<option value="<?php echo $rule->id;?>"<?php echo $this->badge->get( 'rule_id' ) == $rule->id ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
						<?php } ?>
						</select>
						<?php foreach( $this->rules as $rule ){ ?>
						<div id="rule-<?php echo $rule->id;?>" class="rule-description" style="display:none;"><?php echo $rule->description;?></div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_ACTION_THRESHOLD' );?></td>
					<td valign="top">
						<input type="text" name="rule_limit" class="inputbox" size="8" style="text-align: center;" value="<?php echo $this->badge->get( 'rule_limit'); ?>" />
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</td>
	<td width="50%" valign="top">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_EASYDISCUSS_BADGE' );?></legend>
			<p><?php echo JText::sprintf( 'To upload new badges, you will need to upload them in the folder, <strong>%1s</strong>. Ensure that the size is at 64x64 pixels.' , DISCUSS_BADGES_PATH );?>
			<ul class="badges-list reset-ul float-li clearfix">
				<?php foreach( $this->badges as $badge ){ ?>
					<li class="badge-item<?php echo $this->badge->avatar == $badge ? ' selected-badge' : '';?>">
						<label for="<?php echo $badge;?>">
							<div><img src="<?php echo DISCUSS_BADGES_URI . '/' . $badge;?>" width="48" /></div>
							<input type="radio" value="<?php echo $badge;?>" name="avatar" id="<?php echo $badge;?>"<?php echo $this->badge->avatar == $badge ? ' checked="checked"' : '';?> />
						</label>
					</li>
				<?php } ?>
			</ul>
		</fieldset>
	</td>
</tr>
</table>
<input type="hidden" name="id" value="<?php echo $this->badge->id; ?>" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="controller" value="badges" />
<input type="hidden" name="option" value="com_easydiscuss" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
