<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: Novemeber 28th, 2010
Last updated: Novemeber 28th, 2010

This script contains additional functions relevent to this 
plugin only.
*/

/*
Server-side functions
---------------------------------------------------------
*/

//Prepare an uploaded file for storage
	function filePrepare($file) {
		$tempFile = $_FILES[$file] ['tmp_name'];
		$targetFile = basename($_FILES[$file] ['name']);
		$fileNameArray = explode(".", $targetFile);
		$targetFile = "";
		
		for ($count = 0; $count <= sizeof($fileNameArray) - 1; $count++) {
			if ($count == sizeof($fileNameArray) - 2) {
				$targetFile .= $fileNameArray[$count] . " " . randomValue(10, "alphanum") . ".";
			} elseif($count == sizeof($fileNameArray) - 1) {
				$targetFile .= $fileNameArray[$count];
			} else {
				$targetFile .= $fileNameArray[$count] . ".";
			}
		}
		
		$targetFile = escape($targetFile);
		
		return $targetFile;
	}
	
//Process an uploaded file
	function fileProcess($fileField, $uploadDirectory, $insertRequired, $updateRequired, $tableCheck, $arrayValue, $emptyURL, $errorUploadURL, $errorMIMEURL = false, array $allowedFiles = NULL) {
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$oldFile = query("SELECT * FROM `{$tableCheck}` WHERE `id` = '{$id}'");
			$targetFile = $oldFile[$arrayValue];
		} else {
			$targetFile = "";
		}
		
		if (is_uploaded_file($_FILES[$fileField] ['tmp_name'])) {
			$tempFile = $_FILES[$fileField] ['tmp_name'];
			$targetFile = filePrepare($fileField);	
			$filePath = rtrim($uploadDirectory, "/") . "/" . $targetFile;		
			
			if (!empty($allowedFiles) && $allowedFiles != NULL && !in_array(extension($targetFile), $allowedFiles)) {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $errorMIMEURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $errorMIMEURL);
				}
			}
			
			if (move_uploaded_file($tempFile, $filePath)) {
				unlink(rtrim($uploadDirectory, "/") . "/" . $oldFile[$arrayValue]);	
			} else {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $errorUploadURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $errorUploadURL);
				}
			}
		} else {
			if ($insertRequired == true) {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $emptyURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $emptyURL);
				}
			}
			
			if ($updateRequired == true) {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $emptyURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $emptyURL);
				}
			}
		}
		
		return $targetFile;
	}
	
