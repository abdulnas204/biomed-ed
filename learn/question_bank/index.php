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
Last updated: December 10th, 2010

This is the question bank management page.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Check to see if a category name exists
	validateName("categories", "category");
	
//Set necessary sessions and variables, and test to see if they are allowed access to this category
	if (isset($_GET['id'])) {
		$_SESSION['questionBank'] = $_GET['id'];
		$categoryPrep = query("SELECT * FROM `categories` WHERE `id` = '{$_GET['id']}'");
		
		if ($categoryPrep['organization'] == $userData['organization'] && exist("categories", "id", $_GET['id'])) {
			$category = escape($categoryPrep['category']);
		} else {
			redirect("index.php");
		}
	}
	
//Create a function to delete questions
	function questionBankDelete($id, $type) {
		global $userData;
		
		if ($type == "single") {
			$deleteGrabber = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$id}'", "raw");
			$sql = "`linkID` = '{$id}'";
		} else {
			$categoryPrep = query("SELECT * FROM `categories` WHERE `id` = '{$id}'");
			$category = escape($categoryPrep['category']);
			$deleteGrabber = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$category}'", "raw");
			$sql = "`category` = '{$category}'";
		}
		
		while ($delete = fetch($deleteGrabber)) {
		//Delete from tests in which this appears			
			if (exist("learningunits")) {
				$testDeleteGrabber = query("SELECT * FROM `learningunits`", "raw");
				
				while ($testDelete = fetch($testDeleteGrabber)) {
					$currentTable = $testDelete['id'];
					$questionGrabber = query("SELECT * FROM `test_{$currentTable}` WHERE {$sql}", "raw", false);
					
					if ($questionGrabber) {
						while($questions = fetch($questionGrabber)) {
							query("DELETE FROM `test_{$currentTable}` WHERE `id` = '{$questions['id']}'");
							query("UPDATE `test_{$currentTable}` SET `position` = position - 1 WHERE `position` > '{$questions['position']}'");
						}
					}
				}
			}
			
			query("DELETE FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$delete['id']}'");
			
			if ($delete['type'] == "File Response") {
				unlink("../questionbank_" . $userData['organization'] . "/test/answers/" . $delete['fileURL']);
			}
		}
	}
	
//Delete a question
	if (isset ($_GET['questionID']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		if (exist("questionbank_{$userData['organization']}", "id", $_GET['id'])) {
			questionBankDelete($_GET['questionID'], "single");
			redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['questionID']);
		}
	}
	
//Add a new category
	if (isset($_POST['submit']) && !empty($_POST['category']) && !exist("categories", "category", escape($_POST['category']))) {
		$category = $_POST['category'];
		$organization = $userData['organization'];
		
		query("INSERT INTO `categories` (
			  `id`, `organization`, `category`
			  ) VALUES (
			  NULL, '{$organization}', '{$category}'
			  )");
				
		redirect("index.php");
	}
	
//Edit a category name
	if (isset($_POST['submit']) && !empty($_POST['edit'])) {
		$id = $_POST['id'];
		$nameCheck = query("SELECT * FROM `categories` WHERE `id` = '{$id}'");
		$oldCategory = escape($nameCheck['category']);
		
		if (strtolower($nameCheck['category']) == strtolower($_POST['edit']) || !exist("categories", "category", escape($_POST['edit']))) {
			$category = escape($_POST['edit']);
			
			query("UPDATE `categories` SET `category` = '{$category}' WHERE `id` = '{$id}'");
			query("UPDATE `questionbank_{$userData['organization']}` SET `category` = '{$category}' WHERE `category` = '{$oldCategory}'");
			redirect("index.php");
		} else {
			redirect("index.php?error=category");
		}
	}
	
