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

class DiscussTwitter
{	
	public static function getButtonHTML( $row, $position = 'vertical' )
	{
		$config	= DiscussHelper::getConfig();

		if( !$config->get('integration_twitter_button') )
		{
		    return '';
		}
		
		$html       = '';
		$style		= $config->get( 'integration_twitter_button_style' );
		$dataURL	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $row->id, false, true);
		
		if( $position == 'vertical' && ($style == 'vertical' || $style == 'none' ) )
		{
			$html		= '<div class="social-button retweet"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $dataURL . '" data-counturl="'.$dataURL.'" data-count="' . $style .'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';
		}
		else if($position == 'horizontal' && $style == 'horizontal' )
		{
			$html        = '<div class="social-button-horizontal" style="float: left;">';
			$html		.= '<div class="social-button retweet"><a href="http://twitter.com/share" class="twitter-share-button" data-url="' . $dataURL . '" data-counturl="'.$dataURL.'" data-count="' . $style .'">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';
			$html       .= '</div>';
		}

		return $html;
	}
}