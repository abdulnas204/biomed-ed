<?php require_once('../../Connections/connDBA.php'); ?>
<?php
//Grant access to this page an id is defined and the user exists
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$userGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
		if ($userCheck = mysql_fetch_array($userGrabber)) {
			$user = $userCheck;
		} else {
			$user = false;
			header("Location: index.php");
			exit;
		}
	} else {
		header("Location: index.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title($user['firstName'] . " " . $user['lastName']); ?>
<?php headers(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2><?php echo $user['firstName'] . " " . $user['lastName']; ?></h2>
<?php
	if ($user['organization'] == "1" && $user['role'] !== "Site Administrator" && $user['role'] !== "Site Manager") {
		errorMessage($user['firstName'].  " needs assigned to an organization. <a href=\"assign_user.php?id=" . $user['id'] . "\">Assign " . $user['firstName'] . " now</a>.");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<div class="toolBar"><a href="manage_user.php"><img src="../../images/admin_icons/new.png" alt="Add" width="24" height="24" /></a> <a href="manage_user.php">Add New User</a><?php if ($user['userName'] !== $_SESSION['MM_Username']) {echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"manage_user.php?id=" . $user['id'] . "\"><img src=\"../../images/admin_icons/edit.png\" alt=\"Edit\" width=\"24\" height=\"24\" /></a> <a href=\"manage_user.php?id=" . $user['id'] . "\">Edit this User</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?action=delete&id=" . $user['id'] . "\"><img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" width=\"24\" height=\"24\" /></a> <a href=\"index.php?action=delete&id=" . $user['id'] ."\">Delete this User</a>";} ?></div>
<br />
<div class="catDivider"><img src="../../images/numbering/1.gif" alt="1." width="22" height="22" /> User Information</div>
<div class="stepContent">
<table width="100%">
  <tr>
    <td width="200"><div align="right">First Name:</div></td>
    <td><?php echo $user['firstName']; ?></td>
  </tr>
  <tr>
    <td width="200"><div align="right">Last Name:</div></td>
    <td><?php echo $user['lastName']; ?></td>
  </tr>
  <tr>
    <td width="200"><div align="right">User Name:</div></td>
    <td><?php echo $user['userName']; ?></td>
  </tr>
  <tr>
    <td><div align="right">Role:</div></td>
    <td><?php echo $user['role']; ?></td>
  </tr>
</table>
</div>
<div class="catDivider"><img src="../../images/numbering/2.gif" alt="2." width="22" height="22" /> Contact Information</div>
<div class="stepContent">
    <table width="100%">
    <tr>
        <td width="200"><div align="right">
          <?php if ($user['emailAddress2'] == "" && $user['emailAddress3'] == "") {echo "Email Address:";} else {echo "Primary Email Address:";} ?>
      </div></td>
      <td><?php echo "<a href=\"mailto:" . $user['emailAddress1'] . "\">" . $user['emailAddress1'] . "</a>"; ?></td>
      </tr>
      <?php
      //If a second email address is configured
            if ($user['emailAddress2'] !== "") {
                echo "<tr>
                    <td><div align=\"right\">Secondary Email Address:</div></td>
                    <td><a href=\"mailto:" . $user['emailAddress2'] . "\">" . $user['emailAddress2'] . "</a></td>
                </tr>";
            }
      ?>
  	  <?php
      //If a tertiary email address is configured
            if ($user['emailAddress2'] !== "") {
                echo "<tr>
                    <td><div align=\"right\">Tertiary Email Address:</div></td>
                    <td><a href=\"mailto:" . $user['emailAddress3'] . "\">" . $user['emailAddress3'] . "</a></td>
                </tr>";
            }
      ?>
      <tr>
        <td width="200"><div align="right">Work Phone:</div></td>
        <td><?php echo $user['phoneWork']; ?></td>
      </tr>
      <tr>
        <td width="200"><div align="right">Home Phone:</div></td>
        <td><?php echo $user['phoneHome']; ?></td>
      </tr>
  	  <?php
      //If a mobile phone is configured
            if ($user['phoneMobile'] !== "") {
                echo "<tr>
                    <td><div align=\"right\">Mobile Phone:</div></td>
                    <td>" . $user['phoneMobile'] . "</td>
                </tr>";
            }
      ?>
  	  <?php
      //If a fax number is configured
            if ($user['phoneFax'] !== "") {
                echo "<tr>
                    <td><div align=\"right\">Fax Number:</div></td>
                    <td>" . $user['phoneFax'] . "</td>
                </tr>";
            }
      ?>
  </table>
</div>
<?php
	if ($user['role'] == "Site Administrator" || $user['role'] == "Site Manager") {
		echo "<div class=\"catDivider\"><img src=\"../../images/numbering/3.gif\" alt=\"3.\" width=\"22\" height=\"22\" /> Finish</div><div class=\"stepContent\"><blockquote><input name=\"finish\" id=\"finish\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Finish\" type=\"button\"></blockquote></div>";
	} else {
?>
<div class="catDivider"><img src="../../images/numbering/3.gif" alt="3." width="22" height="22" /> Workplace Information</div>
<div class="stepContent">
    <table width="100%">
      <tr>
        <td width="200"><div align="right">Work Location:</div></td>
        <td><?php $user['workLocation']; ?></td>
      </tr>
      <tr>
        <td width="200"><div align="right">Job Title:</div></td>
        <td><?php $user['jobTitle']; ?></td>
      </tr>
      <tr>
        <td width="200"><div align="right">Department:</div></td>
        <td><?php $user['department']; ?></td>
      </tr>
      <tr>
        <td width="200"><div align="right">Department ID:</div></td>
        <td><?php $user['departmentID']; ?></td>
      </tr>
      <?php
	  //If the user is not participating in an organization do not provide any information on it
	  		if ($user['organization'] !== "1") {
				echo "<tr>
					<td width=\"200\"><div align=\"right\">Participating Organization:</div></td>
					<td>" . $user['organization']. "</td>
				  </tr>";
			}
	  ?>
    </table>
</div>
<div class="catDivider"><img src="../../images/numbering/4.gif" alt="4." width="22" height="22" /> Finish</div>
<div class="stepContent">
  <blockquote>
    <p>
      <input name="finish" id="finish" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Finish" type="button">
    </p>
  </blockquote>
</div>
<?php
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
