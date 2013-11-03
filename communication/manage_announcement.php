<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the announcement is being edited
	$userInfo = userData();
	$table = "announcements_" . $userInfo['organization'];
	
	if (isset ($_GET['id']) && isset($_GET['type'])) {
		$announcement = $_GET['id'];
		
		if ($announcementCheck = query("SELECT * FROM `{$table}` WHERE `id` = '{$announcement}'")) {
			if ($announcementCheck['display'] == "Selected Users" && $_GET['type'] == "users") {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "All Users" && $_GET['type'] == "all") {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "Selected Organizations" && $_GET['type'] == "organizations" && access("manageAllCommunication")) {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "All Organizations" && $_GET['type'] == "allOrganizations" && access("manageAllCommunication")) {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "Selected Roles" && $_GET['type'] == "roles") {
				$announcement = $announcementCheck;
			} else {
				redirect("index.php");
			}
		} else {
			redirect("index.php");
		}
	}
	
	if (isset ($announcement)) {
		$title = "Edit the " . prepare($announcement['title'], true, true) . " Announcement";
	} else {
		$title =  "Create a New Announcement";
	}
	
	headers($title, "Organization Administrator,Site Administrator", "tinyMCEAdvanced,validate,optionTransfer,enableDisable,calendar", true, " onload=\"opt.init(document.forms[0])\"");
	
