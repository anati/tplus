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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'views.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );

class EasyDiscussViewUsers extends EasyDiscussView
{
	function display( $tmpl = null )
	{
		$document	= JFactory::getDocument();

		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_MEMBERS' ) );
		$model      = $this->getModel( 'Users' );
		$result		= $model->getData();
		$pagination	= $model->getPagination();

		$sort			= JRequest::getString('sort', 'latest');
		$filteractive	= JRequest::getString('filter', 'allposts');

		$users			= DiscussHelper::formatUsers( $result );

		$tpl		= new DiscussThemes();
		$tpl->set( 'users'		, $users );
		$tpl->set( 'pagination'	, $pagination );
		echo $tpl->fetch( 'users.list.php' );
	}
}
