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
jimport( 'joomla.html.pane' );

require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_ADMIN_ROOT . DS . 'views.php');

class EasyDiscussViewAutoposting extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		$config			= DiscussHelper::getConfig();

		$this->assignRef( 'config' , $config );

		$layout			= $this->getLayout();

		if( method_exists( $this , $layout ) )
		{
			$this->$layout( $tpl );

			return;
		}

		$facebookSetup	= $this->setuped( 'facebook' );
		$twitterSetup	= $this->setuped( 'twitter' );

		$this->assignRef( 'twitterSetup'	, $twitterSetup );
		$this->assignRef( 'facebookSetup' 	, $facebookSetup );
		parent::display($tpl);
	}

	public function form( $tpl = null )
	{
		$type	= JRequest::getVar( 'type' );
		$config	= DiscussHelper::getConfig();

		$callback	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=autoposting' , false, true );
		$oauth	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( 'facebook' , $config->get( 'main_autopost_facebook_id') , $config->get( 'main_autopost_facebook_secret') , $callback );

		$oauth	= DiscussHelper::getTable( 'OAuth' );
		$oauth->loadByType( 'facebook' );
		$associated	= (bool) $oauth->id;

		$this->assignRef( 'associated' , $associated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'type'	, $type );

		parent::display($tpl);
	}

	public function setuped( $type )
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_oauth' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'access_token' ) . ' IS NOT NULL';
		$db->setQuery( $query );

		$exists	= $db->loadResult();

		return $exists > 0;
	}

	public function facebook( $tpl = null )
	{
		$step	= JRequest::getVar( 'step' );
		$config	= DiscussHelper::getConfig();

		$callback	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=autoposting' , false, true );
		$oauth	= DiscussHelper::getHelper( 'OAuth' )->getConsumer( 'facebook' , $config->get( 'main_autopost_facebook_id') , $config->get( 'main_autopost_facebook_secret') , $callback );

		$oauth	= DiscussHelper::getTable( 'OAuth' );
		$oauth->loadByType( 'facebook' );

		$associated	= (bool) $oauth->id;

		$this->assignRef( 'associated' , $associated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'step'	, $step );

		parent::display($tpl);
	}


	public function twitter( $tpl = null )
	{
		$step	= JRequest::getVar( 'step' );
		$config	= DiscussHelper::getConfig();

		$callback	= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=autoposting' , false, true );
		$oauth		= DiscussHelper::getHelper( 'OAuth' )->getConsumer( 'twitter' , $config->get( 'main_autopost_twitter_id') , $config->get( 'main_autopost_twitter_secret') , $callback );

		$oauth	= DiscussHelper::getTable( 'OAuth' );
		$oauth->loadByType( 'twitter' );

		$associated	= (bool) $oauth->id;

		$this->assignRef( 'associated' , $associated );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'step'	, $step );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_AUTOPOST' ), 'autoposting' );

		JToolBarHelper::back();

		if( $this->getLayout() == 'form' )
		{
			JToolBarHelper::divider();
			JToolBarHelper::save('save');
			JToolBarHelper::cancel();
		}
	}
}
