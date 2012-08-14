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

static $loaded	= false;

if( !$loaded )
{
	$doc 	= JFactory::getDocument();

	// @task: Add the core foundry.js into the header
	$debug = JRequest::getInt( 'foundry_debug' );

	$scriptPath = rtrim( JURI::root() , '/' ) . '/media/foundry/js/';
	$scriptDevPath = $scriptPath . 'dev/';

	if ($debug)
	{
		$doc->addScript($scriptDevPath . 'jquery.js');
		$doc->addScript($scriptDevPath . 'jquery.handy.js');
		$doc->addScript($scriptDevPath . 'jquery.lang.js');
		$doc->addScript($scriptDevPath . 'jquery.lang.rsplit.js');
		$doc->addScript($scriptDevPath . 'jquery.number.js');
		$doc->addScript($scriptDevPath . 'jquery.event.default.js');
		$doc->addScript($scriptDevPath . 'jquery.event.destroyed.js');
		$doc->addScript($scriptDevPath . 'jquery.class.js');
		$doc->addScript($scriptDevPath . 'jquery.controller.js');
		$doc->addScript($scriptDevPath . 'jquery.view.js');
		$doc->addScript($scriptDevPath . 'jquery.view.ejs.js');
		$doc->addScript($scriptDevPath . 'jquery.implement.js');
		$doc->addScript($scriptDevPath . 'jquery.component.js');
		$doc->addScript($scriptDevPath . 'jquery.json.js');
		$doc->addScript($scriptDevPath . 'jquery.server.js');
		$doc->addScript($scriptDevPath . 'jquery.require.js');
	} else {
		$doc->addScript($scriptPath . 'foundry.js');
	}

	// @task: Append necessary headers
	ob_start();
	?>
	Foundry.rootPath   = '<?php echo JURI::root(); ?>';
	Foundry.indexUrl   = '<?php echo JURI::base() . "index.php"; ?>';
	Foundry.scriptPath = '<?php echo JURI::root() . "media/foundry/js/"; ?>';
	<?php
	$contents	= ob_get_contents();
	ob_end_clean();

	$doc->addScriptDeclaration( $contents );
}

?>
