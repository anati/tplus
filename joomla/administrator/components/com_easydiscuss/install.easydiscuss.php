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

require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_easydiscuss'.DS.'install.default.php' );

defined('_JEXEC') or die('Restricted access');

function com_install()
{
	$message = array();
	$status = true;

	//get version number from manifest file.
	$installer	= JInstaller::getInstance();
	$manifest	= $installer->getManifest();
	$sourcePath	= $installer->getPath('source');
	$version	= $manifest->document->getElementByPath('version');

	//create default easy blog config
	if( !configExist() )
	{
		if(!createConfig())
		{
			$message[] = 'Warning : The system encounter an error when it tries to create default config. Please kindly proceed to the configuration and save manually.';
		}
	}

	//update Db columns first before proceed.
	updateEasyDiscussDBColumns();

	//update or create menu item.
	if( menuExist() )
	{
		if(!updateMenuItems())
		{
			$message[] = 'Warning : The system encounter an error when it tries to update the menu item. Please kindly update the menu item manually.';
		}
	}
	else
	{
		if(!createMenuItems())
		{
			$message[] = 'Warning : The system encounter an error when it tries to create a menu item. Please kindly create the menu item manually.';
		}
	}

	//check if need to create default category
	if( !blogCategoryExist() )
	{
		if(!createBlogCategory())
		{
			$message[] = 'Warning : The system encounter an error when it tries to create default blog categories. Please kindly create the categories manually.';
		}
	}

	// @task: Create default tags
	if( !defaultTagExists() )
	{
		if(!createDefaultTags())
		{
			$message[] = 'Warning : The system encounter an error when it was trying to create the default tags. Please kindly create the tags manually.';
		}
	}

	// @task: Create default tags
	if( !postExist() )
	{
		if(!createSamplePost())
		{
			$message[] = 'Warning : The system encounter an error when it was trying to create some sample posts.';
		}
	}

	//truncate the table before recreating the default acl rules.
	if(!truncateACLTable())
	{
		$message[] = 'Fatal Error : The system encounter an error when it tries to truncate the acl rules table. Please kindly check your database permission and try again.';
		$status = false;
	}

	//update acl rules
	if(!updateACLRules())
	{
		$message[] = 'Fatal Error : The system encounter an error when it tries to create the ACL rules. Please kindly check your database permission and try again.';
		$status = false;
	}
	else
	{
		//update user group acl rules
		if(!updateGroupACLRules())
		{
			$message[] = 'Fatal Error : The system encounter an error when it tries to create the user groups ACL rules. Please kindly check your database permission and try again.';
			$status = false;
		}
	}

	if( !hasAnyRules() )
	{
		installDefaultRules();
		installDefaultRulesBadges();
	}

	//install default plugin.
	if(!installDefaultPlugin($sourcePath))
	{
		$message[] = 'Warning : The system encounter an error when it tries to install the user plugin. Please kindly install the plugin manually.';
	}

	//copy media files
	if(!copyMediaFiles($sourcePath))
	{
		$message[]	= 'Warning: The system could not copy files to Media folder. Please kindly check the media folder permission.';
		$status		= false;
	}

	if($status)
	{
		$message[] = 'Success : Installation Completed. Thank you for choosing EasyDiscuss as your discussion solution.';
	}

	ob_start();
	?>

	<style type="text/css">
	/**
	 * Messages
	 */

	#easydiscuss-message {
		color: red;
		font-size:13px;
		margin-bottom: 15px;
		padding: 5px 10px 5px 35px;
	}

	#easydiscuss-message.error {
		border-top: solid 2px #900;
		border-bottom: solid 2px #900;
		color: #900;
	}

	#easydiscuss-message.info {
		border-top: solid 2px #06c;
		border-bottom: solid 2px #06c;
		color: #06c;
	}

	#easydiscuss-message.warning {
		border-top: solid 2px #f90;
		border-bottom: solid 2px #f90;
		color: #c30;
	}
	</style>

	<table width="100%" border="0">
		<tr>
			<td>
				<div><img src="http://stackideas.com/images/easydiscuss/success_2.png" /></div>
			</td>
		</tr>
		<?php
			foreach($message as $msgString)
			{
				$msg = explode(":", $msgString);
				switch(trim($msg[0]))
				{
					case 'Fatal Error':
						$classname = 'error';
						break;
					case 'Warning':
						$classname = 'warning';
						break;
					case 'Success':
					default:
						$classname = 'info';
						break;
				}
				?>
				<tr>
					<td><div id="easydiscuss-message" class="<?php echo $classname; ?>"><?php echo $msg[0] . ' : ' . $msg[1]; ?></div></td>
				</tr>
				<?php
			}
		?>
	</table>
	<?php
	$html = ob_get_contents();
	@ob_end_clean();

	echo $html;

	return $status;
}
