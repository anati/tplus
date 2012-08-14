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

require( DISCUSS_ADMIN_ROOT . DS . 'views.php');
require_once( DISCUSS_HELPERS . DS . 'helper.php' );

class EasyDiscussViewBadge extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		$id		= JRequest::getInt( 'id' , 0 );

		$badge	= DiscussHelper::getTable( 'Badges' );
		$badge->load( $id );

		if( !$badge->created )
		{
			$date			= DiscussHelper::getHelper( 'Date' )->dateWithOffset( JFactory::getDate()->toMySQL() );
			$badge->created	= $date->toMySQL();
		}

		$model	= $this->getModel( 'Badges' );
		$rules	= $model->getRules();
		$badges	= $this->getBadges();

		$this->assign( 'badges'	, $badges );
		$this->assign( 'rules'	, $rules );
		$this->assign( 'badge'	, $badge );

		parent::display($tpl);
	}
	
	public function getBadges()
	{
		$files	= JFolder::files( DISCUSS_BADGES_PATH );
		$badges	= array();

		foreach( $files as $file )
		{
			if( $file != '.DS_Store' && $file != '.' && $file != '..' )
			{
				$badges[]	= $file;
			}
		}
		return $badges;
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_BADGES' ), 'badges' );

		JToolBarHelper::cancel();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
		JToolBarHelper::custom( 'saveNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_NEW_BUTTON' ) , false);
	}
}
