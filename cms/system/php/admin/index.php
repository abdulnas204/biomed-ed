<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: December 20th, 2010
Last updated: Janurary 3rd, 2011

This is the CMS settings overview page.
*/

//Header functions
	require_once('../../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Content Management Administration", false, true);
	lockAccess();
	
//Title
	title("Content Management Administration", "This is the content management administration panel, which is used to create site templates, and manage the site's global settings.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo toolBarURL("Add Template", "manage_template.php", "toolBarItem new");
	echo toolBarURL("Site Settings", "site_settings.php", "toolBarItem settings");
	echo "</div>\n";
	
//Display all avaliable fields
	if (exist("templates")) {
		$templatesGrabber = query("SELECT * FROM `templates` ORDER BY `id` ASC", "raw");
		$count = 1;
		
		echo "<br />\n<table class=\"dataTable\">\n<tr>\n";
		echo column("Name", "200");
		echo column("Description");
		echo column("Edit", "50");
		echo column("Delete", "50");
		echo "</tr>\n";
		
		while($templates = fetch($templatesGrabber)) {
			echo "<tr";
			if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			echo cell($templates['name'], 200);
			echo cell(commentTrim(75, $templates['description']));
			echo editURL("manage_field.php?id=" . $templates['id'], $templates['name'], "field");
			echo deleteURL("index.php?id=" . $templates['id'] . "&action=delete", $templates['name'], "field");
			echo "</tr>\n";
			
			$count ++;
		}
		
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">There are no templates currently avaliable. Please " . URL("add one now", "manage_template.php") . ".</div>\n";
	}
	
//Include the footer
	footer();
?>