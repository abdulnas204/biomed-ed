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
Last updated: December 4th, 2010

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
				$targetFile .= $fileNameArray[$count] . "_" . randomValue(10, "alphanum") . ".";
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
			$customScript = "<script type=\"text/javascript\">
  var data = new Spry.Data.XMLDataSet(\"" . $_SERVER['PHP_SELF'] . "?data=xml\", \"/root/group\");
</script>";
		} else {
			$customScript = "";
		}
		
		headers($titlePrefix . $title, $functions, true, $class, false, $hideHTML, $customScript);
		$parentTable = "learningunits";
		
		if (isset($_SESSION['currentUnit'])) {
			$lessonTable = "lesson" . "_" . $_SESSION['currentUnit'];
			$testTable = "test" . "_" . $_SESSION['currentUnit'];
			$directory = "../" . $_SESSION['currentUnit'] . "/";
			$gatewayPath = "../gateway.php/" . $_SESSION['currentUnit'] . "/";
			$redirect = "../wizard/test_content.php";
			$type = "Learning Unit";
			
			if (isset($_SESSION['currentUnit'])) {
				$currentUnit = $_SESSION['currentUnit'];
				$currentTable = $_SESSION['currentUnit'];
			} else {
				$currentUnit = "";
				$currentTable = "";
			}
			
			$monitor = array("parentTable" => $parentTable, "lessonTable" => $lessonTable, "testTable" => $testTable, "directory" => $directory, "gatewayPath" => $gatewayPath, "currentUnit" => $currentUnit, "currentTable" => $currentTable, "title" => $titlePrefix, "redirect" => $redirect, "type" => $type);
		} else {
			$id = nextID($parentTable);
			$directory = "../" . $id . "/";
			
			$monitor = array("parentTable" => $parentTable, "title" => $titlePrefix, "directory" => $directory);
		}
		
		$pageFile = end(explode("/", $_SERVER['SCRIPT_NAME']));
		
		if (isset($_SESSION['currentUnit'])) {
			$id = $_SESSION['currentUnit'];
			$data = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$id}'");
			
			if (!exist($monitor['lessonTable'], "position", "1") && empty($data['name'])) {
				$allowedArray = array("index.php", "lesson_settings.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_settings.php");
				}
			}
				
			if (!exist($monitor['lessonTable'], "position", "1") && !empty($data['name'])) {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "manage_content.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_content.php");
				}
			}
			
			if (exist($monitor['lessonTable'], "position", "1") && $data['test'] == "0") {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "complete.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_verify.php");
				}
			} elseif (exist($monitor['lessonTable'], "position", "1") && $data['test'] == "1") {
				if (empty($data['testName'])) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_settings.php");
					}
				}
				
				if (!empty($data['testName']) && !exist($monitor['testTable'], "position", "1")) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview_question.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_content.php");
					}
				}
				
				if (exist($monitor['testTable'], "position", "1") && !empty($data['testName'])) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview.php", "test_verify.php", "complete.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_verify.php");
					}
				}
			}
		} elseif (!isset($_SESSION['currentUnit']) && !strstr($_SERVER['REQUEST_URI'], "lesson_settings.php")) {
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
	
//Keep track of steps in the learning unit wizard
	function navigation($title, $text, $break = true) {
		global $connDBA, $monitor;
		
		function navigationHighlight($title, $URL) {
			if (strstr($_SERVER['PHP_SELF'], $URL)) {
				echo "<li><a style=\"color:#0000FF; font-weight:bolder; cursor:default;\" name=\"current\">" . $title . "</a></li>\n";
			} else {
				echo "<li>" . URL($title, $URL) . "</li>\n";
			}
		}
		
		echo "<div class=\"layoutControl\">\n<div class=\"contentLeft\">\n";
		title($monitor['title'] . $title, $text, $break);
		echo "</div>\n<div class=\"dataRight\" style=\"padding-top:15px;\">\n";
		
		if (isset($_SESSION['currentUnit'])) {
			$id = $_SESSION['currentUnit'];
			$data = query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'");
		}
		
		echo "<ul id=\"navigationmenu\">\n<li class=\"toplast\">\n<a name=\"navigation\"><span>Navigation</span></a>\n<ul>\n<li>\n";
		
		if (isset($data) && !empty($data['name'])) {
			navigationHighlight("Lesson Settings", "lesson_settings.php");
		} else {
			navigationHighlight("Lesson Settings", "lesson_settings.php");
		}
		
		if (isset($data) && !empty($data['name'])) {
			navigationHighlight("Lesson Content", "lesson_content.php");
		}
				
		if (isset($data) && exist($monitor['lessonTable'], "position", "1")) {
			navigationHighlight("Verify Content", "lesson_verify.php");
		}
		
		if (isset($data) && exist($monitor['lessonTable'], "position", "1") && $data['test'] == "0") {
			navigationHighlight("Add Test", "test_check.php", "incomplete");
			navigationHighlight("Complete", "complete.php", "complete");
		} elseif (isset($data) && exist($monitor['lessonTable'], "position", "1") && $data['test'] == "1") {
			navigationHighlight("Test Settings", "test_settings.php");
			
			if (!empty($data['testName'])) {
				navigationHighlight("Test Content", "test_content.php");
			}
			
			if (exist($monitor['testTable'], "position", "1")) {
				navigationHighlight("Verify Test", "test_verify.php");
			}
			
			if (!empty($data['testName']) && exist($monitor['testTable'], "position", "1")) {
				navigationHighlight("Complete", "complete.php", "complete");
			}
		}
		
		echo "</ul>\n</li>\n</ul>\n</div>\n</div>\n";
	}
	
//Regulate the how questions are inserted and updated
	function insertQuery($type, $unitQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		switch ($type) {
			case "Learning Unit" :
				query("INSERT INTO `{$monitor['testTable']}` (
						  `id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (
						  {$unitQuery}
					  )");
					  
				redirect($monitor['redirect'] . "?inserted=question");
				break;
							
			case "Bank" :
				query("INSERT INTO `questionbank_0` (
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
	
	function updateQuery($type, $unitQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		if (isset($_GET['id'])) {
			$update = $_GET['id'];
		} elseif (isset($_GET['bankID'])) {
			$update = $_GET['bankID'];
		} elseif (isset($_GET['feedbackID'])) {
			$update = $_GET['feedbackID'];
		}
		
		switch ($type) {
			case "Learning Unit" :
				query("UPDATE `{$monitor['testTable']}` SET {$unitQuery} WHERE `id` = '{$update}'");
				
				redirect($monitor['redirect'] . "?updated=question");
				break;
				
			case "Bank" :
				query("UPDATE `questionbank_0` SET {$bankQuery} WHERE `id` = '{$update}'");
				
				$category = prepare($_POST['category']);
				$categoryID = query("SELECT * FROM `categories` WHERE `category` = '{$category}'");
				
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
		global $monitor, $root, $pluginRoot, $userData;
		
		if ($preview == false) {
			$URL = $_SERVER['PHP_SELF'] . "?id=" . $id . "&";
		} else {
			$URL = $_SERVER['PHP_SELF'] . "?";
		}
		
	//Grab all of the lesson content and settings
		$settings = query("SELECT * FROM `learningunits` WHERE `id` = '{$id}'");
	
		if (isset($_GET['page'])) {
			if (exist($table)) {
				$page = $_GET['page'];
								
				if (exist($table, "position", $page)) {
					$lesson = query("SELECT * FROM `{$table}` WHERE `position` = '{$page}'");
				} else {
					redirect($URL . "page=1");
				}
			} else {
				redirect($_SERVER['PHP_SHELF']);
			}
		} else {
			redirect($URL . "page=1");
		}
		
	//Display the title and navigation
		if ($preview == false) {
			$previousPage = intval($_GET['page']) - 1;
			$nextPage = intval($_GET['page']) + 1;
			
			echo "<div class=\"toolBar noPadding\">";
			
			title($lesson['title'], false);
			
		//Drop-down menu displaying all pages
			if ($preview == false) {
				$pagesGrabber = query("SELECT * FROM `{$table}` ORDER BY `position` ASC", "raw");
				$count = 1;
				
				echo "<ul id=\"navigationmenu\">\n<li class=\"toplast\">\n<a name=\"navigation\"><span>Lesson Navigation</span></a>\n<ul>\n<li>\n";
				
				while($pages = fetch($pagesGrabber)) {
					if ($_GET['page'] != $pages['position']) {
						echo "<li>" . URL($count . ". " . $pages['title'], $URL . "page=" . $pages['position']) . "</li>\n";
					} else {
						echo "<li><a style=\"color:#0000FF; font-weight:bolder; cursor:default;\" name=\"current\">" . $count . ". " . $pages['title'] . "</a></li>\n";
					}
					
					$count++;
				}
				
				echo "</ul>\n</li>\n</ul>\n";
			}
			
			$navigation = "<div align=\"center\">\n";
			
			if (exist($table, "position", $previousPage)) {
				$navigation .= URL("Previous Step", $URL . "page=" . $previousPage , "previousPage");
				
				if (exist($table, "position", $nextPage) || ($settings['test'] == "1" && exist(str_replace("lesson", "test", $table) && $preview == false))) {
					$navigation .= " | ";
				}
			}
			
			if (exist($table, "position", $nextPage)) {
				$navigation .= URL("Next Step", $URL . "page=" . $nextPage , "nextPage");
			}
		}
		
	//Link to the test, if it exists and the user is assigned to this learning unit
		$accessArray = unserialize($userData['learningunits']);
	
		if ($preview == false && is_array($accessArray) && in_array($id, $accessArray)) {
			if (array_key_exists($id, $accessArray)) {
				if ($accessArray[$id]['testStatus'] == "F") {
					$testURL = "review.php?id=" . $id;
					$text = "Review Test";
				} else {
					$testURL = $URL . "action=finish";
					$text = "Proceed to Test";
				}
			} else {
				$testURL = $URL . "action=finish";
				$text = "Finish";
			}
			
			if (!exist($table, "position", $nextPage)) {
				if ($settings['reference'] == "0" && $accessArray[$id]['testStatus'] != "F") {
					$alert = " onclick=\"return confirm('This action will close and lock access to the lesson until you have completed the test. Continue?')\"";
				} else {
					$alert = false;
				}
				
				$navigation .= URL($text, $testURL, "nextPage", false, false, false, false, false, false, $alert);
			} elseif (!exist($table, "position", $nextPage) && $moduleData['test'] == "0") {				
				$navigation .= URL("Finish", $testURL, "nextPage");
			}
		}
		
		if ($preview == false) {
			$navigation .= "</div>";
			
			echo $navigation . "</div><p>&nbsp;</p>";
		}
		
	//Display the content		
		echo prepare($lesson['content'], false, true);
		echo "\n";
		
		if (!empty($lesson['attachment'])) {
			$siteInfo = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
			$file = $pluginRoot . "gateway.php/" . $id . "/lesson/" . $lesson['attachment'];
			$fileType = extension($file);
			
			echo "<br />\n";
			echo "<div align=\"center\">\n";
			
			switch ($fileType) {
			//If it is a PDF
				case "pdf" : 
					echo "<script type=\"text/javascript\">
  if (pluginlist.indexOf(\"Acrobat Reader\") != -1) {
    document.write(\"<embed src=\\\"" . $file . "#toolbar=0\\\" width=\\\"800\\\" height=\\\"500\\\">\");
  } else {
    document.write(\"<a href=\\\"http://get.adobe.com/reader/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/acrobat.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://get.adobe.com/reader/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Adobe&reg; Acrobat&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is a Word Document
				case "doc" : 
				case "docx" : 
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/word.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Click to download this file", $file, false, "_blank") . "\n";
					echo "</div>\n";
					echo "<br />\n<strong>You will need a document viewer which can open Microsoft&reg; Word&reg; documents</strong>\n";
					echo "<br />\n<strong>If you do not have such a viwer installed, you may download the above file, then view it using " . URL("this online service", "http://viewer.zoho.com/Upload.jsp", false, "_blank", false, false, false, false, false, "onclick=\"return confirm('You are about to be taken to the Zoho&reg; Corporation website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\"") . ".</strong>\n";
					break;
				
			//If it is a PowerPoint Presentation
				case "ppt" : 
				case "pptx" : 
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/presentation.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Click to download this file", $file, false, "_blank") . "\n";
					echo "</div>\n";
					echo "<br />\n<strong>You will need a presentation viewer which can open Microsoft&reg; PowerPoint&reg; presentations</strong>\n";
					echo "<br />\n<strong>If you do not have such a viwer installed, you may download the above file, then view it using " . URL("this online service", "http://viewer.zoho.com/Upload.jsp", false, "_blank", false, false, false, false, false, "onclick=\"return confirm('You are about to be taken to the Zoho&reg; Corporation website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\"") . ".</strong>\n";
					break;
				
			//If it is an Excel Spreadsheet
				case "xls" : 
				case "xlsx" : 
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/spreadsheet.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Click to download this file", $file, false, "_blank") . "\n";
					echo "</div>\n";
					echo "<br />\n<strong>You will need a spreadsheet viewer which can open Microsoft&reg; Excel&reg; spreadsheets</strong>\n";
					echo "<br />\n<strong>If you do not have such a viwer installed, you may download the above file, then view it using " . URL("this online service", "http://viewer.zoho.com/Upload.jsp", false, "_blank", false, false, false, false, false, "onclick=\"return confirm('You are about to be taken to the Zoho&reg; Corporation website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\"") . ".</strong>\n";
					break;
				
			//If it is a Standard Text Document
				case "txt" : 
				case "rtf" : 
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/text.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Click to download this file", $file, false, "_blank") . "\n";
					echo "</div>\n";
					echo "<br />\n<strong>You will need a text viewer which can open &quot;" . $fileType . "&quot; files</strong>\n";
					echo "<br />\n<strong>If you do not have such a viwer installed, you may download the above file, then view it using " . URL("this online service", "http://viewer.zoho.com/Upload.jsp", false, "_blank", false, false, false, false, false, "onclick=\"return confirm('You are about to be taken to the Zoho&reg; Corporation website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\"") . ".</strong>\n";
					break;
				
			//If it is a WAV audio file
				case "wav" : 
					echo "<script type=\"text/javascript\">
  if (pluginlist.indexOf(\"QuickTime\") != -1) {
	  document.write(\"<object width=\\\"640\\\" height=\\\"16\\\" classid=\\\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\\\" codebase=\\\"http://www.apple.com/qtactivex/qtplugin.cab\\\"><param name=\\\"src\\\" value=\\\"" . $file . "\\\"><param name=\\\"autoplay\\\" value=\\\"false\\\"><param name=\\\"controller\\\" value=\\\"true\\\"><embed src=\\\"" . $file . "\\\" width=\\\"640\\\" height=\\\"16\\\" autoplay=\\\"false\\\" controller=\\\"true\\\" pluginspage=\\\"http://www.apple.com/quicktime/download/\\\"></embed></object>\");
  } else {
	document.write(\"<a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/quicktime.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Apple&reg; QuickTime&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is an MP3 audio file
				case "mp3" : 
					echo "<script type=\"text/javascript\">
  if (pluginlist.indexOf(\"Flash\") != -1) {
	  document.write(\"<object id=\\\"player\\\" width=\\\"640\\\" height=\\\"30\\\" data=\\\"" . $pluginRoot . "system/flash/player.swf\\\" type=\\\"application/x-shockwave-flash\\\"><param name=\\\"movie\\\" value=\\\"" . $pluginRoot . "system/flash/player.swf\\\" /><param name=\\\"allowfullscreen\\\" value=\\\"false\\\" /><param name=\\\"flashvars\\\" value='config={\\\"clip\\\":{\\\"url\\\":\\\"" . $file . "\\\",\\\"autoPlay\\\":false},\\\"plugins\\\":{\\\"controls\\\":{\\\"autoHide\\\":false,\\\"fullscreen\\\":false}}}' /></object>\");
  } else {
	document.write(\"<a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/flash.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Adobe&reg; Flash&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is an AVI or WMV video file
				case "avi" : 
				case "wmv" : 
					echo "<script type=\"text/javascript\">
  if (pluginlist.indexOf(\"Windows Media Player\") != -1) {
    document.write(\"<object id=\\\"MediaPlayer\\\" width=\\\"640\\\" height=\\\"480\\\" classid=\\\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\\\" standby=\\\"Loading Windows Media Player components...\\\" type=\\\"application/x-oleobject\\\"><param name=\\\"FileName\\\" value=\\\"" . $file . "\\\"><param name=\\\"autostart\\\" value=\\\"false\\\"><param name=\\\"ShowControls\\\" value=\\\"true\\\"><param name=\\\"ShowStatusBar\\\" value=\\\"true\\\"><param name=\\\"ShowDisplay\\\" value=\\\"false\\\"><embed type=\\\"application/x-mplayer2\\\" src=\\\"" . $file . "\\\" name=\\\"MediaPlayer\\\"width=\\\"640\\\" height=\\\"480\\\" showcontrols=\\\"1\\\" showstatusBar=\\\"1\\\" showdisplay=\\\"0\\\" autostart=\\\"0\\\"></embed></object><br /><br /><strong>Having problems? <a href=\\\"" . $file . "?force=true\\\" target=\\\"_blank\\\">Try downloading the file</a>.</strong>\");
  } else {
    if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
      document.write(\"<a href=\\\"http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; Port25 website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/mediaplayer.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; Port25 website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Windows&reg; Media Player&reg; plugin to view this content.</a>\");
    } else if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
      document.write(\"<a href=\\\"http://windows.microsoft.com/en-US/windows/downloads/windows-media-player\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/mediaplayer.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://windows.microsoft.com/en-US/windows/downloads/windows-media-player\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Windows&reg; Media Player&reg; plugin to view this content.</a>\");
    } else {
      document.write(\"<img src=\\\"" . $pluginRoot . "system/images/programIcons/error.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /> The Windows&reg; Media Player&reg; plugin is not avaliable for your browser. Please use the most recent version of either <a href=\\\"http://www.getfirefox.net/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Mozilla&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">Mozilla&reg; Firefox&reg;</a> or <a href=\\\"http://www.microsoft.com/windows/internet-explorer/default.aspx\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">Microsoft&reg; Internet Explorer&reg;</a> in order to use this plugin.\");
    }
  }
</script>\n";
					break;
				
			//If it is an FLV or MP4 video file
				case "mp4" : 
				case "flv" : 
					echo "<script type=\"text/javascript\">
  if (pluginlist.indexOf(\"Flash\") != -1) {
	  document.write(\"<object id=\\\"player\\\" width=\\\"640\\\" height=\\\"480\\\" data=\\\"" . $root . "system/flash/player.swf\\\" type=\\\"application/x-shockwave-flash\\\"><param name=\\\"movie\\\" value=\\\"" . $pluginRoot . "system/flash/player.swf\\\" /><param name=\\\"allowfullscreen\\\" value=\\\"true\\\" /><param name=\\\"flashvars\\\" value='config={\\\"clip\\\":{\\\"url\\\":\\\"" . $file . "\\\",\\\"autoPlay\\\":false},\\\"plugins\\\":{\\\"controls\\\":{\\\"autoHide\\\":false}}}' /></object>\")
  } else {
	  document.write(\"<a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/flash.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Adobe&reg; Flash&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is an MOV video file
				case "mov" : 
					echo "<script type=\"text/javascript\">
  if (pluginlist.indexOf(\"QuickTime\") != -1) {
	  document.write(\"<object width=\\\"640\\\" height=\\\"480\\\" classid=\\\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\\\" codebase=\\\"http://www.apple.com/qtactivex/qtplugin.cab\\\"><param name=\\\"src\\\" value=\\\"" . $file . "\\\"><param name=\\\"autoplay\\\" value=\\\"false\\\"><param name=\\\"controller\\\" value=\\\"true\\\"><embed src=\\\"" . $file . "\\\" width=\\\"640\\\" height=\\\"480\\\" autoplay=\\\"false\\\" controller=\\\"true\\\" pluginspage=\\\"http://www.apple.com/quicktime/download/\\\"></embed></object>\");
  } else {
	document.write(\"<a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/quicktime.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Apple&reg; QuickTime&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is a SWF file
				case "swf" : 
					echo "<script type=\"text/javascript\">
  if (pluginlist.indexOf(\"Flash\") != -1) {
	  document.write(\"<object width=\\\"640\\\" height=\\\"480\\\" data=\\\"" . $file . "\\\" type=\\\"application/x-shockwave-flash\\\"><param name=\\\"src\\\" value=\\\"" . $file . "\\\" /><embed src=\\\"" . $file . "\\\" width=\\\"640\\\" height=\\\"480\\\"></embed></object>\")
  } else {
	  document.write(\"<a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/flash.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Adobe&reg; Flash&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//Display an error if an unsupported file was uploaded
				echo "<img src=\"" . $pluginRoot . "system/images/programIcons/error.png\" alt=\"icon\" style=\"vertical-align:middle;\" />\n This is an unsupported file format.\n";
				break;
			}
			
			echo "</div>\n";
		}
		
		if ($preview !== "miniPreview") {
			echo "<p>&nbsp;</p>\n" . $navigation;
		}
	}
	
//Test content
	function test($table, $fileURL, $preview = false) {
		global $connDBA, $testValues, $monitor, $userData, $pluginRoot;
		
		$attempt = lastItem($testTable, "testID", $testID, "attempt");
		
		if ($attempt - 1 == 0) {
			$currentAttempt = 1;
		} else {
			$currentAttempt = $attempt - 1;
		}
		
		echo form("test", "post", true);
		echo "<table width=\"100%\" class=\"dataTable\">\n";
		
		if ($preview == true) {
			if (is_numeric($preview)) {
				$additionalSQL = " WHERE `id` = '{$preview}'";
				$limit = " LIMIT 1";
			} else {
				$additionalSQL = "";
				$limit = "";
			}
		} else {
			$testID = str_replace("test_", "", $table);
			$selectionGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}'", "raw");
			$additionalSQLConstruct = " WHERE ";
			
			while ($selection = fetch($selectionGrabber)) {
				$additionalSQLConstruct .= "`id` = '{$selection['questionID']}' OR ";
			}
			
			$additionalSQL = rtrim($additionalSQLConstruct, " OR ") . " AND `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'";
			$limit = "";
		}
		
		if ($table != "questionbank_" . $userData['organization'] && $preview != false) {
			$order = " ORDER BY `position` ASC";
			$grab = "*";
			$join = "";
		} elseif (is_numeric($preview)) {
			$order = "";
			$grab = "*";
			$join = "";
		} else {
			$moduleInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$_GET['id']}'");
			
			if ($moduleInfo['randomizeAll'] == "Randomize") {
				$order = " ORDER BY `randomPosition` ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = $table . ".*, testdata_" . $userData['id'] . ".randomPosition, testdata_" . $userData['id'] . ".answerValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $table . ".id = testdata_" . $userData['id'] . ".questionID";
		}
		
		if (!is_numeric($preview)) {
			$settings = query("SELECT * FROM`learningunits` WHERE `id` = '{$testID}'");
		}
		
		$testDataGrabber = query("SELECT {$grab} FROM `{$table}`{$join}{$additionalSQL}{$order}{$limit}", "raw");
		$count = 1;
		$restrictImport = array();
		
	  	while ($testDataLoop = fetch($testDataGrabber)) {
			if ($preview == false) {
				$testValues = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}' AND `questionID` = '{$testDataLoop['id']}'");
			}
			
			if ($table != "questionbank_" . $userData['organization'] && $testDataLoop['questionBank'] == "1") {
				$testData = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$testDataLoop['linkID']}'");
			} else {
				$testData = $testDataLoop;
			}
			
			if (!is_numeric($preview) && isset($testData['link']) && exist($table, "id", $testData['link']) && $settings['randomizeAll'] == "Randomize" && !empty($testData['link']) && $testDataLoop['link'] != "0" && !in_array($testDataLoop['link'], $restrictImport)) {
				$importDescription = query("SELECT * FROM `{$table}` WHERE `id` = '{$testDataLoop['link']}'");
				
				if ($importDescription['questionBank'] == "1") {
					$importDescription = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$importDescription['linkID']}'");
				} else {
					$importDescription = $importDescription;
				}
				
				echo "<tr>\n<td colspan=\"2\" valign=\"top\">\n" . $importDescription['question'] . "\n</td>\n</tr>\n";
				array_push($restrictImport, $testDataLoop['link']);
			}
			
			if ($testData['type'] != "Description") {
				echo "<tr>\n<td width=\"100\" valign=\"top\">\n<p>";
				
				if (!is_numeric($preview)) {
					echo "<span class=\"questionNumber\">Question " . $count++ . "</span><br />\n";
				}
				
				echo "<span class=\"questionPoints\">" . $testData['points'] . " ";
				
				if ($testData['points'] == "1") {
					echo "Point";
				} else {
					echo "Points";
				}
				
				echo "</span>";
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br />\n<span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "</p>\n</td>\n<td valign=\"top\">\n" . $testData['question'];
				
				if ($testData['choiceType'] == "checkbox") {
					echo "\nThere may be more than one correct answer.)<br />";
				}
				
				echo "<br /><br />\n";
			}
			
			switch ($testData['type']) {
				case "Description" : 
					if (!in_array($testDataLoop['id'], $restrictImport)) {
						echo "<tr>\n<td colspan=\"2\" valign=\"top\">\n" . $testData['question'] . "\n</td>\n</tr>\n";
						array_push($restrictImport, $testDataLoop['id']);
					}
					
					break;
				case "Essay" : 
					if (isset($testValues)) {
						echo textArea($testDataLoop['id'], $testDataLoop['id'], "small", true, false, unserialize($testValues['userAnswer']));
					} else {
						echo textArea($testDataLoop['id'], $testDataLoop['id'], "small", true);
					}
						
					break;
					
				case "File Response" : 
					if ($testData['totalFiles'] > 1 || sizeof(unserialize($testValues['userAnswer'])) > 1) {
						if (isset($monitor)) {
							$URL = $monitor['gatewayPath'] . "/test/responses";
						} else {
							$URL = $pluginRoot . "gateway.php/" . $_GET['id'] . "/test/responses";
							$fillValue = unserialize($testValues['userAnswer']);
						}
						
						echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">\n";
						
						if (isset($testValues) && !empty($fillValue)) {
							$fileID = 1;
							
							foreach ($fillValue as $key => $file) {
								echo "<tr id=\"" . $fileID . "\">\n<td>";
								
								echo fileUpload($testDataLoop['id'] . "_" . $fileID, $testDataLoop['id'] . "_" . $fileID, false, true, false, $fillValue[$key], false, false, $URL, false, true);
								echo "</td>\n<td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=" . $fileID, "action smallDelete", false, false, false, false, false, false, " onclick=\"return confirm('This action will delete this file. Continue?')\"");
								echo "</td>\n</tr>\n";
								
								$fileID++;
							}
							
							unset($fileID);
							
							echo "</table\n><p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p>\n<p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>\n";
						} else {
							echo "<tr id=\"1\">\n<td>";
							echo fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, false, false, false, false, false, true);
							echo "</td>\n<td><span class=\"action smallDelete\" onclick=\"deleteObject('upload_" . $testDataLoop['id'] . "', '1', '1', true)\"></span>";
							echo "</td>\n</tr>\n</table>\n<p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p>\n";
						}
						
						echo "<p>Max file size (for single file): " . ini_get('upload_max_filesize') . "<br>Max file size (for all files): " . ini_get('post_max_size') . "</p>\n";
					} else {
						if (isset($testValues)) {
							$fillValue = unserialize($testValues['userAnswer']);
							
							if (!empty($fillValue)) {
								echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">\n";
								echo "<tr id=\"1\">\n<td>";
								echo fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, $fillValue['0'], false, false, "../gateway.php/modules/" . $_GET['id'] . "/test/responses", false, false);
								echo "</td>\n<td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=1", "action smallDelete", false, false, false, false, false, false, " return confirm('This action will delete this file. Continue?')");
								echo "</td>\n</tr>\n</table>\n";
								echo "<p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>\n";
							} else {
								echo fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
							}
						} else {
							echo fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
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
Create standard question types for the question generator
---------------------------------------------------------
*/
	
//Pull category or employee data for auto-suggestion
	if ((strstr($_SERVER['REQUEST_URI'], "wizard/lesson_settings.php") || strstr($_SERVER['REQUEST_URI'], "/questions/")) && isset($_GET['data']) && $_GET['data'] == "xml") {
		header("Content-type: text/xml");
		echo "<root>";
		
		$userData = userData();		
		$categoryBank = query("SELECT * FROM `modulecategories` ORDER BY `category` ASC", "raw");
		$priorEntries = query("SELECT * FROM `learningunits` WHERE `organization` = '{$userData['organization']}'", "raw");
		$noRepeat = array(array(), array());
		
		if (access("accessAllSuggestions")) {
			while($category = mysql_fetch_array($categoryBank)) {
				echo "<group>";
				
				if (!in_array(prepare($category['category'], false, true), $noRepeat['0'])) {
					echo "<category>" . prepare($category['category'], false, true) . "</category>";
				}
				
				echo "<employee></employee>";
				echo "</group>";
				
				array_push($noRepeat['0'], prepare($category['category'], false, true));
			}
		}
		
		while($suggestion = mysql_fetch_array($priorEntries)) {
			echo "<group>";
			
			if (!in_array(prepare($suggestion['category'], false, true), $noRepeat['0'])) {
				echo "<category>" . prepare($suggestion['category'], false, true) . "</category>";
			}
			
			if (!in_array(prepare($suggestion['employee'], false, true), $noRepeat['1'])) {
				echo "<employee>" . prepare($suggestion['employee'], false, true) . "</employee>";
			}
			
			echo "</group>";
			
			array_push($noRepeat['0'], prepare($suggestion['category'], false, true));
			array_push($noRepeat['1'], prepare($suggestion['employee'], false, true));
		}
		
		echo "</root>";
		exit;
	}
	
//Ensure the page is handling the correct question type
	function dataGrabber($type) {
		global $connDBA, $monitor;
		
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$dataGrabber = mysql_query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$id}'", $connDBA);
			
			if ($dataGrabber) {
				$data = mysql_fetch_array($dataGrabber);
				
				if ($data['type'] == $type) {
					return $data;
				} else {
					redirect($monitor['redirect']);
				}
			} else {
				redirect($monitor['redirect']);
			}
		}
		
		if (isset($_GET['bankID'])) {
			$id = $_GET['bankID'];
			$dataGrabber = mysql_query("SELECT * FROM `questionbank_0` WHERE `id` = '{$id}'", $connDBA);
			
			if ($dataGrabber) {
				$data = mysql_fetch_array($dataGrabber);
				
				if ($data['type'] == $type) {
					return $data;
				} else {
					redirect("../question_bank/index.php");
				}
			} else {
				redirect("../question_bank/index.php");
			}
		}
	}
	