//Grab the required values for the selection fields
	if (isset($_GET['type'])) {
		if (!access("manageAllCommunication")) {
			$usersSQL = " WHERE `organization` = '{$userInfo['organization']}' AND `role` != 'Site Administrator' OR 'Site Manager'";
		} else {
			$usersSQL = "";
		}
		
		$potentialValuesPrep = "";
		$potentialIDsPrep = "";
		$selectedValuesPrep = "";
		$selectedIDsPrep = "";
		
		switch ($_GET['type']) {
			case "users" :                       
				$notToUsersGrabber = query("SELECT * FROM `users`{$usersSQL} ORDER BY `lastName` ASC", "raw");
				$toUsersGrabber = query("SELECT * FROM `users`{$usersSQL} ORDER BY `lastName` ASC", "raw");
				
				while($users = mysql_fetch_array($notToUsersGrabber)) {
					if (isset($announcement)) {
						$toArray = explode(",", $announcement['to']);
						
						if (!in_array($users['id'], $toArray)) {
							$potentialValuesPrep .= $users['firstName'] . " " . $users['lastName'] . ",";
							$potentialIDsPrep .= $users['id'] . ",";
						}
					} else {
						$potentialValuesPrep .= $users['firstName'] . " " . $users['lastName'] . ",";
						$potentialIDsPrep .= $users['id'] . ",";
					}
				}
				
				while($users = mysql_fetch_array($toUsersGrabber)) {
					if (isset($announcement)) {
						$toArray = explode(",", $announcement['to']);
						$toSize = sizeof($toArray);
						
						for ($count = 0; $count <= $toSize - 1; $count++) {
							if ($toArray[$count] == $users['id']) { 
								$selectedValuesPrep .= $users['firstName'] . " " . $users['lastName'] . ",";
								$selectedIDsPrep .= $users['id'] . ",";
							}
						}
					}
				}
				
				break;
			
			case "organizations" && access("manageAllCommunication") :                       
				$notToOrganizationsGrabber = query("SELECT * FROM `organizations` ORDER BY `organization` ASC", "raw");
				$toOrganizationsGrabber = query("SELECT * FROM `organizations` ORDER BY `organization` ASC", "raw");
				
				while($organizations = mysql_fetch_array($notToOrganizationsGrabber)) {
					if (isset($announcement)) {
						$toArray = explode(",", $announcement['to']);
						
						if (!in_array($organizations['id'], $toArray)) {
							$potentialValuesPrep .= $organizations['organization'] . ",";
							$potentialIDsPrep .= $organizations['id'] . ",";
						}
					} else {
						$potentialValuesPrep .= $organizations['organization'] . ",";
						$potentialIDsPrep .= $organizations['id'] . ",";
					}
				}
				
				while($organizations = mysql_fetch_array($toOrganizationsGrabber)) {
					if (isset($announcement)) {
						$toArray = explode(",", $announcement['to']);
						$toSize = sizeof($toArray);
						
						for ($count = 0; $count <= $toSize - 1; $count++) {
							if ($toArray[$count] == $organizations['id']) {
								$selectedValuesPrep .= $organizations['organization'] . ",";
								$selectedIDsPrep .= $organizations['id'] . ",";
							}
						}
					}
				}
				
				break;
			
			case "roles" : 
				function createOption($value, $potential) {
					global $announcement, $potentialValuesPrep, $potentialIDsPrep, $selectedValuesPrep, $selectedIDsPrep;
					
					$toArray = explode(",", $announcement['to']);
					
					if ($potential == true) {
						if (!in_array($value, $toArray)) {
							$potentialValuesPrep .= $value . "s,";
							$potentialIDsPrep .= $value . ",";
						}
					} else {
						if (in_array($value, $toArray)) {
							$selectedValuesPrep .= $value . "s,";
							$selectedIDsPrep .= $value . ",";
						}
					}
				}
						
				if (isset($announcement)) {
					createOption("Administrative Assistant", true);
					createOption("Instructorial Assistant", true);
					createOption("Instructor", true);
					createOption("Organization Administrator", true);
					
				if (!access("manageAllCommunication")) {
					createOption("Site Administrator", true);
					createOption("Site Manager", true);
				}
				
					createOption("Student", true);
				} else {
					if (access("manageAllCommunication")) {
						$additionalRoles = "Site Administrators,Site Managers,";
					} else {
						$additionalRoles = "";
					}
					
					$potentialValuesPrep = "Administrative Assistants,Instructorial Assisstants,Instructors,Organization Administrators,{$additionalRoles}Students,";
					$potentialIDsPrep = "Administrative Assistant,Instructorial Assisstant,Instructor,Organization Administrator,{$additionalRoles}Student,";
				}
				
				if (isset($announcement)) {	
					createOption("Administrative Assistant", false);
					createOption("Instructorial Assistant", false);
					createOption("Instructor", false);
					createOption("Organization Administrator", false);
				
				if (!access("manageAllCommunication")) {
					createOption("Site Administrator", true);
					createOption("Site Manager", true);
				}
					createOption("Student", false);
				}
				
				break;
				
			case "all" :
				break;
				
			case "allOrganizations" && access("manageAllCommunication") :
				break;
				
			default :
				redirect("manage_announcement.php");
				break;
		}
		
		if (empty($potentialValuesPrep) || empty($potentialIDsPrep)) {
			$potentialValues = false;
			$potentialIDs = false;
		} else {
			$potentialValues = rtrim($potentialValuesPrep, ",");
			$potentialIDs = rtrim($potentialIDsPrep, ",");
		}
		
		if (empty($selectedValuesPrep) || empty($selectedIDsPrep)) {
			$selectedValues = false;
			$selectedIDs = false;
		} else {
			$selectedValues = rtrim($selectedValuesPrep, ",");
			$selectedIDs = rtrim($selectedIDsPrep, ",");
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['toDetirmine']) && !empty($_POST['toImport']) && !empty($_POST['content'])) {
		$title = mysql_real_escape_string($_POST['title']);
		$toDetirmine = rtrim($_POST['toDetirmine']);
		$toImport = $_POST['toImport'];
		$fromDate = $_POST['from'];
		$fromTime = $_POST['fromTime'];
		$toDate = $_POST['to'];
		$toTime = $_POST['toTime'];
		$content = mysql_real_escape_string($_POST['content']);
	
	//Ensure times are not inferior, the dates are the same, and all dates are set
		if (empty($fromDate) || empty($toDate) || empty($_POST['toggleAvailability'])) {
			$fromDate = "";
			$fromTime = "";
			$toDate = "";
			$toTime = "";
		}
		
		if ($fromDate == $toDate && !empty($fromDate) && !empty($toDate)) {
			$fromTimeArray = explode(":", $fromTime);
			$toTimeArray = explode(":", $toTime);
			
			if ($fromTime == $toTime) {
				$fromDate = "";
				$fromTime = "";
				$toDate = "";
				$toTime = "";
				$redirect = "manage_announcement.php?message=inferior";
			}
			
			if ($toTimeArray[0] < $fromTimeArray[0]) {
				$fromDate = "";
				$fromTime = "";
				$toDate = "";
				$toTime = "";
				$redirect = "manage_announcement.php?message=inferior";
			} elseif ($toTimeArray[0] == $fromTimeArray[0]) {					
				if ($toTimeArray[1] < $fromTimeArray[1]) {
					$fromDate = "";
					$fromTime = "";
					$toDate = "";
					$toTime = "";
					$redirect = "manage_announcement.php?message=inferior";
				}
			} else {
				$redirect = "index.php?inserted=announcement";
			}
		} else {
			$redirect = "index.php?inserted=announcement";
		}
		
		if (!isset ($announcement)) {			
			$positionArray = query("SELECT * FROM `{$table}` ORDER BY position DESC");
			$position = $positionArray['position'] + 1;
			
			query("INSERT INTO `{$table}` (
					  `id`, `position`, `visible`, `display`, `to`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`
				  ) VALUES (
					  NULL, '{$position}', 'on', '{$toDetirmine}', '{$toImport}', '{$fromDate}', '{$fromTime}', '{$toDate}', '{$toTime}', '{$title}', '{$content}'
				  )");
						
			redirect("index.php?inserted=announcement");
		} else {			
			query("UPDATE `{$table}` SET `display` = '{$toDetirmine}', `to` = '{$toImport}', `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}' WHERE `id` = '{$_GET['id']}'");
			
			redirect("index.php?updated=announcement");
		}
	} 

