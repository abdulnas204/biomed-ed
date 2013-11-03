<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the organization name exists
	validateName("organizations", "organization");
	
//Check to see if the organization is being edited	
	$userInfo = userData();
	
	if (isset ($_GET['id']) && access("manageAllOrganizations")) {
		if ($organizationData = exist("organizations", "id", $_GET['id'])) {
			$id = $organizationData['id'];
		} else {
			redirect("index.php");
		}
	} else {
		if (!access("manageAllOrganizations")) {
			$organizationData = query("SELECT * FROM `organizations` WHERE `id` = '{$userInfo['organization']}'");
			$id = $organizationData['id'];
		}
	}
	
	if (access("manageAllOrganizations")) {
		if (isset($pageData)) {
			$title = "Edit the " . prepare($organizationData['organization'], true) . " Organization";
		} else {
			$title =  "Create a New Organization";
		}
		
		$description = "Organizations can be managed by filling in the information below. The organization's complete details and payment method will be setup when the organization administrator first logs in.";
	} else {
		$title = prepare($organizationData['organization'], false, true);
		$description = "Manage " . $title . "'s information by altering the information below.";
	}
	
	headers($title, "Organization Administrator,Site Administrator", "validate,optionTransfer,showHide", true, " onload=\"opt.init(document.forms[0])\"");
	
