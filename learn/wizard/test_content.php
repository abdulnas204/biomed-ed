<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: September 9th, 2010
Last updated: December 4th, 2010

This is the test content page for the learning unit 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Test Content", "navigationMenu");

//Reorder questions
	reorder($monitor['testTable'], "test_content.php");
	
//Delete a test question
	if (isset ($_GET['id']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		if (exist($monitor['testTable'], "id", $_GET['id'])) {
			$delete = query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$_GET['id']}'");
			
			if ($delete['type'] == "File Response") {
				delete($monitor['testTable'], "test_content.php", true, $monitor['directory'] . "test/answers/" . $delete['fileURL']);
			} else {
				delete($monitor['testTable'], "test_content.php", true);
			}
		}
	}
	
//Title
	navigation("Test Content", "Content may be added to the test by using the guide below.");
	
//Admin toolbar
	echo "<div class=\"toolBar noPadding\">\n";
	echo form("jump");
	echo "<span class=\"toolBarItem noLink\">\nAdd: \n";	
	echo dropDown("menu", "nenu", "- Select Question Type -,Description,Essay,File Response,Fill in the Blank,Matching,Multiple Choice,Short Answer,True or False,Import from Question Bank", ",../questions/description.php,../questions/essay.php,../questions/file_response.php,../questions/blank.php,../questions/matching.php,../questions/multiple_choice.php,../questions/short_answer.php,../questions/true_false.php,../questions/question_bank.php");
	echo button("submit", "submit", "Go", "button", false, " onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\"");
	echo "</span>\n";
	echo closeForm(false); 
	echo "</div>\n";
	
//Display message updates
	message("inserted", "question", "success", "The question was successfully inserted");
	message("updated", "question", "success", "The question was successfully updated");
	
//Questions table
	if (exist($monitor['testTable'])) {
		$testDataGrabber = query("SELECT * FROM `{$monitor['testTable']}` ORDER BY `position` ASC", "raw");
		
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("Order", "100");
		echo column("Type", "150");
		echo column("Point Value", "100");
		echo column("Question");
		echo column("Edit", "50");
		echo column("Delete", "50");
		echo "</tr>\n";
			
		while ($testData = fetch($testDataGrabber)) {
		//Select the external data, if needed
			if ($testData['questionBank'] == "1") {
				$importedQuestion = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$testData['linkID']}' LIMIT 1");				
				$type = $importedQuestion['type'];
				$points = $importedQuestion['points'];
				$extraCredit = $importedQuestion['extraCredit'];
				$question = $importedQuestion['question'];
			} else {
				$type = $testData['type'];
				$points = $testData['points'];
				$extraCredit = $testData['extraCredit'];
				$question = $testData['question'];
			}
			
			echo "<tr";
			if ($testData['position'] & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			
			if ($testData['questionBank'] == "1") {
				$class = " class=\"questionBank\"";
			} else {
				$class = "";
			}
			
			echo reorderMenu($monitor['testTable'] , $testData['id'], false, false, "<div" . $class . ">{content}</div>");
			echo cell(URL($type, "preview_question.php?id=" . $testData['id'], false, true, "Preview this <strong>" . $type . "</strong> question", false, true, "640", "480"), "150");
			echo "<td width=\"100\"><div";
			
			if ($extraCredit == "on") {
				echo " class=\"extraCredit\"";
			}
			
			echo ">\n" . $points;
			
			if ($points == "1") {
				echo " Point";
			} else {
				echo " Points";
			}
			
			echo "</div>\n</td>\n";
			echo cell(commentTrim(100, $question));
			
			if (isset($importedQuestion)) {
				cell(URL(false, "question_merge.php?type=import&questionID=" . $testData['id'] . "&bankID=" . $testData['linkID'], "action edit", false, "Edit this <strong>" . $type . "</strong> question", false, false, false, false, " onclick=\"return confirm('This question is currently located in the question bank. Once you edit this question, it will no long be linked to the question bank. Do you want to import and edit this question inside of the test? Click OK to continue.')\""), "50");
			} else {
				$URL = "../questions/";
				
				switch ($testData['type']) {
					case "Description" : $URL .= "description.php"; break;
					case "Essay" : $URL .= "essay.php"; break;
					case "File Response" : $URL .= "file_response.php"; break;
					case "Fill in the Blank" : $URL .= "blank.php"; break;
					case "Matching" : $URL .= "matching.php"; break;
					case "Multiple Choice" : $URL .= "multiple_choice.php"; break;
					case "Short Answer" : $URL .= "short_answer.php"; break;
					case "True False" : $URL .= "true_false.php"; break;
				}
				
				$URL .= "?id=" . $testData['id'];
				
				echo editURL($URL, $type, "question");
			}
			
			echo deleteURL("test_content.php?id=" .  $testData['id'], $type, "question");
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">There are no test questions. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>\n";
	}
	
//Display navigation buttons
	echo "<blockquote>\n";
	echo button("back", "back", "&lt;&lt;  Previous Step", "button", "test_settings.php");
	
	if (exist($monitor['testTable'])) {
		echo button("next", "next", "Next Step &gt;&gt;", "button", "test_verify.php");
		
		if (isset ($_SESSION['review'])) {
			echo button("submit", "submit", "Finish", "button", "../index.php?updated=unit");
		}
	}
	
	echo "</blockquote>\n";
	
//Include the footer
	footer();
?>