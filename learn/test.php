<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: September 4th, 2010
Last updated: December 23rd, 2010

This script is dedicated to displaying the test section 
of each learning unit.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Set variables which will be access repeatedly through out this script
	$testID = $_GET['id'];
	$parentTable = "test_" . $testID;
	$testTable = "testdata_" . $userData['id'];
	$questionBank = "questionbank_0";
	$unitInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$_GET['id']}' LIMIT 1");
	$attempt = lastItem($testTable, "testID", $testID, "attempt");
	$accessGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}'");
	$accessArray = unserialize($accessGrabber['learningunits']);
	
	if ($attempt - 1 == 0) {
		$currentAttempt = 1;
	} else {
		$currentAttempt = $attempt - 1;
	}
	
	if ($accessArray[$testID]['testStatus'] == "C") {
		$query = "SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'";
		$updateArray = serialize($accessArray);
		
		query("UPDATE `users` SET `learningunits` = '{$updateArray}' WHERE `id` = '{$userData['id']}'");
	} else {
		$query = "SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'";
	}
	
//Ensure the user has permission to access this test
	if (isset ($_GET['id'])) {		
		if (!exist("learningunits", "id", $_GET['id'])) {
			redirect("index.php");
		}
		
		if (!array_key_exists($testID, $accessArray)) {
			redirect("index.php");
		}
		
		if ($accessArray[$testID]['lessonStatus'] != "F") {
			redirect("lesson.php?id=" . $testID);
		}
		
		if ($accessArray[$testID]['testStatus'] == "A" || $accessArray[$testID]['testStatus'] == "F") {
			redirect("review.php?id=" . $testID);
		}
	} else {
		redirect("index.php");
	}
	
//Check to see if test questions exist
	function questionExist($id) {
		global $testTable, $testID, $currentAttempt;
		
		$questionCheck = query("SELECT * FROM `{$testTable}` WHERE `questionID` = '{$id}' AND `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' LIMIT 1", "raw");
		
		if ($questionCheck) {
			return true;
		} else {
			return false;
		}
	}
	
