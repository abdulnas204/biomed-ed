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
Last updated: Feburary 5th, 2010

This is the overview page for managing the public website.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("cms/system/php") . "index.php");
	require_once(relativeAddress("cms/system/php") . "functions.php");
	headers("Pages Control Panel", "liveSubmit,customVisible", true); 
	
//Reorder pages	
	reorder("pages", "index.php");

//Set page avaliability
	avaliability("pages", "index.php");
	
//Delete a page
	delete("pages", "index.php");
	
//Title
	title("Pages Control Panel", "This is the pages control panel, where you can add, edit, delete, and reorder pages.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo toolBarURL("Create New Page", "manage_page.php", "toolBarItem new") . "\n";
	echo toolBarURL("Manage Sidebar", "sidebar.php", "toolBarItem sideBar") . "\n";

	if (exist("pages")) {
		echo toolBarURL("Preview this Site", "../index.php", "toolBarItem search") . "\n";
	}
	
	echo "</div>\n";

//Display message updates
	message("added", "page", "success", "The page was successfully added");
	message("updated", "page", "success", "The page was successfully updated");

//Pages table
	if (exist("pages")) {
		$pageGrabber = mysql_query("SELECT * FROM pages ORDER BY `position` ASC", $connDBA);
		
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("", "25");
		echo column("Order", "75");
		echo column("Title", "200");
		echo column("Content");
		echo column("Edit", "50");
		echo column("Delete", "50");
		echo "</tr>\n";
		
		while($pageData = mysql_fetch_array($pageGrabber)) {
			echo "<tr";
			if ($pageData['position'] & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			echo option("pages", $pageData['id'], "visible");
			echo reorderMenu("pages", $pageData['id']);
			echo "<td width=\"200\">";
			
			if ($pageData['position'] == "1") {
				$class = "homePage";
			} else {
				$class = "";
			}
			
			echo URL($pageData['title'], "../index.php?page=" . $pageData['id'], $class, false, "Preview the <strong>" . $pageData['title'] . "</strong> page");
			
			echo "</td>\n";
			echo cell(commentTrim(100, $pageData['content']));
			echo editURL("manage_page.php?id=" . $pageData['id'], $pageData['title'], "page");
			echo deleteURL("index.php?action=delete&id=" . $pageData['id'], $pageData['title'], "page");
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	 } else {
		echo "<div class=\"noResults\">This site has no pages. " . URL("Create one now", "manage_page.php") . ".</div>\n";
	 }
	  
//Include the footer
	footer();
?>