<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionBank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Fill in the Blank") {
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
	if (isset ($_POST['submit']) && !empty ($_POST['question']) && !empty ($_POST['points']) && !empty ($_POST['category']) && !empty ($_POST['questionValue']) && !empty ($_POST['answerValue'])) {
	//If the page is updating an item
		if (isset ($update)) {			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$category = $_POST['category'];
			$partialCredit = $_POST['partialCredit'];
			$case = $_POST['case'];
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
			$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		
			$updateBlankQuery = "UPDATE questionBank SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `category` = '{$category}', `partialCredit` = '{$partialCredit}', `case` = '{$case}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
							
			$updateBlank = mysql_query($updateBlankQuery, $connDBA);
			header ("Location: ../index.php?updated=blank");
			exit;
	//If the page is inserting an item		
		} else {					
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$category = $_POST['category'];
			$partialCredit = $_POST['partialCredit'];
			$case = $_POST['case'];
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
			$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertBlankQuery = "INSERT INTO questionBank (
							`id`, `category`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$category}', 'Fill in the Blank', '{$points}', '{$extraCredit}', '{$partialCredit}', '0', '{$case}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackInorrect}'
							)";
							
			$insertBlank = mysql_query($insertBlankQuery, $connDBA);
			header ("Location: ../index.php?inserted=blank");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Insert Fill in the Blank Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../../javascripts/insert/newTextFieldAdvanced.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>      
    <h2>Module Setup Wizard : Insert Fill in the Blank Question</h2>
<p>A fill in the blank question will prompt a user to  complete a broken sentence by filling in the blanks.</p>
    <p>&nbsp;</p>
	<form action="blank.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" name="blank" id="validate" onsubmit="return errorsOnSubmit(this);">
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
			?> />Extra Credit</label>
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
                        echo "<option value=\"" . stripslashes($category['category']) . "\">" . stripslashes($category['category']) . "</option>";
                    }
                }
            ?>
            </select>
        </p>
        <p>Allow Partial Credit:
          <label>
          <select name="partialCredit" id="partialCredit">
            <option value="1"<?php if (isset ($update)) { if ($testData['partialCredit'] == "1") { echo " selected=\"selected\"";}} ?>>Yes</option>
            <option value="0"<?php if (isset ($update)) { if ($testData['partialCredit'] == "0") { echo " selected=\"selected\"";}} ?>>No</option>
          </select>
          </label>
        </p>
        <p>Ignore case: 
          <select name="case" id="case">
            <option value="1"<?php if (isset ($update)) { if ($testData['case'] == "1") { echo " selected=\"selected\"";}} ?>>Yes</option>
            <option value="0"<?php if (isset ($update)) { if ($testData['case'] == "0") { echo " selected=\"selected\"";}} ?>>No</option>
          </select>
        </p>
      </blockquote>
      </div>
  <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Question Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Question Content<span class="require">*</span>:<br />
        </p>
        <table width="100%" border="0">
        <tr><td>
            <?php
			//Grab all of the answers and values if the question is being edited
				if (isset ($update)) {	
					$valueGrabber = mysql_query("SELECT * FROM questionbank WHERE id = '{$update}'", $connDBA);	
					$value = mysql_fetch_array($valueGrabber);
					$questions = unserialize($value['questionValue']);
					$answers = unserialize($value['answerValue']);
					
					echo "<table width=\"50%\" name=\"questions\" id=\"questions\"><tr>
							<th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Sentence</div></th>
						  </tr>";
					while (list($questionKey, $questionArray) = each($questions)) {
						echo "<tr><td><div align=\"center\"><label><input name=\"questionValue[]\" autocomplete=\"off\" type=\"text\" id=\"q"; echo $questionKey+1; echo "\" size=\"65\" value=\""; echo $questionArray;  echo "\" class=\"validate[required]\" /></label></div>";
					}
					echo "</table>";
					echo "</td><td>";
					echo "<table width=\"50%\" name=\"answers\" id=\"answers\"><tr>
							<th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Values</div></th>
						  </tr>";
					while (list($answerKey, $answerArray) = each($answers)) {
						echo "<tr><td><div align=\"center\"><label><input name=\"answerValue[]\" autocomplete=\"off\" type=\"text\" id=\""; echo $answerKey+1; echo "\" size=\"65\" value=\""; echo stripslashes($answerArray);  echo "\" /></label></div>";
					}
					echo "</table>";
			//Echo empty fields if the page is not editing a question
				} else {
					echo "<table width=\"50%\" name=\"questions\" id=\"questions\"><tr><th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Sentence</div></th></tr><tr><td><div align=\"center\"><label><input name=\"questionValue[]\" autocomplete=\"off\" type=\"text\" id=\"q1\" size=\"65\" class=\"validate[required]\" /></label></div></td></tr></table></td><td><table width=\"50%\" name=\"answers\" id=\"answers\"><tr><th width=\"100%\" class=\"tableHeader\"><div align=\"center\">Values</div></th></tr><tr><td><div align=\"center\"><label><input name=\"answerValue[]\" autocomplete=\"off\" type=\"text\" id=\"a1\" size=\"65\" /></label></div></td></tr></table>";
				}
			?>
            </td>
           </tr>
        </table>
        <div style="float:left"></span></div>
        <p>
          <input value="Add Another Line" type="button" onclick="appendRow('questions', '<div align=\'center\'><input name=\'questionValue[]\' type=\'text\' id=\'q', '\' autocomplete=\'off\' size=\'65\' class=\'validate[required]\' /></div>'); appendRow('answers', '<div align=\'center\'><input name=\'answerValue[]\' type=\'text\' id=\'a', '\' autocomplete=\'off\' size=\'65\' /></div>')" />
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
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../index.php');return document.MM_returnValue" value="Cancel" />
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