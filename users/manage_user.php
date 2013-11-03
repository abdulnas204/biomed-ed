<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the user name exists
	validateName("users", "userName");
	
//Generate XML data for user suggestions
	if (isset($_GET['data'])) {
		headers("Organizations Data Collection", "Organization Administrator,Site Administrator", false, false, false, false, false, false, false, "XML");
		header("Content-type: text/xml");
		$userData = userData();
		
		if ($userData['organization'] == "0") {
			$sql = "";
		} else {
			$sql = " WHERE `organization` = '{$userData['organization']}'";
		}
		
		$userGrabber = query("SELECT * FROM `users`{$sql}", "raw");
		$limit = array(array(), array(), array(), array());
		
		echo "<root>";
		
		while ($users = mysql_fetch_array($userGrabber)) {
			echo "<user>";
			
			if (!in_array($users['workLocation'], $limit['0'])) {
				echo "<location>" . $users['workLocation'] . "</location>";
			}
			
			if (!in_array($users['jobTitle'], $limit['1'])) {
				echo "<jobTitle>" . $users['jobTitle'] . "</jobTitle>";
			}
			
			if (!in_array($users['department'], $limit['2'])) {
				echo "<department>" . $users['department'] . "</department>";
			}
			
			if (!in_array($departmentID['jobTitle'], $limit['3'])) {
				echo "<departmentID>" . $users['departmentID'] . "</departmentID>";
			}
			
			echo "</user>";
			
			array_push($limit['0'], $users['workLocation']);
			array_push($limit['1'], $users['jobTitle']);
			array_push($limit['2'], $users['department']);
			array_push($limit['3'], $users['departmentID']);
		}
		
		echo "</root>";
		exit;
	}
	
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
	
	headers($title, "Organization Administrator,Site Administrator", "tinyMCEAdvanced,validate,autoSuggest,showHide", true, false, false, false, false, false, false, "<script type=\"text/javascript\">var data = new Spry.Data.XMLDataSet(\"manage_user.php?data=xml\", \"/root/user\");</script>");
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['role']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['primaryEmail'])) {		
		if (!access("manageAllUsers") && !empty($_POST['staffID']) && !empty($_POST['phoneWork']) && !empty($_POST['workLocation']) && !empty($_POST['jobTitle']) && !empty($_POST['department']) && !empty($_POST['departmentID'])) {
			//Continue to processor, all form fields are complete
		} elseif (access("manageAllUsers")) {
			//Continue to processor, all necessary form fields are complete
		} else {
			redirect($_SERVER['REQUEST_URI']);
		}
		
		$currentUser = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}'");
		$role = $_POST['role'];
		$firstName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['firstName']));
		$lastName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['lastName']));
		$changePassword = $_POST['changePassword'];
		$primaryEmail = $_POST['primaryEmail'];
		$secondaryEmail = $_POST['secondaryEmail'];
		$tertiaryEmail = $_POST['tertiaryEmail'];
		$staffID = $_POST['staffID'];
		$phoneWork = $_POST['phoneWork'];
		$phoneFax = $_POST['phoneFax'];
		$phonePager = $_POST['phonePager'];
		$phoneMobile = $_POST['phoneMobile'];
		$phoneHome = $_POST['phoneHome'];
		$workLocation = $_POST['workLocation'];
		$jobTitle = $_POST['jobTitle'];
		$department = $_POST['department'];
		$departmentID = $_POST['departmentID'];
		
		if (!access("manageAllUsers")) {
			$allowedArray = array("Student", "Instructorial Assisstant", "Instructor", "Administrative Assistant", "Organization Administrator");
			
			if (!in_array($role, $allowedArray)) {
				redirect($_SERVER['REQUEST_URI']);
			}
			
			$currentUser = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}'");
		
			if ($currentUser['role'] == "Organization Administrator") {
				$organizationData = query("SELECT * FROM `users` WHERE `organization` = '{$currentUser['organization']}' AND `role` = 'Organization Administrator'", "num");
				
				if ($organizationData == 1) {
					if (isset($_GET['id'])) {
						redirect($_SERVER['REQUEST_URI'] . "&error=noAdmin");
					} else {
						redirect($_SERVER['PHP_SELF'] . "?error=noAdmin");
					}
				}
			}
		}
		
		$adminGrabber = query("SELECT * FROM `organizations` WHERE `id` = '{$currentUser['organization']}'");
		
		if ($role != "Organization Administrator") {
			$admins = implode(",", removeElement(explode(",", $adminGrabber['admin']), $_GET['id']));
			query("UPDATE `organizations` SET `admin` = '{$admins}' WHERE `id` = '{$adminGrabber['id']}'");
		} else {
			$admins = $adminGrabber['admin'] . "," . $_GET['id'];
			
			if (!in_array($_GET['id'], explode(",", $adminGrabber['admin']))) {
				query("UPDATE `organizations` SET `admin` = '{$admins}' WHERE `id` = '{$currentUser['organization']}'");
				
				if ($_POST['organization'] != "0") {
					query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$id}' WHERE `id` = '{$userID}'");
					$emailGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userID}'");
					
					autoEmail($emailGrabber['emailAddress1'], "Your are an Organization Administrator", "
					You have been granted the Organization Administrator role for \"" . $_POST['name'] . "\".
					
					Please copy and paste the following link into your browser to access the login page.
					
					" . $root . "login.php
					
					-------------------------------------------------
					Please DO NOT reply to this email. If you have any questions regarding the contents of this email, please contact one of the site administrators.");
				}
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
				$password = encrypt($_POST['password']);
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
			if (!empty($_POST['password'])) {
				$password = encrypt($_POST['password']);
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
				$organization = $_POST['organization'];
			} else {
				$organization = "0";
			}
		}
		
		if (isset ($user)) {			
			query("UPDATE `users` SET `staffID` = '{$staffID}', `firstName` = '{$firstName}', `lastName` = '{$lastName}', `userName` = '{$userName}', `password` = '{$password}', `changePassword` = '{$changePassword}', `emailAddress1` = '{$primaryEmail}', `emailAddress2` = '{$secondaryEmail}', `emailAddress3` = '{$tertiaryEmail}', `phoneWork` = '{$phoneWork}', `phoneHome` = '{$phoneHome}', `phoneMobile` = '{$phoneMobile}', `phoneFax` = '{$phoneFax}', `phonePager` = '{$phonePager}', `workLocation` = '{$workLocation}', `jobTitle` = '{$jobTitle}', `department` = '{$department}', `departmentID` = '{$departmentID}', `role` = '{$role}', `organization` = '{$organization}' WHERE `id` = '{$_GET['id']}'");
			
			if ($userData['id'] == $_GET['id'] && $role != $_SESSION['MM_UserGroup']) {
				$_SESSION['MM_UserGroup'] = $role;
				redirect("../portal/index.php");
			}
			
			if (isset($_GET['return']) && $_POST['organization'] == "0") {
				redirect(urldecode($_GET['return']));
			} else {
				redirect("index.php?updated=user");
			}
		} else {
			query("INSERT INTO `users` (
					  `id`, `locked`, `active`, `staffID`, `firstName`, `lastName`, `userName`, `password`, `changePassword`, `emailAddress1`, `emailAddress2`, `emailAddress3`, `phoneWork`, `phoneHome`, `phoneMobile`, `phoneFax`, `phonePager`, `workLocation`, `jobTitle`, `department`, `departmentID`, `role`, `organization`, `modules`
				  ) VALUES (
					  NULL, '', '', '{$staffID}', '{$firstName}', '{$lastName}', '{$userName}', '{$password}', '{$changePassword}', '{$primaryEmail}', '{$secondaryEmail}', '{$tertiaryEmail}', '{$phoneWork}', '{$phoneHome}', '{$phoneMobile}', '{$phoneFax}', '{$phonePager}', '{$workLocation}', '{$jobTitle}', '{$department}', '{$departmentID}', '{$role}', '{$organization}', ''
				  )");
			
			if (isset($_GET['return']) && $_POST['organization'] == "0") {
				redirect(urldecode($_GET['return']));
			} else {
				redirect("index.php?inserted=user");
			}
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
	
	if (!access("manageAllUsers") || isset($_GET['return'])) {
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
	
	if (access("manageAllOrganizations")) {
		echo URL("Enter Additional Contact Information (Not Required)", "javascript:void", false, false, false, false, false, false, false, " onclick=\"toggleInfo('contactInformation')\"");
	}
	
	if (!access("manageAllUsers")) {
		$require = true;
	} else {
		$require = false;
		echo "<div id=\"contactInformation\" class=\"contentHide\">";
	}
	
	directions("Work Telephone", $require);
	echo "<blockquote><p>";
	textField("phoneWork", "phoneWork", false, false, false, $require, ",custom[telephone]", false, "user", "phoneWork");
	echo "</p></blockquote>";
	directions("Work Fax", false);
	echo "<blockquote><p>";
	textField("phoneFax", "phoneFax", false, false, false, false, ",custom[telephone]", false, "user", "phoneFax");
	echo "</p></blockquote>";
	directions("Work Pager", false);
	echo "<blockquote><p>";
	textField("phonePager", "phonePager", false, false, false, false, ",custom[telephone]", false, "user", "phonePager");
	echo "</p></blockquote>";
	directions("Mobile Telephone", false);
	echo "<blockquote><p>";
	textField("phoneMobile", "phoneMobile", false, false, false, false, ",custom[telephone]", false, "user", "phoneMobile");
	echo "</p></blockquote>";
	directions("Home Telephone", false);
	echo "<blockquote><p>";
	textField("phoneHome", "phoneHome", false, false, false, false, ",custom[telephone]", false, "user", "phoneHome");
	echo "</p></blockquote>";
	
	if (access("manageAllUsers")) {
		echo "</div>";
	}
	
	echo "</blockquote>";
	
	catDivider("Workplace Information", "four");
	echo "<blockquote>";
	
	if (access("manageAllUsers")) {
		$organizationGrabber = query("SELECT * FROM `organizations`", "raw");
		$organizationValuesPrep = "- None -,";
		$organizationIDsPrep = "0,";
		
		while ($organization = mysql_fetch_array($organizationGrabber)) {
			$organizationValuesPrep .= $organization['organization'] . ",";
			$organizationIDsPrep .= $organization['id'] . ",";
		}
		
		$organizationValues = rtrim($organizationValuesPrep, ",");
		$organizationIDs = rtrim($organizationIDsPrep, ",");
		
		if (!isset($_GET['return'])) {
			directions("Assign this user to an organization");
			echo "<blockquote><p>";
			dropDown("organization", "organization", $organizationValues, $organizationIDs, false, false, false, false, "user", "organization");
			echo "</p></blockquote>";
		} else {
			hidden("organization", "organization", "0");
		}
	}
	
	if (access("manageAllOrganizations")) {
		echo URL("Enter Additional Workplace Information (Not Required)", "javascript:void", false, false, false, false, false, false, false, " onclick=\"toggleInfo('workplaceInformation')\"");
	}
	
	if (access("manageAllUsers")) {
		echo "<div id=\"workplaceInformation\" class=\"contentHide\">";
	}
	
	directions("Staff ID", $require);
	echo "<blockquote><p>";
	textField("staffID", "staffID", false, false, false, $require, false, false, "user", "staffID");
	echo "</p></blockquote>";
	directions("Work Location", $require);
	echo "<blockquote><p>";
	echo "<div id=\"workLocationMenu\">";
	textField("workLocation", "workLocation", false, false, false, $require, false, false, "user", "workLocation");
	echo "<div><div id=\"locationSuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{location}\">{location}</div></div></div></div>";
	echo "</p></blockquote>";
	echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"workLocationMenu\", \"locationSuggestions\", \"data\", \"location\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
	directions("Job Title", $require);
	echo "<blockquote><p>";
	echo "<div id=\"jobTitleMenu\">";
	textField("jobTitle", "jobTitle", false, false, false, $require, false, false, "user", "jobTitle");
	echo "<div><div id=\"titleSuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{jobTitle}\">{jobTitle}</div></div></div></div>";
	echo "</p></blockquote>";
	echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"jobTitleMenu\", \"titleSuggestions\", \"data\", \"jobTitle\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
	directions("Department", $require);
	echo "<blockquote><p>";
	echo "<div id=\"departmentMenu\">";
	textField("department", "department", false, false, false, $require, false, false, "user", "department");
	echo "<div><div id=\"departmentSuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{department}\">{department}</div></div></div></div>";
	echo "</p></blockquote>";
	echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"departmentMenu\", \"departmentSuggestions\", \"data\", \"department\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
	directions("Department ID", $require);
	echo "<blockquote><p>";
	echo "<div id=\"departmentIDMenu\">";
	textField("departmentID", "departmentID", false, false, false, $require, false, false, "user", "departmentID");
	echo "<div><div id=\"departmentIDSuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{departmentID}\">{departmentID}</div></div></div></div>";
	echo "</p></blockquote>";
	echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"departmentIDMenu\", \"departmentIDSuggestions\", \"data\", \"departmentID\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
	
	if (access("manageAllUsers")) {
		echo "</div>";
	}
	
	echo "</blockquote>";
	
	catDivider("Submit", "five");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "index.php");
	echo "</p></blockquote>";
	closeForm(true, true);

//Include the footer
	footer();
?>