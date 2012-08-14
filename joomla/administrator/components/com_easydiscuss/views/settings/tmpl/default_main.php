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
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_WORKFLOW' ) , 'workflow');
echo $this->loadTemplate( 'main_workflow' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_COMMENTS' ) , 'comments');
echo $this->loadTemplate( 'main_comments' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_ATTACHMENTS' ) , 'attachments');
echo $this->loadTemplate( 'main_attachments' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_SUBSCRIPTIONS' ) , 'subscriptions');
echo $this->loadTemplate( 'main_subscriptions' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_LIVE_NOTIFICATIONS' ) , 'subscriptions');
echo $this->loadTemplate( 'main_livenotifications' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_SEO' ) , 'seo');
echo $this->loadTemplate( 'main_seo' );
echo $pane->endPanel();
echo $pane->startPanel( JText::_( 'COM_EASYDISCUSS_SETTINGS_WORKFLOW_SUBTAB_MAIL_PARSER' ) , 'parser');
echo $this->loadTemplate( 'main_parser' );
echo $pane->endPanel();
echo $pane->endPane();
