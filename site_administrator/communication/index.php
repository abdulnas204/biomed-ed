<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Check to see if announcements exist
	$announcementCheck = mysql_query("SELECT * FROM announcements WHERE `position` = 1", $connDBA);
	if (mysql_fetch_array($announcementCheck)) {
		$announcementGrabber = mysql_query("SELECT * FROM announcements ORDER BY position ASC", $connDBA);
	} else {
		$announcementGrabber = 0;
	}

//Reorder announcements	
	if (isset ($_GET['action']) && $_GET['action'] == "modifySettings" && isset($_GET['id']) && isset($_GET['position']) && isset($_GET['currentPosition'])) {
	//Grab all necessary data	
	  //Grab the id of the moving item
	  $id = $_GET['id'];
	  //Grab the new position of the item
	  $newPosition = $_GET['position'];
	  //Grab the old position of the item
	  $currentPosition = $_GET['currentPosition'];
		  
	  //Do not process if item does not exist
	  //Get item name by URL variable
	  $getannouncementID = $_GET['position'];
  
	  $announcementCheckGrabber = mysql_query("SELECT * FROM announcements WHERE position = {$getannouncementID}", $connDBA);
	  $announcementCheckArray = mysql_fetch_array($announcementCheckGrabber);
	  $announcementCheckResult = $announcementCheckArray['position'];
		   if (isset ($announcementCheckResult)) {
			   $announcementCheck = 1;
		   } else {
			  $announcementCheck = 0;
		   }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE announcements SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE announcements SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE announcements SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE announcements SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
		}
	}

//Set announcement avaliability
	if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
		$id = $_POST['id'];
		
		if (!$_POST['option']) {
			$option = "";
		} else {
			$option = $_POST['option'];
		}
		
		$setAvaliability = "UPDATE announcements SET `visible` = '{$option}' WHERE id = '{$id}'";
		mysql_query($setAvaliability, $connDBA);
		
		header ("Location: index.php");
		exit;
	}
	
//Delete an announcement
	if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['announcement']) && isset($_GET['id'])) {
		//Do not process if announcement does not exist
		//Get announcement name by URL variable
		$getannouncementID = $_GET['announcement'];
	
		$announcementCheckGrabber = mysql_query("SELECT * FROM announcements WHERE position = {$getannouncementID}", $connDBA);
		$announcementCheckArray = mysql_fetch_array($announcementCheckGrabber);
		$announcementCheckResult = $announcementCheckArray['position'];
			 if (isset ($announcementCheckResult)) {
				 $announcementCheck = 1;
			 } else {
				$announcementCheck = 0;
			 }
	 
		if (!isset ($_GET['id']) || $_GET['id'] == 0 || $announcementCheck == 0) {
			header ("Location: index.php");
			exit;
		} else {
			$deleteannouncement = $_GET['id'];
			$announcementLift = $_GET['announcement'];
			
			$announcementPositionGrabber = mysql_query("SELECT * FROM announcements WHERE position = {$announcementLift}", $connDBA);
			$announcementPositionFetch = mysql_fetch_array($announcementPositionGrabber);
			$announcementPosition = $announcementPositionFetch['position'];
			
			$otherannouncementsUpdateQuery = "UPDATE announcements SET position = position-1 WHERE position > '{$announcementPosition}'";
			$deleteannouncementQueryResult = mysql_query($otherannouncementsUpdateQuery, $connDBA);
			
			$deleteannouncementQuery = "DELETE FROM announcements WHERE id = {$deleteannouncement}";
			$deleteannouncementQueryResult = mysql_query($deleteannouncementQuery, $connDBA);
			
			header ("Location: index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Communication"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
</head>

<body>
<?php tooltip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Communication</h2>
<p>Communication can be established to registered users and organizations via announcements and mass emails.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem announcementLink" href="manage_announcement.php">Create Announcement</a><a class="toolBarItem email" href="send_email.php">Send Mass Email</a></div>
<?php 
	if (isset ($_GET['added']) && $_GET['added'] == "announcement") {successMessage("The annoumcement was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "announcement") {successMessage("The annoumcement was successfully updated");}
	if (!isset ($_GET['updated']) && !isset ($_GET['added'])) {echo "<br />";}
?>
<?php
//Table header, only displayed if announcements exist.
	if ($announcementGrabber !== 0) {
	//Provide some data for the time tracker
		$time = getdate();
		
		if (0 < $time['minutes'] && $time['minutes'] < 9) {
			$minutes = "0" . $time['minutes'];
		} else {
			$minutes = $time['minutes'];
		}
		
		$currentTime = $time['hours'] . ":" . $minutes;
		$currentDate = strtotime($time['mon'] . "/" . $time['mday'] . "/" . $time['year'] . " " . $currentTime);
		
	echo "<table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\" width=\"200\">Display To</th><th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
	//Loop through each announcement.
		while($announcementData = mysql_fetch_array($announcementGrabber)) {
			echo "<tr";
		//Alternate the color of each row.
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
