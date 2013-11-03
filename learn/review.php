<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Review Test", "Student,Site Administrator", "validate,calculate", true);
	
//Grab all module and test data
	$userData = userData();
	$testID = $_GET['id'];
	$parentTable = "moduletest_" . $testID;
	$testTable = "testdata_" . $userData['id'];
	$attempt = lastItem($testTable, "testID", $testID, "attempt");
	$currentAttempt = $attempt - 1;
	$updateGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}'");
	$updateArray = unserialize($updateGrabber['modules']);
	
	if (($updateArray[$testID]['testStatus'] == "A" || $updateArray[$testID]['testStatus'] == "F") && isset ($_GET['id']) && exist($testTable, "testID", $testID)) {
		$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$testID}' LIMIT 1");
		$randomize = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' LIMIT 1");
		
		if ($randomize['randomizeTest'] == "Randomize") {
			$testDataGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' ORDER BY `randomPosition` ASC", "raw");
		} else {
			$testDataGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' ORDER BY `testPosition` ASC", "raw");
		}
	} else {
		redirect("index.php");
	}
	
//Process the form
	if (isset($_POST['submit'])) {
		foreach ($_POST as $key => $score) {
			$id = str_replace("score_", "", $key);
			query("UPDATE `{$testTable}` SET `score` = '{$score}' WHERE `testID` = '{$testID}' AND `questionID` = '{$id}' AND `attempt` = '{$currentAttempt}' LIMIT 1");
		}
		
		$updateArray[$testID]['testStatus'] = "F";
		$update = serialize($updateArray);
		
		query("UPDATE `users` SET `modules` = '{$update}' WHERE `id` = '{$userData['id']}'");
		redirect($_SERVER['REQUEST_URI']);
	}
	
