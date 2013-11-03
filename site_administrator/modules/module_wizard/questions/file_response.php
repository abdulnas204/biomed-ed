<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
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
//If the page is updating an item
	if (isset ($_GET['question']) && isset ($_GET['id'])) {
		$update = $_GET['id'];
		$currentModule = $_SESSION['currentModule'];
		$currentTable = strtolower(str_replace(" ","", $currentModule));
		$testDataGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "File Response") {
				$testData = $testDataCheck;
			} else {
				header ("Location: ../test_content.php");
				exit;
			}
		} else {
			header ("Location: ../test_content.php");
			exit;
		}
	} elseif (isset ($_GET['question']) || isset ($_GET['id'])) {
		header ("Location: ../test_content.php");
		exit;
	}
//Process the form
	if (isset ($_POST['submit']) && isset ($_POST['question']) && isset ($_POST['points'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			$location = str_replace(" ","", $_SESSION['currentModule']);
		
		//If a new file is uploaded
			if ($_FILES['answer'] ['name'] !== "") {
			//Delete the old file
				$oldFile = $testData['fileURL'];
				$directory = "modules/{$location}/test/fileresponse/answer/";
				unlink ("../../../../". $directory . $oldFile);
				
			//Grab the uploaded file
				$tempFile = $_FILES['answer'] ['tmp_name'];
				$tempFileName = basename($_FILES['answer'] ['name']);
				$uploadDir = "../../../../modules/{$location}/test/fileresponse/answer";
			
			//Strip any underscores in the filename and replace it with a space to eliminate display errors
				$targetFile = str_replace("_"," ", $tempFileName);
				
			//Move the uploaded file
				move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile);
		
			//Provide answer link in database	
				$fileURL = "{$targetFile}";
			
			//Get form data values
				$question = mysql_real_escape_string($_POST['question']);
				$points = $_POST['points'];
				$extraCredit = $_POST['extraCredit'];
				$totalFiles = $_POST['totalFiles'];
				$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
				$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
				$updateFileQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `totalFiles` = '{$totalFiles}', `fileURL` = '{$fileURL}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
		//If a new file is not uploaded	
			} else {	
			//Get form data values
				$question = mysql_real_escape_string($_POST['question']);
				$points = $_POST['points'];
				$extraCredit = $_POST['extraCredit'];
				$totalFiles = $_POST['totalFiles'];
				$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
				$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
				$updateFileQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `totalFiles` = '{$totalFiles}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
			}
			
			$updateFile = mysql_query($updateFileQuery, $connDBA);
			header ("Location: ../test_content.php?updated=file");
			exit;
	//If the page is inserting an item		
		} else {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
			//Get the last test question, and add one to the value for the next test
			$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
			
		//Create a directory based off the current session
			//First strip any spaces from the session name for use as directory name
			$location = str_replace(" ","", $_SESSION['currentModule']);
			
			//Now make the directory
			mkdir("../../../../modules/{$location}/test", 0777);
			mkdir("../../../../modules/{$location}/test/fileresponse", 0777);
			mkdir("../../../../modules/{$location}/test/fileresponse/responses", 0777);
			mkdir("../../../../modules/{$location}/test/fileresponse/answer", 0777);
		
		//Grab the uploaded file
			$tempFile = $_FILES['answer'] ['tmp_name'];
			$tempFileName = basename($_FILES['answer'] ['name']);
			$uploadDir = "../../../../modules/{$location}/test/fileresponse/answer";
		
		//Strip any underscores in the filename and replace it with a space to eliminate display errors
			$targetFile = str_replace("_"," ", $tempFileName);
			
		//Move the uploaded file
			move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile);
	
		//Provide answer link in database	
			$fileURL = "{$targetFile}";
		
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$totalFiles = $_POST['totalFiles'];
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertFileResponseQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$lastQuestion}', 'File Response', '{$points}', '{$extraCredit}', '', '{$totalFiles}', '1', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackInorrect}'
							)";
							
			$insertFileResponse = mysql_query($insertFileResponseQuery, $connDBA);
			header ("Location: ../test_content.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Insert File Response Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body onload="MM_showHideLayers('progress','','hide')"<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Insert File Response Question</h2>
<p>A file response is a question that must be responded to in the form of an uploaded file, such as a video or a PDF. Files responses must be scored manually.</p>
    <p>&nbsp;</p>
    <form action="file_response.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" method="post" enctype="multipart/form-data" name="fileResponse" onsubmit="return errorsOnSubmit(this);" id="validate">
      <div class="catDivider"><img src="../../../../images/numbering/1.gif" alt="1." width="22" height="22" /> Question</div>
      <div class="stepContent">
      <blockquote>
        <p>Question Directions<span class="require">*</span>:</p>
        <blockquote>
          <p><span id="directionsCheck">
          <textarea id="question" name="question" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['question']);
			}
		  ?></textarea>
          <span class="textareaRequiredMsg"></span></span></p>
        </blockquote>
        <p>&nbsp;</p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/2.gif" alt="2." width="22" height="22" /> Question Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Question Points<span class="require">*</span>:
          <label>
          <input name="points" type="text" id="points" size="5" autocomplete="off" maxlength="5" class="validate[required,custom[onlyNumber]]"<?php
		  	if (isset ($update)) {
				echo " value=\"" . $testData['points'] . "\"";
			}
		  ?> />
          </label>
          <label>
              <input type="checkbox" name="extraCredit" id="extraCredit"<?php
				if (isset ($update)) {
					if ($testData['extraCredit'] == "on") {
						echo " checked=\"checked\"";
					}
				}
			  ?> />
              Extra Credit </label>
        </p>
        <p>Number of files the student is premitted to upload: 
          <select name="totalFiles" id="totalFiles">
            <option value="1"<?php if (isset ($update)) { if ($testData['totalFiles'] == "1") { echo " selected=\"selected\"";}} ?>>1</option>
            <option value="2"<?php if (isset ($update)) { if ($testData['totalFiles'] == "2") { echo " selected=\"selected\"";}} ?>>2</option>
            <option value="3"<?php if (isset ($update)) { if ($testData['totalFiles'] == "3") { echo " selected=\"selected\"";}} ?>>3</option>
            <option value="4"<?php if (isset ($update)) { if ($testData['totalFiles'] == "4") { echo " selected=\"selected\"";}} ?>>4</option>
            <option value="5"<?php if (isset ($update)) { if ($testData['totalFiles'] == "5") { echo " selected=\"selected\"";}} ?>>5</option>
            <option value="6"<?php if (isset ($update)) { if ($testData['totalFiles'] == "6") { echo " selected=\"selected\"";}} ?>>6</option>
            <option value="7"<?php if (isset ($update)) { if ($testData['totalFiles'] == "7") { echo " selected=\"selected\"";}} ?>>7</option>
            <option value="8"<?php if (isset ($update)) { if ($testData['totalFiles'] == "8") { echo " selected=\"selected\"";}} ?>>8</option>
            <option value="9"<?php if (isset ($update)) { if ($testData['totalFiles'] == "9") { echo " selected=\"selected\"";}} ?>>9</option>
            <option value="10"<?php if (isset ($update)) { if ($testData['totalFiles'] == "10") { echo " selected=\"selected\"";}} ?>>10</option>
          </select>
