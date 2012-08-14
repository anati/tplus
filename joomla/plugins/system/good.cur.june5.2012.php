<?php
/*
 * The plgSystemFix_Registration_Marks plugin
 * ensures that all registration marks have the proper HTML class.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/* 
 * A basic class for executing the regexes against the HTML.
 * This is separated into its own class in case it needs to be
 * reused within a module.
 */
class Fix_Registration_Marks {
    // Replace the old style of registration marks.
    private static $clean_regex = '/<span class="register_mark">(\s*)(®|\&reg;)(\s*)<\/span>/';
    private static $clean_replace = '$1®$3';

    // Apply a class and style for all registration marks.
    private static $regex = '/(®|\&reg;)/';
    private static $replace = '<sup class="superscript-inline">&reg;</sup>';

    public static function replace( $text )
    {
        $text = preg_replace(self::$clean_regex, self::$clean_replace, $text);
        $text = preg_replace(self::$regex, self::$replace, $text);
        return $text;
    }
    /*
     * Fix all registration marks within the content body.
     */
    public static function contentBody(&$output)
    {
        $output = self::replace($output);
    }
}
/* 
 * The plgSystemFix_Registration_Marks plugin will execute after the content
 * has been rendered.
 */
class plgSystemFix_Registration_Marks extends JPlugin
{
	function plgSystemFix_Registration_Marks( &$subject, $params )
	{
		parent::__construct( $subject, $params );
	}
    /* 
     * After the content has been rendered, clean up the
     * registration marks.
     */
    function onAfterRender()
    {
        $app = JFactory::getApplication();

        if($app->isAdmin()) {
                return;
        }

        $output = JResponse::getBody();
        Fix_Registration_Marks::contentBody($output);
        JResponse::setBody($output);
        return true;
    }
}
