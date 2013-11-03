<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the organization name exists
	validateName("organizations", "organization");
	
//Check to see if the organization is being edited	
	if (isset ($_GET['id']) && access("manageAllOrganizations")) {
		if ($organizationData = exist("organizations", "id", $_GET['id'])) {
			$id = $organizationData['id'];
		} else {
			redirect("index.php");
		}
	} else {
		if (!access("manageAllOrganizations")) {
			$userInfo = userData();
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
	
	headers($title, "Organization Administrator,Site Administrator", "tinyMCEAdvanced,validate,optionTransfer", true, " onload=\"opt.init(document.forms[0])\"");
	
//Find the list of administrators, and potential users
	if (!access("manageAllOrganizations")) {
		$sql = "`organization` = '{$userInfo['organization']}'";
	} else {
		$sql = "`role` = 'Student' OR 'Instructor' OR 'Instructorial Assisstant' OR 'Organization Assisstant'";
	}
	
	$potentialValuesPrep = "";
	$potentialIDsPrep = "";
	$adminValuesPrep = "";
	$adminIDsPrep = "";
	$potentialUserGrabber = query("SELECT * FROM `users` WHERE {$sql} ORDER BY `lastName` ASC", "raw");
	$adminGrabber = query("SELECT * FROM `users` WHERE `role` = 'Organization Administrator' ORDER BY `lastName` ASC", "raw");
	
	if (!isset($id)) {
		while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
			$potentialValuesPrep .= $potentialUser['firstName'] . " " . $potentialUser['lastName'] . ",";
			$potentialIDsPrep .= $potentialUser['id'] . ",";
		}
	} else {		
		$organization = query("SELECT * FROM `organizations` WHERE `id` = '{$id}'");
		$currentAdministrator = explode(",", $organization['admin']);
		
		while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
			if (!in_array($potentialUser['id'], $currentAdministrator)) {
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
		$organization = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
		$admin = $_POST['toImport'];
		
		if (query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'")) {
			if (isset($organizationData) && $organizationData['organization'] !== $organization) {
				if (isset($id)) {
					redirect($_SERVER['REQUEST_URI'] . "&error=identical");
				} else {
					redirect($_SERVER['PHP_SELF'] . "?error=identical");
				}
			}
		}
		
		if (!access("manageAllOrganizations")) {	
			if (!empty($_POST['specialty']) && !empty($_POST['webSite']) && !empty($_POST['phone']) && !empty($_POST['fax']) && !empty($_POST['mailingAddress1']) && !empty($_POST['mailingCity']) && !empty($_POST['mailingState']) && !empty($_POST['mailingZIP']) && !empty($_POST['billingAddress1']) && !empty($_POST['billingCity']) && !empty($_POST['billingState']) && !empty($_POST['billingZIP']) && !empty($_POST['billingPhone']) && !empty($_POST['billingFax']) && !empty($_POST['billingEmail']) && !empty($_POST['timeZone'])) {
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
			} else {
				redirect($_SERVER['REQUEST_URI']);
			}
		} else {
			if (isset($id)) {
				$specialty = $organzationData['specialty'];
				$webSite = $organzationData['webSite'];
				$phone = $organzationData['phone'];
				$fax = $organzationData['fax'];
				$mailingAddress1 = $organzationData['mailingAddress1'];
				$mailingAddress2 = $organzationData['mailingAddress2'];
				$mailingCity = $organzationData['mailingCity'];
				$mailingState = $organzationData['mailingState'];
				$mailingZIP = $organzationData['mailingZIP'];
				$billingAddress1 = $organzationData['billingAddress1'];
				$billingAddress2 = $organzationData['billingAddress2'];
				$billingCity = $organzationData['billingCity'];
				$billingState = $organzationData['billingState'];
				$billingZIP = $organzationData['billingZIP'];
				$billingPhone = $organzationData['billingPhone'];
				$billingFax = $organzationData['billingFax'];
				$billingEmail = $organzationData['billingEmail'];
				$timeZone = $organzationData['timeZone'];
			} else {
				$specialty = "";
				$webSite = "";
				$phone = "";
				$fax = "";
				$mailingAddress1 = "";
				$mailingAddress2 = "";
				$mailingCity = "";
				$mailingState = "";
				$mailingZIP = "";
				$billingAddress1 = "";
				$billingAddress2 = "";
				$billingCity = "";
				$billingState = "";
				$billingZIP = "";
				$billingPhone = "";
				$billingFax = "";
				$billingEmail = "";
				$timeZone = "";
			}
		}
		
		if (isset($id)) {
			$oldData = query("SELECT * FROM `organizations` WHERE `id` = '{$id}'");
			$oldAdmin = explode(",", $oldData['admin']);
			$newAdmin = explode(",", $admin);
			
			foreach ($oldAdmin as $userID) {
				mysql_query("UPDATE `users` SET `role` = 'Student', `organization` = '{$id}' WHERE `id` = '{$userID}'", $connDBA);
			}
			
			foreach ($newAdmin as $userID) {
				mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$id}' WHERE `id` = '{$userID}'", $connDBA);
			}
		} else {
			$newAdmin = explode(",", $admin);
			
			foreach ($newAdmin as $userID) {
				mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$id}' WHERE `id` = '{$userID}'", $connDBA);
			}
		}
		
		if (isset($id)) {
			query("UPDATE `organizations` SET `organization` = '{$organization}', `admin` = '{$admin}', `specialty` = '{$specialty}', `webSite` = '{$webSite}', `phone` = '{$phone}', `fax` = '{$fax}', `mailingAddress1` = '{$mailingAddress1}', `mailingAddress2` = '{$mailingAddress2}', `mailingCity` = '{$mailingCity}', `mailingState` = '{$mailingState}', `mailingZIP` = '{$mailingZIP}', `billingAddress1` = '{$billingAddress1}', `billingAddress2` = '{$billingAddress2}', `billingCity` = '{$billingCity}', `billingState` = '{$billingState}', `billingZIP` = '{$billingZIP}', `billingPhone` = '{$billingPhone}', `billingFax` = '{$billingFax}', `billingEmail` = '{$billingEmail}', `timeZone` = '{$timeZone}' WHERE `id` = '{$id}'");
			
			if (!in_array($userInfo['id'], $newAdmin) && in_array($userInfo['id'], $oldAdmin)) {
				$_SESSION['MM_UserGroup'] = "Student";
				redirect("../portal/index.php");
			}
			
			redirect("index.php?updated=organization");
		} else {
			$contractStart = strtotime("now");
			$contractEnd = strtotime("+1 year");
			
			query("INSERT INTO organizations (
					`id`, `organization`, `admin`, `specialty`, `webSite`, `phone`, `fax`, `mailingAddress1`, `mailingAddress2`, `mailingCity`, `mailingState`, `mailingZIP`, `billingAddress1`, `billingAddress2`, `billingCity`, `billingState`, `billingZIP`, `billingPhone`, `billingFax`, `billingEmail`, `contractStart`, `contractEnd`, `active`, `timeZone`
				  ) VALUES (
					NULL , '{$organization}', '{$admin}', '{$specialty}', '{$website}', '{$phone}', '{$fax}', '{$mailingAddress1}', '{$mailingAddress2}', '{$mailingCity}', '{$mailingState}', '{$mailingZIP}', '{$billingAddress1}', '{$billingAddress2}', '{$billingCity}', '{$billingState}', '{$billingZIP}', '{$billingPhone}', '{$billingFax}', '{$billingEmail}', '{$contractStart}', '{$contractEnd}', '1', '{$timeZone}'
				  )");
			
			redirect("index.php?inserted=organization");
		}
	}