</p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Answer</div>
      <div class="stepContent">
      <blockquote>
        <p>Provide an exmaple of a correct answer: </p>
        <blockquote>
        <p>
        <?php
		//Display current file if it exists
			if (isset ($update)) {
			//First strip any spaces from the session name for use as directory name
				$location = str_replace(" ","", $_SESSION['currentModule']);
			
			//Prepare the directory string for future use
				$directory = "../../../../modules/{$location}/test/fileresponse/answer";
			
				if (file_exists($directory)) {
				//Narrow down the file results, if more than one exists, then use the database to select the correct file
					$file = str_replace("modules/{$location}/test/fileresponse/answer/", "", $testData['fileURL']);	
				
					$moduleDirectory = opendir("../../../../modules/{$location}/test/fileresponse/answer");
					$module = readdir($moduleDirectory);
					while ($module = readdir($moduleDirectory)) {
						//Leave out the "." and the ".."
						if (($module != ".") && ($module != "..") && ($module != "Resource id #3") && ($module == $file)) {
							echo "<br/>";
								echo "Current file: <a href=\"../../../../modules/{$location}/test/fileresponse/answer/" . $module . "\" target=\"_blank\">" . $module . "</a>";
						} 
					} 
				}
			}
		?>
        </p>
        <p>
          <label>
          <input name="answer" type="file" id="answer" size="50" onchange="validateField(this)" />
          </label>
          <br />Max file size: <?php echo ini_get('upload_max_filesize'); ?>
        </p>
      </blockquote>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/4.gif" alt="4." width="22" height="22" /> Feedback</div>
      <div class="stepContent">
      <blockquote>
        <p>Feedback for Correct Answer: </p>
        <blockquote>
          <p>
          <textarea id="feedBackCorrect" name="feedBackCorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['correctFeedback']);
			}
		  ?></textarea>
          </p>
        </blockquote>
        <p>&nbsp;</p>
        <p>Feedback for Incorrect Answer: </p>
        <blockquote>
          <p>
          <textarea id="feedBackIncorrect" name="feedBackIncorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['incorrectFeedback']);
			}
		  ?></textarea>
          </p>
        </blockquote>
        <p>&nbsp;</p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/5.gif" alt="5." width="22" height="22" /> Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <label>
          <?php
          	  fileSubmit("submit", "Submit", "'fileResponse', 'answer'");
		  ?>
          </label>
          <label>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          </label>
          <label>
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../test_content.php');return document.MM_returnValue" value="Cancel" />
          </label>
        </p>
        <div id="progress" style="display:none;">
          <p><span class="require">Uploading in progress... </span><img src="../../../../images/common/loading.gif" alt="Uploading" width="16" height="16" /></p>
        </div>
        <?php formErrors(); ?>
      </blockquote>
      </div>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
<script type="text/javascript">
<!--
var sprytextarea1 = new Spry.Widget.ValidationTextarea("directionsCheck", {validateOn:["change"]});
//-->
</script>
</body>
</html>