<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the organization is being edited
	if (isset ($_GET['id'])) {
		if ($organizationData = exist("organizations", "id", $_GET['id'])) {
			//Do nothing
		} else {
			redirect("index.php");
		}
	}
	
	if (isset($pageData)) {
		$title = "Edit the " . prepare($organizationData['organization'], true) . " Organization";
	} else {
		$title =  "Create a New Organization";
	}
	
	headers($title, "Site Administrator", "tinyMCEAdvanced,validate,optionTransfer", true, " onload=\"opt.init(document.forms[0])\"");
	
//Find the list of administrators, and potential users
	$potentialValuesPrep = "";
	$potentialIDsPrep = "";
	$adminValuesPrep = "";
	$adminIDsPrep = "";
	$potentialUserGrabber = query("SELECT * FROM `users` WHERE `role` != 'Site Administrator' OR 'Site Manager' ORDER BY `lastName` ASC", "raw");
	$adminGrabber = query("SELECT * FROM `users` WHERE `role` != 'Site Administrator' OR 'Site Manager' ORDER BY `lastName` ASC", "raw");
		  
	if (!isset($_GET['id'])) {
		while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
			if ($potentialUser['role'] != "Organization Administrator" && !empty($potentialUser['id'])) {
				$potentialValuesPrep .= $potentialUser['firstName'] . " " . $potentialUser['lastName'] . ",";
				$potentialIDsPrep .= $potentialUser['id'] . ",";
			}
		}
	} else {
		$organization = query("SELECT * FROM `organizations` WHERE `id` = '{$_GET['id']}'");
		$currentAdministrator = explode(",", $organization['admin']);
		
		while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
			if (!in_array($potentialUser['id'], $currentAdministrator)) {
				if ($potentialUser['role'] != "Organization Administrator" && empty($potentialUser['id'])) {
					$potentialValuesPrep .= $potentialUser['firstName'] . " " . $potentialUser['lastName'] . ",";
					$potentialIDsPrep .= $potentialUser['id'] . ",";
				}
			}
		}
	}

	if (isset($_GET['id'])) {
		$organization = query("SELECT * FROM `organizations` WHERE `id` = '{$_GET['id']}'");
		$currentAdministrator = explode(",", $organization['admin']);
		
		while ($admin = mysql_fetch_array($adminGrabber)) {
			if (in_array($potentialUser['id'], $currentAdministrator)) {
				$adminValuesPrep .= $admin['firstName'] . " " . $admin['lastName'] . ",";
				$adminIDsPrep .= $admin['id'] . ",";
			}
		}
	}
	
	$potentialValues = rtrim($potentialValuesPrep, ",");
	$potentialIDs = rtrim($potentialIDsPrep, ",");
	$adminValues = rtrim($adminValuesPrep, ",");
	$adminIDs = rtrim($adminIDsPrep, ",");

//Process the form
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['admin'])) {
		$organization = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
		$admin = $_POST['toImport'];
		
		if (exist("organizations", "organization", "organization")) {
			if (isset($_GET['id'])) {
				$organizationID = $_GET['id'];
				$currentOrganizationGrabber = query("SELECT * FROM `organizations` WHERE `id` = '{$organizationID}'", "raw");
				$currentOrganization = mysql_fetch_array($currentOrganizationGrabber);
				
				if (strtolower($currentOrganization['organization']) != strtolower($organization)) {
					redirect("manage_organization.php?id=" . $id . "&error=identical");
				}
				
				$oldData = query("SELECT * FROM `organizations` WHERE `id` = '{$id}'");
				$oldName = $oldData['organization'];
				$oldAdmin = explode(",", $oldData['admin']);
				$newAdmin = explode(",", $admin);
				
				foreach ($newAdmin as $userID) {
					mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$id}' WHERE `id` = '{$userID}'", $connDBA);
				}
			} else {
				redirect("manage_organization.php?error=identical");
			}
		}
		
		if (isset($id)) {			
			query("UPDATE `organizations` SET `organization` =  '{$organization}', `admin` = '{$admin}' WHERE `id` = '{$id}'");
			
			foreach ($oldAdmin as $oldAdmin) {
				if (!in_array($oldAdmin, $newAdmin)) {
					mysql_query("UPDATE `users` SET `role` = 'Student' WHERE `id` = '{$oldAdmin}'", $connDBA);
				}
			}
			
			redirect("index.php?updated=organization");
		} else {
			query("INSERT INTO organizations (
					  `id`, `organization`, `organizationID`, `admin`, `type`, `webSite`, `phone`, `mailingAddress1`, `mailingAddress2`, `mailingCity`, `mailingState`, `mailingZIP`, `billingAddress1`, `billingAddress2`, `billingCity`, `billingState`, `billingZIP`, `billingPhone`, `billingFax`, `billingEmail`, `contractStart`, `contractEnd`, `contractAgreement`, `active`, `timeZone`
				  ) VALUES (
					  NULL, '{$organization}', '', '{$admin}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', ''
				  )");
			
			redirect("index.php?inserted=organization");
		}
	}

//Title
	title($title, "Organizations can be managed by filling in the information below. The organization's complete details and payment method will be setup when the organization administrator first logs in.");

//Organization form	
	form("manageOrganization");
	catDivider("Organization Name", "one", true);
	echo "<blockquote>";
	directions("Assign the organization name", true);
	echo "<blockquote><p>";
	textField("name", "name", false, false, false, true, ",ajax[checkName]", false, "organizationData", "organization");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Assign Administrator", "two");
	echo "<blockquote>";
	directions("Assign the organization an administrator", true);
	echo "<blockquote><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential users:</h3><div class=\"collapseElement\">";
	textField("placeHolder", "placeHolder");
	echo "</div><div align=\"center\">";
	dropDown("notToList", "notToList", $potentialValues, $potentialIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferRight()\"");
	echo "<br /><br />";
	button("right", "right", "&gt;&gt;", "button", false, " onclick=\"opt.transferRight()\"");
	echo "</div></div><div class=\"halfRight\"><h3>Selected users:</h3><div class=\"collapseElement\">";
	textField("toImport", "toImport", false, false, false, true, false, false, "organizationData", "admin", " readonly=\"readonly\"");
	echo "</div><div align=\"center\">";
	dropDown("toList", "toList", $adminValues, $adminIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferLeft()\"");
	echo "<br /><br />";
	button("left", "left", "&lt;&lt;", "button", false, " onclick=\"opt.transferLeft()\"");
	echo "</div></div></div> </blockquote></blockquote>";
	
	catDivider("Submit", "three");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "index.php");
	echo "</p>";
	closeForm(true, true);

//Include the footer
	footer();
?>