//Delete a category
	if (isset($_GET['categoryID']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		if (exist("categories", "id", $_GET['categoryID'])) {
			questionBankDelete($_GET['categoryID'], "category");
		}
		
		query("DELETE FROM `categories` WHERE `id` = '{$_GET['categoryID']}'");
		redirect("index.php");
	}
	
//Top Content
	if (isset ($_GET['id'])) {
		$bankTitle = query("SELECT * FROM `categories` WHERE `id` = '{$_GET['id']}'");
		$title = $bankTitle['category'] . " Bank";
	} else {
		$title = "Question Bank";
	}
	
	if(exist("categories", "organization", $userData['organization'])) {
		$categoryGrabber = query("SELECT * FROM `categories` WHERE `organization` = '{$userData['organization']}' ORDER BY `id` ASC", "raw");
		$validateReady = "<script type=\"text/javascript\">";
		
		while($categoryValidation = fetch($categoryGrabber)) {
			$validateReady .= "
  $(document).ready(function() {
	$(\"#validate_" . $categoryValidation['id'] . "\").validationEngine()
  });";
		}
		
		$validateReady .= "
</script>";
	} else {
		$validateReady = "";
	}
	
	headers($title, "learningUnitLibrary,validate", true, "onunload=\"opener.location.reload();\"", false, false, $validateReady);
	
//Title
	title($title, "Questions may be created here and be imported into tests when a learning unit is being created. The questions are broken up by their category.");

//Admin toolbar
	if (isset ($_GET['id'])) {
		echo "<div class=\"toolBar noPadding\">\n";
		echo form("jump");
		echo "<span class=\"toolBarItem noLink\">Add: ";
		echo dropDown("menu", "nenu", "- Select Question Type -,Description,Essay,File Response, Fill in the Blank, Matching, Multiple Choice, Short Answer, True or False", ",../questions/description.php,../questions/essay.php,../questions/file_response.php,../questions/blank.php,../questions/matching.php,../questions/multiple_choice.php,../questions/short_answer.php,../questions/true_false.php");
		echo button("submit", "submit", "Go", "button", false, "onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\"");
		echo "</span>\n";
		echo toolBarURL("Back to Categories", "index.php", "toolBarItem back");
		
		if (exist("questionbank_{$userData['organization']}", "category", $category)) {
			echo toolBarURL("Search", "search.php?id=" . $_GET['id'], "toolBarItem search");
		}
		
		echo closeForm(false); 
		echo "</div>\n";
	 } else {
		 echo "<div class=\"toolBar\">\n";
		 echo toolBarURL("Back to Overview", "../index.php", "toolBarItem back");
		 
		 if (exist("questionbank_{$userData['organization']}")) {
		 	echo toolBarURL("Search", "search.php", "toolBarItem search");
		 }
		 
		 echo "</div>\n";
	 }
	 
//Display message updates
	message("inserted", "question", "success", "The question was successfully inserted");
	message("updated", "question", "success", "The question was successfully updated");
	
	if (exist("categories", "organization", $userData['organization'])) {
	//Display the categories
		if (!isset ($_GET['id'])) {
			echo "<p>Please select a category from the list below.</p>\n";
			echo "<blockquote>\n";
			
			$categoryGrabber = query("SELECT * FROM `categories` WHERE `organization` = '{$userData['organization']}' ORDER BY `id` ASC", "raw");
			
			while ($categoryData = fetch($categoryGrabber)) {
				$name = escape($categoryData['category']);
				$questionValue = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$name}'", "num");
				
			//The standard display
				echo "<div onmouseover=\"rollOverTools('edit_" . $categoryData['id'] . "'); rollOverTools('delete_" . $categoryData['id'] . "');\" onmouseout=\"rollOverTools('edit_" . $categoryData['id'] . "'); rollOverTools('delete_" . $categoryData['id'] . "');\">\n";
				echo "<div class=\"contentShow\" id=\"standardDisplay_"  . $categoryData['id'] . "\">\n";
				echo URL($categoryData['category'], "index.php?id=" . $categoryData['id']) . " : ";
				
				if ($questionValue == 1) {
					echo $questionValue . " Question \n";
				} else {
					echo $questionValue . " Questions\n";
				}
				
				echo URL("", "javascript:void;", "contentHide action mediumEdit", false, "Edit the <strong>" . $categoryData['category'] . "</strong> category", false, false, false, false, "onclick=\"edit('" . $categoryData['id'] . "')\" id=\"edit_" . $categoryData['id'] . "\"") . "\n";
				echo URL("", "index.php?categoryID=" . $categoryData['id'] . "&action=delete", "contentHide action smallDelete", false, "Delete the <strong>" . $categoryData['category'] . "</strong> category", false, false, false, false, "onclick=\"return confirm('This action will remove this category and all of it\'s containing questions. If any of these questions are imported into a test, they will be removed from the test. To prevent the loss of these questions from a test, either move them to another category, or manually import the questions into the test. This action cannot be undone. Continue?')\" id=\"delete_" . $categoryData['id'] . "\"") . "\n";
				echo "</div>\n";
				
			//The quick editing view
				echo "<div class=\"contentHide\" id=\"editDisplay_"  . $categoryData['id'] . "\">\n";
				echo form("quickEdit", false, false, false, "validate_" . $categoryData['id']);
				echo textField("edit", "edit", "25", false, false, true, false, prepare($categoryData['category'], true));
				echo " : ";
				
				if ($questionValue == 1) {
					echo $questionValue . " Question \n";
				} else {
					echo $questionValue . " Questions\n";
				}
				
				echo button("submit", "submit", "Submit", "submit");
				echo URL("", "javascript:void", "action smallDelete", false, "Cancel", false, false, false, false, "onclick=\"clearEdit('"  . $categoryData['id'] . "')\"") . "\n";
				echo hidden("id", "id", $categoryData['id']) . "\n";
				echo closeForm(false);
				echo "</div>\n";
				echo "</div>\n";
				echo "<br />\n";
			}
			
		//Allow addition of category types
			echo "<div id=\"newCategory\" class=\"contentHide\">\n";
			echo form("insertCategory");
			directions("Category Name", true);
			echo textField("category", "category", false, false, false, true, "ajax[ajaxName]");
			echo "<br /><br />\n";
			echo button("submit", "submit", "Submit", "submit");
			echo closeForm(false);
			echo "<br />\n</div>\n<span class=\"smallAdd\" onclick=\"toggleInfo()\">Add New Category</span>\n";
			echo "</blockquote>\n";
	//Display the questions within a category
		} else {		
			if (exist("questionbank_{$userData['organization']}", "category", $category)) {
				$testImport = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$category}'", "raw");
				$count = 1;
				
				echo "<table class=\"dataTable\">\n<tr>\n";
				echo column("Type", "150");
				echo column("Point Value", "100");
				echo column("Question");
				echo column("Discover", "50");
				echo column("Edit", "50");
				echo column("Delete", "50");
				echo "</tr>\n";
				
				while ($testData = fetch($testImport)) {
					echo "<tr";
					if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
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
					echo cell(URL("", "discover.php?linkID=" . $testData['id'], "action discover", false, "Discover in which tests this <strong>" . $testData['type'] . "</strong> question is used"));
					
					$URL = "../questions/";
				
					switch ($testData['type']) {
						case "Description" : $URL .= "description.php"; break;
						case "Essay" : $URL .= "essay.php"; break;
						case "File Response" : $URL .= "file_response.php"; break;
						case "Fill in the Blank" : $URL .= "blank.php"; break;
						case "Matching" : $URL .= "matching.php"; break;
						case "Multiple Choice" : $URL .= "multiple_choice.php"; break;
						case "Short Answer" : $URL .= "short_answer.php"; break;
						case "True False" : $URL .= "true_false.php"; break;
					}
					
					$URL .= "?bankID=" . $testData['id'];
					
					echo editURL($URL, $testData['type'], "question");
					echo deleteURL("index.php?id=" . $_GET['id'] . "&questionID=" . $testData['id'] . "&action=delete", $testData['type'], "question", "This action will delete this question from the question bank, and from any tests which it is currently located. If you wish to keep this question inside its current tests, click the &quot;discover&quot; button, find in which tests this question is located, then import the question into the test.");
					echo "</tr>\n";
				}
				
				echo "</table>\n";
			} elseif (exist("categories", "id", $_GET['id'])) {
				echo "\n<div class=\"noResults\">There are no questions in this bank. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>\n";
			}
		}
	} else {
		echo "\n<div class=\"noResults\">Please <a href=\"javascript:void()\" onclick=\"toggleInfo()\">add at least one category</a> prior to entering questions.";
		
	//Allow addition of category types
		echo "\n<div id=\"newCategory\" class=\"contentHide\">";
		echo form("insertCategory");
		echo "<br />\n";
		echo textField("category", "category", false, false, false, true, "ajax[ajaxName]");
		echo "<br /><br />\n";
		echo button("submit", "submit", "Submit", "submit");
		echo closeForm(false);
		echo "</div>\n</div>\n";
	}
	
//Include the footer
	footer();
?>