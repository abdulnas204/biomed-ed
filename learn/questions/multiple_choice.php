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

This is the multiple choice management page for the test 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Multiple Choice", "tinyMCEMedia,tinyMCEQuestion,validate,newObject,autoSuggest");
	$questionData = dataGrabber("Multiple Choice");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['choices']) && !empty($_POST['values'])) {
		$question = escape($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$partialCredit = $_POST['partialCredit'];
		$randomize = $_POST['randomize'];
		$tags = escape($_POST['tags']);
		$questionValue = escape(serialize($_POST['values']));
		$answerValue = escape(serialize($_POST['choices']));
		$feedBackCorrect = escape($_POST['feedBackCorrect']);
		$feedBackIncorrect = escape($_POST['feedBackIncorrect']);
		$feedBackPartial = escape($_POST['feedBackPartial']);
		
		if (sizeof($_POST['choices']) == "1") {
			$interface = "radio";
		} elseif (sizeof($_POST['choices']) > "1") {
			$interface = "checkbox";
		} elseif (sizeof($_POST['choices']) == "0") {
			redirect("multiple_choice.php");
		}
		
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `link` = '{$link}', `randomize` = '{$randomize}', `partialCredit` = '{$partialCredit}', `choiceType` = '{$interface}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `randomize` = '{$randomize}', `partialCredit` = '{$partialCredit}', `choiceType` = '{$interface}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Multiple Choice', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$category}', '{$link}', '{$randomize}', '0', '{$interface}', '1', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Multiple Choice', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$category}', '{$randomize}', '0', '{$interface}', '1', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Title
	title($monitor['title'] . "Multiple Choice", "A multiple choice question will prompt a user to select the correct answer(s) from a list of choices.");
	
//Multiple choice form
	echo form("choice");
	catDivider("Question", "one", true);
	question();
	
	catDivider("Question Settings", "two");
	echo "<blockquote>\n";
	points();
	type();
	category();
	descriptionLink();
	partialCredit();
	randomize();
	tags();
	customField("Question Generator", "questionData");
	echo "</blockquote>\n";
	
	catDivider("Question Content", "three");
	echo "<blockquote>\n";
	directions("Question content (Fill then select correct answers)", true, "A multiple choice question will prompt a user to select the correct answer(s) from a list of choices.<br />When entering the information, the text will go in the text fields, and the correct answer(s) will be <br />provided by checking the check box next to the corresponding text field.");
	echo "<blockquote>\n<table id=\"items\">\n";
	
	if (isset($questionData)) {
		$values = unserialize($questionData['questionValue']);
		$choices = unserialize($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($values) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\">\n";
			
			if (in_array($value, $choices)) { 
				echo cell(checkbox("choices[]", "choice" . $value, false, $value, true, "1", true));
			} else {
				echo cell(checkbox("choices[]", "choice" . $value, false, $value, true, "1"));
			}
			
			echo cell(textArea("values[]", "value" . $value, "extraSmall", true, false, $values[$count], false, false, "class=\"noEditorMedia editorQuestion value" . $count . "\""));
			echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true)\"></span>", "50");
			echo "</tr>\n";
		}
		
		echo hidden("id", "id", $value);
	} else {
		echo "<tr id=\"1\" align=\"center\">\n";
		echo cell(checkbox("choices[]", "choice1", false, "1", true, "1"));
		echo cell(textArea("values[]", "value1", "extraSmall", true, false, false, false, false, "class=\"noEditorMedia editorQuestion value1\""));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true, true)\"></span>", "50");
		echo "</tr>\n<tr id=\"2\" align=\"center\">\n";
		echo cell(checkbox("choices[]", "choice2", false, "2", true, "1"));
		echo cell(textArea("values[]", "value2", "extraSmall", true, false, false, false, false, "class=\"noEditorMedia editorQuestion value2\""));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true, true)\"></span>", "50");
		echo "</tr>\n";
		echo hidden("id", "id", "2");
	}
	
	echo "</table>\n<p>";
	echo "<span class=\"smallAdd\" onclick=\"addMultipleChoice('items')\">Add Another Item</span>";
	echo "</p>\n</blockquote>\n</blockquote>\n";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>