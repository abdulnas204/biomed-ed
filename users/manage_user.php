<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the user name exists
	validateName("users", "userName");
	
//Check to see if the user is being edited
	if (isset ($_GET['id'])) {
		if ($user = exist("users", "id", $_GET['id'])) {
			$userData = userData();
		} else {
			redirect("index.php");
		}
	}
	
	if (isset($user)) {
		$title = "Edit " . prepare($user['firstName'], true) . " " . prepare($user['lastName'], true);
		$description = "Modify " . prepare($user['firstName'], true) . " " . prepare($user['lastName']) . "'s information information below.";
	} else {
		$title =  "Create a New User";
		$description = "Create a new user by filling in the information below.";
	}
	
	headers($title, "Organization Administrator,Site Administrator", "tinyMCEAdvanced,validate", true);
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['role']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['primaryEmail'])) {
		$currentUser = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}'");
		$role = $_POST['role'];
		$firstName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['firstName']));
		$lastName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['lastName']));
		$changePassword = $_POST['changePassword'];
		$primaryEmail = $_POST['primaryEmail'];
		$secondaryEmail = $_POST['secondaryEmail'];
		$tertiaryEmail = $_POST['tertiaryEmail'];
		
		if (!access("manageAllUsers")) {
			$allowedArray = array("Student", "Instructorial Assisstant", "Instructor", "Administrative Assistant", "Organization Administrator");
			
			if (!in_array($role, $allowedArray)) {
				redirect($_SERVER['REQUEST_URI']);
			}
		}
		
		if (isset ($user)) {
			if ($_SESSION['MM_UserGroup'] == "Organization Administrator") {
				$sql = " AND `organization` = '{$userData['organization']}'";
			} else {
				$sql = "";
			}
			
			if ($administrators = query("SELECT * FROM `users` WHERE `role` = '{$_SESSION['MM_UserGroup']}'{$sql}", "num") && $administrators == "1" && $userData['id'] == $_GET['id'] && ($_SESSION['MM_UserGroup'] == "Organization Administrator" || $_SESSION['MM_UserGroup'] == "Site Administrator") && $role != $_SESSION['MM_UserGroup']) {
				redirect($_SERVER['REQUEST_URI'] . "&error=noAdmin");
			}
		}
		
		if (isset($user) && $userData['id'] == $_GET['id']) {
			$userName = $userData['userName'];
		} else {
			$userName = $_POST['userName'];
		}
		
		if (query("SELECT * FROM `users` WHERE `userName` = '{$userName}'")) {
			if ($currentUser['userName'] !== $userName) {
				if (isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "&error=identical");
				} else {
					redirect($_SERVER['PHP_SELF'] . "?error=identical");
				}
			}
		}
		
		if (!isset($user)) {
			if (!empty($_POST['password'])) {
				$password = $_POST['password'];
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
			if (!empty($_POST['password'])) {
				$password = $_POST['password'];
			} else {
				$checkPassWord = query("SELECT * FROM `users` WHERE id = '{$_GET['id']}'");
				$password = $checkPassWord['passWord'];
			}
		}
		
		if (!access("manageAllUsers")) {
			$userData = userData();
			$organization = $userData['organization'];
		} else {
			if (!empty($_POST['organization']) && $role != "Site Administrator" && $role != "Site Manager") {
				$organizationPrep = mysql_real_escape_string($_POST['organization']);
				$organizationData = query("SELECT * FROM `organizations` WHERE `organization` = {$organizationPrep}");
				$organization = $organizationData['id'];
			} else {
				$organization = "0";
			}
		}
		
		if (!access("manageAllUsers")) {
			if (!empty($_POST['staffID']) && !empty($_POST['phoneWork']) && !empty($_POST['phoneHome']) && !empty($_POST['workLocation']) && !empty($_POST['jobTitle']) && !empty($_POST['department']) && !empty($_POST['departmentID'])) {
				$staffID = $_POST['staffID'];
				$phoneWork = $_POST['phoneWork'];
				$phoneHome = $_POST['phoneHome'];
				$phoneMobile = $_POST['phoneMobile'];
				$phoneFax = $_POST['phoneFax'];
				$workLocation = $_POST['workLocation'];
				$jobTitle = $_POST['jobTitle'];
				$department = $_POST['department'];
				$departmentID = $_POST['departmentID'];
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
			if (isset ($user)) {
				$staffID = $currentUser['staffID'];
				$phoneWork = $currentUser['phoneWork'];
				$phoneHome = $currentUser['phoneHome'];
				$phoneMobile = $currentUser['phoneMobile'];
				$phoneFax = $currentUser['phoneFax'];
				$workLocation = $currentUser['workLocation'];
				$jobTitle = $currentUser['jobTitle'];
				$department = $currentUser['department'];
				$departmentID = $currentUser['departmentID'];
			} else {
				$staffID = "";
				$phoneWork = "";
				$phoneHome = "";
				$phoneMobile = "";
				$phoneFax = "";
				$workLocation = "";
				$jobTitle = "";
				$department = "";
				$departmentID = "";
			}
		}
		
		if (isset ($user)) {			
			query("UPDATE `users` SET `staffID` = '{$staffID}', `firstName` = '{$firstName}', `lastName` = '{$lastName}', `userName` = '{$userName}', `password` = '{$password}', `changePassword` = '{$changePassword}', `emailAddress1` = '{$primaryEmail}', `emailAddress2` = '{$secondaryEmail}', `emailAddress3` = '{$tertiaryEmail}', `phoneWork` = '{$phoneWork}', `phoneHome` = '{$phoneHome}', `phoneMobile` = '{$phoneMobile}', `phoneFax` = '{$phoneFax}', `workLocation` = '{$workLocation}', `jobTitle` = '{$jobTitle}', `department` = '{$department}', `departmentID` = '{$departmentID}', `role` = '{$role}', `organization` = '{$organization}' WHERE `id` = '{$_GET['id']}'");
			
			if ($userData['id'] == $_GET['id'] && $role != $_SESSION['MM_UserGroup']) {
				$_SESSION['MM_UserGroup'] = $role;
				redirect("../portal/index.php");
			}
			
			redirect("index.php?updated=user");
		} else {
			query("INSERT INTO `users` (
					  `id`, `locked`, `active`, `staffID`, `firstName`, `lastName`, `userName`, `password`, `changePassword`, `emailAddress1`, `emailAddress2`, `emailAddress3`, `phoneWork`, `phoneHome`, `phoneMobile`, `phoneFax`, `workLocation`, `jobTitle`, `department`, `departmentID`, `role`, `organization`, `modules`
				  ) VALUES (
					  NULL, '', '', '{$staffID}', '{$firstName}', '{$lastName}', '{$userName}', '{$password}', '{$changePassword}', '{$primaryEmail}', '{$secondaryEmail}', '{$tertiaryEmail}', '{$phoneWork}', '{$phoneHome}', '{$phoneMobile}', '{$phoneFax}', '{$workLocation}', '{$jobTitle}', '{$department}', '{$departmentID}', '{$role}', '{$organization}', ''
				  )");
			
			redirect("index.php?inserted=user");
		}
	}

//Title
	if (!isset($_GET['error'])) {
		title($title, $description);
	} else {
		title($title, $description, false);
	}
	
//Display message updates
	message("error", "identical", "error", "A user with this user name already exists.");
	message("error", "noAdmin", "error", "Your profile was not updated since you tried to change your role, and thus would have left this site without an administrator.");
	
//Users form
	form("users");
	catDivider("Role", "one", true);
	echo "<blockquote>";
	directions("Select a role for this user", true);
	echo "<blockquote><p>";
	
	if (!access("manageAllUsers")) {
		$additionalRoles = "";
	} else  {
		$additionalRoles = ",Site Manager,Site Administrator";
	}
	
	
	dropDown("role", "role", "- Select -,Student,Instructorial Assisstant,Instructor,Administrative Assistant,Organization Administrator" . $additionalRoles, ",Student,Instructorial Assisstant,Instructor,Administrative Assistant,Organization Administrator" . $additionalRoles, false, true, false, false, "user", "role");
	echo "</p></blockquote></blockquote>";
	
	catDivider("User Information", "two");
	echo "<blockquote>";
	directions("First Name", true);
	echo "<blockquote><p>";
	textField("firstName", "firstName", false, false, false, true, ",custom[noSpecialCharacters]", false, "user", "firstName");
	echo "</p></blockquote>";
	directions("Last Name", true);
	echo "<blockquote><p>";
	textField("lastName", "lastName", false, false, false, true, ",custom[noSpecialCharacters]", false, "user", "lastName");
	echo "</p></blockquote>";
	
	if (isset($user) && $userData['id'] == $_GET['id']) {
		$disabled = " disabled=\"disabled\"";
	} else {
		$disabled = "";
	}
	
	directions("User Name", true);
	echo "<blockquote><p>";
	textField("userName", "userName", false, false, false, true, ",custom[noSpecialCharacters],length[6,30],ajax[ajaxName]", false, "user", "userName", $disabled);
	echo "</p></blockquote>";
	
	if (isset($user)) {
		if ($user['userName'] != $_SESSION['MM_Username']) {
			$change = true;
		} else {
			$change = false;
		}
		
		$require = false;
	} else {
		$require = true;
		$change = true;
	}
	
	directions("Password", $require);
	echo "<blockquote><p>";
	textField("password", "password", false, false, true, $require, ",length[6,30]");
	
	if ($change == true) {
		echo " ";
		checkbox("changePassword", "changePassword", "Force Password Change", false, false, false, false, "user", "changePassword", "on");
	}
	
	echo "</p></blockquote></blockquote>";
	
	catDivider("Contact Information", "three");
	echo "<blockquote>";
	directions("Primary Email Address", true);
	echo "<blockquote><p>";
	textField("primaryEmail", "primaryEmail", false, false, false, true, ",custom[email]", false, "user", "emailAddress1");
	echo "</p></blockquote>";
	directions("Secondary Email Address", false);
	echo "<blockquote><p>";
	textField("secondaryEmail", "secondaryEmail", false, false, false, false, ",custom[email]", false, "user", "emailAddress2");
	echo "</p></blockquote>";
	directions("Tertiary Email Address", false);
	echo "<blockquote><p>";
	textField("tertiaryEmail", "tertiaryEmail", false, false, false, false, ",custom[email]", false, "user", "emailAddress3");
	echo "</p></blockquote>";
	
	if (!access("manageAllUsers")) {
		directions("Work Telephone", true);
		echo "<blockquote><p>";
		textField("phoneWork", "phoneWork", false, false, false, true, ",custom[telephone]", false, "user", "phoneWork");
		echo "</p></blockquote>";
		directions("Home Telephone", true);
		echo "<blockquote><p>";
		textField("phoneHome", "phoneHome", false, false, false, true, ",custom[telephone]", false, "user", "phoneHome");
		echo "</p></blockquote>";
		directions("Mobile Telephone", false);
		echo "<blockquote><p>";
		textField("phoneMobile", "phoneMobile", false, false, false, false, ",custom[telephone]", false, "user", "phoneMobile");
		echo "</p></blockquote>";
		directions("Fax", false);
		echo "<blockquote><p>";
		textField("phoneFax", "phoneFax", false, false, false, false, ",custom[telephone]", false, "user", "phoneFax");
		echo "</p></blockquote></blockquote>";
		
		catDivider("Workplace Information", "four");
		echo "<blockquote>";
		directions("Staff ID", true);
		echo "<blockquote><p>";
		textField("staffID", "staffID", false, false, false, true, false, false, "user", "staffID");
		echo "</p></blockquote>";
		directions("Work Location", true);
		echo "<blockquote><p>";
		textField("workLocation", "workLocation", false, false, false, true, false, false, "user", "workLocation");
		echo "</p></blockquote>";
		directions("Job Title", true);
		echo "<blockquote><p>";
		textField("jobTitle", "jobTitle", false, false, false, true, false, false, "user", "jobTitle");
		echo "</p></blockquote>";
		directions("Department", true);
		echo "<blockquote><p>";
		textField("department", "department", false, false, false, true, false, false, "user", "department");
		echo "</p></blockquote>";
		directions("Department ID", true);
		echo "<blockquote><p>";
		textField("departmentID", "departmentID", false, false, false, true, false, false, "user", "departmentID");
		echo "</p></blockquote>";
	}
	
	echo "</blockquote>";
	
	if (access("manageAllUsers")) {
		$organizationGrabber = query("SELECT * FROM `organizations`", "raw");
		$organizationValuesPrep = "- None -,";
		$organizationIDsPrep = ",";
		
		while ($organization = mysql_fetch_array($organizationGrabber)) {
			$organizationValuesPrep .= $organization['organization'] . ",";
			$organizationIDsPrep .= $organization['id'] . ",";
		}
		
		$organizationValues = rtrim($organizationValuesPrep, ",");
		$organizationIDs = rtrim($organizationIDsPrep, ",");
		
		catDivider("Organization", "four");
		echo "<blockquote>";
		directions("Assign this user to an organization", true);
		echo "<blockquote><p>";
		dropDown("organization", "organization", $organizationValues, $organizationIDs, false, false, false, false, "user", "organization");
		echo "</p></blockquote></blockquote>";
	}
	
	catDivider("Submit", "five");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "index.php");
	echo "</p>";
	closeForm(true, true);

//Include the footer
	footer();
?>