<?php require_once('../../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
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
		$testCheckGrabber = mysql_query("SELECT * FROM moduleData WHERE `name` = '{$name}'", $connDBA);
		$testCheckArray = mysql_fetch_array($testCheckGrabber);
		
		if ($testCheckArray['test'] == "0") {
			header ("Location: test_check.php");
			exit;
		}
	} else {
		header ("Location: ../question_bank.php");
		exit;
	}
?>
<?php
//Select all categories
	$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY position ASC", $connDBA);
	if (mysql_fetch_array($categoryGrabber)) {
	//Use the URL to narrow the categories down on request
		if (isset ($_GET['category'])) {
			$category = urldecode($_GET['category']);
			$categoryCheck = mysql_query("SELECT * FROM `modulecategories` WHERE `category` = '{$category}'", $connDBA);
			$testCheck = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$category}' ORDER BY id ASC", $connDBA);
			$testImport = mysql_query("SELECT * FROM `questionbank` WHERE `category` = '{$category}' ORDER BY id ASC", $connDBA);
			
			if (!mysql_fetch_array($categoryCheck)) {
				header ("Location: question_bank.php");
				unset($_SESSION['category']);
				exit;
			}
			$_SESSION['category'] = urlencode($category);
		}
	
		$categoryResult = 1;
	} else {
		$categoryResult = 0;
		unset($_SESSION['category']);
	}
