<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: August 16th, 2010
Last updated: December 3rd, 2010

This is the lesson settings page for the learning unit 
generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Lesson Settings", "tinyMCEMedia,validate,enableDisable,navigationMenu,autoSuggest");
	
//Grab the form data
	if (isset($_SESSION['currentUnit'])) {
		$lessonData = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentUnit']}'");
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['category']) && !empty($_POST['time']) && !empty($_POST['timeLabel']) && is_numeric($_POST['selected'])) {
		$name = escape($_POST['name']);
		$comments = escape($_POST['comments']);
		$time = $_POST['time'];
		$timeLabel = $_POST['timeLabel'];
		$category = escape($_POST['category']);
		$price = escape($_POST['price']);
		$enablePrice = escape($_POST['enablePrice']);
		$locked = $_POST['locked'];
		$selected = $_POST['selected'];
		$feedback = $_POST['feedback'];
		$tags = escape($_POST['tags']);
		$searchEngine = $_POST['searchEngine'];
		$timeFrame = $time . $timeLabel;
		
		if ($lessonData) {
			$id = $lessonData['id'];
								
			query("UPDATE `{$monitor['parentTable']}` SET `locked` = '{$locked}', `name` = '{$name}', `category` = '{$category}', `timeFrame` = '{$timeFrame}', `comments` = '{$comments}', `price` = '{$price}', `enablePrice` = '{$enablePrice}', `selected` = '{$selected}', `feedback` = '{$feedback}', `tags` = '{$tags}', `searchEngine` = '{$searchEngine}' WHERE `id` = '{$id}'");
		} else {
			$organization = $userData['organization'];
			
			query("INSERT INTO `{$monitor['parentTable']}` (
				  `id`, `locked`, `visible`, `name`, `category`, `timeFrame`, `comments`, `price`, `enablePrice`, `selected`, `feedback`, `tags`, `searchEngine`, `test`, `testName`, `directions`, `score`, `attempts`, `forceCompletion`, `completionMethod`, `reference`, `delay`, `gradingMethod`, `penalties`, `timer`, `time`, `randomizeAll`, `questionBank`, `display`, `organization`
				  ) VALUES (
				  NULL, '{$locked}', '', '{$name}', '{$category}', '{$timeFrame}', '{$comments}', '{$price}',  '{$enablePrice}', '{$selected}', '{$feedback}', '{$tags}', '{$searchEngine}', '0', '', '', '80', '1', '', '0', '0', '0', 'Highest Grade', '1', '', 'a:2:{i:0;s:1:\"0\";i:1;s:2:\"00\";}', 'Sequential Order', '0', 'a:1:{i:0;s:1:\"1\";}', '{$organization}'
				  )");
						
			$id =  mysql_insert_id();
			
			query("CREATE TABLE IF NOT EXISTS `lesson_{$id}` (
					`id` int(255) NOT NULL AUTO_INCREMENT,
					`position` int(100) NOT NULL,
					`title` longtext NOT NULL,
					`content` longtext NOT NULL,
					`attachment` longtext NOT NULL,
					PRIMARY KEY (`id`)
				  )");
						
			mkdir("../" . $id, 0777);
			mkdir("../" . $id . "/lesson", 0777);
			mkdir("../" . $id . "/lesson/browser", 0777);
			mkdir("../" . $id . "/lesson/browser/public", 0777);
			mkdir("../" . $id . "/lesson/browser/secure", 0777);
			mkdir("../" . $id . "/test", 0777);
			mkdir("../" . $id . "/test/answers", 0777);
			mkdir("../" . $id . "/test/responses", 0777);
						
			$_SESSION['currentUnit'] = $id;
		}
		
		if ($_POST['submit'] == "Finish" && isset($_SESSION['currentUnit']) && isset($_SESSION['review'])) {
			redirect("../index.php?updated=unit");
		} else {
			redirect("lesson_content.php");
		}
	}
	
//Title
	navigation("Lesson Settings", "Begin by setting up the lesson settings, such as its name, time frame, and any comments.");
	
//Lesson settings form
	echo form("lessonSettings");
	catDivider("Lesson Information", "one", true);
	echo "<blockquote>\n";
	directions("Lesson Name", true, "The name of the lesson");
	indent(textField("name", "name", false, false, false, true, false, false, "lessonData", "name"));
	directions("Directions" , true, "Comments or directions regarding the content of this lesson");
	indent(textArea("comments", "comments", "small", true, false, false, "lessonData", "comments"));
	directions("Due date" , false, "The amount of time the user will have to complete this unit from the assigned date");
	
	//Select the time frame
	if ($lessonData) {
		$time = strip($lessonData['timeFrame'], "numbersOnly");
		$timeLabel = strip($lessonData['timeFrame'], "lettersOnly");
	} else {
		$time = "2";
		$timeLabel = "Weeks";
	}
	
	indent(dropDown("time", "time", "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25", "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25", false, false, false, $time) . 
	dropDown("timeLabel", "timeLabel", "Days,Weeks,Months,Years", "Days,Weeks,Months,Years", false, false, false, $timeLabel) . 
	" from scheduled date");
	category();
	echo "</blockquote>\n";
	
	catDivider("Lesson Settings", "two");
	echo "<blockquote>";
	
	if (access("Edit Unowned Learning Units")) {
		directions("Price", false, "Set the price of this learning unit, if a user purchases <br />them individually.");
		
		if (empty($lessonData['price'])) {
			$price = textField("price", "price", "7", false, false, false, false, false, "lessonData", "price", " disabled=\"disabled\"");
		} else {
			$price = textField("price", "price", "7", false, false, false, false, false, "lessonData", "price");
		}
		
		indent("\$ " . $price . " " . 
		checkbox("enablePrice", "enablePrice", "Enable", false, false, false, false, "lessonData", "enablePrice", "on", " onclick=\"flvFTFO1('lessonSettings','price,t')\""));
	}
	
	directions("Lock settings", false, "Prevent this learning unit from being customized");
	indent(radioButton("locked", "locked", "Yes,No", "1,0", true, false, false, "0", "lessonData", "locked"));
	directions("Force lesson", false, "Force every user in this system to take this lesson");
	indent(radioButton("selected", "selected", "Yes,No", "1,0", true, false, false, "0", "lessonData", "selected"));
	
	if (access("Edit Unowned Learning Units")) {
		directions("Force user to give feedback", false, "Force a user to provide feedback at the end of this lesson");
		indent(radioButton("feedback", "feedback", "Yes,No", "1,0", true, false, false, "0", "lessonData", "feedback"));
		directions("Search keywords (Seperate keywords with a comma and a space)", false, "Supply a list of key words to help narrow down results in searches.<br />These seach results can show up on a search engine, such as Google, to help boost sales.");
		indent(textField("tags", "tags", false, false, false, false, false, false, "lessonData", "tags") . 
		"&nbsp;" . 
		checkbox("searchEngine", "searchEngine", "Accessible by search engines", false, false, false, false, "lessonData", "searchEngine", "on"));
	}
	
	echo "</blockquote>";
	
	catDivider("Submit", "three");
	echo "<blockquote><p>\n";
	
//Display navigation buttons
	echo button("submit", "submit", "Next Step &gt;&gt;", "submit");
	
	if (!isset($_SESSION['currentUnit'])) {
		echo button("cancel", "cancel", "Cancel", "cancel", "../index.php");
	}

	if (isset ($_SESSION['review'])) {
		echo button("submit", "submit", "Finish", "submit");
	}
	
	echo "</p></blockquote>\n";
	echo closeForm();
	
//Include the footer
	footer();
?>