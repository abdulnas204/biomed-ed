<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionBank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "File Response") {
				$testData = $testDataCheck;
			} else {
				header ("Location: ../index.php");
				exit;
			}
		} else {
			header ("Location: ../index.php");
			exit;
		}
	}
//Process the form
	if (isset ($_POST['submit']) && !empty ($_POST['question']) && !empty ($_POST['points']) && !empty ($_POST['category'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$location = "questionBank";
		
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
				$category = $_POST['category'];
				$category = $_POST['category'];
				$extraCredit = $_POST['extraCredit'];
				$totalFiles = $_POST['totalFiles'];
				$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
				$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
				$updateFileQuery = "UPDATE questionBank SET `category` = '{$category}', `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `totalFiles` = '{$totalFiles}', `fileURL` = '{$fileURL}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
		//If a new file is not uploaded	
			} else {	
			//Get form data values
				$question = mysql_real_escape_string($_POST['question']);
				$points = $_POST['points'];
				$category = $_POST['category'];
				$extraCredit = $_POST['extraCredit'];
				$totalFiles = $_POST['totalFiles'];
				$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
				$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
				$updateFileQuery = "UPDATE questionBank SET `question` = '{$question}', `points` = '{$points}', `category` = '{$category}', `extraCredit` = '{$extraCredit}', `totalFiles` = '{$totalFiles}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
			}
			
			$updateFile = mysql_query($updateFileQuery, $connDBA);
			$location = urlencode($category);
			header ("Location: ../index.php?updated=file&category=" . $location);
			exit;
	//If the page is inserting an item		
		} else {
			$location = "questionBank";
					
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
			$category = $_POST['category'];
			$extraCredit = $_POST['extraCredit'];
			$totalFiles = $_POST['totalFiles'];
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertFileResponseQuery = "INSERT INTO questionBank (
							`id`, `category`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$category}', 'File Response', '{$points}', '{$extraCredit}', '', '{$totalFiles}', '1', '{$question}', '', '', '', '{$fileURL}', '{$feedBackCorrect}', '{$feedBackInorrect}'
							)";
							
			$insertFileResponse = mysql_query($insertFileResponseQuery, $connDBA);
			$location = urlencode($category);
			header ("Location: ../index.php?inserted=file&category=" . $location);
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Question Bank : File Response Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Question Bank : File Response Question</h2>
<p>A file response is a question which must be responded to in the form of an uploaded file, such as a video or a PDF. These questions must be scored manually.</p>
    <p>&nbsp;</p>
    <form action="file_response.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" enctype="multipart/form-data" name="fileResponse" onsubmit="return errorsOnSubmit(this, 'answer', 'false');" id="validate">
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
              Extra Credit</label>
        </p>
        <p>
        Category<span class="require">*</span>: 
            <select name="category" id="category" class="validate[required]">
            <?php
            //Select all of the category items
                $categoryGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
                //If the module is being edited
                if (isset($update)) {
                    echo "<option value=\"\">- Select -</option>";
                    while ($category = mysql_fetch_array($categoryGrabber)) {
                        echo "<option value=\"" . stripslashes($category['category']) . "\"";
                        
                        if ($category['category'] == $testData['category']) {
                            echo " selected=\"selected\"";
                        }
                        
                        echo ">" . stripslashes($category['category']) . "</option>";
                    }
                } else {
                    echo "<option selected=\"selected\" value=\"\">- Select -</option>";
                    while ($category = mysql_fetch_array($categoryGrabber)) {
                        echo "<option value=\"" . stripslashes($category['category']) . "\"";
						if (isset ($_SESSION['category']) && urldecode($_SESSION['category']) == stripslashes($category['category'])) {
							echo " selected=\"selected\"";
                        }
						echo ">" . stripslashes($category['category']) . "</option>";
                    }
                }
            ?>
            </select>
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
        <p>Provide an exmaple of a correct answer: 
        <?php 
			if (isset ($update)) {
				echo "<br /><strong>Note:</strong> Uploading a new file will replace the exising file";
			}
		?>
        </p>
        <blockquote>
        <p>
        <?php
		//Display current file if it exists
			if (isset ($update)) {
			//First strip any spaces from the session name for use as directory name
				$location = "questionBank";
			
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
          <input name="answer" type="file" id="answer" size="50" />
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
          <?php submit("submit", "Submit"); ?>
          </label>
          <label>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          </label>
          <label>
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../index.php<?php if (isset ($_SESSION['category'])) {echo "?category=" . urldecode($_SESSION['category']);} ?>');return document.MM_returnValue" value="Cancel" />
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