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
Last updated: February 26th, 2010

This script is dedicated to displaying the lesson section 
of each learning unit.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Grab all learning unit data
	if (isset($_GET['id'])) {
		$learningUnits = arrayRevert($userData['learningunits']);
		$unitInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$_GET['id']}' LIMIT 1");
		
		if (!exist("learningunits", "id", $_GET['id']) || !exist("lesson_" . $_GET['id']) || !access("View Learning Units") || empty($unitInfo['visible'])) {
			redirect("index.php");
		}
		
		if (isset($_GET['page']) && (access("Edit Unowned Learning Units") || is_array($learningUnits) || array_key_exists($_GET['id'], $learningUnits))) {
			if (!access("Edit Unowned Learning Units")) {
				if (!is_array($learningUnits)) {
					redirect("index.php");
				}
					
				if ($learningUnits[$_GET['id']]['lessonStatus'] == "C") {
					redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
				}
									
				if ($learningUnits[$_GET['id']]['lessonStatus'] == "F" && $learningUnits[$_GET['id']]['testStatus'] != "F" && $learningUnits[$_GET['id']]['testStatus'] != "A" && $unitInfo['reference'] == "0") {
					redirect("test.php?id=" . $_GET['id']);
				}
			}
		}
	} else {
		redirect("index.php");
	}
	
//Open the lesson
	if (loggedIn() && isset($_POST['submit'])) {
		$learningUnits[$_GET['id']]['lessonStatus'] = "O";
		$updatedUnits = escape(arrayStore($learningUnits));
		
		query("UPDATE `users` SET `learningUnits` = '{$updatedUnits}' WHERE `id` = '{$userData['id']}'");
		redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&page=1");
	}
	
//Open the test
	if (loggedIn() && isset($_GET['action']) && $_GET['action'] == "finish") {
		if ($learningUnits[$_GET['id']]['lessonStatus'] == "O" || $learningUnits[$_GET['id']]['lessonStatus'] == "F") {
			if ($learningUnits[$_GET['id']]['testStatus'] == "C") {
				$learningUnits[$_GET['id']]['lessonStatus'] = "F";
				
				if (exist("test_" . $_GET['id'])) {
					$learningUnits[$_GET['id']]['testStatus'] = "O";
				} else {
					$learningUnits[$_GET['id']]['testStatus'] = "F";
					$learningUnits[$_GET['id']]['submitted'] = time();
				}
				
				$updatedUnits = escape(arrayStore($learningUnits));
				
				query("UPDATE `users` SET `learningunits` = '{$updatedUnits}' WHERE `id` = '{$userData['id']}'");
			}
			
			if (exist("test_" . $_GET['id'])) {
				redirect("test.php?id=" . $_GET['id']);
			} else {
				redirect("lesson.php?id=" . $_GET['id']);
			}
		}
	}
	
//Reopen the test
	$lastAttempt = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$_GET['id']}' ORDER BY `attempt` DESC LIMIT 1", false, false);
	
	if (isset($_POST['retake']) && $lastAttempt && ($unitInfo['attempts'] == "999" || $unitInfo['attempts'] > $lastAttempt['attempt'])) {
		if ($learningUnits[$_GET['id']]['lessonStatus'] == "F" && $learningUnits[$_GET['id']]['testStatus'] == "F" && exist("test_" . $_GET['id'])) {
			$learningUnits[$_GET['id']]['testStatus'] = "O";
			$updatedUnits = escape(arrayStore($learningUnits));
			
			query("UPDATE `users` SET `learningunits` = '{$updatedUnits}' WHERE `id` = '{$userData['id']}'");
			redirect("test.php?id=" . $_GET['id']);
		}
	}

//Top content
	headers($unitInfo['name'], "navigationMenu,plugins");

//Information bar
	if (!isset($_GET['page'])) {
		title($unitInfo['name'], false, false);
		
		echo "<div class=\"toolBar noPadding\">\n";
		
		if (is_array($learningUnits) && array_key_exists($_GET['id'], $learningUnits)) {
			echo "<strong>Due Date:</strong> " . date("l, F jS, Y", strtotime(date("Y-m-d", $learningUnits[$_GET['id']]['startDate']) . " +" . strip($unitInfo['timeFrame'], "numbersOnly") . " " . strip($unitInfo['timeFrame'], "lettersOnly"))) . "<br />\n";
		} else {
			echo "<strong>Due Date:</strong> " . strip($unitInfo['timeFrame'], "numbersOnly") . " " . strip($unitInfo['timeFrame'], "lettersOnly") . " from assigned date<br />\n";
		}
		
		echo "<strong>Category:</strong> " . $unitInfo['category'] . "<br />\n";
		
		$additionalFieldsGrabber = query("SELECT * FROM `fields` ORDER BY `position` ASC", "raw");
		
		while($additionalFields = fetch($additionalFieldsGrabber)) {
			if (is_array(arrayRevert($additionalFields['section'])) && in_array("Lesson Settings", arrayRevert($additionalFields['section']))) {
				echo "<strong>" . $additionalFields['name'] . ":</strong> ";
				
				switch($additionalFields['fieldType']) {
					case "dropDown" : 
					case "radio" : 
					case "textField" : 
						echo $unitInfo['field_' . $additionalFields['id']] . "<br />";
						break;
						
					case "textArea" : 
						echo strip_tags($unitInfo['field_' . $additionalFields['id']]) . "<br />";
						break;
						
					case "checkbox" : 
						$return = "";
						
						foreach(arrayRevert($unitInfo['field_' . $additionalFields['id']]) as $value) {
							$return .= $value . ", ";
						}
						
						echo trim($return, ", ");
						break;
				}
			}
		}
		
		echo "</div>";
	}
	
