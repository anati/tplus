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

jimport('joomla.html.pagination');

class DiscussPagination extends JPagination
{
	public function __construct($total, $limitstart, $limit, $prefix = '')
	{
		parent::__construct($total, $limitstart, $limit, $prefix);
	}

	// alias method
	public function getPagesLinks( $viewpage = 'index', $filtering = array(), $doReplace = false )
	{
		return $this->toHTML( $viewpage, $filtering, $doReplace );
	}
	
	/**
	 *
	 * $filtering
	 *      if index page:
	 *      category_id
	 *      filter
	 *      sort
	 *      query
	 */
	public function toHTML( $viewpage = 'index', $filtering = array(), $doReplace = false )
	{
		$data	= $this->getData();
		
		$queries    = '';
		if( !empty( $filtering ) )
		{
		    if( isset( $filtering['category_id'] ) )
		    {
		        $queries    .= '&category_id=' . $filtering['category_id'];
		    }
		    
		    if( isset( $filtering['filter'] ) )
		    {
		        $queries    .= '&filter=' .$filtering['filter'];
		    }
		    
		    if( isset( $filtering['sort'] ) )
		    {
		        $queries    .= '&sort=' .$filtering['sort'];
		    }
		    
		    if( isset( $filtering['query'] ) )
		    {
		        $queries    .= '&query=' .$filtering['query'];
		    }
		    
		    // profile
		    if( isset( $filtering['viewtype'] ) )
		    {
		        $queries    .= '&viewtype=' .$filtering['viewtype'];
		    }
		    
		    if( isset( $filtering['id'] ) )
		    {
		        $queries    .= '&id=' .$filtering['id'];
		    }
		}




// echo '<pre>';
// print_r($data);
// echo '</pre>';

		if( !empty( $data ) && $doReplace)
		{
			$curPageLink    = 'index.php?option=com_easydiscuss&view=' . $viewpage . $queries;

			foreach( $data->pages as $page )
			{
			    if( !empty( $page->link ) )
			    {
			        $limitstart  = ( !empty($page->base) ) ? '&limitstart=' . $page->base : '';
			        $page->link   = DiscussRouter::_( $curPageLink . $limitstart);
			    }
			}

			// newer link
			if( !empty( $data->next->link ) )
			{
			    $limitstart  = ( !empty($data->next->base) ) ? '&limitstart=' . $data->next->base : '';
			    $data->next->link   = DiscussRouter::_( $curPageLink . $limitstart);
			}

			// older link
			if( !empty( $data->previous->link ) )
			{
			    $limitstart  = ( !empty($data->previous->base) ) ? '&limitstart=' . $data->previous->base : '';
			    $data->previous->link   = DiscussRouter::_( $curPageLink . $limitstart);
			}

		}

		
		
		ob_start();
		?>
		<ul class="list-pagination reset-ul float-li clearfull">
			<li class="">
				<span class="fs-11 ttu"><?php echo JText::_( 'COM_EASYDISCUSS_PAGINATION_PAGE' );?> :</span>
			</li>
			
			<?php if( $data->previous->link ){ ?>
				<li class="older"><a href="<?php echo $data->previous->link ?>" rel="nofollow"><?php echo JText::_( 'COM_EASYDISCUSS_PAGINATION_OLDER' );?></a></li>
			<?php } ?>
			
			<?php foreach( $data->pages as $page ){ ?>
			<?php 	if( $page->link ) { ?>
			<li><a href="<?php echo $page->link ?>" rel="nofollow"><?php echo $page->text;?></a></li>
			<?php 	} else { ?>
			<li class="active"><b><?php echo $page->text;?></b></li>
			<?php 	} ?>
			<?php } ?>
			
			<?php if( $data->next->link ){ ?>
				<li class="newer"><a href="<?php echo $data->next->link ?>" rel="nofollow"><?php echo JText::_( 'COM_EASYDISCUSS_PAGINATION_NEWER' ); ?></a></li>
			<?php } ?>
		</ul>
		<?php
		$html	= ob_get_clean();
		return $html;
	}

	public function getCounter()
	{
		$start	= $this->limitstart + 1;
		$end	= $this->limitstart + $this->limit < $this->total ? $this->limitstart + $this->limit : $this->total;
		ob_start();
		?>
<div id="es-pagination-counter">
	<?php if( $this->total > 0 ){ ?>
	<b><?php echo $start;?></b> - <b><?php echo $end;?></b>
	<em class="ffg"><?php echo JText::_( 'of' );?></em>
	<b><?php echo $this->total;?></b>
	<?php } else { ?>
	<em class="ffg"><?php echo JText::_( 'No conversations yet' );?>
	<?php } ?>
</div>
		<?php
		$html	= ob_get_clean();
		return $html;
	}
}
