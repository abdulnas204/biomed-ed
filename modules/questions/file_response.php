<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("File Response", "tinyMCESimple,validate");
	require_once('functions.php');
	$questionData = dataGrabber("File Response");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$difficulty = $_POST['difficulty'];
		$link = $_POST['link'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$totalFiles = $_POST['totalFiles'];
		$answer = mysql_real_escape_string($_POST['answer']);
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
		if (is_uploaded_file($_FILES['answer'] ['tmp_name'])) {				
			$tempFile = $_FILES['answer'] ['tmp_name'];
			$targetFile = basename($_FILES['answer'] ['name']);
			$uploadDir = $monitor['directory'] . "test/answers";
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
				if (isset ($questionData)) {
					unlink ($monitor['directory'] . "test/answers/" . $questionData['fileURL']);
				}
				
				$fileURL = $targetFile;
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
			if (isset ($questionData)) {
				$fileURL = $questionData['fileURL'];
			} else {
				$fileURL = "";
			}
		}
				
		if (isset ($questionData)) {
			updateQuery($monitor['type'], "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `link` = '{$link}', `tags` = '{$tags}', `totalFiles` = '{$totalFiles}', `fileURL` = '{$fileURL}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
			
			redirect($monitor['redirect'] . "?updated=question");	
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($monitor['type'], "NULL, '0', '0', '{$lastQuestion}', 'File Response', '{$points}', '{$extraCredit}', '0', '{$difficulty}', '{$link}', '0', '{$totalFiles}', '', '1', '{$tags}', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?inserted=question");
		}
	}
	
//Title
	title($monitor['title'] . "File Response", "A file response is a question that must be responded to in the form of an uploaded file, such as a video or a PDF. Files responses must be scored manually.");
	
//File response form
	form("fileResponse", "post", true, true, false, " return errorsOnSubmit(this, 'answer');");
	catDivider("Question", "one", true);
	echo "<blockquote>";
	question();
	echo "</blockquote>";
	
	catDivider("Question Settings", "two");
	echo "<blockquote>";
	points();
	difficulty();
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
	fileUpload("answer", "answer", false, false, false, false, "questionData", "fileURL", $monitor['gatewayPath'] . "test/answers", true);
	echo "</blockquote></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback();
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>