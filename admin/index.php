<?php
/*
LICENSE: See "license.php" located at the root installation

This is the system administration overview page, which displays a list of add-ons currently installed add-ons, as well as the ability to install new ones, and manage existing ones.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	headers("System Administration Panel");
	lockAccess();
	
//Reorder pages	
	reorder("addons", "index.php");
	
//Title
	title("System Administration Panel", "This is the system administration panel, which is designed to manage existing add-ons, and install new ones.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo toolBarURL("Install New Add-on", "roles/index.php", "toolBarItem new");
	echo toolBarURL("Leave Administration", "logout.php", "toolBarItem back");
	echo "</div>\n";
	echo "<br />\n";
	
//Display the loaded add-ons
	if (exist("addons")) {
		$addonsGrabber = query("SELECT * FROM `addons` ORDER BY `position`", "raw");
		
		echo "<table class=\"dataTable\">\n";
		echo "<tr>\n";
		echo column("Menu Order", "75");
		echo column("Name", "250");
		echo column("Version", "75");
		echo column("Author", "125");
		echo column("Information", "125");
		echo column("Manage", "50");
		echo "</tr>\n";
		
		while ($addon = fetch($addonsGrabber)) {
			echo "<tr";
			if ($addon['position'] & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			echo reorderMenu("addons", $addon['id']);
			echo cell(URL($addon['name'], $root . $addon['pluginRoot'] . "index.php", false, "_blank"), "250");
			echo cell($addon['version'], "75");
			echo cell($addon['author'], "125");
			echo cell(URL($addon['infoURL'], $addon['infoURL'], false, "_blank"), "125");
			echo editURL($addon['pluginRoot'] . "index.php", $addon['name'], "addon");
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">There are no add-ons currently installed.</div>\n";
	}
	
//Include the footer
	footer();
?>