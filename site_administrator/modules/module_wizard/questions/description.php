<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this step has not yet been reached in the module setup
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
		$testCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$name}'", $connDBA);
		$testCheckArray = mysql_fetch_array($testCheckGrabber);
		
		if ($testCheckArray['test'] == "0") {
			header ("Location: ../test_check.php");
			exit;
		}
	} else {
		header ("Location: ../../index.php");
		exit;
	}
?>
<?php
//If the page is updating an item
	if (isset ($_GET['question']) && isset ($_GET['id'])) {
		$update = $_GET['id'];
		$currentModule = strtolower($_SESSION['currentModule']);
		$currentTable = strtolower(str_replace(" ","", $currentModule));
		$testDataGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Description") {
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
	if (isset ($_POST['submit']) && !empty($_POST['question'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$currentModule = strtolower($_SESSION['currentModule']);
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$category = mysql_real_escape_string($_SESSION['category']);
			$tags = mysql_real_escape_string($_POST['tags']);
		
			$updateDescriptionQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}' WHERE id = '{$update}'";
							
			$updateDescription = mysql_query($updateDescriptionQuery, $connDBA);
			header ("Location: ../test_content.php?updated=description");
			exit;
	//If the page is inserting an item		
		} else {
			$currentModule = strtolower($_SESSION['currentModule']);
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
			//Get the last test question, and add one to the value for the next test
			$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$category = mysql_real_escape_string($_SESSION['category']);
			$tags = mysql_real_escape_string($_POST['tags']);
		
			$insertDescriptionQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (
							NULL, '0', '', '{$lastQuestion}', 'Description', '0', '', '0', '', '{$category}', '0', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''
							)";
							
			$insertDescription = mysql_query($insertDescriptionQuery, $connDBA);
			header ("Location: ../test_content.php?inserted=description");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Description"); ?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
    <h2>Module Setup Wizard : Description</h2>
    <p>A description is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.</p>
    <p>&nbsp;</p>
    <form action="description.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" method="post" name="description" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Description content<span class="require">*</span>: </p>
      <blockquote>
            <p align="left"><span id="questionCheck">
              <textarea name="question" id="question" cols="45" rows="5" style="width:640px; height:320px;"><?php 
			  //If the page is updating an item
			  		if (isset ($update)) {
						echo stripslashes($testData['question']);
					}
			  ?></textarea>
            <span class="textareaRequiredMsg"></span></span></p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Settings</div>
      <div class="stepContent">
        <blockquote>
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
      <div class="catDivider three">Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php submit("submit", "Submit"); ?>
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
var sprytextarea1 = new Spry.Widget.ValidationTextarea("questionCheck", {validateOn:["change"]});
//-->
</script>
</body>
</html>