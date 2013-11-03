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
Last updated: February 14th, 2011

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
	$accessGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}'");
	$accessArray = unserialize($accessGrabber['learningunits']);
	
//Set up the user's test table, if needed
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
	
//Ensure the user has permission to access this test
	if (isset ($_GET['id']) && exist("test_" . $_GET['id'])) {		
		if (!exist("learningunits", "id", $_GET['id']) || !array_key_exists($testID, $accessArray) || empty($unitInfo['visible'])) {
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
	
	if (empty($accessArray[$testID]['submitted'])) {
		if ($attempt = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}' ORDER BY `attempt` DESC LIMIT 1", false, false)) {
			$currentAttempt = $attempt['attempt'];
		} else {
			$currentAttempt = 1;
		}
	} else {
		$attempt = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}' ORDER BY `attempt` DESC LIMIT 1");
		$currentAttempt = $attempt['attempt'] + 1;
	}
	
	$query = "SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'";
	
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
				$columns = "";
				$values = "";
				$count = 0;
				
				foreach($_GET as $key => $parameterPrep) {
					$parameter = escape(urldecode($parameterPrep));
					
					if ($key != "id" && $key != "data") {
						$columns .= " `field_{$key}`,";
					}
				}
				
				$columns = rtrim($columns, ",");
				
				foreach($_GET as $key => $parameterPrep) {
					$parameter = escape(urldecode($parameterPrep));
					
					if ($key != "id" && $key != "data") {
						$values .= " `field_{$key}` = '{$parameter}' AND";
					}
				}
				
				$questions = query("SELECT 0,{$columns} FROM `{$parentTable}` WHERE" . rtrim($values, " AND") . " UNION ALL SELECT `id`,{$columns} FROM `{$questionBank}` WHERE" . rtrim($values, " AND"), "raw");
				
				while($question = fetch($questions)) {
					if ($question['0'] == 0 || exist($parentTable, "linkID", $question['0'])) {
						$count ++;
					}
				}
				
				if ($count > 0) {
					for ($values = $count; $values >= 1; $values --) {
						echo "<group>\n";
						echo "<question>" . $values ."</question>\n";
						echo "</group>\n";
					}
				} else {
					echo "<group>\n";
					echo "<question>None Avaliable</question>\n";
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
		if (isset($_POST['setup'])) {
			$totalQuestions = query("SELECT * FROM `{$parentTable}` WHERE `type` != 'Description'", "num");
			$questionsPercentage = number_format($_POST['questions']/$totalQuestions, 2);
			$totalDescriptions = query("SELECT * FROM `{$parentTable}` WHERE `type` = 'Description'", "num");
			$descriptionNumber = ceil($totalDescriptions * $questionsPercentage);
			$values = "";
			
			foreach($_POST as $key => $parameter) {
				if ($key != "parameters" && $key != "questions" && $key != "setup" && !empty($parameter)) {
					$values .= " {$parentTable}.field_{$key} = '{$parameter}' OR {$questionBank}.field_{$key} = '{$parameter}' AND";
				}
			}
			
			if ($values != "") {
				$values = " AND (" . rtrim($values, " AND") . ")";
			}
			
			$questions = $_POST['questions'];
			$count = 1;
			
			$testDataGrabber = query("(SELECT {$parentTable}.*, {$questionBank}.* FROM `{$parentTable}` LEFT JOIN `{$questionBank}` ON {$parentTable}.linkID = {$questionBank}.id WHERE {$parentTable}.type != 'Description'{$values} ORDER BY RAND() LIMIT {$questions}) UNION (SELECT {$parentTable}.*, {$questionBank}.* FROM `{$parentTable}` LEFT JOIN `{$questionBank}` ON {$parentTable}.linkID = {$questionBank}.id WHERE {$parentTable}.type = 'Description'{$values} ORDER BY RAND() LIMIT {$descriptionNumber}) ORDER BY RAND()", "raw");
			
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
			
			while ($testDataLoop = fetch($testDataGrabber)) {
				if ($testDataLoop['questionBank'] == "1") {
					$testData = query("SELECT * FROM `{$questionBank}` WHERE `id` = '{$testDataLoop['linkID']}'");
				} else {
					$testData = $testDataLoop;
				}
				
				if (!empty($testDataLoop['link']) && $testDataLoop['link'] != "0" && exist($parentTable, "id", $testDataLoop['link']) && !in_array($testDataLoop['link'], $restrictImport)) {
					$randomPosition = $count ++;
					$questionID = $testDataLoop['link'];
					
					query("INSERT INTO `{$testTable}` (
						  `testID`, `questionID`, `attempt`, `type`, `link`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `questionValue`, `answerValue`, `answerValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
						  ) VALUES (
						  '{$testID}', '{$questionID}', '{$currentAttempt}', 'Description', '', '', '{$randomPosition}', '', '', '', '', '', '', '', '', '', '', '', ''
						  )");
					  
					array_push($restrictImport, $testDataLoop['link']);
				}
				
				if (!in_array($testDataLoop['id'], $restrictImport)) {
					$questionID = $testDataLoop['0'];
					
					if (empty($testDataLoop['type'])) {
						$type = $testDataLoop['4'];
					} else {
						$type = $testDataLoop['type'];
					}
					
					$randomPosition = $count ++;
					
					if ($testData['type'] == "Matching" || $testData['type'] == "Multiple Choice" || $testData['type'] == "True False") {
						if ($testData['type'] == "Multiple Choice") {
							$questionValue = "";
							$answerValue = escape($testData['questionValue']);
							$answerValueScrambledPrep = unserialize($testData['questionValue']);
						} elseif($testData['type'] == "Matching") {
							$questionValue = escape($testData['questionValue']);
							$answerValue = escape($testData['answerValue']);
							$answerValueScrambledPrep = unserialize($testData['answerValue']);
						} elseif ($testData['type'] == "True False") {
							$questionValue = "";
							$answerValue = escape(serialize(array("1", "0")));
							$answerValueScrambledPrep = array("1", "0");
						}
						
						shuffle($answerValueScrambledPrep);
						$answerValueScrambled = escape(serialize($answerValueScrambledPrep));
					} else {
						$questionValue = "";
						$answerValue = "";
						$answerValueScrambled = "";
					}
					
					query("INSERT INTO `{$testTable}` (
						  `testID`, `questionID`, `attempt`, `type`, `link`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `questionValue`, `answerValue`, `answerValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
						  ) VALUES (
						  '{$testID}', '{$questionID}', '{$currentAttempt}', '{$type}', '', '', '{$randomPosition}', '', '', '', '', '', '', '{$questionValue}', '{$answerValue}', '{$answerValueScrambled}', '', '', ''
						  )");
					
					array_push($restrictImport, $testDataLoop['id']);
				}
			}
			
			$accessArray[$testID]['submitted'] = "";
			$unitInfoUpdate = escape(serialize($accessArray));
			
			query("UPDATE `users` SET `learningunits` = '{$unitInfoUpdate}' WHERE `id` = '{$userData['id']}'");
			redirect($_SERVER['REQUEST_URI']);
		}
			
	//Top content
		headers($unitInfo['name'] . " Configuration", "liveUpdate", true, false, false, false, "<script type=\"text/javascript\">
  var dsQuestions = new Spry.Data.XMLDataSet(\"" . $_SERVER['REQUEST_URI'] . "&data=xml\", \"root/group\", {useCache:false});
</script>");
		
	//Title
		title($unitInfo['name'] . " Configuration", "Configure this test to best suit your needs. Keep in mind that once these settings are set, they cannot be changed, unless you decide to retake this test after this session.");
		
	//Configuration form
		echo form("configuration");
		echo hidden("parameters", "parameters", "");
		catDivider("Configuration", "one", true);
		echo "<blockquote>\n";
		
		$customFields = query("SELECT * FROM `fields` WHERE `testFilter` = '1' AND `fieldType` != 'checkbox' AND `fieldType` != 'textArea' ORDER BY `position` ASC", "raw");
		
		while ($field = fetch($customFields)) {
			if (in_array("Question Generator", unserialize($field['section']))) {				
				if ($field['showTip'] == "1") {
					$tip = strip_tags($field['description']);
				} else {
					$tip = false;
				}
				
				directions($field['name'], false, $tip);
				
			//Generate the values
				$values = array();		
				$questions = query("SELECT 0, `field_{$field['id']}` FROM `{$parentTable}` UNION ALL SELECT `id`, `field_{$field['id']}` FROM `{$questionBank}`", "raw");
				
				while($question = fetch($questions)) {
					if ($question['0'] == 0 || exist($parentTable, "linkID", $question['0'])) {
						array_push($values, $question['field_' . $field['id']]);
					}
				}
				
				sort($values);
				$values = array_filter(array_unique($values));
				
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
						
						foreach ($values as $value) {
							if (!empty($value)) {
								$items .= prepare($value, true, true) . ",";
								$IDs .= prepare($value, true, true) . ",";
							}
						}
						
						indent(dropDown($field['id'], $field['id'], rtrim($items, ","), rtrim($IDs, ","), false, false, false, false, false, false, "onchange=\"updateDataSet(this.id)\""));
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
	
	while ($testData = fetch($testDataGrabber)) {
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
			$bankData = query("SELECT * FROM `{$questionBank}` WHERE `id` = '{$bankDataGrabber['linkID']}'");
		} else {
			$bankData = $bankDataGrabber;
		}
		
		if (exist($parentTable, "id", $testData['questionID'])) {
			if (in_array($bankData['type'], $questionConfig)) {
				if ($testData['type'] == "Matching") {
					$questionValue = escape($bankData['questionValue']);
					$answerValue = escape($bankData['answerValue']);
					$answerValueScrambledPrep = unserialize($bankData['answerValue']);
					$answerCompare = $bankData['answerValue'];
				} else {
					$questionValue = "";
					$answerValue = escape($bankData['questionValue']);
					$answerValueScrambledPrep = unserialize($bankData['questionValue']);
					$answerCompare = $bankData['questionValue'];
				}
				
				shuffle($answerValueScrambledPrep);
				$answerValueScrambled = escape(serialize($answerValueScrambledPrep));
				
				if ($answerCompare !== $testData['answerValue']) {
					query("UPDATE `{$testTable}` SET `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `answerValueScrambled` = '{$answerValueScrambled}' WHERE `questionID` = '{$testData['questionID']}' AND `attempt` = '{$currentAttempt}'");
				}
			}
		} else {
			query("DELETE FROM `{$testTable}` WHERE `questionID` = '{$testData['questionID']}' AND `attempt` = '{$currentAttempt}' LIMIT 1");
			query("UPDATE `{$testTable}` SET `randomPosition` = randomPosition-1 WHERE `randomPosition` > '{$testData['randomPosition']}' AND `attempt` = '{$currentAttempt}'");
		}
	}
	
//Process the form
	if (isset($_POST['submit']) || isset($_POST['save'])) {	
		$count = 0;
		$questions = array();
		
		foreach ($_POST as $key => $answer) {
			if (exist($parentTable, "id", $key)) {
				$value = escape(serialize($answer));
				
				query("UPDATE `{$testTable}` SET `userAnswer` = '{$value}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$key}'");
				array_push($questions, $key);
			}
		}
		
		$choiceGrabber = query("SELECT * FROM `{$testTable}` WHERE `type` = 'Multiple Choice' AND `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'", "raw");
		
		while ($choice = fetch($choiceGrabber)) {
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
				$uploadDir = "unit_" . $_GET['id'] . "/test/responses";
				$fileNameArray = explode(".", $targetFile);
				$targetFile = "";
				extension($file['name']);
				
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
				
				if (move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile)) {
					$fileGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$id['0']}'");
											
					if (is_array(unserialize($fileGrabber['userAnswer']))) {
						$filesArray = unserialize($fileGrabber['userAnswer']);
					} else {
						$filesArray = array();
					}
					
					unlink($uploadDir . "/" . $filesArray[intval($id['1']) - 1]);
					$filesArray[intval($id['1']) - 1] = $targetFile;
					$value = escape(serialize($filesArray));
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
			$randomizeTest = $unitInfo['randomizeAll'];
			$additionalSQLConstruct = " WHERE ";
				
			while ($selection = fetch($selectionGrabber)) {
				$additionalSQLConstruct .= "`id` = '{$selection['questionID']}' OR ";
			}
			
			$additionalSQL = rtrim($additionalSQLConstruct, " OR ");
				
			if ($unitInfo['randomizeAll'] == "Randomize") {
				$order = " ORDER BY `randomPosition` ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = $parentTable . ".*, " . $testTable . ".randomPosition, " . $testTable . ".userAnswer, " . $testTable . ".answerValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $parentTable . ".id = testdata_" . $userData['id'] . ".questionID";
			
			$testDataGrabber = query("SELECT {$grab} FROM `{$parentTable}`{$join}{$additionalSQL}{$order}", "raw");
			
			if (exist("learningunits", "id", $_GET['id']) == false) {
				redirect("index.php");
			}
		
		//Include only the questions that can be automatically scored
			$gradeConfig = array("Fill in the Blank", "Matching", "Multiple Choice", "Short Answer", "True False");
			$noGrade = array();
			
			while ($testDataLoop = fetch($testDataGrabber)) {
				if ($testDataLoop['questionBank'] == "1") {
					$testData = query("SELECT * FROM `{$questionBank}` WHERE `id` = '{$testDataLoop['linkID']}'");
					$position = $testDataLoop['position'];
				} else {
					$testData = $testDataLoop;
					$position = $testData['position'];
				}
				
				$userSelection = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `questionID` = '{$testDataLoop['id']}' AND `attempt` = '{$currentAttempt}'");
				
				$randomizeQuestion = $testData['randomize'];
				$link = $testDataLoop['link'];
				$points = $testData['points'];
				$extraCredit = $testData['extraCredit'];
				$question = escape($testData['question']);
				$questionValue = escape($testData['questionValue']);
				
				if (!empty($testData['answerValue'])) {
					$answerValue = escape($testData['answerValue']);
				} elseif (!empty($testData['answer'])) {
					$answerValue = escape($testData['answer']);
				} elseif (!empty($testData['fileURL'])) {
					$answerValue = escape($testData['fileURL']);
				} else {
					$answerValue = "";
				}
				
				if (empty($testData['score']) && in_array($testData['type'], $gradeConfig)) {					
					switch ($testData['type']) {
						case "Fill in the Blank" :
							$testAnswers = unserialize($testData['answerValue']);
							$userAnswers = unserialize($userSelection['userAnswer']);
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
							$testAnswers = unserialize($userSelection['answerValueScrambled']);
							$userAnswers = unserialize($userSelection['userAnswer']);
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
								$answerCompare = unserialize($userSelection['answerValueScrambled']);
							} else {
								$answerCompare = unserialize($testData['questionValue']);
							}
							
							$testQuestion = unserialize($testData['questionValue']);
							$testAnswers = unserialize($testData['answerValue']);
							$userAnswers = unserialize($userSelection['userAnswer']);
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
							$userAnswers = unserialize($userSelection['userAnswer']);
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
							$userAnswers = unserialize($userSelection['userAnswer']);
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
							$feedback = escape($testData['incorrectFeedback']);
						} else {
							$score = $scorePrep;
							$feedback = escape($testData['correctFeedback']);
						}
					} else {						
						if ($scorePrep < 0) {
							$score = "0";
						} else {
							$score = $scorePrep;
						}
						
						if ($scorePrep !== $testData['points'] && $scorePrep !== "0") {
							$feedback = escape($testData['partialFeedback']);
						} elseif ($scorePrep !== $testData['points'] && $scorePrep === "0") {
							$feedback = escape($testData['incorrectFeedback']);
						} else {
							$feedback = escape($testData['correctFeedback']);
						}
					}
					
					query("UPDATE `{$testTable}` SET `link` = '{$link}', `testPosition` = '{$position}', `randomizeTest` = '{$randomizeTest}', `randomizeQuestion` = '{$randomizeQuestion}', `extraCredit` = '{$extraCredit}', `points` = '{$points}', `score` = '{$score}', `question` = '{$question}', `questionValue` = '{$questionValue}', `testAnswer` = '{$answerValue}', `feedback` = '{$feedback}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$testDataLoop['id']}' LIMIT 1");
				} else {
					if ($testData['type'] != "Description") {
						array_push($noGrade, $testData['type']);
					}
					
					query("UPDATE `{$testTable}` SET `link` = '{$link}', `testPosition` = '{$position}', `randomizeTest` = '{$randomizeTest}', `randomizeQuestion` = '{$randomizeQuestion}', `extraCredit` = '{$extraCredit}', `points` = '{$points}', `question` = '{$question}', `questionValue` = '{$questionValue}', `testAnswer` = '{$answerValue}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$testDataLoop['id']}' LIMIT 1");
				}
				
				unset($feedback);
			}
			
			$testUpdateGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}' LIMIT 1");
			$testUpdateArray = unserialize($testUpdateGrabber['learningunits']);
			
			if (!empty($noGrade)) {
				$testUpdateArray[$testID]['testStatus'] = "A";
			} else {
				$testUpdateArray[$testID]['testStatus'] = "F";
			}
			
			$testUpdateArray[$testID]['submitted'] = time();
			$testUpdate = serialize($testUpdateArray);
			
			query("UPDATE `users` SET `learningunits` = '{$testUpdate}' WHERE `id` = '{$userData['id']}'");
			redirect("review.php?id=" . $_GET['id'] . "&attempt=" . $currentAttempt);
		}
	}
	
//Delete a file from the test
	if (isset($_GET['delete']) && $_GET['delete'] == "true" && isset($_GET['questionID']) && isset($_GET['fileID'])) {
		$fileData = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$_GET['questionID']}'");
		
		if (array_key_exists(intval($_GET['fileID']) - 1, unserialize($fileData['userAnswer']))) {
			$file = unserialize($fileData['userAnswer']);
			unlink("unit_" . $testID . "/test/responses/" . $file[intval($_GET['fileID']) - 1]);
			unset($file[intval($_GET['fileID']) - 1]);
			$return = serialize(array_merge($file));
			query("UPDATE `{$testTable}` SET `userAnswer` = '{$return}' WHERE `testID` = '{$testID}' AND `questionID` = '{$_GET['questionID']}' AND `attempt` = '{$currentAttempt}'");
			redirect($_SERVER['PHP_SELF'] . "?id=" . $_GET['id']);
		}
	}
	
