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

<form id="dc_edit" name="dc_edit" method="post">
	<table width="100%" class="form">
		<tr>
			<td class="value">
				<input type="text" class="inputbox fullwidth" id="edit_title" name="edit_title" value="<?php echo $post->title; ?>" />
			</td>
		</tr>
        
		
		<tr>
			<td class="key">
				<label for="content"><?php echo JText::_('COM_EASYDISCUSS_CONTENT'); ?></label>
			</td>
		</tr>
        
        
		<tr>
			<td class="value">
				<textarea id="edit_content" name="edit_content" class="textarea fullwidth"><?php echo $post->content; ?></textarea>
			</td>
		</tr>
        
		
		<tr>
			<td class="key">
				<?php
					if(!empty($tags))
					{
		  		?>
	    			<div class="key"><?php echo JText::_('COM_EASYDISCUSS_HOT_TAGS'); ?></div>
	    			<div id="tags-container">
		  			<?php
	  					foreach($tags as $tag)
	  					{
	  						$tagTitle = JString::trim($tag->title);
						?>
							<div id="<?php echo $tag->id; ?>" class="tag-list" style="float: left;margin-right: 10px;"><a href="javascript:void(0)" onclick="discuss.post.tags.addexisting('<?php echo $tagTitle; ?>')"><?php echo $tagTitle; ?></a></div>
						<?php
						}
					?>
					</div>
				<?php
				}
				?>
			</td>
		</tr>
        
        
		<tr>
			<td class="value">
			    <?php if($config->get('main_allowcreatetag', 1)) : ?>
			    <div class="key" style="margin-top:20px;>
					<label for="tags"><?php echo JText::_('COM_EASYDISCUSS_TAGS'); ?></label>
				<div>
				<input type="text" class="inputbox fullwidth" id="tags" name="tags" />
				<input type="button" id="name_tags" id="add_tags" value="<?php echo JText::_('COM_EASYDISCUSS_ADD_TAGS'); ?>" onclick="discuss.post.tags.add()" />
				<?php endif; ?>
				<div id="taglist">
					<ul id="tag_items">
					<?php if( $post->tags ) { ?>
						<?php foreach( $post->tags as $tag ) : ?>
						<li class="tag_item" id="tag_<?php echo str_replace(' ', '-', $tag->title); ?>">
							<a class="remove_tag" href="javascript:void(0);" onclick="discuss.post.tags.remove('<?php echo str_replace(' ', '-', $tag->title);?>');"><span><?php echo JText::_('X'); ?></span></a>
							<span class="tag_caption"><?php echo $tag->title; ?></span>
							<input type="hidden" name="tags" value="<?php echo $tag->title;?>" />
						</li>
					<?php endforeach; ?>
					<?php } else { ?>
						<li><?php echo JText::_('COM_EASYDISCUSS_NO_TAGS_AVAILABLE'); ?></li>
					<?php } ?>	
					</ul>
				</div>
			</td>
		</tr>
		<?php if(! empty($recaptcha)) { ?>
		<tr>
			<td class="value">
			    <?php echo $recaptcha; ?>
		    </td>
		</tr>
		<?php } ?>
		<tr>
			<td class="value">
				<input type="hidden" name="id" id="id" value="<?php echo $post->id; ?>" />
				<input type="hidden" name="parent_id" id="parent_id" value="0" />
				<input type="hidden" name="isresolve" id="isresolve" value="<?php echo $post->isresolve; ?>" />
				<input type="button" name="submit" id="submit" class="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT'); ?>" onclick="discuss.post.submitEditPost();return false;" />
				<input type="button" name="cancel" id="Cancel" class="button" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?>" onclick="discuss.post.cancelEditPost();return false;" />
			</td>
		</tr>
	</table>
</form>