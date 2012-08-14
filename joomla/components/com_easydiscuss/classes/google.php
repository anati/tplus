<?php
/**
* @package      EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once( DISCUSS_HELPERS . DS .'helper.php' );
require_once( DISCUSS_HELPERS . DS .'router.php' );

class DiscussGoogleBuzz
{	
	public static function getButtonHTML( $row )
	{
		$config	= DiscussHelper::getConfig();

		if( !$config->get('integration_googlebuzz') )
		{
			return '';
		}

		$html		= '<div class="social-button google-buzz">
						<a href="http://www.google.com/buzz/post" class="google-buzz-button" title="Google Buzz" data-message="' . $row->title . '" data-button-style="normal-count"></a>
						<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>
						</div>';

		return $html;
	}
}


class DiscussGoogleOne
{
	public static function getButtonHTML( $row,  $position = 'vertical' )
	{
		$config	= DiscussHelper::getConfig();

		if( !$config->get('integration_googleone') )
		{
			return '';
		}

	    $size		= $config->get('integration_googleone_layout');
	    $dataURL	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $row->id, false, true);
	    
	    
	    $googleHTML  = '';
	    
	    if( $position == 'vertical' && $size == 'tall')
	    {
			$googleHTML	.= '<div class="social-button google-plusone">';
			$googleHTML	.= '<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>';
			$googleHTML	.= '<g:plusone size="' . $size . '" href="' . $dataURL . '"></g:plusone>';
			$googleHTML	.= '</div>';
		}
		else if($position == 'horizontal' && $size == 'medium')
		{
			$googleHTML .= '<div class="social-button-horizontal" style="float: left;">';
			$googleHTML	.= '	<div class="social-button google-plusone">';
			$googleHTML	.= '	<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>';
			$googleHTML	.= '	<g:plusone size="' . $size . '" href="' . $dataURL . '"></g:plusone>';
			$googleHTML	.= '	</div>';
			$googleHTML	.= '</div>';
		}

		return $googleHTML;
	}
}