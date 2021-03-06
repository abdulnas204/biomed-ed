<?php
/*
LICENSE: See "license.php" located at the root installation

This script contains additional functions relevent to this plugin only.
*/

/*
Global server-side declarations
---------------------------------------------------------
*/

	$pluginRootPrep = str_replace($root, "", $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	$pluginRootArray = explode("/", $pluginRootPrep);
	$pluginRoot = $root . $pluginRootArray['0'] . "/";
	

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
		
		$targetFile = stripslashes($targetFile);
		
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
		global $strippedRoot;
		
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
		
		if (strstr($_SERVER['SCRIPT_NAME'], "/wizard/lesson_settings.php") || strstr($_SERVER['SCRIPT_NAME'], "/questions/")) {
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
			$directory = "../../data/learn/unit_" . $_SESSION['currentUnit'] . "/";
			$gatewayPath = "../preview.php/unit_" . $_SESSION['currentUnit'] . "/";
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
			$directory = "../../data/learn/unit_" . $id . "/";
			
			$monitor = array("parentTable" => $parentTable, "title" => $titlePrefix, "directory" => $directory);
		}
		
		$pageFilePrep = explode("/", $_SERVER['SCRIPT_NAME']);
		$pageFile = end($pageFilePrep);
		
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
		global $monitor;
		
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
		
		echo "<div id=\"menu\">\n<ul class=\"level1\">\n<li class=\"level1-li\">\n<a class=\"level1-a fly\" name=\"navigation\">Navigation<!--[if gte IE 7]><!--></a><!--<![endif]-->\n<!--[if lte IE 6]><table><tr><td><![endif]-->\n<ul class=\"level2\">\n";
		
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
		
		echo "</ul>\n<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n</li>\n</ul>\n</div>\n</div>\n</div>\n";
	}
	
