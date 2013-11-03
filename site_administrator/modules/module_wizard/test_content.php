<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this step has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
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
			header ("Location: test_check.php");
			exit;
		}
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Check the test settings
	$currentModule = strtolower($_SESSION['currentModule']);
	$testInfoGrabber = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$currentModule}'", $connDBA);
	$testInfo = mysql_fetch_array($testInfoGrabber);
	
//Check to see if any module data exists
	$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
	$testDataCheckGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable}", $connDBA);
	
	if ($testDataCheck = mysql_fetch_array($testDataCheckGrabber)) {
		$testDataResult = $testDataCheck['id'];
	}
	
	if (isset($testDataResult)) {
		$test = "exist";
	} else {
		$test = "empty";
	}
?>
<?php
//Reorder data
	if (isset ($_GET['currentPosition']) && isset ($_GET['id'])) {
	//Grab all necessary data	
		//Grab the id of the moving item
		$id = $_GET['id'];
		//Grab the new position of the item
		$newPosition = $_GET['position'];
		//Grab the old position of the item
		$currentPosition = $_GET['currentPosition'];
			
	//Do not process if item does not exist
		//Get item name by URL variable
		$getQuestionID = $_GET['currentPosition'];
	
		$questionCheckGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE position = {$getQuestionID}", $connDBA);
		$questionCheckArray = mysql_fetch_array($questionCheckGrabber);
		$questionCheckResult = $questionCheckArray['position'];
			 if (isset ($questionCheckResult)) {
				 $questionCheck = 1;
			 } else {
				$questionCheck = 0;
			 }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE moduletest_{$currentTable} SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE moduletest_{$currentTable} SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: test_content.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE moduletest_{$currentTable} SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE moduletest_{$currentTable} SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: test_content.php");
			exit;
		}
	}
?>
<?php
//Delete a test question
	if (isset ($_GET['question'])) {
	//Do not process if question does not exist
	//Get question by URL variable
		$getQuestionID = $_GET['question'];
	
		$questionCheckGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE position = {$getQuestionID}", $connDBA);
		$questionCheckArray = mysql_fetch_array($questionCheckGrabber);
		$questionCheckResult = $questionCheckArray['position'];
		
	   if (isset ($questionCheckResult)) {
		   $questionCheck = 1;
	   } else {
		  $questionCheck = 0;
	   }
	}
 
    if (isset ($_GET['question']) && isset ($_GET['id']) && $questionCheck == "1") {
        $deleteQuestion = $_GET['id'];
		$questionLift = $_GET['question'];
		$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
        
        $questionPositionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE position = {$questionLift}", $connDBA);
        $questionPositionFetch = mysql_fetch_array($questionPositionGrabber);
        $questionPosition = $questionPositionFetch['position'];
		
	//Detirmine the type of question being deleted
		if ($questionPositionFetch['questionBank'] !== "0") {
			$linkID = $questionPositionFetch['linkID'];
			$questionTypeGrabber = mysql_query("SELECT * FROM questionbank WHERE id = {$linkID}", $connDBA);
			$questionTypeArray = mysql_fetch_array($questionTypeGrabber);
			$questionType = $questionTypeArray['type'];
			$questionImport = "true";
		} else {
			$questionTypeGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE id = {$deleteQuestion}", $connDBA);
			$questionTypeArray = mysql_fetch_array($questionTypeGrabber);
			$questionType = $questionTypeArray['type'];
			$fileURL = $questionTypeArray['fileURL'];
			$questionImport = "false";
		}
		
		switch ($questionType) {
			case "Description" : $type = "description"; break;
			case "Essay" : $type = "essay"; break;
			case "File Response" : $type = "file"; break;
			case "Fill in the Blank" : $type = "blank"; break;
			case "Matching" : $type = "matching"; break;
			case "Multiple Choice" : $type = "choice"; break;
			case "Short Answer" : $type = "answer"; break;
			case "True False" : $type = "truefalse"; break;
		}
		
	//If this is a file response, delete the answer, if any
		if ($questionImport !== "true" && $questionType == "File Response" && $fileURL !== "") {
			unlink ("../../../modules/{$currentTable}/test/fileresponse/answer/" . $fileURL);
		}
		
        $otherQuestionsUpdateQuery = "UPDATE moduletest_{$currentTable} SET position = position-1 WHERE position > '{$questionPosition}'";
        $deleteQuestionQueryResult = mysql_query($otherQuestionsUpdateQuery, $connDBA);
        
        $deleteQuestionQuery = "DELETE FROM moduletest_{$currentTable} WHERE id = {$deleteQuestion}";
        $deleteQuestionQueryResult = mysql_query($deleteQuestionQuery, $connDBA);
		
		header ("Location: test_content.php?deleted=" . $type);
		exit;
    }