//If the test is left unconfigured, then prompt the user to configure it before taking the test
	if (!query($query, false, false)) {		
		if (is_array($_GET) && sizeof($_GET) >= 2) {
			header("Content-type: text/xml");
			
			echo "<root>\n";
			
			if (sizeof($_GET) > 2) {
				$sql = "SELECT `field_{$key}` FROM `{$parentTable}` WHERE";
				$restrictImport = array();
				
				foreach($_GET as $key => $parameterPrep) {
					$parameter = escape($parameterPrep);
					
					if ($key != "id" && $key != "data") {
						$sql .= " `{$parentTable}`.`field_{$key}` = '{$parameter}' AND `{$questionBank}`.`field_{$key}` = '{$parameter}' AND";
					}
				}
				
				$sql = rtrim($sql, " AND") . "UNION ALL ELECT `field_{$key}` FROM `{$parentTable}` WHERE";
				
				$questions = query(rtrim($sql, " AND"), "num");
				
				for ($count = $questions; $count >= 1; $count --) {
					echo "<group>\n";
					echo "<question>" . $count ."</question>\n";
					echo "</group>\n";
				}
			} else {
				$questions = query("SELECT * FROM `{$parentTable}`", "num");
				
				for ($count = $questions; $count >= 1; $count --) {
					echo "<group>\n";
					echo "<question>" . $count ."</question>\n";
					echo "</group>\n";
				}
			}
			
			echo "</root>\n";
			exit;
		}
		
	//Process the form
		if (isset($_POST['setup']) && isset($_POST['difficulty']) && isset($_POST['questions'])) {
			$totalQuestions = query("SELECT * FROM `{$parentTable}` WHERE `type` != 'Description'", "num");
			$questionsPercentage = number_format(sprintf($_POST['questions']/$totalQuestions), 2);
			$totalDescriptions = query("SELECT * FROM `{$parentTable}` WHERE `type` = 'Description'", "num");
			$descriptionNumber = ceil(sprintf($totalDescriptions * $questionsPercentage));
			$difficulty = $_POST['difficulty'];
			$questions = $_POST['questions'];
			$limit = $questions + $descriptionNumber;
			$count = 1;
			
			if ($difficulty == "All Levels" || $difficulty == urlencode("All Levels")) {
				$testDataGrabber = query("(SELECT * FROM `{$parentTable}` WHERE `type` != 'Description' ORDER BY RAND() LIMIT {$questions}) UNION (SELECT * FROM `{$parentTable}` WHERE `type` = 'Description' ORDER BY RAND() LIMIT {$descriptionNumber})", "raw");
			} else {
				$testDataGrabber = query("(SELECT * FROM `{$parentTable}` WHERE `difficulty` = '{$difficulty}' AND `type` != 'Description' ORDER BY RAND() LIMIT {$questions}) UNION (SELECT * FROM `{$parentTable}` WHERE `type` = 'Description' ORDER BY RAND() LIMIT {$descriptionNumber})", "raw");
			}
			
			query("CREATE TABLE IF NOT EXISTS `{$testTable}` (
				  `testID` int(255) NOT NULL,
				  `questionID` int(255) NOT NULL,
				  `attempt` int(255) NOT NULL,
				  `type` longtext NOT NULL,
				  `link` int(255) NOT NULL,
				  `testPosition` int(255) NOT NULL,
				  `randomPosition` int(255) NOT NULL,
				  `randomizeTest` longtext NOT NULL,
				  `randomizeQuestion` int(255) NOT NULL,
				  `extraCredit` text NOT NULL,
				  `points` varchar(5) NOT NULL,
				  `score` varchar(8) NOT NULL,
				  `question` longtext NOT NULL,
				  `questionValue` longtext NOT NULL,
				  `answerValue` longtext NOT NULL,
				  `answerValueScrambled` longtext NOT NULL,
				  `userAnswer` longtext NOT NULL,
				  `testAnswer` longtext NOT NULL,
				  `feedback` longtext NOT NULL
				)");
				
			$restrictImport = array();
			
			while ($testDataLoop = mysql_fetch_array($testDataGrabber)) {
				if ($testDataLoop['questionBank'] == "1") {
					$importQuestion = query("SELECT * FROM `questionbank` WHERE `id` = '{$testDataLoop['linkID']}'");
				} else {
					$testData = $testDataLoop;
				}
				
				if (!empty($testDataLoop['link']) && $testDataLoop['link'] != "0" && exist($parentTable, "id", $testDataLoop['link']) && !in_array($testDataLoop['link'], $restrictImport)) {
					$randomPosition = $count ++;
					$questionID = $testDataLoop['link'];
					
					query("INSERT INTO `{$testTable}` (
					  `testID`, `questionID`, `attempt`, `type`, `link`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `questionValue`, `answerValue`, `answerValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
					  ) VALUES (
					  '{$testID}', '{$questionID}', '{$attempt}', 'Description', '', '', '{$randomPosition}', '', '', '', '', '', '', '', '', '', '', '', ''
					  )");
					  
					array_push($restrictImport, $testDataLoop['link']);
				}
				
				if (!in_array($testDataLoop['id'], $restrictImport)) {
					$questionID = $testDataLoop['id'];
					$type = $testDataLoop['type'];
					$randomPosition = $count ++;
					
					if ($testData['type'] == "Matching" || $testData['type'] == "Multiple Choice" || $testData['type'] == "True False") {
						if ($testData['type'] == "Multiple Choice") {
							$questionValue = "";
							$answerValue = mysql_real_escape_string($testData['questionValue']);
							$answerValueScrambledPrep = unserialize($testData['questionValue']);
						} elseif($testData['type'] == "Matching") {
							$questionValue = mysql_real_escape_string($testData['questionValue']);
							$answerValue = mysql_real_escape_string($testData['answerValue']);
							$answerValueScrambledPrep = unserialize($testData['answerValue']);
						} elseif ($testData['type'] == "True False") {
							$questionValue = "";
							$answerValue = mysql_real_escape_string(serialize(array("1", "0")));
							$answerValueScrambledPrep = array("1", "0");
						}
						
						shuffle($answerValueScrambledPrep);
						$answerValueScrambled = mysql_real_escape_string(serialize($answerValueScrambledPrep));
					} else {
						$questionValue = "";
						$answerValue = "";
						$answerValueScrambled = "";
					}
					
					query("INSERT INTO `{$testTable}` (
						  `testID`, `questionID`, `attempt`, `type`, `link`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `questionValue`, `answerValue`, `answerValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
						  ) VALUES (
						  '{$testID}', '{$questionID}', '{$attempt}', '{$type}', '', '', '{$randomPosition}', '', '', '', '', '', '', '{$questionValue}', '{$answerValue}', '{$answerValueScrambled}', '', '', ''
						  )");
					
					array_push($restrictImport, $testDataLoop['id']);
				}
			}
			
			redirect($_SERVER['REQUEST_URI']);
		}
			
	//Top content
		headers($unitInfo['name'] . " Configuration", "liveUpdate", true, false, false, false, "<script type=\"text/javascript\">
  var dsQuestions = new Spry.Data.XMLDataSet(\"" . $_SERVER['REQUEST_URI'] . "&data=xml\", \"root/group\", {useCache:false});
</script>");
		
	//Title
		title($unitInfo['name'] . " Configuration", "Please configure this test to best suit your needs. Keep in mind that once these settings are set, they cannot be changed, unless you decide to retake this test after this session.");
		
	//Configuration form
		echo form("configuration");
		echo hidden("parameters", "parameters", "");
		catDivider("Configuration", "one", true);
		echo "<blockquote>\n";
		
		$customFields = query("SELECT * FROM `fields` WHERE `testFilter` = '1' AND `fieldType` != 'checkbox' AND `fieldType` != 'textArea' ORDER BY `position` ASC", "raw");
		
		while ($field = fetch($customFields)) {
			if (in_array("Question Generator", unserialize($field['section']))) {
				$items = "";
				$values = unserialize($field['values']);
				
				if ($field['showTip'] == "1") {
					$tip = strip_tags($field['description']);
				} else {
					$tip = false;
				}
				
				directions($field['name'], false, $tip);
				
				switch($field['fieldType']) {
					case "dropDown" : 
					case "radio" : 
						$items = "- All -,";
						$IDs = ",";
						
						foreach ($values as $value) {
							$items .= prepare($value, true, true) . ",";
							$IDs .= prepare($value, true, true) . ",";
						}
						
						indent(dropDown($field['id'], $field['id'], rtrim($items, ","), rtrim($IDs, ","), false, false, false, false, false, false, "onchange=\"updateDataSet(this.id)\""));
						
						break;
						
					case "textField" : 
						$items = "- All -,";
						$IDs = ",";
						$values = array_unique(query("SELECT `field_{$field['id']}` FROM `{$parentTable}`", "selected"));
						
						foreach ($values as $value) {
							if (!empty($value)) {
								$items .= prepare($value, true, true) . ",";
								$IDs .= prepare($value, true, true) . ",";
							}
						}
						
						indent(dropDown($field['id'], $field['id'], rtrim($items, ","), rtrim($IDs, ","), false, false, false, false, false, false, "onchange=\"updateDataSet(this.id)\""));
						break;
						
					case "checkbox" : 
						$count = 0;
						echo "<blockquote><p>";
						
						foreach ($values as $value) {
							echo checkbox($field['id'] . "[]", $field['id'] . "_" . $count, $value, prepare($value, true, true), false, false, false, false, false, false, "onclick=\"updateDataSet(this.id)\"");
							echo "<br />\n";
							$count++;
						}
						
						echo "</p></blockquote>";
						break;
						
					default : 
						die(errorMessage("Incorrect field type selected on " . $fields['id']));
						break;
				}
			}
		}
		
		directions("Number of questions", false);
		echo "\n<blockquote>\n";
		echo "<span spry:region=\"dsQuestions\" id=\"questionSelector\">\n";
		echo "<select id=\"questions\" name=\"questions\">\n<option spry:repeat=\"dsQuestions\" value=\"{question}\">{question}</option>\n</select>\n";
		echo "</span>\n";
		echo "</blockquote>\n";
		echo "</blockquote>\n";		
		
		catDivider("Submit", "two");
		indent(button("setup", "setup", "Submit", "submit") . 
		button("cancel", "cancel", "Cancel", "cancel", "index.php"));
		echo closeForm();
		
	//Include the footer
		footer();
		exit;
	}
	
//Check for updates to the test canvas, and apply them to this test
	$testDataGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' ORDER BY `questionID` ASC", "raw");
	$questionConfig = array("Matching", "Multiple Choice");
	
	while ($testData = mysql_fetch_array($testDataGrabber)) {
		$bankDataGrabber = query("SELECT * FROM `{$parentTable}` WHERE `id` = '{$testData['questionID']}'");
		
		if (!empty($bankDataGrabber['link']) && !questionExist($bankDataGrabber['link']) && exist($parentTable, "id", $bankDataGrabber['link'])) {
			$lastQuestionGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' ORDER BY `randomPosition` DESC LIMIT 1");
			$lastQuestion = $lastQuestionGrabber['randomPosition'] + 1;
			
			query("INSERT INTO `{$testTable}` (
				  `testID`, `questionID`, `attempt`, `type`, `link`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `questionValue`, `answerValue`, `answerValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
				  ) VALUES (
				  '{$testID}', '{$bankDataGrabber['link']}', '{$currentAttempt}', 'Description', '', '', '{$lastQuestion}', '', '', '', '', '', '', '', '', '', '', '', ''
				  )");
		}
		
		if ($bankDataGrabber['questionBank'] == "1") {
			$bankData = query("SELECT * FROM `questionbank` WHERE `id` = '{$bankDataGrabber['linkID']}'");
		} else {
			$bankData = $bankDataGrabber;
		}
		
		if (exist($parentTable, "id", $testData['questionID'])) {
			if (in_array($bankData['type'], $questionConfig)) {
				if ($testData['type'] == "Matching") {
					$questionValue = mysql_real_escape_string($bankData['questionValue']);
					$answerValue = mysql_real_escape_string($bankData['answerValue']);
					$answerValueScrambledPrep = unserialize($bankData['answerValue']);
					$answerCompare = $bankData['answerValue'];
				} else {
					$questionValue = "";
					$answerValue = mysql_real_escape_string($bankData['questionValue']);
					$answerValueScrambledPrep = unserialize($bankData['questionValue']);
					$answerCompare = $bankData['questionValue'];
				}
				
				shuffle($answerValueScrambledPrep);
				$answerValueScrambled = mysql_real_escape_string(serialize($answerValueScrambledPrep));
				
				if ($answerCompare !== $testData['answerValue']) {
					query("UPDATE `{$testTable}` SET `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `answerValueScrambled` = '{$answerValueScrambled}' WHERE `questionID` = '{$testData['questionID']}' AND `attempt` = '{$currentAttempt}'");
				}
			}
		} else {
			query("DELETE FROM `{$testTable}` WHERE `questionID` = '{$testData['questionID']}' AND `attempt` = '{$currentAttempt}' LIMIT 1");
			query("UPDATE `{$testTable}` SET `randomPosition` = randomPosition-1 WHERE `randomPosition` > '{$testData['randomPosition']}' AND `attempt` = '{$currentAttempt}'");
		}
	}
	
//Top content
	headers($moduleInfo['name'], "Student,Site Administrator", "tinyMCESimple,newObject", true);
	
//Process the form
	if (isset($_POST['submit']) || isset($_POST['save'])) {	
		$count = 0;
		$questions = array();
		
		foreach ($_POST as $key => $answer) {
			if (exist($parentTable, "id", $key)) {
				$value = mysql_real_escape_string(serialize($answer));
				query("UPDATE `{$testTable}` SET `userAnswer` = '{$value}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$key}'");
				array_push($questions, $key);
			}
		}
		
		$choiceGrabber = query("SELECT * FROM `{$testTable}` WHERE `type` = 'Multiple Choice' AND `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'", "raw");
		
		while ($choice = mysql_fetch_array($choiceGrabber)) {
			if (!in_array($choice['questionID'], $questions)) {
				query("UPDATE `{$testTable}` SET `userAnswer` = '' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$choice['questionID']}'");
			}
		}
		
		foreach ($_FILES as $key => $file) {
			$count ++;
			
			if (is_uploaded_file($file['tmp_name'])) {
				$id = explode("_", $key);
				$tempFile = $file['tmp_name'];
				$targetFile = basename($file['name']);
				$uploadDir = $_GET['id'] . "/test/responses";
				$fileNameArray = explode(".", $targetFile);
				$targetFile = "";
				extension($file['name']);
				
				for ($count = 0; $count <= sizeof($fileNameArray) - 1; $count++) {
					if ($count == sizeof($fileNameArray) - 2) {
						$targetFile .= $fileNameArray[$count] . " " . randomValue(10, "alphanum") . ".";
					} elseif($count == sizeof($fileNameArray) - 1) {
						$targetFile .= $fileNameArray[$count];
					} else {
						$targetFile .= $fileNameArray[$count] . ".";
					}
				}
				
				$targetFile = mysql_real_escape_string($targetFile);
				
				if (move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile)) {
					$fileGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$id['0']}'");
											
					if (is_array(unserialize($fileGrabber['userAnswer']))) {
						$filesArray = unserialize($fileGrabber['userAnswer']);
					} else {
						$filesArray = array();
					}
					
					unlink($uploadDir . "/" . $filesArray[intval($id['1']) - 1]);
					$filesArray[intval($id['1']) - 1] = $targetFile;
					$value = mysql_real_escape_string(serialize($filesArray));
					query("UPDATE `{$testTable}` SET `userAnswer` = '{$value}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$id['0']}'");
				} else {
					$errors = true;
				}
			}
		}
		
		if (isset($_POST['save'])) {
			if (isset($errors)) {
				redirect($_SERVER['REQUEST_URI'] . "&error=upload");
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
		//Grade the test
		//Grab the necessary info
			$selectionGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'", "raw");
			$randomizeTest = $moduleInfo['randomizeAll'];
			$additionalSQLConstruct = " WHERE ";
				
			while ($selection = mysql_fetch_array($selectionGrabber)) {
				$additionalSQLConstruct .= "`id` = '{$selection['questionID']}' OR ";
			}
			
			$additionalSQL = rtrim($additionalSQLConstruct, " OR ");
				
			if ($moduleInfo['randomizeAll'] == "Randomize") {
				$order = " ORDER BY `randomPosition` ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = $parentTable . ".*, " . $testTable . ".randomPosition, " . $testTable . ".userAnswer, " . $testTable . ".answerValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $parentTable . ".id = testdata_" . $userData['id'] . ".questionID";
			
			$testDataGrabber = query("SELECT {$grab} FROM `{$parentTable}`{$join}{$additionalSQL}{$order}", "raw");
			
			if (exist("moduledata", "id", $_GET['id']) == false) {
				redirect("index.php");
			}
		
		//Include only the questions that can be automatically scored
			$gradeConfig = array("Fill in the Blank", "Matching", "Multiple Choice", "Short Answer", "True False");
			$noGrade = array();
			
			while ($testDataLoop = mysql_fetch_array($testDataGrabber)) {
				if ($testDataLoop['questionBank'] == "1") {
					$testData = query("SELECT * FROM `questionbank` WHERE `id` = '{$testDataLoop['linkID']}'");
					$position = $testDataLoop['position'];
				} else {
					$testData = $testDataLoop;
					$position = $testData['position'];
				}
				
				$randomizeQuestion = $testData['randomize'];
				$link = $testData['link'];
				$points = $testData['points'];
				$extraCredit = $testData['extraCredit'];
				$question = mysql_real_escape_string($testData['question']);
				$questionValue = mysql_real_escape_string($testData['questionValue']);
				
				if (!empty($testData['answerValue'])) {
					$answerValue = mysql_real_escape_string($testData['answerValue']);
				} elseif (!empty($testData['answer'])) {
					$answerValue = mysql_real_escape_string($testData['answer']);
				} elseif (!empty($testData['fileURL'])) {
					$answerValue = mysql_real_escape_string($testData['fileURL']);
				} else {
					$answerValue = "";
				}
				
				if (empty($testData['score']) && in_array($testData['type'], $gradeConfig)) {					
					switch ($testData['type']) {
						case "Fill in the Blank" :
							$testAnswers = unserialize($testData['answerValue']);
							$userAnswers = unserialize($testData['userAnswer']);
							$wrong = 0;
							
							if (empty($testAnswers[sprintf(sizeof($testAnswers) - 1)])) {
								$totalValues = sprintf(sizeof($testAnswers) - 1);
							} else {
								$totalValues = sizeof($testAnswers);
							}
							
							for ($count = 0; $count <= sizeof($userAnswers) - 1; $count ++) {
								if ($testData['case'] == "0") {
									$userValue = $userAnswers[$count];
									$testValue = $testAnswers[$count];
								} else {
									$userValue = strtolower($userAnswers[$count]);
									$testValue = strtolower($testAnswers[$count]);
								}
								
								if (trim($userValue) === trim($testValue)) {
									$wrong = $wrong;
								} else {
									$wrong = $wrong + 1;
								}
							}
							
							break;
							
						case "Matching" :
							$testQuestion = unserialize($testData['answerValue']);
							$testAnswers = unserialize($testData['answerValueScrambled']);
							$userAnswers = unserialize($testData['userAnswer']);
							$totalValues = sizeof($testAnswers);
							$wrong = 0;
							
							for ($count = 0; $count <= sizeof($testAnswers) - 1; $count ++) {
								$variable = $testAnswers[sprintf($userAnswers[$count] - 1)];
								
								if ($testQuestion[$count] === $variable) {
									$wrong = $wrong;
								} else {
									$wrong = $wrong + 1;
								}
							}
							
							break;
							
						case "Multiple Choice" :
							if ($testData['randomize'] == "1") {
								$answerCompare = unserialize($testData['answerValueScrambled']);
							} else {
								$answerCompare = unserialize($testData['questionValue']);
							}
							
							$testQuestion = unserialize($testData['questionValue']);
							$testAnswers = unserialize($testData['answerValue']);
							$userAnswers = unserialize($testData['userAnswer']);
							$correctAnswers = array();
							$wrong = 0;
							
							if (is_array($testAnswers) && sizeof($testAnswers) > 1) {
								$totalValues = sizeof($testAnswers);
								
								if (sizeof($testAnswers) > sizeof($userAnswers)) {
									$wrong = $wrong + (sizeof($testAnswers) - sizeof($userAnswers));
								}
								
								for ($count = 0; $count <= sizeof($testAnswers) - 1; $count ++) {
									array_push($correctAnswers, $testQuestion[sprintf($testAnswers[$count] - 1)]);
								}
								
								for ($count = 0; $count <= sizeof($userAnswers) - 1; $count ++) {
									if (in_array($answerCompare[sprintf($userAnswers[$count] - 1)], $correctAnswers)) {
										$wrong = $wrong;
									} else {
										$wrong = $wrong + 1;
									}
								}
							} else {
								$totalValues = 1;
								
								if ($testQuestion[sprintf($testAnswers['0'] - 1)] === $answerCompare[sprintf($userAnswers['0'] - 1)]) {
									$wrong = $wrong;
								} else {
									$wrong = $wrong + 1;
								}
							}
							
							break;
							
						case "Short Answer" :
							$testAnswers = unserialize($testData['answerValue']);
							$userAnswers = unserialize($testData['userAnswer']);
							$totalValues = 1;
							$wrong = 0;
							
							if (is_array($testAnswers)) {
								if ($testData['case'] == "0") {
									if (in_array($userAnswers, $testAnswers)) {
										$wrong = $wrong;
									} else {
										$wrong = $wrong + 1;
									}
								} else {
									if (inArray($userAnswers, $testAnswers)) {
										$wrong = $wrong;
									} else {
										$wrong = $wrong + 1;
									}
								}
							} else {
								if ($testAnswers === $userAnswers) {
									$wrong = $wrong;
								}
							}
							
							break;
							
						case "True False" :
							$testAnswers = $testData['answer'];
							$userAnswers = unserialize($testData['userAnswer']);
							$totalValues = 1;
							$wrong = 0;
							
							if (is_numeric($userAnswers) && $testAnswers == $userAnswers) {
								$wrong = $wrong;
							} else {
								$wrong = $wrong + 1;
							}
							
							break;
					}
					
					$scorePrep = number_format($testData['points'] * (($totalValues - $wrong) / $totalValues), 2);
					
					if ($testData['partialCredit'] == "0") {
						if ($scorePrep !== number_format($testData['points'], 2)) {
							$score = "0";
							$feedback = mysql_real_escape_string($testData['incorrectFeedback']);
						} else {
							$score = $scorePrep;
							$feedback = mysql_real_escape_string($testData['correctFeedback']);
						}
					} else {						
						if ($scorePrep < 0) {
							$score = "0";
						} else {
							$score = $scorePrep;
						}
						
						if ($scorePrep !== $testData['points'] && $scorePrep !== "0") {
							$feedback = mysql_real_escape_string($testData['partialFeedback']);
						} elseif ($scorePrep !== $testData['points'] && $scorePrep === "0") {
							$feedback = mysql_real_escape_string($testData['incorrectFeedback']);
						} else {
							$feedback = mysql_real_escape_string($testData['correctFeedback']);
						}
					}
					
					query("UPDATE `{$testTable}` SET `link` = '{$link}', `testPosition` = '{$position}', `randomizeTest` = '{$randomizeTest}', `randomizeQuestion` = '{$randomizeQuestion}', `extraCredit` = '{$extraCredit}', `points` = '{$points}', `score` = '{$score}', `question` = '{$question}', `questionValue` = '{$questionValue}', `testAnswer` = '{$answerValue}', `feedback` = '{$feedback}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$testDataLoop['id']}' LIMIT 1");
				} else {
					array_push($noGrade, $testData['type']);
					
					query("UPDATE `{$testTable}` SET `link` = '{$link}', `testPosition` = '{$position}', `randomizeTest` = '{$randomizeTest}', `randomizeQuestion` = '{$randomizeQuestion}', `extraCredit` = '{$extraCredit}', `points` = '{$points}', `question` = '{$question}', `questionValue` = '{$questionValue}', `testAnswer` = '{$answerValue}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$testDataLoop['id']}' LIMIT 1");
				}
				
				unset($feedback);
			}
			
			$testUpdateGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}' LIMIT 1");
			$testUpdateArray = unserialize($testUpdateGrabber['modules']);
			
			if (!empty($noGrade)) {
				$testUpdateArray[$testID]['testStatus'] = "A";
			} else {
				$testUpdateArray[$testID]['testStatus'] = "F";
			}
			
			$testUpdate = serialize($testUpdateArray);
			
			query("UPDATE `users` SET `modules` = '{$testUpdate}' WHERE `id` = '{$userData['id']}'");
			redirect("review.php?id=" . $_GET['id']);
		}
	}
	
