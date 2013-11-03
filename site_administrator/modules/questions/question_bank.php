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
//Select all categories
	$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY position ASC", $connDBA);
	if (mysql_fetch_array($categoryGrabber)) {
	//Use the URL to narrow the categories down on request
		if (isset ($_GET['id'])) {
			$id = $_GET['id'];
			
		//Display any questions from categories whose questions may have been deleted
			if ($_GET['id'] == "0") {
				$currentCategoriesGrabber = mysql_query("SELECT `id` FROM `modulecategories` ORDER BY position ASC", $connDBA);
				$currentCategories = mysql_fetch_array($currentCategoriesGrabber);
				$otherQuestionsGrabber = mysql_query("SELECT * FROM `questionbank` ORDER BY `id` ASC", $connDBA);
				$count = 0;
				$sql = "";
				
				while ($otherQuestions = mysql_fetch_array($otherQuestionsGrabber)) {
					if (!in_array($otherQuestions['category'], $currentCategories)) {
						if ($count == 0) {
							$sql .= "`category` != '" . $otherQuestions['id'] . "'";
						} else {
							$sql .= " OR `category` != '" . $otherQuestions['id'] . "'";
						}
						
						$count ++;
					}
				}
				
				$testCheck = mysql_query("SELECT * FROM `questionbank` WHERE {$sql} ORDER BY id ASC", $connDBA);
					
				if (!$testCheck) {
					header("Location: question_bank.php");
					exit;
				}
				
				$testImport = mysql_query("SELECT * FROM `questionbank` WHERE {$sql} ORDER BY id ASC", $connDBA);
			} else {
				$testCheck = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$id}' ORDER BY id ASC", $connDBA);
				$categoryCheck = mysql_query("SELECT * FROM `modulecategories` WHERE `id` = '{$id}'", $connDBA);
				$testImport = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$id}' ORDER BY id ASC", $connDBA);
				
				if (!mysql_fetch_array($categoryCheck)) {
					header ("Location: question_bank.php");
					unset($_SESSION['bankCategory']);
					exit;
				} else {
					$bankTitleGrabber = mysql_query("SELECT * FROM `modulecategories` WHERE `id` = '{$id}'", $connDBA);
					$bankTitle = mysql_fetch_array($bankTitleGrabber);
				}
			}
			
			$_SESSION['bankCategory'] = $id;
		}
	
		$categoryResult = 1;
	} else {
		$categoryResult = 0;
		unset($_SESSION['categoryName']);
	}
