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
	function display($tpl = null)
	{
		//Load pane behavior
		jimport('joomla.html.pane');

		$slider		= JPane::getInstance( 'sliders' );

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();

		$this->assignRef( 'slider'		, $slider );
		parent::display($tpl);

	}

	function addButton( $link, $image, $text, $description = '' )
	{
?>
	<li>
		<a href="<?php echo $link;?>">
			<?php echo JHTML::_('image', 'administrator/components/com_easydiscuss/assets/images/'.$image, $text );?>
			<span class="item-title"><?php echo $text;?></span>
			<span class="item-description">
				<i class="tipsArrow"></i>
				<div class="tipsBody"><?php echo $description;?></div>
			</span>
		</a>
	</li>
<?php
	}


	function getTotalPosts()
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM #__discuss_posts WHERE ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote('0');
		$db->setQuery( $query );
		return $db->loadResult();
	}


	function getTotalReplies()
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM #__discuss_posts WHERE ' . $db->nameQuote('parent_id') . ' != ' . $db->Quote('0');
		$db->setQuery( $query );
		return $db->loadResult();
	}


	function getTotalSolved()
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM #__discuss_posts WHERE ' . $db->nameQuote('parent_id') . ' = ' . $db->Quote('0') . ' AND ' . $db->nameQuote('isresolve') . ' = ' . $db->Quote('1');
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getTotalTags()
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM #__discuss_tag';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getTotalCategories()
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT COUNT(1) FROM #__discuss_categories';
		$db->setQuery( $query );
		return $db->loadResult();
	}

	function getRecentNews()
	{
		return DiscussHelper::getRecentNews();
	}

	function registerToolbar()
	{
		// Set the titlebar text
		JToolBarHelper::title( JText::_( 'EasyDiscuss' ), 'home');
	}
}