//Display the lesson
	if (!isset($_GET['page'])) {
		echo $unitInfo['comments'];
		
		if (is_array($learningUnits) && array_key_exists($_GET['id'], $learningUnits)) {
			echo "\n<div class=\"noResults\">\n";
			
			switch ($learningUnits[$_GET['id']]['lessonStatus']) {
				case "C" : 
					echo form("startLesson");
					echo button("submit", "submit", "Begin Lesson", "submit");
					echo closeForm(false);
					break;
				
				case "O" : 
					echo button("continue", "continue", "Continue Lesson", "button", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&page=1");
					break;
				
				case "F" : 
					if (exist("test_" . $_GET['id'])) {
						switch ($learningUnits[$_GET['id']]['testStatus']) {
							case "O" : 
								echo button("continue", "continue", "Continue Test", "button", "test.php?id=" . $_GET['id']);
								break;
							
							case "A" : 
								if ($userData['organization'] == "0") {
									echo "<div class=\"complete\">You have completed this learning unit! However, it is awaiting a final grade. " . URL("You may grade it now", "review.php?id=" . $_GET['id']) . ".</div>\n";
									echo "<br /><br />";
									echo button("review", "review", "Grade Test", "button", "review.php?id=" . $_GET['id']);
								} else {
									echo "<div class=\"complete\">You have completed this learning unit! However, it is awaiting a final grade from your instructor.</div>\n";
									echo "<br /><br />";
									echo button("review", "review", "Review Test", "button", "review.php?id=" . $_GET['id']);
								}
								
								
								break;
								
							case "F" : 
								echo "<div class=\"complete\">You have completed this learning unit!</div>\n";
								echo "<br /><br />";
								echo button("lesson", "lesson", "Review Lesson", "button", $_SERVER['REQUEST_URI'] . "&page=1");			
								echo button("review", "review", "Review Test", "button", "review.php?id=" . $_GET['id']);
								
								if ($lastAttempt && ($unitInfo['attempts'] == "999" || $unitInfo['attempts'] > $lastAttempt['attempt'])) {
									echo "<br /><br />\n<div align=\"center\">- OR -</div>\n<br />";
									echo form("retakeTest");
									echo button("retake", "retake", "Retake Test", "submit", false, "return confirm('This action will reopen the test and mark this learning unit as incomplete until the test has been completed. Continue?')");
									echo closeForm(false);
								} else {
									echo "<br /><br />\n<div align=\"center\">You may not retake this test.</div>\n";
								}
								
								break;
						}
					} else {
						echo "<div class=\"complete\">You have completed this learning unit!</div>\n";
						echo "<br /><br />";
						echo button("continue", "continue", "Review Lesson", "button", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . "&page=1");
						echo button("exit", "exit", "Finish", "button", "index.php");
					}
					
					break;
			}
			
			echo "</div>\n";
		} elseif (access("Purchase Learning Unit")) {
			if (!empty($unitInfo['enablePrice'])) {
				echo "\n<div class=\"noResults\">\n";
				echo form("cart", false, false, "enroll/cart.php");
				echo hidden("purchase[]", "purchase[]", $_GET['id']);
				echo button("submit", "submit", "Add to Cart", "submit");
				echo closeForm(false);
				echo "</div>\n";
			} else {
				echo "\n<div class=\"noResults\">\n";
				echo form("cart", false, false, "enroll/enroll.php");
				echo hidden("enroll", "enroll", $_GET['id']);
				echo hidden("redirect", "redirect", "true");
				echo button("submit", "submit", "Enroll in Unit", "submit");
				echo closeForm(false);
				echo "</div>\n";
			}
		} elseif (access("Edit Unowned Learning Units")) {
			echo "\n<div class=\"noResults\">\n";
			echo button("begin", "begin", "Begin Lesson", "button", "lesson.php?id=" . $_GET['id'] . "&page=1");
			echo "</div>\n";
		}
	} else {
		lesson($_GET['id'], "lesson_" . $_GET['id'], false);
	}
	
//Include the footer
	footer();
?>