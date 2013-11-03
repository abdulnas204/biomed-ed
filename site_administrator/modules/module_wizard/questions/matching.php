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
			if ($testDataCheck['type'] == "Matching") {
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
	if (isset ($_POST['submit']) && isset ($_POST['question']) && isset ($_POST['points']) && isset ($_POST['questionValue']) && isset ($_POST['answerValue'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$currentModule = $_SESSION['currentModule'];
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$partialCredit = $_POST['partialCredit'];
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
			$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		
			$updateMatchingQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `partialCredit` = '{$partialCredit}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
							
			$updateMatching = mysql_query($updateMatchingQuery, $connDBA);
			header ("Location: ../test_content.php?updated=matching");
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
			$partialCredit = $_POST['partialCredit'];
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
			$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertMatchingQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$lastQuestion}', 'Matching', '{$points}', '{$extraCredit}', '{$partialCredit}', '0', '1', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackInorrect}'
							)";
							
			$insertMatching = mysql_query($insertMatchingQuery, $connDBA);
			header ("Location: ../test_content.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<?php title("Module Setup Wizard : Insert Matching Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../../javascripts/insert/newTextFieldAdvanced.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Insert Matching Question</h2>
	<form name="matching" method="post" action="matching.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
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
        <p>Allow Partial Credit:
          <label>
            <select name="partialCredit" id="partialCredit">
              <option value="1"<?php if (isset ($update)) { if ($testData['partialCredit'] == "1") { echo " selected=\"selected\"";}} ?>>Yes</option>
              <option value="0"<?php if (isset ($update)) { if ($testData['partialCredit'] == "0") { echo " selected=\"selected\"";}} ?>>No</option>
            </select>
          </label>
        </p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Question Content</div>
      <div class="stepContent">
      </blockquote>
      <blockquote>
        <p>Question Content<span class="require">*</span>:<br />
        </p>
        <table width="100%" border="0">
        <tr><td>
            <?php
			//Grab all of the answers and values if the question is being edited
				if (isset ($update)) {	
					$valueGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = '{$update}'", $connDBA);	
					$value = mysql_fetch_array($valueGrabber);
					$questions = unserialize($value['questionValue']);
					$answers = unserialize($value['answerValue']);
					
					echo "<table width=\"50%\" name=\"questions\" id=\"questions\"><tr>
							<th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Sentence</div></th>
						  </tr>";
					while (list($questionKey, $questionArray) = each($questions)) {
						echo "<tr><td><div align=\"center\"><label><input name=\"questionValue[]\" autocomplete=\"off\" type=\"text\" id=\"q"; echo $questionKey+1; echo "\" size=\"65\" class=\"validate[required]\" value=\""; echo stripslashes($questionArray);  echo "\" /></label></div>";
					}
					echo "</table>";
					echo "</td><td>";
					echo "<table width=\"50%\" name=\"answers\" id=\"answers\"><tr>
							<th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Values</div></th>
						  </tr>";
					while (list($answerKey, $answerArray) = each($answers)) {
						echo "<tr><td><div align=\"center\"><label><input name=\"answerValue[]\" autocomplete=\"off\" type=\"text\" id=\"a"; echo $answerKey+1; echo"\" size=\"65\" class=\"validate[required]\" value=\""; echo stripslashes($answerArray);  echo "\" /></label></div>";
					}
					echo "</table>";
			//Echo empty fields if the page is not editing a question
				} else {
					echo "<table width=\"50%\" name=\"questions\" id=\"questions\"><tr><th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Sentence</div></th></tr><tr><td><div align=\"center\"><label><input name=\"questionValue[]\" autocomplete=\"off\" type=\"text\" id=\"q1\" size=\"65\" class=\"validate[required]\" /></label></div></td></tr></table></td><td><table width=\"50%\" name=\"answers\" id=\"answers\"><tr><th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Values</div></th></tr><tr><td><div align=\"center\"><label><input name=\"answerValue[]\" autocomplete=\"off\" type=\"text\" id=\"a1\" size=\"65\" class=\"validate[required]\" /></label></div></td></tr></table>";
				}
			?>
            </td>
          </tr>
        </table>

        <div style="float:left"></span></div>
        <p>
          <input value="Add Another Line" type="button" onclick="appendRow('questions', '<div align=\'center\'><input name=\'questionValue[]\' type=\'text\' id=\'q', '\' autocomplete=\'off\' size=\'65\' class=\'validate[required]\' /></div>'); appendRow('answers', '<div align=\'center\'><input name=\'answerValue[]\' autocomplete=\'off\' type=\'text\' id=\'a', '\' size=\'65\' class=\'validate[required]\' /></div>')" />
          <input value="Remove Last Line" type="button" onclick="deleteLastRow('questions'); deleteLastRow('answers')" />
        </p>
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