<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Export data to XML
	$userData = userData();
	
	if (isset($_GET['data'])) {
		headers("Users Data Collection", "Organization Administrator,Site Administrator", false, false, false, false, false, false, false, "XML");
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
	headers("Users", "Organization Administrator,Site Administrator", "liveData", true, false, false, false, false, false, false, "<script type=\"text/javascript\">var dsUsers = new Spry.Data.XMLDataSet(\"index.php?data=xml\", \"root/user\"); var pvUsers = new Spry.Data.PagedView(dsUsers, {pageSize: 20, sortOnLoad: \"name\"}); var pvUsersPagedInfo = pvUsers.getPagingInfo();</script>"); 
	
//Delete a user
	if ($userData['id'] != $_GET['id']) {
		delete("users", "index.php");
	}
	
//Title
	switch($_SESSION['MM_UserGroup']) {
		case "Organization Administrator" :
			$title = "Below is a list of all users registered within this organization. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column."; break;
		case "Site Administrator" :
			$title = "Below is a list of all users registered within this system. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column."; break;
	}
	
	title("Users", $title, true);
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Add New User", "manage_user.php", "toolBarItem new");	
	echo URL("Search for Users", "search.php", "toolBarItem search");
	echo "</div>"; 
	
//Display message updates
	message("inserted", "user", "success", "The user was created");
	message("updated", "user", "success", "The user was modified");
	
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
	//Top navigation toolbar
		navigate("pvUsers", "top");
		
	//The loading state
		echo "<div spry:region=\"pvUsers dsUsers\" spry:loadingstate=\"loadingData\" spry:readystate=\"loaded\">";
		echo "<div spry:state=\"loadingData\" class=\"noResults\">Loading Users...</div>";
		
	//Users table
		echo "<table spry:state=\"loaded\" spry:if=\"{ds_UnfilteredRowCount} > 0\" class=\"dataTable\"><tbody><tr><th width=\"200\" class=\"tableHeader\">" . URL("Name", "javascript:void", "descending", false, false, false, false, false, false, " id=\"name\" spry:sort=\"name\" onclick=\"toggleClass(this.id);\"") . "</th><th width=\"150\" spry:sort=\"emailAddress\" class=\"tableHeader\">Email Address</th><th width=\"175\" spry:sort=\"phone\" class=\"tableHeader\">Role</th><th width=\"200\" spry:sort=\"administrators\" class=\"tableHeader\">Organization</th><th width=\"50\" class=\"tableHeader\">Statistics</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		echo "<tr spry:repeat=\"pvUsers\" spry:odd=\"odd\" spry:even=\"even\">";
		echo "<td width=\"200\">" . URL("{pvUsers::name}", "profile.php?id={pvUsers::id}") . "</td>";
		echo "<td width=\"150\">" . URL("{pvUsers::email}", "../communication/send_email.php?type=user&id={pvUsers::id}") . "</td>";
		echo "<td width=\"175\">{pvUsers::role}</td>";
		echo "<td width=\"200\">{function::formatLine}</td>";
		echo "<td width=\"50\">" . URL(false,"../statistics/index.php?type=user&period=overall&id={pvUsers::id}", "action statistics", false, "View <strong>{pvUsers::firstName} {pvUsers::lastName}\'s</strong> statistics") . "</td>";
		echo "<td width=\"50\">" . URL(false, "manage_user.php?id={pvUsers::id}", "action edit", false, "Edit <strong>{pvUsers::firstName} {pvUsers::lastName}</strong>") . "</td>";
		echo "<td width=\"50\">{function::noDelete}</td>";
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