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
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			$location = str_replace(" ","", $_SESSION['currentModule']);
		
		//If a new file is uploaded
			if ($_FILES['answer'] ['name'] !== "") {
			//Make the directories
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
				if (!file_exists("../../../../modules/{$location}/test/fileresponse/answer")) {
					mkdir("../../../../modules/{$location}/test/fileresponse/answer", 0777);
				}	
				
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
				$difficulty = $_POST['difficulty'];
				$category = mysql_real_escape_string($_SESSION['category']);
				$link = $_POST['link'];
				$tags = mysql_real_escape_string($_POST['tags']);
				$totalFiles = $_POST['totalFiles'];
				$answer = mysql_real_escape_string($_POST['answer']);
				$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
				$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
				$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
			
				$updateFileQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `tags` = '{$tags}', `totalFiles` = '{$totalFiles}', `fileURL` = '{$fileURL}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}' WHERE id = '{$update}'";
		//If a new file is not uploaded	
			} else {	
			//Get form data values
				$question = mysql_real_escape_string($_POST['question']);
				$points = $_POST['points'];
				$extraCredit = $_POST['extraCredit'];
				$difficulty = $_POST['difficulty'];
				$category = mysql_real_escape_string($_SESSION['category']);
				$link = $_POST['link'];
				$tags = mysql_real_escape_string($_POST['tags']);
				$totalFiles = $_POST['totalFiles'];
				$answer = mysql_real_escape_string($_POST['answer']);
				$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
				$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
				$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
			
				$updateFileQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `tags` = '{$tags}', `totalFiles` = '{$totalFiles}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}' WHERE id = '{$update}'";
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
			$location = str_replace(" ", "", $_SESSION['currentModule']);
			
		//Make the directories
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
			if (!file_exists("../../../../modules/{$location}/test/fileresponse/answer")) {
				mkdir("../../../../modules/{$location}/test/fileresponse/answer", 0777);
			}
		
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
			$difficulty = $_POST['difficulty'];
			$category = mysql_real_escape_string($_SESSION['category']);
			$link = $_POST['link'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$totalFiles = $_POST['totalFiles'];
			$answer = mysql_real_escape_string($_POST['answer']);
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
			
		
			$insertFileResponseQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (
							NULL, '0', '0', '{$lastQuestion}', 'File Response', '{$points}', '{$extraCredit}', '0', '{$difficulty}', '{$category}', '{$link}', '0', '{$totalFiles}', '', '1', '{$tags}', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'
							)";
							
			$insertFileResponse = mysql_query($insertFileResponseQuery, $connDBA);
			header ("Location: ../test_content.php?inserted=file");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : File Response"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body onload="MM_showHideLayers('progress','','hide')"<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : File Response</h2>
<p>A file response is a question that must be responded to in the form of an uploaded file, such as a video or a PDF. Files responses must be scored manually.</p>
    <p>&nbsp;</p>
    <form action="file_response.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" method="post" enctype="multipart/form-data" name="fileResponse" onsubmit="return errorsOnSubmit(this, 'true', 'answer', 'false');" id="validate">
      <div class="catDivider"><img src="../../../../images/numbering/1.gif" alt="1." width="22" height="22" /> Question</div>
      <div class="stepContent">
      <blockquote>
        <p>Question directions<span class="require">*</span>:</p>
        <blockquote>
          <p><span id="directionsCheck">
          <textarea id="question" name="question" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['question']);
			}
		  ?></textarea>
          <span class="textareaRequiredMsg"></span></span></p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/2.gif" alt="2." width="22" height="22" /> Question Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Question points<span class="require">*</span>:</p>
        <blockquote>
          <p>
            <input name="points" type="text" id="points" size="5" autocomplete="off" maxlength="5" class="validate[required,custom[onlyNumber]]"<?php
                if (isset ($update)) {
                    echo " value=\"" . $testData['points'] . "\"";
                }
              ?> />
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
        </blockquote>
        <p>Difficulty:</p>
        <blockquote>
          <p>
            <select name="difficulty" id="difficulty">
              <option value="Easy"<?php if (isset ($update)) {if ($testData['difficulty'] == "Easy") {echo " selected=\"selected\"";}} else {if ($_SESSION['difficulty'] == "Easy") {echo " selected=\"selected\"";}} ?>>Easy</option>
              <option value="Average"<?php if (isset ($update)) {if ($testData['difficulty'] == "Average") {echo " selected=\"selected\"";}} else {if ($_SESSION['difficulty'] == "Average") {echo " selected=\"selected\"";}} ?>>Average</option>
              <option value="Difficult"<?php if (isset ($update)) {if ($testData['difficulty'] == "Difficult") {echo " selected=\"selected\"";}} else {if ($_SESSION['difficulty'] == "Difficult") {echo " selected=\"selected\"";}} ?>>Difficult</option>
            </select>
          </p>
        </blockquote>
        <p>Link to description:</p>
        <blockquote>
          <p>
            <select name="link" id="link">
              <?php
			//Select all of the descriptions in this test
				$currentTable = str_replace(" ", "", $_SESSION['currentModule']);
				$descriptionCheck = mysql_query("SELECT * FROM `moduletest_{$currentTable}`", $connDBA);
				
				if (mysql_fetch_array($descriptionCheck)) {
					$descriptionGrabber = mysql_query("SELECT * FROM `moduletest_{$currentTable}` ORDER BY `position` ASC", $connDBA);
					
					echo "<option value=\"\">- Select -</option>";
					while ($description = mysql_fetch_array($descriptionGrabber)) {
						if ($description['type'] == "Description") {
							echo "<option value=\"" . $description['id'] ."\"";
							if (isset($update)) {
								if ($testData['link'] == $description['id']) {
									echo " selected=\"selected\"";
								}
							}
							echo ">" . $description['position'] . ". " . stripslashes(htmlentities(commentTrim(25, $description['question']))) . "</option>";
						}
						
						if ($description['questionBank'] == "1") {
							$importID = $description['linkID'];
							$descriptionImportGrabber = mysql_query("SELECT * FROM `questionBank` WHERE `id` = '{$importID}'", $connDBA);
							$descriptionImport = mysql_fetch_array($descriptionImportGrabber);
							
							if ($descriptionImport['type'] == "Description") {
								echo "<option value=\"" . $description['id'] ."\"";
							if (isset($update)) {
								if ($testData['link'] == $description['id']) {
									echo " selected=\"selected\"";
								}
							}
							echo ">" . $description['position'] . ". " . stripslashes(htmlentities(commentTrim(25, $descriptionImport['question']))) . "</option>";
							}
							
							unset($importID);
							unset($descriptionImportGrabber);
							unset($descriptionImport);
						}
					}
				} else {
					echo "<option value=\"\">- None -</option>";
				}
			?>
            </select>
          </p>
        </blockquote>
