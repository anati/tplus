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

<div class="field-polls pa-10 bg-ff b-sc bt-0">
	<div class="poll-check clearfix">
		<input type="checkbox" class="input float-l" name="polls" id="polls" value="1" onchange="discuss.polls.show();"<?php echo isset( $polls ) && !empty( $polls ) ? ' checked="checked"' :'';?> />
		<label for="polls" class="fs-11 float-l ml-5"><?php echo JText::_( 'COM_EASYDISCUSS_INSERT_POLL' );?></label>
	</div>
	<div id="discuss-polls" class="poll-area mt-10"<?php echo isset( $polls ) && !empty( $polls ) ? '' : ' style="display:none"';?>>
		<ul class="polls-list attach-list reset-ul mb-10">
	<?php if( isset( $polls ) && $polls ){ ?>
		<?php
			$total  = count( $polls );

			for( $i = 0; $i < $total; $i++ )
			{
		?>
		<li>
			<div>
				<input type="text" name="pollitems[]" class="input width-400" value="<?php echo $polls[ $i ]->value; ?>" />
			</div>
			<?php if( $i != 0 ){ ?>
				<a href="javascript:void(0);" onclick="discuss.polls.remove(this,'<?php echo $polls[ $i ]->id;?>' );" id="remove-url" class="remove-att pos-a ir"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' ); ?></a>
			<?php } else { ?>
				<a href="javascript:void(0);" onclick="discuss.polls.remove(this,'<?php echo $polls[ $i ]->id;?>' );" style="display: none;" id="remove-url" class="remove-att pos-a ir"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' ); ?></a>
			<?php } ?>
		</li>
		<?php } ?>
	<?php } else { ?>
		<li>
			<div>
				<input type="text" name="pollitems[]" class="input width-400" />
			</div>
			<a href="javascript:void(0);" onclick="discuss.polls.remove(this);" style="display: none;" id="remove-poll" class="remove-att pos-a ir"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' ); ?></a>
		</li>
	<?php } ?>
	</ul>
	<a href="javascript:void(0);" class="button-link add-poll" onclick="discuss.polls.insert(this);" ><?php echo JText::_( 'COM_EASYDISCUSS_ADD_ITEM_BUTTON' );?></a>
	</div>
	<input type="hidden" value="1" id="poll-item-count" />
	<input type="hidden" name="pollsremove" id="pollsremove" value="" />
</div>
