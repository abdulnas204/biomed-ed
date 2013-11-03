<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM feedback WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Multiple Choice") {
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
	if (isset ($_POST['submit']) && !empty($_POST['question']) && !empty($_POST['questionValue'])) {
	//If the page is updating an item
		if (isset ($update)) {			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$choiceType = $_POST['type'];
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
		
			$updateChoiceQuery = "UPDATE feedback SET `choiceType` = '{$choiceType}', `question` = '{$question}', `questionValue` = '{$questionValue}' WHERE id = '{$update}'";
							
			$updateChoice = mysql_query($updateChoiceQuery, $connDBA);
			header ("Location: ../index.php?updated=choice");
			exit;
	//If the page is inserting an item		
		} else {
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$choiceType = $_POST['type'];
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
			
			//Get the last feedBack question, and add one to the value for the next feedBack
			$lastQuestionGrabber = mysql_query("SELECT * FROM feedback ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
		
			$insertChoiceQuery = "INSERT INTO feedback (
							`id`, `type`, `position`, `choiceType`, `question`, `questionValue`
							) VALUES (
							NULL, 'Multiple Choice', '{$lastQuestion}', '{$choiceType}', '{$question}', '{$questionValue}'
							)";
							
			$insertChoice = mysql_query($insertChoiceQuery, $connDBA);
			header ("Location: ../index.php?inserted=choice");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Feedback : Multiple Choice"); ?>
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
    <h2>Feedback : Multiple Choice</h2>
    <p>A multiple choice question will prompt a user to select their opinion on a question from a list of choices.</p>
    <p>&nbsp;</p>
	<form action="multiple_choice.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
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
      <div class="catDivider two">Settings</div>
      <div class="stepContent">
        <blockquote>
          <p>Question type:</p>
          <blockquote>
            <p>
              <select name="type" id="type">
                <option value="radio"<?php if (isset ($update)) {if ($testData['choiceType'] == "radio") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>Bullet (Single Answer)</option>
                <option value="checkbox"<?php if (isset ($update)) {if ($testData['choiceType'] == "checkbox") {echo " selected=\"selected\"";}} ?>>Checkbox (Multiple Answers)</option>
              </select>
            </p>
          </blockquote>
        </blockquote>
      </div>
      <div class="catDivider three">Question Content</div>
      <div class="stepContent">
      <blockquote>
      <p>Question content<span class="require">*</span>:</p>
      <div>
      <blockquote>
		<?php
			//Grab all of the answers and values if the question is being edited
				if (isset ($update)) {	
					$values = unserialize($testData['questionValue']);
					
					echo "<table width=\"50%\" name=\"values\" id=\"values\">";
					while (list($valueKey, $valueArray) = each($values)) {
						$id = $valueKey+1;
                    	echo "<tr><td><input type=\"text\" name=\"questionValue[]\" autocomplete=\"off\" id=\"c" . $id . "\" value=\""; echo stripslashes(htmlentities($valueArray));  echo "\" class=\"validate[required]\" size=\"50\" /></td></tr>";
					}
					echo "</table>";
			//Echo empty fields if the page is not editing a question
				} else {					
					echo "<table width=\"50%\" name=\"values\" id=\"values\"><tr><td><input type=\"text\" name=\"questionValue[]\" autocomplete=\"off\" id=\"c1\" size=\"50\" class=\"validate[required]\" /></td></tr><tr><td><input type=\"text\" name=\"questionValue[]\" autocomplete=\"off\" id=\"c2\" size=\"50\" class=\"validate[required]\" /></td></tr></table>";
				}
			?>
        </blockquote>
         <p><input value="Add Another Option" type="button" onclick="appendRow('values', '<input type=\'text\' name=\'questionValue[]\' autocomplete=\'off\' id=\'c', '\' size=\'50\' class=\'validate[required]\' /><!--', '//-->')" />
          <input value="Remove Last Option" type="button" onclick="deleteLastRow('values')" />
        </p>
      </div>
      </blockquote>
      </div>
      <div class="catDivider four">Finish</div>
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