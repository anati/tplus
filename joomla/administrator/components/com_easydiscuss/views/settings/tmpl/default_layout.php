<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$pane	= JPane::getInstance('Tabs');

echo $pane->startPane("submain");
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_SUBTAB_GENERAL' ) , 'general');
echo $this->loadTemplate( 'layout_general' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_SUBTAB_AVATARS' ) , 'avatars');
echo $this->loadTemplate( 'layout_avatars' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_SUBTAB_FEATURED' ) , 'featured');
echo $this->loadTemplate( 'layout_featured' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_LAYOUT_SUBTAB_CATEGORY' ) , 'category');
echo $this->loadTemplate( 'layout_category' );
echo $pane->endPanel();
echo $pane->endPane();