//Display a question
	function question() {
		echo "<blockquote>\n";
		directions("Question", true);
		indent(textArea("question", "question", "small", true, false, false, "questionData", "question", " class=\"noEditorQuestion\""));
		echo "</blockquote>\n";
	}
	
//Display the point value
	function points() {
		directions("Question points", true);
		indent(textField("points", "points", "5", "5", false, true, ",custom[onlyNumber]", false, "questionData", "points") . 
		"&nbsp;" . 
		checkbox("extraCredit", "extraCredit", "Extra Credit", false, false, false, false, "questionData", "extraCredit", "on"));
	}
	
//Include where this question is being inserted
	function type() {
		global $questionData;
		
		$active = 0;
		$valuesPrep = "";
		$valueIDsPrep = "";
		
		if (isset($_SESSION['currentUnit'])) {
			$active = 1;
			$valuesPrep .= "Current Test,";
			$valueIDsPrep .= "Learning Unit,";
		}
		
		if (isset($_SESSION['questionBank'])) {
			$active = $active + 1;
			$valuesPrep .= "Question Bank,";
			$valueIDsPrep .= "Bank,";
		}
		
		if (isset($_SESSION['feedback'])) {
			$active = $active + 1;
			$valuesPrep .= "Feedback,";
			$valueIDsPrep .= "Feedback,";
		}
		
		$values = rtrim($valuesPrep, ",");
		$valueIDs = rtrim($valueIDsPrep, ",");
		
		if ($active > 1 && !isset($_GET['id']) && !isset($_GET['bankID']) && !isset($_GET['feedbackID'])) {
			directions("Insert question into");
			echo "<blockquote><p>";
			dropDown("type", "type", $values, $valueIDs, false, false, false, false, false, false, " onchange=\"toggleDescription(this.value);\"");
			echo "</p></blockquote>";
		} else {
			if (isset($questionData)) {
				if (!array_key_exists("position", $questionData)) {
					$valueIDs = "Bank";
				} elseif (!array_key_exists("link", $questionData)) {
					$valueIDs = "Feedback";
				} else {
					$valueIDs = "Learning Unit";
				}
			}
			
			echo hidden("type", "type", $valueIDs);
		}
	}
	
