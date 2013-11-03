<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Ensure the needed data is provided
	if (!isset($_SESSION['currentModule']) || !isset($_SESSION['review'])) {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Check to see if a test exists
	$name = $_SESSION['currentModule'];
	$testCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE name = '{$name}'", $connDBA);
	$testCheck = mysql_fetch_array($testCheckGrabber);
?>
<?php
//Ensure that the test generator cannot be access if any required variables to not fulfilled
	if ($testCheck['test'] == "0" || $testCheck['testName'] == "T" || $testCheck['directions'] == "D") {
		mysql_query("UPDATE moduledata SET `test` = '0', `testName` = 'T', `directions` = 'D' WHERE `name` = '{$name}'", $connDBA);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Modify a Module"); ?>
<?php headers(); ?>
<script type="text/javascript">
<!--
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
//-->
</script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Modify a Module</h2>
<p>Select a specific part of the module to modify:</p>
<p>
  <?php
//Display a success message
	if (isset ($_GET['updated'])) {
		switch ($_GET['updated']) {
			case "lessonSettings" : successMessage("The module settings were updated"); break;
			case "lessonContent" : successMessage("The module content was updated"); break;
			case "testSettings" : successMessage("The test settings were updated"); break;
			case "testContent" : successMessage("The test content was updated"); break;
		}
	}
?>
</p>
<div class="catDivider"><img src="../../../images/numbering/1.gif" alt="1." width="22" height="22" /> Modify Module</div>
<div class="stepContent">
<blockquote>
  <p><a href="lesson_settings.php">Module Settings</a><br/ >
  <a href="lesson_content.php">Module Content</a></p>
</blockquote>
</div>
<div class="catDivider"><img src="../../../images/numbering/2.gif" alt="2." width="22" height="22" /> Modify Test</div>
<div class="stepContent">
<blockquote>
    <?php
  //Selectively display the test settings
  		if ($testCheck['test'] == "1") {
			echo "<p><a href=\"test_settings.php\">Test Settings</a><br/ >
  				<a href=\"test_content.php\">Test Content</a></p>";
		} elseif ($testCheck['test'] == "0") {
			echo "<p>No test exists for this module. <a href=\"test_check.php\">Create one now</a></p>";
		}
  ?>
</blockquote>
</div>
<div class="catDivider"><img src="../../../images/numbering/3.gif" alt="3." width="22" height="22" /> Finish</div>
<div class="stepContent">
  <p>
    <blockquote>
      <p>
        <input name="done" type="submit" id="done" onclick="MM_goToURL('parent','../index.php');return document.MM_returnValue" value="Done" />
      </p>
    </blockquote>
  </p>
</blockquote>
</div>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
