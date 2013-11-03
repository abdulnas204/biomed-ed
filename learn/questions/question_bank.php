<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: August 21st, 2010
Last updated: Janurary 13th, 2011

This is the question selection page for importing questions 
from the question bank.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Question Bank", "liveSubmit");
		
//Check access to category
	if (exist("categories")) {
		if (isset ($_GET['id'])) {
			$category = query("SELECT * FROM `categories` WHERE `id` = '{$_GET['id']}'");
			
			if ($category['organization'] !== $userData['organization']) {
				redirect("question_bank.php");
			} else {
				$bankTitle = query("SELECT * FROM `categories` WHERE `id` = '{$_GET['id']}'");
			}
		}
	}

//Process the form
	if (isset($_POST['id'])) {
		if ($_POST['import']) {
			if (exist("questionbank_{$userData['organization']}", "id", $_POST['id'])) {
				$questionData = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$_POST['id']}'");
				$id = $_POST['id'];
				$type = $questionData['type'];
				$lastQuestion = lastItem($monitor['testTable']);
				
				insertQuery("Learning Unit", "NULL, '1', '{$id}', '{$lastQuestion}', '{$type}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''");
				redirect("../wizard/test_content.php");
			}
		} else {
			$questionPositionArray = query("SELECT * FROM `{$monitor['testTable']}` WHERE `linkID` = '{$_POST['id']}'");
			$questionPosition = $questionPositionArray['position'];
			
			query("DELETE FROM `{$monitor['testTable']}` WHERE `linkID` = '{$_POST['id']}'");
			query("UPDATE `{$monitor['testTable']}` SET `position` = position - 1 WHERE `position` > '{$questionPosition}'");
			redirect("../wizard/test_content.php");
		}
	}
	
//Title
	title("Question Bank", "Questions may be imported into the current test via the question bank tool.");

//Admin toolbar
	if (isset ($_GET['id'])) {
		echo "<div class=\"toolBar\">\n";
		echo toolBarURL("Back to Categories", "question_bank.php", "toolBarItem back");
		echo toolBarURL("Edit Questions", "../question_bank/index.php?id=" . $_GET['id'], "toolBarItem editTool", false, false, false, true, "800", "600");
		echo "</div>\n<br />\n";
	}

	if (exist("categories", "organization", $userData['organization'])) {
		if (!isset ($_GET['id'])) {
			echo "<p>Please select a category from the list below.</p>\n";
			echo "<blockquote>\n";
			$categoryGrabber = query("SELECT * FROM `categories` ORDER BY `id` ASC", "raw");
			
			while ($category = fetch($categoryGrabber)) {
				$name = escape($category['category']);
				$questionValue = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$name}'", "num");
				
				echo URL($category['category'], "question_bank.php?id=" . $category['id']) . " : ";
				
				if ($questionValue == 1) {
					echo $questionValue . " Question\n<br /><br />\n";
				} else {
					echo $questionValue . " Questions\n<br /><br />\n";
				}
			}
			
			echo "<br /><br />\n";
			echo button("cancel", "cancel", "Back to Test Questions", "cancel", "../wizard/test_content.php");
			echo "</blockquote>\n";
		} else {
			$name = escape($category['category']);
				
			if (exist("questionbank_{$userData['organization']}", "category", $name)) {
				$testImport = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$name}'", "raw");
				$count = 1;	
				
				catDivider("Select Questions", "one", true);
				echo "<blockquote>\n";
				echo "<table class=\"dataTable\">\n<tr>\n";
				echo column("Import", "50");
				echo column("Type", "150");
				echo column("Point Value", "100");
				echo column("Question");
				echo "</tr>";
				
				while ($testData = fetch($testImport)) {
					$checkboxImport = query("SELECT * FROM `{$monitor['testTable']}` WHERE `linkID` = '{$testData['id']}'", "raw");
					
					echo "<tr";
					if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					
					$content = form("importForm");
					$content .= hidden("id", "id_" . $testData['id'], $testData['id']);
					
					if ($checkboxImport) {
						$content .= checkbox("import", "import_" . $testData['id'], false, false, false, false, "on", false, false, false, " onclick=\"Spry.Utils.submitForm(this.form);\"");
					} else {
						$content .= checkbox("import", "import_" . $testData['id'], false, false, false, false, false, false, false, false, " onclick=\"Spry.Utils.submitForm(this.form);\"");
					}
					
					$content .= closeForm(false);
					
					echo cell($content, "50");
					echo preview(commentTrim(30, $testData['type']), "preview.php?id=" . $testData['id'], "question", "150", true);
					
					if ($testData['extraCredit'] == "on") {
						$class = " class=\"extraCredit\"";
					} else {
						$class = "";
					}
					
					if ($testData['points'] == "1") {
						$point = " Point";
					} else {
						$point = " Points";
					}
					
					echo cell("<div" . $class  . ">" . $testData['points'] . $point . "</div>", "100");
					echo cell(commentTrim(85, $testData['question']));
					echo "</tr>\n";
				}
				
				echo "</table>\n</blockquote>\n";
				
				catDivider("Submit", "two");
				indent(button("submit", "submit", "Submit", "cancel", "../wizard/test_content.php") . "\n" . 
				button("cancel", "cancel", "Cancel", "cancel", "../wizard/test_content.php"));
				echo "</div>\n";
			} else {
				echo "<div class=\"noResults\">There are no questions in this bank.</div>\n";
			}
		}
	} else {
		echo "<div class=\"noResults\">There are no categories in the question bank.</div>\n</br /><br />\n<blockquote>";
		echo button("cancel", "cancel", "Back to Test Questions", "cancel", "../wizard/test_content.php");
		echo "</blockquote>\n";
	}
	
//Include the footer
	footer();
?>