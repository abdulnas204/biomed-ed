<?php
/*
LICENSE: See "license.php" located at the root installation

This is the overview page for the users and user groups.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
//Export data to XML
	if (isset($_GET['data'])) {		
		if (!access("Manage All Users")) {
			$sql = " WHERE `organization` = '{$userData['organization']}'";
		} else {
			$sql = "";
		}
		
		$usersGrabber = query("SELECT * FROM `users`{$sql}", "raw");
		$return = array();
		
		while ($users = fetch($usersGrabber)) {
			if ($users['organization'] != "0") {
				$organzationData = query("SELECT * FROM `organizations` WHERE `id` = '{$users['organization']}'");
				$organization = $organzationData['organization'];
			} else {
				$organization = "0";
			}
			
			array_push($return, array("id" => $users['id'], "name" => $users['lastName'] . ", " . $users['firstName'], "firstName" => $users['firstName'], "lastName" => $users['lastName'], "email" => $users['emailAddress1'], "role" => $users['role'], "organization" => $organization, "organizationID" => $users['organization']));
		}
		
		echo json_encode($return);
		exit;
	}
	
//Top content
	headers("Users", "liveData", true, false, false, false, "<script type=\"text/javascript\" src=\"../system/javascripts/common/jquery.pagination.js\"></script>
	
<script type=\"text/javascript\">
  var dsUsers = new Spry.Data.XMLDataSet(\"index.htm?data=xml\", \"root/user\"); 
  var pvUsers = new Spry.Data.PagedView(dsUsers, {pageSize: 20, sortOnLoad: \"name\"});
  var pvUsersPagedInfo = pvUsers.getPagingInfo();
  
  $(document).ready(function() {
	  $('#users').pagination('index.htm?data=json', {loadingHTML : 'Loading users...', sortBy : 'id', displayMax : 10});
  });
</script>"); 
	
//Delete a user
	delete("users", "index.php");
	
//Title	
	title("Users", "Below is a list of all registered users. Users may be sorted according to a certain criteria by clicking on the text in the header row of the desired column.", true);
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo toolBarURL("Add New User", "manage_user.php", "toolBarItem new", false, "Create New User");
	echo toolBarURL("Manage Groups", "group.php", "toolBarItem user", false, "View Groups");
	echo toolBarURL("Search for Users", "javascript:void();", "toolBarItem search", false, "View Users");
	echo "</div>\n";
	
//Display message updates
	message("inserted", "user", "success", "The user was created");
	message("updated", "user", "success", "The user was modified");
	message("error", "noAdmin", "error", "This user could not be deleted, since it would have their organization without an administrator.");

/*	
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
		if (access("Manage All Users")) {
			$emailWidth = "300";
			$roleWidth = "175";
		} else {
			$emailWidth = "33%";
			$roleWidth = "33%";
		}
		
	//Top navigation toolbar
		navigate("pvUsers", "top");
		
	//The loading state
		echo "\n<div spry:region=\"pvUsers dsUsers\" spry:loadingstate=\"loadingData\" spry:readystate=\"loaded\">\n";
		echo "<div spry:state=\"loadingData\" class=\"noResults\">Loading Users...</div>\n";
		
	//Users table
		echo "<table spry:state=\"loaded\" spry:if=\"{ds_UnfilteredRowCount} > 0\" class=\"dataTable\">\n";
		echo "<tr>\n";
		echo column(URL("Name", "javascript:void", "descending", false, false, false, false, false, false, " id=\"name\" spry:sort=\"name\""));
		echo column(URL("Email Address", "javascript:void", "sortHover", false, false, false, false, false, false, " id=\"name\" spry:sort=\"email\""), $emailWidth);
		echo column(URL("Role", "javascript:void", "sortHover", false, false, false, false, false, false, " id=\"name\" spry:sort=\"role\""), $roleWidth);
		
		if (access("Manage All Users")) {
			echo column(URL("Organization", "javascript:void", "sortHover", false, false, false, false, false, false, " id=\"name\" spry:sort=\"organization\" onclick=\"toggleClass(this.id);\""), "200");
		}
		
		if (access("Edit User")) {
			echo column("Edit", "50");
		}
		
		if (access("Delete User")) {
			echo column("Delete", "50");
		}
		
		echo "</tr>\n";
		
		echo "<tr spry:repeat=\"pvUsers\" spry:odd=\"odd\" spry:even=\"even\">";
		echo cell(URL("{pvUsers::name}", "profile.php?id={pvUsers::id}"));
		echo cell(URL("{pvUsers::email}", "../communication/send_email.php?type=users&id={pvUsers::id}"), $emailWidth);
		echo cell("{pvUsers::role}", $roleWidth);
		
		if (access("Manage All Users")) {
			echo cell("{function::formatLine}");
		}
		
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
*/

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
		if (access("Manage All Users")) {
			$emailWidth = "300";
			$roleWidth = "200";
		} else {
			$emailWidth = "33%";
			$roleWidth = "33%";
		}
		
	//Users table
		echo "<table class=\"dataTable\" id=\"users\">\n";
		echo "<tr>\n";
		echo column("<span id=\"name\">Name</span>");
		echo column("<span id=\"email\">Email Address</span>", $emailWidth);
		echo column("<span id=\"role\">Role</span>", $roleWidth);
		
		if (access("Manage All Users")) {
			echo column("<span id=\"organization\">Organization</span>", "200");
		}
		
		if (access("Edit User")) {
			echo column("Edit", "50");
		}
		
		if (access("Delete User")) {
			echo column("Delete", "50");
		}
		
		echo "</tr>\n";
		
		echo "<tr>\n";
		echo cell(URL("{name}", "profile.php?id={id}"));
		echo cell(URL("{email}", "../communication/send_email.php?type=users&id={id}"), $emailWidth);
		echo cell("{role}", $roleWidth);
		
		if (access("Manage All Users")) {
			echo cell("<span id=\"modifyOrganization\">{organization}</span>", "250");
		}
		
		if (access("Manage All Users")) {
			echo cell(URL(false, "manage_user.php?id={id}", "action edit", false, "Edit <strong>{firstName} {lastName}</strong>"), "50");
			echo cell("<span id=\"delete\">" . URL(false, "index.php?id={id}&action=delete", "action delete", false, "Delete <strong>{firstName} {lastName}</strong>", true) . "</span>", "50");
		}
		echo "</tr>\n";
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">No users exist.</div>";
	}
	
//Search dialog
	if (access("View Users")) {
		echo "<div id=\"searchDialog\" title=\"Search for Users\" class=\"contentHide\">\n";
		echo "<table>\n";
		echo "<tr>\n";
		echo cell("Keywords:", "100");
		echo cell(textField("keywords", "keywords"));
		echo "</tr>\n";
		echo "<tr>\n";
		echo cell("Criteria:", "100");
		echo cell(dropDown("criteria", "criteria", "Name,Email Address,Role,Organization", "name,email,role,organization"));
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
	}

//Include the footer
	footer();
?>