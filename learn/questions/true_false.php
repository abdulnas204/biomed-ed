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
Last updated: December 21st, 2010

This is the true or false management page for the test 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("True False", "tinyMCEMedia,validate,autoSuggest");
	$questionData = dataGrabber("True False");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && is_numeric($_POST['answer'])) {
		$question = escape($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$randomize = $_POST['randomize'];
		$tags = escape($_POST['tags']);
		$answer = $_POST['answer'];
		$feedBackCorrect = escape($_POST['feedBackCorrect']);
		$feedBackIncorrect = escape($_POST['feedBackIncorrect']);
		
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `link` = '{$link}', `randomize` = '{$randomize}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `randomize` = '{$randomize}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}'");	
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'True False', '{$points}', '{$extraCredit}', '', '{$category}', '{$link}', '{$randomize}', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', ''", "NULL, 'True False', '{$points}', '{$extraCredit}', '', '{$category}', '{$randomize}', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', ''");
		}
	}
	
//Title
	title($monitor['title'] . "True or False", "A true or false question will prompt a user to respond to a question as a true or false statement.");
	
//True false form
	echo form("trueFalse");
	catDivider("Question", "one", true);
	question();
	
	catDivider("Question Settings", "two");
	echo "<blockquote>\n";
	points();
	type();
	category();
	descriptionLink();
	randomize();
	tags();
	customField("Question Generator", "questionData");
	echo "</blockquote>\n";
	
	catDivider("Question Content", "three");
	echo "<blockquote>\n";
	directions("Select the correct answer", true);
	indent(radioButton("answer", "answer", "True,False", "1,0", true, true, false, false, "questionData", "answer"));
	echo "</blockquote>\n";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>