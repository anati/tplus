<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<script language="javascript" type="text/javascript">

	function tableOrdering( order, dir, task )
	{
		var form = document.adminForm;

		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		document.adminForm.submit( task );
	}
</script>
<?php
$label = "Articles";
if( isset($this->items[0]) )
{
    // Try to use category names, for the article labels.
    $first_item = $this->items[0];
    if( isset($first_item->category) )
    {
        $label = $first_item->category;
    }
}
$items_by_year = array();
// Separate items into the year they were created
foreach( $this->items as $item )
{
    $year = date('Y', strtotime($item->created));
    if( strtotime($item->created) > strtotime('-1 year') )
    {
        $year = 'recent';
    }
    if( ! isset($items_by_year[$year]) )
    {
        $items_by_year[$year] = array();
    }
    $items_by_year[$year][] = $item;
}
?>
<?php
$show_full_article_link = $this->params->get('feed_summary');
?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if ($this->params->get('filter') || $this->params->get('show_pagination_limit')) : ?>
<tr>
	<td colspan="5">
		<table>
		<tr>
		<?php if ($this->params->get('filter')) : ?>
			<td align="left" width="60%" nowrap="nowrap">
				<?php echo JText::_($this->params->get('filter_type') . ' Filter').'&nbsp;'; ?>
				<input type="text" name="filter" value="<?php echo $this->escape($this->lists['filter']);?>" class="inputbox" onchange="document.adminForm.submit();" />
			</td>
		<?php endif; ?>
		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<td align="right" width="40%" nowrap="nowrap">
			<?php
				echo '&nbsp;&nbsp;&nbsp;'.JText::_('Display Num').'&nbsp;';
				echo $this->pagination->getLimitBox();
			?>
			</td>
		<?php endif; ?>
		</tr>
		</table>
	</td>
</tr>
<?php endif; ?>
<?php if ($this->params->get('show_headings')) : ?>
<tr>
	<td class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" align="right" width="5%">
		<?php echo JText::_('Num'); ?>
	</td>
	<?php if ($this->params->get('show_title')) : ?>
 	<td class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" >
		<?php echo JHTML::_('grid.sort',  'Item Title', 'a.title', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</td>
	<?php endif; ?>
	<?php if ($this->params->get('show_date')) : ?>
	<td class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" width="25%">
		<?php echo JHTML::_('grid.sort',  'Date', 'a.created', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</td>
	<?php endif; ?>
	<?php if ($this->params->get('show_author')) : ?>
	<td class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>"  width="20%">
		<?php echo JHTML::_('grid.sort',  'Author', 'author', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</td>
	<?php endif; ?>
	<?php if ($this->params->get('show_hits')) : ?>
	<td align="center" class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>" width="5%" nowrap="nowrap">
		<?php echo JHTML::_('grid.sort',  'Hits', 'a.hits', $this->lists['order_Dir'], $this->lists['order'] ); ?>
	</td>
	<?php endif; ?>
</tr>
</table>
<?php endif; ?>
<?php 
$archive_displayed = false;
foreach ($items_by_year as $year => $items) : ?>
    <?php
    $counter    = 1;
    $is_recent  = ($year == 'recent');
    $table_id   = "category-list-{$year}";
    if( ! $is_recent && ! $archive_displayed )
    { 
        $archive_displayed = true;
        ?><br /><span class="bluetitle">Archive</span><hr /><?php 
    }
    if( ! $is_recent ): ?>
        <a href="javascript: void(0);" onclick="jQuery('#<?php echo $table_id; ?>').toggle();" class="category-list-date"><?php echo $label; ?> from <?php echo $year; ?></a><br />
    <?php endif; ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" id="<?php echo $table_id; ?>" style="<?php echo $is_recent? "" : "display: none;"; ?> margin-bottom:<?php echo $is_recent? "0px" : "20px"; ?>; width: 100%;">
    <?php foreach ($items as $item) : ?>
    <?php ++ $counter; ?>
    <tr class="sectiontableentry<?php echo ($counter % 2) + 1 . $this->escape($this->params->get('pageclass_sfx')); ?>" >
    	<td align="right">
    		<?php echo $this->pagination->getRowOffset( $item->count ); ?>
    	</td>
    	<?php if ($item->access <= $this->user->get('aid', 0)) : ?>
    	<td>
            <?php
            if( $this->params->get('show_intro') ) {
                $introtext_id = "article-list-item-{$item->id}";
                $view_full_link_id = "article-list-link-{$item->id}";
            ?>

            <p style="margin: 6px 0px;">
    		    <a href="javascript: void(0);" onclick="
                    jQuery('#<?php echo $introtext_id; ?>').fadeIn(300);
                    jQuery('#<?php echo $view_full_link_id; ?>').show();
                    "><?php echo $this->escape($item->title); ?></a>
                <?php if( $show_full_article_link ) { ?>
    		        <a href="<?php echo $item->link; ?>" id="<?php echo $view_full_link_id; ?>" style="display: none;">Full Article</a>
                <?php } ?>
            </p>
            <div id="<?php echo $introtext_id; ?>" style="display: none; padding: 0px 6px;">
                <?php echo $item->introtext; ?>
            </div>
            <?php } else { ?>
    		    <a href="<?php echo $item->link; ?>"><?php echo $this->escape($item->title); ?></a>
            <?php } ?>
            <?php $this->item = $item; echo JHTML::_('icon.edit', $item, $this->params, $this->access) ?>
    	</td>
    	<?php else : ?>
    	<td>
    		<?php
    			echo $this->escape($item->title).' : ';
    			$link = JRoute::_('index.php?option=com_user&view=login');
    			$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid), false);
    			$fullURL = new JURI($link);
    			$fullURL->setVar('return', base64_encode($returnURL));
    			$link = $fullURL->toString();
    		?>
    		<a href="<?php echo $link; ?>">
    			<?php echo JText::_( 'Register to read more...' ); ?></a>
    	</td>
    	<?php endif; ?>
    	<?php if ($this->params->get('show_date')) : ?>
    	<td>
    		<?php echo $item->created; ?>
    	</td>
    	<?php endif; ?>
    	<?php if ($this->params->get('show_author')) : ?>
    	<td >
    		<?php echo $this->escape($item->created_by_alias) ? $this->escape($item->created_by_alias) : $this->escape($item->author); ?>
    	</td>
    	<?php endif; ?>
    	<?php if ($this->params->get('show_hits')) : ?>
    	<td align="center">
    		<?php echo $this->escape($item->hits) ? $this->escape($item->hits) : '-'; ?>
    	</td>
    	<?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </table>
<?php endforeach; ?>
<?php if ($this->params->get('show_pagination')) : ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td colspan="5">&nbsp;</td>
</tr>
<tr>
	<td align="center" colspan="4" class="sectiontablefooter<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</td>
</tr>
<tr>
	<td colspan="5" align="right">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</td>
</tr>
<?php endif; ?>
</table>

<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="sectionid" value="<?php echo $this->category->sectionid; ?>" />
<input type="hidden" name="task" value="<?php echo $this->lists['task']; ?>" />
<input type="hidden" name="filter_order" value="" />
<input type="hidden" name="filter_order_Dir" value="" />
<input type="hidden" name="limitstart" value="0" />
<input type="hidden" name="viewcache" value="0" />
</form>
