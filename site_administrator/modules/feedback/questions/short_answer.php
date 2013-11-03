<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM feedback WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Short Answer") {
				$testData = $testDataCheck;
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
		
			$updateShortAnswerQuery = "UPDATE feedback SET `question` = '{$question}' WHERE id = '{$update}'";
							
			$updateShortAnswer = mysql_query($updateShortAnswerQuery, $connDBA);
			header ("Location: ../index.php?updated=answer");
			exit;
	//If the page is inserting an item		
		} else {			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			
			//Get the last feedBack question, and add one to the value for the next feedBack
			$lastQuestionGrabber = mysql_query("SELECT * FROM feedback ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
		
			$insertShortAnswerQuery = "INSERT INTO feedback (
							`id`, `type`, `position`, `choiceType`, `question`, `questionValue`
							
							) VALUES (						
							NULL, 'Short Answer', '{$lastQuestion}', '', '{$question}', ''
							)";
							
			$insertShortAnswer = mysql_query($insertShortAnswerQuery, $connDBA);
			header ("Location: ../index.php?inserted=answer");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Feedback : Short Answer"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Feedback : Short Answer</h2>
    <p>A short answer is a question in which a user must provide a one or two word response.</p>
<p>&nbsp;</p>
    <form action="short_answer.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" name="shortAnswer" id="validate" onsubmit="return errorsOnSubmit(this);">
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
      <div class="catDivider"><img src="../../../../images/numbering/2.gif" alt="2." width="22" height="22" /> Finish</div>
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
var sprytextarea1 = new Spry.Widget.ValidationTextarea("directionsCheck", {validateOn:["change"]});
//-->
</script>
</body>
</html>