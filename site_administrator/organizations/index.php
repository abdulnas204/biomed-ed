<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Delete an organization
	if (isset ($_GET['action']) && isset ($_GET['id']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$organizationCheck = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
		if ($organization = mysql_fetch_array($organizationCheck)) {
			$organizationName = $organization['organization'];
			mysql_query("DELETE FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
			mysql_query("DELETE FROM `users` WHERE `organization` = '{$organizationName}'", $connDBA);
			
			header ("Location: index.php?message=organizationDeleted");
			exit;
		} else {
			header ("Location: index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Organizations"); ?>
<?php headers(); ?>
<script src="../../javascripts/common/warningDelete.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Organizations</h2>
    <p>Below is a list of all organizations registered within this system. Organizations may be sorted according to a certain criteria by clicking on the text in the header   row of the desired column.</p>
<?php
//If the user has assigned someone to an organization
	if (isset($_GET['message']) && $_GET['message'] == "assignedUser") {
		successMessage("The organization was assigned to an organization.");
//A user was created
	} elseif (isset($_GET['message']) && $_GET['message'] == "organizationCreated") {
		successMessage("The organization was created.");	
//A user was edited
	} elseif (isset($_GET['message']) && $_GET['message'] == "organizationEdited") {
		successMessage("The organization was modified.");	
//A user was deleted
	} elseif (isset($_GET['message']) && $_GET['message'] == "organizationDeleted") {
		successMessage("The organization was deleted.");
//If the user is given an error when assigning an administrator to an orgainzation
	} elseif (isset($_GET['message']) && $_GET['message'] == "errorAssign") {
		errorMessage("Site administrators and site managers cannot be assigned to an organization. Please change their role if you wish to assign them.");
//If the user is given an error that an assigned user does not exist
	} elseif (isset($_GET['message']) && $_GET['message'] == "noUser") {
		errorMessage("The user you are attempting to assign to an organization does not exist.");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <div class="toolBar"><a href="manage_organization.php"><img src="../../images/admin_icons/new.png" alt="Add" width="24" height="24" /></a> <a href="manage_organization.php">Add New Organization</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="manage_billing.php"><img src="../../images/admin_icons/dollar_sign.png" alt="Billing" width="24" height="24" /></a> <a href="manage_billing.php">Manage Billing</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="search.php"><img src="../../images/admin_icons/search.png" alt="Search" width="24" height="24" /></a> <a href="search.php">Search for Organizations</a></div>
<br />
<div align="center">
<?php
	if (!isset ($_GET['sort'])) {
		$organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY organization ASC", $connDBA);
	} else {
		switch ($_GET['sort']) {
			case "nameDescending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY organization DESC", $connDBA); break;
			case "nameAscending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY organization ASC", $connDBA); break;
			case "emailDescending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY billingEmail DESC", $connDBA); break;
			case "emailAscending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY billingEmail ASC", $connDBA); break;
			case "phoneDescending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY phone DESC", $connDBA); break;
			case "phoneAscending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY phone ASC", $connDBA); break;
			case "adminDescending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY admin DESC", $connDBA); break;
			case "adminAscending" : $organizationGrabber = mysql_query("SELECT * FROM organizations ORDER BY admin ASC", $connDBA); break;
		}
	}
	
	$organizationCheck = mysql_query("SELECT * FROM organizations ORDER BY organization ASC", $connDBA);
	
	if (mysql_fetch_array($organizationCheck)) {
		$organizations = "valid";
	} else {
		$organizations = "empty";
	}
	
	$organizationNumberGrabber = mysql_query("SELECT * FROM organizations ORDER BY organization ASC", $connDBA);
	$organizationNumber = mysql_num_rows($organizationNumberGrabber);