//Montior access to the lesson and test wizard
	function monitor($title, $functions = false, $hideHTML = false) {
		global $connDBA, $strippedRoot;
		
		$titlePrefix = "Learning Wizard : ";
		
		if ($hideHTML == true) {
			$class = " class=\"overrideBackground\"";
		} else {
			$class = "";
		}
		
		if (!strstr($_SERVER['REQUEST_URI'], "/wizard/")) {
			if ($functions == false) {
				$functions = "showHide";
			} else {
				$functions .= ",showHide";
			}
		}
		
		if (strstr($_SERVER['REQUEST_URI'], "/wizard/lesson_settings.php") || strstr($_SERVER['REQUEST_URI'], "/questions/")) {
			$customScript = "<script type=\"text/javascript\">var data = new Spry.Data.XMLDataSet(\"" . $_SERVER['PHP_SELF'] . "?data=xml\", \"/root/group\");</script>";
		} else {
			$customScript = "";
		}
		
		headers($titlePrefix . $title, $functions, true, $class, false, false, false, false, $hideHTML, $customScript);
		$parentTable = "learningdata";
		$prefix = "";
		
		if (isset($_SESSION['currentModule'])) {
			$lessonTable = $prefix . "modulelesson" . "_" . $_SESSION['currentModule'];
			$testTable = $prefix . "moduletest" . "_" . $_SESSION['currentModule'];
			$directory = "../" . $_SESSION['currentModule'] . "/";
			$gatewayPath = "../../gateway.php/modules/" . $_SESSION['currentModule'] . "/";
			$redirect = "../module_wizard/test_content.php";
			$type = "Module";
			
			if (isset($_SESSION['currentModule'])) {
				$currentModule = $_SESSION['currentModule'];
				$currentTable = $_SESSION['currentModule'];
			} else {
				$currentModule = "";
				$currentTable = "";
			}
			
			$monitor = array("parentTable" => $parentTable, "lessonTable" => $lessonTable, "testTable" => $testTable, "prefix" => $prefix, "directory" => $directory, "gatewayPath" => $gatewayPath, "currentModule" => $currentModule, "currentTable" => $currentTable, "title" => $titlePrefix, "redirect" => $redirect, "type" => $type);
		} else {
			$id = lastItem($parentTable);
			$directory = "../" . $id . "/";
			
			$monitor = array("parentTable" => $parentTable, "title" => $titlePrefix, "prefix" => $prefix, "directory" => $directory);
		}
		
		$pageFile = end(explode("/", $_SERVER['SCRIPT_NAME']));
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
			$moduleDataTestGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'", $connDBA);
			$moduleDataTest = mysql_fetch_array($moduleDataTestGrabber);
			
			if ($moduleDataTestGrabber && empty($moduleDataTest['name'])) {
				$allowedArray = array("index.php", "lesson_settings.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_settings.php");
				}
			}
				
			if (!exist($monitor['lessonTable'], "position", "1")) {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "manage_content.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_content.php");
				}
			}
			
			if ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "0") {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "complete.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("test_check.php");
				}
			} elseif ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "1") {
				if (empty($moduleDataTest['testName'])) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_settings.php");
					}
				}
				
				if (!empty($moduleDataTest['testName']) && ! exist($monitor['testTable'], "position", "1")) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_content.php");
					}
				}
				
				if (!empty($moduleDataTest['testName']) && exist($monitor['testTable'], "position", "1")) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview.php", "test_verify.php", "complete.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_verify.php");
					}
				}
			}
		} elseif (!isset($_SESSION['currentModule']) && !strstr($_SERVER['REQUEST_URI'], "lesson_settings.php")) {
			if (!isset($_SESSION['questionBank'])) {
				$allowedArray = array("index.php", "lesson_settings.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_settings.php");
				}
			} else {
				$allowedArray = array("index.php", "lesson_settings.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php");
				if (!in_array($pageFile, $allowedArray)) {
					redirect("../question_bank/index.php");
				}
			}
		}
		
		return $monitor;
	}
	
//Keep track of steps in a module
	function navigation($title, $text, $break = true) {
		global $connDBA, $monitor;
		
		echo "<div class=\"layoutControl\"><div class=\"contentLeft\">";
		title($monitor['title'] . $title, $text, $break);
		echo "</div><div class=\"dataRight\" style=\"padding-top:15px;\">";
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
			$moduleDataTestGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'", $connDBA);
			$moduleDataTest = mysql_fetch_array($moduleDataTestGrabber);
		}
		
		echo "<ul id=\"navigationmenu\"><li class=\"toplast\"><a name=\"navigation\"><span>Navigation</span></a><ul><li>";
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php") . "</li>";
		} else {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php") . "</li>";
		}
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Content", "lesson_content.php") . "</li>";
		}
				
		if (exist($monitor['lessonTable'], "position", "1")) {
			echo "<li>" . URL("Verify Lesson", "lesson_verify.php") . "</li>";
		}
		
		if ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "0") {
			echo "<li>" . URL("Add Test", "test_check.php", "incomplete") . "</li>";
			echo "<li>" . URL("Complete", "complete.php", "complete") . "</li>";
		} elseif ($moduleDataTestGrabber && exist($monitor['lessonTable']) == true && $moduleDataTest['test'] == "1") {
			if ($moduleDataTestGrabber && $moduleDataTest['test'] == "1") {
				echo "<li>" . URL("Test Settings", "test_settings.php") . "</li>";
			}
			
			if (!empty($moduleDataTest['testName'])) {
				echo "<li>" . URL("Test Content", "test_content.php") . "</li>";
			}
			
			if (exist($monitor['testTable'], "position", "1")) {
				echo "<li>" . URL("Verify Test", "test_verify.php") . "</li>";
			}
			
			if (!empty($moduleDataTest['testName']) && exist($monitor['testTable'], "position", "1")) {
				echo "<li>" . URL("Complete", "complete.php", "complete") . "</li>";
			}
		}
		
		echo "</ul></li></ul></div></div>";

	}
	