//Display all levels of difficulty
	function difficulty() {
		global $monitor;
		
		if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
			if (isset($_SESSION['currentModule'])) {
				$difficulty = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$_SESSION['currentModule']}'");
				directions("Difficulty", false);
				echo "<blockquote><p>";
				dropDown("difficulty", "difficulty", "Easy,Average,Difficult", "Easy,Average,Difficult", false, false, false, $difficulty['difficulty'], "questionData", "difficulty");
				echo "</p></blockquote>";
			} else {
				directions("Difficulty", false);
				echo "<blockquote><p>";
				dropDown("difficulty", "difficulty", "Easy,Average,Difficult", "Easy,Average,Difficult", false, false, false, "Average", "questionData", "difficulty");
				echo "</p></blockquote>";
			}
		} else {
			directions("Difficulty", false, "The overall difficulty of this module");
			echo "<blockquote><p>";
			dropDown("difficulty", "difficulty", "Easy,Average,Difficult", "Easy,Average,Difficult", false, false, false, "Average", "moduleData", "difficulty");
			echo "</p></blockquote>";
		}
	}
	
//Display all of the descriptions in this test
	function descriptionLink() {
		global $connDBA, $monitor;
		
		if (isset($_SESSION['currentModule']) && !isset($_GET['bankID']) && !isset($_GET['feedbackID'])) {
			echo "<div id=\"descriptionLink\">";
			
			if (exist($monitor['testTable'], "type", "Description")) {
				$descriptionGrabber = query("SELECT * FROM `{$monitor['testTable']}` WHERE `type` = 'Description' ORDER BY `position` ASC", "raw");
				$descriptionID = ",";			
				$descriptionName = "- Select -,";
				
				while ($description = mysql_fetch_array($descriptionGrabber)) {
					if ($description['type'] == "Description" && $description['questionBank'] != "1") {
						$descriptionID .= $description['id'] . ",";
						$descriptionName .= $description['position'] . ". " . commentTrim(25, $description['question']) . ",";
					}
					
					if ($description['questionBank'] == "1") {
						$importID = $description['linkID'];
						$descriptionImportGrabber = mysql_query("SELECT * FROM `questionbank_0` WHERE `id` = '{$importID}'", $connDBA);
						$descriptionImport = mysql_fetch_array($descriptionImportGrabber);
						
						if ($descriptionImport['type'] == "Description") {
							$descriptionID .= $description['id'] . ",";
							$descriptionName .= $description['position'] . ". " . commentTrim(25, $descriptionImport['question']) . ",";
						}
						
						unset($importID);
						unset($descriptionImportGrabber);
						unset($descriptionImport);
					}
				}
				
				$IDs = rtrim($descriptionID, ",");
				$values = rtrim($descriptionName, ",");
			} else {
				$IDs = "";			
				$values = "- None -";
			}
			
			directions("Link to description", false);
			echo "<blockquote><p>";
			dropDown("link", "link", $values, $IDs, false, false, false, false, "questionData", "link");
			echo "</p></blockquote></div>";
		} else {
			hidden("link", "link", "");
		}
	}
	
