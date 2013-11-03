<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionBank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "True False") {
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
	if (isset ($_POST['submit']) && !empty ($_POST['question']) && !empty ($_POST['points']) && !empty ($_POST['answer'])) {
		//If the page is updating an item
		if (isset ($update)) {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$answer = $_POST['answer'];
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		
			$updateMatchingQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
							
			$updateMatching = mysql_query($updateMatchingQuery, $connDBA);
			header ("Location: ../test_content.php?updated=truefalse");
			exit;
	//If the page is inserting an item		
		} else {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
			//Get the last test question, and add one to the value for the next test
			$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$answer = $_POST['answer'];
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertChoiceQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$lastQuestion}', 'True False', '{$points}', '{$extraCredit}', '0', '0', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackInorrect}'
							)";
							
			$insertChoice = mysql_query($insertChoiceQuery, $connDBA);
			header ("Location: ../test_content.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Insert True or False Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Insert True or False Question</h2>
    <p>Creates a bulleted true or false response to a question.</p>
    <p>&nbsp;</p>
    <form action="true_false.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" method="post" name="trueFalse" id="validate" onsubmit="return errorsOnSubmit(this);">
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
      </div>
        <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Answer</div>
        <div class="stepContent">
        <blockquote>
          <p> Select the bullet with the correct answer<span class="require">*</span>.</p>
          <p>
            <input type="radio" name="answer" value="1" id="true" class="validate[required] radio"<?php
				if (isset ($update)) {
					if ($testData['answer'] == "1") {
						echo " checked=\"checked\"";
					}
				}
			  ?> />
True </p>
          <p>
            <input type="radio" name="answer" value="0" id="false" class="validate[required] radio"<?php
				if (isset ($update)) {
					if ($testData['answer'] == "0") {
						echo " checked=\"checked\"";
					}
				}
			  ?> />
False </p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/4.gif" alt="4." width="22" height="22" /> Feedback</div>
      <div class="stepContent">
      <blockquote>
        <p>Feedback for Correct Answer:</p>
        <blockquote>
          <p>
          <textarea id="feedBackCorrect" name="feedBackCorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['correctFeedback']);
			}
		  ?></textarea>
          </p>
        </blockquote>
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
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../test_content.php');return document.MM_returnValue" value="Cancel" />
          </label>
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