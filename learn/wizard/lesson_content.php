<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: August 13th, 2010
Last updated: December 3rd, 2010

This is the lesson content page for the learning unit 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Lesson Content", "navigationMenu");
	
//Reorder pages
	reorder($monitor['lessonTable'], "lesson_content.php");
	
//Delete a page
	if (isset ($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
		if (exist($monitor['lessonTable'], "id", $_GET['id'])) {
			$delete = query("SELECT * FROM `{$monitor['lessonTable']}` WHERE `id` = '{$_GET['id']}'");
			
			if (empty($delete['attachment'])) {
				delete($monitor['lessonTable'], "lesson_content.php", false, true, $monitor['directory'] . "lesson/" . $delete['attachment']);
			} else {
				delete($monitor['lessonTable'], "lesson_content.php", false, true);
			}
		}
	}
	
//Title
	navigation("Lesson Content", "All of the content for this lesson will be managed from this page.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo toolBarURL("Add New Page", "manage_content.php", "toolBarItem new");
	echo "</div>\n<br />\n";
	
//Display message updates
	message("inserted", "page", "success", "The page was successfully inserted");
	message("updated", "page", "success", "The page was successfully updated");

//Pages table
	if (exist($monitor['lessonTable'])) {
		$pageGrabber = query("SELECT * FROM `{$monitor['lessonTable']}` ORDER BY `position` ASC", "raw");
		
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("Order", "75");
		echo column("Title", "250");
		echo column("Content");
		echo column("Edit", "50");
		echo column("Delete", "50");
		echo "</tr>\n";
		
		while($lessonData = fetch($pageGrabber)) {
			echo "<tr";
			if ($lessonData['position'] & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			echo reorderMenu($monitor['lessonTable'], $lessonData['id']);
			echo preview(commentTrim(30, $lessonData['title']), "preview_page.php?page=" . $lessonData['position'], "page", "250", true);
			echo cell(commentTrim(100, $lessonData['content']));
			echo editURL("manage_content.php?id=" . $lessonData['id'], $lessonData['title'], "page");	
			echo deleteURL("lesson_content.php?action=delete&id=" . $lessonData['id'], $lessonData['title'], "page");		
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">There are no pages in this lesson. " . URL("Create a new page now", "manage_content.php") . ".</div>";
	}
	
//Display navigation buttons
	echo "<blockquote>\n";
	echo button("back", "back", "&lt;&lt; Previous Step", "button", "lesson_settings.php");
	
	if (exist($monitor['lessonTable'])) {
		echo button("next", "next", "Next Step &gt;&gt;", "button", "lesson_verify.php");
		
		if (isset($_SESSION['review'])) {
			echo button("submit", "submit", "Finish", "button", "../index.php?updated=unit");
		}
	}
	
	echo "</blockquote>\n";
	
//Include the footer
	footer();
?>