<p>Number of files the student is premitted to upload:        </p>
        <blockquote>
          <p>
            <select name="totalFiles" id="totalFiles">
              <option value="1"<?php if (isset ($update)) { if ($testData['totalFiles'] == "1") { echo " selected=\"selected\"";}} else { echo " selected=\"selected\"";} ?>>1</option>
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
        <p>Tags (Seperate with commas):</p>
        <blockquote>
          <p>
            <input name="tags" type="text" id="tags" size="50" autocomplete="off"<?php 
			  //If the page is updating an item
			  		if (isset ($update)) {
						echo " value=\"" . stripslashes(htmlentities($testData['tags'])) . "\"";
					}
			  ?> />
          </p>
        </blockquote>
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
          <input name="answer" type="file" id="answer" size="50" />
          <br />Max file size: <?php echo ini_get('upload_max_filesize'); ?>
        </p>
      </blockquote>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/4.gif" alt="4." width="22" height="22" /> Feedback</div>
      <div class="stepContent">
      <blockquote>
        <p>Feedback for correct answer: </p>
        <blockquote>
          <p>
          <textarea id="feedBackCorrect" name="feedBackCorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['correctFeedback']);
			}
		  ?></textarea>
          </p>
        </blockquote>
        <p>Feedback for partially correct answer:</p>
        <blockquote>
          <p>
            <textarea id="feedBackPartial" name="feedBackPartial" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['partialFeedback']);
			}
		  ?>
            </textarea>
          </p>
        </blockquote>
<p>Feedback for incorrect answer: </p>
        <blockquote>
          <p>
          <textarea id="feedBackIncorrect" name="feedBackIncorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['incorrectFeedback']);
			}
		  ?></textarea>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/5.gif" alt="5." width="22" height="22" /> Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php
          	  submit("submit", "Submit");
		  ?>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../test_content.php');return document.MM_returnValue" value="Cancel" />
        </p>
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