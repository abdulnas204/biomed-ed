<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: November 28th, 2010
Last updated: Novemeber 29th, 2010

This is the role details page, which displays all of the 
details and privileges that are assigned to a role.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("admin/system/php") . "index.php");
	require_once(relativeAddress("admin/system/php") . "functions.php");
	
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
	
	//Grab all of the config files for each plugin
	$pluginsDirectory = opendir("../../");
	
	while ($plugins = readdir($pluginsDirectory)) {
		if ($plugins !== "." && $plugins !== "..") {
			if (is_dir("../../" . $plugins) && file_exists("../../" . $plugins . "/system/php/index.php")) {
				require("../../" . $plugins . "/system/php/index.php");
				
				if (!empty($privileges)) {
				//Return the settings for this plugin
					echo "<p class=\"homeDivider\">" . $name . "</p>\n";
					echo "<blockquote>\n";
					
					foreach($privileges as $privilege) {
						$roles = unserialize($role['privileges']);
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
		}
	}
	
	echo "</blockquote>\n";
	catDivider(false, false, false, true);
//Include the footer
	footer();
?>