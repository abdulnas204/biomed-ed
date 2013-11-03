<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: September 4th, 2010
Last updated: February 24th, 2011

This is the page for previewing individual questions from 
the question bank.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Process the given data
	if (isset($_GET['linkID'])) {
		if (exist("questionbank_" . $userData['organization'], "id", $_GET['linkID']) && exist("learningunits")) {
			$location = "<fieldset>\n";
			$location .= "<legend>This question appears in the following test(s):</legend>\n";
			$location .= "<ul>\n";
			$testDataGrabber = query("SELECT * FROM `learningunits`", "raw");
			
			while ($testData = fetch($testDataGrabber)) {
				if ($testInfo = exist("test_" . $testData['id'], "linkID", $_GET['linkID'])) {
					$location .= "<li>" . $testData['name'] . ", Question Number " . $testInfo['position'] . "</li>\n";
					$exists = true;
				}
			}
				
			$location .= "</ul>\n";
			$location .= "</fieldset>";
			$questionData = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$_GET['linkID']}'");
			$title = "Results for the " . $questionData['type'] . " Question";
		} else {
			$title = "No Results Found";
		}
	} else {
		redirect("index.php");
	}
	
//Top content
	headers($title);

//Title
	title($title, "This discovery page will show in which tests questions from the question bank are used.");
	
//Page content
	if (isset($location) && isset($exists)) {	
		echo $location;
	} else {
		echo errorMessage("This question does not appear in any tests.");
	}
	
	indent(button("finish", "finish", "Finish", "history"));
	
//Include the footer
	footer();
?>