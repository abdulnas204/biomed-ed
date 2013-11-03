<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: September 1st, 2010
Last updated: February 24th, 2011

This script is dedicated to displaying the results of a 
test, and grading questions which could not be automatically
graded.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Review Test", "validate,calculate", true);
	
//Grab all learning unit settings and test data
	$testID = $_GET['id'];
	$parentTable = "test_" . $testID;
	$testTable = "testdata_" . $userData['id'];
	$questionBank = "questionbank_0";
	$updateGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}'");
	$updateArray = arrayRevert($updateGrabber['learningunits']);
	
	if (!exist("testdata_" . $userData['id'], "testID", $_GET['id'])) {
		redirect("index.php");
	}
	
	$attempt = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$_GET['id']}' ORDER BY `attempt` DESC LIMIT 1");
	
	if (!isset($_GET['attempt']) && $attempt['attempt'] == "1") {
		$currentAttempt = 1;
	} elseif (isset($_GET['attempt']) && $_GET['attempt'] <= $attempt['attempt']) {
		$currentAttempt = $_GET['attempt'];
	}
	
	if (isset($currentAttempt)) {
		if (($updateArray[$testID]['testStatus'] == "A" || $updateArray[$testID]['testStatus'] == "F") && isset ($_GET['id']) && exist($testTable, "testID", $testID)) {
			$unitInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$testID}' LIMIT 1");
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
			$update = arrayStore($updateArray);
			
			query("UPDATE `users` SET `learningunits` = '{$update}' WHERE `id` = '{$userData['id']}'");
			redirect($_SERVER['REQUEST_URI']);
		}
		
	//Display the test results
		echo form("review");
		$count = 1;
		$restrictImport = array();
		$values = arrayRevert($unitInfo['display']);
		$displayScore = false;
		$selectedAnswers = false;
		$correctAnswers = false;
		$feedback = false;
		
		if (is_array($values)) {
			foreach($values as $setting) {
				switch ($setting) {
					case "1" : $displayScore = true; break;
					case "2" : $selectedAnswers = true; break;
					case "3" : $correctAnswers = true; break;
					case "4" : $feedback = true; break;
				}
			}
		}
		
		$submitVerifyGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `type` != 'Description'", "raw");
		
		while ($submitVerify = fetch($submitVerifyGrabber)) {
			if (empty($submitVerify['score']) && !is_numeric($submitVerify['score'])) {
				$submit = true;
			}
		}
		
		if (isset($submit)) {
			title("Review Test", "There are several questions in this test which require manual grading. Please scroll down and locate the test question(s) which require grading (indicated by a gray background). Some questions may be accompanied by a sample answer provided by the test author. Compare your answer with the one provided and enter the appropriate score in the text field located under the question number.");
		} else {
			$pointsGrabber = query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}' AND `type` != 'Description'", "raw");
			$pointValue = 0;
			$totalExtraCredit = 0;
			$extraCredit = 0;
			$scorePrep = 0;
			
			while ($points = fetch($pointsGrabber)) {			
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
			echo "<br /><br />\n<div class=\"toolBar noPadding\">\n";
			echo "<strong>Score</strong>: " . $score . " out of " . $pointValue . " ";
			
			if (intval($pointValue) == 1) {
				echo "point\n";
			} else {
				echo "points\n";
			}
			
			if (intval($totalExtraCredit) > 0) {
				echo "<br />\n<strong>Extra Credit</strong>: " . $extraCredit . " out of " . $totalExtraCredit . " extra credit ";
				
				if (intval($totalExtraCredit) == 1) {
					echo "point\n";
				} else {
					echo "points\n";
				}
			}
			
			echo "<br />\n<strong>Grade</strong>: " . grade($score, $pointValue);
			
			echo "\n</div>\n<br />\n";
		}
		
		echo "<table class=\"dataTable\">\n";
		
		while ($testData = fetch($testDataGrabber)) {	
			if ($testData['link'] != "0" && !empty($testData['link']) && !in_array($testData['link'], $restrictImport)) {
				$linkData = query("SELECT * FROM `{$testTable}` WHERE `questionID` = '{$testData['link']}'");
				array_push($restrictImport, $testData['link']);
				echo "<tr>\n<td colspan=\"2\" valign=\"top\">" . prepare($linkData['question'], false, true) . "</td>\n</tr>\n";
				unset($linkData);
			}
			
			if ($testData['type'] != "Description") {
				echo "<tr";
				if (empty($testData['score']) && $testData['score'] !== "0") {echo " class=\"attention\">\n";} else {echo ">\n";}
				echo "<td width=\"100\" valign=\"top\">\n<p>";
				echo "<span class=\"questionNumber\">Question " . $count++ . "</span>\n<br />";
				
				if (empty($testData['score']) && $testData['score'] !== "0" && isset($submit)) {
					echo "<br />";
					echo textField("score_" . $testData['questionID'], "score_" . $testData['questionID'], "5", "5", false, true, "custom[onlyNumber]", false, "testData", "score", " onkeyup=\"calculate('score_" . $testData['questionID'] . "', '" . $testData['points'] . "', 'calculate_" . $testData['questionID'] . "');\" tabindex=\"" . $count . "\"");
					echo " / " . $testData['points'] . "\n";
					
					if ($testData['extraCredit'] == "on") {
						echo "<br /><br />\n<span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>\n";
					}
					
					echo "</p>\n<div align=\"center\">";
					echo textField("calculate_" . $testData['questionID'], "calculate_" . $testData['questionID'], "7", "7", false, false, false, false, false, false, " class=\"calculate\" onclick=\"blur()\"");
					echo "</div>\n";
				} else {
					if ($displayScore == true) {
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
						
						echo "</span>\n";
					}
					
					if ($testData['extraCredit'] == "on") {
						echo "<br /><br />\n<span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>\n";
					}
					
					echo "</p>\n";
					
					if ($displayScore == true) {
						echo "<div align=\"center\"><img src=\"system/images/common/";
						
						if (intval($testData['score']) === intval($testData['points'])) {
							echo "correct";
						}
						
						if (intval($testData['score']) < intval($testData['points']) && intval($testData['score']) !== 0) {
							echo "partial";
						}
						
						if (intval($testData['score']) === 0) {
							echo "incorrect";
						}
						
						echo ".png\"></div>\n";
					}
				}
				
				echo "</td>\n<td valign=\"top\">" . prepare($testData['question'], false, true) . "\n<br /><br />\n";
			}
			
			switch ($testData['type']) {
				case "Description" : 
					if (!in_array($testData['questionID'], $restrictImport)) {
						echo "<tr>\n<td colspan=\"2\" valign=\"top\">\n" . $testData['question'] . "\n</td>\n</tr>\n";
						array_push($restrictImport, $testData['questionID']);
					}
					
					break;
				case "Essay" : 
					if (empty($testData['score']) || $selectedAnswers == true) {
						echo "<p>Selected Answer: </p>\n<blockquote>\n";
						
						if (!empty ($testData['userAnswer'])) {
							echo arrayRevert($testData['userAnswer']);
						} else {
							echo "<span class=\"notAssigned\">None Given</span>";
						}
						
						echo "\n</blockquote>\n";
					}
					
					if (!empty($testData['testAnswer']) && (empty($testData['score']) || $correctAnswers == true)) {
						echo "<p>Correct Answer: </p>\n<blockquote>\n" . $testData['testAnswer'] . "\n</blockquote>\n";
					}
						
					break;
					
				case "File Response" : 
					$fillValue = arrayRevert($testData['userAnswer']);
					
					if (empty($testData['score']) || ($selectedAnswers == true && !empty($testData['userAnswer']))) {
						echo "<p>Selected Answers: </p>\n<ol>\n";
						
						foreach ($fillValue as $file) {
							if (file_exists("unit_" . $_GET['id'] . "/test/responses/" . $file)) {
								echo "<li><a href=\"preview.php/unit_" . $_GET['id'] . "/test/responses/" . urlencode($file) . "\" target=\"_blank\">" . $file . "</a></li>\n";
							} else {
								echo "<li>" . $file . " <span class=\"notAssigned\">File deleted</span></li>\n";
							}
						}
						
						echo "</ol>\n";
					} else {
						if ($selectedAnswers == true) {
							echo "<p>Selected Answer: </p>\n<blockquote>\n<p><span class=\"notAssigned\">None Given</span></p>\n</blockquote>\n";
						}
					}
					
					if (!empty($testData['testAnswer']) && (empty($testData['score']) || $correctAnswers == true)) {
						echo "<p>Correct Answer: </p>\n<blockquote>\n";
						
						if (file_exists($_GET['id'] . "/test/answers/" . $testData['testAnswer'])) {
							echo "<a href=\"preview.php/unit_" . $_GET['id'] . "/test/answers/" . urlencode($testData['testAnswer']) . "\" target=\"_blank\">" . $testData['testAnswer'] . "</a>\n";
						} elseif (file_exists($questionBank . "/test/answers/" . $testData['testAnswer'])) {
							echo "<a href=\"preview.php/" . $questionBank . "/test/answers/" . urlencode($testData['testAnswer']) . "\" target=\"_blank\">" . $testData['testAnswer'] . "</a>\n";
						} else {
							echo $testData['testAnswer'] . " <span class=\"notAssigned\">File deleted</span>\n";
						}
						
						echo "</blockquote>\n";
					}
					
					break;
					
				case "Fill in the Blank" : 
					$sentenceValues = arrayRevert($testData['questionValue']);
					$correctAnswer = arrayRevert($testData['testAnswer']);
					$userAnswer = arrayRevert($testData['userAnswer']);
					
					if (empty($testData['score']) || $selectedAnswers == true) {
						echo "<p>Selected Answer: </p>\n<blockquote>\n";
						
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
						
						echo "\n</blockquote>\n";
					}
					
					if (empty($testData['score']) || $correctAnswers == true) {
						echo "<p>Correct Answer: </p>\n<blockquote>\n";
						
						for ($list = 0; $list <= sizeof($sentenceValues) - 1; $list ++) {
							echo $sentenceValues[$list];
							
							if ($list <= sizeof($sentenceValues) - 1 && isset($correctAnswer[$list])) {
								echo " <strong>" . $correctAnswer[$list] . "</strong> ";
							}
						}
						
						echo "\n</blockquote>\n";
					}
					
					break;
				
				case "Matching" : 
					$questionValue = arrayRevert($testData['questionValue']);
					$userAnswer = arrayRevert($testData['userAnswer']);
					$answerValues = arrayRevert($testData['answerValueScrambled']);
					$correctAnswer = arrayRevert($testData['testAnswer']);
					
					if (empty($testData['score']) || $selectedAnswers == true || $correctAnswers == true) {
						echo "<table width=\"100%\" class=\"dataTable\">\n<tr>\n";
						echo column("Question", "200");
						
						if (empty($testData['score']) || $selectedAnswers == true) {
							echo column("Selected Answers", "200");
						}
						
						if (empty($testData['score']) || $correctAnswers == true) {
							echo column("Correct Answers", "200");
						}
						
						echo "</tr>\n";
					
						for ($list = 0; $list <= sizeof($questionValue) - 1; $list++) {
							echo "<tr";
							if (sprintf($list + 1) & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
							
							echo cell($questionValue[$list], "200");
							
							if (empty($testData['score']) || $selectedAnswers == true) {
								echo cell($answerValues[sprintf($userAnswer[$list] - 1)], "200");
							}
							
							if (empty($testData['score']) || $correctAnswers == true) {
								echo cell($correctAnswer[$list], "200");
							}
							
							echo "</tr>\n";
						}
						
						echo "</table>\n";	
					}
								  
					break;
				
				case "Multiple Choice" : 
					$answers = arrayRevert($testData['userAnswer']);
					$correctAnswer = arrayRevert($testData['testAnswer']);
					$correctAnswerValues = arrayRevert($testData['answerValue']);
					
					if ($testData['randomizeQuestion'] == "1") {
						$choices = arrayRevert($testData['answerValueScrambled']);
					} else {
						$choices = arrayRevert($testData['answerValue']);
					}
									
					if (empty($testData['score']) || $selectedAnswers == true) {				
						if (is_array($answers) && sizeof($answers) > 1) {
							echo "<p>Selected Answers: </p>\n";
							echo "<ul>\n";
							
							for ($list = 0; $list <= sizeof($answers) - 1; $list ++) {
								echo "<li>" . $choices[sprintf($answers[$list] - 1)] . "</li>\n";
							}
							
							echo "</ul>\n";
						} else {
							echo "<p>Selected Answer: </p>\n";
							
							if (!empty($answers['0'])) {
								echo "<blockquote>\n" . $choices[sprintf($answers['0'] - 1)] . "\n</blockquote>\n";
							} else {
								echo "<blockquote>\n<p><span class=\"notAssigned\">None Given</span></p>\n</blockquote>\n";
							}
						}
					}
					
					if (empty($testData['score']) || $correctAnswers == true) {				
						if (is_array($choices) && sizeof($correctAnswer) > 1) {
							echo "<p>Correct Answers: </p>\n";
							echo "<ul>\n";
							
							for ($list = 0; $list <= sizeof($correctAnswer) - 1; $list ++) {
								echo "<li>" . $correctAnswerValues[sprintf($correctAnswer[$list] - 1)] . "</li>\n";
							}
							
							echo "</ul>\n";
						} else {						
							echo "<p>Correct Answer: </p>\n";
							echo "<blockquote>\n" . $correctAnswerValues[sprintf($correctAnswer['0'] - 1)] . "\n</blockquote>\n";
						}
					}
					
					break;
					
				case "Short Answer" : 
					if (empty($testData['score']) || $selectedAnswers == true) {
						echo "<p>Selected Answer: </p>\n<blockquote>\n<p><strong>" . arrayRevert($testData['userAnswer']) . "</strong></p>\n</blockquote>\n";
					}
					
					if (empty($testData['score']) || $correctAnswers == true) {				
						if (is_array(arrayRevert($testData['testAnswer']))) {
							echo "<p>Correct Answers: </p>\n";
							echo "<ul>\n";
							
							foreach (arrayRevert($testData['testAnswer']) as $correctAnswer) {
								echo "<li>" . $correctAnswer . "</li>\n";
							}
							
							echo "</ul>\n";
						} else {
							echo "<p>Correct Answer: </p>\n";
							echo "<blockquote>\n<p><strong>" . arrayRevert($testData['testAnswer']) . "</strong></p>\n</blockquote>\n";
						}
					}
					
					break;
					
				case "True False" : 
					if ($selectedAnswers == true) {
						echo "<p>Selected Answer: </p>\n<blockquote>\n<p>";
						
						if (arrayRevert($testData['userAnswer']) == "1") {
							echo "True";
						} else {
							echo "False";
						}
						
						echo "</p>\n</blockquote>\n";
					}
					
					if ($correctAnswers == true) {
						echo "<p>Correct Answer: </p>\n<blockquote>\n<p>";
						
						if ($testData['testAnswer'] == "1") {
							echo "True";
						} else {
							echo "False";
						}
						
						echo "</p>\n</blockquote>\n";
					}
					
					break;
			}
			
			if ($feedback == true && !empty($testData['feedback'])) {
				echo "<p>Feedback :</p>\n<blockquote>\n" . $testData['feedback'] . "\n</blockquote>\n";
			}
			
			if ($testData['type'] != "Description") {
				echo "<br /><br />\n</td>\n</tr>\n";
			}
		}
		
		echo "</table>\n";
		
		if (isset($submit)) {
			echo "<blockquote><p>";
			echo button("submit", "submit", "Submit Scores", "submit");
			echo "</p></blockquote>";
		} else {
			echo "<blockquote><p>";
			
			if ($attempt > 1) {
				if (isset($_GET['return']) && $_GET['return'] == "gradebook") {
					$URLaddition = "&return=gradebook";
				} else {
					$URLaddition = "";
				}
				
				if ($currentAttempt > 1) {
					echo button("back", "back", "Back to Attempts", "button", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'] . $URLaddition);
				}
			}
			
			if (isset($_GET['return']) && $_GET['return'] == "gradebook") {
				$return = "gradebook/index.php";
			} else {
				$return = "index.php";
			}
			
			echo button("submit", "submit", "Finish", "button", $return);
			echo "</p></blockquote>";
		}
		
		echo closeForm(false, true);
	} else {
	//Title
		title("Review Test", "You have taken this test multiple times. Please select which attempt you wish to review.");
		
	//Admin toolbar
		echo "<div class=\"toolBar\">\n";
		
		if (isset($_GET['return']) && $_GET['return'] == "gradebook") {
			echo toolBarURL("Back to Gradebook", "gradebook/index.php", "toolBarItem back");
		} else {
			echo toolBarURL("Back to Learning Units", "index.php", "toolBarItem back");
		}
		
		echo "</div>\n<br />\n"; 
		echo "<blockquote>\n";
		
		for ($count = $attempt['attempt']; $count >= 1; $count --) {
			$attemptDataGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}' AND `attempt` = '{$count}'", "raw");
			$score = 0;
			$points = 0;
			
			while($attemptData = fetch($attemptDataGrabber)) {
				$score += $attemptData['score'];
				$points += $attemptData['points'];
			}
			
			if ($points == 1) {
				$pointWord = " point ";
			} else {
				$pointWord = " points ";
			}
			
			echo URL("Attempt " . $count, $_SERVER['REQUEST_URI'] . "&attempt=" . $count) . " - " . $score . " out of " . $points . $pointWord . "(" . round($score/$points * 100) . "%)";
			
			if ($count == $attempt['attempt'] && $updateArray[$testID]['testStatus'] == "A") {
				echo " [Awaiting Grade]";
			}
			
			echo "<br />\n";
		}
	}

//Include the footer
	footer();
?>