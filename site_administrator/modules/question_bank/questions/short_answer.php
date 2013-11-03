<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['question']) && isset ($_GET['id'])) {
		$update = $_GET['id'];
		$currentModule = $_SESSION['currentModule'];
		$currentTable = strtolower(str_replace(" ","", $currentModule));
		$testDataGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Short Answer") {
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
	if (isset ($_POST['submit']) && isset ($_POST['question']) && isset ($_POST['points']) && isset ($_POST['answerValue'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$case = $_POST['case'];
			$answer = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		
			$updateShortAnswerQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `case` = '{$case}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
							
			$updateShortAnswer = mysql_query($updateShortAnswerQuery, $connDBA);
			header ("Location: ../test_content.php?updated=answer");
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
			$case = $_POST['case'];
			$answer = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertShortAnswerQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$lastQuestion}', 'Short Answer', '{$points}', '{$extraCredit}', '0', '0', '{$case}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackInorrect}'
							)";
							
			$insertShortAnswer = mysql_query($insertShortAnswerQuery, $connDBA);
			header ("Location: ../test_content.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Insert Short Answer Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../../javascripts/insert/newTextFieldSimple.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Insert Short Answer Question</h2>
    <p>This will insert a text box, which a user must provide a one or two word response to a question. These questions are scored automatically.</p>
<p>&nbsp;</p>
    <form action="short_answer.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" method="post" name="shortAnswer" id="validate" onsubmit="return errorsOnSubmit(this);">
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
          <input name="points" type="text" id="points" size="5" maxlength="5" autocomplete="off"  class="validate[required,custom[onlyNumber]]"<?php
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
			  ?> /> Extra Credit          </label>
        </p>
        <p>Ignore case: 
          <label>
          <select name="case" id="case">
            <option value="1"<?php if (isset ($update)) { if ($testData['case'] == "1") { echo " selected=\"selected\"";}} ?>>Yes</option>
            <option value="0"<?php if (isset ($update)) { if ($testData['case'] == "0") { echo " selected=\"selected\"";}} ?>>No</option>
          </select>
          </label>
        </p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Answer</div>
      <div class="stepContent">
      <blockquote>
        <p>Provide the correct answer(s)<span class="require">*</span>:</p>
        <blockquote>
        <?php
			//Grab all of the answers and values if the question is being edited
				if (isset ($update)) {	
					$valueGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$update}'", $connDBA);	
					$value = mysql_fetch_array($valueGrabber);
					$answers = unserialize($value['answer']);
					
					echo "<table width=\"50%\" name=\"answers\" id=\"answers\">";
					while (list($answerKey, $answerArray) = each($answers)) {
						$id = $answerKey+1;
						echo "<tr><td><label><input name=\"answerValue[]\" autocomplete=\"off\" type=\"text\" id=\"a" . $id . "\" size=\"50\" value=\""; echo stripslashes($answerArray);  echo "\" class=\"validate[required]\" /></label>";
					}
					echo "</table>";
			//Echo empty fields if the page is not editing a question
				} else {
					echo "<table id=\"answers\" border=\"0\"><tr><td><input name=\"answerValue[]\" type=\"text\" id=\"a1\" size=\"50\" autocomplete=\"off\" class=\"validate[required]\" /></td></tr></table>";
				}
			?>
            </blockquote>
        <p>
          <input value="Add Another Answer" type="button" onclick="appendRow('answers', '<input name=\'answerValue[]\' type=\'text\' id=\'a', '\' autocomplete=\'off\' size=\'50\' class=\'validate[required]\' />')" />
          <input value="Remove Last Answer" type="button" onclick="deleteLastRow('answers')" />
        </p>
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
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../test_content.php');return document.MM_returnValue" value="Cancel" />
          </label>
          <?php formErrors(); ?>
        </p>
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