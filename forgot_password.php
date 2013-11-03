<?php require_once('Connections/connDBA.php'); ?>
<?php
	if (isset ($_SESSION['MM_Username'])) {
		$userRole = $_SESSION['MM_UserGroup'];
		
		switch ($userRole) {
			case "Student": header ("Location: student/index.php"); exit; break;
			case "Instructor": header ("Location: instructor/index.php"); exit; break;
			case "Organization Administrator": header ("Location: organization_administrator/index.php"); exit; break;
			case "Site Administrator": header ("Location: site_administrator/index.php"); exit; break;
		}
	}
?>
<?php
//Process the form
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Password Recovery"); ?>
<?php headers(); ?>
<body<?php bodyClass(); ?>>
<?php topPage("includes/top_menu.php"); ?>
<h2>Password Recovery</h2>
<p>Enter  your username and email address to recover your password:</p>
<form>
<table width="100%" border="0" align="center">
  <tr>
    <td width="30%"><div align="right">Username:</div></td>
    <td width="70%"><div align="left">
      <label>
      <input type="text" name="emailUsername" id="emailUsername" />
      </label>
    </div></td>
  </tr>
  <tr>
    <td width="30%"><div align="right">Email Address:</div></td>
    <td width="70%"><div align="left">
      <label>
      <input type="text" name="emailRecovery" id="emailRecovery" />
      </label>
    </div></td>
  </tr>
  <tr>
    <td width="30%">&nbsp;</td>
    <td width="70%"><div align="left">
      <label>
      <input type="submit" name="submit" id="submit" value="Submit" />
      </label>
    </div></td>
  </tr>
</table>
</form>
<?php footer("includes/bottom_menu.php"); ?>
</body>
</html>
