<?php
/*
LICENSE: See "license.php" located at the root installation

This is the role details page, which displays all of the details and privileges that are assigned to a role.
*/

//Header functions
	require_once('../../system/server/index.php');
	lockAccess();
	
//Grab the role information
	if (isset($_GET['id'])) {
		if (exist("roles", "id", $_GET['id'])) {
			$role = query("SELECT * FROM `roles` WHERE `id` = '{$_GET['id']}'");
		} else {
			redirect("index.php");
		}
	}
	
//Top content
	headers($role['name'] . " Role");
	
//Title
	title($role['name'] . " Role", "This is the role details page, which displays all of the details and privileges that are assigned to a role.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo URL("Back to Roles", "index.php", "toolBarItem back") . "\n";
	echo "</div>\n<br />\n";
	
//Role details
	catDivider("Role Information", "one", true);
	echo "<blockquote>\n";
	directions("Role name");
	indent($role['name']);
	directions("Description");
	indent($role['description']);
	echo "</blockquote>\n";
	
	catDivider("Role Privileges", "two");
	echo "<blockquote>\n";
	echo "<p>Below is a list of plugins, along with all of the privileges associated with each plugin.</p>\n";
	
	//Grab all of the config options for each addon
	$addons = query("SELECT * FROM `addons` ORDER BY `position` ASC", "raw");
	
	while ($addon = fetch($addons)) {
		if (!empty($addon['privileges'])) {
		//Return the settings for this addon
			echo "<p class=\"homeDivider\">" . $addon['name'] . "</p>\n";
			echo "<blockquote>\n";
			
			foreach(arrayRevert($addon['privileges']) as $privilege) {
				$roles = arrayRevert($role['privileges']);
				$currentPrivilege = str_replace(" ", "_", $privilege);
				
				if (array_key_exists($currentPrivilege, $roles)) {
					if ($roles[$currentPrivilege] == "1") {
						$allowed = "<strong style=\"color:#00FF00\">Yes</strong>";
					} else {
						$allowed = "<strong style=\"color:#FF0000\">No</strong>";
					}
				} else {
					$allowed = "<strong style=\"color:#FF0000\">No</strong>";
				}
				
				echo "<p>" . $privilege . ": " . $allowed . "</p>\n";
			}
					
			echo "</blockquote>\n";
		}
	}
	
	echo "</blockquote>\n";
	echo "</div>\n";
	
//Include the footer
	footer();
?>