<?php require_once('Connections/connDBA.php'); ?>
<?php
//Logout the user and destroy all attached sessions
	session_destroy();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Logout"); ?>
<?php headers(); ?>
<meta http-equiv="refresh" content="3; url=index.php">
<script src="javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("includes/top_menu.php"); ?>

      
    <h2>Logout</h2>
    <div align="right" class="main_text_box">
      <p align="center">&nbsp;</p>
      <div align="center">You have successfully logged out.</div>
      <br />
      <div align="center">
        <input name="continue" type="button" id="continue" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Continue" />
      </div>
      <p align="center">&nbsp;</p>
      <p align="center">&nbsp;</p>
      <p align="center">&nbsp;</p>
    </div>
    <p>&nbsp;</p>
<?php footer("includes/bottom_menu.php"); ?>
</body>
</html>