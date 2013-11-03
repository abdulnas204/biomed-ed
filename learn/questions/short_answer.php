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

This is the short answer management page for the test 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Short Answer", "tinyMCEMedia,validate,newObject,autoSuggest");
	$questionData = dataGrabber("Short Answer");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['answerValue'])) {
		$question = escape($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$case = $_POST['case'];
		$tags = escape($_POST['tags']);
		$answerValue = escape(arrayStore($_POST['answerValue']));
		$feedBackCorrect = escape($_POST['feedBackCorrect']);
		$feedBackIncorrect = escape($_POST['feedBackIncorrect']);
		$feedBackPartial = escape($_POST['feedBackPartial']);
		
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `link` = '{$link}', `case` = '{$case}', `tags` = '{$tags}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `case` = '{$case}', `tags` = '{$tags}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
		
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Short Answer', '{$points}', '{$extraCredit}', '', '{$category}', '{$link}', '0', '0', '', '{$case}', '{$tags}', '{$question}', '', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Short Answer', '{$points}', '{$extraCredit}', '', '{$category}', '0', '0', '', '{$case}', '{$tags}', '{$question}', '', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Title
	title($monitor['title'] . "Short Answer", "A short answer is a question in which a user must provide a one or two word response. These questions are scored automatically.");
	
//Short answer form
	echo form("shortAnswer");
	catDivider("Question", "one", true);
	question();
	
	catDivider("Question Settings", "two");
	echo "<blockquote>\n";
	points();
	type();
	category();
	descriptionLink();
	ignoreCase();
	tags();
	customField("Question Generator", "questionData");
	echo "</blockquote>\n";
	
	catDivider("Answers", "three");
	echo "<blockquote>\n";
	directions("Provide correct answer(s)", true, "A short answer is a question in which a user must provide a one or two   word   response. <br />When entering the information, all possible answer(s) to a question be provided in the <br />test setup. However, there will only be one text field in the test to provide an answer, <br />regardless of the number of possible answers provided in the setup. The user must only <br />match one of these answers in order to get the correct answer.");
	echo "<blockquote>\n<table id=\"items\">\n";
	
	if (isset($questionData)) {
		$answers = arrayRevert($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($answers) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\">\n";
			echo cell(textField("answerValue[]", "answerValue" . $value, false, false, false, true, false, $answers[$count]));
			echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" . $value . "', '1', true)\"></span>", "50");
			echo "</tr>\n";
		}
	} else {
		echo "<tr id=\"1\" align=\"center\">\n";
		echo cell(textField("answerValue[]", "answerValue1"));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '1', true)\"></span>");
		echo "</tr>\n";
	}
	
	echo "</table>\n<p>";
	echo "<span class=\"smallAdd\" onclick=\"addShortAnswer('items')\">Add Another Item</span>";
	echo "</p>\n</blockquote>\n</blockquote>\n";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>