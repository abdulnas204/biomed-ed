<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grab all module and test data
	$userData = userData();
	$testID = $_GET['id'];
	$parentTable = "moduletest_" . $testID;
	$testTable = "testdata_" . $userData['id'];
	
	if (isset ($_GET['id'])) {
		$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}' LIMIT 1");
		
		if (exist("moduledata", "id", $_GET['id']) == false) {
			redirect("index.php");
		}
	} else {
		redirect("index.php");
	}
	
//If the test is left unconfigured, then prompt the user to configure it before taking the test
	if (!query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = {$_GET['id']}", false, false)) {
	//Generate the test configuration form
		$testDifficultyGrabber = query("SELECT * FROM `moduletest_{$_GET['id']}`", "raw");
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
					$questionsGrabber = query("SELECT `position` FROM `moduletest_{$_GET['id']}`{$sql} AND `type` != 'Description' ORDER BY `position` DESC", "selected", false);
					
					if ($_GET['type'] != "all") {						
						if ($bankGrabber = query("SELECT * FROM `moduletest_{$_GET['id']}` WHERE `questionBank` = '1' AND `type` != 'Description'", "raw", false)) {
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
					$questionsGrabber = query("SELECT `position` FROM `moduletest_{$_GET['id']}` WHERE `type` != 'Description' ORDER BY `position` DESC", "selected", false);
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
				$questionsGrabber = query("SELECT `position` FROM `moduletest_{$_GET['id']}` WHERE `type` != 'Description' ORDER BY `position` DESC", "selected", false);
				
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
			$descriptionNumber = query("SELECT * FROM `moduletest_{$testID}` WHERE `type` = 'Description'", "num");
			$difficulty = $_POST['difficulty'];
			$questions = $_POST['questions'] + $descriptionNumber;
			$count = 0;
			
			if ($difficulty == "All Levels" || $difficulty == urlencode("All Levels")) {
				$testDataGrabber = query("SELECT * FROM `moduletest_{$testID}` ORDER BY RAND() LIMIT {$questions}", "raw");
			} else {
				$testDataGrabber = query("SELECT * FROM `moduletest_{$testID}` WHERE `difficulty` = '{$difficulty}' ORDER BY RAND() LIMIT {$questions}", "raw");
			}
			
			query("CREATE TABLE IF NOT EXISTS `testdata_{$userData['id']}` (
					  `testID` int(255) NOT NULL,
					  `questionID` int(255) NOT NULL,
					  `randomPosition` int(255) NOT NULL,
					  `question` longtext NOT NULL,
					  `answer` longtext NOT NULL,
					  `score` int(5) NOT NULL,
					  `feedback` longtext NOT NULL
					)");
			
			while ($testData = mysql_fetch_array($testDataGrabber)) {
				$count ++;
				$allowedArray = array("Matching", "Multiple Choice", "True False");
				
				if ($testData['randomize'] == "1" || in_array($testData['type'], $allowedArray)) {
					if ($testData['type'] == "True False") {
						$questionValue = array("1", "0");
					} else {
						$questionValue = $testData['answerValue'];
					}
					
					$shuffledValue = unserialize($questionValue);
					shuffle($shuffledValue);
					
					$question = serialize(array(unserialize($questionValue), $shuffledValue));
				} else {
					$question = "";
				}
				
				$questionID = $testData['id'];
									
				query("INSERT INTO `testdata_{$userData['id']}` (
					  `testID`, `questionID`, `randomPosition`, `question`, `answer`, `score`, `feedback`
					  ) VALUES (
					  '{$testID}', '{$questionID}', '{$count}', '{$question}', '', '', ''
					  )");
				
				unset($question);
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
	headers($moduleInfo['name'], "Student,Site Administrator", "tinyMCESimple", true);
	
//Process the form
	if (isset($_POST['submit']) || isset($_POST['save'])) {	
		$count = 0;
		
		foreach ($_POST as $key => $answer) {
			if (exist($parentTable, "id", $key)) {
				$value = mysql_real_escape_string(serialize($answer));
				query("UPDATE `{$testTable}` SET `answer` = '{$value}' WHERE `testID` = '{$testID}' AND `questionID` = '{$key}'");
			}
		}
		
		foreach ($_FILES as $file) {
			$count ++;
			$selectionGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}'", "raw");
			$additionalSQLConstruct = "";
			
			while ($selection = mysql_fetch_array($selectionGrabber)) {
				$additionalSQLConstruct .= "`id` = '{$selection['questionID']}' OR ";
			}
			
			$additionalSQL = " WHERE `type` = 'File Response' AND (" . rtrim($additionalSQLConstruct, " OR ") . ")";
			
			if ($count > 1) {
				$order = " ORDER BY `randomPosition` DESC";
			} else {
				$order = " ORDER BY `randomPosition` ASC";
			}
			
			$grab = "{$parentTable}.*, {$testTable}.randomPosition";
			$join = " LEFT JOIN {$testTable} ON {$parentTable}.id = {$testTable}.questionID";
			$questionID = query("SELECT {$grab} FROM `{$parentTable}`{$join}{$additionalSQL}{$order} LIMIT {$count}");
			
			if (is_uploaded_file($file['tmp_name']['0'])) {
				$tempFile = $file['tmp_name']['0'];
				$targetFile = basename($file['name']['0']);
				$uploadDir = $_GET['id'] . "/test/responses";
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
				
				
				if (move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile)) {
					$value = mysql_real_escape_string(serialize($targetFile));
					$fileGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `questionID` = '{$questionID['id']}'");
					
					if (!empty($fileGrabber['answer'])) {
						unlink($uploadDir . "/" . unserialize($fileGrabber['answer']));
					}
					
					query("UPDATE `{$testTable}` SET `answer` = '{$value}' WHERE `testID` = '{$testID}' AND `questionID` = '{$questionID['id']}'");
				} else {
					$errors = true;
				}
			}
			
			unset ($questionID);
		}
		
		if (isset($_POST['save'])) {
			if (isset($errors)) {
				redirect($_SERVER['REQUEST_URI'] . "&error=upload");
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
			redirect("review.php?id=" . $_GET['id']);
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