//Find the list of administrators, and potential users
	if (!access("manageAllOrganizations")) {
		$sql = "`organization` = '{$userInfo['organization']}'";
	} else {
		$sql = "`role` != 'Site Administrator' OR 'Site Manager'";
	}
	
	$potentialValuesPrep = "";
	$potentialIDsPrep = "";
	$adminValuesPrep = "";
	$adminIDsPrep = "";
	$potentialUserGrabber = query("SELECT * FROM `users` WHERE {$sql} ORDER BY `firstName` ASC", "raw");
	$adminGrabber = query("SELECT * FROM `users` WHERE `role` = 'Organization Administrator' ORDER BY `firstName` ASC", "raw");
	
	if (!isset($id)) {
		while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
			if (($potentialUser['role'] == "Organization Administrator" && $potentialUser['organization'] == "0") || $potentialUser['role'] != "Organization Administrator") {
				$potentialValuesPrep .= $potentialUser['firstName'] . " " . $potentialUser['lastName'] . ",";
				$potentialIDsPrep .= $potentialUser['id'] . ",";
			}
		}
	} else {		
		$organization = query("SELECT * FROM `organizations` WHERE `id` = '{$id}'");
		$currentAdministrator = explode(",", $organization['admin']);
		
		while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
			if (($potentialUser['role'] == "Organization Administrator" && $potentialUser['organization'] == "0") || $potentialUser['role'] != "Organization Administrator" && !in_array($potentialUser['id'], $currentAdministrator)) {
				$potentialValuesPrep .= $potentialUser['firstName'] . " " . $potentialUser['lastName'] . ",";
				$potentialIDsPrep .= $potentialUser['id'] . ",";
			}
		}
		
		while ($admin = mysql_fetch_array($adminGrabber)) {
			if (in_array($admin['id'], $currentAdministrator)) {
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
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['toImport'])) {
		if (!access("manageAllOrganizations") && !empty($_POST['specialty']) && !empty($_POST['webSite']) && !empty($_POST['phone']) && !empty($_POST['fax']) && !empty($_POST['mailingAddress1']) && !empty($_POST['mailingCity']) && !empty($_POST['mailingState']) && !empty($_POST['mailingZIP']) && !empty($_POST['billingAddress1']) && !empty($_POST['billingCity']) && !empty($_POST['billingState']) && !empty($_POST['billingZIP']) && !empty($_POST['billingPhone']) && !empty($_POST['billingFax']) && !empty($_POST['billingEmail']) && !empty($_POST['timeZone'])) {
			//Continue to processor, all form fields are complete
		} elseif (access("manageAllOrganizations")) {
			//Continue to processor, all necessary form fields are complete
		} else {
			redirect($_SERVER['REQUEST_URI']);
		}
		
		$organization = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
		$admin = $_POST['toImport'];
		$specialty = $_POST['specialty'];
		$webSite = $_POST['webSite'];
		$phone = $_POST['phone'];
		$fax = $_POST['fax'];
		$mailingAddress1 = $_POST['mailingAddress1'];
		$mailingAddress2 = $_POST['mailingAddress2'];
		$mailingCity = $_POST['mailingCity'];
		$mailingState = $_POST['mailingState'];
		$mailingZIP = $_POST['mailingZIP'];
		$billingAddress1 = $_POST['billingAddress1'];
		$billingAddress2 = $_POST['billingAddress2'];
		$billingCity = $_POST['billingCity'];
		$billingState = $_POST['billingState'];
		$billingZIP = $_POST['billingZIP'];
		$billingPhone = $_POST['billingPhone'];
		$billingFax = $_POST['billingFax'];
		$billingEmail = $_POST['billingEmail'];
		$timeZone = $_POST['timeZone'];
		
		if (query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'")) {
			if (isset($organizationData) && $organizationData['organization'] !== $organization) {
				if (isset($id)) {
					redirect($_SERVER['REQUEST_URI'] . "&error=identical");
				} else {
					redirect($_SERVER['PHP_SELF'] . "?error=identical");
				}
			}
		}
		
		if (isset($id)) {
			$oldData = query("SELECT * FROM `organizations` WHERE `id` = '{$id}'");
			$oldAdmin = explode(",", $oldData['admin']);
			$newAdmin = explode(",", $admin);
			
			foreach ($oldAdmin as $userID) {
				if (!in_array($userID, $newAdmin)) {
					mysql_query("UPDATE `users` SET `role` = 'Student', `organization` = '{$id}' WHERE `id` = '{$userID}'", $connDBA);
				}
			}
			
			foreach ($newAdmin as $userID) {				
				if (!in_array($userID, $oldAdmin)) {
					mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$id}' WHERE `id` = '{$userID}'", $connDBA);
					
					$domain = explode(":", $_SERVER['HTTP_HOST']);
					$emailGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userID}'");
					mail($emailGrabber['emailAddress1'], "Your are an Organization Administrator", "
					You have been granted the Organization Administrator role for \"" . $_POST['name'] . "\".
					
					Please copy and paste the following link into your browser to access the login page.
					
					" . $root . "login.php
					
					-------------------------------------------------
					Please DO NOT reply to this email. If you have any questions regarding the contents of this email, please contact one of the site administrators.", "From: No Reply<no-reply@" . $domain['0'] . ">");
				}
			}
			
			$organizationStatus = query("SELECT * FROM `organizations` WHERE `id` = '{$userData['organization']}'");
					
			query("UPDATE `organizations` SET `organization` = '{$organization}', `admin` = '{$admin}', `specialty` = '{$specialty}', `webSite` = '{$webSite}', `phone` = '{$phone}', `fax` = '{$fax}', `mailingAddress1` = '{$mailingAddress1}', `mailingAddress2` = '{$mailingAddress2}', `mailingCity` = '{$mailingCity}', `mailingState` = '{$mailingState}', `mailingZIP` = '{$mailingZIP}', `billingAddress1` = '{$billingAddress1}', `billingAddress2` = '{$billingAddress2}', `billingCity` = '{$billingCity}', `billingState` = '{$billingState}', `billingZIP` = '{$billingZIP}', `billingPhone` = '{$billingPhone}', `billingFax` = '{$billingFax}', `billingEmail` = '{$billingEmail}', `timeZone` = '{$timeZone}' WHERE `id` = '{$id}'");
			
			if (!in_array($userInfo['id'], $newAdmin) && in_array($userInfo['id'], $oldAdmin)) {
				$_SESSION['MM_UserGroup'] = "Student";
				redirect("../portal/index.php");
			}
			
			if ($_SESSION['MM_UserGroup'] == "Organization Administrator" && (empty($organizationStatus['specialty']) || empty($organizationStatus['webSite']) || empty($organizationStatus['phone']) || empty($organizationStatus['fax']) || empty($organizationStatus['mailingAddress1']) || empty($organizationStatus['mailingCity']) || empty($organizationStatus['mailingState']) || empty($organizationStatus['mailingZIP']) || empty($organizationStatus['billingAddress1']) || empty($organizationStatus['billingCity']) || empty($organizationStatus['billingState']) || empty($organizationStatus['billingZIP']) || empty($organizationStatus['billingPhone']) || empty($organizationStatus['billingFax']) || empty($organizationStatus['billingEmail']) || empty($organizationStatus['timeZone']))) {
				$title = "Welcome to your new Organization!";
				$content = mysql_real_escape_string('<p>We are proud to provide a complete education solution your organization. This powerful application is broken down in to several sections, all of which are easily accessible via the navigation bar above. Each of these sections are intended to manage different parts of the organization.</p>
<p style="padding-left: 30px;"><a href="index.php"><strong>Home</strong></a> - This is the screen you are currently viewing. This page will display immediate information relevent to this organization, such as announcements, daily traffic, overview of registered users, and recent user activity.</p>
<p style="padding-left: 30px;"><a href="../users/index.php"><strong>Users</strong></a> - Create, update, and delete users and custom user groups.</p>
<p style="padding-left: 30px;"><a href="../organization/index.php"><strong>Organization</strong></a> - View all information and billing history relevent to this organization. This information can be updated as needed.</p>
<p style="padding-left: 30px;"><a href="../communication/index.php"><strong>Communication</strong></a> - Send announcements to be displayed on the home page (such as this one), or send a mass email to selected users or groups.</p>
<p style="padding-left: 30px;"><a href="../modules/index.php"><strong>Modules</strong></a> - Create new lessons and tests, customize existing ones provided by the site administrators, and set which ones will be available to instructors and students.</p>
<p style="padding-left: 30px;"><a href="../statistics/index.php"><strong>Statistics</strong></a> - View detailed charts and tables with information about your organization, modules, and users.</p>');
				
				query("INSERT INTO `announcements_{$organizationStatus['id']}` (
						  `id`, `position`, `visible`, `display`, `to`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`
					  ) VALUES (
						  NULL, '1', 'on', 'Selected Roles', 'Organization Administrator', '', '', '', '', '{$title}', '{$content}'
					  )");
					  
				redirect("../portal/index.php");
			} else {
				redirect("index.php?updated=organization");
			}
		} else {			
			$contractStart = strtotime("now");
			$contractEnd = strtotime("+1 year");
			
			query("INSERT INTO organizations (
					`id`, `organization`, `admin`, `specialty`, `webSite`, `phone`, `fax`, `mailingAddress1`, `mailingAddress2`, `mailingCity`, `mailingState`, `mailingZIP`, `billingAddress1`, `billingAddress2`, `billingCity`, `billingState`, `billingZIP`, `billingPhone`, `billingFax`, `billingEmail`, `contractStart`, `contractEnd`, `active`, `timeZone`
				  ) VALUES (
					NULL , '{$organization}', '{$admin}', '{$specialty}', '{$website}', '{$phone}', '{$fax}', '{$mailingAddress1}', '{$mailingAddress2}', '{$mailingCity}', '{$mailingState}', '{$mailingZIP}', '{$billingAddress1}', '{$billingAddress2}', '{$billingCity}', '{$billingState}', '{$billingZIP}', '{$billingPhone}', '{$billingFax}', '{$billingEmail}', '{$contractStart}', '{$contractEnd}', '1', '{$timeZone}'
				  )");
				  
			$newID = mysql_insert_id();
				  
			query("CREATE TABLE IF NOT EXISTS `announcements_{$newID}` (
							  `id` int(11) NOT NULL AUTO_INCREMENT,
							  `position` int(11) NOT NULL,
							  `visible` text NOT NULL,
							  `display` longtext NOT NULL,
							  `to` longtext NOT NULL,
							  `fromDate` longtext NOT NULL,
							  `fromTime` longtext NOT NULL,
							  `toDate` longtext NOT NULL,
							  `toTime` longtext NOT NULL,
							  `title` longtext NOT NULL,
							  `content` longtext NOT NULL,
							  PRIMARY KEY (`id`)
							)");
							
			query("CREATE TABLE IF NOT EXISTS `organizationstatistics_{$newID}` (
							  `id` int(255) NOT NULL AUTO_INCREMENT,
							  `date` varchar(255) NOT NULL,
							  `hits` int(255) NOT NULL,
							  PRIMARY KEY (`id`)
							)");
							
			$newAdmin = explode(",", $admin);
			
			foreach ($newAdmin as $userID) {
				mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$newID}' WHERE `id` = '{$userID}'", $connDBA);
				
				$emailGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userID}'");
				mail($emailGrabber['emailAddress1'], "Your are an Organization Administrator", "
				You have been granted the Organization Administrator role for \"" . $_POST['name'] . "\".
				
				This organization many require some initial setup. Please copy and paste the following link into your browser to access the login page.
				
				" . $root . "login.php
				
				-------------------------------------------------
				Please DO NOT reply to this email. If you have any questions regarding the contents of this email, please contact one of the site administrators.", "From: No Reply<no-reply@" . $domain['0'] . ">");
			}
							
			redirect("index.php?inserted=organization");
		}
	}

//Title
	$organizationStatus = query("SELECT * FROM `organizations` WHERE `id` = '{$userInfo['organization']}'");
	
	if (!isset($_GET['error']) && !isset($_GET['inserted']) && (!empty($organizationStatus['phone']) || $userInfo['organization'] == "0")) {
		title($title, $description);
	} else {
		title($title, $description, false);
	}
	
//Display message updates
	if ($_SESSION['MM_UserGroup'] == "Organization Administrator" && empty($organizationStatus['phone'])) {
		message(false, false, "alert", "Please set up the organization information.");
	}
	
	message("error", "identical", "error", "An organization with this name already exists.");
	message("error", "noAdmin", "error", "Please assign at least one administrator.");
	message("inserted", "user", "success", "The user has been created and is now included in the list.");

//Organization form	
	form("manageOrganization");
	catDivider("Organization Name", "one", true);
	echo "<blockquote>";
	directions("Assign the organization name", true);
	echo "<blockquote><p>";
	textField("name", "name", false, false, false, true, ",ajax[ajaxName]", false, "organizationData", "organization");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Assign Administrator", "two");
	echo "<blockquote>";
	
	if ($_SESSION['MM_UserGroup'] == "Organization Administrator" && empty($organizationStatus['phone'])) {
		hidden("toImport", "toImport", $organizationStatus['admin']);
		echo "<p>You are the only administrator for this organization. More administrators can added later under the user management section.</p>";
	} else {
		directions("Assign the organization administrators", true);
		
		if (strstr($_SERVER['REQUEST_URI'], "?")) {
			$return = urlencode($_SERVER['REQUEST_URI'] . "&inserted=user");
		} else {
			$return = urlencode($_SERVER['REQUEST_URI'] . "?inserted=user");
		}
		
		echo "<p>If the user you are looking for is not listed, " . URL("create this user now", "../users/manage_user.php?return=" . $return, false, false, false, false, false, false, false, " onclick=\"return confirm('You are about to leave this page. Click OK to continue.')\"") . ".";
		echo "<blockquote><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential users:</h3><div class=\"collapseElement\">";
		textField("placeHolder", "placeHolder", false, false, false, false);
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
		echo "</div></div></div></blockquote>";
		
		if (access("manageAllOrganizations")) {
			echo URL("Enter Organization Specific Information (Not Required)", "javascript:void", false, false, false, false, false, false, false, " onclick=\"toggleInfo('additionalInformation', 'toggleNumber', 'catDivider three', 'catDivider six')\"");
		}
	} 
	
	echo "</blockquote>";
	
	if (!access("manageAllOrganizations")) {
		$require = true;
		catDivider("Organization Information", "three");
	} else {
		$require = false;
		catDivider(false, false, false, true);
		echo "<div id=\"additionalInformation\" class=\"contentHide\">";
		catDivider("Organization Information", "three", true);
	}

	$statesValues = "- Select -,Alabama,Alaska,Arizona,Arkansas,California,Colorado,Connecticut,Delaware,District of Columbia,Florida,Georgia,Hawaii,Idaho,Illinois,Indiana,Iowa,Kansas,Kentucky,Louisiana,Maine,Maryland,Massachusetts,Michigan,Minnesota,Mississippi,Missouri,Montana,Nebraska,Nevada,New Hampshire,New Jersey,New Mexico,New York,North Carolina,North Dakota,Ohio,Oklahoma,Oregon,Pennsylvania,Rhode Island,South Carolina,South Dakota,Tennessee,Texas,Utah,Vermont,Virginia,Washington,West Virginia,Wisconsin,Wyoming";
	$statesIDs = ",AL,AK,AZ,AR,CA,CO,CT,DE,DC,FL,GA,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MA,MI,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,NC,ND,OH,OK,OR,PA,RI,SC,SD,TN,TX,UT,VT,VA,WA,WV,WI,WY";
	
	echo "<blockquote>";
	directions("Specialty (Seperate with a comma and a space)", $require);
	echo "<blockquote><p>";
	textField("specialty", "specialty", false, false, false, $require, false, false, "organizationData", "specialty");
	echo "</p></blockquote>";
	directions("Website (Begin with http://)", $require);
	echo "<blockquote><p>";
	
	if (isset($organizationData) && !empty($organizationData['webSite'])) {
		textField("webSite", "webSite", false, false, false, $require, false, false, "organizationData", "webSite");
	} else {
		textField("webSite", "webSite", false, false, false, $require, false, "http://");
	}
	
	echo "</p></blockquote>";
	directions("Time Zone", false);
	echo "<blockquote><p>";
	
	$timeZoneValues = "Eastern Time Zone,Central Time Zone,Mountain Time Zone,Pacific Time Zone,Alaskan Time Zone,Hawaii-Aleutian Time Zone";
	$timeZoneIDs = "America/New_York,America/Chicago,America/Denver,America/Los_Angeles,America/Juneau,Pacific/Honolulu";
	
	dropDown("timeZone", "timeZone", $timeZoneValues, $timeZoneIDs, false, true, false, false, "organizationData", "timeZone");
	echo "</p></blockquote>";
	echo "</blockquote>";
	
	catDivider("Contact Information", "four");
	echo "<blockquote>";
	directions("Organization Phone", $require);
	echo "<blockquote><p>";
	textField("phone", "phone", false, false, false, $require, ",custom[telephone]", false, "organizationData", "phone");
	echo "</p></blockquote>";
	directions("Organization Fax", $require);
	echo "<blockquote><p>";
	textField("fax", "fax", false, false, false, $require, ",custom[telephone]", false, "organizationData", "fax");
	echo "</p></blockquote>";
	directions("Organization Address 1", $require);
	echo "<blockquote><p>";
	textField("mailingAddress1", "mailingAddress1", false, false, false, $require, false, false, "organizationData", "mailingAddress1");
	echo "</p></blockquote>";
	directions("Organization Address 2", false);
	echo "<blockquote><p>";
	textField("mailingAddress2", "mailingAddress2", false, false, false, false, false, false, "organizationData", "mailingAddress2");
	echo "</p></blockquote>";
	directions("Organization City", $require);
	echo "<blockquote><p>";
	textField("mailingCity", "mailingCity", false, false, false, $require, false, false, "organizationData", "mailingCity");
	echo "</p></blockquote>";
	directions("Organization State", $require);
	echo "<blockquote><p>";		
	dropDown("mailingState", "mailingState", $statesValues, $statesIDs, false, $require, false, false, "organizationData", "mailingState");
	echo "</p></blockquote>";
	directions("Organization ZIP", $require);
	echo "<blockquote><p>";
	textField("mailingZIP", "mailingZIP", "5", "5", false, $require, ",custom[onlyNumber]", false, "organizationData", "mailingZIP");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Billing Information", "five");
	echo "<blockquote>";
	directions("Billing Phone", $require);
	echo "<blockquote><p>";
	textField("billingPhone", "billingPhone", false, false, false, $require, ",custom[telephone]", false, "organizationData", "billingPhone");
	echo "</p></blockquote>";
	directions("Billing Fax", $require);
	echo "<blockquote><p>";
	textField("billingFax", "billingFax", false, false, false, $require, ",custom[telephone]", false, "organizationData", "billingFax");
	echo "</p></blockquote>";
	directions("Billing Address 1", $require);
	echo "<blockquote><p>";
	textField("billingAddress1", "billingAddress1", false, false, false, $require, false, false, "organizationData", "billingAddress1");
	echo "</p></blockquote>";
	directions("Billing Address 2", false);
	echo "<blockquote><p>";
	textField("billingAddress2", "billingAddress2", false, false, false, false, false, false, "organizationData", "billingAddress2");
	echo "</p></blockquote>";
	directions("Billing City", $require);
	echo "<blockquote><p>";
	textField("billingCity", "billingCity", false, false, false, $require, false, false, "organizationData", "billingCity");
	echo "</p></blockquote>";
	directions("Billing State", $require);
	echo "<blockquote><p>";		
	dropDown("billingState", "billingState", $statesValues, $statesIDs, false, $require, false, false, "organizationData", "billingState");
	echo "</p></blockquote>";
	directions("Billing ZIP", $require);
	echo "<blockquote><p>";
	textField("billingZIP", "billingZIP", "5", "5", false, $require, ",custom[onlyNumber]", false, "organizationData", "billingZIP");
	echo "</p></blockquote>";
	directions("Billing Email Address", $require);
	echo "<blockquote><p>";
	textField("billingEmail", "billingEmail", false, false, false, $require, ",custom[email]", false, "organizationData", "billingEmail");
	echo "</p></blockquote>";
	echo "</blockquote>";
	
	if (!access("manageAllOrganizations")) {
		catDivider("Submit", "six");
	} else {
		catDivider(false, false, false, true);
		echo "</div>";
		catDivider("Submit", "three", true, false, "toggleNumber");
	}
	
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "index.php");
	echo "</p></blockquote>";
	closeForm(true, true);

//Include the footer
	footer();
?>