<?php
/*
LICENSE: See "license.php" located at the root installation

This is the home page for logged in users, which displays information relavent to the user.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
	headers("Home", "portlet", true);
	
//Title
	title("Home", "Welcome to your customized portal. This page contains a quick reference to major information relevent to your account. Major parts of this site can be accessed by navigating the links above.");
	
//Locate and load all the addon plugins for this page
	$addons = query("SELECT * FROM `addons` WHERE `portalPlugin` = '1' ORDER BY `position` ASC", "raw");
	
	if ($addons) {
		while($addon = fetch($addons)) {
		//Use output buffering to see if the included script generated any content
			ob_start();
			include("addons/" . $addon['pluginRoot'] . "index.php");
			$output = ob_get_clean();
			
		//Only display this plugin if output was generated
			if (!empty($output)) {
				echo "<div class=\"addon\" style=\"background-image:url(addons/" . $addon['pluginRoot'] . "icon.png)\">\n";
				echo "<h3>" . $addon['name'] . "</h3>\n";
				echo $output;
				echo "\n</div>\n";
			}
		}
	}
	
//A function to calculate the number of users in a particular system

	/*
		echo preg_replace("/<title>(.*)<\/title>/", "<title>Replace with me</title>", "<title>SoMe CoNeNt</title>");
	function userCount($role, $type) {
		global $connDBA;	
		
		if ($type == true) {
			$sql = " AND `organization` = '{$type}'";
		} else {
			$sql = "";
		}
		
		$userGrabber = mysql_query("SELECT * FROM `users` WHERE `role` = '{$role}'{$sql}", $connDBA);
		$userNumber = mysql_num_rows($userGrabber);
		return "<strong>" . $userNumber . "</strong>";
	}
	
	function users($type = false) {
		global $connDBA;
		
		//Count all users
		if ($type == true) {
			$sql = " WHERE `organization` = '{$type}'";
		} else {
			$sql = "";
		}
		
		$userGrabber = mysql_query("SELECT * FROM `users`{$sql}", $connDBA);
		$userNumber = mysql_num_rows($userGrabber);
		
		//Construct the box
		$content = "Number of registered users:<br /><ul>";
			if (!is_numeric($type)) {
				$content .= "<li>Site Admin: " . userCount("Site Administrator", $type) . "</li>
				<li>Site Managers: " . userCount("Site Manager", $type) . "</li>";
			}
			
		$content .= "<li>Organization Admin: " . userCount("Organization Administrator", $type) . "</li>
			<li>Admin Assistants: " . userCount("Administrative Assistant", $type) . "</li>
			<li>Instructors: " . userCount("Instructor", $type) . "</li>
			<li>Instructorial Assisstants: " . userCount("Instructorial Assisstant", $type) . "</li>
			<li>Students: " . userCount("Student", $type) . "</li>
		</ul>
		<hr />
		Total Users: <strong>" . $userNumber . "</strong>";
		
		sideBox("Registered Users", "Custom Content", $content);
	}
	
//A function to display the active users in a particular system
	function active($type = false) {
		global $connDBA;
		
		//Grab correct users
		if ($type == true) {
			$sql = " AND `organization` = '{$type}'";
		} else {
			$sql = "";
		}
		
		$currentTime = time();
		$activityTime = time() - 1800;
		$activeCheck = mysql_query("SELECT * FROM `users` WHERE `active` BETWEEN '{$activityTime}' AND '{$currentTime}'{$sql} ORDER BY `lastName` ASC", $connDBA);
		$count = 0;
		
		if (mysql_fetch_array($activeCheck)) {
			$activeGrabber = mysql_query("SELECT * FROM `users` WHERE `active` BETWEEN '{$activityTime}' AND '{$currentTime}'{$sql} ORDER BY `lastName` ASC", $connDBA);
			
			$content = "<div style=\"max-height:250px; overflow:auto;\"><p>Active users within the last 30 min.</p><ul>";
			
			while($activeUsers = mysql_fetch_array($activeGrabber)) {
				$content .= "<li>" . $activeUsers['firstName'] . " " . $activeUsers['lastName'] . "</li>";
				$count++;
			}
			
			$content .= "</ul></div><hr />Total Active Users: <strong>" . $count . "</strong>";
		} else {
			$content = "<div align=\"center\"><p><i>None</i></p></div>";
		}
		
		sideBox("Active Users", "Custom Content", $content);
	}
	
//Display annoumcements
	$userData = userData();
	$table = "announcements_" . $userData['organization'];
	$announcementsCheck = mysql_query("(SELECT * FROM `announcements_0`) UNION (SELECT * FROM `{$table}`)", $connDBA);
	
	if (mysql_fetch_array($announcementsCheck)) {		
		$limit = 1;
		$time = getdate();
		
		if (0 < $time['minutes'] && $time['minutes'] < 9) {
			$minutes = "0" . $time['minutes'];
		} else {
			$minutes = $time['minutes'];
		}
		
		$currentTime = $time['hours'] . ":" . $minutes;
		$currentDate = strtotime($time['mon'] . "/" . $time['mday'] . "/" . $time['year'] . " " . $currentTime);
		$userName = $_SESSION['MM_Username'];
		$role = $_SESSION['MM_UserGroup'];
		$userDataGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}'", $connDBA);
		$userData = mysql_fetch_array($userDataGrabber);
		$announcementsGrabber = mysql_query("(SELECT * FROM `announcements_0` ORDER BY `position` ASC) UNION (SELECT * FROM `{$table}` ORDER BY `position` ASC)", $connDBA);
		
		function announcements() {
			global $limit;
			global $userData;
			global $role;
			global $announcements;
			
			switch($announcements['display']) {
				case "Selected Users" : 
					$toArray = explode(",", $announcements['to']);
					$toSize = sizeof($toArray);
					
					for ($count = 0; $count <= $toSize - 1; $count++) {
						if ($toArray[$count] == $userData['id']) {
							if ($limit++ == 1) {
								echo "<p class=\"homeDivider\">Announcements</p>";
							}
							
							echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . prepare($announcements['title'], false, true) . "</p>" . prepare($announcements['content'], false, true) . "</div>";
						}
					} break;
					
				case "All Users" : 
					if ($limit++ == 1) {
						echo "<p class=\"homeDivider\">Announcements</p>";
					}
							
					echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . prepare($announcements['title'], false, true) . "</p>" . prepare($announcements['content'], false, true) . "</div>";
					break;
						
				case "Selected Organizations" : 
					$toArray = explode(",", $announcements['to']);
					$toSize = sizeof($toArray);
					
					for ($count = 0; $count <= $toSize - 1; $count++) {							
						if ($toArray[$count] == $userData['organization']) {
							if ($limit++ == 1) {
								echo "<p class=\"homeDivider\">Announcements</p>";
							}
							
							echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . prepare($announcements['title'], false, true) . "</p>" . prepare($announcements['content'], false, true) . "</div>";
						}
					} break;
					
				case "All Organizations" : 
					if ($userData['organization'] != "0") {
						if ($limit++ == 1) {
							echo "<p class=\"homeDivider\">Announcements</p>";
						}
						
						echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . prepare($announcements['title'], false, true) . "</p>" . prepare($announcements['content'], false, true) . "</div>";
					} break;
					
				case "Selected Roles" : 
					$toArray = explode(",", $announcements['to']);
					$toSize = sizeof($toArray);
					
					for ($count = 0; $count <= $toSize - 1; $count++) {							
						if ($toArray[$count] == $role) {
							if ($limit++ == 1) {
								echo "<p class=\"homeDivider\">Announcements</p>";
							}
							
							echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . prepare($announcements['title'], false, true) . "</p>" . prepare($announcements['content'], false, true) . "</div>";
						}
					} break;
			}
		}
		
		while($announcements = mysql_fetch_array($announcementsGrabber)) {	
			if (($announcements['visible'] == "on" || $announcements['fromDate'] != "") || ($announcements['visible'] == "on" && $announcements['fromDate'] != "")) {				
				$from = strtotime($announcements['fromDate'] . " " . $announcements['fromTime']);
				$to = strtotime($announcements['toDate'] . " " . $announcements['toTime']);
				
				if ($announcements['fromDate'] != "") {
					if ($from > $currentDate) {
						//Do nothing, this will display at a later time
					} elseif ($to <= $currentDate) {
						//Do nothing, this has expired
					} else {
						announcements();
					}
				} else {
					announcements();
				}
			}
		}
	}

	switch($_SESSION['MM_UserGroup']) {
		case "Site Administrator" : 
		//Site layout
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Site Data</p><div class=\"layoutControl\"><div class=\"contentLeft\">";
			
		//Render the chart
			chart("line", "overall");
		
		//Site layout
		   echo "</div><div class=\"dataRight\">";
		   
		//Select all users from the site
			users();
			  
		//Show active users
			active();
			
		//Close the site layout
			echo "</div></div>";
			break;
		
		case "Organization Administrator" : 
		//Site layout
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Organization Data</p><div class=\"layoutControl\"><div class=\"contentLeft\">";
			
		//Render the chart
			chart("line", "overall");
		
		//Site layout
		   	echo"</div><div class=\"dataRight\">";
		   
		//Select all users from the site
			users($userData['organization']);
			  
		//Show active users
			active($userData['organization']);
			
		//Close the site layout
			echo "</div></div>";
			break;
			
		case "Instructor" : 
		//Site layout
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Organization Data</p><div class=\"layoutControl\"><div class=\"contentLeft\">";
			
		//Render the chart
			chart("stacked2D", "assignedUsers", false, "200");
		
		//Site layout
		   	echo"</div><div class=\"dataRight\">";
		   
		//Select all users from the site
			users($userData['organization']);
			  
		//Show active users
			active($userData['organization']);
			
		//Close the site layout
			echo "</div></div>";
			break;
		
		case "Student" :
		//Grab the user specific data
			$userInfo = userData();
			
		//Category divider
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Account Data</p>";
			
		if (!empty($userInfo['modules']) && is_array(unserialize($userInfo['modules']))) {
		//Render the chart
			chart("bar2D", "account", false, "200");
			
		//Display module statistics
			echo "<p>Information on modules you are currently enrolled:</p><ul>";
			
			foreach (unserialize($userInfo['modules']) as $key => $value) {
				$moduleData = query("SELECT * FROM `moduledata` WHERE `id` = '{$key}'");
				
				echo "<li class=\"";
				
				if ($value['moduleStatus'] == "F" && $value['testStatus'] == "F") {
					echo "completed";
				} elseif ($value['moduleStatus'] == "C" && $value['testStatus'] == "C") {
					echo "notStarted";
				} else {
					echo "inProgress";
				}
				
				if ($value['moduleStatus'] == "F") {
					$moduleStatus = "Completed";
				} elseif ($value['moduleStatus'] == "C") {
					$moduleStatus = "Not Started";
				} else {
					$moduleStatus = "In Progress";
				}
				
				if ($value['testStatus'] == "F") {
					$testStatus = "Completed";
				} elseif ($value['testStatus'] == "C") {
					$testStatus = "Not Started";
				} else {
					$testStatus = "In Progress";
				}
				
				echo "\">" . tip("<strong>Lesson Progress</strong> - " . $moduleStatus . "<br /><strong>Test Progress</strong> - " . $testStatus,  URL($moduleData['name'], "../modules/lesson.php?id=" . $moduleData['id']));
				echo "</li>";
			}
			
			echo "</ul>";
		} else {
			echo "<div class=\"spacer\"><p>You are not currently enrolled in any modules. " . URL("Browse the list of modules", "../modules/index.php") . ", and enroll in the one's you choose.</p></div>";
		}
	}*/

//Include the footer
	footer();
?>