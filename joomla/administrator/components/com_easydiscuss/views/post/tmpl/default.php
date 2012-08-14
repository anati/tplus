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
Foundry.run(function($)
{
	EasyDiscuss.ready( function($) {
	 	$( '#dc_reply_content' )
	 		.markItUp( mySettings );
	});

	<?php if($this->joomlaversion >= '1.6') { ?>
		Joomla.submitbutton = function( task ){
			submitAction( task );
		}
	<?php } else { ?>
		function submitbutton( action )
		{
			submitAction( action );
		}
	<?php } ?>

	function submitAction( action )
	{
		setActiveTab();

		if(action == 'cancel')
		{
			admin.post.cancel();
		}
		else if(action == 'submit')
		{
			if(admin.post.validate(false, 'newpost'))
			{
				admin.post.submit();
			}
		}
		else
		{
			<?php if($this->joomlaversion >= '1.6') { ?>
				Joomla.submitform( action );
			<?php } else { ?>
				submitform( action );
			<?php } ?>
		}
	}

	function setActiveTab()
	{
		$('#submenu li').children().each( function(){
			if( $(this).hasClass( 'active' ) )
			{
				$( '#active' ).val( $(this).attr('id') );
			}
		});
	}
});
</script>
<div id="dc_post_notification"><div class="msg_in"></div></div>
<form id="adminForm" name="adminForm" action="index.php" method="post" enctype="multipart/form-data" class="adminform-body">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td valign="top">
			<table class="admintable">
				<tbody>
					<tr>
						<td>
							<label for="title"><?php echo JText::_( 'Title' );?></label>
						</td>
						<td>
							<input type="text" maxlength="255" size="100" id="title" name="title" class="inputbox" value="<?php echo $this->post->title;?>" />
						</td>
						<td>
							<label><?php echo JText::_( 'Published' ); ?></label>
						</td>
						<td>
							<input type="radio" value="<?php echo ($this->post->published == DISCUSS_ID_PENDING)? DISCUSS_ID_PENDING : DISCUSS_ID_UNPUBLISHED; ?>" id="published0" name="published">
							<label for="published0">No</label>
							<input type="radio" checked="checked" value="1" id="published1" name="published">
							<label for="published1">Yes</label>
						</td>
					</tr>
					<tr>
						<td>
							<label for="alias"><?php echo JText::_( 'Alias' );?></label>
						</td>
						<td>
							<input type="text" maxlength="255" size="100" id="alias" name="alias" class="inputbox" value="<?php echo $this->post->alias;?>" />
						</td>
						<?php if(empty($this->post->id)) : ?>
						<td>
							<label><?php echo JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_TO_POST' ); ?></label>
						</td>
						<td>
							<input type="radio" value="0" id="self_subscribe0" name="self_subscribe">
							<label for="self_subscribe0">No</label>
							<input type="radio" checked="checked" value="1" id="self_subscribe1" name="self_subscribe">
							<label for="self_subscribe1">Yes</label>
						</td>
						<?php else: ?>
						<td colspan="2">&nbsp;</td>
						<?php endif; ?>
					</tr>
					</tbody>
			</table>
			<table width="100%" class="form">
				<tr>
					<td class="value"><textarea id="dc_reply_content" name="dc_reply_content" class="textarea fullwidth"><?php echo $this->post->content; ?></textarea></td>
				</tr>
			</table>
		</td>
		<td width="320" style="padding: 10px !important;" valign="top">
			<?php if($this->post->parent_id == 0) : ?>
			<table width="100%" class="form">
				<tr>
					<td class="value">
						<div class="dc_input_wrap">
							<?php echo $this->nestedCategories; ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div id="taglist">
							<div class="key"><h3><?php echo JText::_( 'COM_EASYDISCUSS_TAGS_INUSE' );?></div>
							<ul id="tag_items">
							<?php if( $this->post->tags ) { ?>
								<?php foreach( $this->post->tags as $tag ) : ?>
								<li class="tag_item" id="tag_<?php echo str_replace(' ', '-', $tag->title); ?>">
									<a class="remove_tag" href="javascript:void(0);" onclick="admin.post.tags.remove('<?php echo str_replace(' ', '-', $tag->title);?>');"><span><?php echo JText::_('X'); ?></span></a>
									<span class="tag_caption"><?php echo $tag->title; ?></span>
									<input type="hidden" name="tags[]" value="<?php echo $tag->title;?>" />
								</li>
							<?php endforeach; ?>
							<?php }?>
							</ul>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="tag_popular">
							<div class="key"><h3><?php echo JText::_( 'COM_EASYDISCUSS_POPULAR_TAGS' );?></div>
							<div id="tags-container" class="small">
							<?php
								foreach($this->populartags as $tag)
								{
									$tagTitle = JString::trim($tag->title);
								?>
									<a href="javascript:void(0)" id="<?php echo $tag->id; ?>" onclick="admin.post.tags.addexisting('<?php echo $tagTitle; ?>')"><?php echo $tagTitle; ?></a>
								<?php
								}
							?>
							<div class="clr"></div>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="value">
						<div class="tag_new">
							<div class="key"><h3><?php echo JText::_( 'COM_EASYDISCUSS_POST_CREATE_TAGS' );?></div>
							<input type="text" class="inputbox fullwidth" id="new_tags" name="new_tags" />
							<input type="button" id="add_tags" value="<?php echo JText::_('COM_EASYDISCUSS_ADD_TAGS'); ?>" onclick="admin.post.tags.add()" />

							<div id="taglist">
								<ul id="tag_items">
								</ul>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="key"><h3><?php echo JText::_( 'File attachments' ); ?></h3></div>

						<?php
						if(!empty($this->post->id))
						{
						?>
							<!-- File Attachments -->
							<div id="dc_attachments">
							<?php
							$attachments	= $this->post->getAttachments();

							if( $attachments )
							{
							?>
							<ul>
							<?php
								foreach( $this->post->getAttachments() as $attachment )
								{
							?>
								<li id="dc-attachments-<?php echo $attachment->id; ?>" class="dc-att-<?php echo $attachment->attachmentType; ?>"><?php echo $attachment->toHTML(); ?><input type="button" id="button-delete-att-<?php echo $attachment->id; ?>" onclick="admin.files.remove('<?php echo $attachment->id; ?>');" value="<?php echo JText::_('COM_EASYDISCUSS_DELETE_ATTACHMENT'); ?>" /></li>
							<?php
								}
							?>
							</ul>
							<?php
							}
							?>
							</div>
						<?php
						}
						?>
						<div class="dcc_att" id="file_contents">
							<input type="file" name="filedata[]" id="filedata" size="50" />
							<div style="margin-top:5px"><a href="javascript:void(0);" onclick="admin.files.add();"><?php echo JText::_( 'Attach more files' ); ?></a></div>
						</div>
					</td>
				</tr>
			</table>
			<?php endif; ?>
		</td>
	</tr>
</table>
<input type="hidden" name="id" id="id" value="<?php echo $this->post->id; ?>" />
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->post->parent_id; ?>" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="posts" />
<input type="hidden" id="task" name="task" value="submit" />
<input type="hidden" name="source" value="<?php echo $this->source ;?>" />

</form>
