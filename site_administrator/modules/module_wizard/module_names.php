<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			//case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
	header("Content-type: text/xml");
	$moduleGrabber = mysql_query("SELECT * FROM moduledata", $connDBA);
	
	echo "<root>";
	while ($module = mysql_fetch_array($moduleGrabber)) {
		echo "<name>" . $module['name'] . "</name>";
	}
	echo "</root>";
?>