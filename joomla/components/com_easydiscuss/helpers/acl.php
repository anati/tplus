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

class DiscussACLHelper
{
	var $ruleset = null;

	function __construct()
	{
		$this->getRuleSet();
	}

	public function getRuleSet( $cid='' )
	{
		static $rulesData = null;

		$my		= empty($cid) ? JFactory::getUser() : JFactory::getUser($cid);

		if( !isset( $rulesData[ $my->id ] ) )
		{
			$db		= JFactory::getDBO();
			$config = DiscussHelper::getConfig();

			$rulesets			= new stdClass();
			$rulesets->rules	= new stdClass();

			if( !empty($my->id) )
			{
				$rulesets->id		= $my->id;
				$rulesets->name		= $my->name;
				$rulesets->group	= $my->usertype;

				// @Task: Retreive assigned rulesets for this particular user.
				// Assigned rulesets always have higher precedence
				$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_acl_group' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $my->id ) . ' '
						. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'assigned' )
						. ' ORDER BY `acl_id`';

				$db->setQuery( $query );
				$result = $db->loadObjectList();

				if(count($result) > 0)
				{
					$rulesets	= $this->mapRules( $result , $rulesets );
				}
				else
				{
				    if(DiscussHelper::getJoomlaVersion() >= '1.6')
				    {
				        // get user's joomla usergroups ids.
				        $groupIds   = '';
						$query		= 'SELECT `group_id` FROM `#__user_usergroup_map` WHERE `user_id` = ' . $db->Quote($my->id);
				        $db->setQuery($query);

				        $groupIds   = $db->loadResultArray();
						$groups		= array();

						// get the last index.
				        for($i = 0; $i < count($groupIds); $i++)
				        {
					        $grpId   = $groupIds[$i];
							$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_acl_group' ) . ' '
									. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote($grpId) . ' '
									. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' )
									. ' ORDER BY `acl_id`';

							$db->setQuery( $query );
							$groups[] = $db->loadObjectList();
						}

						// Allow explicit overrides in the groups
						// If user A is in group A (allow) and group B (not allowed) , user A should be allowed
						$result		= array();

						foreach( $groups as $group )
						{
							foreach( $group as $rule )
							{
								if( !isset( $result[ $rule->acl_id ] ) )
								{
									$result[ $rule->acl_id ]	= new stdClass();
								}

								if( isset( $result[ $rule->acl_id]->acl_id ) && $result[ $rule->acl_id ]->status != '1' || !isset( $result[ $rule->acl_id]->acl_id ) )
								{
									$result[ $rule->acl_id ]->acl_id	= $rule->acl_id;
									$result[ $rule->acl_id ]->status	= $rule->status;
								}
							}
						}
				    }
				    else
				    {
						$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_acl_group' ) . ' '
								. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $my->gid ) . ' '
								. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' )
								. ' ORDER BY `acl_id`';

						$db->setQuery( $query );
						$group 	= $db->loadObjectList();
						$result = array();

						foreach( $group as $rule )
						{
							if( !isset( $result[ $rule->acl_id ] ) )
							{
								$result[ $rule->acl_id ]	= new stdClass();
							}

							if( isset( $result[ $rule->acl_id]->acl_id ) && $result[ $rule->acl_id ]->status != '1' || !isset( $result[ $rule->acl_id]->acl_id ) )
							{
								$result[ $rule->acl_id ]->acl_id	= $rule->acl_id;
								$result[ $rule->acl_id ]->status	= $rule->status;
							}
						}
					}

					$rulesets	= $this->mapRules( $result , $rulesets );
				}
			}
			else
			{
				$rulesets->id 		= '0';
				$rulesets->name 	= 'guest';
				$rulesets->group	= 'public';

				if(DiscussHelper::getJoomlaVersion() >= '1.6')
				{
					$query	= 'SELECT `id` FROM ' . $db->nameQuote( '#__usergroups' ) . ' '
							. 'WHERE ' . $db->nameQuote( 'parent_id' ) . '=' . $db->Quote( '0' );
					$db->setQuery( $query );
					$publicGroup = $db->loadResult();
				}
				else
				{
					$publicGroup = '28';
				}

				$query	= 'SELECT * FROM ' . $db->nameQuote( '#__discuss_acl_group' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'content_id' ) . '=' . $db->Quote( $publicGroup ) . ' '
						. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( 'group' )
						. ' ORDER BY `acl_id`';

				$db->setQuery( $query );
				$tmp = $db->loadObjectList();

				$result = array();

				foreach( $tmp as $row )
				{
				    $result[ $row->acl_id ] = $row;
				}

				$rulesets	= $this->mapRules( $result , $rulesets );
			}

			$rulesData[ $my->id ]	= $rulesets;
		}

		$this->ruleset = $rulesData[ $my->id ];

		return $rulesData[ $my->id ];
	}

	public function mapRules( $result , $rulesets )
	{
		// @Task: Retrieve rules
		$rules				= $this->getRules( 'id' );

		foreach($rules as $rule)
		{
		    $rulesets->rules->{$rule->action} = isset($result[ $rule->id ])? (INT) $result[ $rule->id ]->status : (INT) $rule->default;
		}
		return $rulesets;
	}

	public function getRules($key='')
	{
		$db = JFactory::getDBO();
		$sql = 'SELECT * FROM '.$db->nameQuote('#__discuss_acl').' WHERE `published`=1 ORDER BY `id` ASC';
		$db->setQuery($sql);

		return $db->loadObjectList($key);
	}

	public function allowed($action, $default)
	{
		$allowed = isset($this->ruleset->rules->{$action})? $this->ruleset->rules->$action : $default;
		return $allowed;
	}
}