?>
<?php
//If no organizations exist	
	if (isset ($organizations) && $organizations == "empty") {
		centerDiv("No organizations exist on this system");
		echo "<br /><br/ ><br /><br />";
	} else {
//If organizations exist
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
				<th width=\"150\" class=\"tableHeader\"><strong><a href=\"index.php";
				if (isset ($_GET['sort']) && $_GET['sort'] == "roleAscending") {
					echo "?sort=phoneDescending\" class=\"descending\">Phone</a></strong>";
				} elseif (isset ($_GET['sort']) && $_GET['sort'] == "roleDescending") {
					echo "?sort=phoneAscending\" class=\"ascending\">Phone</a></strong>";
				} else {
					echo "?sort=phoneAscending\" class=\"sortHover\">Phone</a></strong>";
				}
				echo "</th>
				<th class=\"tableHeader\"><strong><a href=\"index.php";
				if (isset ($_GET['sort']) && $_GET['sort'] == "adminAscending") {
					echo "?sort=adminDescending\" class=\"descending\">Administrator</a></strong>";
				} elseif (isset ($_GET['sort']) && $_GET['sort'] == "adminDescending") {
					echo "?sort=adminAscending\" class=\"ascending\">Administrator</a></strong>";
				} else {
					echo "?sort=adminAscending\" class=\"sortHover\">Administrator</a></strong>";
				}
				echo "</th>
				<th width=\"50\" class=\"tableHeader\"><strong>Statistics</strong></th>
				<th width=\"50\" class=\"tableHeader\"><strong>Edit</strong></th>
				<th width=\"50\" class=\"tableHeader\"><strong>Delete</strong></th>
				
			</tr>";
		$number = 1;
		while(($organizationData = mysql_fetch_array($organizationGrabber)) && ($number <= $organizationNumber)) {
			echo "<tr";
			//Alternate the color of each row.
			if ($number++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"200\"> <div align=\"center\"><a href=\"profile.php?id=" . $organizationData['id'] . "\">" . $organizationData['organization'] . "</a></div></td>" . 
			"<td width=\"150\"> <div align=\"center\">";
			if ($organizationData['billingEmail'] == "1") {
				echo "<i>Awaiting Assignment</i>";
			} else {
				echo "<a href=\"mailto:" . $organizationData['billingEmail'] . "\">" . $organizationData['billingEmail'] . "</a>";
			}
			echo "</div></td>" . 
			"<td width=\"150\"> <div align=\"center\">";
			if ($organizationData['phone'] == "1") {
				echo "<i>Awaiting Assignment</i>";
			} else {
				echo "<a href=\"mailto:" . $organizationData['phone'] . "\">" . $organizationData['phone'] . "</a>";
			}	
			echo "</div></td>";
			
			$adminName = explode(" ", $organizationData['admin']);
			$firstName = $adminName[0];
			$lastName = $adminName[1];
			
			$adminGrabber = mysql_query("SELECT * FROM `users` WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
			$admin = mysql_fetch_array($adminGrabber);
			
			echo "<td> <div align=\"center\"><a href=\"../users/profile.php?id=" . $admin['id'] . "\">" . $admin['lastName'] . ", " . $admin['firstName'] . "</a></div></td>" . 
			"<td width=\"50\"> <div align=\"center\"><a href=\"../statistics/index.php?type=organization&number=single&period=overall&id=" . $organizationData['id'] . "\"><img src=\"../../images/admin_icons/statistics.png\" alt=\"Statistics\" onmouseover=\"Tip('View <strong>" . $organizationData['organization'] . "\'s</strong> statistics</strong>')\" onmouseout=\"UnTip()\"></a>" . 
			"<td width=\"50\" align=\"center\"> <div align=\"center\"><a href=\"manage_organization.php?id=" . $organizationData['id'] . "\"><img src=\"../../images/admin_icons/edit.png\" alt=\"Edit\" onmouseover=\"Tip('Edit <strong> " .  $organizationData['organization'] . "</strong>')\" onmouseout=\"UnTip()\"></a>" . 
			"</div></td>" . "<td width=\"50\" align=\"center\"><div align=\"center\"><a href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&id=" . $organizationData['id'] . "', 'organization')\"><img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" onmouseover=\"Tip('Delete <strong>" . $organizationData['organization'] . "</strong>')\" onmouseout=\"UnTip()\"></a>". 
			"</div></td></tr>";
		}
		echo "</tbody>
		</table>";
	}
?>
</div>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>