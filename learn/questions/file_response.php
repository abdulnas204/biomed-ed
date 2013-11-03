<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	require_once('functions.php');
	$monitor = monitor("File Response", "tinyMCEMedia,validate,autoSuggest");
	$questionData = dataGrabber("File Response");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$difficulty = $_POST['difficulty'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$totalFiles = $_POST['totalFiles'];
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
		if (is_uploaded_file($_FILES['answer'] ['tmp_name'])) {
			if ($type == "Module") {
				$uploadDir = $monitor['directory'] . "test/answers";
			} else {
				$uploadDir = "../questionbank/test/answers";
			}
						
			$tempFile = $_FILES['answer'] ['tmp_name'];
			$targetFile = basename($_FILES['answer'] ['name']);
			$fileNameArray = explode(".", $targetFile);
			$targetFile = "";
			extension($_FILES['answer'] ['name']);
			
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
				if (isset ($questionData)) {
					unlink ($uploadDir . "/" . $questionData['fileURL']);
				}
				
				$fileURL = mysql_real_escape_string($targetFile);
				$sql = "`fileURL` = '{$fileURL}', ";
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
			$sql = "";
		}
				
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `tags` = '{$tags}', `totalFiles` = '{$totalFiles}', {$sql}`correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `tags` = '{$tags}', `totalFiles` = '{$totalFiles}', {$sql}`correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");	
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'File Response', '{$points}', '{$extraCredit}', '0', '{$difficulty}', '{$category}', '{$link}', '0', '{$totalFiles}', '', '1', '{$tags}', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'File Response', '{$points}', '{$extraCredit}', '0', '{$difficulty}', '{$category}', '0', '{$totalFiles}', '', '1', '{$tags}', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Delete the current answer
	if (isset($_GET['delete']) && $_GET['delete'] == "true") {
		if (isset($_GET['id'])) {
			$table = $monitor['testTable'];
			$id = $_GET['id'];
			$directory = $monitor['directory'] . "test/answers";
			$redirect = "?id=" . $id;
		} elseif (isset($_GET['bankID'])) {
			$table = "questionbank";
			$id = $_GET['bankID'];
			$directory = "../questionbank/test/answers";
			$redirect = "?bankID=" . $id;
		}
		
		$file = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'");
		query("UPDATE `{$table}` SET `fileURL` = '' WHERE `id` = '{$id}'");
		deleteAll($directory . "/" . $file['fileURL']);
		redirect($_SERVER['PHP_SELF'] . $redirect);
	}
	
//Title
	title($monitor['title'] . "File Response", "A file response is a question that must be responded to in the form of an uploaded file, such as a video or a PDF. Files responses must be scored manually.");
	
//File response form
	form("fileResponse", "post", true);
	catDivider("Question", "one", true);
	echo "<blockquote>";
	question();
	echo "</blockquote>";
	
	catDivider("Question Settings", "two");
	echo "<blockquote>";
	points();
	type();
	difficulty();
	category();
	descriptionLink();
	directions("Number of files user is premitted to upload");
	echo "<blockquote><p>";
	dropDown("totalFiles", "totalFiles", "1,2,3,4,5,6,7,8,9,10", "1,2,3,4,5,6,7,8,9,10", false, false, false, "1", "questionData", "totalFiles");
	echo "</p></blockquote>";
	tags();
	echo "</blockquote>";
	
	catDivider("Answer", "three");
	echo "<blockquote>";
	directions("Provide an example of a correct answer");
	echo "<blockquote>";
	
	if (isset($_GET['id'])) {
		$directory = $monitor['gatewayPath'] . "test/answers";
	} elseif (isset($_GET['bankID'])) {
		$directory = "../../gateway.php/modules/questionbank/test/answers";
	} else {
		$directory = false;
	}
	
	if ($directory != false && isset($questionData) && !empty($questionData['fileURL'])) {
		echo "<table name=\"answerTable\" id=\"answerTable\"><tr><td>";
		fileUpload("answer", "answer", false, false, false, false, "questionData", "fileURL", $directory, true);
		echo "</td><td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true", "action smallDelete", false, false, false, false, false, false, " onclick=\"return confirm('This action will delete this file. Continue?')\"") . "</td></tr></table>";
	} else {
		fileUpload("answer", "answer", false, false, false, false, "questionData", "fileURL");
	}
	
	echo "</blockquote></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback();
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>