?>
<?php
//Update a session to go to previous steps
	if (isset ($_POST['back'])) {
		$_SESSION['step'] = "testSettings";
		header ("Location: lesson_settings.php");
		exit;
	}
?>
<?php
//Update a session to go to next steps
	if (isset ($_POST['next'])) {
		$_SESSION['step'] = "testVerify";
		header ("Location: lesson_verify.php");
		exit;
	}
?>
<?php
//Update a session to go to next steps
	if (isset ($_POST['modify'])) {
		header ("Location: modify.php?updated=testContent");
		exit;
	}
?>
<?php
//Update a session to go to next steps
	if (isset ($_POST['cancel'])) {
		header ("Location: modify.php");
		exit;
	}
?>
<?php
//Set a session to allow test settings to be modified
	$_SESSION['testSettings'] = "modify";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Test Content"); ?>
<?php headers(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      <h2>Module Setup Wizard : Test Content</h2>
      <p>Content may be added to the test by using the guide below.</p>
      <p>&nbsp;</p>
<div class="toolBar noPadding">
         <form name="jump" id="validate" onsubmit="return errorsOnSubmit(this);">
         <span class="toolBarItem noLink">
                  Add: 
                  <select name="menu" id="menu">
                    <option value="">- Select Question Type -</option>
                    <option value="questions/description.php">Description</option>
                    <option value="questions/essay.php">Essay</option>
                    <option value="questions/file_response.php">File Response</option>
                    <option value="questions/blank.php">Fill in the Blank</option>
                    <option value="questions/matching.php">Matching</option>
                    <option value="questions/multiple_choice.php">Multiple Choice</option>
                    <option value="questions/short_answer.php">Short Answer</option>
                    <option value="questions/true_false.php">True or False</option>
                    <option value="questions/question_bank.php">Import from Question Bank</option>
                  </select>
                  <?php formErrors(); ?>
         <input type="button" onclick="location=document.jump.menu.options[document.jump.menu.selectedIndex].value;" value="Go" />
         </span>
         <a class="toolBarItem help" href="help.php" target="_blank">Help</a>
         </form>
</div>
<?php
//If an inserted alert is shown
  if (isset ($_GET['inserted'])) {
	  $message = "The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['inserted']) {
		  case "description" : $message .= "description"; break;
		  case "essay" : $message .= "essay"; break;
		  case "file" : $message .= "file response"; break;
		  case "blank" : $message .= "fill in the blank"; break;
		  case "matching" : $message .= "matching"; break;
		  case "choice" : $message .= "multiple choice"; break;
		  case "answer" : $message .= "short answer"; break;
		  case "truefalse" : $message .= "true false"; break;
	  }
	  $message .= "</strong> question was successfully inserted";
	  
	  successMessage($message);
//If an updated alert is shown
  } elseif (isset ($_GET['updated'])) {
	  $message = "The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['updated']) {
		  case "description" : $message .= "description"; break;
		  case "essay" : $message .= "essay"; break;
		  case "file" : $message .= "file response"; break;
		  case "blank" : $message .= "fill in the blank"; break;
		  case "matching" : $message .= "matching"; break;
		  case "choice" : $message .= "multiple choice"; break;
		  case "answer" : $message .= "short answer"; break;
		  case "truefalse" : $message .= "true false"; break;
	  }
	  $message .= "</strong> question was successfully updated";
	  
	  successMessage($message);
//If an deleted alert is shown  
  } elseif (isset ($_GET['deleted'])) {
	  $message = "The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['deleted']) {
		  case "description" : $message .= "description"; break;
		  case "essay" : $message .= "essay"; break;
		  case "file" : $message .= "file response"; break;
		  case "blank" : $message .= "fill in the blank"; break;
		  case "matching" : $message .= "matching"; break;
		  case "choice" : $message .= "multiple choice"; break;
		  case "answer" : $message .= "short answer"; break;
		  case "truefalse" : $message .= "true false"; break;
	  }
	  $message .= "</strong> question was successfully deleted";
	  
	  successMessage($message);
  } else {
	  echo "<br />";
  }
