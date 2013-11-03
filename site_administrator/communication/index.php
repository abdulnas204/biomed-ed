<?php require_once('../../Connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Communication"); ?>
<?php headers(); ?>
</head>

<body>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Communication</h2>
<div class="toolBar"><a href="announcements/index.php"><img src="../../images/common/announcement.png" alt="Announcement" width="24" height="24" border="0" /></a> <a href="announcements/index.php">Create Announcement</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="email/index.php"><img src="../../images/common/email.png" alt="Email" width="24" height="24" border="0" /></a> <a href="email/index.php">Send Mass Email</a></div>
<p>&nbsp;</p>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
