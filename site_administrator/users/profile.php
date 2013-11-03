<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
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
<script src="../../javascripts/common/warningDelete.js" type="text/javascript"></script>
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
<?php 
	echo "<div class=\"toolBar\">";
	
	if ($user['role'] != "Site Administrator" && $user['role'] != "Site Manager" && $user['organization'] == "") {
		echo "<a class=\"toolBarItem user\" href=\"assign_user.php?id=" . $user['id'] . "\">Assign to Organization</a>";
	}
	
	echo "<a class=\"toolBarItem editTool\" href=\"manage_user.php?id=" . $user['id'] . "\">Edit this User</a>";
	
	if ($user['userName'] != $_SESSION['MM_Username']) {
		echo "<a class=\"toolBarItem deleteTool\" a href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&id=" . $user['id'] . "', 'user')\">Delete this User</a>";
	}
	
	echo "</div>";
?>
<br />
<div class="catDivider one">User Information</div>
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
<div class="catDivider two">Contact Information</div>
<div class="stepContent">
    <table width="100%">
    <tr>
        <td width="200"><div align="right">
          <?php if ($user['emailAddress2'] == "" && $user['emailAddress3'] == "") {echo "Email Address:";} else {echo "Primary Email Address:";} ?>
      </div></td>
      <td><?php echo "<a href=\"../communication/send_email.php?id=" . $user['id'] . "&address=1\">" . $user['emailAddress1'] . "</a>"; ?></td>
      </tr>
      <?php
      //If a second email address is configured
            if ($user['emailAddress2'] != "") {
                echo "<tr>
                    <td><div align=\"right\">Secondary Email Address:</div></td>
                    <td><a href=\"../communication/send_email.php?id=" . $user['id'] . "&address=2\">" . $user['emailAddress2'] . "</a></td>
                </tr>";
            }
      ?>
  	  <?php
      //If a tertiary email address is configured
            if ($user['emailAddress3'] != "") {
                echo "<tr>
                    <td><div align=\"right\">Tertiary Email Address:</div></td>
                    <td><a href=\"../communication/send_email.php?id=" . $user['id'] . "&address=3\">" . $user['emailAddress3'] . "</a></td>
                </tr>";
            }
      ?>
      <?php
	  //If a work phone is configured
	  		if ($user['phoneWork'] != "") {
				echo "<tr>
                    <td><div align=\"right\">Work Phone:</div></td>
                    <td>" . $user['phoneWork'] . "</td>
                </tr>";
			}
	  ?>
      <?php
	  //If a home phone is configured
	  		if ($user['phoneHome'] != "") {
				echo "<tr>
                    <td><div align=\"right\">Home Phone:</div></td>
                    <td>" . $user['phoneHome'] . "</td>
                </tr>";
			}
	  ?>
  	  <?php
      //If a mobile phone is configured
            if ($user['phoneMobile'] != "") {
                echo "<tr>
                    <td><div align=\"right\">Mobile Phone:</div></td>
                    <td>" . $user['phoneMobile'] . "</td>
                </tr>";
            }
      ?>
  	  <?php
      //If a fax number is configured
            if ($user['phoneFax'] != "") {
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
		echo "<div class=\"catDivider three\">Finish</div><div class=\"stepContent\"><blockquote><input name=\"finish\" id=\"finish\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Finish\" type=\"button\"></blockquote></div>";
	} else {
		if ($user['organization'] != "") {
			echo "<div class=\"catDivider three\">Workplace Information</div>
					<div class=\"stepContent\">
    					<table width=\"100%\">";
						
						if ($user['workLocation'] != "") {
							echo "<tr>
								<td width=\"200\"><div align=\"right\">Work Location:</div></td>
								<td>" . $user['workLocation'] . "</td>
							  </tr>";
						}
						
						if ($user['jobTitle'] != "") {
							echo "<tr>
								<td width=\"200\"><div align=\"right\">Job Title:</div></td>
								<td>" . $user['jobTitle'] . "</td>
							  </tr>";
						}
                          
                        if ($user['department'] != "") {
							echo "<tr>
								<td width=\"200\"><div align=\"right\">Department:</div></td>
								<td>" . $user['department'] . "</td>
							  </tr>";
						}
						
						if ($user['departmentID'] != "") {
							echo "<tr>
								<td width=\"200\"><div align=\"right\">Departnment ID:</div></td>
								<td>" . $user['departmentID'] . "</td>
							  </tr>";
						}
						
						$organizationID = $user['organization'];
						$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$organizationID}'", $connDBA);
						$organization = mysql_fetch_array($organizationGrabber);
						
						echo "<tr>
								<td width=\"200\"><div align=\"right\">Assigned Organization:</div></td>
								<td>" . $organization['organization'] . "</td>
							  </tr>
						</table>
					</div>
					<div class=\"catDivider four\">Finish</div>
					<div class=\"stepContent\">
					  <blockquote>
						<p><input name=\"finish\" id=\"finish\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Finish\" type=\"button\"></p>
					  </blockquote>
					</div>";
		} else {
?>
<div class="catDivider three">Workplace Information</div>
<div class="stepContent">
  <blockquote>
    <p><div align="center">Awaiting information</div> </p>
  </blockquote>
</div>
<div class="catDivider four">Finish</div>
<div class="stepContent">
  <blockquote>
    <p><input name="finish" id="finish" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Finish" type="button"></p>
  </blockquote>
</div>
<?php			
		}
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
