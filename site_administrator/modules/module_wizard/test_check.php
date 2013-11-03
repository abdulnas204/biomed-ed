<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			//case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
	//Check to see if a test is set to be created, otherwise allow access to this page
		$name = $_SESSION['currentModule'];
		$testCheckGrabber = mysql_query("SELECT * FROM moduleData WHERE `name` = '{$name}'", $connDBA);
		$testCheckArray = mysql_fetch_array($testCheckGrabber);
		
		if ($testCheckArray['test'] == "1") {
			header ("Location: test_settings.php");
			exit;
		}
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Create a test table if the user has selected it
	if (isset ($_POST['submit'])) {
		$dataBaseName = str_replace(" ","", $_SESSION['currentModule']);
		mysql_query("CREATE TABLE IF NOT EXISTS `moduletest_{$dataBaseName}` (
					  `id` int(255) NOT NULL AUTO_INCREMENT,
					  `questionBank` int(1) NOT NULL,
					  `linkID` int(255) NOT NULL,
					  `position` int(100) NOT NULL,
					  `type` longtext NOT NULL,
					  `points` int(3) NOT NULL,
					  `extraCredit` text NOT NULL,
					  `partialCredit` int(1) NOT NULL,
					  `difficulty` longtext NOT NULL,
					  `category` longtext NOT NULL,
					  `link` longtext NOT NULL,
					  `randomize` int(1) NOT NULL,
					  `totalFiles` int(2) NOT NULL,
					  `choiceType` text NOT NULL,
					  `case` int(1) NOT NULL,
					  `tags` longtext NOT NULL,
					  `question` longtext NOT NULL,
					  `questionValue` longtext NOT NULL,
					  `answer` longtext NOT NULL,
					  `answerValue` longtext NOT NULL,
					  `fileURL` longtext NOT NULL,
					  `correctFeedback` longtext NOT NULL,
					  `incorrectFeedback` longtext NOT NULL,
					  `partialFeedback` longtext NOT NULL,
					  PRIMARY KEY (`id`)
					)");
	
	//Add data to moduledata, showing that a test is included with this module	
		//Use the session to find where to insert the test data
		$currentModule = $_SESSION['currentModule'];
		
		$addToModuleQuery = "UPDATE moduledata SET test = '1' WHERE name = '{$currentModule}'";
		
		//Execute command on database			
		$addToModuleQueryResult = mysql_query($addToModuleQuery, $connDBA);	
		
	//Update the session to manage the steps
		$_SESSION['step'] = "testSettings";		
			
		header ("Location: test_settings.php");
		exit;
	}
	
//Skip the test, and go to the end
	if (isset ($_POST['skipTest'])) {
		header ("Location: complete.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Create a Test"); ?>
<?php headers(); ?>
<?php validate(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Module Setup Wizard : Create a Test</h2>
<p align="center">Do you wish to create a test for this module?</p>
    <form name="testCheck" method="post" action="test_check.php" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div>
        <div align="center">
          <?php 
			submit("submit", "Create a Test");
			submit("skipTest", "Do not Create Test");
		  ?>
          <?php formErrors(); ?>
        </div>
      </div>
	</form>
    <p>&nbsp;</p>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>