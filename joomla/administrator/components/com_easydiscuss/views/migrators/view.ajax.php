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

require_once( DISCUSS_ADMIN_ROOT . DS . 'views.php');
require_once( DISCUSS_CLASSES . DS . 'json.php' );

class EasyDiscussViewMigrators extends EasyDiscussAdminView
{
	public function kunena()
	{
		$ajax		= new Disjax();

		// @task: Get list of categories from Kunena first.
		$categories	= $this->getKunenaCategories();

		// @task: Add some logging
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_CATEGORIES' , count( $categories ) ) , 'kunena' );

		$json	= new Services_JSON();
		$items	= array();

		foreach( $categories as $category )
		{
			$items[]	= $category->id;
		}

		$data	= $json->encode( $items );

		// @task: Start migration process, passing back to the AJAX methods
		$ajax->script( 'runMigrationCategory("kunena", ' . $data . ');' );

		return $ajax->send();
	}

	public function showMigrationButton( &$ajax )
	{
		$ajax->script( 'Foundry(".migrator-button").show();' );
	}

	public function kunenaCategoryItem( $current , $categories )
	{
		$ajax	= new Disjax();

		$kCategory	= $this->getKunenaCategory( $current );


		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( !is_array( $categories ) )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_CATEGORY_MIGRATION_COMPLETED' ) , 'kunena' );

			$posts		= $this->getKunenaPostsIds();
			$data		= $this->json_encode( $posts );

			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_TOTAL_POSTS' , count( $posts ) ) , 'kunena' );

			// @task: Run migration for post items.
			$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );
			return $ajax->send();
		}

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_kunena' , $current , 'category') )
		{
			$data	= $this->json_encode( $categories );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED_SKIPPING' , $kCategory->name ) , 'kunena' );
			$ajax->script( 'runMigrationCategory("kunena" , ' . $data . ');' );
			return $ajax->send();
		}

		// @task: Create the category
		$category	= DiscussHelper::getTable( 'Category' );
		$this->mapKunenaCategory( $kCategory , $category );
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_KUNENA_CATEGORY_MIGRATED' , $kCategory->name ) , 'kunena' );

		$data	= $this->json_encode( $categories );

		$ajax->script( 'runMigrationCategory("kunena" , ' . $data . ');' );

		$ajax->send();
	}

	public function kunenaPostItem( $current , $items )
	{
		$ajax	= new Disjax();

		// @task: If categories is no longer an array, then it most likely means that there's nothing more to process.
		if( !is_array( $items ) )
		{
			$this->log( $ajax , JText::_( 'COM_EASYDISCUSS_MIGRATORS_MIGRATION_COMPLETED' ) , 'kunena' );
			$this->showMigrationButton( $ajax );
			return $ajax->send();
		}


		// @task: Map kunena post item with EasyDiscuss items.
		$kItem	= $this->getKunenaPost( $current );
		$item	= DiscussHelper::getTable( 'Post' );

		// @task: Skip the category if it has already been migrated.
		if( $this->migrated( 'com_kunena' , $current , 'post') )
		{
			$data	= $this->json_encode( $items );
			$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED_SKIPPING' , $kItem->subject ) , 'kunena' );
			$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );
			return $ajax->send();
		}


		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_MIGRATED' , $kItem->subject ) , 'kunena' );
		$this->mapKunenaItem( $kItem , $item );

		// @task: Once the post is migrated successfully, we'll need to migrate the child items.
		$this->log( $ajax , JText::sprintf( 'COM_EASYDISCUSS_MIGRATORS_POST_REPLIES_MIGRATED' , $kItem->subject ) , 'kunena' );
		$this->mapKunenaItemChilds( $kItem , $item );

		$data	= $this->json_encode( $items );

		$ajax->script( 'runMigrationItem("kunena" , ' . $data . ');' );

		$ajax->send();
	}

	private function json_encode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->encode( $data );

		return $data;
	}

	private function json_decode( $data )
	{
		$json	= new Services_JSON();
		$data	= $json->decode( $data );

		return $data;
	}

	private function log( &$ajax , $message , $type )
	{
		$ajax->script( 'appendLog("' . $type . '" , "' . $message . '");' );
	}

	private function mapKunenaCategory( $kCategory , &$category )
	{
		$category->set( 'title'			, $kCategory->name );

		$category->set( 'description'	, $kCategory->description );
		$category->set( 'published'		, $kCategory->published );
		$category->set( 'parent_id'		, 0 );

		// @task: Since Kunena does not store the creator of the category, we'll need to assign a default owner.
		$category->set( 'created_by'	, DiscussHelper::getDefaultSAIds() );

		// @TODO: Detect if it has a parent id and migrate according to the category tree.
		$category->store( true );

		$this->added( 'com_kunena' , $category->id , $kCategory->id , 'category' );
	}

	private function mapKunenaItem( $kItem , &$item , &$parent = null )
	{
		$content	= $this->getKunenaMessage( $kItem );

		$item->set( 'content'		, $content );
		$item->set( 'title' 		, $kItem->subject );
		$item->set( 'category_id' 	, $this->getNewCategory( $kItem ) );
		$item->set( 'user_id'		, $kItem->userid );
		$item->set( 'user_type' 	, DISCUSS_POSTER_MEMBER );
		$item->set( 'hits'			, $kItem->hits );
		$item->set( 'created'	 	, JFactory::getDate( $kItem->time )->toMySQL() );
		$item->set( 'created' 		, JFactory::getDate( $kItem->time )->toMySQL() );
		$item->set( 'poster_name'	, $kItem->name );
		$item->set( 'parent_id'		, 0 );

		// @task: If this is a child post, we definitely have the item's id.
		if( $parent )
		{
			$item->set( 'parent_id'	, $parent->id );
		}

		$item->set( 'islock'		, $kItem->locked );
		$item->set( 'poster_email'	, $kItem->email );
		$item->set( 'published'		, DISCUSS_ID_PUBLISHED );

		if( !$kItem->userid )
		{
			$item->set( 'user_type' , DISCUSS_POSTER_GUEST );
		}

		$item->store();

		// @task: Get attachments
		$files	= $this->getKunenaAttachments( $kItem );

		if( $files )
		{
			foreach( $files as $kAttachment )
			{
				$attachment	= DiscussHelper::getTable( 'Attachments');

				$attachment->set( 'uid' 	, $item->id );
				$attachment->set( 'size'	, $kAttachment->size );
				$attachment->set( 'title'	, $kAttachment->filename );
				$attachment->set( 'type'	, $item->getType() );
				$attachment->set( 'published',	DISCUSS_ID_PUBLISHED );
				$attachment->set( 'mime'	, $kAttachment->filetype );

				// Regenerate the path
				$path	= JUtility::getHash( $kAttachment->filename . JFactory::getDate()->toMySQL() );
				$attachment->set( 'path'	, $path );

				// Copy files over.
				$config		= DiscussHelper::getConfig();
				$storage	= DISCUSS_MEDIA_PATH . DS . trim( $config->get( 'attachment_path' ) , DS ) . DS . $path;
				$kStorage	= JPATH_ROOT . DS . rtrim( $kAttachment->folder , '/' )  . DS . $kAttachment->filename;

				JFile::copy( $kStorage , $storage );

				// @task: Since Kunena does not store this, we need to generate the own creation timestamp.
				$attachment->set( 'created'	, JFactory::getDate()->toMySQL() );

				$attachment->store();
			}
		}

		// we need to do it here... boh pien.
		//perform cleanup


		$this->added( 'com_kunena' , $item->id , $kItem->id , 'post' );
	}

	private function mapKunenaItemChilds( $kItem , &$parent )
	{
		$items	= $this->getKunenaPosts( $kItem );

		if( !$items )
		{
			return false;
		}

		foreach( $items as $kChildItem )
		{
			$item	= DiscussHelper::getTable( 'Post' );
			$this->mapKunenaItem( $kChildItem , $item , $parent );
		}
	}

	private function added( $component , $internalId , $externalId , $type )
	{
		$migrator	= DiscussHelper::getTable( 'Migrators' );
		$migrator->set( 'component' 	, $component );
		$migrator->set( 'external_id'	, $externalId );
		$migrator->set( 'internal_id'	, $internalId );
		$migrator->set( 'type'			, $type );

		return $migrator->store();
	}

	private function getNewCategory( $kItem )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT ' . $db->nameQuote( 'internal_id' ) . ' '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $kItem->catid ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( 'category' ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( 'com_kunena' );

		$db->setQuery( $query );
		$categoryId	= $db->loadResult();

		return $categoryId;
	}

	private function getKunenaMessage( $kItem )
	{
		$db		= JFactory::getDBO();

		$query	= 'SELECT ' . $db->nameQuote( 'message' ) . ' FROM ' . $db->nameQuote( '#__kunena_messages_text' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'mesid' ) . '=' . $db->Quote( $kItem->id );
		$db->setQuery( $query );

		$message	= $db->loadResult();

		// @task: Replace unwanted bbcode's.
		$message    = preg_replace( '/\[attachment\="?(.*?)"?\](.*?)\[\/attachment\]/ms' , '' , $message );

		return $message;
	}

	private function getKunenaPostsIds()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( 0 );
		$db->setQuery( $query );
		return $db->loadResultArray();
	}

	private function getKunenaPost( $id )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_messages' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );
		$item	= $db->loadObject();

		return $item;
	}

	private function getKunenaAttachments( $kItem )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_attachments' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'mesid' ) . '=' . $db->Quote( $kItem->id );
		$db->setQuery( $query );
		$attachments	= $db->loadObjectList();

		return $attachments;
	}

	private function getKunenaPosts( $kItem = null , $kCategory = null )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_messages' );

		if( !is_null( $kItem ) )
		{
			$query	.= ' WHERE ' . $db->nameQuote( 'parent' ) . ' = ' . $db->Quote( $kItem->id );
		}
		else
		{
			$query	.= ' WHERE ' . $db->nameQuote( 'parent' ) . '=' . $db->Quote( 0 );
		}

		if( !is_null( $kCategory ) )
		{
			$query	.= ' AND ' . $db->nameQuote( 'catid' ) . '=' . $db->Quote( $kCategory->id );
		}


		$db->setQuery( $query );

		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}

	private function getKunenaCategory( $id )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_categories' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . '=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadObject();
	}

	/**
	 * Determines if an item is already migrated
	 */
	private function migrated( $component , $externalId , $type )
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT COUNT(1) '
				. 'FROM ' . $db->nameQuote( '#__discuss_migrators' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'external_id' ) . ' = ' . $db->Quote( $externalId ) . ' '
				. 'AND ' . $db->nameQuote( 'type' ) . ' = ' . $db->Quote( $type ) . ' '
				. 'AND ' . $db->nameQuote( 'component' ) . ' = ' . $db->Quote( $component );
		$db->setQuery( $query );
		$exists	= $db->loadResult() > 0;

		return $exists;
	}

	/**
	 * Retrieves a list of categories in Kunena
	 *
	 * @param	null
	 * @return	string	A JSON string
	 **/
	private function getKunenaCategories()
	{
		$db		= JFactory::getDBO();
		$query	= 'SELECT * FROM ' . $db->nameQuote( '#__kunena_categories' ) . ' '
				. 'ORDER BY ' . $db->nameQuote( 'parent' ) . ' ASC';
		$db->setQuery( $query );
		$result	= $db->loadObjectList();

		if( !$result )
		{
			return false;
		}

		return $result;
	}
}
