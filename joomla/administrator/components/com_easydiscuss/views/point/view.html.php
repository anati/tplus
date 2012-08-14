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

class EasyDiscussViewPoint extends EasyDiscussAdminView
{
	function display($tpl = null)
	{
		$id		= JRequest::getInt( 'id' , 0 );

		$point	= DiscussHelper::getTable( 'Points' );
		$point->load( $id );

		if( !$point->created )
		{
			$date			= DiscussHelper::getHelper( 'Date' )->dateWithOffset( JFactory::getDate()->toMySQL() );
			$point->created	= $date->toMySQL();
		}

		$model	= $this->getModel( 'Points' );
		$rules	= $model->getRules();

		$this->assign( 'rules'	, $rules );
		$this->assign( 'point'	, $point );

		parent::display($tpl);
	}

	function getFilterState ($filter_state='*')
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'Select State' ) .' -' );
		$state[] = JHTML::_('select.option',  'P', JText::_( 'Published' ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( 'Unpublished' ) );
		$state[] = JHTML::_('select.option',  'A', JText::_( 'Pending' ) );

		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_POINTS' ), 'points' );

		JToolBarHelper::cancel();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_BUTTON' ) , false);
		JToolBarHelper::custom( 'saveNew','save.png','save_f2.png', JText::_( 'COM_EASYDISCUSS_SAVE_NEW_BUTTON' ) , false);
	}
}
