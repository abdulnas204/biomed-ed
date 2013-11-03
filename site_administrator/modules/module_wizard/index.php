<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Set a session to monitor each of the steps
	if (isset ($_POST['submit'])) {
		$_SESSION['step'] = "lessonSettings";
		header ("Location: lesson_settings.php");
		exit;
	}
?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		header ("Location: modify.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>      
    <h2>Welcome to the Module Setup  Wizard</h2>
    <p>This wizard will guide you through the process of setting up a module. Click &quot;Launch Wizard&quot; to begin. </p>
<p>&nbsp;</p>
      <form id="startup" name="startup" method="post" action="index.php">
        <div align="center">
          <div align="center">
            <input name="submit" type="submit" id="submit" value="Launch Wizard" />
          </div>
        </div>
</form>
<br />
    <br />
    <br />
    <br />
    <br />
    <br />
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>