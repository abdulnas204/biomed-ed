<?php require_once('../Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Access Denied"); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php
	if (isset ($_SESSION['MM_UserGroup'])) {
		switch($_SESSION['MM_UserGroup']) {
			case "Student": $topPage = "student/includes/top_menu.php"; break;
			case "Instructor": $topPage = "instructor/includes/top_menu.php"; break;
			case "Organization Administrator": $topPage = "administrator/includes/top_menu.php"; break;
			case "Site Administrator": $topPage = "site_administrator/includes/top_menu.php"; break;
		}
	} else {
		$topPage = "includes/top_menu.php";
	}
?>
<?php topPage($topPage); ?>
<h2>Access Denied</h2>
<?php
	errorMessage("You do not have premission to access this content");
?>
<p align="center">
  <input type="button" name="continue" id="continue" value="Continue" onclick="history.go(-1)" />
</p>
<?php
	if (isset ($_SESSION['MM_UserGroup'])) {
		switch($_SESSION['MM_UserGroup']) {
			case "Student": $bottomPage = "student/includes/bottom_menu.php"; break;
			case "Instructor": $bottomPage = "instructor/includes/bottom_menu.php"; break;
			case "Organization Administrator": $bottomPage = "administrator/includes/bottom_menu.php"; break;
			case "Site Administrator": $bottomPage = "site_administrator/includes/bottom_menu.php"; break;
		}
	} else {
		$bottomPage = "includes/bottom_menu.php";
	}
?>
<?php footer($bottomPage); ?>
</body>
</html>