<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Select all categories
	$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY position ASC", $connDBA);
	if (mysql_fetch_array($categoryGrabber)) {
	//Use the URL to narrow the categories down on request
		if (isset ($_GET['id'])) {
			$id = $_GET['id'];
			$categoryCheck = mysql_query("SELECT * FROM `modulecategories` WHERE `id` = '{$id}'", $connDBA);
			$testCheck = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$id}' ORDER BY id ASC", $connDBA);
			$testImport = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$id}' ORDER BY id ASC", $connDBA);
			
			if (!mysql_fetch_array($categoryCheck)) {
				header ("Location: index.php");
				unset($_SESSION['bankCategory']);
				exit;
			} else {
				$bankTitleGrabber = mysql_query("SELECT * FROM `modulecategories` WHERE `id` = '{$id}'", $connDBA);
				$bankTitle = mysql_fetch_array($bankTitleGrabber);
			}
			
			$_SESSION['bankCategory'] = $id;
		}
	
		$categoryResult = 1;
	} else {
		$categoryResult = 0;
		unset($_SESSION['bankCategory']);
	}
?>
<?php
//Delete a test question
	if (isset ($_GET['questionID']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		$getQuestionID = $_GET['questionID'];
		$questionCheckGrabber = mysql_query("SELECT * FROM questionbank WHERE id = {$getQuestionID}", $connDBA);
		$questionCheckArray = mysql_fetch_array($questionCheckGrabber);
		$questionCheckResult = $questionCheckArray['id'];
		
		if (isset ($questionCheckResult)) {
			$deleteQuestion = $_GET['questionID'];
		//Delete from the question bank
			$deleteQuestionQuery = "DELETE FROM questionbank WHERE id = '{$deleteQuestion}'";
			$deleteQuestionQueryResult = mysql_query($deleteQuestionQuery, $connDBA);
			
		//If this is a file response, delete the answer, if any
			if ($questionCheckArray['type'] == "File Response" && $questionCheckArray['fileURL'] !== "") {
				$file = $questionCheckArray['fileURL'];
				unlink ("../../../modules/questionbank/test/fileresponse/answer/" . $file);
			}

		//Delete from tests in which this appears
			$questionBankDeleteGrabber = mysql_query("SELECT * FROM moduledata", $connDBA);
			
			while ($questionBankDelete = mysql_fetch_array($questionBankDeleteGrabber)) {
				$currentTable = str_replace(" ", "", $questionBankDelete['name']);
				$questionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE `linkID` = '{$deleteQuestion}'");
				
				if ($questionArray = mysql_fetch_array($questionGrabber)) {
					$questionID = $questionArray['id'];
					$questionPosition = $questionArray['position'];
					
					$deleteUpdateQuery = "UPDATE moduletest_{$currentTable} SET position = position-1 WHERE position > '{$questionPosition}'";
					$deleteBankQuery = "DELETE FROM moduletest_{$currentTable} WHERE id = '{$questionID}'";
							
					$deleteBank = mysql_query($deleteBankQuery, $connDBA);
					$deleteUpdate = mysql_query($deleteUpdateQuery, $connDBA);
				}
			}
			
		//Detirmine the type of question being deleted			
			switch ($questionCheckArray['type']) {
				case "Description" : $typeOutput = "description"; break;
				case "Essay" : $typeOutput = "essay"; break;
				case "File Response" : $typeOutput = "file"; break;
				case "Fill in the Blank" : $typeOutput = "blank"; break;
				case "Matching" : $typeOutput = "matching"; break;
				case "Multiple Choice" : $typeOutput = "choice"; break;
				case "Short Answer" : $typeOutput = "answer"; break;
				case "True False" : $typeOutput = "truefalse"; break;
			}	
			
			if (isset($_SESSION['bankCategory'])) {
				header ("Location: index.php?id=" . $_SESSION['bankCategory'] . "&deleted=" . $typeOutput);
				exit;
			} else {
				header ("Location: index.php?deleted=" . $typeOutput);
				exit;
			}
		} else {
			header ("Location: index.php?id=" . $_SESSION['bankCategory']);
			exit;
		}
	}
?>
<?php
//Assign the page title
	if (isset ($_GET['id'])) {
		$title = stripslashes($bankTitle['category']) . " Bank";
	} else {
		$title = "Question Bank";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title($title); ?>
<?php headers(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body onunload="window.opener.location.reload()"<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      <h2><?php echo $title; ?></h2>
      <p>Questions may be created here and be imported into tests when a module is being created. The questions are broken up by their category.</p>
<p>&nbsp;</p>
<?php
//If no categories are present
	if (isset ($_GET['id'])) {
		echo "<div class=\"toolBar noPadding\"><form name=\"jump\" onsubmit=\"return errorsOnSubmit(this);\">
		<span class=\"toolBarItem noLink\">
		  Add: 
		  <select name=\"menu\" id=\"menu\">
			<option value=\"\">- Select Question Type -</option>
			<option value=\"questions/description.php\">Description</option>
			<option value=\"questions/essay.php\">Essay</option>
			<option value=\"questions/file_response.php\">File Response</option>
			<option value=\"questions/blank.php\">Fill in the Blank</option>
			<option value=\"questions/matching.php\">Matching</option>
			<option value=\"questions/multiple_choice.php\">Multiple Choice</option>
			<option value=\"questions/short_answer.php\">Short Answer</option>
			<option value=\"questions/true_false.php\">True or False</option>
		  </select><input type=\"button\" onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\" value=\"Go\" />
		  </span>";
		  
		  echo "<a class=\"toolBarItem back\" href=\"index.php\">Back to Categories</a>";
	 } else {
		 echo "<div class=\"toolBar\">";
	 }
?><a class="toolBarItem home" href="../index.php">Back to Modules</a><a class="toolBarItem settings" href="../settings.php?type=category">Manage Categories</a><a class="toolBarItem search" href="search.php">Search</a><a class="toolBarItem help" href="help.php" target="_blank">Help</a></form>
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
	if ($categoryResult !== 0) {
		if (!isset ($_GET['id'])) {
			echo "<br /><br /><p>Please select a category from the list below.</p><blockquote>";
			
			$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY position ASC", $connDBA);
			while ($category = mysql_fetch_array($categoryGrabber)) {
				$currentCategory = $category['id'];
				$questionGrabber = mysql_query("SELECT * FROM `questionBank` WHERE `category` = '{$currentCategory}'", $connDBA);
				$questionValue = mysql_num_rows($questionGrabber);
				
				echo "<a href=\"index.php?id=" . $category['id'] . "\">" . stripslashes($category['category']) . "</a> : ";
				if ($questionValue == 1) {
					echo $questionValue . " Question<br /><br />";
				} else {
					echo $questionValue . " Questions<br /><br />";
				}
			}
			
			echo "</blockquote>";
		}
		
		if (isset ($_GET['id'])) {								
			if (mysql_fetch_array($testCheck)) {
				echo "<table class=\"dataTable\"><tbody><tr><th width=\"150\" class=\"tableHeader\">Type</th><th width=\"100\" class=\"tableHeader\">Point Value</th><th class=\"tableHeader\">Question</th><th width=\"50\" class=\"tableHeader\">Discover</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
				
			//Loop through the items
				$count = 1;	
				while ($testData = mysql_fetch_array($testImport)) {
					echo "<tr";
					if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"150\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('preview.php?id=" . $testData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('Preview this <strong>" . $testData['type'] . "</strong> question')\" onmouseout=\"UnTip()\">" . $testData['type'] . "</a></td><td width=\"100\"><div";
					if ($testData['extraCredit'] == "on") {
						echo " class=\"extraCredit\"";
					}
					echo ">" . $testData['points'];
					if ($testData['points'] == "1") {
						echo " Point";
					} else {
						echo " Points";
					}
					
					echo "</div></td><td>" . commentTrim(85, $testData['question']) . "</td><td width=\"50\"><a class=\"action discover\"href=\"discover.php?linkID=" . $testData['id'] . "\" onmouseover=\"Tip('Discover in which tests this <strong>" . $testData['type'] . "</strong> question is used')\" onmouseout=\"UnTip()\"></a></td><td width=\"50\"><a class=\"action edit\" href=\"";
					
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
					
					echo "?id=" .  $testData['id'] . "\" onmouseover=\"Tip('Edit this <strong>" . $testData['type'] . "</strong> question')\" onmouseout=\"UnTip()\"></a></td><td width=\"50\"><a class=\"action delete\" href=\"index.php?questionID=" .  $testData['id'] . "&action=delete\" onclick=\"return confirm ('This action will delete this question from the question bank, and from any of the tests it is currently linked. Continue?');\" onmouseover=\"Tip('Delete this <strong>" . $testData['type'] . "</strong> question')\" onmouseout=\"UnTip()\"></a></td></tr>";
				}
				echo "</tbody></table></div><br /><br />";
			} else {
				echo "<div class=\"noResults\">There are no questions in this bank. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>";
			}
		}
	} else {
		echo "<div class=\"noResults\">Please <a href=\"../settings.php?type=category\">add at least one category</a> prior to entering questions.</div>";
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>