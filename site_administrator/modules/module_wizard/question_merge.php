<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
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
		$currentModule = str_replace(" ", "", $_SESSION['currentModule']);
		$importCheckGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `name` = '{$name}'", $connDBA);
		$importCheck = mysql_fetch_array($importCheckGrabber);
		$statusCheckGrabber = mysql_query("SELECT * FROM `moduletest_{$currentModule}` WHERE `questionBank` = '1'", $connDBA);
		
		if ($importCheck['questionBank'] == "1") {
			if (mysql_fetch_array($statusCheckGrabber)) {
				//Do nothing
			} else {
			//Select all of the questions from the bank
				$category = $_SESSION['category'];
				echo $category;
				$importQuestionsGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$category}'", $connDBA);	
				
			//Import the questions into the test
				$currentTable = str_replace(" ", "", $_SESSION['currentModule']);
				$lastQuestionGrabber = mysql_query("SELECT * FROM `moduletest_{$currentModule}` ORDER BY position DESC", $connDBA);
				$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
				if ($lastQuestionFetch['position'] == "") {
					$lastQuestion = 1;
				} else {
					$lastQuestion = $lastQuestionFetch['position'];
				}
				
				
				while ($importQuestions = mysql_fetch_array($importQuestionsGrabber)) {			
					$position = $lastQuestion++;
					$id = $importQuestions['id'];
					
					$insertQuestionQuery = "INSERT INTO `moduletest_{$currentTable}` (
										`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialCorrect`
										) VALUES (
										NULL, '1', '{$id}', '{$position}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '','','','','',''
										)";
										
					mysql_query($insertQuestionQuery, $connDBA);
				}
			}
		}
	}
	
//Import selected questions from the question bank
	if (isset($_GET['type']) && $_GET['type'] == "import" && isset($_POST['id'])) {		
		$question = $_POST['import'];
		$importQuestionsGrabber = mysql_query("SELECT * FROM questionbank WHERE `id` = '{$question}'", $connDBA);
		$importQuestions = mysql_fetch_array($importQuestionsGrabber);
		
	//Import those questions into the test
		$currentTable = str_replace(" ", "", $_SESSION['currentModule']);
		$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC", $connDBA);
		$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
		$lastQuestion = $lastQuestionFetch['position']+1;
		
		$id = $importQuestions['id'];
		$type = $importQuestions['type'];
		$points = $importQuestions['points'];
		$extraCredit = $importQuestions['extraCredit'];
		$partialCredit = $importQuestions['partialCredit'];
		$difficulty = $importQuestions['difficulty'];
		$category = $importQuestions['category'];
		$link = $importQuestions['link'];
		$randomize = $importQuestions['randomize'];
		$totalFiles = $importQuestions['totalFiles'];
		$case = $importQuestions['case'];
		$question = $importQuestions['question'];
		$questionValue = $importQuestions['questionValue'];
		$answer = $importQuestions['answer'];
		$answerValue = $importQuestions['answerValue'];
		$fileURL = $importQuestions['fileURL'];
		$correctFeedback = $importQuestions['correctFeedback'];
		$incorrectFeedback = $importQuestions['incorrectFeedback'];
		$partialFeedback = $importQuestions['partialCorrect'];
		
		$insertQuestionQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialCorrect`
							) VALUES (
							NULL, '0', '', '{$lastQuestion}', '{$type}', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$category}', '{$link}', '{$randomize}', '{$totalFiles}', '{$case}', '{$question}', '{$questionValue}', '{$answer}', '{$answerValue}', '{$fileURL}', '{$correctFeedback}', '{$incorrectFeedback}', '{$partialFeedback}'
							)";
							
		mysql_query($insertQuestionQuery, $connDBA);
		
	//Move an uploaded file if it is a file response question	
		if ($importQuestions['type'] == "File Response") {
			$location = str_replace(" ", "", $_SESSION['currentModule']);
	
			if(!is_dir("../../../modules/{$location}")) {
				mkdir("../../../modules/{$location}", 0777);
			}
			
			if(!is_dir("../../../modules/{$location}/test")) {
				mkdir("../../../modules/{$location}/test", 0777);
			}
			
			copy("../../../questionBank/" . $importQuestions['fileURL'], "../../../modules/{$location}/test");
		}
		
		$category = urlencode($_GET['category']);
		header ("Location: question_bank.php?category=" . $category);
		exit;
	}


//Update the session to manage the content
	$_SESSION['step'] = "testContent";	
		
	if (isset ($_SESSION['review'])) {
		header ("Location: modify.php?updated=testSettings");
		exit;
	} else {
		header ("Location: test_content.php");
		exit;	
	}
?>