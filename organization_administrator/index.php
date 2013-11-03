<?php require_once('../Connections/connDBA.php'); ?>
<?php loginCheck("Organization Administrator"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Organization Administration"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("organization_administrator/includes/top_menu.php"); ?>
    <h2>Organization Administration</h2>
<?php footer("organization_administrator/includes/bottom_menu.php"); ?>
</body>
</html>