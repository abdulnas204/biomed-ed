<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the group is being edited
	$userData = userData();
	
	if (isset ($_GET['id'])) {
		if (exist("groups", "id", $_GET['id'])) {
			$groupData = query("SELECT * FROM `groups` WHERE `id` = '{$_GET['id']}'");
			
			if ($userData['organization'] !== $groupData['organization']) {
				redirect("group.php");
			}
		} else {
			redirect("group.php");
		}
	}
	
	if (isset($groupData)) {
		$title = "Edit the " . prepare($groupData['name'], false, true) . " Group";
	} else {
		$title =  "Create a New Group";
	}
	
	headers($title, "Organization Administrator", "tinyMCESimple,validate,optionTransfer", true, " onload=\"opt.init(document.forms[0])\"");
	
//Find the list of users, and potential users
	$potentialValuesPrep = "";
	$potentialIDsPrep = "";
	$selectedValuesPrep = "";
	$selectedIDsPrep = "";
	$userGrabber = query("SELECT * FROM `users` WHERE `organization` = '{$userData['organization']}' ORDER BY `firstName` ASC", "raw");
	
	while ($user = mysql_fetch_array($userGrabber)) {
		$groupsGrabber = query("SELECT * FROM `groups` WHERE `organization` = '{$userData['organization']}'", "raw");
		$participantingGroupPrep = "";
		
		if ($groupsGrabber) {
			while ($groups = mysql_fetch_array($groupsGrabber)) {
				if (in_array($user['id'], explode(",", $groups['participants']))) {
					$participantingGroupPrep .= prepare($groups['name'], false, true) . "; ";
				}
				
				if (empty($participantingGroupPrep)) {
					$participantingGroup = " (No Groups)";
				} else {
					$participantingGroup = " (" . rtrim($participantingGroupPrep, "; ") . ")";
				}
			}
		} else {
			$participantingGroup = " (No Groups)";
		}
		
		if (!isset($groupData)) {
			$potentialValuesPrep .= $user['firstName'] . " " . $user['lastName'] . $participantingGroup . ",";
			$potentialIDsPrep .= $user['id'] . ",";
		} else {
			if (!in_array($user['id'], explode(",", $groupData['participants']))) {
				$potentialValuesPrep .= $user['firstName'] . " " . $user['lastName'] . $participantingGroup . ",";
				$potentialIDsPrep .= $user['id'] . ",";
			} else {
				$selectedValuesPrep .= $user['firstName'] . " " . $user['lastName'] . $participantingGroup . ",";
				$selectedIDsPrep .= $user['id'] . ",";
			}
		}
	}
	
	$potentialValues = rtrim($potentialValuesPrep, ",");
	$potentialIDs = rtrim($potentialIDsPrep, ",");
	$selectedValues = rtrim($selectedValuesPrep, ",");
	$selectedIDs = rtrim($selectedIDsPrep, ",");
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['toImport'])) {
		$organization = $userData['organization'];
		$name = mysql_real_escape_string($_POST['name']);
		$comments = mysql_real_escape_string($_POST['comments']);
		$participants = mysql_real_escape_string($_POST['toImport']);
		
		if (isset($groupData)) {			
			query("UPDATE `groups` SET `name` = '{$name}', `comments` = '{$comments}', `participants` = '{$participants}' WHERE `id` = '{$groupData['id']}'");
			
			redirect ("group.php?updated=group");
		} else {
			query("INSERT INTO `groups` (
					  `id`, `organization`, `name`, `comments`, `participants`
				  ) VALUES (
					  NULL, '{$organization}', '{$name}', '{$comments}', '{$participants}'
				  )");
				  
			redirect ("group.php?inserted=group");
		}
	}
	
//Title
	title($title, "Use this page to manage group names and users.");
	
//Groups form
	form("manageGroup");
	catDivider("Name", "one", true);
	echo "<blockquote>";
	directions("Assign this group a name", true);
	echo "<blockquote><p>";
	textField("name", "name", false, false, false, true, false, false, "groupData", "name");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Comments", "two");
	echo "<blockquote>";
	directions("Comments", false);
	echo "<blockquote><p>";
	textArea("comments", "comments", "small", false, false, false, "groupData", "comments");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Participants", "three");
	echo "<blockquote>";
	directions("Assign the group participants", true);
	echo "<blockquote><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential users:</h3><div class=\"collapseElement\">";
	textField("placeHolder", "placeHolder", false, false, false, false);
	echo "</div><div align=\"center\">";
	dropDown("notToList", "notToList", $potentialValues, $potentialIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferRight()\"");
	echo "<br /><br />";
	button("allRight", "allRight", "All &gt;&gt;", "button", false, " onclick=\"opt.transferAllRight()\"");
	echo " ";
	button("right", "right", "&gt;&gt;", "button", false, " onclick=\"opt.transferRight()\"");
	echo "</div></div><div class=\"halfRight\"><h3>Selected users:</h3><div class=\"collapseElement\">";
	textField("toImport", "toImport", false, false, false, true, false, false, "organizationData", "admin", " readonly=\"readonly\"");
	echo "</div><div align=\"center\">";
	dropDown("toList", "toList", $selectedValues, $selectedIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferLeft()\"");
	echo "<br /><br />";
	button("left", "left", "&lt;&lt;", "button", false, " onclick=\"opt.transferLeft()\"");
	echo " ";
	button("allLeft", "allLeft", "&lt;&lt; All", "button", false, " onclick=\"opt.transferAllLeft()\"");
	echo "</div></div></div></blockquote>";
	echo "</blockquote>";
	
	catDivider("Submit", "four");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "index.php");
	echo "</p></blockquote>";
	closeForm(true, true);

//Include the footer
	footer();
?>