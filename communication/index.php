<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Communication", "Organization Administrator,Site Administrator", "liveSubmit,customVisible", true); 

//Reorder announcements	
	reorder("announcements", "index.php");

//Set announcement avaliability
	avaliability("announcements", "index.php");
	
//Delete an announcement
	delete("announcements", "index.php");
	
//Title
	title("Communication", "Communication can be established to registered users and organizations via announcements and mass emails.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Create Announcement", "manage_announcement.php", "toolBarItem announcementLink");
	echo URL("Send Mass Email", "send_email.php", "toolBarItem email");
	echo "</div>";
	
//Display message updates
	message("inserted", "announcement", "success", "The annoumcement was successfully added");
	message("updated", "announcement", "success", "The annoumcement was successfully updated");
	message("email", "success", "success", "The email was successfully sent");

//Announcements table
	if (exist("announcements") == true) {
		$announcementGrabber = mysql_query("SELECT * FROM `announcements` ORDER BY `position` ASC", $connDBA);
		$currentDate = strtotime(date("m/d/y g:i a"));
		
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\" width=\"200\">Display To</th><th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
	
		while($announcementData = mysql_fetch_array($announcementGrabber)) {
			echo "<tr";
			if ($announcementData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			
			if ($announcementData['fromDate'] != "") {
				$from = strtotime($announcementData['fromDate'] . " " . $announcementData['fromTime']);
				$to = strtotime($announcementData['toDate'] . " " . $announcementData['toTime']);
				$fromTimeArray = explode(":", $announcementData['fromTime']);
				$toTimeArray = explode(":", $announcementData['toTime']);
				
				if ($fromTimeArray['0'] == "00") {
					$showTime = "12:" . $fromTimeArray['1'] . " am";
				} elseif (01 <= $fromTimeArray['0'] &&  $fromTimeArray['0'] <= 11) {
					$showTime = $fromTimeArray['0'] . ":" . $fromTimeArray['1'] . " am";
				} elseif ($fromTimeArray['0'] == "12") {
					$showTime = "12:" . $toTimeArray['1'] . " pm";
				} else {
					$showTime = $fromTimeArray['0'] - 12 . ":" . $fromTimeArray['1'] . " pm";
				}
				
				if ($toTimeArray['0'] == "00") {
					$expiredTime = "12:" . $toTimeArray['1'] . " am";
				} elseif (01 <= $toTimeArray['0'] &&  $toTimeArray['0'] <= 11) {
					$expiredTime = $toTimeArray['0'] . ":" . $toTimeArray['1'] . " am";
				} elseif ($toTimeArray['0'] == "12") {
					$expiredTime = "12:" . $toTimeArray['1'] . " pm";
				} else {
					$expiredTime = $toTimeArray['0'] - 12 . ":" . $toTimeArray['1'] . " pm";
				}
							
				if ($from > $currentDate) {
					echo "<td width=\"25\"><span onmouseover=\"Tip('This announcement will display on <strong>" . $announcementData['fromDate'] . " at " . $showTime . "</strong>')\" onmouseout=\"UnTip()\" class=\"action upcoming\"></span></td>";
				} elseif ($to <= $currentDate) {
					echo "<td width=\"25\"><span onmouseover=\"Tip('This announcement has expired.<br />It was last visible on <strong>" . $announcementData['toDate'] . " at " . $expiredTime . "</strong>.')\" onmouseout=\"UnTip()\" class=\"action expired\"></span></td>";
				} else {
					echo "<td width=\"25\"><span onmouseover=\"Tip('This announcement is currently being displayed')\" onmouseout=\"UnTip()\" class=\"action current\"></span></td>";
				}
			} else {
				echo "<td width=\"25\">"; option($announcementData['id'], $announcementData['visible'], "announcementData", "visible"); echo "</td>";
			}
			
			echo "<td width=\"75\">"; reorderMenu($announcementData['id'], $announcementData['position'], "announcementData", "announcements"); echo "</td>";
			
			echo "<td width=\"200\">" . $announcementData['display'] . "</td>";
			echo "<td width=\"200\">" . commentTrim(30,$announcementData['title']) . "</td>";
			echo "<td>" . commentTrim(60, $announcementData['content']) . "</td>";
						
			switch($announcementData['display']) {
				case "Selected Users" : $URL = "users"; break;
				case "All Users" : $URL = "all"; break;
				case "Selected Organizations" : $URL = "organizations"; break;
				case "All Organizations" : $URL = "allOrganizations"; break;
				case "Selected Roles" : $URL = "roles"; break;
			}
			
			echo "<td width=\"50\">" . URL("", "manage_announcement.php?id=" . $announcementData['id'] . "&type=" . $URL, "action edit", false, "Edit the <strong>" . $announcementData['title'] . "</strong> announcement") . "</td>"; 
			echo "<td width=\"50\">" . URL("", "index.php?action=delete&id=" . $announcementData['id'], "action delete", false, "Delete the <strong>" . $announcementData['title'] . "</strong> announcement", true) . "</td>";
			}
		echo "</tr></tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no announcements. <a href=\"manage_announcement.php\">Create a new announcement now</a>.</div>";
	 } 
	 
//Include the footer
	footer();
?>
