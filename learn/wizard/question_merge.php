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
Last updated: December 4th, 2010

This is purly a backend script used to merge questions 
from the question bank into the test.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Question Merge", false, true);

//Check to see if questions from the question bank need to be mass merged
	if (!isset($_GET['type'])) {
		$importCheck = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentUnit']}'");
		
		if ($importCheck['questionBank'] == "1") {
		//Select all of the questions from the bank, with the same category as the test
			$importQuestions = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$importCheck['category']}' ORDER BY `id` ASC", "raw");	
			
		//Import the questions into the test
			$lastQuestion = lastItem($monitor['testTable']);
			
			while ($importQuestions = fetch($importQuestionsGrabber)) {			
				$position = $lastQuestion++;
				$id = $importQuestions['id'];
									
				query("INSERT INTO `{$monitor['testTable']}` (
					  `id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (
					  NULL, '1', '{$id}', '{$position}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
					  )");
			}
		}
		
		redirect("test_content.php");
	}
	
//Import an individual question from the bank completely into the test
	if (isset($_GET['type']) && $_GET['type'] == "import" && isset($_GET['questionID']) && isset($_GET['bankID'])) {
		$bankID = $_GET['bankID'];
		$questionID = $_GET['questionID'];
		$importQuestions = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$bankID}'");
		
		$type = $importQuestions['type'];
		$points = $importQuestions['points'];
		$extraCredit = $importQuestions['extraCredit'];
		$partialCredit = $importQuestions['partialCredit'];
		$link = $importQuestions['link'];
		$randomize = $importQuestions['randomize'];
		$totalFiles = $importQuestions['totalFiles'];
		$choiceType = $importQuestions['choiceType'];
		$case = $importQuestions['case'];
		$tags = $importQuestions['tags'];
		$question = escape(stripslashes($importQuestions['question']));
		$questionValue = escape(stripslashes($importQuestions['questionValue']));
		$answer = escape(stripslashes($importQuestions['answer']));
		$answerValue = escape(stripslashes($importQuestions['answerValue']));
		$fileURL = $importQuestions['fileURL'];
		$correctFeedback = escape(stripslashes($importQuestions['correctFeedback']));
		$incorrectFeedback = escape(stripslashes($importQuestions['incorrectFeedback']));
		$partialFeedback = escape(stripslashes($importQuestions['partialFeedback']));
							
		query("UPDATE `{$monitor['testTable']}` SET `questionBank` = '0', `linkID` = '0', `type` = '{$type}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `partialCredit` = '{$partialCredit}', `link` = '{$link}', `randomize` = '{$randomize}', `totalFiles` = '{$totalFiles}', `choiceType` = '{$choiceType}', `case` = '{$case}', `tags` = '{$tags}', `question` = '{$question}', `questionValue` = '{$questionValue}', `answer` = '{$answer}', `answerValue` = '{$answerValue}', `fileURL` = '{$fileURL}', `correctFeedback` = '{$correctFeedback}', `incorrectFeedback` = '{$incorrectFeedback}', `partialFeedback` = '{$partialFeedback}' WHERE `id` = '{$questionID}'");
		
		if ($type == "File Response") {
			copy("../questionbank_" . $userData['organization'] . "/test/answers/" . $fileURL, $monitor['directory'] . "test/answers/" . $fileURL);
		}
		
		$redirect = "../questions/";
		
		switch ($type) {
			case "Description" : $redirect .= "description"; break;
			case "Essay" : $redirect .= "essay"; break;
			case "File Response" : $redirect .= "file_response"; break;
			case "Fill in the Blank" : $redirect .= "blank"; break;
			case "Matching" : $redirect .= "matching"; break;
			case "Multiple Choice" : $redirect .= "multiple_choice"; break;
			case "Short Answer" : $redirect .= "short_answer"; break;
			case "True False" : $redirect .= "true_false"; break;
		}
		
		$redirect .= ".php";
		
		$questionInfo = query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$questionID}'");
		
		redirect($redirect . "?id=" . $questionInfo['id']);
	}
?>