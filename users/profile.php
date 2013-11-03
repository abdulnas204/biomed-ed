<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grant access to this page a user is defined and the user exists
	if (access("manageThisUser") && exist("users", "id", $_GET['id'])) {
		$user = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}'");
	} else {
		redirect("../portal/index.php");
	}
	
//Create a function to easily create table rows
	function row($lebel, $content, $contentExists = false) {
		if ($contentExists == true) {
			if (!empty($contentExists)) {
				echo "<tr><td width=\"200\"><div align=\"right\">" . $label . ":</div></td><td>" .  $content . "</td></tr>";
			}
		} else {
			echo "<tr><td width=\"200\"><div align=\"right\">" . $label . ":</div></td><td>" .  $content . "</td></tr>";
		}
	}
	
	headers($user['firstName'] . " " . $user['lastName'], "Student,Organization Administrator,Site Administrator");

//Title
	title($user['firstName'] . " " . $user['lastName'], false, false);
	
//Admin toolbar
	if (access("manageThisUser") && $_SESSION['MM_UserGroup'] != "Student") {
		echo "<div class=\"toolBar\">";
		echo URL("Edit this User", "manage_user.php?id=" . $user['id'], "toolBarItem editTool");
		
		if ($user['userName'] != $_SESSION['MM_Username']) {
			echo URL("Edit this User", "index.php?action=delete&id=" . $user['id'], "toolBarItem deleteTool", false, false, true);
		}
		
		echo "</div><br />";
	}
?>

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
      <td><?php echo "<a href=\"../communication/send_email.php?type=user&id=" . $user['id'] . "&address=1\">" . $user['emailAddress1'] . "</a>"; ?></td>
      </tr>
      <?php
      //If a second email address is configured
            if ($user['emailAddress2'] != "") {
                echo "<tr>
                    <td><div align=\"right\">Secondary Email Address:</div></td>
                    <td><a href=\"../communication/send_email.php?type=user&id=" . $user['id'] . "&address=2\">" . $user['emailAddress2'] . "</a></td>
                </tr>";
            }
      ?>
  	  <?php
      //If a tertiary email address is configured
            if ($user['emailAddress3'] != "") {
                echo "<tr>
                    <td><div align=\"right\">Tertiary Email Address:</div></td>
                    <td><a href=\"../communication/send_email.php?type=user&id=" . $user['id'] . "&address=3\">" . $user['emailAddress3'] . "</a></td>
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
		if ($user['organization'] != "1") {
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
								<td><a href=\"../organizations/profile.php?id=" . $organization['id'] . "\">" . $organization['organization'] . "</a></td>
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
  <p><div class="noResults">Awaiting information</div></p>
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
