<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: November 24th, 2010
Last updated: Novemeber 29th, 2010

This is the developer administration overview page, which 
displays a summary of developer-administered extensible 
content, and provides quick access to each of these tools.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("admin/system/php") . "index.php");
	require_once(relativeAddress("admin/system/php") . "functions.php");
	headers("Developer Administration Panel");
	
//Title
	title("Developer Administration Panel", "This is the developer administration panel, designed for developers to administer extensible areas of the site.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Manage Roles", "roles/index.php", "toolBarItem user");
	echo URL("Leave Administration", "logout.php", "toolBarItem back");
	echo "</div>";
	
//Display the loaded plugins
	echo "<p class=\"homeDivider\">Loaded Plugins</p>\n";
	echo "<blockquote>\n";
	
	$pluginsDirectory = opendir("../");
	
	while ($plugins = readdir($pluginsDirectory)) {
		if ($plugins !== "." && $plugins !== "..") {
			if (is_dir("../" . $plugins) && file_exists("../" . $plugins . "/system/php/index.php")) {
				require("../" . $plugins . "/system/php/index.php");
				echo "<strong>" . $name . "</strong>";
				
				//Link to the administration page if there is an administration plugin
				if (file_exists("../" . $plugins . "/system/php/admin/index.php")) {
					echo " (" .  URL("Manage Plugin", "administer.php?plugin=" . $plugins) . ")";
				}
				
				echo "<br />\n";
				echo "<blockquote>\n";
				echo "Version: " . $version . "<br />\n";
				echo "Author: " . $author . "<br />\n";
				echo "Installation Root: " . URL($pluginRoot . "index.php", $pluginRoot . "index.php", false, "_blank") . "<br />\n";
				echo "Information: " . URL($infoURL, $infoURL, false, "_blank") . "<br />\n";				
				echo "</blockquote>\n";
			}
		}
	}
	
	echo "</blockquote>\n";
	
//Display the list of roles
	echo "<p class=\"homeDivider\">Roles</p>\n";
	echo "<blockquote>\n";
	
	$rolesGrabber = query("SELECT * FROM `roles` ORDER BY `position` ASC", "raw");
	
	while($roles = fetch($rolesGrabber)) {
		echo URL($roles['name'], "roles/details.php?id=" . $roles['id']) . " - " . strip_tags($roles['description']) . "<br />\n";
	}
	
	echo "</blockquote>\n";
	
//Include the footer
	footer();
?>