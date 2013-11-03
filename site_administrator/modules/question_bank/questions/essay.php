<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionBank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Essay") {
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
	if (isset ($_POST['submit']) && !empty ($_POST['question']) && !empty ($_POST['points']) && !empty ($_POST['category'])) {
	//If the page is updating an item
		if (isset ($update)) {
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$category = $_POST['category'];
			$extraCredit = $_POST['extraCredit'];
			$answer = mysql_real_escape_string($_POST['answer']);
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		
			$updateEssayQuery = "UPDATE questionBank SET `category` = '{$category}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackInorrect}' WHERE id = '{$update}'";
							
			$updateEssay = mysql_query($updateEssayQuery, $connDBA);
			$location = urlencode($category);
			header ("Location: ../index.php?updated=essay&category=" . $location);
			exit;
	//If the page is inserting an item		
		} else {
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$category = $_POST['category'];
			$extraCredit = $_POST['extraCredit'];
			$answer = mysql_real_escape_string($_POST['answer']);
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackInorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			
		
			$insertEssayQuery = "INSERT INTO questionBank (
							`id`, `category`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$category}', 'Essay', '{$points}', '{$extraCredit}', '0', '0', '1', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackInorrect}'
							)";
							
			$insertEssay = mysql_query($insertEssayQuery, $connDBA);
			$location = urlencode($category);
			header ("Location: ../index.php?inserted=essay&category=" . $location);
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Question Bank : Essay Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Question Bank : Essay Question</h2>
<p>This will insert an essay question into the test. Essays must be scored manually.</p>
    <p>&nbsp;</p>
    <form action="essay.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" name="essay" id="validate" onsubmit="return errorsOnSubmit(this);">
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
          <input name="points" type="text" id="points" size="5" autocomplete="off" maxlength="5" class="validate[required,custom[onlyNumber]]"<?php
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
			  ?> />
              Extra Credit </label>
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
                        echo "<option value=\"" . stripslashes($category['category']) . "\"";
						if (isset ($_SESSION['category']) && urldecode($_SESSION['category']) == stripslashes($category['category'])) {
							echo " selected=\"selected\"";
                        }
						echo ">" . stripslashes($category['category']) . "</option>";
                    }
                }
            ?>
            </select>
        </p>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Answer</div>
      <div class="stepContent">
      <blockquote>
        <p>Provide an exmaple of a correct answer: </p>
        <blockquote>
          <p>
          <textarea id="answer" name="answer" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['answer']);
			}
		  ?></textarea>
          </p>
        </blockquote>
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
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../index.php<?php if (isset ($_SESSION['category'])) {echo "?category=" . urldecode($_SESSION['category']);} ?>');return document.MM_returnValue" value="Cancel" />
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