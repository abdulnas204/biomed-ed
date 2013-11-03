<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Delete a user
	if (isset ($_GET['action']) && isset ($_GET['id']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$userCheck = mysql_query("SELECT * FROM `users` WHERE `id` = '{$id}'", $connDBA);
		if ($user = mysql_fetch_array($userCheck)) {
			if ($user['userName'] !== $_SESSION['MM_Username']) {
				mysql_query("DELETE FROM `users` WHERE `id` = '{$id}'", $connDBA);
				
				header ("Location: index.php?message=userDeleted");
				exit;
			} else {
				header ("Location: index.php");
				exit;
			}
		} else {
			header ("Location: index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Users"); ?>
<?php headers(); ?>
<script src="../../javascripts/common/warningDelete.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>

      
      <h2>Users </h2>
      <p>Below is a list of all users registered within this system. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column.</p>
<?php
//If the user is prompted to assign a user to an organization
	if (isset($_GET['message']) && $_GET['message'] == "organization") {
		if (isset ($_GET['id'])) {
			$id = $_GET['id'];
			$userGrabber = mysql_query("SELECT * FROM users WHERE `id` = '{$id}'", $connDBA);
			if ($user = mysql_fetch_array($userGrabber)) {
				if ($user['role'] !== "Site Administrator" && $user['role'] !== "Site Manager") {
				successMessage($user['firstName'] . " " . $user['lastName'] . " needs to be enrolled in an organization. <a href=\"assign_user.php?id=" . $id . "\">Enroll " . $user['firstName'] . " now</a>.");
				} else {
					header ("Location: index.php");
					exit;
				}
			} else {
				header ("Location: index.php");
				exit;
			}
		} else {
			header ("Location: index.php");
			exit;
		}
//If the user has assigned someone to an organization
	} elseif (isset($_GET['message']) && $_GET['message'] == "assignedUser") {
		successMessage("The user was assigned to an organization.");
//A user was created
	} elseif (isset($_GET['message']) && $_GET['message'] == "userCreated") {
		successMessage("The user was created.");	
//A user was edited
	} elseif (isset($_GET['message']) && $_GET['message'] == "userEdited") {
		successMessage("The user was modified.");	
//A user was deleted
	} elseif (isset($_GET['message']) && $_GET['message'] == "userDeleted") {
		successMessage("The user was deleted.");
//If the user is given an error when assigning an administrator to an orgainzation
	} elseif (isset($_GET['message']) && $_GET['message'] == "errorAssign") {
		errorMessage("Site administrators and site managers cannot be assigned to an organization. Please change their role if you wish to assign them.");
//If the user is given an error that an assigned user does not exist
	} elseif (isset($_GET['message']) && $_GET['message'] == "noUser") {
		errorMessage("The user you are attempting to assign to an organization does not exist.");
//If the user is given an error that an assigned organization does not exist
	} elseif (isset($_GET['message']) && $_GET['message'] == "noOrganization") {
		errorMessage("The organization you are attempting to assign to a user does not exist.");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<div class="toolBar"><a href="manage_user.php"><img src="../../images/admin_icons/new.png" alt="Add" width="24" height="24" /></a> <a href="manage_user.php">Add New User</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="assign_user.php"><img src="../../images/admin_icons/profile.png" alt="User" width="24" height="24" /></a> <a href="assign_user.php">Assign Users to Organization</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="search.php"><img src="../../images/admin_icons/search.png" alt="Search" width="24" height="24" /></a> <a href="search.php">Search for Users</a></div>
<div class="toolBar">Items per Page: 
  <label>
    <select name="items" id="items">
      <option value="25">25</option>
      <option value="50">50</option>
      <option value="100">100</option>
      <option value="200">200</option>
      <option value="all">All</option>
    </select>
  </label>
<a href="search.php"></a></div>
<br />
   <div align="center">
		<?php
			if (!isset ($_GET['sort'])) {
				$userGrabber = mysql_query("SELECT * FROM users ORDER BY lastName ASC", $connDBA);
			} else {
			//If a session is controlling the number of list items
			 	if (isset ($_SESSION['items'])) {
					
				}
				
				switch ($_GET['sort']) {
					case "nameDescending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY lastName DESC, role ASC", $connDBA); break;
					case "nameAscending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY lastName ASC, role ASC", $connDBA); break;
					case "emailDescending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY emailAddress1 DESC", $connDBA); break;
					case "emailAscending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY emailAddress1 ASC", $connDBA); break;
					case "roleDescending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY role DESC, lastName ASC", $connDBA); break;
					case "roleAscending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY role ASC, lastName ASC", $connDBA); break;
					case "organizationDescending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY organization DESC, lastName ASC", $connDBA); break;
					case "organizationAscending" : $userGrabber = mysql_query("SELECT * FROM users ORDER BY organization ASC, lastName ASC", $connDBA); break;
				}
			}
			
			if (!$userGrabber) {
				$users = "empty";
			}
			
			$userNumberGrabber = mysql_query("SELECT * FROM users ORDER BY lastName ASC, role ASC", $connDBA);
			$userNumber = mysql_num_rows($userNumberGrabber);
		?>
		<?php
		//If no users exist	
			if (isset ($users) && $users == "empty") {
				centerDiv("No users exist on this system");
			} else {
		//If users exist
				echo "<table align=\"center\" class=\"dataTable\">
				<tbody>
					<tr>
						<th width=\"200\" class=\"tableHeader\"><strong><a href=\"index.php";
						if (isset ($_GET['sort']) && $_GET['sort'] == "nameDescending") {
							echo "?sort=nameAscending\" class=\"ascending\">Name</a></strong>";
						} elseif (isset ($_GET['sort']) && $_GET['sort'] == "nameAscending") {
							echo "?sort=nameDescending\" class=\"descending\">Name</a></strong>";
						} elseif (!isset ($_GET['sort'])) {
							echo "?sort=nameDescending\" class=\"descending\">Name</a></strong>";
						} else {
							echo "?sort=nameAscending\" class=\"sortHover\">Name</a></strong>";
						}
						echo "</th>
						<th width=\"150\" class=\"tableHeader\"><strong><a href=\"index.php";
						if (isset ($_GET['sort']) && $_GET['sort'] == "emailAscending") {
							echo "?sort=emailDescending\" class=\"descending\">Email Address</a></strong>";
						} elseif (isset ($_GET['sort']) && $_GET['sort'] == "emailDescending") {
							echo "?sort=emailAscending\" class=\"ascending\">Email Address</a></strong>";
						} else {
							echo "?sort=emailAscending\" class=\"sortHover\">Email Address</a></strong>";
						}
						echo "</th>
						<th width=\"175\" class=\"tableHeader\"><strong><a href=\"index.php";
						if (isset ($_GET['sort']) && $_GET['sort'] == "roleAscending") {
							echo "?sort=roleDescending\" class=\"descending\">Role</a></strong>";
						} elseif (isset ($_GET['sort']) && $_GET['sort'] == "roleDescending") {
							echo "?sort=roleAscending\" class=\"ascending\">Role</a></strong>";
						} else {
							echo "?sort=roleAscending\" class=\"sortHover\">Role</a></strong>";
						}
						echo "</th>
						<th class=\"tableHeader\"><strong><a href=\"index.php";
						if (isset ($_GET['sort']) && $_GET['sort'] == "organizationAscending") {
							echo "?sort=organizationDescending\" class=\"descending\">Participating Organization</a></strong>";
						} elseif (isset ($_GET['sort']) && $_GET['sort'] == "organizationDescending") {
							echo "?sort=organizationAscending\" class=\"ascending\">Participating Organization</a></strong>";
						} else {
							echo "?sort=organizationAscending\" class=\"sortHover\">Participating Organization</a></strong>";
						}
						echo "</th>
						<th width=\"50\" class=\"tableHeader\"><strong>Statistics</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Edit</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Delete</strong></th>
						
					</tr>";
				$number = 1;
				while(($userData = mysql_fetch_array($userGrabber)) && ($number <= $userNumber)) {
					echo "<tr";
					//Alternate the color of each row.
					if ($number++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"200\"> <div align=\"center\"><a href=\"profile.php?id=" . $userData['id'] . "\">" . $userData['lastName'] . ", " . $userData['firstName'] . "</a></div></td>" . 
					"<td width=\"150\"> <div align=\"center\">" . "<a href=\"mailto:" . $userData['emailAddress1'] . "\">" . $userData['emailAddress1'] . "</a>" . "</div></td>" . 
					"<td width=\"175\"> <div align=\"center\">" . $userData['role'] . "</div></td>";
					if (!$userData['organization'] || $userData['organization'] == "1") {
						if ($userData['role'] !== "Site Administrator" && $userData['role'] !== "Site Manager") {
							$organization = "<img src=\"../../images/admin_icons/warning.png\" alt=\"Alert\" width=\"24\" height=\"24\" onmouseover=\"Tip('This user is not assigned to an organization')\" onmouseout=\"UnTip()\" /> <i>None</i>";
						} else {
							$organization = "<i>None</i>";
						}
					} else {
						$organization = $userData['organization'];
					}
					 
					echo "<td> <div align=\"center\">" . $organization . "</div></td>" . 
					"<td width=\"50\" align=\"center\"> <div align=\"center\"><a href=\"../statistics/index.php?type=user&number=single&period=overall&id=" . $userData['id'] . "\"><img src=\"../../images/admin_icons/statistics.png\" alt=\"Statistics\" onmouseover=\"Tip('View <strong>" . $userData['firstName'] . " " . $userData['lastName'] . "\'s</strong> statistics</strong>')\" onmouseout=\"UnTip()\"></a>" . 
					"<td width=\"50\" align=\"center\"> <div align=\"center\">";
					if ($userData['userName'] !== $_SESSION['MM_Username']) {
						echo "<a href=\"manage_user.php?id=" . $userData['id'] . "\"><img src=\"../../images/admin_icons/edit.png\" alt=\"Edit\" onmouseover=\"Tip('Edit <strong> " .  $userData['firstName'] . " " . $userData['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>";
					} else {
						echo "<img src=\"../../images/admin_icons/noEdit.png\" alt=\"Edit\" onmouseover=\"Tip('You may not edit yourself')\" onmouseout=\"UnTip()\">";
					}
						
					echo "</div></td>" . "<td width=\"50\" align=\"center\"><div align=\"center\">";
					if ($userData['userName'] !== $_SESSION['MM_Username']) {
						echo "<a href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&id=" . $userData['id'] . "', 'user')\"><img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" onmouseover=\"Tip('Delete <strong>" . $userData['firstName'] . " " .  $userData['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>";
					} else {
						echo "<img src=\"../../images/admin_icons/noDelete.png\" alt=\"Delete\" onmouseover=\"Tip('You may not delete yourself')\" onmouseout=\"UnTip()\">";
					}
					echo "</div></td></tr>";
				}
				echo "</tbody>
				</table>";
			}
		?>
</div>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>