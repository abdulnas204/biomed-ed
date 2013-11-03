<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionbank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Description") {
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
	if (isset ($_POST['submit']) && !empty($_POST['question']) && !empty($_POST['category'])) {
	//If the page is updating an item
		if (isset ($update)) {			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$category = mysql_real_escape_string($_POST['category']);
			$tags = mysql_real_escape_string($_POST['tags']);
		
			$updateDescriptionQuery = "UPDATE questionbank SET `question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}' WHERE id = '{$update}'";
							
			$updateDescription = mysql_query($updateDescriptionQuery, $connDBA);
			header ("Location: ../index.php?id=" . $_SESSION['bankCategory'] . "&updated=description");
			exit;
	//If the page is inserting an item		
		} else {						
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$category = mysql_real_escape_string($_POST['category']);
			$tags = mysql_real_escape_string($_POST['tags']);
		
			$insertDescriptionQuery = "INSERT INTO questionbank (
							`id`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (
							NULL, 'Description', '0', '', '0', '', '{$category}', '0', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''
							)";
							
			$insertDescription = mysql_query($insertDescriptionQuery, $connDBA);
			
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
					}
				}
				
				header ("Location: ../index.php?id=" . $_SESSION['bankCategory'] . "&inserted=description&export=true&exportID=" . $linkID);
				exit;
			} else {
				header ("Location: ../index.php?id=" . $_SESSION['bankCategory'] . "&inserted=description");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Question Bank : Description"); ?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
    <h2>Question Bank : Description</h2>
    <p>A description is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.</p>
    <p>&nbsp;</p>
    <form action="description.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" name="description" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Description content<span class="require">*</span>: </p>
      <blockquote>
            <p align="left"><span id="questionCheck">
              <textarea name="question" id="question" cols="45" rows="5" style="width:640px; height:320px;"><?php 
			  //If the page is updating an item
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
      <div class="catDivider three">Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php submit("submit", "Submit"); ?>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../index.php?id=<?php echo $_SESSION['bankCategory'];?>');return document.MM_returnValue" value="Cancel" />
        </p>
        <?php formErrors(); ?>
      </blockquote>
      </div>
    </form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
<script type="text/javascript">
<!--
var sprytextarea1 = new Spry.Widget.ValidationTextarea("questionCheck", {validateOn:["change"]});
//-->
</script>
</body>
</html>