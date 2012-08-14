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
function insertMember( id , name )
{
	admin.category.acl.addpaneluser(id, name);
	
	<?php
	if( DiscussHelper::getJoomlaVersion() >= '1.6')
	{
	?>
		window.parent.SqueezeBox.close();
	<?php
	}
	else
	{
	?>
		window.parent.document.getElementById('sbox-window').close();
	<?php
	}
	?>
}

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
<table width="100%">
    <tr>
        <td width="50%" valign="top">
        	<fieldset>
        		<legend><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_SETTINGS' ); ?></legend>
				<table class="admintable">
					<tr>
						<td class="key">
							<label for="catname"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_NAME' ); ?>:</label>
						</td>
						<td>
							<input class="inputbox" id="catname" name="title" size="55" maxlength="255" value="<?php echo $this->cat->title;?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="alias"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_ALIAS' ); ?>:</label>
						</td>
						<td>
							<input class="inputbox" id="alias" name="alias" size="55" maxlength="255" value="<?php echo $this->cat->alias;?>" />
						</td>
					</tr>
					<tr>
					    <td class="key"><label for="parent_id"><?php echo JText::_('COM_EASYDISCUSS_PARENT_CATEGORY'); ?>:</label></td>
						<td><?php echo $this->parentList; ?></td>
					</tr>
					<tr>
			        	<td class="key" valign="top"><label for="private"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_PRIVACY'); ?>:</label></td>
			        	<td valign="top">
			        	    <select name="private" id="private" class="inputbox">
			        	        <option value="0" <?php echo ($this->cat->private == '0') ? 'selected="selected"' : '' ?> >Public</option>
			        	        <option value="1" <?php echo ($this->cat->private == '1') ? 'selected="selected"' : '' ?> >Private</option>
			        	        <option value="2" <?php echo ($this->cat->private == '2') ? 'selected="selected"' : '' ?> >Use Category ACL</option>
			        	    </select>
			        	    <div class="notice" style="text-align:left;margin-top:10px;width: 300px;display:block;">
								<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_PRIVACY_DESC'); ?>
			        	    </div>
						</td>
					</tr>
					<?php if($this->config->get('layout_categoryavatar', true)) : ?>
					<tr>
			        	<td class="key"><label for="Filedata"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_EDIT_AVATAR'); ?>:</label></td>
						<td>
						    <?php if(! empty($this->cat->avatar)) { ?>
								<div>
									<img style="border-style:solid; float:none;" src="<?php echo $this->cat->getAvatar(); ?>" width="60" height="60"/>
								</div>
								<div>
									[ <a href="index.php?option=com_easydiscuss&controller=category&task=removeAvatar&id=<?php echo $this->cat->id;?>&<?php echo JUtility::getToken();?>=1"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE_AVATAR' ); ?></a> ]
								</div>
						    <?php } ?>
						    	<div style="margin-top:5px;">
									<input id="file-upload" type="file" name="Filedata" class="inputbox" size="33"/>
								</div>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td class="key"><label for="published"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_PUBLISHED' ); ?>:</label></td>
						<td>
						    <?php echo $this->renderCheckbox( 'published' , $this->cat->published );?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="created"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_CREATED' ); ?>:</label></td>
						<td><?php echo JHTML::_('calendar', $this->cat->created , "created", "created"); ?></td>
					</tr>
					<tr>
						<td class="key"><label for="show_description"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_SHOW_DESCRIPTION' ); ?>:</label></td>
						<td>
						    <?php echo $this->renderCheckbox( 'show_description' , $this->cat->getParam( 'show_description' , true ) );?>
						</td>
					</tr>
					<tr>
						<td class="key" style="vertical-align:top;"><label for="description"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_CATEGORY_DESCRIPTION' ); ?>:</label></td>
						<td valign="top">
							<textarea id="description" name="description" class="inputbox full-width" cols="65" rows="15"><?php echo $this->cat->description; ?></textarea>
						</td>
					</tr>
				</table>
			</fieldset>
		</td>
        <td width="50%" valign="top">
        	<fieldset>
        		<legend><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORY_POST_PARAMETERS' ); ?></legend>
				<table class="admintable">
					<tr>
						<td class="key">
							<label for="maxlength"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH' ); ?>:</label>
						</td>
						<td>
							<?php echo $this->renderCheckbox( 'maxlength' , $this->cat->getParam( 'maxlength' , false ) );?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="maxlength_size"><?php echo JText::_( 'COM_EASYDISCUSS_CATEGORIES_EDIT_POST_MAX_LENGTH_SIZE' ); ?>:</label>
						</td>
						<td>
							<input type="text" class="inputbox" size="3" name="maxlength_size" id="maxlength_size" value="<?php echo $this->cat->getParam( 'maxlength_size' , 1000 );?>" />
							<span><?php echo JText::_( 'COM_EASYDISCUSS_CHARACTERS' ); ?></span>
						</td>
					</tr>
				</table>
			</fieldset>
        </td>
	</tr>
</table>
<input type="hidden" name="savenew" id="savenew" value="0" />