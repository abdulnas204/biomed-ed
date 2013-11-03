<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grab all module and test data
	$userData = userData();
	$testID = $_GET['id'];
	$parentTable = "moduletest_" . $testID;
	$testTable = "testdata_" . $userData['id'];
	$attempt = lastItem($testTable, "testID", $testID, "attempt");
	if ($attempt - 1 == 0) {
		$currentAttempt = 1;
	} else {
		$currentAttempt = $attempt - 1;
	}
	
	if (isset ($_GET['id'])) {
		$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}' LIMIT 1");
		
		if (exist("moduledata", "id", $_GET['id']) == false) {
			redirect("index.php");
		}
	} else {
		redirect("index.php");
	}
	
//Create a function to see if test questions exist
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
	if (!query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}'", false, false)) {
	//Generate the test configuration form
		$testDifficultyGrabber = query("SELECT * FROM `{$parentTable}`", "raw");
		$questionsCalc = "";
		$questions = "";
		$difficulty = "";
		$count = 1;
		
		while ($testDifficulty = mysql_fetch_array($testDifficultyGrabber)) {
			if ($testDifficulty['difficulty'] == "Easy") {
				$easy = true;
			} elseif ($testDifficulty['difficulty'] == "Average") {
				$average = true;
			} elseif ($testDifficulty['difficulty'] == "Difficult") {
				$difficult = true;
			}
		}
		
		if (isset($easy)) {
			$difficulty .= "Easy,";
		}
		
		if (isset($average)) {
			$difficulty .= "Average,";
		}
		
		if (isset($difficult)) {
			$difficulty .= "Difficult,";
		}
		
		if (isset($_GET['data'])) {
			header("Content-type: text/xml");
			
			if (isset ($_GET['difficulty'])) {
				echo "<root><level><url>all</url><difficulty>All Levels</difficulty></level>";
				
				foreach(explode(",", rtrim($difficulty, ",")) as $difficultValue) {
					echo "<level>";
					echo "<url>" . strtolower($difficultValue) . "</url>";
					echo "<difficulty>" . $difficultValue . "</difficulty>";
					echo "</level>";
				}
				
				echo "</root>";
				exit;
			}
			
			if (isset($_GET['type'])) {
				switch ($_GET['type']) {
					case "all" :
						$sql = "";
						break;
						
					case "easy" :
						$sql = " WHERE `difficulty` = 'Easy'";
						break;
						
					case "average" :
						$sql = " WHERE `difficulty` = 'Average'";
						break;
						
					case "difficult" :
						$sql = " WHERE `difficulty` = 'Difficult'";
						break;
				}
				
				if (in_array($_GET['type'], explode(",", rtrim(strtolower($difficulty), ",")))) {
					$questionsGrabber = query("SELECT `position` FROM `{$parentTable}`{$sql} AND `type` != 'Description' ORDER BY `position` DESC", "selected", false);
					
					if ($_GET['type'] != "all") {						
						if ($bankGrabber = query("SELECT * FROM `{$parentTable}` WHERE `questionBank` = '1' AND `type` != 'Description'", "raw", false)) {
							while ($bank = mysql_fetch_array($bankGrabber)) {
								if ($bank['questionBank'] == "1") {
									if ($externalCheck = query("SELECT * FROM `questionbank`{$sql} AND `id` = '{$bank['linkID']}' AND `type` != 'Description'", "array", false)) {
										while ($external = mysql_fetch_array($externalCheck)) {
											if (is_array($externalCheck) && !empty($externalCheck) && in_array($_GET['type'], $external['difficulty'])) {
												array_push($questionsGrabber, $external['position']);
											}
										}
									}
								}
							}
						}
					}
				} else {
					$questionsGrabber = query("SELECT `position` FROM `{$parentTable}` WHERE `type` != 'Description' ORDER BY `position` DESC", "selected", false);
				}
				
				foreach ($questionsGrabber as $number) {
					$questionsCalc .= $number . ",";
				}
				
				echo "<root>";
				
				for ($count = sizeof(explode(",", rtrim($questionsCalc, ","))); $count >= 1; $count--) {
					echo "<data>";
					echo "<question>" . $count . "</question>";
					echo "</data>";
				}
				
				echo "</root>";
			} else {
				$questionsGrabber = query("SELECT `position` FROM `{$parentTable}` WHERE `type` != 'Description' ORDER BY `position` DESC", "selected", false);
				
				foreach ($questionsGrabber as $number) {
					$questionsCalc .= $number . ",";
				}
				
				echo "<root>";
				
				for ($count = sizeof(explode(",", rtrim($questionsCalc, ","))); $count >= 1; $count--) {
					echo "<data>";
					echo "<question>" . $count . "</question>";
					echo "</data>";
				}
				
				echo "</root>";
			}
			
			exit;
		}
		
	//Process the form
		if (isset($_POST['submit']) && isset($_POST['difficulty']) && isset($_POST['questions'])) {
			$totalQuestions = query("SELECT * FROM `{$parentTable}` WHERE `type` != 'Description'", "num");
			$questionsPercentage = round(sprintf($_POST['questions']/$totalQuestions));
			$totalDescriptions = query("SELECT * FROM `{$parentTable}` WHERE `type` = 'Description'", "num");
			$descriptionNumber = ceil(sprintf($totalDescriptions * $questionsPercentage));
			$difficulty = $_POST['difficulty'];
			$questions = $_POST['questions'];
			$limit = $questions + $descriptionNumber;
			$count = 1;
			
			if ($difficulty == "All Levels" || $difficulty == urlencode("All Levels")) {
				$testDataGrabber = query("SELECT * FROM `{$parentTable}` ORDER BY RAND() LIMIT {$limit}", "raw");
			} else {
				$testDataGrabber = query("(SELECT * FROM `{$parentTable}` WHERE `difficulty` = '{$difficulty}' AND `type` != 'Description' ORDER BY RAND() LIMIT {$questions}) UNION (SELECT * FROM `{$parentTable}` WHERE `type` = 'Description' ORDER BY RAND() LIMIT {$descriptionNumber})", "raw");
			}
			
			query("CREATE TABLE IF NOT EXISTS `{$testTable}` (
				  `testID` int(255) NOT NULL,
				  `questionID` int(255) NOT NULL,
				  `link` int(255) NOT NULL,
				  `attempt` int(255) NOT NULL,
				  `type` longtext NOT NULL,
				  `testPosition` int(255) NOT NULL,
				  `randomPosition` int(255) NOT NULL,
				  `randomizeTest` longtext NOT NULL,
				  `randomizeQuestion` int(255) NOT NULL,
				  `extraCredit` text NOT NULL,
				  `points` varchar(5) NOT NULL,
				  `score` varchar(8) NOT NULL,
				  `question` longtext NOT NULL,
				  `matchingQuestion` longtext NOT NULL,
				  `questionValue` longtext NOT NULL,
				  `questionValueScrambled` longtext NOT NULL,
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
				
				if (!empty($testDataLoop['link']) && $testDataLoop['link'] != "0" && !in_array($testDataLoop['link'], $restrictImport)) {
					$randomPosition = $count ++;
					$questionID = $testDataLoop['link'];
					
					query("INSERT INTO `{$testTable}` (
					  `testID`, `questionID`, `link`, `attempt`, `type`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `matchingQuestion`, `questionValue`, `questionValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
					  ) VALUES (
					  '{$testID}', '{$questionID}', '', '{$attempt}', 'Description', '', '{$randomPosition}', '', '', '', '', '', '', '', '', '', '', '', ''
					  )");
					  
					array_push($restrictImport, $testDataLoop['link']);
				}
				
				if (!in_array($testDataLoop['id'], $restrictImport)) {
					$questionID = $testData['id'];
					$type = $testData['type'];
					$randomPosition = $count ++;
					
					if ($testData['type'] == "Matching" || $testData['type'] == "Multiple Choice") {
						if ($testData['type'] != "Matching") {
							$questionValue = mysql_real_escape_string($testData['questionValue']);
							$questionValueScrambledPrep = unserialize($testData['questionValue']);
						} else {
							$questionValue = mysql_real_escape_string($testData['answerValue']);
							$questionValueScrambledPrep = unserialize($testData['answerValue']);
						}
						
						shuffle($questionValueScrambledPrep);
						$questionValueScrambled = mysql_real_escape_string(serialize($questionValueScrambledPrep));
					} else {
						$questionValue = "";
						$questionValueScrambled = "";
					}
					
					if ($testData['type'] == "True False") {
						$questionValue = mysql_real_escape_string(serialize(array("1", "0")));
						$questionValueScrambledPrep = array("1", "0");
						shuffle($questionValueScrambledPrep);
						$questionValueScrambled = mysql_real_escape_string(serialize($questionValueScrambledPrep));
					}
					
					query("INSERT INTO `{$testTable}` (
						  `testID`, `questionID`, `link`, `attempt`, `type`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `matchingQuestion`, `questionValue`, `questionValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
						  ) VALUES (
						  '{$testID}', '{$questionID}', '{$testDataLoop['link']}', '{$attempt}', '{$type}', '', '{$randomPosition}', '', '', '', '', '', '', '', '{$questionValue}', '{$questionValueScrambled}', '', '', ''
						  )");
						  
					array_push($restrictImport, $testData['id']);
				}
			}
			
			redirect($_SERVER['REQUEST_URI']);
		}
			
	//Top content
		headers($moduleInfo['name'] . " Configuration", "Student", "liveUpdate", false, false, false, false, false, false, false, "<script type=\"text/javascript\">var dsDifficulty = new Spry.Data.XMLDataSet(\"" . $_SERVER['REQUEST_URI'] . "&data=xml&difficulty=true\", \"root/level\");var dsQuestions = new Spry.Data.XMLDataSet(\"" . $_SERVER['REQUEST_URI'] . "&data=xml&type={dsDifficulty::url}\", \"root/data\");</script>");
		
	//Title
		title($moduleInfo['name'] . " Configuration", "Please configure this test to best suit your needs prior to starting. Keep in mind that once these settings are set, then cannot be changed for this test, unless you decide to retake it after this session.");
		
	//Configuration form
		$possibleConfig = array("Easy", "Average", "Difficult");
		form("configuration");
		catDivider("Configuration", "one", true);
		echo "<blockquote>";
		directions("Difficulty", false);
		echo "<blockquote><p><span spry:region=\"dsDifficulty\" id=\"difficultySelector\"><select id=\"difficulty\" name=\"difficulty\" onchange=\"document.forms[0].questions.disabled = true; dsDifficulty.setCurrentRowNumber(this.selectedIndex);\"><option spry:repeat=\"dsDifficulty\" value=\"{difficulty}\">{difficulty}</option></select></span>";
		echo "</p></blockquote>";
		directions("Number of questions", false);
		echo "<blockquote><p><span spry:region=\"dsQuestions\" id=\"questionSelector\"><select id=\"questions\" name=\"questions\"><option spry:repeat=\"dsQuestions\" value=\"{question}\">{question}</option></select></span>";
		echo "</p></blockquote></blockquote>";		
		
		catDivider("Submit", "two");
		echo "<blockquote><p>";
		button("submit", "submit", "Submit", "submit");
		button("cancel", "cancel", "Cancel", "cancel", "index.php");
		echo "</p></blockquote>";
		closeForm(true, false);
		
	//Include the footer
		footer();
		exit;
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
				query("UPDATE `{$testTable}` SET `userAnswer` = '{$value}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND`questionID` = '{$key}'");
				array_push($questions, $key);
			}
		}
		
		$choiceGrabber = query("SELECT * FROM `{$testTable}` WHERE `type` = 'Multiple Choice' AND `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'", "raw");
		
		while ($choice = mysql_fetch_array($choiceGrabber)) {
			if (!in_array($choice['questionID'], $questions)) {
				query("UPDATE `{$testTable}` SET `userAnswer` = '' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND`questionID` = '{$choice['questionID']}'");
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
			$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}' LIMIT 1");
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
			
			$grab = $parentTable . ".*, " . $testTable . ".randomPosition, " . $testTable . ".userAnswer, " . $testTable . ".questionValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $parentTable . ".id = testdata_" . $userData['id'] . ".questionID";
			
			$testDataGrabber = query("SELECT {$grab} FROM `{$parentTable}`{$join}{$additionalSQL}{$order}", "raw");
			
			if (exist("moduledata", "id", $_GET['id']) == false) {
				redirect("index.php");
			}
		
		//Include only the questions that can be automatically scored
			$gradeConfig = array("Fill in the Blank", "Matching", "Multiple Choice", "Short Answer", "True False");
			
			while ($testDataLoop = mysql_fetch_array($testDataGrabber)) {
				if ($testDataLoop['questionBank'] == "1") {
					$testData = query("SELECT * FROM `questionbank` WHERE `id` = '{$testDataLoop['linkID']}'");
					$position = $testDataLoop['position'];
				} else {
					$testData = $testDataLoop;
					$position = $testData['position'];
				}
				
				$randomizeQuestion = $testData['randomize'];
				$extraCredit = $testData['extraCredit'];
				$question = mysql_real_escape_string($testData['question']);
				$matchingQuestion = mysql_real_escape_string($testData['questionValue']);
				
				if ($testData['type'] == "File Response") {
					$answerValue = mysql_real_escape_string($testData['fileURL']);
				} else {
					$answerValue = mysql_real_escape_string($testData['answerValue']);
				}
				
				$points = $testData['points'];
				
				if (empty($testData['score']) && in_array($testData['type'], $gradeConfig)) {					
					if (!empty($testData['answerValue'])) {
						$answerValue = mysql_real_escape_string($testData['answerValue']);
					} elseif (!empty($testData['answer'])) {
						$answerValue = mysql_real_escape_string($testData['answer']);
					} elseif (!empty($testData['fileURL'])) {
						$answerValue = mysql_real_escape_string($testData['fileURL']);
					} else {
						$answerValue = "";
					}
					
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
							$testAnswers = unserialize($testData['questionValueScrambled']);
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
								$answerCompare = unserialize($testData['questionValueScrambled']);
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
								
								if (sizeof($testAnswers) < sizeof($userAnswers)) {
									$wrong = sizeof($userAnswers) - sizeof($testAnswers);
								}
								
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
						$score = $scorePrep;
						
						if ($scorePrep !== $testData['points'] && $scorePrep !== "0") {
							$feedback = mysql_real_escape_string($testData['partialFeedback']);
						} elseif ($scorePrep !== $testData['points'] && $scorePrep === "0") {
							$feedback = mysql_real_escape_string($testData['incorrectFeedback']);
						} else {
							$feedback = mysql_real_escape_string($testData['correctFeedback']);
						}
					}
					
					query("UPDATE `{$testTable}` SET `testPosition` = '{$position}', `randomizeTest` = '{$randomizeTest}', `extraCredit` = '{$extraCredit}', `points` = '{$points}', `score` = '{$score}', `question` = '{$question}', `matchingQuestion` = '{$matchingQuestion}', `testAnswer` = '{$answerValue}', `feedback` = '{$feedback}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$testDataLoop['id']}' LIMIT 1");
				} else {
					query("UPDATE `{$testTable}` SET `testPosition` = '{$position}', `randomizeTest` = '{$randomizeTest}', `extraCredit` = '{$extraCredit}', `points` = '{$points}', `question` = '{$question}', `matchingQuestion` = '{$matchingQuestion}', `testAnswer` = '{$answerValue}' WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `questionID` = '{$testDataLoop['id']}' LIMIT 1");
				}
			}
			
			redirect("review.php?id=" . $_GET['id']);
		}
	}
	
