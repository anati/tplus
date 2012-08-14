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
function appendLog( type , message )
{
	Foundry( '#migrator-' + type + '-log' ).append( '<li>' + message + '</li>');
}

function runMigration( type )
{
	// Hide migration button.
	Foundry( '.migrator-button' ).hide();

	disjax.load( 'migrators' , type );
}

function runMigrationCategory( type , categories )
{
	// Removes the first element
	var current	= categories.shift();

	if( categories.length == 0 && !current )
	{
		return;
	}

	if( categories.length == 0 )
	{
		categories	= 'done';
	}

	disjax.load( 'migrators' , type + 'CategoryItem' , current , categories );
}

function runMigrationItem( type , items )
{
	var current	= items.shift();

	if( items.length == 0 )
	{
		items	= 'done';
	}

	disjax.load( 'migrators' , type + 'PostItem' , current , items );
}
</script>
<form name="adminForm">
<table width="100%" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<td valign="top" width="50%">
			<fieldset>
				<legend><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' );?></legend>
				<?php if( $this->kunenaExists() ){ ?>
				<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_DESC' );?></p>
				<ul>
					<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_BACKUP' ); ?></li>
					<li><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_NOTICE_OFFLINE' ); ?></li>
				</ul>
				<?php } else { ?>
				<p><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_NOT_INSTALLED' ); ?></p>
				<?php } ?>
				<input type="button" class="button migrator-button" onclick="runMigration( 'kunena' );" value="<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_RUN_MIGRATION_TOOL' );?>" />
			</fieldset>
		</td>
		<td valign="top">
			<fieldset>
				<legend><?php echo JText::_( 'COM_EASYDISCUSS_PROGRESS' ); ?></legend>
				<ul id="migrator-kunena-log" style="max-height: 170px; overflow-y:scroll;list-style:none;">
				</ul>
			</fieldset>
		</td>
	</tr>
</tbody>
</table>
</form>