//Show if the type of announcement is not set
	if (!isset($_GET['type'])) {
	//Title
		title("Create a New Announcement", "Select to whom this announcment will go.");
		
	//List of possible values
		echo "<blockquote><p>";
		echo URL("Selected Users", "manage_announcement.php?type=users") . " - Only selected users will recieve this announcement<br />";
		echo URL("All Users", "manage_announcement.php?type=all") . " - All registered users will recieve this announcement<br />";
		
		if (access("manageAllCommunication")) {
			echo URL("Selected Organizations", "manage_announcement.php?type=organizations") . " - All users within selected organizations will recieve this announcement<br />";
			echo URL("All Organizations", "manage_announcement.php?type=allOrganizations") . " - All registered organizations will recieve this announcement<br />";
		}
		
		echo URL("Selected Roles", "manage_announcement.php?type=roles") . " - All users with a selected role will recieve this announcement<br />";
		echo "</p></blockquote>";
//Show if the type of announcement is selected
	} elseif ($_GET['type'] == ("users" || "all" || "organizations" || "allOrganizations" || "roles")) {
	//Title
		$description = "Use this page to ";
		
		if (isset ($pageData)) {
			$description .= "edit this announcment.";
		} else {
			$description .= "create a new announcment.";
		}
		
		title($title, $description);
		
	//Display message updates
		message("message", "inferior", "error", "The start time can not be inferior to or the same as the end time");
	
	//Announcement form
		if (isset($_GET['type'])) {
			if ($_GET['type'] == "users" || $_GET['type'] == "organizations" || $_GET['type'] == "roles") {
				$validate = true;
			} else {
				$validate = false;
			}
		}
		
		form("manageAnnouncement");
		catDivider("Settings", "one", true);
		echo "<blockquote>";
		directions("Title", true);
		echo "<blockquote><p>";
		textField("title", "title", false, false, false, true, false, false, "announcement", "title");
		echo "</p></blockquote>";
		
		if ($_GET['type'] == "users" || $_GET['type'] == "organizations" || $_GET['type'] == "roles") {
			hidden("toDetirmine", "toDetirmine", $_GET['type']);
			directions("To", true);
			echo "<blockquote><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential options:</h3><div class=\"collapseElement\">";
			textField("placeHolder", "placeHolder", false, false, false, false);
			echo "</div><div align=\"center\">";
			dropDown("notToList", "notToList", $potentialValues, $potentialIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferRight()\"");
			echo "<br /><br />";
			button("allRight", "allRight", "All &gt;&gt;", "button", false, " onclick=\"opt.transferAllRight()\"");
			echo " ";
			button("right", "right", "&gt;&gt;", "button", false, " onclick=\"opt.transferRight()\"");
			echo "</div></div><div class=\"halfRight\"><h3>Selected options:</h3><div class=\"collapseElement\">";
			textField("toImport", "toImport", false, false, false, true, false, false, false, false, " readonly=\"readonly\"");
			echo "</div><div align=\"center\">";
			dropDown("toList", "toList", $selectedValues, $selectedIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferLeft()\"");
			echo "<br /><br />";
			button("left", "left", "&lt;&lt;", "button", false, " onclick=\"opt.transferLeft()\"");
			echo " ";
			button("allLeft", "allLeft", "&lt;&lt; All", "button", false, " onclick=\"opt.transferAllLeft()\"");
			echo "</div></div></div></blockquote>";
		} else {
			$type = str_replace("all", "", $_GET['type']);
			
			if (empty($type)) {
				$type = "users";
			}
			
			directions("To", false);
			hidden("toDetirmine", "toDetirmine", "All " . ucfirst($type));
			hidden("toImport", "toImport", "all" . ucfirst($type));
			echo "<blockquote><p><strong>This announcement will be sent to all registered " . strtolower($type) . ".</strong></p></blockquote>";
		}
		
		if (isset ($announcement) && empty($announcement['fromDate'])) {
			$disabled = " disabled=\"disabled\"";
		} elseif (!isset($announcement)) {
			$disabled = " disabled=\"disabled\"";
		} else {
			$disabled = "";
		}
		
		if (isset ($announcement) && $announcement['toDate'] != "") {
			$enabled = true;
		} else {
			$enabled = false;
		}
		
		$timeValue = "12:00 am,12:30 am,1:00 am,1:30 am,2:00 am,2:30 am,3:00 am,3:30 am,4:00 am,4:30 am,5:00 am,5:30 am,6:00 am,6:30 am,7:00 am,7:30 am,8:00 am,8:30 am,9:00 am,9:30 am,10:00 am,10:30 am,11:00 am,11:30 am,12:00 pm,12:30 pm,1:00 pm,1:30 pm,2:00 pm,2:30 pm,3:00 pm,3:30 pm,4:00 pm,4:30 pm,5:00 pm,5:30 pm,6:00 pm,6:30 pm,7:00 pm,7:30 pm,8:00 pm,8:30 pm,9:00 pm,9:30 pm,10:00 pm,10:30 pm,11:00 pm,11:30 pm";
		$timeID = "00:00,12:30,1:00,1:30,2:00,2:30,3:00,3:30,4:00,4:30,5:00,5:30,6:00,6:30,7:00,7:30,8:00,8:30,9:00,9:30,10:00,10:30,11:00,11:30,12:00,12:30,13:00,13:30,14:00,14:30,15:00,15:30,16:00,16:30,17:00,17:30,18:00,18:30,19:00,19:30,20:00,20:30,21:00,21:30,22:00,22:30,23:00,23:30";
		
		directions("Availability", false);
		echo "<blockquote><p>";
		textField("from", "from", "25", false, false, false, false, false, "announcement", "fromDate", $disabled . " readonly=\"readonly\"");
		echo " ";
		dropDown("fromTime", "fromTime", $timeValue, $timeID, false, false, false, "12:00", "announcement", "fromTime", $disabled);
		echo " to ";
		textField("to", "to", "25", false, false, false, false, false, "announcement", "toDate", $disabled . " readonly=\"readonly\"");
		echo " ";
		dropDown("toTime", "toTime", $timeValue, $timeID, false, false, false, "12:00", "announcement", "fromTime", $disabled);
		echo " ";
		checkbox("toggleAvailability", "toggleAvailability", "Enable", "on", false, false, false, $enabled, false, false, " onclick=\"flvFTFO1('manageAnnouncement','from,t','fromTime,t','to,t','toTime,t')\"");
		echo "</p></blockquote></blockquote>";
		
		catDivider("Content", "two");
		echo "<blockquote>";
		directions("Content", true);
		echo "<blockquote><p>";
		textArea("content", "content1", "large", true, false, false, "announcement", "content");
		echo "</p></blockquote></blockquote>";
		
		catDivider("Finish", "three");
		echo "<blockquote><p>";
		button("submit", "submit", "Submit", "submit");
		button("reset", "reset", "Reset", "reset");
		button("cancel", "cancel", "Cancel", "cancel", "index.php");
		echo "</p></blockquote>";
		closeForm(true, true);
//Redirect if the requested type doesn't exist
	} else {
		redirect("manage_announcement.php");
	}
	
//Include the footer
	footer();
?>