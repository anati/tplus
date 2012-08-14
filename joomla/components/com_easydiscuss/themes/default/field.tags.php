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

$tagForm_mode = ($config->get('main_allowcreatetag', 1) && $acl->allowed('add_tag', '0')) ? 'entry' : 'picker';
?>

<script type="text/javascript">
EasyDiscuss.require(
[
	'easydiscuss.controllers.tagform'
],
function($)
{
	var tagForm = '#dc_tag';

	$(tagForm)
		.implement(
			'EasyDiscuss.Controllers.TagForm',
			{
				mode: '<?php echo $tagForm_mode ?>',
				dataset: <?php echo $this->json_encode($tags); ?>,
				tags: <?php echo $this->json_encode($post->tags); ?>,

				lang: {
					'COM_EASYDISCUSS_POST_NO_TAGS_ASSIGNED_YET': '<?php echo JText::_('COM_EASYDISCUSS_POST_NO_TAGS_ASSIGNED_YET'); ?>'
				}
			},
			function()
			{
				this.textField()
					.stretchToFit();
			});
});
</script>

<div id="dc_tag" class="tagform">
	<div class="input-wrap mb-10">
		<input type="text" class="input tagform-textfield" style="display: none;" value="" />

	    <?php if ($tagForm_mode=='picker'): ?>
		<label class="select-tag"><?php echo JText::_('COM_EASYDISCUSS_SELECT_A_TAG'); ?></label>
	    <ul class="tagform-pickerfield reset-ul float-li clearfix">
	    </ul>
	    <?php endif; ?>
    </div>
    <div class="tagform-taglist">
    	<small class="tagform-taglist-message"></small>
    	<ul class="tagform-tagitemgroup reset-ul float-li clearfix">
    	</ul>
    </div>
</div>