//Title
	if (!isset($_GET['error'])) {
		title($title, $description);
	} else {
		title($title, $description, false);
	}
	
//Display message updates
	message("error", "identical", "error", "An organization with this name already exists.");
	message("error", "noAdmin", "error", "Please assign at least one administrator.");

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
	directions("Assign the organization an administrator", true);
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
	echo "</div></div></div></blockquote></blockquote>";
	
	if (access("manageAllOrganizations")) {
		catDivider("Submit", "three");
		echo "<blockquote><p>";
		button("submit", "submit", "Submit", "submit");
		button("reset", "reset", "Reset", "reset");
		button("cancel", "cancel", "Cancel", "cancel", "index.php");
		echo "</p>";
		closeForm(true, true);
	} else {
		$statesValues = "- Select -,Alabama,Alaska,Arizona,Arkansas,California,Colorado,Connecticut,Delaware,District of Columbia,Florida,Georgia,Hawaii,Idaho,Illinois,Indiana,Iowa,Kansas,Kentucky,Louisiana,Maine,Maryland,Massachusetts,Michigan,Minnesota,Mississippi,Missouri,Montana,Nebraska,Nevada,New Hampshire,New Jersey,New Mexico,New York,North Carolina,North Dakota,Ohio,Oklahoma,Oregon,Pennsylvania,Rhode Island,South Carolina,South Dakota,Tennessee,Texas,Utah,Vermont,Virginia,Washington,West Virginia,Wisconsin,Wyoming";
		$statesIDs = ",AL,AK,AZ,AR,CA,CO,CT,DE,DC,FL,GA,HI,ID,IL,IN,IA,KS,KY,LA,ME,MD,MA,MI,MN,MS,MO,MT,NE,NV,NH,NJ,NM,NY,NC,ND,OH,OK,OR,PA,RI,SC,SD,TN,TX,UT,VT,VA,WA,WV,WI,WY";
		
		catDivider("Organization Information", "three");
		echo "<blockquote>";
		directions("Specialty (Seperate with a comma and a space)", true);
		echo "<blockquote><p>";
		textField("specialty", "specialty", false, false, false, true, false, false, "organizationData", "specialty");
		echo "</p></blockquote>";
		directions("Website (Begin with http://)", true);
		echo "<blockquote><p>";
		textField("webSite", "webSite", false, false, false, true, false, "http://", "organizationData", "webSite");
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
		directions("Organization Phone", true);
		echo "<blockquote><p>";
		textField("phone", "phone", false, false, false, true, ",custom[telephone]", false, "organizationData", "phone");
		echo "</p></blockquote>";
		directions("Organization Fax", true);
		echo "<blockquote><p>";
		textField("fax", "fax", false, false, false, true, ",custom[telephone]", false, "organizationData", "fax");
		echo "</p></blockquote>";
		directions("Organization Address 1", true);
		echo "<blockquote><p>";
		textField("mailingAddress1", "mailingAddress1", false, false, false, true, false, false, "organizationData", "mailingAddress1");
		echo "</p></blockquote>";
		directions("Organization Address 2", false);
		echo "<blockquote><p>";
		textField("mailingAddress2", "mailingAddress2", false, false, false, false, false, false, "organizationData", "mailingAddress2");
		echo "</p></blockquote>";
		directions("Organization City", false);
		echo "<blockquote><p>";
		textField("mailingCity", "mailingCity", false, false, false, false, false, false, "organizationData", "mailingCity");
		echo "</p></blockquote>";
		directions("Organization State", false);
		echo "<blockquote><p>";		
		dropDown("mailingState", "mailingState", $statesValues, $statesIDs, false, true, false, false, "organizationData", "mailingState");
		echo "</p></blockquote>";
		directions("Organization ZIP", true);
		echo "<blockquote><p>";
		textField("mailingZIP", "mailingZIP", "5", "5", false, true, ",custom[onlyNumber]", false, "organizationData", "mailingZIP");
		echo "</p></blockquote>";
		directions("Billing Phone", true);
		echo "<blockquote><p>";
		textField("billingPhone", "billingPhone", false, false, false, true, ",custom[telephone]", false, "organizationData", "billingPhone");
		echo "</p></blockquote>";
		directions("Billing Fax", true);
		echo "<blockquote><p>";
		textField("billingFax", "billingFax", false, false, false, true, ",custom[telephone]", false, "organizationData", "billingFax");
		echo "</p></blockquote>";
		directions("Billing Address 1", true);
		echo "<blockquote><p>";
		textField("billingAddress1", "billingAddress1", false, false, false, true, false, false, "organizationData", "billingAddress1");
		echo "</p></blockquote>";
		directions("Billing Address 2", false);
		echo "<blockquote><p>";
		textField("billingAddress2", "billingAddress2", false, false, false, false, false, false, "organizationData", "billingAddress2");
		echo "</p></blockquote>";
		directions("Billing City", false);
		echo "<blockquote><p>";
		textField("billingCity", "billingCity", false, false, false, false, false, false, "organizationData", "billingCity");
		echo "</p></blockquote>";
		directions("Billing State", false);
		echo "<blockquote><p>";		
		dropDown("billingState", "billingState", $statesValues, $statesIDs, false, true, false, false, "organizationData", "billingState");
		echo "</p></blockquote>";
		directions("Billing ZIP", true);
		echo "<blockquote><p>";
		textField("billingZIP", "billingZIP", "5", "5", false, true, ",custom[onlyNumber]", false, "organizationData", "billingZIP");
		echo "</p></blockquote>";
		directions("Billing Email Address", true);
		echo "<blockquote><p>";
		textField("billingEmail", "billingEmail", false, false, false, true, ",custom[email]", false, "organizationData", "billingEmail");
		echo "</p></blockquote>";
		echo "</blockquote>";
		
		catDivider("Submit", "five");
		echo "<blockquote><p>";
		button("submit", "submit", "Submit", "submit");
		button("reset", "reset", "Reset", "reset");
		button("cancel", "cancel", "Cancel", "cancel", "index.php");
		echo "</p>";
		closeForm(true, true);
	}

//Include the footer
	footer();
?>