?>
<?php
//The test questions
	if ($test == "exist") {
		echo "<table class=\"dataTable\">";
		echo "<tbody>";
			echo "<tr>";
				echo "<th width=\"100\" class=\"tableHeader\">Order</th>";
				echo "<th width=\"150\" class=\"tableHeader\">Type</th>";
				echo "<th width=\"100\" class=\"tableHeader\">Point Value</th>";
				echo "<th class=\"tableHeader\">Question</th>";
				echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
				echo "<th width=\"50\" class=\"tableHeader\">Delete</th>";
			echo "</tr>";
		//Select the module name, to fill in all test data
			$currentModule = strtolower($_SESSION['currentModule']);
			$currentTable = str_replace(" ","", $currentModule);
		
			$testDataGrabber = mysql_query ("SELECT * FROM moduletest_{$currentTable} ORDER BY position ASC", $connDBA);	
			
		//Select data for drop down menu
			$dropDownDataGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position ASC", $connDBA);
			
			while ($testData = mysql_fetch_array($testDataGrabber)) {
			//Select the external data, if needed
				if ($testData['questionBank'] == "1") {
					$linkID = $testData['linkID'];
					$importedQuestionGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$linkID}' LIMIT 1", $connDBA);
					$importedQuestion = mysql_fetch_array($importedQuestionGrabber);
					
					$type = $importedQuestion['type'];
					$points = $importedQuestion['points'];
					$extraCredit = $importedQuestion['extraCredit'];
					$question = $importedQuestion['question'];
				} else {
					$type = $testData['type'];
					$points = $testData['points'];
					$extraCredit = $testData['extraCredit'];
					$question = $testData['question'];
				}
				
				echo "<tr";
				if ($testData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				">";
					echo "<form action=\"test_content.php\">";
					echo "<input type=\"hidden\" name=\"currentPosition\" value=\"" . $testData['position'] . "\" />";
					echo "<input type=\"hidden\" name=\"id\" value=\"" . $testData['id'] . "\" />";
					echo "<td width=\"100\"><div";
					
					if (isset($importedQuestion)) {
						echo " class=\"questionBank\"";
					}
					echo ">";
							echo "<select name=\"position\" onchange=\"this.form.submit();\">";
							$testCount = mysql_num_rows($dropDownDataGrabber);
							for ($count=1; $count <= $testCount; $count++) {
								echo "<option value=\"{$count}\"";
								if ($testData ['position'] == $count) {
									echo " selected=\"selected\"";
								}
								echo ">$count</option>";
							}
							echo "</select>";
						echo "</div></td>";
					echo "<td width=\"150\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('preview_question.php?id=" . $testData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('Preview this <strong>" . $type . "</strong> question')\" onmouseout=\"UnTip()\">" . $type . "</a></td>";
					echo "<td width=\"100\"><div";
					if ($extraCredit == "on") {
						echo " class=\"extraCredit\"";
					}
					echo ">" . $points;
					if ($points == "1") {
						echo " Point";
					} else {
						echo " Points";
					}
					echo "</div></td>";
					echo "<td>" . commentTrim(55, $question) . "</td>";
					echo "<td width=\"50\">" . "<a class=\"action edit\" href=\"";
					if (isset($importedQuestion)) {
						echo "question_merge.php?type=import&questionID=" . $testData['id'] . "&bankID=" . $testData['linkID'];
					} else {
						switch ($testData['type']) {
							case "Description" : echo "questions/description.php"; break;
							case "Essay" : echo "questions/essay.php"; break;
							case "File Response" : echo "questions/file_response.php"; break;
							case "Fill in the Blank" : echo "questions/blank.php"; break;
							case "Matching" : echo "questions/matching.php"; break;
							case "Multiple Choice" : echo "questions/multiple_choice.php"; break;
							case "Short Answer" : echo "questions/short_answer.php"; break;
							case "True False" : echo "questions/true_false.php"; break;
						}
						
						echo "?question=" . $testData['position'] . "&id=" . $testData['id'];
					}
					echo "\"";
					if (isset($importedQuestion)) {
						echo " onclick=\"return confirm('This question is currently located in the question bank. Once you edit this question, it will no long be linked to the question bank. Do you want to import and edit this question inside of the test? Click OK to continue.')\"";
					}
					echo " onmouseover=\"Tip('Edit this <strong>" . $type . "</strong> question')\" onmouseout=\"UnTip()\"></a></td>";
					echo "<td width=\"50\"><a class=\"action delete\" href=\"test_content.php?question=" .  $testData['position'] . "&id=" .  $testData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete this <strong>" . $type . "</strong> question')\" onmouseout=\"UnTip()\"></a></td>";
				echo "</form>";
				echo "</tr>";
				
			//Unset the $importedQuestion, $type, $points, $extraCredit, and $question variables
				unset($importedQuestion);
				unset($type);
				unset($points);
				unset($extraCredit);
				unset($question);
			}
		echo "</tbody>";
		echo "</table>";
		echo "<br />";
	} else {
		echo "<div class=\"noResults\">There are no test questions. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>";
	}
?>
 <form action="test_content.php" method="post" id="validate" onsubmit="return errorsOnSubmit(this);">
      <blockquote>
          <?php
		  //Selectively display the buttons
		  		if (isset ($_SESSION['review'])) {
					if ($test !== "empty") {
						submit("modify", "Modify Content");
						submit("cancel", "Cancel");
					}
				} else {
					submit("back", "&lt;&lt; Previous Step");
					if ($test !== "empty") {
						submit("next", "Next Step &gt;&gt;");
					}
				}
		  ?>
          <?php formErrors(); ?>
      </blockquote>  
   </form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>