//Display the test results
	form("review");
	echo "<table class=\"dataTable\">";
	$count = 1;
	$restrictImport = array();
	$values = unserialize($moduleInfo['display']);
	$score = false;
	$selectedAnswers = false;
	$correctAnswers = false;
	$feedback = false;
	
	if (is_array($values)) {
		foreach($values as $setting) {
			switch ($setting) {
				case "1" : $score = true; break;
				case "2" : $selectedAnswers = true; break;
				case "3" : $correctAnswers = true; break;
				case "4" : $feedback = true; break;
			}
		}
	}
	
	$submitVerifyGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `type` != 'Description'", "raw");
	
	while ($submitVerify = mysql_fetch_array($submitVerifyGrabber)) {
		if (empty($submitVerify['score']) && !is_numeric($submitVerify['score'])) {
			$submit = true;
		}
	}
	
	if (isset($submit)) {
		title("Review Test", "There are several questions in this test which require manual grading. Please scroll down and locate the test question(s) which require grading (indicated by a gray background). Some questions may be accompanied by a sample answer provided by the module author. Compare your answer with the one provided and enter the appropriate score in the text field located under the question number.");
	} else {
		$pointsGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `type` != 'Description'", "raw");
		$pointValue = 0;
		$totalExtraCredit = 0;
		$extraCredit = 0;
		$scorePrep = 0;
		
		while ($points = mysql_fetch_array($pointsGrabber)) {			
			if ($points['extraCredit'] == "on") {
				$totalExtraCredit = $totalExtraCredit + $points['points'];
				$extraCredit = $extraCredit + $points['score'];
			} else {
				$pointValue = $pointValue + $points['points'];
				$scorePrep = $scorePrep + $points['score'];
			}
			
			$score = $scorePrep + $extraCredit;
		}
		
		title("Review Test", "Below are the results to your test.", false);
		
	//Display the test information
		echo "<br /><br /><div class=\"toolBar noPadding\">";
		echo "<strong>Score</strong>: " . $score . " out of " . $pointValue . " ";
		
		if (intval($pointValue) == 1) {
			echo "point";
		} else {
			echo "points";
		}
		
		if (intval($totalExtraCredit) > 0) {
			echo "<br /><strong>Extra Credit</strong>: " . $extraCredit . " out of " . $totalExtraCredit . " extra credit ";
			
			if (intval($totalExtraCredit) == 1) {
				echo "point";
			} else {
				echo "points";
			}
		}
		
		echo "<br /><strong>Grade</strong>: " . grade($score, $pointValue);
		
		echo "</div><br />";
	}
	
	while ($testData = mysql_fetch_array($testDataGrabber)) {	
		if ($testData['link'] != "0" && !empty($testData['link']) && !in_array($testData['link'], $restrictImport)) {
			$linkData = query("SELECT * FROM `{$testTable}` WHERE `questionID` = '{$testData['link']}'");
			array_push($restrictImport, $testData['link']);
			echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($linkData['question'], false, true) . "</td></tr>";
			unset($linkData);
		}
		
		if ($testData['type'] != "Description") {
			echo "<tr";
			if (empty($testData['score']) && $testData['score'] !== "0") {echo " class=\"attention\">";} else {echo ">";}
			echo "<td width=\"100\" valign=\"top\"><p>";
			echo "<span class=\"questionNumber\">Question " . $count++ . "</span><br />";
			
			if (empty($testData['score']) && $testData['score'] !== "0" && isset($submit)) {
				echo "<br />";
				textField("score_" . $testData['questionID'], "score_" . $testData['questionID'], "5", "5", false, true, ",custom[onlyNumber]", false, "testData", "score", " onkeyup=\"calculate('score_" . $testData['questionID'] . "', '" . $testData['points'] . "', 'calculate_" . $testData['questionID'] . "');\" tabindex=\"" . $count . "\"");
				echo " / " . $testData['points'];
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "<div align=\"center\">";
				textField("calculate_" . $testData['questionID'], "calculate_" . $testData['questionID'], "7", "7", false, false, false, false, false, false, " class=\"calculate\" onclick=\"blur()\"");
				echo "</div>";
			} else {
				if (strstr($testData['score'], ".")) {
					$scoreFormatPrep = explode("." , $testData['score']);
					
					if ($scoreFormatPrep['1'] == 0) {
						$scoreFormat = $scoreFormatPrep['0'];
					} else {
						$scoreFormat = $testData['score'];
					}
				} else {
					$scoreFormat = $testData['score'];
				}
				
				echo "<span class=\"questionPoints\">" . $scoreFormat . " / " . $testData['points'] . " ";
				
				if ($testData['score'] == "1") {
					echo "Point";
				} else {
					echo "Points";
				}
				
				echo "</span>";
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "<br /><br /><div align=\"center\"><img src=\"../system/images/common/";
				
				if (intval($testData['score']) === intval($testData['points'])) {
					echo "correct";
				}
				
				if (intval($testData['score']) < intval($testData['points']) && intval($testData['score']) !== 0) {
					echo "partial";
				}
				
				if (intval($testData['score']) === 0) {
					echo "incorrect";
				}
				
				echo ".png\"></div>";
			}
			
			echo "</p></td><td valign=\"top\">" . prepare($testData['question'], false, true) . "<br /><br />";
		}
		
		switch ($testData['type']) {
			case "Description" : 
				if (!in_array($testData['questionID'], $restrictImport)) {
					echo "<tr><td colspan=\"2\" valign=\"top\">" . $testData['question'] . "</td></tr>";
					array_push($restrictImport, $testData['questionID']);
				}
				
				break;
			case "Essay" : 
				if (empty($testData['score']) || $selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote>";
					
					if (!empty ($testData['userAnswer'])) {
						echo unserialize($testData['userAnswer']);
					} else {
						echo "<span class=\"notAssigned\">None Given</span>";
					}
					
					echo "</blockquote>";
				}
				
				if (!empty($testData['testAnswer']) && (empty($testData['score']) || $correctAnswers == true)) {
					echo "<p>Correct Answer: </p><blockquote>" . $testData['testAnswer'] . "</blockquote>";
				}
					
				break;
				
			case "File Response" : 
				$fillValue = unserialize($testData['userAnswer']);
				
				if (empty($testData['score']) || ($selectedAnswers == true && !empty($testData['userAnswer']))) {
					echo "<p>Selected Answers: </p><ol>";
					
					foreach ($fillValue as $file) {
						if (file_exists($_GET['id'] . "/test/responses/" . $file)) {
							echo "<li><a href=\"../gateway.php/modules/" . $_GET['id'] . "/test/responses/" . urlencode($file) . "\" target=\"_blank\">" . $file . "</a></li>";
						} else {
							echo "<li>" . $file . " <span class=\"notAssigned\">File deleted</span></li>";
						}
					}
					
					echo "</ol>";
				} else {
					echo "<p>Selected Answer: </p><span class=\"notAssigned\">None Given</span>";
				}
				
				if (!empty($testData['testAnswer']) && (empty($testData['score']) || $correctAnswers == true)) {
					echo "<p>Correct Answer: </p><blockquote>";
					
					if (file_exists($_GET['id'] . "/test/answers/" . $testData['testAnswer'])) {
						echo "<a href=\"../gateway.php/modules/" . $_GET['id'] . "/test/answers/" . urlencode($testData['testAnswer']) . "\" target=\"_blank\">" . $testData['testAnswer'] . "</a>";
					} elseif (file_exists("QuestionBank/test/answers/" . $testData['testAnswer'])) {
						echo "<a href=\"../gateway.php/modules/QuestionBank/test/answers/" . urlencode($testData['testAnswer']) . "\" target=\"_blank\">" . $testData['testAnswer'] . "</a>";
					} else {
						echo $testData['testAnswer'] . " <span class=\"notAssigned\">File deleted</span>";
					}
					
					echo "</blockquote>";
				}
				
				break;
				
			case "Fill in the Blank" : 
				$sentenceValues = unserialize($testData['questionValue']);
				$correctAnswer = unserialize($testData['testAnswer']);
				$userAnswer = unserialize($testData['userAnswer']);
				
				if (empty($testData['score']) || $selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote>";
					
					for ($list = 0; $list <= sizeof($sentenceValues) - 1; $list ++) {
						echo $sentenceValues[$list];
						
						if ($list <= sizeof($sentenceValues) - 1 && isset($userAnswer[$list]) && !empty($userAnswer[$list])) {
							echo " <strong>" . $userAnswer[$list] . "</strong> ";
						} else {
							if (!empty($correctAnswer[$list])) {
								echo " <strong><span class=\"notAssigned\">None Given</span></strong> ";
							}
						}
					}
					
					echo "</blockquote>";
				}
				
				if (empty($testData['score']) || $correctAnswers == true) {
					echo "<p>Correct Answer: </p><blockquote>";
					
					for ($list = 0; $list <= sizeof($sentenceValues) - 1; $list ++) {
						echo $sentenceValues[$list];
						
						if ($list <= sizeof($sentenceValues) - 1 && isset($correctAnswer[$list])) {
							echo " <strong>" . $correctAnswer[$list] . "</strong> ";
						}
					}
					
					echo "</blockquote>";
				}
				
				break;
			
			case "Matching" : 
				$questionValue = unserialize($testData['questionValue']);
				$userAnswer = unserialize($testData['userAnswer']);
				$answerValues = unserialize($testData['answerValueScrambled']);
				$correctAnswer = unserialize($testData['testAnswer']);
				
				if (empty($testData['score']) || $selectedAnswers == true || $correctAnswers == true) {
					echo "<table width=\"100%\" class=\"dataTable\"><tr><th class=\"tableHeader\" width=\"200\">Question</th>";
					
					if (empty($testData['score']) || $selectedAnswers == true) {
						echo "<th class=\"tableHeader\" width=\"200\">Selected Answers</th>";
					}
					
					if (empty($testData['score']) || $correctAnswers == true) {
						echo "<th class=\"tableHeader\" width=\"200\">Correct Answers</th>";
					}
					
					echo "</tr>";
				
					for ($list = 0; $list <= sizeof($questionValue) - 1; $list++) {
						echo "<tr";
			  			if (sprintf($list + 1) & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
						
						echo "<td width=\"200\"><p>" . $questionValue[$list] . "</p></td>";
						
						if (empty($testData['score']) || $selectedAnswers == true) {
							echo "<td width=\"200\"><p>" . $answerValues[sprintf($userAnswer[$list] - 1)] . "</p></td>";
						}
						
						if (empty($testData['score']) || $correctAnswers == true) {
							echo "<td width=\"200\"><p>" . $correctAnswer[$list] . "</p></td>";
						}
						
						echo "</tr>";
					}
				}
				
				echo"</table>";				  
				break;
			
			case "Multiple Choice" : 
				$answers = unserialize($testData['userAnswer']);
				$correctAnswer = unserialize($testData['testAnswer']);
				$correctAnswerValues = unserialize($testData['answerValue']);
				
				if ($testData['randomizeQuestion'] == "1") {
					$choices = unserialize($testData['answerValueScrambled']);
				} else {
					$choices = unserialize($testData['answerValue']);
				}
								
				if (empty($testData['score']) || $selectedAnswers == true) {				
					if (is_array($answers) && sizeof($answers) > 1) {
						echo "<p>Selected Answers: </p>";
						echo "<ul>";
						
						for ($list = 0; $list <= sizeof($answers) - 1; $list ++) {
							echo "<li>" . $choices[sprintf($answers[$list] - 1)] . "</li>";
						}
						
						echo "</ul>";
					} else {
						echo "<p>Selected Answer: </p>";
						
						if (!empty($answers['0'])) {
							echo "<blockquote><p>" . $choices[sprintf($answers['0'] - 1)] . "</p></blockquote>";
						} else {
							echo "<blockquote><p><span class=\"notAssigned\">None Given</span></p></blockquote>";
						}
					}
				}
				
				if (empty($testData['score']) || $correctAnswers == true) {				
					if (is_array($choices) && sizeof($correctAnswer) > 1) {
						echo "<p>Correct Answers: </p>";
						echo "<ul>";
						
						for ($list = 0; $list <= sizeof($correctAnswer) - 1; $list ++) {
							echo "<li>" . $correctAnswerValues[sprintf($correctAnswer[$list] - 1)] . "</li>";
						}
						
						echo "</ul>";
					} else {						
						echo "<p>Correct Answer: </p>";
						echo "<blockquote><p>" . $correctAnswerValues[sprintf($correctAnswer['0'] - 1)] . "</p></blockquote>";
					}
				}
				
				break;
				
			case "Short Answer" : 
				if (empty($testData['score']) || $selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote><p><strong>" . unserialize($testData['userAnswer']) . "</strong></p></blockquote>";
				}
				
				if (empty($testData['score']) || $correctAnswers == true) {				
					if (is_array(unserialize($testData['testAnswer']))) {
						echo "<p>Correct Answers: </p>";
						echo "<ul>";
						
						foreach (unserialize($testData['testAnswer']) as $correctAnswer) {
							echo "<li>" . $correctAnswer . "</li>";
						}
						
						echo "</ul>";
					} else {
						echo "<p>Correct Answer: </p>";
						echo "<p><strong>" . unserialize($testData['testAnswer']) . "</strong></p></blockquote>";
					}
				}
				
				break;
				
			case "True False" : 
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote><p>";
					
					if (unserialize($testData['userAnswer']) == "1") {
						echo "True";
					} else {
						echo "False";
					}
					
					echo "</p></blockquote>";
				}
				
				if ($correctAnswers == true) {
					echo "<p>Correct Answer: </p><blockquote><p>";
					
					if ($testData['testAnswer'] == "1") {
						echo "True";
					} else {
						echo "False";
					}
					
					echo "</p></blockquote>";
				}
				
				break;
		}
		
		if ($feedback == true && !empty($testData['feedback'])) {
			echo "<p>Feedback :</p><blockquote>" . $testData['feedback'] . "</blockquote>";
		}
		
		if ($testData['type'] != "Description") {
			echo "<br /><br /></td></tr>";
		}
	}
	
	echo "</table>";
	
	if (isset($submit)) {
		echo "<blockquote><p>";
		button("submit", "submit", "Submit Scores", "submit");
		echo "</p></blockquote>";
	} else {
		echo "<blockquote><p>";
		button("submit", "submit", "Finish", "button", "index.php");
		echo "</p></blockquote>";
	}
	
	closeForm(false, true);

//Include the footer
	footer();
?>