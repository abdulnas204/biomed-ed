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
Last updated: Janurary 8th, 2011

This is the test checking page, which asks a user if a test 
should be generated along with the lesson.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");	
	$monitor = monitor("Create a Test", "navigationMenu");

//Process the form
	if (isset ($_POST['submit'])) {
	//Select all custom form fields
		$fields = query("SELECT * FROM `fields`", "raw");
		$sql = "";
		
		while($field = fetch($fields)) {
			$sql .= "`field_{$field['id']}` longtext NOT NULL,";
		}
		
		query("CREATE TABLE IF NOT EXISTS `{$monitor['testTable']}` (
			  `id` int(255) NOT NULL AUTO_INCREMENT,
			  `questionBank` int(1) NOT NULL,
			  `linkID` int(255) NOT NULL,
			  `position` int(100) NOT NULL,
			  `type` longtext NOT NULL,
			  `points` int(3) NOT NULL,
			  `extraCredit` text NOT NULL,
			  `partialCredit` int(1) NOT NULL,
			  `category` longtext NOT NULL,
			  `link` longtext NOT NULL,
			  `randomize` int(1) NOT NULL,
			  `totalFiles` int(2) NOT NULL,
			  `choiceType` text NOT NULL,
			  `case` int(1) NOT NULL,
			  `tags` longtext NOT NULL,
			  `question` longtext NOT NULL,
			  `questionValue` longtext NOT NULL,
			  `answer` longtext NOT NULL,
			  `answerValue` longtext NOT NULL,
			  `fileURL` longtext NOT NULL,
			  `correctFeedback` longtext NOT NULL,
			  `incorrectFeedback` longtext NOT NULL,
			  `partialFeedback` longtext NOT NULL,
			  {$sql}
			  PRIMARY KEY (`id`)
			  )");
							
		query("UPDATE `{$monitor['parentTable']}` SET `test` = '1' WHERE `id` = '{$monitor['currentUnit']}'");	
			
		redirect("test_settings.php");
	}
	
//Redirect to the end if no test is going to be added
	if (isset ($_POST['skipTest'])) {
		redirect("complete.php");
	}

//Title
	navigation("Create a Test", "Do you wish to create a test?");
	
//Test check form
	echo "<div class=\"noResults\">\n";
	echo form("testCheck");
	echo button("submit", "submit", "Create a Test", "submit");
	echo button("skipTest", "skipTest", "Do not Create Test", "submit");
	echo closeForm(false);
	echo "</div>\n";

//Include the footer
	footer();
?>