//Top content
	headers($unitInfo['name'], "tinyMCESimple,newObject", true);
	
//Title
	title($unitInfo['name'], false, false);
	
//Information bar
	echo "<div class=\"toolBar noPadding\">\n<strong>Directions</strong>: " . strip_tags($unitInfo['directions']) . "\n";
	
//Display a forced completion alert
	if ($unitInfo['forceCompletion'] == "on") {
		echo "<br />\n<strong>Force Completion</strong>: This test must be completed now.\n";
	}

//Display a timer alert
	if ($unitInfo['timer'] == "on") {
		$time = unserialize($unitInfo['time']);
		
		if ($unitInfo['time'] !== "") {
			$testH = $time['0'];
			$testM = $time['1'];
		}
		
		echo "<br />\n<strong>Time limit</strong>: This test must be completed within <strong>" . $time['0'];
		
		if ($time['0'] == "1") {
			echo " hour and ";
		} elseif ($testH !== "1") {
			echo " hours and ";
		}
		
		echo $time['1'] . " minutes</strong>.\n";
	}
	
//Close the information bar
	echo "</div>\n";
	
//Display link back to the lesson, if premitted
	$lastPage = query("SELECT * FROM `lesson_{$_GET['id']}` ORDER BY `position` DESC LIMIT 1");
	
	if ($unitInfo['reference'] == "1") {
		echo "<br />\n<div align=\"left\">" . URL("Back to Lesson", "lesson.php?id=" . $_GET['id'] . "&page=" . $lastPage['position'], "previousPage") . "</div>\n<br />\n";
	} else {
		echo "<p>&nbsp;</p>\n";
	}
	
//Display an error alert for failed file uploads
	message("error", "upload", "error", "One or more files have failed to upload. Ensure that you did not cancel the upload, or that the files did not exceed the maxmium upload size.");
	
//Display the test
	test("test_" . $_GET['id'], "preview.php/unit_" . $_GET['id'] . "/");

//Display link back to the lesson, if premitted
	if ($unitInfo['reference'] == "1") {
		echo "<br />\n<div align=\"left\">" . URL("Back to Lesson", "lesson.php?id=" . $_GET['id'] . "&page=" . $lastPage['position'], "previousPage") . "</div>\n";
	}
	
//Include the footer
	footer();
?>