<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionbank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Fill in the Blank") {
				$testData = $testDataCheck;
			} else {
				header ("Location: ../index.php?category=" . $_SESSION['bankCategory']);
				exit;
			}
		} else {
			header ("Location: ../index.php?category=" . $_SESSION['bankCategory']);
			exit;
		}
	} elseif (isset ($_GET['question']) || isset ($_GET['id'])) {
		header ("Location: ../index.php?category=" . $_SESSION['bankCategory']);
		exit;
	}
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['category']) && !empty($_POST['questionValue']) && !empty($_POST['answerValue'])) {
	//If the page is updating an item
		if (isset ($update)) {
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$difficulty = $_POST['difficulty'];
			$category = mysql_real_escape_string($_POST['category']);
			$link = $_POST['link'];
			$partialCredit = $_POST['partialCredit'];
			$case = $_POST['case'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
			$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
			$updateBlankQuery = "UPDATE questionbank SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `partialCredit` = '{$partialCredit}', `case` = '{$case}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}' WHERE id = '{$update}'";
							
			$updateBlank = mysql_query($updateBlankQuery, $connDBA);
			header ("Location: ../index.php?category=" . $_SESSION['bankCategory'] . "&updated=blank");
			exit;
	//If the page is inserting an item		
		} else {			
		//Get form data values			
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$difficulty = $_POST['difficulty'];
			$category = mysql_real_escape_string($_POST['category']);
			$link = $_POST['link'];
			$partialCredit = $_POST['partialCredit'];
			$case = $_POST['case'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
			$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
			
		
			$insertBlankQuery = "INSERT INTO questionbank (
							`id`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (							
							NULL, 'Fill in the Blank', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$category}', '{$link}', '0', '0', '', '{$case}', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'
							)";
							
			$insertBlank = mysql_query($insertBlankQuery, $connDBA);
			
		//Automatically insert this question into tests of a similar category
			$questionBankCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE category = '{$category}'", $connDBA);
			
			if ($questionBankCheck = mysql_fetch_array($questionBankCheckGrabber)) {
				$linkIDGrabber = mysql_query("SELECT * FROM questionbank ORDER BY id DESC LIMIT 1");
				$linkIDArray = mysql_fetch_array($linkIDGrabber);
				$linkID = $linkIDArray['id'];
				$questionBankInsertGrabber = mysql_query("SELECT * FROM moduledata WHERE category = '{$category}'", $connDBA);
				
				while ($questionBankInsert = mysql_fetch_array($questionBankInsertGrabber)) {
					if ($questionBankInsert['questionBank'] == "1") {
						$currentTable = str_replace(" ", "", $questionBankInsert['name']);
						$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC LIMIT 1");
						$lastQuestionArray = mysql_fetch_array($lastQuestionGrabber);
						$lastQuestion = $lastQuestionArray['position']+1;
						
						$insertBankQuery = "INSERT INTO moduletest_{$currentTable} (
								`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
								) VALUES (							
								NULL, '1', '{$linkID}', '{$lastQuestion}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
								)";
								
						$insertBank = mysql_query($insertBankQuery, $connDBA);
						
						header ("Location: ../index.php?category=" . $_SESSION['bankCategory'] . "&inserted=blank&export=true&exportID=" . $linkID);
						exit;
					}
				}
			} else {
				header ("Location: ../index.php?category=" . $_SESSION['bankCategory'] . "&inserted=blank");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Question Bank : Fill in the Blank"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/showHide.js" type="text/javascript"></script>
<script src="../../../../javascripts/insert/newTextFieldAdvanced.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>      
    <h2>Question Bank : Fill in the Blank</h2>
<p>A fill in the blank question will prompt a user to  complete a sentence with missing values by filling in the blanks.</p>
    <p>&nbsp;</p>
	<form action="blank.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" name="blank" id="validate" onsubmit="return errorsOnSubmit(this);">
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
  <div class="catDivider"><img src="../../../../images/numbering/2.gif" alt="2." width="22" height="22" /> Question Settings</div>
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
        <p> Category<span class="require">*</span>:        </p>
        <blockquote>
          <select name="category" id="category" class="validate[required]">
            <?php
            //Select all of the category items
                $categoryGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
                //If the module is being edited
                if (isset($update)) {
                    echo "<option value=\"\">- Select -</option>";
                    while ($category = mysql_fetch_array($categoryGrabber)) {
                        echo "<option value=\"" .  stripslashes(htmlentities($category['category'])) . "\"";
                        
                        if ($category['category'] == $testData['category']) {
                            echo " selected=\"selected\"";
                        }
                        
                        echo ">" .  stripslashes(htmlentities($category['category'])) . "</option>";
                    }
                } else {
                    echo "<option selected=\"selected\" value=\"\">- Select -</option>";
                    while ($category = mysql_fetch_array($categoryGrabber)) {
                        echo "<option value=\"" . stripslashes(htmlentities($category['category'])) . "\"";
						
						if ($category['category'] == urldecode($_SESSION['bankCategory'])) {
							echo " selected=\"selected\"";
						}
						
						echo ">" .  stripslashes(htmlentities($category['category'])) . "</option>";
                    }
                }
            ?>
            </select>
        </blockquote>
        <p>Difficulty:</p>
        <blockquote>
          <p>
            <select name="difficulty" id="difficulty">
              <option value="Easy"<?php if (isset ($update)) {if ($testData['difficulty'] == "Easy") {echo " selected=\"selected\"";}} ?>>Easy</option>
              <option value="Average"<?php if (isset ($update)) {if ($testData['difficulty'] == "Average") {echo " selected=\"selected\"";}} else {echo " selected=\"selected\"";} ?>>Average</option>
              <option value="Difficult"<?php if (isset ($update)) {if ($testData['difficulty'] == "Difficult") {echo " selected=\"selected\"";}} ?>>Difficult</option>
            </select>
          </p>
        </blockquote>
        <p>Link to description:</p>
        <blockquote>
          <p>
            <select name="link" id="link">
              <?php
			//Select all of the descriptions in this category
				$category = urldecode($_SESSION['bankCategory']);
				$descriptionCheck = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$category}' AND `type` = 'Description'", $connDBA);
				
				if (mysql_fetch_array($descriptionCheck)) {
					$descriptionGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$category}' AND `type` = 'Description' ORDER BY `id` ASC", $connDBA);
					
					echo "<option value=\"\">- Select -</option>";
					while ($description = mysql_fetch_array($descriptionGrabber)) {
						if ($description['type'] == "Description") {
							echo "<option value=\"" . $description['id'] ."\"";
							if (isset($update)) {
								if ($testData['link'] == $description['id']) {
									echo " selected=\"selected\"";
								}
							}
							echo ">" . stripslashes(htmlentities(commentTrim(25, $description['question']))) . "</option>";
						}
						
						if ($description['questionBank'] == "1") {
							$importID = $description['linkID'];
							$descriptionImportGrabber = mysql_query("SELECT * FROM `questionBank` WHERE `id` = '{$importID}'", $connDBA);
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
              <br />
            </p>
</blockquote>
        <p>Ignore case:</p>
        <blockquote>
          <p>
            <label>
              <input type="radio" name="case" value="1" id="case_0"<?php if (isset ($update)) { if ($testData['case'] == "1") { echo " checked=\"checked\"";}} else { echo " checked=\"checked\"";} ?> />
              Yes</label>
            <label>
              <input type="radio" name="case" value="0" id="case_1"<?php if (isset ($update)) { if ($testData['case'] == "0") { echo " checked=\"checked\"";}} ?> />
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
  <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Question Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Question content<span class="require">*</span>: <a href="../help.php?tab=2" target="_blank"><img src="../../../../images/admin_icons/help.png" alt="Help" width="17" height="17" /></a><br />
          <strong>Hint:</strong> Do not include any characters or spaces in the values section, otherwise this question may be scored incorrectly.<br />
        </p>
        <table width="100%" border="0">
        <tr><td>
            <?php
			//Grab all of the answers and values if the question is being edited
				if (isset ($update)) {	
					$questions = unserialize($testData['questionValue']);
					$answers = unserialize($testData['answerValue']);
					
					echo "<table width=\"50%\" name=\"questions\" id=\"questions\"><tr>
							<td width=\"100%\"><div align=\"center\"><strong>Sentence</strong></div></td>
						  </tr>";
					while (list($questionKey, $questionArray) = each($questions)) {
						echo "<tr><td><div align=\"center\"><input name=\"questionValue[]\" autocomplete=\"off\" type=\"text\" id=\"q"; echo $questionKey+1; echo "\" size=\"65\" value=\""; echo stripslashes(htmlentities($questionArray));  echo "\" class=\"validate[required]\" /></div>";
					}
					echo "</table>";
					echo "</td><td>";
					echo "<table width=\"50%\" name=\"answers\" id=\"answers\"><tr>
							<td width=\"100%\"><div align=\"center\"><strong>Values</strong></div></td>
						  </tr>";
					while (list($answerKey, $answerArray) = each($answers)) {
						echo "<tr><td><div align=\"center\"><input name=\"answerValue[]\" autocomplete=\"off\" type=\"text\" id=\""; echo $answerKey+1; echo "\" size=\"65\" value=\""; echo stripslashes(htmlentities($answerArray));  echo "\" /></div>";
					}
					echo "</table>";
			//Echo empty fields if the page is not editing a question
				} else {
					echo "<table width=\"50%\" name=\"questions\" id=\"questions\"><tr><td width=\"100%\"><div align=\"center\"><strong>Sentence</strong></div></td></tr><tr><td><div align=\"center\"><input name=\"questionValue[]\" autocomplete=\"off\" type=\"text\" id=\"q1\" size=\"65\" class=\"validate[required]\" /></div></td></tr></table></td><td><table width=\"50%\" name=\"answers\" id=\"answers\"><tr><td width=\"100%\"><div align=\"center\"><strong>Values</strong></div></td></tr><tr><td><div align=\"center\"><input name=\"answerValue[]\" autocomplete=\"off\" type=\"text\" id=\"a1\" size=\"65\" /></div></td></tr></table>";
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
		    ?>
              </textarea>
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
        </blockquote>
      </blockquote>
      </div>
  <div class="catDivider"><img src="../../../../images/numbering/5.gif" alt="5." width="22" height="22" /> Finish</div>
        <div class="stepContent">
        <blockquote>
          <p>
            <?php submit("submit", "Submit"); ?>
            <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../index.php?category=<?php echo $_SESSION['bankCategory'];?>');return document.MM_returnValue" value="Cancel" />
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