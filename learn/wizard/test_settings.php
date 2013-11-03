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
Last updated: February 24th, 2011

This is the test settings page for the test and learning 
generators.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Test Settings", "tinyMCEmedia,validate,showHide,enableDisable,navigationMenu");

//Grab the form data
	$testData = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentUnit']}'");
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['testName']) && !empty($_POST['directions']) && is_numeric($_POST['score']) && !empty($_POST['attempts']) && is_numeric($_POST['delay']) && !empty($_POST['gradingMethod']) && is_numeric($_POST['penalties']) && is_numeric($_POST['reference']) && !empty($_POST['randomizeAll'])) {
		$testName = escape($_POST['testName']);
		$directions = escape($_POST['directions']);
		$score = $_POST['score'];
		$attempts = $_POST['attempts'];
		$forceCompletion = $_POST['forceCompletion'];
		$completionMethod = $_POST['completionMethod'];
		$reference = $_POST['reference'];
		$delay = $_POST['delay'];
		$gradingMethod = $_POST['gradingMethod'];
		$penalties = $_POST['penalties'];
		$randomizeAll = $_POST['randomizeAll'];
		$questionBank = $_POST['questionBank'];
		$display = arrayStore($_POST['display']);
		
		if (isset($_POST['timer']) && isset($_POST['timeHours']) && isset($_POST['timeMinutes'])) {
			if ($_POST['timer'] == "on" && $_POST['timeHours'] == "0" && $_POST['timeMinutes'] == "00") {
				$time = arrayStore(array("0", "00"));
				$timer = "0";
			} else {	
				$timeHours = $_POST['timeHours'];
				$timeMinutes = $_POST['timeMinutes'];
				$time = arrayStore(array($timeHours, $timeMinutes));
				$timer = "on";
			}
		} else {
			$timeValue = arrayStore(array("0", "00"));
			$timer = "0";
		}		
					
		query("UPDATE `{$monitor['parentTable']}` SET `testName` = '{$testName}', `directions` = '{$directions}', `score` = '{$score}', `attempts` = '{$attempts}', `forceCompletion` = '{$forceCompletion}', `completionMethod` = '{$completionMethod}', `reference` = '{$reference}', `delay` = '{$delay}', `gradingMethod` = '{$gradingMethod}', `penalties` = '{$penalties}', `time` = '{$time}', `timer` = '{$timer}', `randomizeAll` = '{$randomizeAll}', `questionBank` = '{$questionBank}', `display` = '{$display}' WHERE `id` = '{$monitor['currentUnit']}'");
			
		if ($_POST['submit'] == "Finish") {
			redirect("../index.php?updated=unit");
		} else {
			redirect("question_merge.php");
		}
	}
	
//Title
	navigation("Test Settings", "Setup the test's initial settings, such as the name, directions, and score.");
	
