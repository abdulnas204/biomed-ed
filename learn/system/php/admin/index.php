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
Last updated: December 15th, 2010

This is the page learning units generator field 
administration overview page.
*/

//Header functions
	require_once('../../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Learning Unit Generator Fields", false, true);
	lockAccess();
	
//Delete a field
	if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
		$column = $_GET['id'];
		$units = query("SELECT * FROM `learningunits`", "raw");
		$organizations = query("SELECT * FROM `organizations`", "raw");
		
		while ($unit = fetch($units)) {
			query("ALTER TABLE `test_{$unit['id']}` DROP COLUMN `field_{$column}`", false, false);
		}
		
		while ($organization = fetch($organizations)) {
			query("ALTER TABLE `questionbank_{$organization['id']}` DROP COLUMN `field_{$column}`", false, false);
		}
		
		query("ALTER TABLE `questionbank_0` DROP COLUMN `field_{$column}`", false, false);
		query("ALTER TABLE `learningunits` DROP COLUMN `field_{$column}`", false, false);
		delete("fields", "index.php");
	}
	
//Title
	title("Learning Unit Generator Fields", "This is the learning unit generator field panel, which is used to add additional fields to the lesson and question generators.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo toolBarURL("Add Form Field", "manage_field.php", "toolBarItem new");
	echo toolBarURL("Setup Payment", "payment.php", "toolBarItem billing");
	echo toolBarURL("Back to Overview", "../index.php", "toolBarItem back");
	echo "</div>\n";
	
//Display all avaliable fields
	if (exist("fields")) {
		$fieldsGrabber = query("SELECT * FROM `fields` ORDER BY `position` ASC", "raw");
		
		echo "<br />\n<table class=\"dataTable\">\n<tr>\n";
		echo column("Order", "75");
		echo column("Name", "200");
		echo column("Description");
		echo column("Edit", "50");
		echo column("Delete", "50");
		echo "</tr>\n";
		
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
	} else {
		echo "<div class=\"noResults\">There are no additional form elements. " . URL("Create one now", "manage_field.php") . ".</div>\n";
	}
	
//Include the footer
	footer();
?>