//Check for updates to the test canvas, and apply them to this test
	$testDataGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' ORDER BY `questionID` ASC", "raw");
	$questionConfig = array("Matching", "Multiple Choice");
	
	while ($testData = mysql_fetch_array($testDataGrabber)) {
		$bankDataGrabber = query("SELECT * FROM `{$parentTable}` WHERE `id` = '{$testData['questionID']}'");
		
		if (!empty($bankDataGrabber['link']) && !questionExist($bankDataGrabber['id'])) {
			if ($bankDataGrabber['questionBank'] == "1") {
				$bankData = query("SELECT * FROM `questionbank` WHERE `id` = '{$bankDataGrabber['linkID']}'");
			} else {
				$bankData = query("SELECT * FROM `{$parentTable}` WHERE `id` = '{$bankDataGrabber['link']}'");
			}
			
			$shuffledPrep = unserialize($bankData['questionValue']);
			shuffle($shuffledPrep);
			$shuffled = serialize($shuffledPrep);
			$lastQuestionGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' ORDER BY `randomPosition` DESC LIMIT 1");
			$lastQuestion = $lastQuestionGrabber['position'] + 1;
			
			query("INSERT INTO `{$testTable}` (
				  `testID`, `questionID`, `link`, `attempt`, `type`, `testPosition`, `randomPosition`, `randomizeTest`, `randomizeQuestion`, `extraCredit`, `points`, `score`, `question`, `matchingQuestion`, `questionValue`, `questionValueScrambled`, `userAnswer`, `testAnswer`, `feedback`
				  ) VALUES (
				  '{$testID}', '{$bankDataGrabber['link']}', '', '{$attempt}', '{$bankData['type']}', '', '{$lastQuestion}', '', '', '', '', '', '', '', '{$bankData['questionValue']}', '{$shuffled}', '', '', ''
				  )");
		}
		
		if ($bankDataGrabber['questionBank'] == "1") {
			$bankData = query("SELECT * FROM `questionbank` WHERE `id` = '{$bankDataGrabber['linkID']}'");
		} else {
			$bankData = $bankDataGrabber;
		}
		
		if (exist($parentTable, "id", $testData['questionID'])) {
			if (in_array($bankData['type'], $questionConfig)) {
				if ($testData['type'] != "Matching") {
					$question = $bankData['questionValue'];
					$shuffledPrep = unserialize($bankData['questionValue']);
				} else {
					$question = $bankData['answerValue'];
					$shuffledPrep = unserialize($bankData['answerValue']);
				}
				
				shuffle($shuffledPrep);
				$shuffled = serialize($shuffledPrep);
				
				if ($question !== $testData['questionValue']) {
					query("UPDATE `{$testTable}` SET `questionValue` = '{$question}', `questionValueScrambled` = '{$shuffled}' WHERE `questionID` = '{$testData['questionID']}' AND `attempt` = '{$currentAttempt}'");
				}
			}
		} else {
			query("DELETE FROM `{$testTable}` WHERE `questionID` = '{$testData['questionID']}' AND `attempt` = '{$currentAttempt}' LIMIT 1");
			query("UPDATE `{$testTable}` SET `randomPosition` = randomPosition-1 WHERE `randomPosition` > '{$testData['randomPosition']}'");
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
			query("UPDATE `{$testTable}` SET `userAnswer` = '{$return}' WHERE `testID` = '{$testID}' AND `questionID` = '{$_GET['questionID']}'");
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
		echo "<br /><div align=\"left\">" . URL("Back to Lesson", "lesson.php?id=" . $_GET['id'], "previousPage") . "</div><br />";
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