//Display partial credit option
	function partialCredit() {
		directions("Allow partial credit");
		echo "<blockquote><p>";
		radioButton("partialCredit", "partialCredit", "Yes,No", "1,0", true, false, false, "0", "questionData", "partialCredit", " onchange=\"toggleFeedback(this.value)\"");
		echo "</p></blockquote>";
	}
	
//Display a case sensitivity option
	function ignoreCase() {
		directions("Ignore case");
		echo "<blockquote><p>";
		radioButton("case", "case", "Yes,No", "1,0", true, false, false, "1", "questionData", "case");
		echo "</p></blockquote>";
	}
	
//Display a randomize option
	function randomize() {
		directions("Randomize values");
		echo "<blockquote><p>";
		radioButton("randomize", "randomize", "Yes,No", "1,0", true, false, false, "0", "questionData", "randomize");
		echo "</p></blockquote>";
	}
	
//Display search tags
	function tags() {
		directions("Tags (Seperate with commas)", false);
		echo "<blockquote><p>";
		textField("tags", "tags", false, false, false, false, false, false, "questionData", "tags");
		echo "</p></blockquote>";
	}
	
//Display all of the category items
	function category() {
		global $monitor;
		
		if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
			if (isset($_SESSION['currentModule']) && isset($_SESSION['questionBank'])) {
				$category = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$_SESSION['currentModule']}'");
			} elseif (isset($_SESSION['currentModule'])) {
				$category = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$_SESSION['currentModule']}'");
			} elseif (isset($_SESSION['questionBank'])) {
				$category = query("SELECT * FROM `modulecategories` WHERE `id` = '{$_SESSION['questionBank']}'");
			}
			
			directions("Category", true);
			echo "<blockquote><p><div id=\"categoryMenu\">";
			echo textField("category", "category", false, false, false, true, false, $category['category'], "questionData", "category");
			echo "<div><div id=\"categorySuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{category}\">{category}</div></div></div></div></p></blockquote>";
		} else {
			echo "<div id=\"categoryMenu\">";
			textField("category", "category", false, false, false, true, false, false, "moduleData", "category");
			echo "<div><div id=\"categorySuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{category}\">{category}</div></div></div></div>";
		}
		
		echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"categoryMenu\", \"categorySuggestions\", \"data\", \"category\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
	}
	