//Regulate the how questions are inserted and updated
	function insertQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		switch ($type) {
			case "Module" :
				query("INSERT INTO `{$monitor['testTable']}` (
						  `id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (
						  {$moduleQuery}
					  )");
					  
				redirect($monitor['redirect'] . "?inserted=question");
				break;
							
			case "Bank" :
				query("INSERT INTO questionbank_0 (
						  `id`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (							
						  {$bankQuery}
					  )");
				
				$category = prepare($_POST['category'], false, true);
				$categoryID = query("SELECT * FROM `modulecategories` WHERE `category` = '{$category}'");
				
				redirect("../question_bank/index.php?id=" . $categoryID['id'] . "&inserted=question");
				break;
		}
	}
	
	function updateQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		if (isset($_GET['id'])) {
			$update = $_GET['id'];
		} elseif (isset($_GET['bankID'])) {
			$update = $_GET['bankID'];
		} elseif (isset($_GET['feedbackID'])) {
			$update = $_GET['feedbackID'];
		}
		
		switch ($type) {
			case "Module" :
				query("UPDATE `{$monitor['testTable']}` SET {$moduleQuery} WHERE `id` = '{$update}'");
				
				redirect($monitor['redirect'] . "?updated=question");
				break;
				
			case "Bank" :
				query("UPDATE `questionbank_0` SET {$bankQuery} WHERE `id` = '{$update}'");
				
				$category = prepare($_POST['category'], false, true);
				$categoryID = query("SELECT * FROM `modulecategories` WHERE `category` = '{$category}'");
				
				redirect("../question_bank/index.php?id=" . $categoryID['id'] . "&updated=question");
				break;
		}
		
	}
	
//Provide a letter grade for a test
	function grade($recieved, $total) {
		$score = round(sprintf($recieved / $total) * 100);
		
		switch ($score) {			
			case $score >= 90 :
				$letter = "A";
				$characterPrep = 100 - $score;
				break;
				
			case 80 <= $score && $score < 90 :
				$letter = "B";
				$characterPrep = 90 - $score;
				break;
				
			case 70 <= $score && $score < 80 :
				$letter = "C";
				$characterPrep = 80 - $score;
				break;
				
			case 60 <= $score && $score < 70 :
				$letter = "D";
				$characterPrep = 70 - $score;
				break;
				
			case $score < 60 :
				$letter = "F";
				$characterPrep = 60 - $score;
				break;
		}
		
		if ($score < 100) {
			switch (abs($characterPrep)) {
				case $characterPrep >= 7 :
					$character = "+";
					break;
					
				case $characterPrep <= 3 && $characterPrep < 7 :
					$character = "";
					break;
					
				case $characterPrep < 3 :
					$character = "-";
					break;
			}
		} else {
			$character = "+";
		}
		
		return $letter . $character;
	}
	
//Lesson content
	function lesson($id, $table, $preview = false) {
		global $monitor, $root;
		
		if ($preview == false) {
			$URL = $_SERVER['PHP_SELF'] . "?id=" . $id . "&";
		} else {
			$URL = $_SERVER['PHP_SELF'] . "?";
		}
		
	//Grab all of the lesson and module data
		$moduleData = query("SELECT * FROM `moduledata` WHERE `id` = '{$id}'");
	
		if (isset($_GET['page'])) {
			if (exist($table) == true) {
				$page = $_GET['page'];
				$lesson = exist($table, "position", $page);
				
				if ($lesson = exist($table, "position", $page)) {
					//Do nothing
				} else {
					redirect($URL . "page=1");
				}
			} else {
				redirect($_SERVER['PHP_SHELF']);
			}
		} else {
			redirect($URL . "page=1");
		}
		
		echo "<div class=\"layoutControl\">";
		
	//Display the title and navigation
		if ($preview !== "miniPreview") {
			$previousPage = intval($_GET['page']) - 1;
			$nextPage = intval($_GET['page']) + 1;
			
			echo "<div class=\"toolBar noPadding\">";
			title($lesson['title'], false, false, "lessonTitle");
			
			$navigation = "<div align=\"center\">";
			
			if (exist($table, "position", $previousPage) == true) {
				$navigation .= URL("Previous Step", $URL . "page=" . $previousPage , "previousPage");
				
				if (exist($table, "position", $nextPage) || (!exist($table, "position", $nextPage) && !access("modifyModule"))) {
					$navigation .= " | ";
				}
			}
			
			if (exist($table, "position", $nextPage) == true) {
				$navigation .= URL("Next Step", $URL . "page=" . $nextPage , "nextPage");
			}
		}
		
		if ($preview == false && $_SESSION['MM_UserGroup'] != "Site Administrator") {
			$userData = userData();
			$accessGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}'");
			$accessArray = unserialize($accessGrabber['modules']);
			
			if ($accessArray[$id]['testStatus'] == "F") {
				$testURL = "review.php?id=" . $id;
				$text = "Review Test";
			} else {
				$testURL = $URL . "action=finish";
				$text = "Proceed to Test";
			}
			
			if (!exist($table, "position", $nextPage) && !access("modifyModule")) {
				if ($moduleData['reference'] == "0" && $accessArray[$id]['testStatus'] != "F") {
					$alert = " onclick=\"return confirm('This action will close and lock access to the lesson until you have completed the test. Continue?')\"";
				} else {
					$alert = false;
				}
				
				$navigation .= URL($text, $testURL, "nextPage", false, false, false, false, false, false, $alert);
			} elseif (!exist($table, "position", $nextPage) && $moduleData['test'] == "0") {				
				$navigation .= URL("Finish", $testURL, "nextPage");
			}
		}
		
		if ($preview !== "miniPreview") {
			$navigation .= "</div>";
			
			echo $navigation . "</div><p>&nbsp;</p>";
		}
		
		if ($preview == false) {
			echo "<div class=\"dataLeft\">";
			$pagesGrabber = query("SELECT * FROM `{$table}` ORDER BY `position` ASC", "raw");
			$text = "";
			
			while($pages = fetch($pagesGrabber)) {
				if ($_GET['page'] != $pages['position']) {
					$text .= "<p>" . URL(prepare($pages['title'], false, true), "lesson.php?id=" . $_GET['id'] . "&page=" . $pages['position']) . "</p>";
				} else {
					$text .= "<p><span class=\"currentPage\">" . prepare($pages['title'], false, true) . "</span></p>";
				}
			}
			
			sideBox("Lesson Navigation", "Custom Content", $text);
			echo "</div><div class=\"contentRight\">";
		} else {
			echo "<div>";
		}
		
	//Display the content		
		echo prepare($lesson['content'], false, true);
		
		if (!empty($lesson['attachment'])) {
			echo "<br />";
			$location = str_replace(" ", "", $id);
			$file = $root . "gateway.php/modules/" . $location . "/lesson/" . $lesson['attachment'];
			$fileType = extension($file);
			echo "<div align=\"center\">";
			
			switch ($fileType) {
			//If it is a PDF
				case "pdf" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
			//If it is a Word Document
				case "doc" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "docx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a PowerPoint Presentation
				case "ppt" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "pptx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is an Excel Spreadsheet
				case "xls" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "xlsx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a Standard Text Document
				case "txt" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				case "rtf" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/text.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a WAV audio file
				case "wav" : echo "<object width=\"640\" height=\"16\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"16\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP3 audio file
				case "mp3" : echo "<object id=\"player\" width=\"640\" height=\"30\" data=\"" . $root . "system/flash/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/flash/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\", \"plugins\":{\"controls\":{\"autoHide\":false}}}' /></object>"; break;
			//If it is an AVI video file
				case "avi" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an WMV video file
				case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an FLV file
				case "flv" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "system/flash/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/flash/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is an MOV video file
				case "mov" : echo "<object width=\"640\" height=\"480\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"480\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP4 video file			
				case "mp4" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "system/flash/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/flash/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is a SWF video file
				case "swf" : echo "<object width=\"640\" height=\"480\" data=\"" . $file . "\" type=\"application/x-shockwave-flash\">
<param name=\"src\" value=\"" . $file . "\" /></object>"; break;
			}
			
			echo "</div>";
		}
		
		echo "</div></div>";
		
		if ($preview !== "miniPreview") {
			echo "<p>&nbsp;</p>" . $navigation;
		}
	}
	
//Test content
	function test($table, $fileURL, $preview = false) {
		global $connDBA, $testValues, $monitor;
		$attempt = lastItem($testTable, "testID", $testID, "attempt");
		
		if ($attempt - 1 == 0) {
			$currentAttempt = 1;
		} else {
			$currentAttempt = $attempt - 1;
		}
		
		form("test", "post", true);
		echo "<table width=\"100%\" class=\"dataTable\">";
		
		if ($preview == true) {
			if (is_numeric($preview)) {
				$additionalSQL = " WHERE `id` = '{$preview}'";
				$limit = " LIMIT 1";
			} else {
				$additionalSQL = "";
				$limit = "";
			}
		} else {
			$userData = userData();
			$testID = str_replace("moduletest_", "", $table);
			$selectionGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}'", "raw");
			$additionalSQLConstruct = " WHERE ";
			
			while ($selection = fetch($selectionGrabber)) {
				$additionalSQLConstruct .= "`id` = '{$selection['questionID']}' OR ";
			}
			
			$additionalSQL = rtrim($additionalSQLConstruct, " OR ") . " AND `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'";
			$limit = "";
		}
		
		if ($table != "questionbank_0" && $preview != false) {
			$order = " ORDER BY `position` ASC";
			$grab = "*";
			$join = "";
		} elseif (is_numeric($preview)) {
			$order = "";
			$grab = "*";
			$join = "";
		} else {
			$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}'");
			
			if ($moduleInfo['randomizeAll'] == "Randomize") {
				$order = " ORDER BY `randomPosition` ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = $table . ".*, testdata_" . $userData['id'] . ".randomPosition, testdata_" . $userData['id'] . ".answerValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $table . ".id = testdata_" . $userData['id'] . ".questionID";
		}
		
		if (!is_numeric($preview)) {
			$moduleInfo = query("SELECT * FROM	`moduledata` WHERE `id` = '{$testID}'");
		}
		
		$testDataGrabber = query("SELECT {$grab} FROM `{$table}`{$join}{$additionalSQL}{$order}{$limit}", "raw");
		$count = 1;
		$restrictImport = array();
		
	  	while ($testDataLoop = fetch($testDataGrabber)) {
			if ($preview == false) {
				$testValues = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}' AND `questionID` = '{$testDataLoop['id']}'");
			}
			
			if ($table != "questionbank_0" && $testDataLoop['questionBank'] == "1") {
				$importID = $testDataLoop['linkID'];
				$testData = query("SELECT * FROM `questionbank_0` WHERE `id` = '{$importID}'");
			} else {
				$testData = $testDataLoop;
			}
			
			if (!is_numeric($preview) && isset($testData['link']) && exist($table, "id", $testData['link']) && $moduleInfo['randomizeAll'] == "Randomize" && !empty($testData['link']) && $testDataLoop['link'] != "0" && !in_array($testDataLoop['link'], $restrictImport)) {
				$importDescription = query("SELECT * FROM `{$table}` WHERE `id` = '{$testDataLoop['link']}'");
				
				if ($importDescription['questionBank'] == "1") {
					$importDescription = query("SELECT * FROM `questionbank_0` WHERE `id` = '{$importDescription['linkID']}'");
				} else {
					$importDescription = $importDescription;
				}
				
				echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($importDescription['question'], false, true) . "</td></tr>";
				array_push($restrictImport, $testDataLoop['link']);
			}
			
			if ($testData['type'] != "Description") {
				echo "<tr><td width=\"100\" valign=\"top\"><p>";
				
				if (!is_numeric($preview)) {
					echo "<span class=\"questionNumber\">Question " . $count++ . "</span><br />";
				}
				
				echo "<span class=\"questionPoints\">" . $testData['points'] . " ";
				
				if ($testData['points'] == "1") {
					echo "Point";
				} else {
					echo "Points";
				}
				
				echo "</span>";
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "</p></td><td valign=\"top\">" . prepare($testData['question'], false, true);
				
				if ($testData['choiceType'] == "checkbox") {
					echo "(There may be more than one correct answer.)<br />";
				}
				
				echo "<br /><br />";
			}
			
			switch ($testData['type']) {
				case "Description" : 
					if (!in_array($testDataLoop['id'], $restrictImport)) {
						echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($testData['question'], false, true) . "</td></tr>";
						array_push($restrictImport, $testDataLoop['id']);
					}
					
					break;
				case "Essay" : 
					if (isset($testValues)) {
						textArea($testDataLoop['id'], $testDataLoop['id'], "small", true, false, unserialize($testValues['userAnswer']));
					} else {
						textArea($testDataLoop['id'], $testDataLoop['id'], "small", true);
					}
						
					break;
					
				case "File Response" : 
					if ($testData['totalFiles'] > 1 || sizeof(unserialize($testValues['userAnswer'])) > 1) {
						if (isset($monitor)) {
							$URL = $monitor['gatewayPath'] . "/test/responses";
						} else {
							$URL = "../gateway.php/modules/" . $_GET['id'] . "/test/responses";
							$fillValue = unserialize($testValues['userAnswer']);
						}
						
						echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">";
						
						if (isset($testValues) && !empty($fillValue)) {
							$fileID = 1;
							
							foreach ($fillValue as $key => $file) {
								echo "<tr id=\"" . $fileID . "\"><td>";
								
								fileUpload($testDataLoop['id'] . "_" . $fileID, $testDataLoop['id'] . "_" . $fileID, false, true, false, $fillValue[$key], false, false, $URL, false, true);
								echo "</td><td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=" . $fileID, "action smallDelete", false, false, false, false, false, false, " onclick=\"return confirm('This action will delete this file. Continue?')\"");
								echo "</td></tr>";
								
								$fileID++;
							}
							
							unset($fileID);
							
							echo "</table><p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p><p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>";
						} else {
							echo "<tr id=\"1\"><td>";
							fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, false, false, false, false, false, true);
							echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('upload_" . $testDataLoop['id'] . "', '1', '1', true)\"></span>";
							echo "</td></tr></table><p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p>";
						}
						
						echo "<p>Max file size (for single file): " . ini_get('upload_max_filesize') . "<br>Max file size (for all files): " . ini_get('post_max_size') . "</p>";
					} else {
						if (isset($testValues)) {
							$fillValue = unserialize($testValues['userAnswer']);
							
							if (!empty($fillValue)) {
								echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">";
								echo "<tr id=\"1\"><td>";
								fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, $fillValue['0'], false, false, "../gateway.php/modules/" . $_GET['id'] . "/test/responses", false, false);
								echo "</td><td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=1", "action smallDelete", false, false, false, false, false, false, " return confirm('This action will delete this file. Continue?')");
								echo "</td></tr></table>";
								echo "<p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>";
							} else {
								fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
							}
						} else {
							fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
						}
					}
					
					break;
					
				case "Fill in the Blank" : 
					$blankQuestion = unserialize($testData['questionValue']);
					$blank = unserialize($testData['answerValue']);
					$answerCompare = unserialize($testData['answerValue']);
					$valueNumbers = sizeof($blankQuestion);
					$matchingCount = 1;
					echo "<p>";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
					   echo prepare($blankQuestion[$list], false, true) . " ";
					   
					   if (!empty($blank[$list])) {
						   if (isset($testValues)) {
							   $value = unserialize($testValues['userAnswer']);
							   
							   if (is_array($value)) {
								   if (array_key_exists($list, $value)) {
									   echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true, false, $value[$list])  . " ";
								   } elseif (!array_key_exists($list, $value) && isset($answerCompare[$list])) {
									   echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
								   }
							   } else {
								    echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
							   }
						   } else {
						   		echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
						   }
					   }
					}
					
					echo "</p>";
					break;
				
				case "Matching" : 
					$question = unserialize($testData['questionValue']);
					$answer = unserialize($testValues['answerValueScrambled']);
					$answerCompare = unserialize($testData['answerValue']);
					$valueNumbers = sizeof($question);
					$matchingCount = 1;
					$fillValue = unserialize($testValues['userAnswer']);
					
					echo "<table width=\"100%\">";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
						echo "<tr><td width=\"20\">";
						$dropDownValue = "-,";
						$dropDownID = ",";
						
						for ($value = 1; $value <= $valueNumbers; $value++) {
							$dropDownValue .= $value . ",";
							$dropDownID .= $value . ",";
						}
						
						$values = rtrim($dropDownValue, ",");
						$IDs = rtrim($dropDownID, ",");
						
						if (isset($testValues)) {
							$value = unserialize($testValues['userAnswer']);
							 
							if (is_array($value)) {
								if (array_key_exists($list, $value)) {
									echo dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true, false, $fillValue[$list])  . " ";
								} elseif (!array_key_exists($list, $value) && isset($answerCompare[$list])) {
									dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
								}
							} else {
								dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
							}
						} else {
							dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true);
						}
						
						echo"</td><td width=\"200\"><p>" . prepare($question[$list], false, true) . "</p></td><td width=\"200\"><p>" . $matchingCount++ . ". " . prepare($answer[$list], false, true) . "</p></td></tr>";
						
					}
					
					echo"</table>";				  
					break;
				
				case "Multiple Choice" :									
					if ($preview == true) {
						$questions = unserialize($testData['questionValue']);
						
						if ($testData['randomize'] == "1") {
							$questionsDisplay = $questions;
							shuffle($questionsDisplay);
						} else {
							$questionsDisplay = $questions;
						}
					} else {
						if ($testData['randomize'] == "1") {
							$questions = unserialize($testData['answerValueScrambled']);
						} else {
							$questions = unserialize($testData['questionValue']);
						}
					}
					
					if ($testData['choiceType'] == "radio") {
						$questionValue = "";
						$questionID = "";
					
						while (list($questionKey, $questionArray) = each($questions)) {
							$questionValue .= $questionArray . ",";
							$questionID .= $questionKey + 1 . ",";
						}
						
						$values = rtrim($questionValue, ",");
						$IDs = rtrim($questionID, ",");
						
						
						if (isset($testValues)) {
							radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $IDs, false, true, false, unserialize($testValues['userAnswer']));
						} else {
							radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $IDs, false, true);
						}
						
					} else {
						while (list($questionKey, $questionArray) = each($questions)) {
							$identifier = $questionKey + 1;
							if (isset($testValues)) {
								if (is_array(unserialize($testValues['userAnswer']))) {
									$fillValue = unserialize($testValues['userAnswer']);
								} else {
									$fillValue = array(unserialize($testValues['userAnswer']));
								}
								
								if (in_array($identifier, $fillValue)) {
									checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1", true);
								} else {
									checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1");
								}
							} else {
								checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1");
							}
							
							echo "<br />";
						}
					}
					
					break;
					
				case "Short Answer" : 
					if (isset($testValues)) {
						textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true, false, unserialize($testValues['userAnswer']));
					} else {
						textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true);
					}
					
					break;
					
				case "True False" : 
					if ($preview == false) {
						if ($testData['randomize'] == "1") {						
							$label = unserialize($testValues['answerValueScrambled']);
							$id = implode(",", $label);
						} else {
							$label = unserialize($testValues['answerValue']);
							$id = implode(",", $label);
						}
					} else {
						$label = array("1", "0");
						
						if ($testData['randomize'] == "1") {
							shuffle($label);
						}
						
						if ($label['0'] == "1") {
							$id = "1,";
						} else {
							$id = "0,";
						}
						
						if ($label['1'] == "1") {
							$id .= "0";
						} else {
							$id .= "1";
						}
					}
					
					if ($label['0'] == "1") {
						$values = "True,";
					} else {
						$values = "False,";
					}
					
					if ($label['1'] == "1") {
						$values .= "True";
					} else {
						$values .= "False";
					}
					
					if (isset($testValues)) {
						radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true, false, unserialize($testValues['userAnswer']));
					} else {
						radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true);
					}
					
					break;
			}
			
			if ($testData['type'] != "Description") {
				echo "<br /><br /></td></tr>";
			}
		}
		
		echo "</table>";
		
		if ($preview == false) {
			echo "<blockquote><p>";
			button("save", "save", "Save", "submit", false);
			button("submit", "submit", "Submit", "submit", false, " return confirm('Once the test is submitted, it cannot be reopened. Continue?');");
			echo "</p></blockquote>";
		}
		
		closeForm(false, true);
	}
	
