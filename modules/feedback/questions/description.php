<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$feedBackDataGrabber = mysql_query("SELECT * FROM feedback WHERE id = '{$update}'", $connDBA);
		if ($feedBackDataCheck = mysql_fetch_array($feedBackDataGrabber)) {
			if ($feedBackDataCheck['type'] == "Description") {
				$feedBackData = $feedBackDataCheck;
			} else {
				header ("Location: ../index.php");
				exit;
			}
		} else {
			header ("Location: ../index.php");
			exit;
		}
	} elseif (isset ($_GET['question']) || isset ($_GET['id'])) {
		header ("Location: ../index.php");
		exit;
	}
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question'])) {
	//If the page is updating an item
		if (isset ($update)) {			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
		
			$updateDescriptionQuery = "UPDATE feedback SET `question` = '{$question}' WHERE id = '{$update}'";
							
			$updateDescription = mysql_query($updateDescriptionQuery, $connDBA);
			header ("Location: ../index.php?updated=description");
			exit;
	//If the page is inserting an item		
		} else {						
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			
			//Get the last feedBack question, and add one to the value for the next feedBack
			$lastQuestionGrabber = mysql_query("SELECT * FROM feedback ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
		
			$insertDescriptionQuery = "INSERT INTO feedback (
							`id`, `type`, `position`, `choiceType`, `question`, `questionValue`
							) VALUES (
							NULL, 'Description', '{$lastQuestion}', '', '{$question}', ''
							)";
							
			$insertDescription = mysql_query($insertDescriptionQuery, $connDBA);
			header ("Location: ../index.php?inserted=description");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Feedback : Description"); ?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
    <h2>Feedback : Description</h2>
    <p>A description is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.</p>
    <p>&nbsp;</p>
    <form action="description.php<?php
		if (isset ($update)) {
			echo "?id=" . $feedBackData['id'];
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
						echo stripslashes($feedBackData['question']);
					}
			  ?></textarea>
            <span class="textareaRequiredMsg"></span></span></p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php submit("submit", "Submit"); ?>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../index.php');return document.MM_returnValue" value="Cancel" />
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