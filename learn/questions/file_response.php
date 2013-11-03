<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: September 10th, 2010
Last updated: December 4th, 2010

This is the file response management page for the test 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("File Response", "tinyMCEMedia,validate,autoSuggest");
	$questionData = dataGrabber("File Response");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points'])) {
		$question = escape($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$tags = escape($_POST['tags']);
		$totalFiles = $_POST['totalFiles'];
		$feedBackCorrect = escape($_POST['feedBackCorrect']);
		$feedBackIncorrect = escape($_POST['feedBackIncorrect']);
		$feedBackPartial = escape($_POST['feedBackPartial']);
		
		if (is_uploaded_file($_FILES['answer'] ['tmp_name'])) {
			if ($type == "Learning Unit") {
				$uploadDir = $monitor['directory'] . "test/answers";
			} else {
				$uploadDir = "../questionbank_" . $userData['organization'] . "/test/answers";
			}
			
			$targetFile = fileProcess("answer", $uploadDir , false, false, $monitor['testTable'], "fileURL", false, "error=upload");
			$fileURL = escape($targetFile);
			$sql = "`fileURL` = '{$fileURL}', ";
		} else {
			$targetFile = "";
			$sql = "";
		}
				
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `link` = '{$link}', `tags` = '{$tags}', `totalFiles` = '{$totalFiles}', {$sql}`correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `tags` = '{$tags}', `totalFiles` = '{$totalFiles}', {$sql}`correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");	
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'File Response', '{$points}', '{$extraCredit}', '0', '{$category}', '{$link}', '0', '{$totalFiles}', '', '1', '{$tags}', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'File Response', '{$points}', '{$extraCredit}', '0', '{$category}', '0', '{$totalFiles}', '', '1', '{$tags}', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Delete the current answer
	if (isset($_GET['delete']) && $_GET['delete'] == "true") {
		if (isset($_GET['id'])) {
			$table = $monitor['testTable'];
			$id = $_GET['id'];
			$directory = $monitor['directory'] . "test/answers";
			$redirect = "?id=" . $id;
		} elseif (isset($_GET['bankID'])) {
			$table = "questionbank";
			$id = $_GET['bankID'];
			$directory = "../questionbank_" . $userData['organization'] . "/test/answers";
			$redirect = "?bankID=" . $id;
		}
		
		$file = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'");
		query("UPDATE `{$table}` SET `fileURL` = '' WHERE `id` = '{$id}'");
		deleteAll($directory . "/" . $file['fileURL']);
		redirect($_SERVER['PHP_SELF'] . $redirect);
	}
	
//Title
	title($monitor['title'] . "File Response", "A file response is a question that must be responded to in the form of an uploaded file, such as a video or a PDF. Files responses must be scored manually.");
	
//File response form
	echo form("fileResponse", "post", true);
	catDivider("Question", "one", true);
	question();
	
	catDivider("Question Settings", "two");
	echo "<blockquote>\n";
	points();
	type();
	category();
	descriptionLink();
	directions("Number of files user is premitted to upload");
	indent(dropDown("totalFiles", "totalFiles", "1,2,3,4,5,6,7,8,9,10", "1,2,3,4,5,6,7,8,9,10", false, false, false, "1", "questionData", "totalFiles"));
	tags();
	echo "</blockquote>\n";
	
	catDivider("Answer", "three");
	echo "<blockquote>\n";
	directions("Provide an example of a correct answer");
	echo "<blockquote>\n";
	
	if (isset($_GET['id'])) {
		$directory = $monitor['gatewayPath'] . "test/answers";
	} elseif (isset($_GET['bankID'])) {
		$directory = $pluginRoot . "gateway.php/modules/questionbank_" . $userData['organization'] . "/test/answers";
	} else {
		$directory = false;
	}
	
	if ($directory != false && isset($questionData) && !empty($questionData['fileURL'])) {
		echo "<table name=\"answerTable\" id=\"answerTable\">\n<tr>\n<td>";
		echo fileUpload("answer", "answer", false, false, false, false, "questionData", "fileURL", $directory, true);
		echo "</td>\n<td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true", "action smallDelete", false, false, false, false, false, false, " onclick=\"return confirm('This action will delete this file. Continue?')\"") . "</td>\n</tr>\n</table>\n";
	} else {
		echo fileUpload("answer", "answer", false, false, false, false, "questionData", "fileURL");
	}
	
	echo "</blockquote>\n</blockquote>\n";
	
	catDivider("Feedback", "four");	
	feedback();
	
	catDivider("Finish", "five");
	formButtons();
	echo closeForm(true);
	
//Include the footer
	footer();
?>