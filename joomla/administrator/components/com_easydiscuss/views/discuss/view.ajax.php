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

jimport( 'joomla.application.component.view');

require_once( DISCUSS_HELPERS . DS . 'helper.php' );

class EasyDiscussViewDiscuss extends JView
{
	function getUpdates()
	{
		$version	= DiscussHelper::getVersion();
		$local		= DiscussHelper::getLocalVersion();

		// Test build only since build will always be incremented regardless of version
		$localVersion	= explode( '.' , $local );
		$localBuild		= $localVersion[2];

		if( !$version )
			return JText::_('Unable to contact update servers');

		$remoteVersion	= explode( '.' , $version );
		$build			= $remoteVersion[ 2 ];

		$html			= '<span class="version_outdated">' . JText::sprintf( 'COM_EASYDISCUSS_VERSION_OUTDATED' , $local , $version ) . '</span>';

		if( $localBuild >= $build )
		{
			$html		= '<span class="version_latest">' . JText::sprintf('COM_EASYDISCUSS_VERSION_LATEST' , $local ) . '</span>';
		}

		$news		= DiscussHelper::getRecentNews();
		$content	= '';

		ob_start();
		if( $news )
		{
			foreach( $news as $item )
			{
		?>
		<li>
			<b><span><?php echo $item->title . ' - ' . $item->date; ?></span></b>
			<div><?php echo $item->desc;?></div>
		</li>
		<?php
			}
		}
		else
		{
		?>
		<li><?php echo JText::_('Unable to contact news server');?></li>
		<?php
		}

		$content	= ob_get_contents();
		@ob_end_clean();

		$ajax			= new Disjax();
		$ajax->script( 'Foundry(\'#submenu-box #submenu\').append(\'<li style="float: right; margin:5px 10px 0 0;">' . $html . '</li>\');');
		$ajax->assign( 'news-items' , $content );
		$ajax->send();
	}
}
