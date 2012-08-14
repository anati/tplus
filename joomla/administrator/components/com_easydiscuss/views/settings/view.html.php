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

class EasyDiscussViewSettings extends EasyDiscussAdminView
{

	function display($tpl = null)
	{
		//initialise variables
		JHTML::_('behavior.tooltip');

		$config			= DiscussHelper::getConfig();
		$defaultSAId	= DiscussHelper::getDefaultSAIds();
		$joomlaVersion	= DiscussHelper::getJoomlaVersion();

		$this->assignRef( 'config' , $config );
		$this->assignRef( 'defaultSAId' , $defaultSAId );
		$this->assignRef( 'joomlaversion' , $joomlaVersion );

		parent::display($tpl);
	}

	function getEmailsTemplate()
	{
		JHTML::_('behavior.modal' , 'a.modal' );
		$html	= '';
		$emails = array();

		$path 	= JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . 'default';

		$emails 	= JFolder::files( $path , 'email.*'  );


		ob_start();

		foreach($emails as $email)
		{
		?>
			<div>
				<div style="float:left; margin-right:5px;">
				<?php echo JText::_($email); ?>
				</div>
				<div style="margin-top: 5px;">
				[
				<?php
				if(is_writable(JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'themes'.DS.'default'.DS.$email))
				{
				?>
					<a class="modal" rel="{handler: 'iframe', size: {x: 700, y: 500}}" href="index.php?option=com_easydiscuss&view=settings&layout=editEmailTemplate&file=<?php echo $email; ?>&tmpl=component&browse=1"><?php echo JText::_('COM_EASYDISCUSS_EDIT');?></a>
				<?php
				}
				else
				{
				?>
					<span style="color:red; font-weight:bold;"><?php echo JText::_('COM_EASYDISCUSS_UNWRITABLE');?></span>
				<?php
				}
				?>
				]
				</div>
			</div>
		<?php
		}
		$html   = ob_get_contents();
		@ob_end_clean();

		return $html;
	}

	public function getCategories()
	{
		$db			= JFactory::getDBO();
		$query		= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_category' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'published' ) . '=' . $db->Quote( 1 );
		$db->setQuery( $query );
		$categories	= $db->loadObjectList();

		return $categories;
	}

	function editEmailTemplate()
	{
		$file		= JRequest::getVar('file', '', 'GET');
		$filepath	= JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'themes'.DS.'default'.DS.$file;
		$content	= '';
		$html		= '';
		$msg		= JRequest::getVar('msg', '', 'GET');
		$msgType	= JRequest::getVar('msgtype', '', 'GET');

		ob_start();

		if(!empty($msg))
		{
			$document = JFactory::getDocument();
			$document->addStyleSheet( JURI::root() . '/components/com_easydiscuss/assets/css/common.css' );
		?>
			<div id="discuss-message" class="<?php echo $msgType; ?>"><?php echo $msg; ?></div>
		<?php
		}

		if(is_writable($filepath))
		{
			$content = JFile::read($filepath);
		?>
			<form name="emailTemplate" id="emailTemplate" method="POST">
				<textarea rows="28" cols="93" name="content"><?php echo $content; ?></textarea>
				<input type="hidden" name="option" value="com_easydiscuss">
				<input type="hidden" name="controller" value="settings">
				<input type="hidden" name="task" value="saveEmailTemplate">
				<input type="hidden" name="file" value="<?php echo $file; ?>">
				<input type="hidden" name="tmpl" value="component">
				<input type="hidden" name="browse" value="1">
				<input type="submit" name="save" value="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE' );?>">
				<?php if(DiscussHelper::getJoomlaVersion() <= '1.5') : ?>
				<input type="button" value="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_CLOSE' );?>" onclick="window.parent.document.getElementById('sbox-window').close();">
				<?php endif; ?>
			</form>
		<?php
		}
		else
		{
		?>
			<div><?php echo JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_UNWRITABLE'); ?></div>
		<?php
		}

		$html = ob_get_contents();
		@ob_end_clean();

		echo $html;
	}


	function getThemes( $selectedTheme = 'default' )
	{
		$html	= '<select name="layout_theme" class="inputbox">';

		$themes	= $this->get( 'Themes' );

		for( $i = 0; $i < count( $themes ); $i++ )
		{
			$theme		= JString::strtolower( $themes[ $i ] );

			if ( $theme != 'dashboard' ) {
				$selected	= ( $selectedTheme == $theme ) ? ' selected="selected"' : '';
				$html		.= '<option' . $selected . '>' . $theme . '</option>';
			}
		}

		$html	.= '</select>';

		return $html;
	}

	function getEditorList( $selected )
	{
		$db		= JFactory::getDBO();

		// compile list of the editors
		if(DiscussHelper::getJoomlaVersion() >= '1.6')
		{
			$query = 'SELECT `element` AS value, `name` AS text'
					.' FROM `#__extensions`'
					.' WHERE `folder` = "editors"'
					.' AND `type` = "plugin"'
					.' AND `enabled` = 1'
					.' ORDER BY ordering, name'
					;
		}
		else
		{
			$query = 'SELECT element AS value, name AS text'
					.' FROM #__plugins'
					.' WHERE folder = "editors"'
					.' AND published = 1'
					.' ORDER BY ordering, name'
					;
		}

		//echo $query;

		$db->setQuery($query);
		$editors = $db->loadObjectList();

	    if(count($editors) > 0)
	    {
			if(DiscussHelper::getJoomlaVersion() >= '1.6')
			{
			    $lang = JFactory::getLanguage();
				for($i = 0; $i < count($editors); $i++)
				{
				    $editor =& $editors[$i];
					$lang->load($editor->text . '.sys', JPATH_ADMINISTRATOR, null, false, false);
				    $editor->text   = JText::_($editor->text);
				}
			}
	    }

		$bbcode = new stdClass();
		$bbcode->value  = 'bbcode';
		$bbcode->text   = JText::_( 'Built-in BBCode' );

		array_unshift( $editors, $bbcode);

		return JHTML::_('select.genericlist',  $editors , 'layout_editor', 'class="inputbox" size="1"', 'value', 'text', $selected );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_SETTINGS' ), 'settings' );

		JToolBarHelper::back( 'Home' , 'index.php?option=com_easydiscuss');
		JToolBarHelper::divider();
		JToolBarHelper::save();
		JToolBarHelper::apply( 'apply' );
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
