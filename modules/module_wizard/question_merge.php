<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Question Merge", false, true);

//Check to see if questions from the question bank need to be merged
	if (!isset($_GET['type'])) {
		$importCheck = exist($monitor['parentTable'], "id", $monitor['currentModule']);
		
		if ($importCheck['questionBank'] == "1") {
			if (exist("{$monitor['testTable']}_{$monitor['currentTable']}", "questionbank", "1") == true) {
				//Do nothing
			} else {
			//Select all of the questions from the bank
				$category = $importCheck['category'];
				$importQuestionsGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$category}'", $connDBA);	
				
			//Import the questions into the test
				$lastQuestionGrabber = mysql_query("SELECT * FROM `{$monitor['testTable']}_{$monitor['currentTable']}` ORDER BY `position` DESC", $connDBA);
				$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
				$lastQuestion = $lastQuestionFetch['position'] + 1;
				
				while ($importQuestions = mysql_fetch_array($importQuestionsGrabber)) {			
					$position = $lastQuestion++;
					$id = $importQuestions['id'];
										
					mysql_query("INSERT INTO `{$monitor['testTable']}_{$monitor['currentTable']}` (
								`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
								) VALUES (
								NULL, '1', '{$id}', '{$position}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '','','','','',''
								)", $connDBA);
				}
			}
		}
		
		if (isset ($_SESSION['review'])) {
			$testCheckGrabber = mysql_query("SELECT * FROM `{$monitor['testTable']}_{$monitor['currentTable']}`", $connDBA);
			$testCheck = mysql_num_rows($testCheckGrabber);
			
			if ($testCheckGrabber && $testCheck >= 1) {
				redirect("modify.php?updated=testSettings");
			} else {
				$_SESSION['step'] = "testContent";
				redirect("test_content.php");
			}
		} else {	
			$_SESSION['step'] = "testContent";
			redirect("test_content.php");
		}
	}
	
//Import a question from the bank into the test	
	if (isset($_GET['type']) && $_GET['type'] == "import" && isset($_GET['questionID']) && isset($_GET['bankID'])) {
		$bankID = $_GET['bankID'];
		$questionID = $_GET['questionID'];
		$importQuestions = query("SELECT * FROM `questionbank` WHERE `id` = '{$bankID}'");
		
		$type = $importQuestions['type'];
		$points = $importQuestions['points'];
		$extraCredit = $importQuestions['extraCredit'];
		$partialCredit = $importQuestions['partialCredit'];
		$difficulty = $importQuestions['difficulty'];
		$link = $importQuestions['link'];
		$randomize = $importQuestions['randomize'];
		$totalFiles = $importQuestions['totalFiles'];
		$choiceType = $importQuestions['choiceType'];
		$case = $importQuestions['case'];
		$tags = $importQuestions['tags'];
		$question = mysql_real_escape_string(stripslashes($importQuestions['question']));
		$questionValue = mysql_real_escape_string(stripslashes($importQuestions['questionValue']));
		$answer = mysql_real_escape_string(stripslashes($importQuestions['answer']));
		$answerValue = mysql_real_escape_string(stripslashes($importQuestions['answerValue']));
		$fileURL = $importQuestions['fileURL'];
		$correctFeedback = mysql_real_escape_string(stripslashes($importQuestions['correctFeedback']));
		$incorrectFeedback = mysql_real_escape_string(stripslashes($importQuestions['incorrectFeedback']));
		$partialFeedback = mysql_real_escape_string(stripslashes($importQuestions['partialFeedback']));
							
		mysql_query("UPDATE `{$monitor['testTable']}` SET `questionBank` = '0', `linkID` = '0', `type` = '{$type}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `partialCredit` = '{$partialCredit}', `difficulty` = '{$difficulty}', `link` = '{$link}', `randomize` = '{$randomize}', `totalFiles` = '{$totalFiles}', `choiceType` = '{$choiceType}', `case` = '{$case}', `tags` = '{$tags}', `question` = '{$question}', `questionValue` = '{$questionValue}', `answer` = '{$answer}', `answerValue` = '{$answerValue}', `fileURL` = '{$fileURL}', `correctFeedback` = '{$correctFeedback}', `incorrectFeedback` = '{$incorrectFeedback}', `partialFeedback` = '{$partialFeedback}' WHERE `id` = '{$questionID}'", $connDBA);
		
		if ($type == "File Response") {
			copy("../QuestionBank/test/answers/" . $fileURL, $monitor['directory'] . "test/answers/" . $fileURL);
		}
		
		$redirect = "../questions/";
		
		switch ($type) {
			case "Description" : $redirect .= "description"; break;
			case "Essay" : $redirect .= "essay"; break;
			case "File Response" : $redirect .= "file_response"; break;
			case "Fill in the Blank" : $redirect .= "blank"; break;
			case "Matching" : $redirect .= "matching"; break;
			case "Multiple Choice" : $redirect .= "multiple_choice"; break;
			case "Short Answer" : $redirect .= "short_answer"; break;
			case "True False" : $redirect .= "true_false"; break;
		}
		
		$redirect .= ".php";
		
		$questionInfo = query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$questionID}'");
		
		redirect($redirect . "?id=" . $questionInfo['id']);
	}
?>