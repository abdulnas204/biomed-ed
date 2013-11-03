<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Export data to XML
	$userData = userData();
	
	if (isset($_GET['data'])) {
		headers("Users Data Collection", "Instructor,Organization Administrator,Site Administrator", false, false, false, false, false, false, false, "XML");
		header("Content-type: text/xml");
		
		if (!access("manageAllUsers")) {
			$sql = " WHERE `organization` = '{$userData['organization']}'";
		} else {
			$sql = "";
		}
		
		$usersGrabber = query("SELECT * FROM `users`{$sql}", "raw");
		echo "<root>";
		
		while ($users = mysql_fetch_array($usersGrabber)) {
			echo "<user>";
			echo "<id>" . $users['id'] . "</id>";
			echo "<name>" . prepare($users['lastName'], false, true) . ", " . prepare($users['firstName'], false, true) . "</name>";
			echo "<firstName>" . prepare($users['firstName'], false, true) . "</firstName>";
			echo "<lastName>" . prepare($users['lastName'], false, true) . "</lastName>";
			echo "<email>" . $users['emailAddress1'] . "</email>";
			echo "<role>" . $users['role'] . "</role>";
			
			if ($users['organization'] != "0") {
				$organzation = query("SELECT * FROM `organizations` WHERE `id` = '{$users['organization']}'");
				echo "<organization>" . prepare($organzation['organization'], false, true) . "</organization>";
				echo "<organizationID>" . $users['organization'] . "</organizationID>";
			} else {
				echo "<organization>None</organization>";
				echo "<organizationID>0</organizationID>";
			}
			
			echo "</user>";
		}
		
		echo "</root>";
		exit;
	}
	
//Top content
	headers("Users", "Instructor,Organization Administrator,Site Administrator", "liveData", true, false, false, false, false, false, false, "<script type=\"text/javascript\">var dsUsers = new Spry.Data.XMLDataSet(\"index.php?data=xml\", \"root/user\"); var pvUsers = new Spry.Data.PagedView(dsUsers, {pageSize: 20, sortOnLoad: \"name\"}); var pvUsersPagedInfo = pvUsers.getPagingInfo();</script>"); 
	
//Delete a user
	if (isset($_GET['id']) && $userData['id'] != $_GET['id']) {
		$currentUser = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}'");
		
		if ($currentUser['role'] == "Organization Administrator") {
			$organizationData = query("SELECT * FROM `users` WHERE `organization` = '{$currentUser['organization']}' AND `role` = 'Organization Administrator'", "num");
			
			if ($organizationData > 1) {
				$adminStripGrabber = query("SELECT * FROM `organizations` WHERE `id` = '{$currentUser['organization']}'");
				$adminStrip = str_replace($currentUser['id'] . ",", "", $adminStripGrabber['admin']);
				query("UPDATE `organizations` SET `admin` = '{$adminStrip}' WHERE `id` = '{$currentUser['organization']}'");
			} else {
				redirect($_SERVER['PHP_SELF'] . "?error=noAdmin");
			}
		}
		
		delete("users", "index.php");
	}
	
//Title
	switch($_SESSION['MM_UserGroup']) {
		case "Site Administrator" :
			$title = "Below is a list of all users registered within this system. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column."; break;
		case "Organization Administrator" :
			$title = "Below is a list of all users registered within this organization. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column."; break;
		case "Instructor" :
			$title = "Below is a list of all instructors and students registered within this organization. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column."; break;
	}
	
	title("Users", $title, true);
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	
	if (access("manageOrganizationUsers")) {
		echo URL("Add New User", "manage_user.php", "toolBarItem new");
	}
	
	if (access("manageOrganizationGroups")) {
		echo URL("Manage Groups", "group.php", "toolBarItem user");
	}
	
	if (access("viewOrganizationGroups")) {
		echo URL("View Groups", "group.php", "toolBarItem user");
	}
	
	echo URL("Search for Users", "search.php", "toolBarItem search");
	echo "</div>"; 
	
//Display message updates
	message("inserted", "user", "success", "The user was created");
	message("updated", "user", "success", "The user was modified");
	message("error", "noAdmin", "error", "This user could not be deleted, since it would have their organization without an administrator.");
	
//Users table
	if (!access("manageAllUsers")) {
		$userData = userData();
		$column = "organization";
		$id = $userData['organization'];
	} else {
		$column = false;
		$id = false;
	}
	
	if (exist("users", $column, $id)) {
	//Custom table formatting for differnent roles
		if (access("manageOrganizationUsers")) {
			$emailWidth = "300";
			$roleWidth = "175";
		} else {
			$emailWidth = "33%";
			$roleWidth = "33%";
		}
		
	//Top navigation toolbar
		navigate("pvUsers", "top");
		
	//The loading state
		echo "<div spry:region=\"pvUsers dsUsers\" spry:loadingstate=\"loadingData\" spry:readystate=\"loaded\">";
		echo "<div spry:state=\"loadingData\" class=\"noResults\">Loading Users...</div>";
		
	//Users table
		echo "<table spry:state=\"loaded\" spry:if=\"{ds_UnfilteredRowCount} > 0\" class=\"dataTable\"><tr><th class=\"tableHeader\">" . URL("Name", "javascript:void", "descending", false, false, false, false, false, false, " id=\"name\" spry:sort=\"name\" onclick=\"toggleClass(this.id);\"") . "</th><th width=\"" . $emailWdith . "\" spry:sort=\"email\" class=\"tableHeader\">Email Address</th><th width=\"225\" spry:sort=\"role\" class=\"tableHeader\" width=\"" . $roleWidth . "\">Role</th>";
		
		if (access("manageAllOrganizations")) {
			echo "<th width=\"200\" spry:sort=\"organization\" class=\"tableHeader\">Organization</th>";
		}
		
		//echo "<th width=\"50\" class=\"tableHeader\">Statistics</th>";
		
		if (access("manageOrganizationUsers")) {
			echo "<th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		}
		
		echo "<tr spry:repeat=\"pvUsers\" spry:odd=\"odd\" spry:even=\"even\">";
		echo "<td>" . URL("{pvUsers::name}", "profile.php?id={pvUsers::id}") . "</td>";
		echo "<td width=\"" . $emailWdith . "\">" . URL("{pvUsers::email}", "../communication/send_email.php?type=users&id={pvUsers::id}") . "</td>";
		echo "<td width=\"" . $roleWidth . "\">{pvUsers::role}</td>";
		
		if (access("manageAllOrganizations")) {
			echo "<td width=\"200\">{function::formatLine}</td>";
		}
		
		//echo "<td width=\"50\">" . URL(false,"../statistics/index.php?type=user&period=overall&id={pvUsers::id}", "action statistics", false, "View <strong>{pvUsers::firstName} {pvUsers::lastName}\'s</strong> statistics") . "</td>";
		
		if (access("manageOrganizationUsers")) {
			echo "<td width=\"50\">" . URL(false, "manage_user.php?id={pvUsers::id}", "action edit", false, "Edit <strong>{pvUsers::firstName} {pvUsers::lastName}</strong>") . "</td>";
			echo "<td width=\"50\">{function::noDelete}</td>";
		}
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		
	//Bottom navigation toolbar
		navigate("pvUsers", "bottom");
	} else {
		echo "<div class=\"noResults\">No users exist.</div>";
	}

//Include the footer
	footer();
?>