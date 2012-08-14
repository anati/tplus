<?php
// ----------------------------------------------------------------------------
// markItUp! BBCode Parser
// v 1.0.6
// Dual licensed under the MIT and GPL licenses.
// ----------------------------------------------------------------------------
// Copyright (C) 2009 Jay Salvat
// http://www.jaysalvat.com/
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ----------------------------------------------------------------------------
// Thanks to Arialdo Martini, Mustafa Dindar for feedbacks.
// ----------------------------------------------------------------------------


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

//define ("EMOTICONS_DIR", "/images/emoticons/");
define ("EMOTICONS_DIR", JURI::root() . '/components/com_easydiscuss/assets/vendors/markitup/sets/bbcode/images/');

require_once( JPATH_ROOT . DS. 'components' . DS . 'com_easydiscuss' . DS . 'constants.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
class Parser
{
	public static function bbcode($text)
	{
	    $text	= self::html2bbcode( $text );

		// $text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim($text);

		$text   = nl2br( $text );

		// @rule: Replace [code]*[/code]
		$codesPattern	= '/\[code( type=&quot;(.*?)&quot;)?\](.*?)\[\/code\]/ms';

		// preg_match( $codesPattern , $text , $codes );
		$text = preg_replace_callback( $codesPattern , array( 'Parser' , 'escape' ) , $text );		

		// BBCode to find...
		$bbcodeSearch = array( 	 '/\[b\](.*?)\[\/b\]/ms',
						 '/\[i\](.*?)\[\/i\]/ms',
						 '/\[u\](.*?)\[\/u\]/ms',
						 '/\[img\](.*?)\[\/img\]/ms',
						 '/\[email\](.*?)\[\/email\]/ms',
						 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
						 '/\[quote](.*?)\[\/quote\]/ms',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
						 '/\[list\](.*?)\[\/list\]/ms',
						 '/\[\*\]\s?(.*?)\n/ms'
		);
		// And replace them by...
		$bbcodeReplace = array(	 '<strong>\1</strong>',
						 '<em>\1</em>',
						 '<u>\1</u>',
						 '<img src="\1" alt="\1" />',
						 '<a href="mailto:\1">\1</a>',
						 '<a href="\1">\2</a>',
						 '<span style="font-size:\1%">\2</span>',
						 '<span style="color:\1">\2</span>',
						 '<blockquote>\1</blockquote>',
						 '<ol start="\1">\2</ol>',
						 '<ul>\1</ul>',
						 '<li>\1</li>'
		);

		// @rule: Replace URL links.
		// We need to strip out bbcode's data first.
		$tmp    = preg_replace( $bbcodeSearch , '' , $text );

		// Replace video codes
		$tmp	= DiscussHelper::getHelper( 'Videos' )->strip( $tmp );

		// Replace URLs
		$text   = DiscussHelper::getHelper( 'URL' )->replace( $tmp , $text );

		// @rule: Replace video links
		$text	= DiscussHelper::getHelper( 'Videos' )->replace( $text );

		// Smileys to find...
		$in = array( 	 ':)',
						 ':D',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);
		// And replace them by...
		$out = array(	 '<img alt=":)" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
						 '<img alt=":D" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
						 '<img alt=":o" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" class="bb-smiley" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
		);
		$text = str_replace($in, $out, $text);

		// Replace bbcodes
		$text 	= preg_replace( $bbcodeSearch , $bbcodeReplace, $text);

		return $text;
	}

	public static function escape($s)
	{
		$code = $s[3];
		$code = str_ireplace( "<br />" , "" , $code );

		$code = str_replace("[", "&#91;", $code);
		$code = str_replace("]", "&#93;", $code);

		$brush  = isset( $s[2] ) && !empty( $s[2] ) ? $s[2] : 'xml';

		$code	= html_entity_decode( $code );
		$code	= DiscussHelper::getHelper( 'String' )->escape( $code );

		return '<pre class="brush: '. htmlspecialchars( $brush ) . ';">'.$code.'</pre>';
	}

	public static function removeCodes( $content )
	{
		$codesPattern	= '/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms';

		return preg_replace( $codesPattern , '' , $content );
	}

	public static function filter($text)
	{
		$text	= htmlspecialchars($text , ENT_NOQUOTES );
		$text	= trim($text);

		// paragraphs
		//$text   = nl2br( $text );

		// @rule: Replace [code]*[/code]
		$text = preg_replace_callback('/\[code( type="(.*?)")?\](.*?)\[\/code\]/ms', array( 'Parser' , 'escape' ) , $text );

		// BBCode to find...
		$bbcodeSearch = array( 	 '/\[b\](.*?)\[\/b\]/ms',
						 '/\[i\](.*?)\[\/i\]/ms',
						 '/\[u\](.*?)\[\/u\]/ms',
						 '/\[img\](.*?)\[\/img\]/ms',
						 '/\[email\](.*?)\[\/email\]/ms',
						 '/\[url\="?(.*?)"?\](.*?)\[\/url\]/ms',
						 '/\[size\="?(.*?)"?\](.*?)\[\/size\]/ms',
						 '/\[color\="?(.*?)"?\](.*?)\[\/color\]/ms',
						 '/\[quote](.*?)\[\/quote\]/ms',
						 '/\[list\=(.*?)\](.*?)\[\/list\]/ms',
						 '/\[list\](.*?)\[\/list\]/ms',
						 '/\[\*\]\s?(.*?)\n/ms'
		);

		// @rule: Replace URL links.
		// We need to strip out bbcode's data first.
		$text	= preg_replace( $bbcodeSearch , '' , $text );
		$text	= DiscussHelper::getHelper( 'URL' )->replace( $text , $text );

		// Smileys to find...
		$in = array( 	 ':)',
						 ':D',
						 ':o',
						 ':p',
						 ':(',
						 ';)'
		);
		// And replace them by...
		$out = array(	 '<img alt=":)" src="'.EMOTICONS_DIR.'emoticon-happy.png" />',
						 '<img alt=":D" src="'.EMOTICONS_DIR.'emoticon-smile.png" />',
						 '<img alt=":o" src="'.EMOTICONS_DIR.'emoticon-surprised.png" />',
						 '<img alt=":p" src="'.EMOTICONS_DIR.'emoticon-tongue.png" />',
						 '<img alt=":(" src="'.EMOTICONS_DIR.'emoticon-unhappy.png" />',
						 '<img alt=";)" src="'.EMOTICONS_DIR.'emoticon-wink.png" />'
		);
		$text = str_replace($in, $out, $text);

		return $text;
	}
	
	public static function html2bbcode( $text )
	{
	    if( (strpos($text, '<p>') === false) && (strpos($text, '<div>') === false) &&  (strpos($text, '<br') === false))
	    {
	        return $text;
	    }
	
		$bbcodeSearch = array(
		    '/<strong>(.*?)<\/strong>/ms',
		    '/<b>(.*?)<\/b>/ms',
		    '/<big>(.*?)<\/big>/ms',
		    '/<em>(.*?)<\/em>/ms',
		    '/<i>(.*?)<\/i>/ms',
		    '/<u>(.*?)<\/u>/ms',
		    '/<img.*?src=["|\'](.*?)["|\'].*?\>/ms',
		    '/<p>/ms',
		    '/<\/p>/ms',
			'/<blockquote>(.*?)<\/blockquote>/ms',
 			'/<ol.*?\>(.*?)<\/ol>/ms',
 			'/<ul>(.*?)<\/ul>/ms',
 			'/<li>(.*?)<\/li>/ms',
		    '/<a.*?href=["|\']mailto:(.*?)["|\'].*?\>.*?<\/a>/ms',
			'/<a.*?href=["|\'](.*?)["|\'].*?\>(.*?)<\/a>/ms',
			'/<pre.*?\>(.*?)<\/pre>/ms'
		);
		
		$bbcodeReplace = array(
		    '[b]\1[/b]',
		    '[b]\1[/b]',
		    '[b]\1[/b]',
		    '[i]\1[/i]',
		    '[i]\1[/i]',
		    '[u]\1[/u]',
		    '[img]\1[/img]',
		    '',
		    '<br />',
		    '[quote]\1[/quote]',
			'[list=1]\1[/list]',
			'[list]\1[/list]',
			'[*]\1',
		    '[email]\1[/email]',
		    '[url="\1"]\2[/url]',
		    '[code type="xml"]\1[/code]'
		);

		// Replace bbcodes
		$text   = strip_tags($text, '<br><strong><em><u><img><a><p><blockquote><ol><ul><li><b><big><i><pre>');
		$text 	= preg_replace( $bbcodeSearch , $bbcodeReplace, $text);
		$text	= str_replace('<br />', "\r\n", $text);
		
		return $text;
	}
}
