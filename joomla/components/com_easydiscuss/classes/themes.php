<?php
/*--------------------------------------------------------------*\
	Description:	HTML template class.
	Author:			Brian Lozier (brian@massassi.net)
	License:		Please read the license.txt file.
	Last Updated:	11/27/2002
\*--------------------------------------------------------------*/

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

jimport( 'joomla.application.component.view');

require_once( JPATH_ROOT.DS.'components'.DS.'com_easydiscuss'.DS.'constants.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'integrate.php' );
require_once( DISCUSS_HELPERS . DS . 'string.php' );
require_once( DISCUSS_HELPERS . DS . 'tooltip.php' );
require_once( DISCUSS_HELPERS . DS . 'date.php' );


if( !class_exists( 'DiscussThemes' ) )
{
	class DiscussThemes extends JView
	{
		var $vars; /// Holds all the template variables

		/**
		 * Pass theme name from config
		 */
		function DiscussThemes()
		{
			$config 	= DiscussHelper::getConfig();
			$system		= new stdClass();

			if( !isset( $this->vars['system'] ) )
			{
				$my					= JFactory::getUser();

				$profile	= DiscussHelper::getTable( 'Profile' );
				$profile->load($my->id);

				$system->config		= $config;
				$system->my			= $my;
				$system->profile	= $profile;
				$system->acl		= DiscussHelper::getHelper( 'ACL' );
				$this->vars['acl']			= $system->acl;
				$this->vars['system']		= $system;
			}

			$this->_theme = $config->get( 'layout_theme' );
		}

		function getNouns( $text , $count , $includeCount = false )
		{
			return DiscussHelper::getHelper( 'String' )->getNoun( $text , $count , $includeCount );
		}

		function chopString( $string , $length )
		{
			return JString::substr( $string , 0 , $length );
		}

		function getUserTooltip( $id , $name )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load($id);

			ob_start();
			?>
			<div>
				<strong><u><?php echo $name;?></u></strong>
				<img src='<?php echo $profile->getAvatar();?>' width='32' />
			</div>
			<p>
				<?php echo JText::sprintf('COM_EASYDISCUSS_TOOLTIPS_USER_INFO', $name , $profile->getDateJoined() ,  $profile->numPostCreated, $profile->numPostAnswered); ?>
			</p>
			<?php
			$content	= ob_get_contents();
			ob_end_clean();

			return $content;
		}

		function formatDate( $format , $dateString )
		{
			return DiscussDateHelper::toFormat($dateString, $format);
		}

		/**
		 * Set a template variable.
		 */
		public function set($name, $value = null )
		{
			$this->vars[$name] = $value;
		}

		public function loadTemplate( $file = null )
		{
			return $this->_includeFile( $file );
		}

		function _includeFile( $file )
		{
			jimport( 'joomla.filesystem.file' );
			$mainframe	= JFactory::getApplication();
			$template	= $mainframe->getTemplate();

			/**
			 * Now we check the file for template override
			 * if exists, load it
			 * if not load current theme
			 * if not load default
			 */

			if ( JFile::exists( JPATH_ROOT . DS . 'templates' . DS . $template . DS . 'html' . DS . 'com_easydiscuss' . DS . $file ) )
			{
				$file = JPATH_ROOT . DS . 'templates' . DS . $template . DS . 'html' . DS . 'com_easydiscuss' . DS . $file;
			}
			elseif( JFile::exists( DISCUSS_THEMES . DS . $this->_theme . DS . $file ) )
			{
				$file	= DISCUSS_THEMES . DS . $this->_theme . DS . $file;
			}
			else
			{
				$file	= DISCUSS_THEMES . DS . 'default' . DS . $file;
			}

			if( isset( $this->vars ) )
			{
				extract($this->vars);
			}

			$data	= '';
			if( !JFile::exists( $file ) )
			{
				$data	= JText::sprintf( 'COM_EASYDISCUSS_INVALID_TEMPLATE_FILE' , $file );
			}
			else
			{
				ob_start();
				include($file);
				$data	= ob_get_contents();
				ob_end_clean();
			}
			return $data;
		}

		/**
		 * Open, parse, and return the template file.
		 *
		 * @param $file string the template file name
		 */
		function fetch( $file )
		{
			return $this->_includeFile( $file );
		}

		function getUnansweredCount( $tagId = '0' )
		{
			$db		= JFactory::getDBO();

			$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';
			$query	.= '  LEFT JOIN `#__discuss_posts` AS b';
			$query	.= '    ON a.`id`=b.`parent_id`';
			$query	.= '    AND b.`published`=' . $db->Quote('1');

			if(! empty($tagId))
			{
				$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
				$query	.= ' 	ON a.`id` = c.`post_id`';
				$query	.= ' 	AND c.`tag_id` = ' . $db->Quote($tagId);
			}

			$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
			$query	.= ' AND a.`published`=' . $db->Quote('1');
			$query	.= ' AND b.`id` IS NULL';


			$db->setQuery( $query );

			return $db->loadResult();
		}

		function getFeaturedCount($tagId = '0')
		{
			$db = JFactory::getDBO();

			$query  = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';
			if(! empty($tagId)){
				$query  .= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
				$query  .= ' AND b.`tag_id` = ' . $db->Quote($tagId);
			}

			$query  .= ' WHERE a.`featured` = ' . $db->Quote('1');
			$query  .= ' AND a.`parent_id` = ' . $db->Quote('0');
			$query  .= ' AND a.`published` = ' . $db->Quote('1');

			$db->setQuery($query);

			$result = $db->loadResult();

			return $result;
		}

		function json_encode( $value )
		{
			include_once( DISCUSS_CLASSES . DS . 'json.php' );
			$json	= new Services_JSON();

			return $json->encode( $value );
		}

		function json_decode( $value )
		{
			include_once( DISCUSS_CLASSES . DS . 'json.php' );
			$json	= new Services_JSON();

			return $json->decode( $value );
		}

		private function getFieldContents( $files , $isDiscussion = false , $postObj = null )
		{
			$contents 	= '';

			if( isset( $this->vars ) )
			{
				extract($this->vars);
			}

			foreach( $files as $file )
			{
				ob_start();
				include( $file );
				$contents 	.= ob_get_contents();
				ob_end_clean();
			}

			return $contents;
		}

		public function getFieldFiles( $pattern )
		{
			$app 			= JFactory::getApplication();
			$override 		= JPATH_ROOT . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . 'com_easydiscuss';

			$files			= array();
			$includedFiles	= array();

			if( JFolder::exists( $override ) )
			{
				$extraFiles		= JFolder::files( $override , $pattern );

				if( $extraFiles )
				{
					foreach( $extraFiles as $file )
					{
						if( !in_array( $file , $includedFiles ) )
						{
							$files[]			= $override . DS . $file;

							$includedFiles[]	= $file;
						}
					}
				}
			}
			
			$theme 		= JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . $this->_theme;
			$extraFiles	= JFolder::files( $theme , $pattern );

			if( $extraFiles )
			{
				foreach( $extraFiles as $file )
				{
					if( !in_array( $file , $includedFiles ) )
					{
						$files[]			= $theme . DS . $file;

						$includedFiles[]	= $file;
					}
				}
			}
						
			if( $this->_theme != 'default' )
			{
				$theme			= JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . 'default';
				$extraFiles		= JFolder::files( $theme , $pattern );

				if( $extraFiles )
				{
					foreach( $extraFiles as $file )
					{
						if( !in_array( $file , $includedFiles ) )
						{
							$files[]			= $theme . DS . $file;

							$includedFiles[]	= $file;
						}
					}
				}
			}
			
			return $files;
		}

		public function getFieldForms( $isDiscussion = false , $postObj = false )
		{
			$app 		= JFactory::getApplication();
			$override 	= JPATH_ROOT . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . 'com_easydiscuss';
			$theme 		= JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . $this->_theme;
			$pattern 	= 'field.form.(.*).php';

			$files		= $this->getFieldFiles( $pattern );
			$contents 	= '';

			if( $files )
			{
				$contents .= $this->getFieldContents( $files , $isDiscussion , $postObj );
			}

			return $contents;
		}

		public function getFieldHTML( $isDiscussion = false , $postObj = false )
		{
			$app 		= JFactory::getApplication();
			$override 	= JPATH_ROOT . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . 'com_easydiscuss';
			$theme 		= JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . $this->_theme;
			$pattern 	= 'field\.output\.(.*)\.php';

			$files		= $this->getFieldFiles( $pattern );

			$contents 	= '';

			if( $files )
			{
				$contents .= $this->getFieldContents( $files , $isDiscussion , $postObj );
			}

			return $contents;
		}

		public function getFieldTabs( $isDiscussion = false , $postObj = false )
		{
			$app 		= JFactory::getApplication();
			$override 	= JPATH_ROOT . DS . 'templates' . DS . $app->getTemplate() . DS . 'html' . DS . 'com_easydiscuss';
			$theme 		= JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'themes' . DS . $this->_theme;
			$pattern 	= 'field.tab.(.*).php';

			$files		= $this->getFieldFiles( $pattern );
			$contents 	= '';

			if( $files )
			{
				$contents .= $this->getFieldContents( $files , $isDiscussion , $postObj );
			}

			return $contents;
		}

		public function getFieldData( $fieldName , $params )
		{
			$data 		= array();
			$fieldName 	= (string) $fieldName;
			$pattern 	= '/params_' . $fieldName . '[0-9]=(.*)/i';

			preg_match_all( $pattern , $params , $matches );

			if( !empty( $matches[1] ) )
			{
				foreach( $matches[1] as $match )
				{
					$data[]		= $match;
				}

				return $data;
			}

			return false;
		}
	}
}