?>
<?php
//Process the form
	if (isset($_POST['id'])) {
		if ($_POST['import']) {
			$question = $_POST['import'];
			$importQuestionsGrabber = mysql_query("SELECT * FROM questionbank WHERE `id` = '{$question}'", $connDBA);
			$importQuestions = mysql_fetch_array($importQuestionsGrabber);
						
		//Import those questions into the test
			$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
			$lastQuestionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} ORDER BY position DESC", $connDBA);
			$lastQuestionFetch = mysql_fetch_array($lastQuestionGrabber);
			$lastQuestion = $lastQuestionFetch['position']+1;
			
			$id = $importQuestions['id'];
			$type = $importQuestions['type'];
			$points = $importQuestions['points'];
			$extraCredit = $importQuestions['extraCredit'];
			$partialCredit = $importQuestions['partialCredit'];
			$difficulty = $importQuestions['difficulty'];
			$category = $importQuestions['category'];
			$link = $importQuestions['link'];
			$randomize = $importQuestions['randomize'];
			$totalFiles = $importQuestions['totalFiles'];
			$case = $importQuestions['case'];
			$tags = $importQuestions['tags'];
			$question = $importQuestions['question'];
			$questionValue = $importQuestions['questionValue'];
			$answer = $importQuestions['answer'];
			$answerValue = $importQuestions['answerValue'];
			$fileURL = $importQuestions['fileURL'];
			$correctFeedback = $importQuestions['correctFeedback'];
			$incorrectFeedback = $importQuestions['incorrectFeedback'];
			$partialFeedback = $importQuestions['partialFeedback'];
			
			$insertQuestionQuery = "INSERT INTO moduletest_{$currentTable} (
								`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
								) VALUES (
								NULL, '1', '{$id}', '{$lastQuestion}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
								)";
								
			mysql_query($insertQuestionQuery, $connDBA);
			
			$location = $currentTable;
										
			if ($type == "File Response") {
				if (!file_exists("../../../../modules/{$location}")) {
					mkdir("../../../../modules/{$location}");
				}
				if (!file_exists("../../../../modules/{$location}/test")) {
					mkdir("../../../../modules/{$location}/test", 0777);
				}
				if (!file_exists("../../../../modules/{$location}/test/fileresponse")) {
					mkdir("../../../../modules/{$location}/test/fileresponse", 0777);
				}
				if (!file_exists("../../../../modules/{$location}/test/fileresponse/responses")) {
					mkdir("../../../../modules/{$location}/test/fileresponse/responses", 0777);
				}
			}
			
			header("Location: question_bank?category=" . $_SESSION['categoryName']);
			exit;
		} else {
			$id = $_POST['id'];
			$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
			$questionPositionGrabber = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE linkID = '{$id}'", $connDBA);
			$questionPositionArray = mysql_fetch_array($questionPositionGrabber);
			$questionPosition = $questionPositionArray['position'];
			
			mysql_query("DELETE FROM moduletest_{$currentTable} WHERE linkID = '{$id}'", $connDBA);
			mysql_query("UPDATE moduletest_{$currentTable} SET position = position-1 WHERE position > '{$questionPosition}'", $connDBA);
			
			header("Location: question_bank?category=" . $_SESSION['categoryName']);
			exit;
		}
	}
?>
<?php
//Assign the page title
	if (isset ($_GET['id'])) {
		if ($_GET['id'] == 0) {
			$title = "Uncategorized Bank";
		} else {
			$title = stripslashes($bankTitle['category']) . " Bank";
		}
	} else {
		$title = "Question Bank";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title($title); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2><?php echo $title; ?></h2>
<?php
	if (isset ($_GET['id'])) {
		echo "<p>&nbsp;</p><div class=\"toolBar\"><a class=\"toolBarItem editTool\" href=\"javascript:void\" onclick=\"MM_openBrWindow('../../question_bank/index.php?id=" . $_GET['id'] . "','','status=yes,scrollbars=yes,width=900,height=500')\">Edit Questions in this Category</a><a class=\"toolBarItem back\" href=\"question_bank.php\">Back to Module Categories</a></div>";
	 }
?>
<?php
//If an updated alert is shown
	if (isset ($_GET['updated'])) {
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
	}
	
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
	}
?>
<?php
	if ($categoryResult !== 0) {
		if (!isset ($_GET['id'])) {
			echo "<p>Please select a category from the list below.</p><blockquote>";
			
			$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY position ASC", $connDBA);
			
			while ($category = mysql_fetch_array($categoryGrabber)) {
				$currentCategory = $category['id'];
				$questionGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '$currentCategory'", $connDBA);
				$questionValue = mysql_num_rows($questionGrabber);
				
				echo "<a href=\"question_bank.php?id=" . $category['id'] . "\">" . stripslashes($category['category']) . "</a> : ";
				if ($questionValue == 1) {
					echo $questionValue . " Question<br /><br />";
				} else {
					echo $questionValue . " Questions<br /><br />";
				}
			}
			
		//Display any questions from categories whose questions may have been deleted
			$currentCategoriesGrabber = mysql_query("SELECT `id` FROM `modulecategories` ORDER BY position ASC", $connDBA);
			$currentCategories = mysql_fetch_array($currentCategoriesGrabber);
			$otherQuestionsGrabber = mysql_query("SELECT * FROM `questionbank` ORDER BY `id` ASC", $connDBA);
			$count = 0;
			
			while ($otherQuestions = mysql_fetch_array($otherQuestionsGrabber)) {
				if (!in_array($otherQuestions['category'], $currentCategories)) {
					if ($count = 0) {
						$count++;
					}
					
					$count++;
				}
			}
			
			if ($count > 0) {
				echo "<a href=\"question_bank.php?id=0\">Uncategorized</a> : ";
				
				if ($count == 1) {
					echo $count . " Question<br /><br />";
				} else {
					echo $count . " Questions<br /><br />";
				}
			}
			
			echo "</blockquote>";
			
			echo "<br /><br /><input name=\"cancel\" type=\"button\" id=\"cancel\" onclick=\"MM_goToURL('parent','../test_content.php');return document.MM_returnValue\" value=\"Back to Test Questions\" /></blockquote>";
		}
		
		if (isset ($_GET['id'])) {	
			echo "<br />";
			if (mysql_fetch_array($testCheck)) {
				echo "<div class=\"catDivider one\">Select Questions</div><div class=\"stepContent\"><blockquote><table class=\"dataTable\"><tbody><tr><th width=\"50\" class=\"tableHeader\">Import</th><th width=\"150\" class=\"tableHeader\">Type</th><th width=\"100\" class=\"tableHeader\">Point Value</th><th class=\"tableHeader\">Question</th></tr>";
				
			//Loop through the items
				$count = 1;	
				while ($testData = mysql_fetch_array($testImport)) {
					echo "<tr";
					if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					$currentTable = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
					$currentID = $testData['id'];
					$checkboxImport = mysql_query("SELECT * FROM moduletest_{$currentTable} WHERE `linkID` = '{$currentID}'", $connDBA);
					echo "<td width=\"50\"><form name=\"importForm\" action=\"question_bank.php?id=" . $_GET['id'] . "\" method=\"post\"><input type=\"hidden\" name=\"id\" value=\"" .$testData['id'] . "\"><input type=\"checkbox\" name=\"import\" id=\"import" . $testData['id'] . "\" value=\"" . $testData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if (mysql_fetch_array($checkboxImport)) {echo " checked=\"checked\"";} echo "></form></td><td width=\"150\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('preview.php?id=" . $testData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('Preview this <strong>" . $testData['type'] . "</strong> question')\" onmouseout=\"UnTip()\">" . $testData['type'] . "</a></td><td width=\"100\"><div";
					if ($testData['extraCredit'] == "on") {
						echo " class=\"extraCredit\"";
					}
					echo ">" . $testData['points'];
					if ($testData['points'] == "1") {
						echo " Point";
					} else {
						echo " Points";
					}
					
					echo "</div></td><td>" . commentTrim(85, $testData['question']) . "</td></tr>";
				}
				echo "</tbody></table></blockquote></div><div class=\"catDivider two\">Submit</div><div class=\"stepContent\"><p><blockquote><input name=\"submit\" type=\"button\" id=\"submit\" onclick=\"MM_goToURL('parent','../test_content.php');return document.MM_returnValue\" value=\"Submit\" /><input name=\"cancel\" type=\"button\" id=\"cancel\" onclick=\"MM_goToURL('parent','question_bank.php');return document.MM_returnValue\" value=\"Cancel\" /></blockquote></p></div>";
			} else {
				echo "<div class=\"noResults\">There are no questions in this bank. Click the link above which says &quot;Edit Questions in this Category&quot; to add questions.</div><br /></br /><blockquote><input name=\"cancel\" type=\"button\" id=\"cancel\" onclick=\"MM_goToURL('parent','question_bank.php');return document.MM_returnValue\" value=\"Cancel\" /></blockquote>";
			}
		}
	} else {
		echo "<div class=\"noResults\">There are no categories to add questions into.</div></br /><br /><blockquote><input name=\"cancel\" type=\"button\" id=\"cancel\" onclick=\"MM_goToURL('parent','question_bank.php');return document.MM_returnValue\" value=\"Cancel\" /></blockquote>";
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>