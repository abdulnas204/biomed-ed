<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Review Test", "Student,Site Administrator", "tinyMCESimple,newObject", true);
	
//Grab all module and test data
	$userData = userData();
	$testID = $_GET['id'];
	$parentTable = "moduletest_" . $testID;
	$testTable = "testdata_" . $userData['id'];
	
	if (isset ($_GET['id'])) {
		$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}' LIMIT 1");
		$testDataGrabber = query("SELECT * FROM `{$testTable}` ORDER BY `testPosition` ASC", "raw");
	} else {
		redirect("index.php");
	}
	
//Display the test results
	echo "<table class=\"dataTable\">";
	$count = 1;
	$restrictImport = array();
	$values = unserialize($moduleInfo['display']);
	$score = false;
	$selectedAnswers = false;
	$correctAnswers = false;
	$feedback = false;
	
	if (is_array($values)) {
		foreach($values as $checkbox) {
			switch ($checkbox) {
				case "1" : $score = true; break;
				case "2" : $selectedAnswers = true; break;
				case "3" : $correctAnswers = true; break;
				case "4" : $feedback = true; break;
			}
		}
	}
	
	while ($testData = mysql_fetch_array($testDataGrabber)) {	
		if ($testData['link'] != "0" && !empty($testData['link'])) {
			if (!in_array($testData['link'], $restrictImport) && !query("SELECT * FROM `{$testTable}` WHERE `testID` = '{$testID}' AND `questionID` = '{$testData['id']}'", "raw")) {
				$linkData = query("SELECT * FROM `{$table}` WHERE `id` = '{$testData['link']}'");
				array_push($restrictImport, $testData['link']);
				echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($linkData['question'], false, true) . "</td></tr>";
				unset($linkData);
			}
		}
		
		if ($testData['type'] != "Description") {
			echo "<tr><td width=\"100\" valign=\"top\"><p>";
			
			echo "<span class=\"questionNumber\">Question " . $count++ . "</span><br />";
			
			if (empty($testData['score']) && $testData['score'] !== "0") {
				echo "<br />";
				textField("score_" . $testData['id'], "score_" . $testData['id'], "5", "5", false, true);
				echo " / " . $testData['points'];
			} else {
				echo "<span class=\"questionPoints\">" . $testData['score'] . " ";
				
				if ($testData['score'] == "1") {
					echo "Point";
				} else {
					echo "Points";
				}
			}
			
			echo "</span>";
			
			if ($testData['extraCredit'] == "on") {
				echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
			}
			
			echo "</p></td><td valign=\"top\">" . prepare($testData['questionText'], false, true) . "<br /><br />";
		}
		
		switch ($testData['type']) {
			case "Description" : 
				if (!in_array($testData['id'], $restrictImport)) {
					echo "<tr><td colspan=\"2\" valign=\"top\">" . $testData['question'] . "</td></tr>";
				}
				
				break;
			case "Essay" : 
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote>" . unserialize($testData['userAnswer']) . "</blockquote>";
				}
				
				if ($correctAnswers == true) {
					echo "<p>Correct Answer: </p><blockquote>" . $testData['answer'] . "</blockquote>";
				}
					
				break;
				
			case "File Response" : 
				$fillValue = unserialize($testData['userAnswer']);
				
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><ol>";
					
					foreach ($fillValue as $file) {
						if (file_exists($_GET['id'] . "/test/responses/" . $file)) {
							echo "<li><a href=\"../gateway.php/modules/" . $_GET['id'] . "/test/responses/" . urlencode($file) . "\" target=\"_blank\">" . $file . "</a></li>";
						} else {
							echo "<li>" . $file . " <span class=\"notAssigned\">File deleted</span></li>";
						}
					}
					
					echo "</ol>";
				}
				
				if ($correctAnswers == true && !empty($testData['fileURL'])) {
					echo "<p>Correct Answer: </p><blockquote>";
					
					if (file_exists($_GET['id'] . "/test/responses/" . $file)) {
						echo "<a href=\"../gateway.php/modules/" . $_GET['id'] . "/test/responses/" . urlencode($file) . "\" target=\"_blank\">" . $file . "</a>";
					} else {
						echo $file . " <span class=\"notAssigned\">File deleted</span>";
					}
					
					echo "</blockquote>";
				}
				
				break;
				
			case "Fill in the Blank" : 
				$sentenceValues = unserialize($testData['userQuestion']);
				$userAnswer = unserialize($testData['userAnswer']);
				
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote>";
					
					for ($list = 0; $list <= sizeof($sentenceValues['0']) - 1; $list ++) {
						echo $sentenceValues['0'][$list];
						
						if ($list < sizeof($sentenceValues['0']) - 1 && isset($userAnswer[$list])) {
							echo " <strong>" . $userAnswer[$list] . "</strong> ";
						}
					}
					
					echo "</blockquote>";
				}
				
				if ($correctAnswers == true) {
					echo "<p>Correct Answer: </p><blockquote>";
					
					for ($list = 0; $list <= sizeof($sentenceValues['0']) - 1; $list ++) {
						echo $sentenceValues['0'][$list];
						
						if ($list < sizeof($sentenceValues['0']) - 1 && isset($sentenceValues['1'][$list])) {
							echo " <strong>" . $sentenceValues['1'][$list] . "</strong> ";
						}
					}
					
					echo "</blockquote>";
				}
				
				break;
			
			case "Matching" : 
				$questions = unserialize($testData['userQuestion']);
				$answers = unserialize($testData['userQuestion']);
				
				if ($selectedAnswers == true || $correctAnswers == true) {
					echo "<table width=\"100%\" class=\"dataTable\"><tr><th class=\"tableHeader\" width=\"200\">Question</th>";
					
					if ($selectedAnswers == true) {
						echo "<th class=\"tableHeader\" width=\"200\">Selected Answers</th>";
					}
					
					if ($correctAnswers == true) {
						echo "<th class=\"tableHeader\" width=\"200\">Correct Answers</th>";
					}
					
					echo "</tr>";
				
					for ($list = 0; $list <= sizeof($questions['0']) - 1; $list++) {
						echo "<tr";
			  			if (sprintf($list + 1) & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
						
						echo "<td width=\"200\"><p>" . $questions['2'][$list] . "</p></td>";
						
						if ($selectedAnswers == true) {
							echo "<td width=\"200\"><p>" . $answers['1'][$list] . "</p></td>";
						}
						
						if ($correctAnswers == true) {
							echo "<td width=\"200\"><p>" . $answers['0'][$list] . "</p></td>";
						}
						
						echo "</tr>";
					}
				}
				
				echo"</table>";				  
				break;
			
			case "Multiple Choice" : 
				$choices = unserialize($testData['userQuestion']);
				$answers = unserialize($testData['userAnswer']);
				
				if ($testData['randomize'] == "1") {
					$key = "1";
				} else {
					$key = "0";
				}
								
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p>";
					
					if (is_array($answers) && sizeof($answers) > 1) {
						echo "<ul>";
						
						for ($list = 0; $list <= sizeof($answers) - 1; $list ++) {
							echo "<li>" . $choices[$key][sprintf($answers[$list] - 1)] . "</li>";
						}
						
						echo "</ul>";
					} else {
						echo "<blockquote><p>" . $choices[$key][$answers['0'] - 1] . "</p></blockquote>";
					}
				}
				
				if ($correctAnswers == true) {					
					if (is_array($choices['2']) && sizeof($choices['2']) > 1) {
						echo "<p>Correct Answers: </p>";
						echo "<ul>";
						
						for ($list = 0; $list <= sizeof($choices['0']) - 1; $list ++) {
							echo "<li>" . $choices['0'][sprintf($choices['2'][$list] - 1)] . "</li>";
						}
						
						echo "</ul>";
					} else {
						if (is_array($choices['2'])) {
							$choice = $choices['2']['0'];
						} else {
							$choice = $choices['2'];
						}
						
						echo "<p>Correct Answer: </p>";
						echo "<blockquote><p>" . $choices['0'][$choice - 1] . "</p></blockquote>";
					}
				}
				
				break;
				
			case "Short Answer" : 
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote><p><strong>" . unserialize($testData['userAnswer']) . "</strong></p></blockquote>";
				}
				
				if ($correctAnswers == true) {
					echo "<p>Correct Answer: </p>";
					
					if (is_array(unserialize($testData['answerValue']))) {
						echo "<ul>";
						
						foreach (unserialize($testData['answerValue']) as $correctAnswer) {
							echo "<li>" . $correctAnswer . "</li>";
						}
						
						echo "</ul>";
					} else {
						echo "<p><strong>" . unserialize($testData['answer']) . "</strong></p></blockquote>";
					}
				}
				
				break;
				
			case "True False" : 
				if ($selectedAnswers == true) {
					echo "<p>Selected Answer: </p><blockquote><p><strong>";
					
					if (unserialize($testData['userAnswer']) == "1") {
						echo "True";
					} else {
						echo "False";
					}
					
					echo "</strong></p></blockquote>";
				}
				
				if ($correctAnswers == true) {
					echo "<p>Correct Answer: </p><blockquote><p><strong>";
					
					if ($testData['answer'] == "1") {
						echo "True";
					} else {
						echo "False";
					}
					
					echo "</strong></p></blockquote>";
				}
				
				break;
		}
		
		if ($feedback == true && $testData['type'] != "Description") {
			if ($testData['score'] >= $testData['points'] && !empty($testData['correctFeedback'])) {
				echo "<p>Feedback :</p><blockquote>" . $testData['correctFeedback'] . "</blockquote>";
				$displayFeedback = true;
			}
			
			if ($testData['score'] < $testData['points'] && $testData['score'] !== "0" && !empty($testData['partialFeedback'])) {
				echo "<p>Feedback :</p><blockquote>" . $testData['partialFeedback'] . "</blockquote>";
				$displayFeedback = true;
			}
			
			if ($testData['score'] == "0" && !empty($testData['incorrectFeedback'])) {
				echo "<p>Feedback :</p><blockquote>" . $testData['incorrectFeedback'] . "</blockquote>";
				$displayFeedback = true;
			}
			
			if ((empty($testData['score']) && $testData['score'] !== "0") || !isset($displayFeedback)) {
				echo "<p>Feedback :</p><blockquote>";
				textArea("feedback_" . $testData['id'], "feedback_" . $testData['id'], "small", false);
				echo "</blockquote>";
			}
			
			unset($displayFeedback);
		}
		
		if ($testData['type'] != "Description") {
			echo "<br /><br /></td></tr>";
		}
	}
	
	echo "</table>";
	
	echo "</table>";

//Include the footer
	footer();
?>