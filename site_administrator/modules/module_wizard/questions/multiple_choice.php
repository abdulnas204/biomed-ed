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
			if ($testDataCheck['type'] == "Multiple Choice") {
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
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['choice']) && !empty($_POST['answer'])) {
	//If the page is updating an item
		if (isset ($update)) {
			$currentModule = strtolower($_SESSION['currentModule']);
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Detirmine what kind of user interface this will have, either checkboxes or bullets
			if (sizeof($_POST['choice']) == "1") {
				$interface = "radio";
			} elseif (sizeof($_POST['choice']) > "1") {
				$interface = "checkbox";
			} elseif (sizeof($_POST['choice']) == "0") {
				header ("Location: multiple_choice.php");
				exit;
			}
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$difficulty = $_POST['difficulty'];
			$category = mysql_real_escape_string($_SESSION['category']);
			$link = $_POST['link'];
			$partialCredit = $_POST['partialCredit'];
			$randomize = $_POST['randomize'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$questionValue = serialize($_POST['choice']);
			$answerValue = mysql_real_escape_string(serialize($_POST['answer']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
			$updateChoiceQuery = "UPDATE moduletest_{$currentTable} SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `randomize` = '{$randomize}', `partialCredit` = '{$partialCredit}', `choiceType` = '{$interface}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}' WHERE id = '{$update}'";
							
			$updateChoice = mysql_query($updateChoiceQuery, $connDBA);
			header ("Location: ../test_content.php?updated=choice");
			exit;
	//If the page is inserting an item		
		} else {
			$currentModule = strtolower($_SESSION['currentModule']);
			$currentTable = strtolower(str_replace(" ","", $currentModule));
			
		//Get the last test question, and add one to the value for the next test
			$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
			
		//Detirmine what kind of user interface this will have, either checkboxes or bullets
			if (sizeof($_POST['choice']) == "1") {
				$interface = "radio";
			} elseif (sizeof($_POST['choice']) > "1") {
				$interface = "checkbox";
			} elseif (sizeof($_POST['choice']) == "0") {
				header ("Location: multiple_choice.php");
				exit;
			}
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$difficulty = $_POST['difficulty'];
			$category = mysql_real_escape_string($_SESSION['category']);
			$link = $_POST['link'];
			$partialCredit = $_POST['partialCredit'];
			$randomize = $_POST['randomize'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$questionValue = serialize($_POST['choice']);
			$answerValue = mysql_real_escape_string(serialize($_POST['answer']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
			
		
			$insertChoiceQuery = "INSERT INTO moduletest_{$currentTable} (
							`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (
							NULL, '0', '0', '{$lastQuestion}', 'Multiple Choice', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$category}', '{$link}', '{$randomize}', '0', '{$interface}', '1', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'
							)";
							
			$insertChoice = mysql_query($insertChoiceQuery, $connDBA);
			header ("Location: ../test_content.php?inserted=choice");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Multiple Choice"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/showHide.js" type="text/javascript"></script>
<script src="../../../../javascripts/insert/newMultipleChoice.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Multiple Choice</h2>
    <p>A multiple choice question will prompt a user to select the correct answer(s) from a list of choices.</p>
    <p>&nbsp;</p>
	<form action="multiple_choice.php<?php
		if (isset ($update)) {
			echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
		}
    ?>" method="post" name="choice" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Question</div>
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
      <div class="catDivider two">Question Settings</div>
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
				$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
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
							$descriptionImportGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$importID}'", $connDBA);
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
        <p>Allow partial credit:</p>
        <blockquote>
          <p>
            <label>
              <input type="radio" name="partialCredit" value="1" id="partialCredit_0" onchange="toggleSimpleDiv(this.value);"<?php if (isset ($update)) { if ($testData['partialCredit'] == "1") { echo " checked=\"checked\"";}} ?> />
              Yes</label>
            <label>
              <input type="radio" name="partialCredit" value="0" id="partialCredit_1" onchange="toggleSimpleDiv(this.value);"<?php if (isset ($update)) { if ($testData['partialCredit'] == "0") { echo " checked=\"checked\"";}} else { echo " checked=\"checked\"";} ?> />
              No</label>
          </p>
        </blockquote>
        <p>Randomize values:</p>
        <blockquote>
          <p>
            <label>
              <input type="radio" name="randomize" value="1" id="randomize_0"<?php if (isset ($update)) { if ($testData['randomize'] == "1") { echo " checked=\"checked\"";}} ?> />
              Yes</label>
            <label>
              <input type="radio" name="randomize" value="0" id="randomize_1"<?php if (isset ($update)) { if ($testData['randomize'] == "0") { echo " checked=\"checked\"";}} else { echo " checked=\"checked\"";} ?> />
              No</label>
<br />
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
      <div class="catDivider three">Question Content</div>
      <div class="stepContent">
      <blockquote>
      <p>Question content<span class="require">* (Select correct answers)</span>: <a href="../help.php?tab=4" target="_blank"><img src="../../../../images/admin_icons/help.png" alt="Help" width="17" height="17" /></a></p>
      <div>
      <blockquote>
		<?php
			//Grab all of the answers and values if the question is being edited
				if (isset ($update)) {	
					$answers = unserialize($testData['answerValue']);
					echo "<table width=\"100%\" border=\"0\"><tr><td width=\"10\">";

				//Echo each checkbox item	
					echo "<table width=\"10\" name=\"choices\" id=\"choices\">";
					$start = sizeof (unserialize($testData['answerValue']));
					for ($i = 1; $i <= $start; $i++) {
						echo "<tr><td><div style=\"padding:2px;\"><input type=\"checkbox\" name=\"choice[]\" id=\"c" . $i . "\" value=\"";
						echo $i;
						echo "\" class=\"validate[minCheckbox[1]]\"";
						$questions = unserialize($testData['questionValue']);
						while (list($questionKey, $questionArray) = each($questions)) {
                    		if ($i == $questionArray) {
								echo " checked=\"checked\"";
							}
						}
						echo " /></div></td></tr>";
					}
					echo "</table>";
					
					echo "</td><td>";
					
				//Echo each value
					echo "<table width=\"50%\" name=\"answers\" id=\"answers\">";
					while (list($answerKey, $answerArray) = each($answers)) {
						$id = $answerKey+1;
                    	echo "<tr><td><input type=\"text\" name=\"answer[]\" autocomplete=\"off\" id=\"a" . $id . "\" value=\""; echo stripslashes(htmlentities($answerArray));  echo "\" class=\"validate[required]\" size=\"50\" /></td></tr>";
					}
					echo "</table>";
					
					echo "</td></tr></table>";
			//Echo empty fields if the page is not editing a question
				} else {					
					echo "<table width=\"100%\" border=\"0\"><tr><td width=\"10\"><table width=\"10\" name=\"choices\" id=\"choices\"><tr><td><div style=\"padding:2px;\"><input type=\"checkbox\" name=\"choice[]\" id=\"c1\" value=\"1\" class=\"validate[minCheckbox[1]]\" /></div></td></tr><tr><td><div style=\"padding:2px;\"><input type=\"checkbox\" name=\"choice[]\" id=\"c2\" value=\"2\" class=\"validate[minCheckbox[1]]\" /></div></td></tr></table></td><td><table width=\"50%\" name=\"answers\" id=\"answers\"><tr><td><input type=\"text\" name=\"answer[]\" autocomplete=\"off\" id=\"a1\" size=\"50\" class=\"validate[required]\" /></td></tr><tr><td><input type=\"text\" name=\"answer[]\" autocomplete=\"off\" id=\"a2\" size=\"50\" class=\"validate[required]\" /></td></tr></table></td></tr></table>";
				}
			?>
        </blockquote>
         <p><input value="Add Another Option" type="button" onclick="appendRow('choices', '<div style=\'padding:2px;\'><input type=\'checkbox\' name=\'choice[]\' id=\'c', '\' value=\'', '\' class=\'validate[minCheckbox[1]]\' /></div>');appendRow('answers', '<input type=\'text\' name=\'answer[]\' autocomplete=\'off\' id=\'a', '\' size=\'50\' class=\'validate[required]\' /><!--', '//-->')" />
          <input value="Remove Last Option" type="button" onclick="deleteLastRow('choices');deleteLastRow('answers')" />
        </p>
      </div>
      </blockquote>
      </div>
      <div class="catDivider four">Feedback</div>
      <div class="stepContent">
      <blockquote>
        <p>Feedback for correct answer:</p>
        <blockquote>
          <p>
            <textarea id="feedBackCorrect" name="feedBackCorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['correctFeedback']);
			}
		    ?></textarea>
          </p>
        </blockquote>
        <div id="contentHide"<?php if (isset ($update)) {if ($testData['partialCredit'] == "0") {echo " class=\"contentHide\"";}} else {echo " class=\"contentHide\"";}?>>
          <p>Feedback for partially correct answer:</p>
          <blockquote>
            <p>
            <textarea id="feedBackPartial" name="feedBackPartial" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['partialFeedback']);
			}
		    ?></textarea>
            </p>
          </blockquote>
        </div>
        <p>Feedback for incorrect answer: </p>
        <blockquote>
          <p>
            <textarea id="feedBackIncorrect" name="feedBackIncorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['incorrectFeedback']);
			}
		    ?></textarea>
          </p>
        </blockquote
        >
      </blockquote>
      </div>
      <div class="catDivider five">Finish</div>
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
var sprytextarea1 = new Spry.Widget.ValidationTextarea("directionsCheck", {validateOn:["change"]});
//-->
</script>
</body>
</html>