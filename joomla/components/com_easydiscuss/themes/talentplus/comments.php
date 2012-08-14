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


if(isset($comments))
{
	if(! empty($comments))
	{
	    foreach($comments as $comment)
		{
			$comment->comment   = nl2br($comment->comment);
?>
<li id="comment-<?php echo $comment->id;?>" class="discuss-story-mini">
    <a href="<?php echo $comment->creator->getLink();?>" class="avatar float-l">
        <img src="<?php echo $comment->creator->getAvatar();?>" class="avatar small" />
    </a>
	<div class="discuss-content">
        <div class="comment-author fs-11">
            <a href="<?php echo $comment->creator->getLink();?>" class="comment-author-name"><?php echo $comment->creator->getName(); ?></a>
            <span class="fc-99">- <?php echo $comment->duration; ?></span>
            <?php if($isAdmin) : ?>
    		  <a href="javascript:void(0);" onclick="discuss.comment.remove('<?php echo $comment->id;?>');" class="float-r fs-9" title="<?php echo JText::_('COM_EASYDISCUSS_DELETE_LOWER_CASE');?>"><?php echo JText::_('COM_EASYDISCUSS_DELETE_LOWER_CASE');?></a>
            <?php endif; ?>
        </div>
		<div class="comment-content mt-5 mb-5"><?php echo $comment->comment; ?></div>
	</div>
    <div class="clear"></div>
</li>

<?php
		}//end foreach
	}//end if not empty
}//end if
?>
