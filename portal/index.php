<?php
//Header functions
	require_once('../system/connections/connDBA.php');	
	headers("Home", "Student,Site Administrator");
	
//Title
	switch ($_SESSION['MM_UserGroup']) {
		case "Site Administrator" :
			$title = "Welcome to the administration home page. This page contains a quick reference to major information about this site. Major parts of this site can be administered by navigating the links above."; break;
		case "Student" : 
			$title = "Welcome to your customized portal. This page contains a quick reference to major information relevent to your account. Major parts of this site can be accessed by navigating the links above."; break;
	}
	
	title("Home", $title, false);
	
//Display annoumcements
	$announcementsCheck = mysql_query("SELECT * FROM `announcements`", $connDBA);
	
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
		$announcementsGrabber = mysql_query("SELECT * FROM `announcements` ORDER BY `position` ASC", $connDBA);
		
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
							
							echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . $announcements['title'] . "</p>" . $announcements['content'] . "</div>";
						}
					} break;
					
				case "All Users" : 
					if ($limit++ == 1) {
						echo "<p class=\"homeDivider\">Announcements</p>";
					}
							
					echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . $announcements['title'] . "</p>" . $announcements['content'] . "</div>";
					break;
						
				case "Selected Organizations" : 
					$toArray = explode(",", $announcements['to']);
					$toSize = sizeof($toArray);
					
					for ($count = 0; $count <= $toSize - 1; $count++) {							
						if ($toArray[$count] == $userData['organization']) {
							if ($limit++ == 1) {
								echo "<p class=\"homeDivider\">Announcements</p>";
							}
							
							echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . $announcements['title'] . "</p>" . $announcements['content'] . "</div>";
						}
					} break;
					
				case "All Organizations" : 
					if ($userData['organization'] != "1") {
						if ($limit++ == 1) {
							echo "<p class=\"homeDivider\">Announcements</p>";
						}
						
						echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . $announcements['title'] . "</p>" . $announcements['content'] . "</div>";
					} break;
					
				case "Selected Roles" : 
					$toArray = explode(",", $announcements['to']);
					$toSize = sizeof($toArray);
					
					for ($count = 0; $count <= $toSize - 1; $count++) {							
						if ($toArray[$count] == $role) {
							if ($limit++ == 1) {
								echo "<p class=\"homeDivider\">Announcements</p>";
							}
							
							echo "<div class=\"announcementContent\"><p class=\"announcementTitle\">" . $announcements['title'] . "</p>" . $announcements['content'] . "</div>";
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
		   echo"</div><div class=\"dataRight\">";
		   
		//Select all users from the site
			  //Find the number of specific users
			  function userCount($role) {
				  global $connDBA;
				  $userGrabber = mysql_query("SELECT * FROM `users` WHERE `role` = '{$role}'", $connDBA);
				  $userNumber = mysql_num_rows($userGrabber);
				  return "<strong>" . $userNumber . "</strong>";
			  }
			  
			  //Count all users
			  $userGrabber = mysql_query("SELECT * FROM users", $connDBA);
			  $userNumber = mysql_num_rows($userGrabber);
			  
			  //Construct the box
			  $content = "Number of registered users:<br /><ul>
				<li>Site Admin: " . userCount("Site Administrator") . "</li>
				<li>Site Managers: " . userCount("Site Manager") . "</li>
				<li>Organization Admin: " . userCount("Organization Administrator") . "</li>
				<li>Admin Assistants: " . userCount("Administrative Assistant") . "</li>
				<li>Instructors: " . userCount("Instructor") . "</li>
				<li>Instructorial Assisstants: " . userCount("Instructorial Assisstant") . "</li>
				<li>Students: " . userCount("Student") . "</li>
			  </ul>
			  <hr />
			  Total Users: <strong>" . $userNumber . "</strong>";
			  
			  sideBox("Registered Users", "Custom Content", $content);
			  
		//Show active users
			//Select all active users from the site
			$currentTime = time();
			$activityTime = time() - 1800;
			$activeCheck = mysql_query("SELECT * FROM `users` WHERE `active` BETWEEN '{$activityTime}' AND '{$currentTime}' ORDER BY `lastName` ASC", $connDBA);
			$count = 0;
			
			if (mysql_fetch_array($activeCheck)) {
				$activeGrabber = mysql_query("SELECT * FROM `users` WHERE `active` BETWEEN '{$activityTime}' AND '{$currentTime}' ORDER BY `lastName` ASC", $connDBA);
				
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
			
		//Close the site layout
			echo "</div></div>";
			break;
		
		case "Student" :
		//Grab the user specific data
			$userInfo = userData();
			
		//Category divider
			echo "<p>&nbsp;</p><p class=\"homeDivider\">Account Data</p>";
			
		if (!empty($userInfo['modules'])) {
		//Render the chart
			chart("line", "account");
			break;
		} else {
			echo "<div class=\"spacer\"><p>You are not currently enrolled in any modules. " . URL("Browse the list of modules", "../modules/index.php") . ", and enroll in the one's you choose.</p></div>";
		}
	}

//Include the footer
	footer();
?>