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

This is the essay management page for the test generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Essay", "tinyMCEMedia,validate,autoSuggest");
	$questionData = dataGrabber("Essay");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points'])) {
		$question = escape($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$tags = escape($_POST['tags']);
		$answer = escape($_POST['answer']);
		$feedBackCorrect = escape($_POST['feedBackCorrect']);
		$feedBackIncorrect = escape($_POST['feedBackIncorrect']);
		$feedBackPartial = escape($_POST['feedBackPartial']);
	
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `link` = '{$link}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Essay', '{$points}', '{$extraCredit}', '0', '{$category}', '{$link}', '0', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Essay', '{$points}', '{$extraCredit}', '0', '{$category}', '0', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Title
	title($monitor['title'] . "Essay", "An essay question is a question that requires a long, written response. Essays must be scored manually.");
	
//Essay form
	echo form("essay");
	catDivider("Question", "one", true);
	question();
	
	catDivider("Question Settings", "two");
	echo "<blockquote>\n";
	points();
	type();
	category();
	descriptionLink();
	tags();
	echo "</blockquote>\n";
	
	catDivider("Answer", "three");
	echo "<blockquote>\n";
	directions("Provide an example of a correct answer");
	indent(textArea("answer", "answer", "small", false, false, false, "questionData", "answer"));
	echo "</blockquote>\n";
	
	catDivider("Feedback", "four");
	feedback();
	
	catDivider("Finish", "five");
	formButtons();
	closeForm(true);
	
//Include the footer
	footer();
?>