//Test settings form
	echo form("testSettings");
	catDivider("Test Information", "four", true);
	echo "<blockquote>\n";
	directions("Test name", true, "The name of this test");
	
	if (empty($testData['testName'])) {
		indent(textField("testName", "testName", false, false, false, true, false, false, "testData", "name"));
	} else {
		indent(textField("testName", "testName", false, false, false, true, false, false, "testData", "testName"));
	}
	
	directions("Directions", true, "The directions of this test");
	indent(textArea("directions", "directions", "small", true, false, false, "testData", "directions"));
	echo "</blockquote>\n";
	
	catDivider("Test Settings", "five");
	echo "<blockquote>\n";
	directions("Passing score", false, "The minimum percentage a user must obtain to pass");	
	
	$valuesGenerate = "";
	
	for ($count = 1; $count <= 100; $count++) {
		$valuesGenerate .= $count . ",";
	}
	
	$values = rtrim($valuesGenerate, ",");
	
	indent(dropDown("score", "score", $values, $values, false, false, false, false, "testData", "score") . "%");
	directions("Number of attempts", false, "The number of times a user may take this test");
	indent(dropDown("attempts", "attempts", "Unlimited,1,2,3,4,5,6,7,8,9,10", "999,1,2,3,4,5,6,7,8,9,10", false, false, false, false, "testData", "attempts", " onchange=\"toggleTestOptions(this.value);\""));
	echo "<div id=\"contentHide\"";
	
	if ($testData['attempts'] == "1") {
		echo " class=\"contentHide\">\n";
	} else {
		echo " class=\"contentShow\">\n";
	}
	
	directions("Delay between attempts", false, "Set the amount of time a user must wait between attempts before retaking the test");
	indent(dropDown("delay", "delay", "None,30 minutes,60 minutes,2 hours,3 hours,4 hours,5 hours,6 hours,7 hours,8 hours,9 hours,10 hours,11 hours,12 hours,13 hours,14 hours,15 hours,16 hours,17 hours,18 hours,19 hours,20 hours,21 hours,22 hours,23 hours,24 hours,2 days,3 days,4 days,5 days,6 days,7 days", "0,1800,3600,7200,10800,14400,18000,21600,25200,28800,32400,36000,39600,43200,46800,50400,54000,57600,61200,64800,68400,72000,75600,79200,82800,86400,172800,259200,345600,432000,518400,604800", false, false, false, false, "testData", "delay"));
	directions("Grading method", false, "Set how the test will be scored");
	indent(radioButton("gradingMethod", "gradingMethod", "Highest Grade,Average Grade,First Attempt,Last Attempt", "Highest Grade,Average Grade,First Attempt,Last Attempt", false, false, false, false, "testData", "gradingMethod"));
	directions("Show penalties", false, "Set whether or not all attempts will show in the <br />gradebook, regardless of past scores");
	indent(radioButton("penalties", "penalties", "Yes,No", "1,0", true, false, false, false, "testData", "penalties"));
	echo "</div>\n";
	directions("Timer", false, "Sets a timer, which will only allow the test to be open for a set duration");
	echo "<blockquote><p>\nHours: ";
	
	$time = arrayRevert($testData['time']);
	$testH = $time['0'];
	$testM = $time['1'];
	
	if (empty($testData['timer'])) {
		echo dropDown("timeHours", "timeHours", "0,1,2,3,4,5", "0,1,2,3,4,5", false, false, false, false, "time", "0", " disabled=\"disabled\"");
		echo " Minutes: ";
		echo dropDown("timeMinutes", "timeMinutes", "00,05,10,15,20,25,30,35,40,45,50,55", "00,05,10,15,20,25,30,35,40,45,50,55", false, false, false, false, "time", "1", " disabled=\"disabled\"");
	} else {
		echo dropDown("timeHours", "timeHours", "0,1,2,3,4,5", "0,1,2,3,4,5", false, false, false, false, "time", "0");
		echo " Minutes: ";
		echo dropDown("timeMinutes", "timeMinutes", "00,05,10,15,20,25,30,35,40,45,50,55", "00,05,10,15,20,25,30,35,40,45,50,55", false, false, false, false, "time", "1");
	}
	
	echo " ";
	echo checkbox("timer", "timer", "Enable", false, false, false, false, "testData", "timer", "on", " onclick=\"flvFTFO1('testSettings','timeHours,t','timeMinutes,t')\"");
	echo "</p></blockquote>\n";
	directions("Force completion", false, "Set whether or not this test must be completed the first time <br />it is opened, and what penalties will be applied if this cirteria isn\'t met");
	echo "<blockquote><p>\n";
	echo "Penalties: ";
	
	if (empty($testData['forceCompletion'])) {
		echo dropDown("completionMethod", "completionMethod", "The test will close,All answers will reset,Grade decreases 10%,Grade decreases 20%,Grade decreases 30%,Grade decreases 40%", "0,1,10,20,30,40", false, false, false, false, "testData", "completionMethod", " disabled=\"disabled\"");
	} else {
		echo dropDown("completionMethod", "completionMethod", "The test will close,All answers will reset,Grade decreases 10%,Grade decreases 20%,Grade decreases 30%,Grade decreases 40%", "0,1,10,20,30,40", false, false, false, false, "testData", "completionMethod");
	}
	
	echo " ";
	echo checkbox("forceCompletion", "forceCompletion", "Enable", false, false, false, false, "testData", "forceCompletion", "on", " onclick=\"flvFTFO1('testSettings','completionMethod,t')\"");
	echo "</p></blockquote>\n";
	directions("Allow lesson reference", false, "Allow users to reference the lesson during the test");
	indent(radioButton("reference", "reference", "Yes,No", "1,0", true, false, false, false, "testData", "reference"));
	directions("Randomize questions", false, "Allow users to reference the lesson during the test");
	indent(radioButton("randomizeAll", "randomizeAll", "Sequential Order,Randomize", "Sequential Order,Randomize", false, false, false, false, "testData", "randomizeAll"));
	
	if (access("Edit Unowned Learning Units")) {
		directions("Automatically pull questions from bank", false, "Set whether or not questions will be <br />automatically pulled from the question bank <br />when a new question in the same category <br />is added");
		echo "<blockquote><p>\n";
		echo radioButton("questionBank", "questionBank", "Yes,No", "1,0", true, false, false, false, "testData", "questionBank");
		echo "<br /><br />\n";
		
		if (exist("questionbank_{$userData['organization']}", "category", $testData['category'])) {
			echo "The question bank has test questions for this category.";
		} else {
			echo "The question bank does not have any test questions for this category.";
		}
		
		echo "</p></blockquote>\n";
	}
	
	directions("After the test is taken display", false, "Select what information will be displayed when the test is completed:<br/><br/><strong>Score:</strong> Display a breakdown of points that the user recieved on each quesiton<br/><strong>Selected Answers:</strong> The answer(s) the user selected in the test<br/><strong>Correct Answers:</strong> The correct answer(s) for each problem<br/><strong>Feedback:</strong> The comments the user will recieve based off their answer</li>");
	
	$checks = arrayRevert($testData['display']);
	$firstValue = false;
	$secondValue = false;
	$thirdValue = false;
	$fourthValue = false;
	
	if (is_array($checks)) {
		foreach($checks as $checkbox) {
			switch ($checkbox) {
				case "1" : $firstValue = true; break;
				case "2" : $secondValue = true; break;
				case "3" : $thirdValue = true; break;
				case "4" : $fourthValue = true; break;
			}
		}
	}
	
	indent(checkbox("display[]", "display[]", "Score", "1", false, false, $firstValue) . 
	"<br />\n" . 
	checkbox("display[]", "display[]", "Selected Answers", "2", false, false, $secondValue)  . 
	"<br />\n" . 
	checkbox("display[]", "display[]", "Correct Answers", "3", false, false, $thirdValue) . 
	"<br />\n" . 
	checkbox("display[]", "display[]", "Feedback", "4", false, false, $fourthValue));
	echo "</blockquote>\n";
	
	catDivider("Submit", "six");
	echo "<blockquote><p>\n";
	
	echo button("back", "back", "&lt;&lt; Previous Step", "button", "lesson_verify.php");
	echo button("submit", "submit", "Next Step &gt;&gt;", "submit");
	
	if (isset ($_SESSION['review'])) {
		echo button("submit", "submit", "Finish", "submit");
	}
	
	echo "</p></blockquote>\n";
	echo closeForm();
	
//Include the footer
	footer();
?>