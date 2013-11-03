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
<div class="toolBar"><a class="toolBarItem announcementLink" href="announcements/index.php">Create Announcement</a><a class="toolBarItem email" href="email/index.php">Send Mass Email</a></div>
<p>&nbsp;</p>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
