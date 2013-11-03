<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this step has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			//case "testSettings" : header ("Location: test_settings.php"); exit; break;
			//case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
	//Check to see if a test is set to be created, otherwise allow access to this page
		$name = $_SESSION['currentModule'];
		$testCheckGrabber = mysql_query("SELECT * FROM moduleData WHERE `name` = '{$name}'", $connDBA);
		$testCheckArray = mysql_fetch_array($testCheckGrabber);
		
		if ($testCheckArray['test'] == "0") {
			header ("Location: test_check.php");
			exit;
		}
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Check to see if questions from the question bank need to be merged
	if (!isset($_GET['type'])) {
		$name = $_SESSION['currentModule'];
		$currentModule = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
		$importCheckGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `name` = '{$name}'", $connDBA);
		$importCheck = mysql_fetch_array($importCheckGrabber);
		$statusCheckGrabber = mysql_query("SELECT * FROM `moduletest_{$currentModule}` WHERE `questionBank` = '1'", $connDBA);
		
		if ($importCheck['questionBank'] == "1") {
			if (mysql_fetch_array($statusCheckGrabber)) {
				//Do nothing
			} else {
			//Select all of the questions from the bank
				$category = $_SESSION['category'];
				$importQuestionsGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$category}'", $connDBA);	
				
			//Import the questions into the test
				$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
				$lastQuestionGrabber = mysql_query("SELECT * FROM `moduletest_{$currentModule}` ORDER BY position DESC", $connDBA);
				$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
				if ($lastQuestionFetch['position'] == "") {
					$lastQuestion = 1;
				} else {
					$lastQuestion = $lastQuestionFetch['position']+1;
				}
				
				
				while ($importQuestions = mysql_fetch_array($importQuestionsGrabber)) {			
					$position = $lastQuestion++;
					$id = $importQuestions['id'];
					$type = $importQuestions['type'];
					
					$insertQuestionQuery = "INSERT INTO moduletest_{$currentTable} (
										`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
										) VALUES (
										NULL, '1', '{$id}', '{$position}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '','','','','',''
										)";
										
					mysql_query($insertQuestionQuery, $connDBA);
										
					$location = $currentTable;
										
					if ($type == "File Response") {
						if (!file_exists("../../../../modules/{$location}")) {
							mkdir("../../../../modules/{$location}");
						}
						if (!file_exists("../../../../modules/{$location}/test")) {
							mkdir("../../../../modules/{$location}/test", 0777);
						}
						if (!file_exists("../../../../modules/{$location}/test/fileresponse")) {
							mkdir("../../../../modules/{$location}/test/fileresponse", 0777);
						}
						if (!file_exists("../../../../modules/{$location}/test/fileresponse/responses")) {
							mkdir("../../../../modules/{$location}/test/fileresponse/responses", 0777);
						}
					}
				}
			}
		}
		
		
		if (isset ($_SESSION['review'])) {
			$testCheckGrabber = mysql_query("SELECT * FROM `moduletest_{$currentModule}`", $connDBA);
			$testCheck = mysql_num_rows($testCheckGrabber);
			
			if ($testCheckGrabber && $testCheck >= 1) {
				header ("Location: modify.php?updated=testSettings");
				exit;
			} else {
				$_SESSION['step'] = "testContent";
				header ("Location: test_content.php");
				exit;
			}
		} else {	
			$_SESSION['step'] = "testContent";
			header ("Location: test_content.php");
			exit;
		}
	}
	
//Import a question from the bank into the test	
	if (isset($_GET['type']) && $_GET['type'] == "import" && isset($_GET['questionID']) && isset($_GET['bankID'])) {
		$bankID = $_GET['bankID'];
		$questionID = $_GET['questionID'];
		$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
		$importQuestionsGrabber = mysql_query("SELECT * FROM questionbank WHERE `id` = '{$bankID}'", $connDBA);
		$importQuestions = mysql_fetch_array($importQuestionsGrabber);
		
		$type = $importQuestions['type'];
		$points = $importQuestions['points'];
		$extraCredit = $importQuestions['extraCredit'];
		$partialCredit = $importQuestions['partialCredit'];
		$difficulty = $importQuestions['difficulty'];
		$category = $importQuestions['category'];
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
		
		$insertQuestionQuery = "UPDATE moduletest_{$currentTable} SET `questionBank` = '0', `linkID` = '0', `type` = '{$type}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `partialCredit` = '{$partialCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `randomize` = '{$randomize}', `totalFiles` = '{$totalFiles}', `choiceType` = '{$choiceType}', `case` = '{$case}', `tags` = '{$tags}', `question` = '{$question}', `questionValue` = '{$questionValue}', `answer` = '{$answer}', `answerValue` = '{$answerValue}', `fileURL` = '{$fileURL}', `correctFeedback` = '{$correctFeedback}', `incorrectFeedback` = '{$incorrectFeedback}', `partialFeedback` = '{$partialFeedback}' WHERE id = '{$questionID}'";
							
		mysql_query($insertQuestionQuery, $connDBA);
		
		if ($type == "File Response" && $fileURL !== "") {
			$location = $currentTable;
			
			if (!file_exists("../../../modules/{$location}")) {
				mkdir("../../../modules/{$location}");
			}
			if (!file_exists("../../../../modules/{$location}/test")) {
				mkdir("../../../modules/{$location}/test", 0777);
			}
			if (!file_exists("../../../../modules/{$location}/test/fileresponse")) {
				mkdir("../../../modules/{$location}/test/fileresponse", 0777);
			}
			if (!file_exists("../../../modules/{$location}/test/fileresponse/responses")) {
				mkdir("../../../modules/{$location}/test/fileresponse/responses", 0777);
			}
			if (!file_exists("../../../modules/{$location}/test/fileresponse/answer")) {
				mkdir("../../../modules/{$location}/test/fileresponse/answer", 0777);
			}
			
			copy("../../../modules/questionBank/test/fileresponse/answer/" . $fileURL, "../../../modules/{$location}/test/fileresponse/answer/" . $fileURL);
		}
		
		switch ($type) {
			case "Description" : $redirect = "questions/description.php"; break;
			case "Essay" : $redirect = "questions/essay.php"; break;
			case "File Response" : $redirect = "questions/file_response.php"; break;
			case "Fill in the Blank" : $redirect = "questions/blank.php"; break;
			case "Matching" : $redirect = "questions/matching.php"; break;
			case "Multiple Choice" : $redirect = "questions/multiple_choice.php"; break;
			case "Short Answer" : $redirect = "questions/short_answer.php"; break;
			case "True False" : $redirect = "questions/true_false.php"; break;
		}
		
		$questionInfoGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$questionID}'", $connDBA);
		$questionInfo = mysql_fetch_array($questionInfoGrabber);
		
		header ("Location: " . $redirect . "?question=" . $questionInfo['position'] . "&id=" . $questionInfo['id']);
		exit;
	}
?>