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

require_once( JPATH_ROOT . DS . 'components' . DS . 'com_easydiscuss' . DS . 'constants.php' );
require_once( DISCUSS_HELPERS . DS . 'helper.php' );
require_once( DISCUSS_HELPERS . DS . 'router.php' );

function EasyDiscussBuildRoute(&$query)
{
	$segments	= array();
	$config		= DiscussHelper::getConfig();


	if(isset($query['view']))
	{
		switch($query['view'])
		{
			case 'post':
				// We don't want to include the view for the entry links.
				unset($query['view']);

				if( isset( $query['layout' ] ) )
				{
					$segments[] = $query[ 'layout' ];
					unset( $query['layout'] );
				}

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getPostAlias( $query['id'] );
					unset($query['id']);
				}
				break;
			case 'profile':
				$segments[] = $query['view'];
				unset($query['view']);

				if(isset($query['layout']))
				{
					$segments[]	= $query['layout'];
					unset($query['layout']);
				}

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getUserAlias( $query['id'] );
					unset($query['id']);
				}

				if( isset( $query[ 'category_id' ] ) )
				{
					$aliases		= DiscussRouter::getCategoryAliases( $query['category_id'] );

					foreach( $aliases as $alias )
					{
						$segments[]	= $alias;
					}

					unset( $query[ 'category_id' ] );
				}

				if( isset( $query[ 'viewtype'] ) )
				{
					$segments[]		= $query[ 'viewtype' ];

					unset( $query[ 'viewtype' ] );
				}

				break;
			case 'index':
				$segments[]     = $query[ 'view' ];
				unset( $query[ 'view' ] );

				if( isset( $query[ 'category_id' ] ) )
				{
					$aliases		= DiscussRouter::getCategoryAliases( $query['category_id'] );

					foreach( $aliases as $alias )
					{
						$segments[]	= $alias;
					}

					unset( $query[ 'category_id' ] );
				}
				break;
			case 'ask':
				$segments[]     = $query[ 'view' ];
				unset( $query[ 'view' ] );

				if( isset( $query[ 'category' ] ) )
				{
					$aliases		= DiscussRouter::getCategoryAliases( $query['category'] );

					foreach( $aliases as $alias )
					{
						$segments[]	= $alias;
					}

					unset( $query[ 'category' ] );
				}
				break;
			case 'tags':
				$segments[] = $query['view'];
				unset($query['view']);

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getTagAlias( $query['id'] );
					unset($query['id']);
				}
				break;
			case 'users':
				$segments[]		= $query[ 'view' ];
				unset( $query[ 'view' ] );

				if( isset( $query[ 'sorting' ] ) )
				{
					$segments[]     = 'latest';
					unset( $query[ 'sorting' ] );
				}
				break;
			case 'badges':
				$segments[]		= $query[ 'view' ];
				unset( $query[ 'view' ] );

				if(isset($query['id']))
				{
					$segments[]	= DiscussRouter::getAlias( 'badges', $query['id'] );
					unset($query['id']);
					unset( $query['layout'] );
				}

				if( isset( $query['layout' ] ) )
				{
					$segments[] = $query[ 'layout' ];
					unset( $query['layout'] );
				}

				break;
			default:
				$segments[] = $query['view'];
				unset( $query['view'] );
		}
	}

	if( isset( $query['filter'] ) )
	{
		$segments[]		= $query[ 'filter' ];
		unset( $query[ 'filter' ] );
	}

	if( isset( $query['sort'] ) )
	{
		$segments[]		= $query['sort'];
		unset( $query[ 'sort' ] );
	}

	if( !isset($query['Itemid'] ) )
	{
		$query['Itemid']	= DiscussRouter::getItemId();
	}
	return $segments;
}

