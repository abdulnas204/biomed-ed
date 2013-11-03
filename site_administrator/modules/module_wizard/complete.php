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
			case "testContent" : header ("Location: test_content.php"); exit; break;
			//case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		header ("Location: modify.php");
		exit;
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Process the form
	if (isset ($_POST['submit'])) {
		$currentModule = $_SESSION['currentModule'];
		$setAvaliable = "UPDATE moduledata SET `avaliable` = 'on' WHERE `name` = '{$currentModule}'";
		mysql_query($setAvaliable, $connDBA);
		
		header("Location: ../index.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Complete"); ?>
<?php headers(); ?>
<?php validate(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Complete</h2>
    <form name="finish" method="post" action="complete.php" id="validate" onsubmit="return errorsOnSubmit(this);">
    <div align="center">
      <p>The module &quot;<strong><?php echo $_SESSION['currentModule']; ?></strong>&quot; has been successfully created. </p>
      <p>&nbsp;</p>
      <label>
        <?php submit("submit", "Finish"); ?>
      </label>
      </div>
      <?php formErrors(); ?>
    </form>
	  <p>&nbsp;</p>
	  <p>&nbsp;</p>
	  <p>&nbsp;</p>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>