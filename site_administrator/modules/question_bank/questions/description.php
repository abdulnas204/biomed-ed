<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$testDataGrabber = mysql_query("SELECT * FROM questionBank WHERE id = '{$update}'", $connDBA);
		if ($testDataCheck = mysql_fetch_array($testDataGrabber)) {
			if ($testDataCheck['type'] == "Description") {
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
	if (isset ($_POST['submit']) && !empty ($_POST['question']) && !empty ($_POST['category'])) {
	//If the page is updating an item
		if (isset ($update)) {
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$category = $_POST['category'];
		
			$updateDescriptionQuery = "UPDATE questionBank SET `question` = '{$question}', `category` = '{$category}' WHERE id = '{$update}'";
							
			$updateDescription = mysql_query($updateDescriptionQuery, $connDBA);
			$location = urlencode($category);
			header ("Location: ../index.php?updated=description&category=" . $location);
			exit;
	//If the page is inserting an item		
		} else {
			
		//Get form data values
			$question = mysql_real_escape_string($_POST['question']);
			$category = $_POST['category'];
		
			$insertDescriptionQuery = "INSERT INTO questionBank (
							`id`, `category`, `type`, `points`, `extraCredit`, `partialCredit`, `totalFiles`, `case`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`
							) VALUES (
							NULL, '{$category}', 'Description', '0', 'off', '0', '0', '1', '{$question}', '', '', '', '', '', ''
							)";
							
			$insertDescription = mysql_query($insertDescriptionQuery, $connDBA);
			$location = urlencode($category);
			header ("Location: ../index.php?inserted=description&category=" . $location);
			exit;
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
    <p>A description is not a question field, however, it allows test creators to  insert text into the test without asking any questions or scoring the  viewer on this content.</p>
    <p>&nbsp;</p>
    <form action="description.php<?php
		if (isset ($update)) {
			echo "?id=" . $testData['id'];
		}
    ?>" method="post" name="description" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider"><img src="../../../../images/numbering/1.gif" alt="1." width="22" height="22" /> Description Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Description Content<span class="require">*</span>: </p>
      <blockquote>
            <p align="left"><span id="questionCheck">
              <label>
              <textarea name="question" id="question" cols="45" rows="5" style="width:640px; height:320px;"><?php 
			  //If the page is updating an item
			  		if (isset ($update)) {
						echo stripslashes($testData['question']);
					}
			  ?></textarea>
              </label>
            <span class="textareaRequiredMsg"></span></span></p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider"><img src="../../../../images/numbering/2.gif" alt="2." width="22" height="22" /> Settings</div>
      <div class="stepContent">
        <blockquote>
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
      <div class="catDivider"><img src="../../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php submit("submit", "Submit"); ?>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../index.php<?php if (isset ($_SESSION['category'])) {echo "?category=" . urldecode($_SESSION['category']);} ?>');return document.MM_returnValue" value="Cancel" />
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