<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Check to see if the announcement is being edited
	if (isset ($_GET['id']) && isset($_GET['type'])) {
		$announcement = $_GET['id'];
		$announcementGrabber = mysql_query("SELECT * FROM `announcements` WHERE `id` = '{$announcement}'", $connDBA);
		if ($announcementCheck = mysql_fetch_array($announcementGrabber)) {
			if ($announcementCheck['display'] == "Selected Users" && $_GET['type'] == "users") {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "All Users" && $_GET['type'] == "all") {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "Selected Organizations" && $_GET['type'] == "organizations") {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "All Organizations" && $_GET['type'] == "allOrganizations") {
				$announcement = $announcementCheck;
			} elseif ($announcementCheck['display'] == "Selected Roles" && $_GET['type'] == "roles") {
				$announcement = $announcementCheck;
			} else {
				header ("Location: index.php");
				exit;
			}
		} else {
			header ("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['toDetirmine']) && !empty($_POST['toImport']) && !empty($_POST['content'])) {	
		if (!isset ($announcement)) {
			$title = mysql_real_escape_string($_POST['title']);
			$toDetirmine = $_POST['toDetirmine'];
			$toImport = $_POST['toImport'];
			$fromDate = $_POST['from'];
			$fromTime = $_POST['fromTime'];
			$toDate = $_POST['to'];
			$toTime = $_POST['toTime'];
			$content = mysql_real_escape_string($_POST['content']);
		
		//Ensure times are not inferior if the dates are the same
			if ($fromDate == $toDate && !empty($fromDate) && !empty($toDate)) {
				$fromTimeArray = explode(":", $fromTime);
				$toTimeArray = explode(":", $toTime);
				
				if ($fromTime == $toTime) {
					header("Location: manage_announcement.php?message=inferior");
					exit;
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					header("Location: manage_announcement.php?message=inferior");
					exit;
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {					
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						header("Location: manage_announcement.php?message=inferior");
						exit;
					}
				}
			}
			
			$positionGrabber = mysql_query ("SELECT * FROM `announcements` ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newAnnouncementQuery = "INSERT INTO announcements (
								`id`, `position`, `visible`, `display`, `to`, `fromDate`, `fromTime`, `toDate`, `toTime`, `title`, `content`
							) VALUES (
								NULL, '{$position}', 'on', '{$toDetirmine}', '{$toImport}', '{$fromDate}', '{$fromTime}', '{$toDate}', '{$toTime}', '{$title}', '{$content}'
							)";
			
			mysql_query($newAnnouncementQuery, $connDBA);
			header ("Location: index.php?added=announcement");
			exit;
		} else {
			$announcement = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$toDetirmine = $_POST['toDetirmine'];
			$toImport = $_POST['toImport'];
			$fromDate = $_POST['from'];
			$fromTime = $_POST['fromTime'];
			$toDate = $_POST['to'];
			$toTime = $_POST['toTime'];
			$content = mysql_real_escape_string($_POST['content']);
			
		//Ensure times are not inferior if the dates are the same
			if ($fromDate == $toDate && !empty($fromDate) && !empty($toDate)) {
				$id = $_GET['id'];
				$type = $_GET['type'];
				$fromTimeArray = explode(":", $fromTime);
				$toTimeArray = explode(":", $toTime);
				
				if ($fromTime == $toTime) {
					header("Location: manage_announcement.php?id=" . $id . "&type=" . $type . "&message=inferior");
					exit;
				}
				
				if ($toTimeArray[0] < $fromTimeArray[0]) {
					header("Location: manage_announcement.php?id=" . $id . "&type=" . $type . "&message=inferior");
					exit;
				} elseif ($toTimeArray[0] == $fromTimeArray[0]) {
					if ($toTimeArray[1] < $fromTimeArray[1]) {
						header("Location: manage_announcement.php?id=" . $id . "&type=" . $type . "&message=inferior");
						exit;
					}
				}
			}
				
			if (!empty($fromDate)) {
				$editAnnouncementQuery = "UPDATE announcements SET `display` = '{$toDetirmine}', `to` = '{$toImport}', `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}' WHERE `id` = '{$announcement}'";
			} else {
				$editAnnouncementQuery = "UPDATE announcements SET `display` = '{$toDetirmine}', `to` = '{$toImport}', `fromDate` = '{$fromDate}', `fromTime` = '{$fromTime}', `toDate` = '{$toDate}', `toTime` = '{$toTime}', `title` = '{$title}', `content` = '{$content}' WHERE `id` = '{$announcement}'";
			}
			
			mysql_query($editAnnouncementQuery, $connDBA);
			header ("Location: index.php?updated=announcement");
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($announcement)) {
		$title = "Edit the " . stripslashes(htmlentities($announcement['title'])) . " Announcement";
	} else {
		$title =  "Create a New Announcement";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/optionTransfer.js" type="text/javascript"></script>
<script src="../../javascripts/common/datePicker.js" type="text/javascript"></script>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/enableDisable.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../../styles/common/datePicker.css" />
</head>
<body<?php bodyClass(); ?><?php if (isset($_GET['type'])) {if ($_GET['type'] == "users" || $_GET['type'] == "organizations" || $_GET['type'] == "roles") {echo " onload=\"opt.init(document.forms[0])\"";}} ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<?php
//If the type of user is being selected
	if (!isset($_GET['type'])) {
		echo "<h2>Create a New Announcement</h2><p>Select to whom this announcment will go.</p><p>&nbsp;</p><blockquote><p><a href=\"manage_announcement.php?type=users\">Selected Users</a> - Only selected users will recieve this accouncement<br /><a href=\"manage_announcement.php?type=all\">All Users</a> - All registered users will recieve this accouncement<br /><a href=\"manage_announcement.php?type=organizations\">Selected Organizations</a> - All users within selected organizations will recieve this accouncement<br /><a href=\"manage_announcement.php?type=allOrganizations\">All Organizations</a> - All registered organizations will recieve this accouncement<br /><a href=\"manage_announcement.php?type=roles\">Selected Roles</a> - All users with a selected role will recieve this accouncement</p></blockquote>";
	} elseif ($_GET['type'] == ("users" || "all" || "organizations" || "allOrganizations" || "roles")) {
?>
    <h2>
      <?php if (isset ($announcement)) {echo "Edit the \"" . $announcement['title'] . "\" Announcement";} else {echo "Create a New Announcement";} ?>
    </h2>
<p>Use this page to <?php if (isset ($announcement)) {echo "edit the content of \"<strong>" . stripslashes(htmlentities($announcement['title'])) . "</strong>\"";} else {echo "create a new announcement";} ?>.</p>
<?php
//Display error messages
	if (isset($_GET['message']) && $_GET['message'] == "inferior") {
		errorMessage("The start time can not be inferior to or the same as the end time");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
    <form action="manage_announcement.php<?php 
		if (isset ($announcement)) {
			echo "?id=" . $announcement['id'] . "&type=" . $_GET['type'];
		} else {
			echo "?type=" . $_GET['type'];
		}
	?>" method="post" name="manageAnnouncement" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: </p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($announcement)) {
					echo " value=\"" . stripslashes(htmlentities($announcement['title'])) . "\"";
				}
			?> />
          </p>
        </blockquote>
<p>To<?php if (isset($_GET['type'])) {if ($_GET['type'] == "users" || $_GET['type'] == "organizations" || $_GET['type'] == "roles") {echo "<span class=\"require\">*</span>";}} ?>:</p>
        <blockquote>
          <p>
            <?php
          //Grab all required values
              switch ($_GET['type']) {
                  case "users" :                       
                      echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"Selected Users\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential users:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notTo\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\">";
					  $notToUsersGrabber = mysql_query("SELECT * FROM `users` ORDER BY `lastName` ASC", $connDBA);
                      
					  while($users = mysql_fetch_array($notToUsersGrabber)) {
						  if (isset($announcement)) {
							  $toArray = explode(",", $announcement['to']);
							  
							  if (!in_array($users['id'], $toArray)) {
								  echo "<option value=\"" . $users['id'] . "\">" . $users['firstName'] . " " . $users['lastName'] . "</option>";
							  }
						  } else {
							  echo "<option value=\"" . $users['id'] . "\">" . $users['lastName'] . ", " . $users['firstName'] . "</option>";
						  }
                      }
					  
                      echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected users:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\"";
					  
					  if (isset($announcement)) {
						  echo " value=\"" .   $announcement['to'] . "\"";
					  }
					  
					  echo "></div><div align=\"center\"><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\" class=\"validate[required]\">";
					  $toUsersGrabber = mysql_query("SELECT * FROM `users` ORDER BY `lastName` ASC", $connDBA);
					  
					  while($users = mysql_fetch_array($toUsersGrabber)) {
						  if (isset($announcement)) {
							  $toArray = explode(",", $announcement['to']);
							  $toSize = sizeof($toArray);
							  
							  for ($count = 0; $count <= $toSize - 1; $count++) {
								  if ($toArray[$count] == $users['id']) { 
									  echo "<option value=\"" . $users['id'] . "\">" . $users['lastName'] . ", " . $users['firstName'] . "</option>";
								  }
							  }
						  }
					  }
					  
					  echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>"; break;
                      
                  case "all" : echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"All Users\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"all\" /><p><strong>This accouncement will be sent to all registered users.</strong></p>"; break;
                  
                  case "organizations" :                       
                      echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"Selected Organizations\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential organizations:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notToList\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\">";
					  $notToOrganizationsGrabber = mysql_query("SELECT * FROM `organizations` ORDER BY `organization` ASC", $connDBA);
					  
					  while($organizations = mysql_fetch_array($notToOrganizationsGrabber)) {
						  if (isset($announcement)) {
							  $toArray = explode(",", $announcement['to']);
							  
							  if (!in_array($organizations['id'], $toArray)) {
								  echo "<option value=\"" . $organizations['id'] . "\">" . $organizations['organization'] . "</option>";
							  }
						  } else {
							  echo "<option value=\"" . $organizations['id'] . "\">" . $organizations['organization'] . "</option>";
						  }
                      }
					  
                      echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected organizations:</h3><div align=\"center\"><div class=\"collapseElement\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\"";
					  
					  if (isset($announcement)) {
						  echo " value=\"" .   $announcement['to'] . "\"";
					  }
					  
					  echo "></div><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\">";
					  $toOrganizationsGrabber = mysql_query("SELECT * FROM `organizations` ORDER BY `organization` ASC", $connDBA);
					  
					  while($organizations = mysql_fetch_array($toOrganizationsGrabber)) {
						  if (isset($announcement)) {
							  $toArray = explode(",", $announcement['to']);
							  $toSize = sizeof($toArray);
							  
							  for ($count = 0; $count <= $toSize - 1; $count++) {
								  if ($toArray[$count] == $organizations['id']) { 
									  echo "<option value=\"" . $organizations['id'] . "\">" . $organizations['organization'] . "</option>";
								  }
							  }
						  }
					  }
					  
					  echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>"; break;
                      
                  case "allOrganizations" : echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"All Organizations\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"allOrganizations\" /><p><strong>This accouncement will be sent to all registered organizations.</strong></p>"; break;
                  
                  case "roles" : 
                      echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"Selected Roles\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential roles:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notToList\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\">";
					  
					  if (isset($announcement)) {
						  $toArray = explode(",", $announcement['to']);
						  $toSize = sizeof($toArray);
						  
						  if (!in_array("Administrative Assistant", $toArray)) {
							 echo "<option value=\"Administrative Assistant\">Administrative Assistants</option>";
						  }
						  
						  if (!in_array("Instructor", $toArray)) {
							 echo "<option value=\"Instructor\">Instructor</option>";
						  }
						  
						  if (!in_array("Instructorial Assistant", $toArray)) {
							 echo "<option value=\"Instructorial Assisstant\">Instructorial Assisstants</option>";
						  }
						  
						  if (!in_array("Organization Administrator", $toArray)) {
							 echo "<option value=\"Organization Administrator\">Organization Administrators</option>";
						  }
						  
						  if (!in_array("Site Administrator", $toArray)) {
							 echo "<option value=\"Site Administrator\">Site Administrators</option>";
						  }
						  
						  if (!in_array("Site Manager", $toArray)) {
							 echo "<option value=\"Site Manager\">Site Managers</option>";
						  }
						  
						  if (!in_array("Student", $toArray)) {
							 echo "<option value=\"Student\">Students</option>";
						  }
					  } else {
						  echo "<option value=\"Administrative Assistant\">Administrative Assistants</option><option value=\"Instructor\">Instructors</option><option value=\"Instructorial Assisstant\">Instructorial Assisstants</option><option value=\"Organization Administrator\">Organization Administrators</option><option value=\"Site Administrator\">Site Administrators</option><option value=\"Site Manager\">Site Managers</option><option value=\"Student\">Students</option>";
					  }
					  
					  echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected roles:</h3><div class=\"collapseElement\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\"";
					  
					  if (isset($announcement)) {
						  echo " value=\"" .   $announcement['to'] . "\"";
					  }
					  
					  echo "></div><div align=\"center\"><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\">";
					  
					  if (isset($announcement)) {						  
						  if (in_array("Administrative Assistant", $toArray)) {
							 echo "<option value=\"Administrative Assistant\">Administrative Assistant</option>";
						  }
						  
						  if (in_array("Instructor", $toArray)) {
							 echo "<option value=\"Instructor\">Instructor</option>";
						  }
						  
						  if (in_array("Instructorial Assistant", $toArray)) {
							 echo "<option value=\"Instructorial Assisstant\">Instructorial Assisstant</option>";
						  }
						  
						  if (in_array("Organization Administrator", $toArray)) {
							 echo "<option value=\"Organization Administrator\">Organization Administrator</option>";
						  }
						  
						  if (in_array("Site Administrator", $toArray)) {
							 echo "<option value=\"Site Administrator\">Site Administrator</option>";
						  }
						  
						  if (in_array("Site Manager", $toArray)) {
							 echo "<option value=\"Site Manager\">Site Manager</option>";
						  }
						  
						  if (in_array("Student", $toArray)) {
							 echo "<option value=\"Student\">Student</option>";
						  }
					  }
					  
					  echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>"; break;
              }
          ?>
          </p>
        </blockquote>
        <p>Availability:</p>
        <blockquote>
          <p>
            <input name="from" type="text" id="from" readonly="readonly" class="validate[required]"<?php
            	if (isset ($announcement)) {
					echo " value=\"" . stripslashes(htmlentities($announcement['fromDate'])) . "\"";
				}
				
				if (isset ($announcement) && $announcement['fromDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($announcement)) {
					echo " disabled=\"disabled\"";
				}
			?> />
            <select name="fromTime" id="fromTime"<?php if (isset ($announcement) && $announcement['fromTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($announcement)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "12:00") {echo " selected=\"selected\"";} elseif (!isset ($announcement)) {echo " selected=\"selected\"";} elseif ($announcement['fromTime'] == "") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "13:00") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($announcement) && $announcement['fromTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($announcement) && $announcement['fromTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          to 
          <input type="text" name="to" id="to" readonly="readonly"<?php
            	if (isset ($announcement)) {
					echo " value=\"" . stripslashes(htmlentities($announcement['toDate'])) . "\"";
				}
				
				if (isset ($announcement) && $announcement['toDate'] == "") {
					echo " disabled=\"disabled\"";
				} elseif (!isset($announcement)) {
					echo " disabled=\"disabled\"";
				}
			?> />
          <select name="toTime" id="toTime"<?php if (isset ($announcement) && $announcement['toTime'] == "") {echo " disabled=\"disabled\"";} elseif (!isset($announcement)) {echo " disabled=\"disabled\"";} ?>>
            <option value="00:00"<?php if (isset ($announcement) && $announcement['toTime'] == "00:00") {echo " selected=\"selected\"";} ?>>12:00 am</option>
            <option value="00:30"<?php if (isset ($announcement) && $announcement['toTime'] == "00:30") {echo " selected=\"selected\"";} ?>>12:30 am</option>
            <option value="01:00"<?php if (isset ($announcement) && $announcement['toTime'] == "01:00") {echo " selected=\"selected\"";} ?>>1:00 am</option>
            <option value="01:30"<?php if (isset ($announcement) && $announcement['toTime'] == "01:30") {echo " selected=\"selected\"";} ?>>1:30 am</option>
            <option value="02:00"<?php if (isset ($announcement) && $announcement['toTime'] == "02:00") {echo " selected=\"selected\"";} ?>>2:00 am</option>
            <option value="02:30"<?php if (isset ($announcement) && $announcement['toTime'] == "02:30") {echo " selected=\"selected\"";} ?>>2:30 am</option>
            <option value="03:00"<?php if (isset ($announcement) && $announcement['toTime'] == "03:00") {echo " selected=\"selected\"";} ?>>3:00 am</option>
            <option value="03:30"<?php if (isset ($announcement) && $announcement['toTime'] == "03:30") {echo " selected=\"selected\"";} ?>>3:30 am</option>
            <option value="04:00"<?php if (isset ($announcement) && $announcement['toTime'] == "04:00") {echo " selected=\"selected\"";} ?>>4:00 am</option>
            <option value="04:30"<?php if (isset ($announcement) && $announcement['toTime'] == "04:30") {echo " selected=\"selected\"";} ?>>4:30 am</option>
            <option value="05:00"<?php if (isset ($announcement) && $announcement['toTime'] == "05:00") {echo " selected=\"selected\"";} ?>>5:00 am</option>
            <option value="05:30"<?php if (isset ($announcement) && $announcement['toTime'] == "05:30") {echo " selected=\"selected\"";} ?>>5:30 am</option>
            <option value="06:00"<?php if (isset ($announcement) && $announcement['toTime'] == "06:00") {echo " selected=\"selected\"";} ?>>6:00 am</option>
            <option value="06:30"<?php if (isset ($announcement) && $announcement['toTime'] == "06:30") {echo " selected=\"selected\"";} ?>>6:30 am</option>
            <option value="07:00"<?php if (isset ($announcement) && $announcement['toTime'] == "07:00") {echo " selected=\"selected\"";} ?>>7:00 am</option>
            <option value="07:30"<?php if (isset ($announcement) && $announcement['toTime'] == "07:30") {echo " selected=\"selected\"";} ?>>7:30 am</option>
            <option value="08:00"<?php if (isset ($announcement) && $announcement['toTime'] == "08:00") {echo " selected=\"selected\"";} ?>>8:00 am</option>
            <option value="08:30"<?php if (isset ($announcement) && $announcement['toTime'] == "08:30") {echo " selected=\"selected\"";} ?>>8:30 am</option>
            <option value="09:00"<?php if (isset ($announcement) && $announcement['toTime'] == "09:00") {echo " selected=\"selected\"";} ?>>9:00 am</option>
            <option value="09:30"<?php if (isset ($announcement) && $announcement['toTime'] == "09:30") {echo " selected=\"selected\"";} ?>>9:30 am</option>
            <option value="10:00"<?php if (isset ($announcement) && $announcement['toTime'] == "10:00") {echo " selected=\"selected\"";} ?>>10:00 am</option>
            <option value="10:30"<?php if (isset ($announcement) && $announcement['toTime'] == "10:30") {echo " selected=\"selected\"";} ?>>10:30 am</option>
            <option value="11:00"<?php if (isset ($announcement) && $announcement['toTime'] == "11:00") {echo " selected=\"selected\"";} ?>>11:00 am</option>
            <option value="11:30"<?php if (isset ($announcement) && $announcement['toTime'] == "11:30") {echo " selected=\"selected\"";} ?>>11:30 am</option>
            <option value="12:00"<?php if (isset ($announcement) && $announcement['toTime'] == "12:00") {echo " selected=\"selected\"";} ?>>12:00 pm</option>
            <option value="12:30"<?php if (isset ($announcement) && $announcement['toTime'] == "12:30") {echo " selected=\"selected\"";} ?>>12:30 pm</option>
            <option value="13:00"<?php if (isset ($announcement) && $announcement['toTime'] == "12:00") {echo " selected=\"selected\"";} elseif (!isset ($announcement)) {echo " selected=\"selected\"";} elseif ($announcement['toTime'] == "") {echo " selected=\"selected\"";} ?>>1:00 pm</option>
            <option value="13:30"<?php if (isset ($announcement) && $announcement['toTime'] == "13:30") {echo " selected=\"selected\"";} ?>>1:30 pm</option>
            <option value="14:00"<?php if (isset ($announcement) && $announcement['toTime'] == "14:00") {echo " selected=\"selected\"";} ?>>2:00 pm</option>
            <option value="14:30"<?php if (isset ($announcement) && $announcement['toTime'] == "14:30") {echo " selected=\"selected\"";} ?>>2:30 pm</option>
            <option value="15:00"<?php if (isset ($announcement) && $announcement['toTime'] == "15:00") {echo " selected=\"selected\"";} ?>>3:00 pm</option>
            <option value="15:30"<?php if (isset ($announcement) && $announcement['toTime'] == "15:30") {echo " selected=\"selected\"";} ?>>3:30 pm</option>
            <option value="16:00"<?php if (isset ($announcement) && $announcement['toTime'] == "16:00") {echo " selected=\"selected\"";} ?>>4:00 pm</option>
            <option value="16:30"<?php if (isset ($announcement) && $announcement['toTime'] == "16:30") {echo " selected=\"selected\"";} ?>>4:30 pm</option>
            <option value="17:00"<?php if (isset ($announcement) && $announcement['toTime'] == "17:00") {echo " selected=\"selected\"";} ?>>5:00 pm</option>
            <option value="17:30"<?php if (isset ($announcement) && $announcement['toTime'] == "17:30") {echo " selected=\"selected\"";} ?>>5:30 pm</option>
            <option value="18:00"<?php if (isset ($announcement) && $announcement['toTime'] == "18:00") {echo " selected=\"selected\"";} ?>>6:00 pm</option>
            <option value="18:30"<?php if (isset ($announcement) && $announcement['toTime'] == "18:30") {echo " selected=\"selected\"";} ?>>6:30 pm</option>
            <option value="19:00"<?php if (isset ($announcement) && $announcement['toTime'] == "19:00") {echo " selected=\"selected\"";} ?>>7:00 pm</option>
            <option value="19:30"<?php if (isset ($announcement) && $announcement['toTime'] == "19:30") {echo " selected=\"selected\"";} ?>>7:30 pm</option>
            <option value="20:00"<?php if (isset ($announcement) && $announcement['toTime'] == "20:00") {echo " selected=\"selected\"";} ?>>8:00 pm</option>
            <option value="20:30"<?php if (isset ($announcement) && $announcement['toTime'] == "20:30") {echo " selected=\"selected\"";} ?>>8:30 pm</option>
            <option value="21:00"<?php if (isset ($announcement) && $announcement['toTime'] == "21:00") {echo " selected=\"selected\"";} ?>>9:00 pm</option>
            <option value="21:30"<?php if (isset ($announcement) && $announcement['toTime'] == "21:30") {echo " selected=\"selected\"";} ?>>9:30 pm</option>
            <option value="22:00"<?php if (isset ($announcement) && $announcement['toTime'] == "22:00") {echo " selected=\"selected\"";} ?>>10:00 pm</option>
            <option value="22:30"<?php if (isset ($announcement) && $announcement['toTime'] == "22:30") {echo " selected=\"selected\"";} ?>>10:30 pm</option>
            <option value="23:00"<?php if (isset ($announcement) && $announcement['toTime'] == "23:00") {echo " selected=\"selected\"";} ?>>11:00 pm</option>
            <option value="23:30"<?php if (isset ($announcement) && $announcement['toTime'] == "23:30") {echo " selected=\"selected\"";} ?>>11:30 pm</option>
          </select>
          <label><input type="checkbox" name="toggleAvailability" id="toggleAvailability" onclick="flvFTFO1('manageAnnouncement','from,t','fromTime,t','to,t','toTime,t')"<?php
            	if (isset ($announcement) && $announcement['toDate'] != "") {
					echo " checked=\"checked\"";
				}
			?> />Enable</label>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Content</div>
       <div class="stepContent">
        <blockquote>
        <p>Content<span class="require">*</span>: </p>
        <blockquote>
        <p><span id="contentCheck">
            <textarea name="content" id="content1" cols="45" rows="5" style="width:640px; height:320px;" /><?php 
				if (isset ($announcement)) {
					echo stripslashes($announcement['content']);
				}
			?></textarea>
          <span class="textareaRequiredMsg"></span></span>
          </p>
        </blockquote>
        </blockquote>
      </div>
      <div class="catDivider three">Finish</div>
      <div class="stepContent">
	  <blockquote>
      	<p>
          <?php submit("submit", "Submit"); ?>
			<input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
        </p>
          <?php formErrors(); ?>
      </blockquote>
      </div>
    </form>
<?php
	} else {
		header("Location: manage_announcement.php");
		exit;
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
<script type="text/javascript">
<!--
var sprytextarea1 = new Spry.Widget.ValidationTextarea("contentCheck");
//-->
</script>
</body>
</html>
