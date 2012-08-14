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

class DiscussUrlHelper
{
	public function replace( $tmp , $text )
	{
		$pattern    = '@(https?://[-\w\.]+(:\d+)?(/([\w/_\.-]*(\?\S+)?)?)?)@';
		preg_match_all( $pattern , $tmp , $matches );

		if( isset( $matches[ 0 ] ) && is_array( $matches[ 0 ] ) )
		{
		    foreach( $matches[ 0 ] as $match )
		    {
		        $text   = str_ireplace( $match , '<a href="' . $match . '">' . $match . '</a>' , $text );
			}
		}
		
		$text   = str_ireplace( '&quot;' , '"', $text );
		return $text;
// 		return preg_replace( $pattern , '<a href="$1">$1</a>', $text );
	}
}