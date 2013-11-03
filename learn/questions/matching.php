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

This is the matching management page for the test 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Matching", "tinyMCEMedia,tinyMCEQuestion,validate,newObject,autoSuggest");
	$questionData = dataGrabber("Matching");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['questionValue']) && !empty($_POST['answerValue'])) {
		$question = escape($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$partialCredit = $_POST['partialCredit'];
		$tags = escape($_POST['tags']);
		$questionValue = escape(arrayStore($_POST['questionValue']));
		$answerValue = escape(arrayStore($_POST['answerValue']));
		$feedBackCorrect = escape($_POST['feedBackCorrect']);
		$feedBackIncorrect = escape($_POST['feedBackIncorrect']);
		$feedBackPartial = escape($_POST['feedBackPartial']);
		
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `link` = '{$link}', `partialCredit` = '{$partialCredit}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `partialCredit` = '{$partialCredit}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Matching', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$category}', '{$link}', '0', '0', '', '1', '{$tags}', '{$question}', '{$questionValue}', '{$answer}', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Matching', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$category}', '0', '0', '', '1', '{$tags}', '{$question}', '{$questionValue}', '{$answer}', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Title
	title($monitor['title'] . "Matching", "A matching question will ask a user to match a series of similar values from a list of values.");
	
//Matching form
	echo form("matching");
	catDivider("Question", "one", true);
	question();
	
	catDivider("Question Settings", "two");
	echo "<blockquote>\n";
	points();
	type();
	category();
	descriptionLink();
	partialCredit();
	tags();
	customField("Question Generator", "questionData");
	echo "</blockquote>\n";
	
	catDivider("Question Content", "three");
	echo "<blockquote>\n";
	directions("Question content", true, "A matching question will ask a user to match a series of similar values   from a list of values. <br />When entering the information, the &quot;Left-Column Values&quot; column is the information which <br />the user will match with the &quot;Right-Column Values&quot; list. The &quot;Right-Column Values&quot; <br />column is automatically scrambled in the test for the user to match. When entering the <br />information entering the information, the correct values will go in the same row.");
	echo "<blockquote>\n<table class=\"dataTable\" id=\"items\">\n<tr>\n";
	echo column("Left-Column Values");
	echo column("Right-Column Values");
	echo column(false, "50");
	echo "</tr>\n";
	
	if (isset($questionData)) {
		$questions = arrayRevert($questionData['questionValue']);
		$answers = arrayRevert($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($questions) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\">\n";
			echo cell(textArea("questionValue[]", "questionValue" . $value, "extraSmall", true, false, $questions[$count], false, false, "class=\"noEditorMedia editorQuestion answerValue" . $count . "\""));
			echo cell(textArea("answerValue[]", "answerValue" . $value, "extraSmall", true, false, $answers[$count], false, false, "class=\"noEditorMedia editorQuestion questionValue" . $count . "\""));
			echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" . $value . "', '2')\"></span>", "50");
			echo "</tr>\n";
		}
		
		echo hidden("id", "id", $value);
	} else {
		echo "<tr id=\"1\" align=\"center\">\n";
		echo cell(textArea("questionValue[]", "questionValue1", "extraSmall", true, false, false, false, false, "class=\"noEditorMedia editorQuestion questionValue1\""));
		echo cell(textArea("answerValue[]", "answerValue1", "extraSmall", true, false, false, false, false, "class=\"noEditorMedia editorQuestion answerValue1\""));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '2')\"></span>", "50");
		echo "</tr>\n<tr id=\"2\" align=\"center\">\n";
		echo cell(textArea("questionValue[]", "questionValue2", "extraSmall", true, false, false, false, false, "class=\"noEditorMedia editorQuestion questionValue2\""));
		echo cell(textArea("answerValue[]", "answerValue2", "extraSmall", true, false, false, false, false, "class=\"noEditorMedia editorQuestion answerValue2\""));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '2', '2')\"></span>", "50");
		echo "</tr>\n";
		echo hidden("id", "id", "2");
	}
	
	echo "</table>\n<p>";
	echo "<span class=\"smallAdd\" onclick=\"addMatching('items')\">Add Another Item</span>";
	echo "</p>\n</blockquote>\n</blockquote>\n";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>