/*
Include JavaScripts and CSS for client-side modules
---------------------------------------------------------
*/
	
//Include the uploadify readying function
	function uploadifyTrigger($fileID, $formID) {
		global $root, $pluginRoot;
		
		$fileLimit = sprintf(ereg_replace("[^0-9]", "", ini_get('upload_max_filesize')) * 1024 * 1024);
		
		echo "<script type=\"text/javascript\">\n\$(function() {\n\$('#" . $fileID . "').uploadify({\n'uploader' : '" . $pluginRoot . "system/flash/upload.swf', \n'script' : '" . $_SERVER['REQUEST_URI'] . "', \n'cancelImg' : '" . $root . "system/images/common/x.png', 'sizeLimit' : " . $fileLimit . "\n});\n});\n</script>";
	}
	
//Include a full-size calendar script
	function fullCalendar() {
		global $root, $pluginRoot;
		
		return "<script src=\"" . $root . "system/javascripts/ajaxLibraries/jQuery_1.4.2.js\" type=\"text/javascript\"></script>
<script src=\"" . $root . "system/javascripts/ajaxLibraries/jQuery_UI_1.8.1.js\" type=\"text/javascript\"></script>
<script src=\"" . $pluginRoot . "system/javascripts/calendar.js\" type=\"text/javascript\"></script>
<link rel=\"stylesheet\" href=\"" . $pluginRoot . "system/styles/calendar/theme.css\" type=\"text/css\">
<link rel=\"stylesheet\" href=\"" . $pluginRoot . "system/styles/calendar/style.css\" type=\"text/css\">";
	}
	
//Include the administrative javascript library
	function administrativeLibrary() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/administrativeLibrary.js\" type=\"text/javascript\"></script>";
	}
?>