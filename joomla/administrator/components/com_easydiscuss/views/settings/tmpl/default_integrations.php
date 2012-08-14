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
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_JOMSOCIAL' ) , 'jomsocial');
echo $this->loadTemplate( 'integrations_jomsocial' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_GOOGLE' ) , 'google');
echo $this->loadTemplate( 'integrations_google' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_AUP' ) , 'aup');
echo $this->loadTemplate( 'integrations_aup' );
echo $pane->endPanel();
echo $pane->endPane();