?>
<?php
//Delete a test question
	if (isset ($_GET['id']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
	//Do not process if question does not exist
	//Get question by URL variable
		$getQuestionID = $_GET['id'];
	
		$questionCheckGrabber = mysql_query("SELECT * FROM questionbank WHERE id = {$getQuestionID}", $connDBA);
		$questionCheckArray = mysql_fetch_array($questionCheckGrabber);
		$questionCheckResult = $questionCheckArray['id'];
		 if (isset ($questionCheckResult)) {
			 $questionCheck = 1;
		 } else {
			$questionCheck = 0;
		 }
	}
 
    if (isset ($_GET['id']) && $questionCheck == "1") {
        $deleteQuestion = $_GET['id'];
        
        $deleteQuestionQuery = "DELETE FROM questionbank WHERE id = {$deleteQuestion}";
        $deleteQuestionQueryResult = mysql_query($deleteQuestionQuery, $connDBA);
		
		header ("Location: question_bank.php");
		exit;
    }
?>
<?php
//Assign the page title
	if (isset ($_GET['category'])) {
		$title = urldecode($_GET['category']) . " Bank";
	} else {
		$title = "Question Bank";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title($title); ?>
<?php headers(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      <h2><?php echo $title; ?></h2>
      <p>Questions may be created here and be imported into tests when a module is being created. The questions are broken up by their category.</p>
<p>&nbsp;</p>
       <div class="toolBar">
<?php
	if (isset ($_GET['category'])) {
		echo "<a href=\"javascript:void\" onclick=\"MM_openBrWindow('../../question_bank/index.php?category=" . urlencode($_GET['category']) . "','','status=yes,scrollbars=yes,width=900,height=500')\"><img src=\"../../../../images/admin_icons/edit.png\" alt=\"Edit\"/></a> <a href=\"javascript:void\" onclick=\"MM_openBrWindow('../../question_bank/index.php?category=" . urlencode($_GET['category']) . "','','status=yes,scrollbars=yes,width=900,height=500')\">Edit Questions in this Category</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	 }
?>
<a href="javascript:void" onclick="MM_openBrWindow('../help.php','','status=yes,scrollbars=yes,width=700,height=500')"><img src="../../../../images/admin_icons/help.png" alt="Help" width="16" height="16" border="0" /></a> <a href="javascript:void" onclick="MM_openBrWindow('../help.php','','status=yes,scrollbars=yes,width=700,height=500')">Help</a>
                                                          </form>
</div>
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
		$message .= "</strong> question was successfully updated.";
		
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
		$message .= "</strong> question was successfully added.";
		
		successMessage($message);
	}
?>
<?php
	if ($categoryResult !== 0) {
		if (!isset ($_GET['category'])) {
			echo "<br /><br /><p>Please select a category from the list below.</p><blockquote>";
			
			$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY position ASC", $connDBA);
			while ($category = mysql_fetch_array($categoryGrabber)) {
				$currentCategory = $category['category'];
				$questionGrabber = mysql_query("SELECT * FROM `questionBank` WHERE `category` = '$currentCategory'", $connDBA);
				$questionValue = mysql_num_rows($questionGrabber);
				
				echo "<a href=\"question_bank.php?category=" . urlencode($category['category']) . "\">" . $category['category'] . "</a> : ";
				if ($questionValue == 1) {
					echo $questionValue . " Question<br /><br />";
				} else {
					echo $questionValue . " Questions<br /><br />";
				}
			}
			
			echo "</blockquote>";
		}
		
		if (isset ($_GET['category'])) {
			echo "<br /><br />";								
			if (mysql_fetch_array($testCheck)) {
				echo "<form action=\"question_bank.php?category=" . urlencode($_GET['category']) . "\" method=\"post\"><div class=\"catDivider\"><img src=\"../../../../images/numbering/1.gif\" alt=\"1.\" width=\"22\" height=\"22\"> Select Questions</div><div class=\"stepContent\"><div align=\"center\"><blockquote><table align=\"center\" class=\"dataTable\" width=\"90%\"><tbody><tr><th width=\"50\" class=\"tableHeader\"><strong>Import</strong></th><th width=\"150\" class=\"tableHeader\"><strong>Type</strong></th><th width=\"100\" class=\"tableHeader\"><strong>Point Value</strong></th><th class=\"tableHeader\"><strong>Question</strong></th></tr>";
				
			//Loop through the items
				$count = 1;	
				while ($testData = mysql_fetch_array($testImport)) {
					echo "<tr";
					if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"50\"><div align=\"center\"><input type=\"checkbox\" name=\"import[]\" id=\"" . $testData['id'] . "\" value=\"" . $testData['id'] . "\"></div></td><td width=\"150\"><div align=\"center\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('preview.php?id=" . $testData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('Preview this <strong>" . $testData['type'] . "</strong> question')\" onmouseout=\"UnTip()\">" . $testData['type'] . "</a></div></td><td width=\"100\" align=\"center\"><div align=\"center\">" . $testData['points'];
					if ($testData['points'] == "1") {
						echo " Point";
					} else {
						echo " Points";
					}
					
					echo "</div></td><td align=\"center\"><div align=\"center\">" . commentTrim(85, $testData['question']) . "</div></td></tr>";
				}
				echo "</tbody></table></blockquote></div></div><div class=\"catDivider\"><img src=\"../../../../images/numbering/2.gif\" alt=\"2.\" width=\"22\" height=\"22\"> Submit</div><div class=\"stepContent\"><p><blockquote>"; submit("submit", "Submit"); echo "<input name=\"reset\" type=\"reset\" id=\"reset\" onclick=\"GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue\" value=\"Reset\" /><input name=\"cancel\" type=\"button\" id=\"cancel\" onclick=\"MM_goToURL('parent','question_bank.php');return document.MM_returnValue\" value=\"Cancel\" /></blockquote></p></div></form><br /><br />";
			} else {
				echo "<br /></br /><br /></br /><div align=\"center\">There are no questions in this bank. Questions can be created by selecting a question type from the drop down menu above, and pressing \"Go\".</div><br /></br /><br /></br /><br /></br />";
			}
		}
	} else {
		echo "<br /></br /><br /></br /><div align=\"center\">Please <a href=\"../settings.php?type=category\">add at least one category</a> prior to entering questions.</div><br /></br /><br /></br /><br /></br />";
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>