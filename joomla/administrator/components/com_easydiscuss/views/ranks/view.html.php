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

class EasyDiscussViewRanks extends EasyDiscussAdminView
{
	function display($tpl = null)
	{

		$model	= $this->getModel( 'Ranks' );
		$ranks	= $model->getRanks();

		$config = DiscussHelper::getConfig();

		$this->assign( 'ranks'	, $ranks );
		$this->assign( 'config'	, $config );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::back( JText::_( 'COM_EASYDISCUSS_TOOLBAR_HOME' ) , 'index.php?option=com_easydiscuss');
		JToolBarHelper::divider();

		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_RANKING' ), 'ranks' );
		JToolBarHelper::custom( 'save','save.png','save_f2.png', JText::_( 'Save' ) , false);
	}
}
