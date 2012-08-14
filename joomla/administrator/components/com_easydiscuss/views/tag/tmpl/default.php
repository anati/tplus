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
<?php if( DiscussHelper::getJoomlaVersion() >= 1.6) : ?>
Joomla.submitbutton = function( action ) {

	if( action == 'save' || action == 'savePublishNew' )
	{
		if( action == 'savePublishNew' )
		{
			action = 'save';
			Foundry( '#savenew' ).val( '1' );
		}
	}
	Joomla.submitform( action );
}
<?php else : ?>
function submitbutton( action )
{
	if( action == 'save' || action == 'savePublishNew' )
	{
		if( action == 'savePublishNew' )
		{
			action = 'save';
			Foundry( '#savenew' ).val( '1' );
		}
	}
	submitform( action );
}
<?php endif; ?>
</script>
<div class="adminform-body">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table cellspacing="0" cellpadding="0" border="0" width="100%">
		<tr>
			<td valign="top" width="50%">
				<fieldset class="adminform">
				<legend><?php echo JText::_('COM_EASYDISCUSS_TAG'); ?></legend>
				<table class="admintable">
					<tr>
						<td class="key">
							<label for="catname">
								<?php echo JText::_( 'COM_EASYDISCUSS_TAG' ); ?>
							</label>
						</td>
						<td>
							<input class="inputbox" name="title" size="55" maxlength="255" value="<?php echo $this->tag->title;?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="alias">
								<?php echo JText::_( 'COM_EASYDISCUSS_TAG_ALIAS' ); ?>
							</label>
						</td>
						<td>
							<input class="inputbox" name="alias" size="55" maxlength="255" value="<?php echo $this->tag->alias;?>" />
						</td>
					</tr>
					<tr>													
						<td class="key">
							<label for="published"><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' ); ?></label>
						</td>
						<td>
							<?php echo $this->renderCheckbox( 'published' , $this->tag->published ); ?>
						</td>
					</tr>
					<tr style="display: none;">
						<td>
							<label for="created">
								<?php echo JText::_( 'Created' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('calendar', $this->tag->created , "created", "created"); ?>
						</td>
					</tr>
				</table>
				</fieldset>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="tags" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="tagid" value="<?php echo $this->tag->id;?>" />
	<input type="hidden" name="savenew" id="savenew" value="0" />
</form>
</div>