//Delete a file from the test
	if (isset($_GET['delete']) && $_GET['delete'] == "true" && isset($_GET['questionID']) && isset($_GET['fileID'])) {
		$fileData = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$_GET['questionID']}'");
		
		if (array_key_exists(intval($_GET['fileID']) - 1, unserialize($fileData['userAnswer']))) {
			$file = unserialize($fileData['userAnswer']);
			unlink($testID . "/test/responses/" . $file[intval($_GET['fileID']) - 1]);
			unset($file[intval($_GET['fileID']) - 1]);
			$return = serialize(array_merge($file));
			query("UPDATE `{$testTable}` SET `userAnswer` = '{$return}' WHERE `testID` = '{$testID}' AND `questionID` = '{$_GET['questionID']}' AND `attempt` = '{$currentAttempt}'");
			redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
		}
	}
	
//Title
	title($moduleInfo['name'], false, false);
	
//Information bar
	echo "<div class=\"toolBar noPadding\"><strong>Directions</strong>: " . strip_tags($moduleInfo['directions']);
	
//Display a forced completion alert
	if ($moduleInfo['forceCompletion'] == "on") {
		echo "<br /><strong>Force Completion</strong>: This test must be completed now.";
	}

//Display a timer alert
	if ($moduleInfo['timer'] == "on") {
		$time = unserialize($moduleInfo['time']);
		
		if ($moduleInfo['time'] !== "") {
			$testH = $time['0'];
			$testM = $time['1'];
		}
		
		echo "<br /><strong>Time limit</strong>: This test must be completed within <strong>" . $time['0'];
		
		if ($time['0'] == "1") {
			echo " hour and ";
		} elseif ($testH !== "1") {
			echo " hours and ";
		}
		
		echo $time['1'] . " minutes</strong>.";
	}
	
//Close the information bar
	echo "</div>";
	
//Display link back to the lesson, if premitted
	if ($moduleInfo['reference'] == "1") {
		echo "<br /><div align=\"left\">" . URL("Back to Lesson", "lesson.php?id=" . $_GET['id'] . "&page=1", "previousPage") . "</div><br />";
	} else {
		echo "<p>&nbsp;</p>";
	}
	
//Display the test
	test("moduletest_" . $_GET['id'], "../../gateway.php/modules/" . $_GET['id'] . "/");

//Display link back to the lesson, if premitted
	if ($moduleInfo['reference'] == "1") {
		echo "<br /><div align=\"left\">" . URL("Back to Lesson", "lesson.php?id=" . $_GET['id'], "previousPage") . "</div>";
	}
	
//Include the footer
	footer();
?>