function EasyDiscussParseRoute( $segments )
{
	$vars	= array();
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$config	= DiscussHelper::getConfig();
	$views  = array( 'attachments' , 'categories' , 'index' , 'post' , 'profile' , 'search' , 'tag' , 'tags', 'users' , 'notifications' , 'badges' , 'ask', 'subscriptions' , 'featured');

	// @rule: For view=post&id=xxx we do not include
	if( isset($segments[0]) && !in_array( $segments[0] , $views ) )
	{
		$vars[ 'view' ]	= 'post';

		$count	= count($segments);

		if( $count >= 1 )
		{
			// Submission
			$index      = 0;
			if( $segments[ $index ] == 'submit' )
			{
				$vars[ 'layout' ]   = $segments[ $index ];
				$index          	+= 1;
			}

			if( isset( $segments[ $index ] ) )
			{
				$table			= DiscussHelper::getTable( 'Post' );
				$table->load( $segments[ $index ] , true );

				$vars[ 'id' ]	= $table->id;
				$index          += 1;
			}

			if( isset( $segments[ $index ] ) )
			{
				$vars[ 'sort' ] = $segments[ $index ];
			}
		}
	}

	if( isset($segments[0]) && $segments[0] == 'index' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			$vars[ 'view' ]	= $segments[ 0 ];

			$segments	= DiscussRouter::encodeSegments( $segments );

			if( in_array( $segments[ $count - 1 ] , array( 'unanswered', 'featured', 'new' ) ) )
			{
				$vars[ 'filter' ]	= $segments[ $count - 1 ];


				// Get the last item since the category might be recursive.
				$cid		= $segments[ $count - 2 ];

				$category	= DiscussHelper::getTable( 'Category' );
				$category->load( $cid , true );

				$vars[ 'category_id' ]  = $category->id;

			}
			else
			{
				// Get the last item since the category might be recursive.
				$cid		= $segments[ $count - 1 ];

				$category	= DiscussHelper::getTable( 'Category' );
				$category->load( $cid , true );

				$vars[ 'category_id' ]  = $category->id;
			}
		}
	}

	if( isset($segments[0]) && $segments[0] == 'tags' )
	{
		$count	= count($segments);

		if( $count > 1 )
		{
			$segments   	= DiscussRouter::encodeSegments($segments);

			$table			= DiscussHelper::getTable( 'Tags' );
			$table->load( $segments[ 1 ] , true);
			$vars[ 'id' ]	= $table->id;

			if($count > 2)
			{
				if($segments[2] == 'allposts' || $segments[2] == 'featured' || $segments[2] == 'unanswered')
				{
					$vars[ 'filter' ] = $segments[2];
				}

				if(! empty($segments[3]))
				{
					$vars[ 'sort' ] =  $segments[3];
				}
			}
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'profile' )
	{
		$count	= count($segments);

		if( $count > 1 )
		{
			$segments   	= DiscussRouter::encodeSegments($segments);

			if($segments[1] == 'edit')
			{
				$vars[ 'layout' ] = 'edit';
			}
			else
			{
				$user	= 0;

				$segments[1]	= JString::str_ireplace( '-' , ' ' , $segments[1] );

				if( $id	= DiscussHelper::getUserId( $segments[1] ) )
				{
					$user			= JFactory::getUser( $id );
				}

				if( !$user )
				{
					// For usernames with spaces, we might need to replace with dashes since SEF will rewrite it.
					$id			= DiscussHelper::getUserId( JString::str_ireplace( '-' , ' ' , $segments[1] ) );
					$user		= JFactory::getUser( $id );
				}

				$vars['id']		= $user->id;
			}

			if( isset( $segments[2] ) )
			{
				$vars[ 'viewtype' ]	= $segments[2];
			}
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'users' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			$vars[ 'sort' ]  = $segments[ 1 ];
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'badges' )
	{
		$count	= count($segments);

		if($count > 1)
		{
			if($segments[1] == 'mybadges')
			{
				$vars[ 'layout' ] = 'mybadges';
			}
			else
			{
				$segments		= DiscussRouter::encodeSegments( $segments );
				$table			= DiscussHelper::getTable( 'Badges' );
				$table->load( $segments[ 1 ] , true );

				$vars[ 'id' ]	= $table->id;
				$vars[ 'layout' ] = 'listings';
			}
		}
		$vars[ 'view' ]	= $segments[0];
	}

	if( isset($segments[0]) && $segments[0] == 'ask' )
	{
		$count	= count($segments);

		if($count > 1)
		{
		    $cid    = $segments[1];

			$category	= DiscussHelper::getTable( 'Category' );
			$category->load( $cid , true );

			$vars[ 'category' ]  = $category->id;

		}
		$vars[ 'view' ]	= $segments[0];
	}

	$count	= count($segments);
	if( $count == 1 && in_array( $segments[0 ] , $views ) )
	{
		$vars['view']	= $segments[0];
	}

	// var_dump($vars);

	unset( $segments );
	return $vars;
}
