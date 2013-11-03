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
Last updated: Novemeber 28th, 2010

This is the role management page, which is used to create 
and update roles.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("admin/system/php") . "index.php");
	require_once(relativeAddress("admin/system/php") . "functions.php");
	
//Check to see if the role exists
	validateName("roles", "name");
	
//Top content
	headers("Manage Role", "tinyMCESimple,validate");
	
//Grab the role information
	if (isset($_GET['id'])) {
		if (exist("roles", "id", $_GET['id'])) {
			$role = query("SELECT * FROM `roles` WHERE `id` = '{$_GET['id']}'");
		} else {
			redirect("index.php");
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['description']) && sizeof($_POST) > 3) {
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		$privilegesPrep = $_POST;
		
		unset($privilegesPrep['submit'], $privilegesPrep['name'], $privilegesPrep['description']);
		
		$privileges = escape(serialize($privilegesPrep));
		
		if (exist("roles", "name", $name)) {
			if ($role['name'] !== $name) {
				redirect($_SERVER['REQUEST_URI']);
			}
		}
		
		if (!isset($role)) {
			$position = lastItem("roles");
			
			query("INSERT INTO `roles` (
				  `id`, `position`, `name`, `description`, `privileges`
				  ) VALUES (
				  NULL, '{$position}', '{$name}', '{$description}', '{$privileges}'
				  )");
				  
			redirect("index.php?message=inserted");
		} else {
			query("UPDATE `roles` SET `name` = '{$name}', `description` = '{$description}', `privileges` = '{$privileges}' WHERE `id` = '{$role['id']}'");
			redirect("index.php?message=updated");
		}
	}
	
//Title
	title("Manage Role", "This is the role management page, where roles can be created and updated.");
	
//Role form
	echo form("manageRole");
	catDivider("Role Information", "one", true);
	echo "<blockquote>\n";
	directions("Role name", true);
	indent(textField("name", "name", false, false, false, true, "ajax[ajaxName]", false, "role", "name"));
	directions("Description", true);
	indent(textArea("description", "description", "small", true, false, false, "role", "description"));
	echo "</blockquote>\n";
	
	catDivider("Role Privileges", "two");
	echo "<blockquote>\n";
	echo "<p>Below is a list of plugins, along with all of the privileges associated with each plugin. When creating or modifying a role, each of these privilege types can be enabled or disabled as needed by selecting &quot;Yes&quot; or &quot;No&quot;.</p>\n";
	
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
						if (!isset($role)) {
							echo "<p>" . $privilege . ": " . radioButton($privilege, $privilege, "Yes,No", "1,0", true, true, false, "1") . "</p>\n";
						} else {
							$roles = unserialize($role['privileges']);
							$currentPrivilege = str_replace(" ", "_", $privilege);
							
							if (array_key_exists($currentPrivilege, $roles)) {
								if ($roles[$currentPrivilege] == "1") {
									$selected = "1";
								} else {
									$selected = "0";
								}
							} else {
								$selected = "0";
							}
							
							echo "<p>" . $privilege . ": " . radioButton($privilege, $privilege, "Yes,No", "1,0", true, true, false, $selected) . "</p>\n";
						}
					}
					
					echo "</blockquote>\n";
				}
			}
		}
	}
	
	echo "</blockquote>\n";
	
	catDivider("Submit", "three");
	formButtons();
	echo closeForm(true);

//Include the footer
	footer();
?>