//Regulate the how questions are inserted and updated
	function insertQuery($type, $unitQuery, $bankQuery = false, $feedbackQuery = false) {
		global $monitor, $userData;
		
		switch ($type) {
			case "Learning Unit" :
				query("INSERT INTO `{$monitor['testTable']}` (
						  `id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (
						  {$unitQuery}
					  )");
					  
				$id = mysql_insert_id();
					  
				processFields("Question Generator", $monitor['testTable'], $id);	  
				redirect($monitor['redirect'] . "?inserted=question");
				break;
							
			case "Bank" :
				query("INSERT INTO `questionbank_{$userData['organization']}` (
						  `id`, `type`, `points`, `extraCredit`, `partialCredit`, `category`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (							
						  {$bankQuery}
					  )");
				
				$id = mysql_insert_id();
				$category = prepare($_POST['category'], false, true);
				$categoryID = query("SELECT * FROM `categories` WHERE `category` = '{$category}'");
				
				processFields("Question Generator", "questionbank_" . $userData['organization'], $id);
				redirect("../question_bank/index.php?id=" . $categoryID['id'] . "&inserted=question");
				break;
		}
	}
	
	function updateQuery($type, $unitQuery, $bankQuery = false, $feedbackQuery = false) {
		global $monitor, $userData;
		
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
				processFields("Question Generator", $monitor['testTable'], $update);
				redirect($monitor['redirect'] . "?updated=question");
				break;
				
			case "Bank" :
				query("UPDATE `questionbank_{$userData['organization']}` SET {$bankQuery} WHERE `id` = '{$update}'");
				
				$category = prepare($_POST['category']);
				$categoryID = query("SELECT * FROM `categories` WHERE `category` = '{$category}'");
				
				processFields("Question Generator", "questionbank_" . $userData['organization'], $update);
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
		global $monitor, $root, $pluginRoot, $userData, $protocol;
		
		if ($preview == false) {
			$URL = $_SERVER['PHP_SELF'] . "?id=" . $id . "&";
		} else {
			$URL = $_SERVER['PHP_SELF'] . "?";
		}
		
	//Grab all of the lesson content and settings
		$settings = query("SELECT * FROM `learningunits` WHERE `id` = '{$id}'");
		$accessArray = arrayRevert($userData['learningunits']);
	
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
			
			echo "\n<div class=\"layoutControl\">\n<div class=\"contentLeft\">";
			
			title($lesson['title'], false, false);
			
			echo "</div>\n<div class=\"dataRight\" style=\"padding-top:15px;\">\n";
			
		//Drop-down menu displaying all pages
			if ($preview == false) {
				$pagesGrabber = query("SELECT * FROM `{$table}` ORDER BY `position` ASC", "raw");
				$pagesCount = query("SELECT * FROM `{$table}` ORDER BY `position` ASC", "num");
				$count = 1;
				
				echo "<div id=\"menu\">\n<ul class=\"level1\">\n<li class=\"level1-li\">\n<a class=\"level1-a fly\" name=\"navigation\">Navigation<!--[if gte IE 7]><!--></a><!--<![endif]-->\n<!--[if lte IE 6]><table><tr><td><![endif]-->\n<ul class=\"level2\">\n";
				
				while($pages = fetch($pagesGrabber)) {
					if ($pages['position'] == $pagesCount) {
						$class = "endlist";
					} else {
						$class = "";
					}
					
					if ($_GET['page'] != $pages['position']) {
						echo "<li>" . URL($count . ". " . $pages['title'], $URL . "page=" . $pages['position'], $class) . "</li>\n";
					} else {
						echo "<li><a style=\"color:#0000FF; font-weight:bolder; cursor:default;\"" . $class . ">" . $count . ". " . $pages['title'] . "</a></li>\n";
					}
					
					$count++;
				}
				
				echo "</ul>\n<!--[if lte IE 6]></td></tr></table></a><![endif]-->\n</li>\n</ul>\n</div>\n";
			}
			
			$navigation = "<div class=\"toolBar noPadding\"><div align=\"center\">\n";
			
			if (exist($table, "position", $previousPage)) {
				$navigation .= URL("Previous Step", $URL . "page=" . $previousPage , "previousPage");
				
				if (exist($table, "position", $nextPage) || ($settings['test'] == "1" && exist(str_replace("lesson", "test", $table)) && !isset($_SESSION['currentUnit']))) {
					$navigation .= " | ";
				}
			}
			
			if (exist($table, "position", $nextPage)) {
				$navigation .= URL("Next Step", $URL . "page=" . $nextPage , "nextPage");
			}
		} else {
			$navigation = "";
		}
		
	//Link to the test, if it exists and the user is assigned to this learning unit		
		if ($preview == false) {
			if (is_array($accessArray) && array_key_exists($id, $accessArray)) {
				if (exist("test_" . $id)) {
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
			} else {
				$testURL = $URL . "action=finish";
				$text = "Finish";
			}
			
			$pageName = explode("/", $_SERVER['SCRIPT_NAME']);
			
			if (end($pageName) != "lesson_verify.php") {
				if (!exist($table, "position", $nextPage)) {
					if ($settings['reference'] == "0" && $accessArray[$id]['testStatus'] != "F" && exist("test_" . $id)) {
						$alert = " onclick=\"return confirm('This action will close and lock access to the lesson until you have completed the test. Continue?')\"";
					} else {
						$alert = false;
					}
					
					$navigation .= URL($text, $testURL, "nextPage", false, false, false, false, false, false, $alert);
				} elseif (!exist($table, "position", $nextPage) && $settings['test'] == "0") {				
					$navigation .= URL("Finish", $testURL, "nextPage");
				}
			}
		}
		
		if ($preview == false) {
			$navigation .= "\n</div>\n</div>\n";
			
			echo "</div>\n</div>\n<p>&nbsp;</p>\n" . $navigation;
		}
		
	//Display the content	
		echo prepare($lesson['content']);
		echo "\n";
		
	//A private function which will be included inside of all documents which are previewed externally
		function browserDetect() {
			echo "<script type=\"text/javascript\">
//Detect the versions of Internet Explorer
  if (navigator.userAgent.indexOf('MSIE')!= -1) {
    var fullVersion = navigator.userAgent.substring(navigator.userAgent.indexOf('MSIE') + 5);
	
    if (fullVersion.indexOf(';') != -1) {
      fullVersion = fullVersion.substring(0, fullVersion.indexOf(';'));
    }
    
    if (fullVersion.indexOf(' ') != -1) {
      fullVersion = fullVersion.substring(0, fullVersion.indexOf(' '));
    }
    
    var majorVersion = parseInt('' + fullVersion, 10);
    
    if (isNaN(majorVersion)) {
      fullVersion = '' + parseFloat(navigator.appVersion); 
      majorVersion = parseInt(navigator.appVersion, 10);
    }
    
  //Run this only on Internet Explorer versions 6, 7, and 8
    if (majorVersion == '6' || majorVersion == '7' || majorVersion == '8') {
      var show = true;
      var cookies = document.cookie.split(';');
      var item;
	  
      for(var count = 0; count <= cookies.length - 1; count++) {
        if (cookies[count].indexOf(' ') != -1) {
          item = cookies[count].substring(1);
        } else {
          item = cookies[count];
        }
        
        item = item.split('=');
		
        if (item[0] == 'hideDirections') {
          show = false;
        }
      }
	  
    //Show this alert, only if it is not set to be hidden
      if (show == true) {
        document.write('<div class=\"IEAlert\">The browser you are using may have difficulties displaying the pages in document previewer below. Click <a id=\"modal\">here</a> if you are experiencing troubles. <a id=\"close\">[Close]</a></div><br />');
      }
      
      $(document).ready(function() {
      //Set a cookie to hide the IEAlert <div>
        $('div.IEAlert a#close').click(function() {
          $(this).parent().remove();
          
          var expire = new Date();
          expire.setDate(expire.getDate() + 6000);
          var value = 'true; expires=' + expire.toUTCString();
          document.cookie = 'hideDirections=' + value;
        });
        
      //Generate the help HTML
        var HTML = '<p>On occasions, the pages within the doucment previewer will fail to load. To ensure this problem does not continue, follow these steps:</p><ol><li>Navigate to your browser's &quot;Tools&quot; menu, and select &quot;Internet Options&quot;:<br />';
        
        if (majorVersion == \"6\") {
          HTML += '<img src=\"system/images/help/internet_explorer_6/step_1.jpg\" width=\"628\" height=\"342\" /><br />';
        } else {
          HTML += '<img src=\"system/images/help/internet_explorer_7/step_1.jpg\" width=\"624\" height=\"344\" /><br />';
        }
        
        HTML += '<br /></li><li>Click on the &quot;Privacy&quot; tab:<br />';
        
        if (majorVersion == \"6\") {
          HTML += '<img src=\"system/images/help/internet_explorer_6/step_2.jpg\" width=\"406\" height=\"452\" /><br />';
        } else {
          HTML += '<img src=\"system/images/help/internet_explorer_7/step_2.jpg\" width=\"413\" height=\"519\" /><br />';
        }
        
        HTML += '<br /></li><li>';
        
        if (majorVersion == \"6\") {
          HTML += 'Click on the &quot;Edit&quot; button:<br /><img src=\"system/images/help/internet_explorer_6/step_3.jpg\" width=\"406\" height=\"452\" /><br />';
        } else {
          HTML += 'Click on the &quot;Sites&quot; button:<br /><img src=\"system/images/help/internet_explorer_7/step_3.jpg\" width=\"413\" height=\"519\" /><br />';
        }
        
        HTML += '<br /></li><li>Type &quot;google.com&quot; into the text field as shown in the screenshot below:<br />';
        
        if (majorVersion == \"6\") {
          HTML += '<img src=\"system/images/help/internet_explorer_6/step_4.jpg\" width=\"441\" height=\"461\" /><br />';
        } else {
          HTML += '<img src=\"system/images/help/internet_explorer_7/step_4.jpg\" width=\"441\" height=\"468\" /><br />';
        }
        
        HTML += '<br /></li><li>Click the &quot;Allow&quot; button:<br />';
        
        if (majorVersion == \"6\") {
          HTML += '<img src=\"system/images/help/internet_explorer_6/step_5.jpg\" width=\"441\" height=\"461\" /><br />';
        } else {
          HTML += '<img src=\"system/images/help/internet_explorer_7/step_5.jpg\" width=\"441\" height=\"468\" /><br />';
        }
        
        HTML += '<br /></li><li>Click &quot;OK&quot;:<br />';
        
        if (majorVersion == \"6\") {
          HTML += '<img src=\"system/images/help/internet_explorer_6/step_6.jpg\" width=\"441\" height=\"461\" /><br />';
        } else {
          HTML += '<img src=\"system/images/help/internet_explorer_7/step_6.jpg\" width=\"441\" height=\"468\" /><br />';
        }
        
        HTML += '<br /></li><li>Click &quot;OK&quot;, on the main dialog window:<br />';
        
        if (majorVersion == \"6\") {
          HTML += '<img src=\"system/images/help/internet_explorer_6/step_7.jpg\" width=\"406\" height=\"452\" /><br />';
        } else {
          HTML += '<img src=\"system/images/help/internet_explorer_7/step_7.jpg\" width=\"413\" height=\"519\" /><br />';
        }
        
        HTML += '<br /></li><li>Refresh the web page to see the changes.</li></ol>';
      
      //Use jQuery to create a dialog to help the user with the problem described in the dynamically created <div> above
        $('div.IEAlert a#modal').click(function() {
          $('<div title=\"Document Previewer Help\"></div>').load('help.htm', function() {
            $(this).dialog({
              'height' : 400,
              'width' : 700,
              'modal' : true,
              'resizable' : false,
              'onclose' : function() {
                 $(this).dialog('close').remove();
              }
            });
          });
       });
     });
    }
  }
</script>\n";
		}
		
		if (!empty($lesson['attachment'])) {
			$siteInfo = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
			$file = $pluginRoot . "gateway.php/unit_" . $id . "/lesson/" . $lesson['attachment'];
			$documentURL = $pluginRoot . "preview.php/unit_" . $id . "/lesson/" . $lesson['attachment'];
			$fileType = extension($file);
			
			echo "<br />\n";
			echo "<div align=\"center\">\n";
			
			switch ($fileType) {
			//If it is a PDF
				case "pdf" : 
					browserDetect();
					
					echo "<div align=\"center\">\n<iframe src=\"" . $documentURL . "\" frameborder=\"0\" width=\"900\" height=\"600\"></iframe>\n</div>\n";
					echo "<br />\n<br />\n";
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/acrobat.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Having problems with the previewer? Download this file.", $file, false, "_blank") . "\n";
					echo "</div>\n";
					break;
				
			//If it is a Word Document
				case "doc" : 
				case "docx" : 
					browserDetect();
					
					echo "<div align=\"center\">\n<iframe src=\"" . $documentURL . "\" frameborder=\"0\" width=\"900\" height=\"600\"></iframe>\n</div>\n";
					echo "<br />\n<br />\n";
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/word.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Having problems with the previewer? Download this file.", $file, false, "_blank") . "\n";
					echo "</div>\n";
					break;
				
			//If it is a PowerPoint Presentation
				case "ppt" : 
				case "pptx" : 
					browserDetect();
					
					echo "<div align=\"center\">\n<iframe src=\"" . $documentURL . "\" frameborder=\"0\" width=\"900\" height=\"600\"></iframe>\n</div>\n";
					echo "<br />\n<br />\n";
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/presentation.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Having problems with the previewer? Download this file.", $file, false, "_blank") . "\n";
					echo "</div>\n";
					break;
				
			//If it is an Excel Spreadsheet
				case "xls" : 
				case "xlsx" : 
					browserDetect();
					
					echo "<div align=\"center\">\n<iframe src=\"" . $documentURL . "\" frameborder=\"0\" width=\"900\" height=\"600\"></iframe>\n</div>\n";
					echo "<br />\n<br />\n";
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/spreadsheet.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Having problems with the previewer? Download this file.", $file, false, "_blank") . "\n";
					echo "</div>\n";
					break;
				
			//If it is a Standard Text Document
				case "txt" : 
				case "rtf" : 
					echo "<div>\n";
					echo URL("<img src=\"" . $pluginRoot . "system/images/programIcons/text.png\" alt=\"icon\" style=\"vertical-align:middle;\" />", $file, false, "_blank") . "\n";
					echo URL("Click to dowload the text document.", $file, false, "_blank") . "\n";
					echo "<p><strong>You will need a viewer installed on your computer which can open &quot;" . strtoupper($fileType) . "&quot; documents.</strong></p>\n";
					echo "</div>\n";
					break;
				
			//If it is a WAV audio file
				case "wav" : 
					echo "<script type=\"text/javascript\">
  if (quicktime.installed) {
	  document.write(\"<object width=\\\"640\\\" height=\\\"16\\\" classid=\\\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\\\" codebase=\\\"http://www.apple.com/qtactivex/qtplugin.cab\\\"><param name=\\\"src\\\" value=\\\"" . $file . "\\\"><param name=\\\"autoplay\\\" value=\\\"false\\\"><param name=\\\"controller\\\" value=\\\"true\\\"><embed src=\\\"" . $file . "\\\" width=\\\"640\\\" height=\\\"16\\\" autoplay=\\\"false\\\" controller=\\\"true\\\" pluginspage=\\\"http://www.apple.com/quicktime/download/\\\"></embed></object>\");
  } else {
	document.write(\"<a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/quicktime.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Apple&reg; QuickTime&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is an MP3 audio file
				case "mp3" : 
					echo "<script type=\"text/javascript\">
  if (flash.installed) {
	  document.write(\"<object id=\\\"player\\\" width=\\\"640\\\" height=\\\"30\\\" data=\\\"" . $pluginRoot . "system/flash/player.swf\\\" type=\\\"application/x-shockwave-flash\\\"><param name=\\\"movie\\\" value=\\\"" . $pluginRoot . "system/flash/player.swf\\\" /><param name=\\\"allowfullscreen\\\" value=\\\"false\\\" /><param name=\\\"flashvars\\\" value='config={\\\"clip\\\":{\\\"url\\\":\\\"" . $file . "\\\",\\\"autoPlay\\\":false},\\\"plugins\\\":{\\\"controls\\\":{\\\"autoHide\\\":false,\\\"fullscreen\\\":false}}}' /><param name=\\\"wmode\\\" value=\\\"transparent\\\"></object>\");
  } else {
	document.write(\"<a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/flash.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Adobe&reg; Flash&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is an AVI or WMV video file
				case "avi" : 
				case "wmv" : 
					echo "<script type=\"text/javascript\">
  if (windowsmedia.installed) {
    document.write(\"<object id=\\\"MediaPlayer\\\" width=\\\"640\\\" height=\\\"480\\\" classid=\\\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\\\" standby=\\\"Loading Windows Media Player components...\\\" type=\\\"application/x-oleobject\\\"><param name=\\\"FileName\\\" value=\\\"" . $file . "\\\"><param name=\\\"autostart\\\" value=\\\"false\\\"><param name=\\\"ShowControls\\\" value=\\\"true\\\"><param name=\\\"ShowStatusBar\\\" value=\\\"true\\\"><param name=\\\"ShowDisplay\\\" value=\\\"false\\\"><param name=\\\"wmode\\\" value=\\\"transparent\\\"><embed type=\\\"application/x-mplayer2\\\" src=\\\"" . $file . "\\\" name=\\\"MediaPlayer\\\"width=\\\"640\\\" height=\\\"480\\\" showcontrols=\\\"1\\\" showstatusBar=\\\"1\\\" showdisplay=\\\"0\\\" wmode=\\\"transparent\\\" autostart=\\\"0\\\"></embed></object><br /><br /><strong>Having problems? <a href=\\\"" . $file . "?force=true\\\" target=\\\"_blank\\\">Try downloading the file</a>.</strong>\");
  } else {
    if (is_fx || is_moz || is_chrome || is_opera || (is_safari && navigator.appVersion.indexOf(\"Win\") != -1)) {
      document.write(\"<a href=\\\"http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; Port25 website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/mediaplayer.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://port25.technet.com/pages/windows-media-player-firefox-plugin-download.aspx\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; Port25 website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Windows&reg; Media Player&reg; plugin to view this content.</a>\");
    } else if (is_ie) {
      document.write(\"<a href=\\\"http://windows.microsoft.com/en-US/windows/downloads/windows-media-player\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/mediaplayer.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://windows.microsoft.com/en-US/windows/downloads/windows-media-player\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Microsoft&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Windows&reg; Media Player&reg; plugin to view this content.</a>\");
    } else if (is_safari && navigator.appVersion.indexOf(\"Mac\") != -1) {
      document.write(\"<a href=\\\"http://www.apple.com/downloads/macosx/video/windowsmediaplayerformacosx.html\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/mediaplayer.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://www.apple.com/downloads/macosx/video/windowsmediaplayerformacosx.html\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Windows&reg; Media Player&reg; plugin to view this content.</a>\");
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
  if (flash.installed) {
	  document.write(\"<object id=\\\"player\\\" width=\\\"640\\\" height=\\\"480\\\" data=\\\"" . $root . "system/flash/player.swf\\\" type=\\\"application/x-shockwave-flash\\\"><param name=\\\"movie\\\" value=\\\"" . $pluginRoot . "system/flash/player.swf\\\" /><param name=\\\"allowfullscreen\\\" value=\\\"true\\\" /><param name=\\\"flashvars\\\" value='config={\\\"clip\\\":{\\\"url\\\":\\\"" . $file . "\\\",\\\"autoPlay\\\":false},\\\"plugins\\\":{\\\"controls\\\":{\\\"autoHide\\\":false}}}' /><param name=\\\"wmode\\\" value=\\\"transparent\\\"></object>\")
  } else {
	  document.write(\"<a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/flash.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://get.adobe.com/flashplayer/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Adobe&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Adobe&reg; Flash&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is an MOV video file
				case "mov" : 
					echo "<script type=\"text/javascript\">
  if (quicktime.installed) {
	  document.write(\"<object width=\\\"640\\\" height=\\\"480\\\" classid=\\\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\\\" codebase=\\\"http://www.apple.com/qtactivex/qtplugin.cab\\\"><param name=\\\"src\\\" value=\\\"" . $file . "\\\"><param name=\\\"autoplay\\\" value=\\\"false\\\"><param name=\\\"controller\\\" value=\\\"true\\\"><embed src=\\\"" . $file . "\\\" width=\\\"640\\\" height=\\\"480\\\" autoplay=\\\"false\\\" controller=\\\"true\\\" pluginspage=\\\"http://www.apple.com/quicktime/download/\\\"></embed></object>\");
  } else {
	document.write(\"<a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\"><img src=\\\"" . $pluginRoot . "system/images/programIcons/quicktime.png\\\" alt=\\\"icon\\\" style=\\\"vertical-align:middle;\\\" /></a> <a href=\\\"http://www.apple.com/quicktime/download/\\\" target=\\\"_blank\\\" onclick=\\\"return confirm('You are about to be taken to the Apple&reg; website, which is a trusted source, but is not controlled by " . $siteInfo['siteName'] . ". Click &quot;OK&quot; to continue.')\\\">You need to download the Apple&reg; QuickTime&reg; plugin to view this content.</a>\");
  }
</script>\n";
					break;
				
			//If it is a SWF file
				case "swf" : 
					echo "<script type=\"text/javascript\">
  if (flash.installed) {
	  document.write(\"<object width=\\\"640\\\" height=\\\"480\\\" data=\\\"" . $file . "\\\" type=\\\"application/x-shockwave-flash\\\"><param name=\\\"src\\\" value=\\\"" . $file . "\\\" /><embed src=\\\"" . $file . "\\\" width=\\\"640\\\" height=\\\"480\\\"></embed><param name=\\\"wmode\\\" value=\\\"transparent\\\"></object>\")
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
		global $testValues, $monitor, $userData, $pluginRoot;
		
		$id = strip($table, "numbersOnly");
		$settings = query("SELECT * FROM `learningunits` WHERE `id` = '{$id}'", false, false);
		
		if ($preview == false) {
			if ($attempt = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$id}' ORDER BY `attempt` DESC LIMIT 1")) {
				$currentAttempt = $attempt['attempt'];
			} else {
				$currentAttempt = 1;
			}
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
			$selectionGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$id}' AND `attempt` = '{$currentAttempt}'", "raw");
			$additionalSQLConstruct = " WHERE ";
			
			while ($selection = fetch($selectionGrabber)) {
				$additionalSQLConstruct .= "(`id` = '{$selection['questionID']}' AND `attempt` = '{$currentAttempt}') OR ";
			}
			
			$additionalSQL = rtrim($additionalSQLConstruct, " OR ") . " AND `testID` = '{$id}'";
			$limit = "";
		}
		
		if ($table != "questionbank_" . $userData['organization'] && $preview != false) {
			if ($settings['randomizeAll'] == "Randomize") {
				$order = " ORDER BY RAND() ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = "*";
			$join = "";
		} elseif (is_numeric($preview)) {
			$order = "";
			$grab = "*";
			$join = "";
		} else {			
			if ($settings['randomizeAll'] == "Randomize") {
				$order = " ORDER BY `randomPosition` ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = $table . ".*, testdata_" . $userData['id'] . ".randomPosition, testdata_" . $userData['id'] . ".answerValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $table . ".id = testdata_" . $userData['id'] . ".questionID";
		}
		
		$testDataGrabber = query("SELECT {$grab} FROM `{$table}`{$join}{$additionalSQL}{$order}{$limit}", "raw");
		$count = 1;
		$restrictImport = array();
		
	  	while ($testDataLoop = fetch($testDataGrabber)) {
			if ($preview == false) {
				$testValues = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$_GET['id']}' AND `questionID` = '{$testDataLoop['id']}' AND `attempt` = '{$currentAttempt}'");
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
					echo "\n(There may be more than one correct answer.)<br />";
				}
				
				echo "\n<br /><br />\n";
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
						echo textArea($testDataLoop['id'], $testDataLoop['id'], "small", true, false, arrayRevert($testValues['userAnswer']));
					} else {
						echo textArea($testDataLoop['id'], $testDataLoop['id'], "small", true);
					}
						
					break;
					
				case "File Response" : 
					if ($testData['totalFiles'] > 1 || sizeof(arrayRevert($testValues['userAnswer'])) > 1) {
						if (isset($monitor)) {
							$URL = $fileURL . "test/responses";
						} else {
							$URL = $fileURL . "test/responses";
							$fillValue = arrayRevert($testValues['userAnswer']);
						}
						
						echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">\n";
						
						if (isset($testValues) && !empty($fillValue)) {
							$fileID = 1;
							
							foreach ($fillValue as $key => $file) {
								echo "<tr id=\"" . $fileID . "\">\n";
								echo cell(fileUpload($testDataLoop['id'] . "_" . $fileID, $testDataLoop['id'] . "_" . $fileID, false, true, false, $fillValue[$key], false, false, $URL, false, true));
								echo cell(URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=" . $fileID, "action smallDelete", false, false, false, false, false, false, " onclick=\"return confirm('Important: Please save this test before deleting this file, or, otherwise, your changes may be lost. This action will delete this file. Continue?')\""));
								echo "</tr>\n";
								
								$fileID++;
							}
							
							unset($fileID);
							
							echo "</table>\n";
							echo "<br />
<p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '" . $testDataLoop['id'] . "', '" . $testData['totalFiles'] . "');\">Add Another File</span></p>\n<p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>\n";
						} else {
							echo "<tr id=\"1\">\n";
							echo cell(fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, false, false, false, false, false, true));
							echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('upload_" . $testDataLoop['id'] . "', '1', '1', true)\"></span>");
							echo "</tr>\n";
							echo "</table>\n";
							echo "<p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '" . $testDataLoop['id'] . "', '" . $testData['totalFiles'] . "');\">Add Another File</span></p>\n";
						}
						
						echo "<p>Max file size (for single file): " . ini_get('upload_max_filesize') . "<br>\nMax file size (for all files): " . ini_get('post_max_size') . "</p>\n";
					} else {
						if (isset($testValues)) {
							$fillValue = arrayRevert($testValues['userAnswer']);
							
							if (!empty($fillValue)) {
								echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">\n";
								echo "<tr id=\"1\">\n";
								echo cell(fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, $fillValue['0'], false, false, $URL, false, false));
								echo cell(URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=1", "action smallDelete", false, false, false, false, false, false, "return confirm('This action will delete this file. Continue?')"));
								echo "</tr>\n";
								echo "</table>\n";
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
					$blankQuestion = arrayRevert($testData['questionValue']);
					$blank = arrayRevert($testData['answerValue']);
					$answerCompare = arrayRevert($testData['answerValue']);
					$valueNumbers = sizeof($blankQuestion);
					$matchingCount = 1;
					
					echo "<p>";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
					   echo prepare($blankQuestion[$list], false, true) . " ";
					   
					   if (!empty($blank[$list])) {
						   if (isset($testValues)) {
							   $value = arrayRevert($testValues['userAnswer']);
							   
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
					$question = arrayRevert($testData['questionValue']);
					
					if ($preview == false) {
						$answer = arrayRevert($testValues['answerValueScrambled']);
					} else {
						$answer = arrayRevert($testData['answerValue']);
						shuffle($answer);
					}
					
					$answerCompare = arrayRevert($testData['answerValue']);
					$valueNumbers = sizeof($question);
					$matchingCount = 1;
					$fillValue = arrayRevert($testValues['userAnswer']);
					
					echo "<table width=\"100%\">\n";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
						$dropDownValue = "-,";
						$dropDownID = ",";
						
						echo "<tr>\n";
						echo "<td width=\"10\" valign=\"middle\">\n";
						
						for ($value = 1; $value <= $valueNumbers; $value++) {
							$dropDownValue .= $value . ",";
							$dropDownID .= $value . ",";
						}
						
						$values = rtrim($dropDownValue, ",");
						$IDs = rtrim($dropDownID, ",");
						
						if (isset($testValues)) {
							$value = arrayRevert($testValues['userAnswer']);
							 
							if (is_array($value)) {
								if (array_key_exists($list, $value)) {
									echo dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true, false, $fillValue[$list])  . " ";
								} elseif (!array_key_exists($list, $value) && isset($answerCompare[$list])) {
									echo dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
								}
							} else {
								echo dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
							}
						} else {
							echo dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true);
						}
						
						echo "</td>\n";
						echo cell($question[$list], "200");
						echo cell($matchingCount++, "10");
						echo cell($answer[$list], "200");
						echo "</tr>\n";
					}
					
					echo"</table>\n";				  
					break;
				
				case "Multiple Choice" :									
					if ($preview == true) {
						$questions = arrayRevert($testData['questionValue']);
						
						if ($testData['randomize'] == "1") {
							$questionsDisplay = $questions;
							shuffle($questionsDisplay);
						} else {
							$questionsDisplay = $questions;
						}
					} else {
						if ($testData['randomize'] == "1") {
							$questions = arrayRevert($testValues['answerValueScrambled']);
						} else {
							$questions = arrayRevert($testData['questionValue']);
						}
					}
					
					echo "<table>\n";
					
					if ($testData['choiceType'] == "radio") {						
						while (list($valueID, $value) = each($questions)) {
							echo "<tr>\n";
							echo "<td width=\"5\">";
							if (isset($testValues)) {
								echo radioButton($testDataLoop['id'], $testDataLoop['id'] . "_" . $valueID, false, $valueID + 1, false, true, false, arrayRevert($testValues['userAnswer']));
							} else {
								echo radioButton($testDataLoop['id'], $testDataLoop['id'] . "_" . $valueID, false, $valueID + 1, false, true);
							}
							
							echo "</td>\n";
							echo "<td onclick=\"document.getElementById('" . $testDataLoop['id'] . "_" . $valueID . "_0').checked = true;\">\n";
							echo $value;
							echo "\n</td>\n";
							echo "</tr>\n";
						}					
					} else {
						while (list($valueID, $value) = each($questions)) {
							$identifier = $valueID + 1;
							
							echo "<tr>\n";
							echo "<td width=\"5\">";
							
							if (isset($testValues)) {
								if (is_array(arrayRevert($testValues['userAnswer']))) {
									$fillValue = arrayRevert($testValues['userAnswer']);
								} else {
									$fillValue = array(arrayRevert($testValues['userAnswer']));
								}
								
								if (in_array($identifier, $fillValue)) {
									echo checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $identifier, false, $identifier, true, "1", true);
								} else {
									echo checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $identifier, false, $identifier, true, "1");
								}
							} else {
								echo checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $identifier, false, $identifier, true, "1");
							}
							
							echo "</td>\n";
							echo cell("\n<label for=\"" . $testDataLoop['id'] . "_" . $valueID . "\">" . $value . "</label>\n");
							echo "</tr>\n";
						}
					}
					
					echo "</table>\n";
					
					break;
					
				case "Short Answer" : 
					if (isset($testValues)) {
						echo textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true, false, arrayRevert($testValues['userAnswer']));
					} else {
						echo textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true);
					}
					
					break;
					
				case "True False" : 
					if ($preview == false) {
						if ($testData['randomize'] == "1") {						
							$label = arrayRevert($testValues['answerValueScrambled']);
							$id = implode(",", $label);
						} else {
							$label = arrayRevert($testValues['answerValue']);
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
						echo radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true, false, arrayRevert($testValues['userAnswer']));
					} else {
						echo radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true);
					}
					
					break;
			}
			
			if ($testData['type'] != "Description") {
				echo "\n<br /><br />\n</td>\n</tr>\n";
			}
		}
		
		echo "</table>\n";
		
		if ($preview == false) {
			indent(button("save", "save", "Save", "submit", false, "disableValidation();") . "\n" . 
			button("submit", "submit", "Submit", "submit", false, "return confirm('Once the test is submitted, it cannot be reopened. Continue?');"));
		}
		
		echo closeForm(false);
	}
	
/*
Create standard question types for the question generator
---------------------------------------------------------
*/
	
//Pull category data for auto-suggestion
	if ((strstr($_SERVER['SCRIPT_NAME'], "wizard/lesson_settings.php") || strstr($_SERVER['SCRIPT_NAME'], "/questions/")) && isset($_GET['data']) && $_GET['data'] == "xml") {
		header("Content-type: text/xml");	
		$noRepeat = array("category" => array());
		
		echo "<root>\n";
		
		if (exist("categories", "organization", $userData['organization'])) {
			$categoryBank = query("SELECT * FROM `categories` WHERE `organization` = '{$userData['organization']}' ORDER BY `category` ASC", "raw");
			
			while($category = fetch($categoryBank)) {			
				if (!in_array(prepare($category['category'], true), $noRepeat["category"])) {
					echo "<group>\n<category>" . prepare($category['category'], true) . "</category>\n</group>\n";
				}
				
				array_push($noRepeat['category'], prepare($category['category'], true));
			}
		}
		
		if (exist("learningunits", "organization", $userData['organization'])) {
			$priorEntries = query("SELECT * FROM `learningunits` WHERE `organization` = '{$userData['organization']}' ORDER BY `category` ASC", "raw");
			
			while($suggestion = fetch($priorEntries)) {
				if (!in_array(prepare($suggestion['category'], true), $noRepeat["category"])) {
					echo "<group>\n<category>" . prepare($suggestion['category'], true) . "</category>\n</group>\n";
				}
				
				array_push($noRepeat["category"], prepare($suggestion['category'], true));
			}
		}
		
		if (exist("fields")) {
			$additionalFields = query("SELECT * FROM `fields`", "raw");
			
			while($fields = fetch($additionalFields)) {
				$noRepeat[$fields['id']] = array();
				
				if (exist("learningunits", "organization", $userData['organization'])) {
					$fieldInfo = query("SELECT * FROM `learningunits` WHERE `organization` = '{$userData['organization']}' ORDER BY `field_{$fields['id']}` ASC", "raw");
					
					while ($field = fetch($fieldInfo)) {
						if (!in_array(prepare($field["field_" . $fields['id']], true), $noRepeat[$fields['id']])) {
							echo "<group>\n<field_" . $fields['id'] . ">" . prepare($field["field_" . $fields['id']], true) . "</field_" . $fields['id'] . ">\n</group>\n";
						}
						
						array_push($noRepeat[$fields['id']], prepare($field["field_" . $fields['id']], true));
					}
				}
				
				if (exist("questionbank_" . $userData['organization'])) {
					$bankFields = query("SELECT * FROM `questionbank_{$userData['organization']}` ORDER BY `field_{$fields['id']}` ASC", "raw");
					
					while($bank = fetch($bankFields)) {
						if (!in_array(prepare($bank["field_" . $fields['id']], true), $noRepeat[$fields['id']])) {
							echo "<group>\n<field_" . $fields['id'] . ">" . prepare($bank["field_" . $fields['id']], true) . "</field_" . $fields['id'] . ">\n</group>\n";
						}
						
						array_push($noRepeat[$fields['id']], prepare($bank["field_" . $fields['id']], true));
					}
				}
				
				if (isset($_SESSION['currentUnit']) && exist("test_" . $_SESSION['currentUnit'])) {
					$testFields = query("SELECT * FROM `test_{$_SESSION['currentUnit']}` ORDER BY `field_{$fields['id']}` ASC", "raw");
					
					while ($field = fetch($testFields)) {
						if (!in_array(prepare($field["field_" . $fields['id']], true), $noRepeat[$fields['id']])) {
							echo "<group>\n<field_" . $fields['id'] . ">" . prepare($field["field_" . $fields['id']], true) . "</field_" . $fields['id'] . ">\n</group>\n";
						}
						
						array_push($noRepeat[$fields['id']], prepare($field["field_" . $fields['id']], true));
					}
				}
			}
		}
		
		echo "</root>\n";
		exit;
	}
	
//Ensure the page is handling the correct question type
	function dataGrabber($type) {
		global $monitor, $userData;
		
		if (isset($_GET['id'])) {
			if (exist($monitor['testTable'], "id", $_GET['id'])) {
				$data = query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$_GET['id']}'");
				
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
			if (exist("questionbank_{$userData['organization']}", "id", $_GET['bankID'])) {
				$data = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$_GET['bankID']}'");
				
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
		indent(textField("points", "points", "5", "5", false, true, "custom[onlyNumber]", false, "questionData", "points") . 
		"&nbsp;" . 
		checkbox("extraCredit", "extraCredit", "Extra Credit", false, false, false, false, "questionData", "extraCredit", "on"));
	}
	
//Include where this question is being inserted
	function type() {
		global $questionData;
		
		$active = 0;
		$valuesPrep = "- Select -,";
		$valueIDsPrep = ",";
		
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
			directions("Insert question into", true);
			indent(dropDown("type", "type", $values, $valueIDs, false, true, false, false, false, false, " onchange=\"toggleDescription(this.value);\""));
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
			
			echo hidden("type", "type", ltrim($valueIDs, ","));
		}
	}
	
//Display all of the descriptions in this test
	function descriptionLink() {
		global $monitor, $userData;
		
		if (isset($_SESSION['currentUnit']) && !isset($_GET['bankID']) && !isset($_GET['feedbackID'])) {
			echo "<div id=\"descriptionLink\">\n";
			
			if (exist($monitor['testTable'], "type", "Description")) {
				$descriptionGrabber = query("SELECT * FROM `{$monitor['testTable']}` WHERE `type` = 'Description' ORDER BY `position` ASC", "raw");
				$descriptionName = "- Select -,";
				$descriptionID = ",";
				
				while ($description = fetch($descriptionGrabber)) {
					if ($description['type'] == "Description" && $description['questionBank'] != "1") {
						$descriptionID .= $description['id'] . ",";
						$descriptionName .= $description['position'] . ". " . commentTrim(25, $description['question']) . ",";
					} elseif ($description['type'] == "Description" && $description['questionBank'] == "1") {
						$descriptionImport = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `id` = '{$description['linkID']}'");
						$descriptionID .= $description['id'] . ",";
						$descriptionName .= $description['position'] . ". " . commentTrim(25, $descriptionImport['question']) . ",";
					}
				}
				
				$IDs = rtrim($descriptionID, ",");
				$values = rtrim($descriptionName, ",");
			} else {
				$IDs = "";
				$values = "- None -";
			}
			
			directions("Link to description", false);
			indent(dropDown("link", "link", $values, $IDs, false, false, false, false, "questionData", "link"));
			echo "</div>\n";
		} else {
			hidden("link", "link", "");
		}
	}
	
//Display partial credit option
	function partialCredit() {
		directions("Allow partial credit");
		indent(radioButton("partialCredit", "partialCredit", "Yes,No", "1,0", true, false, false, "0", "questionData", "partialCredit", " onchange=\"toggleFeedback(this.value)\""));
	}
	
//Display a case sensitivity option
	function ignoreCase() {
		directions("Ignore case");
		indent(radioButton("case", "case", "Yes,No", "1,0", true, false, false, "1", "questionData", "case"));
	}
	
//Display a randomize option
	function randomize() {
		directions("Randomize values");
		indent(radioButton("randomize", "randomize", "Yes,No", "1,0", true, false, false, "0", "questionData", "randomize"));
	}
	
//Display search tags
	function tags() {
		directions("Tags (Seperate with commas)", false);
		indent(textField("tags", "tags", false, false, false, false, false, false, "questionData", "tags"));
	}
	
//Display all of the category items
	function category() {
		global $monitor, $userData;
		
		if (!strstr($_SERVER['REQUEST_URI'], "wizard")) {
			
			$noRepeat = array();
			$valuesPrep = "";
			
			if (isset($_SESSION['currentUnit'])) {
				$defaultSelect = query("SELECT * FROM `learningunits` WHERE `id` = '{$_SESSION['currentUnit']}'");
				$categoryBank = query("SELECT * FROM `learningunits` WHERE `organization` = '{$userData['organization']}' ORDER BY `category` ASC", "raw");
				
				while($category = fetch($categoryBank)) {
					if (!in_array(prepare($category['category']), $noRepeat)) {
						$valuesPrep .= prepare($category['category']) . ",";
					}
					
					array_push($noRepeat, prepare($category['category']));
				}
			} else {
				$defaultSelect = query("SELECT * FROM `categories` WHERE `id` = '{$_SESSION['questionBank']}'");
			}
			
			if (exist("categories", "organization", $userData['organization'])) {
				$priorEntries = query("SELECT * FROM `categories` WHERE `organization` = '{$userData['organization']}' ORDER BY `category` ASC", "raw");
				
				while($suggestion = fetch($priorEntries)) {
					if (!in_array(prepare($suggestion['category']), $noRepeat)) {
						$valuesPrep .= prepare($suggestion['category']) . ",";
					}
					
					array_push($noRepeat, prepare($suggestion['category']));
				}
			}
			
			$values = rtrim($valuesPrep, ",");
			
			directions("Category", true);
			indent("\n<div id=\"categoryMenu\">" . 
			dropDown("category", "category", $values, $values, false, true, false, $defaultSelect['category'], "questionData", "category"));
		} else {
			directions("Category", true);
			echo "\n<blockquote>\n<div id=\"categoryMenu\">" . 
			textField("category", "category", false, false, false, true, false, false, "lessonData", "category") . 
			"<div>\n<div id=\"categorySuggestions\" spry:region=\"data\">\n<div spry:repeat=\"data\" spry:suggest=\"{category}\">{category}</div>\n</div>\n</div>\n</div>\n</blockquote>\n";
			echo "<script type=\"text/javascript\">
  var dataSuggestions = new Spry.Widget.AutoSuggest(\"categoryMenu\", \"categorySuggestions\", \"data\", \"category\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});
</script>\n";
		}
	}
	
//Display the feedback
	function feedback($hidePartial = false) {
		global $questionData;
		
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
	
//Pull custom fields from the database
	function customField($type, $variable) {
		global $$variable;
		
		if (exist("fields")) {
			$fieldsGrabber = query("SELECT * FROM `fields` ORDER BY `position` ASC", "raw");
			
			while ($fields = fetch($fieldsGrabber)) {
				$section = arrayRevert($fields['section']);
				$values = arrayRevert($fields['values']);
				$selection = arrayRevert($fields['selected']);
				
				if ($fields['require'] == "1") {
					$required = true;
				} else {
					$required = false;
				}
				
				if (is_array($section) && in_array($type, $section)) {
					if ($fields['showTip'] == "1" && !empty($fields['description'])) {
						directions($fields['name'], $required, escape(strip_tags($fields['description'])));
					} else {
						directions($fields['name'], $required);
					}
					
					switch($fields['fieldType']) {
						case "textField" : 
							$randomValue = randomValue("10", "alpha");
							
							if ($fields['autoSuggest'] == "1") {
								echo "<blockquote>\n<div id=\"" . $randomValue . "_Menu\">" . 
								textField($fields['id'], $fields['id'], false, false, false, $required, false, false, $variable, "field_" . $fields['id']) . 
								"<div>\n<div id=\"" . $randomValue . "_Suggestions\" spry:region=\"data\">\n<div spry:repeat=\"data\" spry:suggest=\"{field_" . $fields['id'] . "}\">{field_" . $fields['id'] . "}</div>\n</div>\n</div>\n</div>\n</blockquote>\n";
								echo "<script type=\"text/javascript\">
  var " . $randomValue . "_Loader = new Spry.Widget.AutoSuggest(\"" . $randomValue . "_Menu\", \"" . $randomValue . "_Suggestions\", \"data\", \"field_" . $fields['id'] . "\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});
</script>\n";
							} else {
								indent(textField($fields['id'], $fields['id'], false, false, false, $required, false, false, $variable, "field_" . $fields['id']));
							}
							
							break;
							
						case "textArea" : 
							indent(textArea($fields['id'], $fields['id'], "small", $required, false, false, $variable, "field_" . $fields['id'], "class=\"noEditorAdvanced\""));
							break;
							
						case "dropDown" : 
							$items = "";
							$selected = explode(",", $items);
							
							if (isset($selected[$selection['0'] - 1])) {
								$select = $selected[$selection['0'] - 1];
								$prependValue = "";
								$prependID = "";
							} else {
								$select = "";
								$prependValue = "- Select -,";
								$prependID = ",";
								
							}
							
							foreach ($values as $value) {
								$items .= prepare($value, true, true) . ",";
							}
							
							indent(dropDown($fields['id'], $fields['id'], $prependValue . rtrim($items, ","), $prependID . rtrim($items, ","), false, $required, false, $select, $variable, "field_" . $fields['id']));
							break;
							
						case "radio" : 
							$items = "";
							
							foreach ($values as $value) {
								$items .= prepare($value, true, true) . ",";
							}
							
							$selected = explode(",", $items);
							
							if (is_array($selection)) {
								indent(radioButton($fields['id'], $fields['id'], rtrim($items, ","), rtrim($items, ","), false, $required, false, $selected[$selection['0'] - 1], $variable, "field_" . $fields['id']));
							} else {
								indent(radioButton($fields['id'], $fields['id'], rtrim($items, ","), rtrim($items, ","), false, $required, false, false, $variable, "field_" . $fields['id']));
							}
							
							break;
							
						case "checkbox" : 
							$items = "";
							$count = 0;
							$checked = false;
							
							echo "<blockquote>\n";
							
							foreach ($values as $value) {
								if (isset($$variable)) {
									$currentItemPrep = $$variable;
									$currentItem = arrayRevert($currentItemPrep["field_" . $fields['id']]);
									
									if (is_array($currentItem) && in_array($value, $currentItem)) {
										$checked = true;
									}
								} else {
									if (is_array($selection) && in_array($count + 1, $selection)) {
										$checked = true;
									}
								}
								
								echo checkbox($fields['id'] . "[]", $fields['id'] . "_" . $count, $value, prepare($value, true, true), $required, "1", $checked);
								echo "<br />\n";
								$count++;
								$checked = false;
							}
							
							echo "</blockquote>\n";
							
							break;
							
						default : 
							die(errorMessage("Incorrect field type selected on " . $fields['id']));
							break;
					}
				}
			}
		}
	}
	
//Process custom questions
	function processFields($type, $table, $id) {
		if (exist("fields")) {
			$fieldsGrabber = query("SELECT * FROM `fields` ORDER BY `position` ASC", "raw");
			$sql = "UPDATE `{$table}` SET";
			
			while ($fields = fetch($fieldsGrabber)) {
				if (isset($_POST[$fields['id']]) && (!empty($_POST[$fields['id']]) || is_numeric($_POST[$fields['id']]))) {
					if (is_array($_POST[$fields['id']])) {
						$value = escape(arrayStore($_POST[$fields['id']]));
					} else {
						$value = escape($_POST[$fields['id']]);
					}
					
					$sql .= " `field_{$fields['id']}` = '{$value}',";
				} else {
					if ($fields['require'] == "1" && in_array($type, arrayRevert($fields['section']))) {
						die(errorMessage("A required field was not filled out"));
					} else {
						$sql .= " `field_{$fields['id']}` = '',";
					}
				}
			}
			
			$sql = rtrim($sql, ",") . " WHERE `id` = '{$id}'";
			
			query($sql);
		}
	}
	
/*
Include JavaScripts and CSS for client-side processing
---------------------------------------------------------
*/
	
//Include a full-size calendar script
	function fullCalendar() {
		global $root, $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/calendar.js\" type=\"text/javascript\"></script>
<link rel=\"stylesheet\" href=\"" . $pluginRoot . "system/styles/calendar.css\" type=\"text/css\">";
	}
	
//Include the administrative javascript library
	function administrativeLibrary() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/administrativeLibrary.js\" type=\"text/javascript\"></script>";
	}
	
//Include the learning unit javascript library
	function learningUnitLibrary() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/learningUnitLibrary.js\" type=\"text/javascript\"></script>";
	}
	
//TinyMCE small media
	function tinyMCEMedia() {
		global $root, $pluginRoot;
		
		return "<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/tiny_mce.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/AtD/editor_plugin.js\"></script>
<script type=\"text/javascript\" src=\"" . $pluginRoot . "system/javascripts/tiny_mce_media.php\"></script>";
	}
	
//TinyMCE media, purposefully excludes the tiny_mce.js and editor_plugin.js scripts
	function tinyMCEMediaConfig() {
		global $pluginRoot;
		
		return "<script type=\"text/javascript\" src=\"" . $pluginRoot . "system/javascripts/tiny_mce_config.php\"></script>";
	}
	
//TinyMCE question, purposefully excludes the tiny_mce.js and editor_plugin.js scripts
	function tinyMCEQuestion() {
		global $pluginRoot;
		
		return "<script type=\"text/javascript\" src=\"" . $pluginRoot . "system/javascripts/tiny_mce_question.php\"></script>";
	}
	
//Plug-in check script
	function plugins() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/systemCheck/browserDetect.js\" type=\"text/javascript\"></script>
<script src=\"" . $pluginRoot . "system/javascripts/systemCheck/acrobatDetect.js\" type=\"text/javascript\"></script>
<script src=\"" . $pluginRoot . "system/javascripts/systemCheck/flashDetect.js\" type=\"text/javascript\"></script>
<script src=\"" . $pluginRoot . "system/javascripts/systemCheck/quicktimeDetect.js\" type=\"text/javascript\"></script>
<script src=\"" . $pluginRoot . "system/javascripts/systemCheck/windowsMediaDetect.js\" type=\"text/javascript\"></script>";
	}
	
//Update the contents of a field in real-time
	function liveUpdate() {
		global $root, $pluginRoot;
		
		return "<script src=\"" . $root . "system/javascripts/ajaxLibraries/SpryData_0.46.js\" type=\"text/javascript\"></script>
<script src=\"" . $pluginRoot . "system/javascripts/live_update.js\" type=\"text/javascript\"></script>
<script src=\"" . $pluginRoot . "system/javascripts/data_set_update.js\" type=\"text/javascript\"></script>";
	}
	
//Navigation menu styles
	function navigationMenu() {
		global $root, $pluginRoot;
		
		return "<link rel=\"stylesheet\" href=\"" . $pluginRoot . "system/styles/menu/menu.css\" type=\"text/css\">
<!--[if lte IE 7]>
<link rel=\"stylesheet\" href=\"" . $pluginRoot . "system/styles/menu/menu_ie6.css\" type=\"text/css\">
<![endif]-->";
	}
	
//Make a live request for data regarding the global average of a user's score for a particular learning unit
	function getStats() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/statistics_loader.js\" type=\"text/javascript\"></script>";
	}
	
//Make a live request for the status of a recent payment
	function paymentStatus() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/payment_loader.js\" type=\"text/javascript\"></script>";
	}
	
//Make a live action to enroll a user in a free of charge learning unit
	function enroll() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/enroll.js\" type=\"text/javascript\"></script>";
	}
	
//Include a library of jQuery tools
	function library() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/library.js\" type=\"text/javascript\"></script>";
	}
?>