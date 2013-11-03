<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: December 4th, 2010
Last updated: December 21st, 2010

This script is dedicated to displaying the lesson section 
of each learning unit.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Grab all learning unit data
	if (isset($_GET['id'])) {
		$learningUnits = unserialize($userData['learningunits']);
		$unitInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$_GET['id']}' LIMIT 1");
		
		if (!exist("learningunits", "id", $_GET['id']) || !exist("lesson_" . $_GET['id']) || !access("View Learning Units")) {
			redirect("index.php");
		}
		
		if (isset($_GET['page']) && (access("Edit Unowned Learning Units") || array_key_exists($_GET['id'], $learningUnits))) {	
			if ($learningUnits[$_GET['id']]['lessonStatus'] == "C") {
				redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
			}
								
			if ($learningUnits[$_GET['id']]['lessonStatus'] == "F" && $learningUnits[$_GET['id']]['testStatus'] != "F" && $learningUnits[$_GET['id']]['testStatus'] != "A" && $unitInfo['reference'] == "0") {
				redirect("test.php?id=" . $_GET['id']);
			}
		}
	} else {
		redirect("index.php");
	}
	
//Open the lesson
	if (loggedIn() && isset($_POST['submit'])) {
		$learningUnits[$_GET['id']]['lessonStatus'] = "O";
		$updatedUnits = escape(serialize($learningUnits));
		
		query("UPDATE `users` SET `learningUnits` = '{$updatedUnits}' WHERE `id` = '{$userData['id']}'");
		redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&page=1");
	}
	
//Open the test
	if (loggedIn() && isset($_GET['action']) && $_GET['action'] == "finish") {
		if ($learningUnits[$_GET['id']]['lessonStatus'] == "O" && $learningUnits[$_GET['id']]['testStatus'] == "C") {
			$learningUnits[$_GET['id']]['lessonStatus'] = "F";
			$learningUnits[$_GET['id']]['testStatus'] = "O";
			$updatedUnits = serialize($learningUnits);
			
			query("UPDATE `users` SET `learningunits` = '{$updatedUnits}' WHERE `id` = '{$userData['id']}'");
			redirect("test.php?id=" . $_GET['id']);
		}
	}

//Top content
	headers($unitInfo['name'], "navigationMenu,plugins");

//Information bar
	if (!isset($_GET['page'])) {
		title($unitInfo['name'], false, false);
		
		echo "<div class=\"toolBar noPadding\">\n<strong>Due Date:</strong> " . date("l, F jS, Y", strtotime(date("Y-m-d", $learningUnits[$_GET['id']]['startDate']) . " +" . strip($unitInfo['timeFrame'], "numbersOnly") . " " . strip($unitInfo['timeFrame'], "lettersOnly"))) . "<br /></div>";
	}
	
//Display the lesson
	if (!isset($_GET['page'])) {
		echo $unitInfo['comments'];
		
		if (access("Edit Unowned Learning Units") || array_key_exists($_GET['id'], $learningUnits)) {
			echo form("startLesson");
			echo "<div class=\"noResults\">\n";
			
			switch ($learningUnits[$_GET['id']]['lessonStatus']) {
				case "C" : 
					echo button("submit", "submit", "Begin Lesson", "submit");
					break;
				
				case "O" : 
					echo button("continue", "continue", "Continue Lesson", "button", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&page=1");
					break;
				
				case "F" : 
					switch ($learningUnits[$_GET['id']]['testStatus']) {
						case "O" : 
							echo button("continue", "continue", "Continue Test", "button", "test.php?id=" . $_GET['id']);
							break;
						
						case "A" : 
						case "F" : 
							echo button("continue", "continue", "Review Test", "button", "review.php?id=" . $_GET['id']);
							break;
							
						default : 
							die(errorMessage("Invalid test progress type"));
							break;
					}
					
					break;
					
				default : 
					die(errorMessage("Invalid lesson progress type"));
					break;
			}
			
			echo "</div>\n";
			echo closeForm(false);
		} elseif (access("Purchase Learning Units")) {
			echo "<div class=\"noResults\">\n";
			echo button("start", "start", "Add to Cart", "button", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&page=1");
			echo "</div>\n";
		}
	} else {
		lesson($_GET['id'], "lesson_" . $_GET['id'], false);
	}
	
//Include the footer
	footer();
?>