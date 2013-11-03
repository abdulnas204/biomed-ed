<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Communication", "Site Administrator", "liveSubmit,customVisible", true); 

//Reorder pages	
	reorder("pages", "index.php");

//Set page avaliability
	avaliability("pages", "index.php");
	
//Delete a page
	delete("pages", "index.php");
	
//Title
	title("Communication", "Communication can be established to registered users and organizations via announcements and mass emails.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Create Announcement", "manage_announcement.php", "toolBarItem announcementLink");
	echo URL("Send Mass Email", "send_email.php", "toolBarItem email");
	echo "</div>";
	
//Display message updates
	message("added", "announcement", "success", "The annoumcement was successfully added");
	message("updated", "announcement", "success", "The annoumcement was successfully updated");
	message("email", "success", "success", "The email was successfully sent");
	message("updated", "icon", "success", "The browser icon was successfully updated. It may take a few moments to update across the system.");
	message("updated", "siteInfo", "success", "The site information was successfully updated");
	message("updated", "page", "theme", "The theme was successfully updated");

//Announcements table
	if (exist("announcements") == true) {
		$announcementGrabber = mysql_query("SELECT * FROM `announcements` ORDER BY `position` ASC", $connDBA);
		$currentDate = date("m/d/y g:i a");
		
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
				echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $announcementData['id'] . "\" class=\"visible"; if ($announcementData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $announcementData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $announcementData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($announcementData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
			}
			
			echo "<td width=\"75\"><form name=\"announcements\" action=\"index.php\"><input type=\"hidden\" name=\"id\" value=\"" . $announcementData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $announcementData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
			
			$announcementCount = mysql_num_rows($announcementGrabber);
			for ($count=1; $count <= $announcementCount; $count++) {
				echo "<option value=\"{$count}\"";
				if ($announcementData ['position'] == $count) {
					echo " selected=\"selected\"";
				}
				echo ">" . $count . "</option>";
			}
			
			echo "</select></form></td><td width=\"200\">" . $announcementData['display'] . "</td>";
			echo "<td width=\"200\">" . stripslashes($announcementData['title']) . "</td>";
			echo "<td>" . commentTrim(60, $announcementData['content']) . "</td>";
			echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_announcement.php?id=" . $announcementData['id'] . "";
			
			switch($announcementData['display']) {
				case "Selected Users" : echo "&type=users"; break;
				case "All Users" : echo "&type=all"; break;
				case "Selected Organizations" : echo "&type=organizations"; break;
				case "All Organizations" : echo "&type=allOrganizations"; break;
				case "Selected Roles" : echo "&type=roles"; break;
			}
			
			echo "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($announcementData['title']) . "</strong> announcement')\" onmouseout=\"UnTip()\"></a></td>"; 
			echo "<td width=\"50\"><a class=\"action delete\" href=\"index.php?action=delete&announcement=" . $announcementData['position'] . "&id=" . $announcementData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($announcementData['title']) . "</strong> announcement')\" onmouseout=\"UnTip()\"></a></td>";
			}
		echo "</tr></tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no announcements. <a href=\"manage_announcement.php\">Create a new announcement now</a>.</div>";
	 } 
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
