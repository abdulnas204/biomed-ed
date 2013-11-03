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
Last updated: December 1st, 2010

This is the page learning units generator field 
administration overview page.
*/

//Header functions
	headers("Learning Unit Generator Fields", false, true);
	
//Title
	title("Learning Unit Generator Fields", "This is the learning unit generator field panel, which is used to add additional fields to the lesson and question generators.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Add Form Field", "manage_field.php", "toolBarItem new");
	echo URL("Back to Overview", "../index.php", "toolBarItem back");
	echo "</div>";
	
//Display all avaliable fields
	if (exist("fields")) {
		$fieldsGrabber = query("SELECT * FROM `fields` ORDER BY `position` ASC", "raw");
		
		echo "<br />\n<table class=\"dataTable\">\n<tr>\n<th class=\"tableHeader\" width=\"75\">Order</th>\n<th class=\"tableHeader\" width=\"200\">Name</th>\n<th class=\"tableHeader\">Description</th>\n<th class=\"tableHeader\" width=\"50\">Edit</th>\n<th class=\"tableHeader\" width=\"50\">Edit</th>\n</tr>";
		
		while($fields = fetch($fieldsGrabber)) {
			echo "<tr";
			if ($fields['position'] & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			echo reorderMenu("fields", $fields['id']);
			echo cell($fields['name'], 200);
			echo cell(commentTrim(75, $fields['description']));
			echo editURL("manage_field.php?id=" . $fields['id'], $fields['name'], "field");
			echo deleteURL("index.php?id=" . $fields['id'] . "&action=delete", $fields['name'], "field");
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	}
	
//Include the footer
	footer();
?>