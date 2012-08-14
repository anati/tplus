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

$references 	= false;

if( $postObj )
{
	$references 	= $this->getFieldData( 'references' , $postObj->params );
}

if( $system->config->get( 'reply_field_references' ) ){
?>
<div class="form-tab-item tab-references" style="display:none">
	<div class="field-references">
		<ul class="attach-list reset-ul mb-10">
		<?php if( isset( $references ) && $references ){ ?>
			<?php
				$total  = count( $references );

				for( $i = 0; $i < $total; $i++ )
				{
			?>
			<li>
				<div>
					<input type="text" name="params_references[]" class="input" value="<?php echo $references[ $i ]; ?>" />
				</div>
				<?php if( $i != 0 ){ ?>
					<a href="javascript:void(0);" onclick="discuss.reply.removeURL(this);" id="remove-url" class="remove-att pos-a ir"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' ); ?></a>
				<?php } else { ?>
					<a href="javascript:void(0);" onclick="discuss.reply.removeURL(this);" style="display: none;" id="remove-url" class="remove-att pos-a ir"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' ); ?></a>
				<?php } ?>
			</li>
			<?php } ?>
		<?php } else { ?>
			<li>
				<div>
					<input type="text" name="params_references[]" class="input" />
				</div>
				<a href="javascript:void(0);" onclick="discuss.reply.removeURL(this);" style="display: none;" id="remove-url" class="remove-att pos-a ir"><?php echo JText::_( 'COM_EASYDISCUSS_REMOVE' ); ?></a>
			</li>
		<?php } ?>
		</ul>
		<a href="javascript:void(0);" class="button-link add-url" onclick="discuss.reply.addURL(this);"><?php echo JText::_( 'COM_EASYDISCUSS_REFERENCES_ADD_LINK_BUTTON' );?></a>
	</div>
</div>
<?php } ?>