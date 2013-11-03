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
Last updated: December 22nd, 2010

This is the description management page for the test 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Description", "tinyMCEAdvanced,tinyMCEMediaConfig,validate,autoSuggest");
	$questionData = dataGrabber("Description");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question'])) {
		$question = escape($_POST['question']);
		$type = $_POST['type'];
		$category = escape($_POST['category']);
		$tags = escape($_POST['tags']);
		
		if (isset($questionData)) {			
			updateQuery($type, "`question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}'", "`question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '', '{$lastQuestion}', 'Description', '0', '', '0', '{$category}', '0', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''", "NULL, 'Description', '0', '', '0', '{$category}', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''");
		}
	}
	
//Title
	title($monitor['title'] . "Description", "A description is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.");
	
//Description form
	echo form("description");
	catDivider("Content", "one", true);
	echo "<blockquote>\n";
	directions("Description content", true);
	indent(textArea("question", "questionContent", "large", true, false, false, "questionData", "question", "class=\"noEditorMedia\""));
	echo "</blockquote>\n";
	
	catDivider("Settings", "two");
	echo "<blockquote>\n";
	type();
	category();
	tags();
	customField("Question Generator", "questionData");
	echo "</blockquote>\n";
	
	catDivider("Submit", "three");
	formButtons();
	echo closeForm();

//Include the footer
	footer();
?>