//Display all of the employee types
	function employeeTypes() {
		echo "<div id=\"employeeMenu\">";
		textField("employee", "employee", false, false, false, true, false, false, "moduleData", "employee");
		echo "<div><div id=\"employeeSuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{employee}\">{employee}</div></div></div></div>";
		
		echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"employeeMenu\", \"employeeSuggestions\", \"data\", \"employee\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
	}
	
//Display the feedback
	function feedback($hidePartial = false) {
		global $connDBA, $questionData;
		
		echo "<blockquote>\n";
		directions("Feedback for correct answer");
		indent(textArea("feedBackCorrect", "feedBackCorrect", "small", false, false, false, "questionData", "correctFeedback"));
		
		if ($hidePartial == true) {
			if (isset($questionData)) {
				if ($questionData['partialCredit'] == "1") {
					$class = "contentShow";
				} else {
					$class = "contentHide";
				}
			} else {
				$class = "contentHide";
			}
			
			echo "<div class=\"" . $class . "\" id=\"toggleFeedback\">\n";
			directions("Feedback for partially correct answer");
			indent(textArea("feedBackPartial", "feedBackPartial", "small", false, false, false, "questionData", "partialFeedback"));
			echo "</div>\n";
		} else {
			directions("Feedback for partially correct answer");
			indent(textArea("feedBackPartial", "feedBackPartial", "small", false, false, false, "questionData", "partialFeedback"));
		}
		
		directions("Feedback for incorrect answer");
		indent(textArea("feedBackIncorrect", "feedBackIncorrect", "small", false, false, false, "questionData", "incorrectFeedback"));
		echo "</blockquote>\n";
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
	
//TinyMCE small media
	function tinyMCEMedia () {
		global $root, $pluginRoot;
		
		return "<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/tiny_mce.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/AtD/editor_plugin.js\"></script>
<script type=\"text/javascript\" src=\"" . $pluginRoot . "system/javascripts/tiny_mce_media.php\"></script>";
	}
	
//TinyMCE question, purposefully excludes the tiny_mce.js and editor_plugin.js scripts
	function tinyMCEQuestion () {
		global $root, $pluginRoot;
		
		return "<script type=\"text/javascript\" src=\"" . $pluginRoot . "system/javascripts/tiny_mce_question.php\"></script>";
	}
?>