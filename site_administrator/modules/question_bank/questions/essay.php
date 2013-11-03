<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionbank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Essay") {
				$testData = $testDataCheck;
			} else {
				header ("Location: ../index.php?id=" . $_SESSION['bankCategory']);
				exit;
			}
		} else {
			header ("Location: ../index.php?id=" . $_SESSION['bankCategory']);
			exit;
		}
	} elseif (isset ($_GET['question']) || isset ($_GET['id'])) {
		header ("Location: ../index.php?id=" . $_SESSION['bankCategory']);
		exit;
	}
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['category'])) {
	//If the page is updating an item
		if (isset ($update)) {			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$points = $_POST['points'];
			$extraCredit = $_POST['extraCredit'];
			$difficulty = $_POST['difficulty'];
			$category = mysql_real_escape_string($_POST['category']);
			$link = $_POST['link'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$answer = mysql_real_escape_string($_POST['answer']);
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
			$updateEssayQuery = "UPDATE questionbank SET `question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}' WHERE id = '{$update}'";
							
			$updateEssay = mysql_query($updateEssayQuery, $connDBA);
			header ("Location: ../index.php?id=" . $_SESSION['bankCategory'] . "&updated=essay");
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
			$tags = mysql_real_escape_string($_POST['tags']);
			$answer = mysql_real_escape_string($_POST['answer']);
			$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
			$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
			$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
			
		
			$insertEssayQuery = "INSERT INTO questionbank (
							`id`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (
							NULL, 'Essay', '{$points}', '{$extraCredit}', '0', '{$difficulty}', '{$category}', '{$link}', '0', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'
							)";
							
			$insertEssay = mysql_query($insertEssayQuery, $connDBA);
			
		//Automatically insert this question into tests of a similar category
			$questionBankCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE category = '{$category}'", $connDBA);
			
			if ($questionBankCheck = mysql_fetch_array($questionBankCheckGrabber)) {
				$linkIDGrabber = mysql_query("SELECT * FROM questionbank ORDER BY id DESC LIMIT 1");
				$linkIDArray = mysql_fetch_array($linkIDGrabber);
				$linkID = $linkIDArray['id'];
				$questionBankInsertGrabber = mysql_query("SELECT * FROM moduledata WHERE category = '{$category}'", $connDBA);
				
				while ($questionBankInsert = mysql_fetch_array($questionBankInsertGrabber)) {
					if ($questionBankInsert['questionBank'] == "1") {
						$currentTable = strtolower(str_replace(" ", "", $questionBankInsert['name']));
						$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC LIMIT 1");
						$lastQuestionArray = mysql_fetch_array($lastQuestionGrabber);
						$lastQuestion = $lastQuestionArray['position']+1;
						
						$insertBankQuery = "INSERT INTO moduletest_{$currentTable} (
								`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
								) VALUES (							
								NULL, '1', '{$linkID}', '{$lastQuestion}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
								)";
								
						$insertBank = mysql_query($insertBankQuery, $connDBA);
					}
				}
				
				header ("Location: ../index.php?id=" . $_SESSION['bankCategory'] . "&inserted=essay&export=true&exportID=" . $linkID);
				exit;
			} else {
				header ("Location: ../index.php?id=" . $_SESSION['bankCategory'] . "&inserted=essay");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Question Bank : Essay"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Question Bank : Essay</h2>
<p>An essay question is  a question that requires a long, written response. Essays must be scored manually.</p>
<p>&nbsp;</p>
    <form action="essay.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" name="essay" id="validate" onsubmit="return errorsOnSubmit(this);">
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
        <p>Category<span class="require">*</span>: </p>
        <blockquote>
          <select name="category" id="category" class="validate[required]">
            <?php
            //Select all of the category items
                $categoryGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
                //If the module is being edited
                if (isset($update)) {
                    echo "<option value=\"\">- Select -</option>";
                    while ($category = mysql_fetch_array($categoryGrabber)) {
                        echo "<option value=\"" .  $category['id'] . "\"";
                        
                        if ($category['id'] == $testData['category']) {
                            echo " selected=\"selected\"";
                        }
                        
                        echo ">" .  stripslashes(htmlentities($category['category'])) . "</option>";
                    }
                } else {
                    echo "<option selected=\"selected\" value=\"\">- Select -</option>";
                    while ($category = mysql_fetch_array($categoryGrabber)) {
                        echo "<option value=\"" . $category['id'] . "\"";
						
						if ($category['id'] == $_SESSION['bankCategory']) {
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
      <div class="catDivider three">Answer</div>
      <div class="stepContent">
      <blockquote>
        <p>Provide an example of a correct answer: </p>
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
      <div class="catDivider four">Feedback</div>
      <div class="stepContent">
      <blockquote>
        <p>Feedback for correct answer: </p>
        <blockquote>
          <p>
          <textarea id="feedBackCorrect" name="feedBackCorrect" rows="5" cols="45" style="width: 450px"><?php
		  	if (isset ($update)) {
				echo stripslashes($testData['correctFeedback']);
			}
		  ?></textarea>
          </p>
        </blockquote>
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
      <div class="catDivider five">Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php submit("submit", "Submit"); ?>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          <input name="cancel" type="button" id="cancel" onclick="history.go(-1)" value="Cancel" />
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