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
Last updated: Janurary 25th, 2011

This is the fill in the blank management page for the 
test generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Fill in the Blank", "tinyMCESimple,validate,newObject,autoSuggest");
	$questionData = dataGrabber("Fill in the Blank");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['questionValue']) && !empty($_POST['answerValue'])) {
		$question = escape($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$partialCredit = $_POST['partialCredit'];
		$case = $_POST['case'];
		$tags = escape($_POST['tags']);
		$questionValue = escape(serialize($_POST['questionValue']));
		$answerValue = escape(serialize($_POST['answerValue']));
		$feedBackCorrect = escape($_POST['feedBackCorrect']);
		$feedBackIncorrect = escape($_POST['feedBackIncorrect']);
		$feedBackPartial = escape($_POST['feedBackPartial']);
			
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `link` = '{$link}', `partialCredit` = '{$partialCredit}', `case` = '{$case}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `partialCredit` = '{$partialCredit}', `case` = '{$case}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Fill in the Blank', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$category}', '{$link}', '0', '0', '', '{$case}', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Fill in the Blank', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$category}', '0', '0', '', '{$case}', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Title
	title($monitor['title'] . "Fill in the Blank", "A fill in the blank question will prompt a user to complete a sentence with missing values by filling in the blanks.");
	
//Fill in the blank form
	echo form("blank");
	catDivider("Question", "one", true);
	question();
	
	catDivider("Question Settings", "two");
	echo "<blockquote>\n";
	points();
	type();
	category();
	descriptionLink();
	partialCredit();
	ignoreCase();
	tags();
	customField("Question Generator", "questionData");
	echo "</blockquote>\n";
	
	catDivider("Question Content", "three");
	echo "<blockquote>\n";
	directions("Question content", true, "A fill in the blank question will prompt a user to complete a sentence with missing values by filling in the blanks. <br />When entering the information, the &quot;Sentence&quot; column is the information the user will see. The &quot;Values&quot; column <br />is what the user will be prompted to fill in, in order to complete the incomplete sentence. If the last value in the <br />&quot;Values&quot; column is left blank, the system will understand that this is the end of the sentence, and will not <br />include it in the test.");
	echo "<blockquote>\n<table class=\"dataTable\" id=\"items\">\n<tr>\n";
	echo column("Sentence");
	echo column("Values");
	echo column(false, "50");
	echo "</tr>\n";
	
	if (isset($questionData)) {
		$questions = unserialize($questionData['questionValue']);
		$answers = unserialize($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($questions) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\">\n";
			echo cell(textField("questionValue[]", "questionValue" . $value, false, false, false, true, false, $questions[$count]));
			echo cell(textField("answerValue[]", "answerValue" . $value, false, false, false, false, false, $answers[$count]));
			echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" . $value . "', '1')\"></span>", "50");
			echo "</tr>\n";
		}
	} else {
		echo "<tr id=\"1\" align=\"center\">\n";
		echo cell(textField("questionValue[]", "questionValue1"));
		echo cell(textField("answerValue[]", "answerValue2", false, false, false, false));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '1')\"></span>", "50");
		echo "</tr>\n";
	}
	
	echo "</table>\n<p>";
	echo "<span class=\"smallAdd\" onclick=\"addBlank('items')\">Add Another Item</span>";
	echo "</p>\n</blockquote>\n</blockquote>\n";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>