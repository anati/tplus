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


EasyDiscuss.ready(function($)
{
    admin.category.acl.showpanel('select');
});


</script>
<table style="width:1000px">
    <tr>
        <td valign="top">
			<div class="acl-panel clearfix">
			    <input type="hidden" name="activerule" id="activerule" value="" />
			    <div id="left">

				<?php
				    $first  = true;
				
					foreach($this->categoryRules as $catRules) :
					$catGroupRuleSet 	= $this->assignedGroupACL[$catRules->id];
					$catUserRuleSet 	= $this->assignedUserACL[$catRules->id];
					$titleString	= 'COM_EASYDISCUSS_CATEGORIES_ACL_'.$catRules->action.'_TITLE';
					$descString		= 'COM_EASYDISCUSS_CATEGORIES_ACL_'.$catRules->action.'_DESC';
				?>
				    <div class="left-panel <?php echo $catRules->action; ?> <?php echo ($first) ? 'active' : '' ?>">
				        <input type="hidden" name="cid[]" value="<?php echo $catRules->action; ?>" />
				        <div alt="<?php echo JText::_( $descString ); ?>" class="panel-head">
							<?php echo JText::_( $titleString ); ?>
							<a href="javascript: admin.category.acl.showpanel('<?php echo $catRules->action; ?>' , '<?php echo $catRules->id;?>');" class="edit-link" id="acl-edit-<?php echo $catRules->id;?>"<?php echo $first ? ' style="display:none;"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_EDIT'); ?></a>
						</div>
			            <div id="category_acl_<?php echo $catRules->action; ?>" style="width:100%;height:300px;overflow-y:scroll;" onclick="admin.category.acl.showpanel('<?php echo $catRules->action; ?>', '<?php echo $catRules->id;?>');">
			            	<div style="float:left;display:inline-block;width:49%;min-height:40px;">
				                <div style="border-bottom:1px solid #ccc;padding-bottom:10px;margin:0 5px 10px 0"><?php echo JText::_( 'COM_EASYDISCUSS_GROUP' );?>:</div>
				                <ul id="category_acl_group_<?php echo $catRules->action; ?>" class="permision-list reset-ul">
					            <?php if( count($catGroupRuleSet) > 0) : ?>
								<?php foreach($catGroupRuleSet as $ruleItem) : ?>
								    <?php if( $ruleItem->status ) : ?>
								    <li id="acl_group_<?php echo $catRules->action; ?>_<?php echo $ruleItem->groupid; ?>">
								        <span>
								        	<a href="javascript: admin.category.acl.delete('acl_group_<?php echo $catRules->action; ?>_<?php echo $ruleItem->groupid; ?>')">
								        		<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_DELETE'); ?>
								        	</a>
								        </span>
								        -
								        <?php echo $ruleItem->groupname; ?>
								        <input type="hidden" name="acl_group_<?php echo $catRules->action; ?>[]" value="<?php echo $ruleItem->groupid; ?>" />
									</li>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php endif; ?>
				                </ul>
			                </div>

			                <div style="float:left;display:inline-block;width:49%;min-height:40px;margin-left:2%">
				                <div style="border-bottom:1px solid #ccc;padding-bottom:10px;margin:0 5px 10px 0"><?php echo JText::_( 'COM_EASYDISCUSS_USER' );?>:</div>
				                <ul id="category_acl_user_<?php echo $catRules->action; ?>" class="permision-list reset-ul">
				                <?php if( count($catUserRuleSet) > 0) : ?>
								<?php foreach($catUserRuleSet as $ruleItem) : ?>
								    <?php if( $ruleItem->status ) : ?>
								    <li id="acl_user_<?php echo $catRules->action; ?>_<?php echo $ruleItem->groupid; ?>">
								        <span>
									        <a href="javascript: admin.category.acl.delete('acl_user_<?php echo $catRules->action; ?>_<?php echo $ruleItem->groupid; ?>')">
									        	<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_DELETE'); ?>
									        </a>
								        </span>
								        -
								        <?php echo $ruleItem->groupname; ?>
								        <input type="hidden" name="acl_user_<?php echo $catRules->action; ?>[]" value="<?php echo $ruleItem->groupid; ?>" />
									</li>
									<?php endif; ?>
								<?php endforeach; ?>
								<?php endif; ?>
				                </ul>
			                </div>
						</div>
				    </div>
				<?php
				    $first = false;
					endforeach;
				?>
			    </div>

				<div id="right">
				    <div id="panel-wraper">
				    	<div class="clearfix">
					        <div id="group-panel-tab" class="panel-tab active">
					            <a href="javascript:void(0);" onclick="admin.category.acl.subpanel('group');"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_TYPE_GROUP'); ?></a>
					        </div>
					        <div id="user-panel-tab" class="panel-tab">
					            <a href="javascript:void(0);" onclick="admin.category.acl.subpanel('user');"><?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_TYPE_USER'); ?></a>
					        </div>
						</div>

						<div id="cat-panel-group" class="panel-pane">
						    <ul class="reset-ul">
				            <?php if( count($this->joomlaGroups) > 0) : ?>
							<?php foreach($this->joomlaGroups as $ruleItem) : ?>
							    <li id="group-li-<?php echo $ruleItem->id; ?>">
							        <input type="checkbox" name="panel_group[]" value="<?php echo $ruleItem->id; ?>" />
							        <input type="hidden" id="panel_group_<?php echo $ruleItem->id; ?>" value="<?php echo $ruleItem->name; ?>" />
									<?php echo $ruleItem->name; ?>
							    </li>
							<?php endforeach; ?>
							<?php endif; ?>
							</ul>

						    <div>
								<input type="button" class="button" onclick="admin.category.acl.assign('group');return;" value="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_ADD_TO_LIST');?>"/>
						    </div>
						</div>

						<div id="cat-panel-user" class="panel-pane" style="display:none;">
						    <ul id="cat-panel-user-ul" class="reset-ul">
							</ul>
						    <div>
						    	<a class="modal" rel="{handler: 'iframe', size: {x: 650, y: 375}}" href="index.php?option=com_easydiscuss&view=users&tmpl=component&browse=1">
									<?php echo JText::_('COM_EASYDISCUSS_BROWSE_USERS');?>
								</a>
								&nbsp; - &nbsp;
								<input type="button" class="button" onclick="admin.category.acl.assign('user');return;" value="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_ADD_TO_LIST');?>"/>
						    </div>
						</div>
				    </div>
				</div>
			</div>
		</td>
	</tr>
</table>
