<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the group exists if group details are requested
	$userData = userData();
	
	if (isset($_GET['id'])) {
		if (exist("groups", "id", $_GET['id'])) {
			$groupData = query("SELECT * FROM `groups` WHERE `id` = '{$_GET['id']}'");
			
			if ($userData['organization'] !== $groupData['organization']) {
				redirect("group.php");
			}
			
			$title = "Group Details";
			$description = "Below are the details for the " . prepare($groupData['name'], false, true) . " group.";
		} else {
			redirect("group.php");
		}
	} else {
		$title = "Groups";
		$description = "Users can be assigned to custom defined groups to enable quick assignments to modules, announcments, and mass emails.";
	}
	
	headers($title, "Instructor,Organization Administrator", false, true);
	
//Delete a group
	delete("groups", "group.php");
	
//Title
	title($title, $description);
	
//Grouping overview
	if (!isset($_GET['id'])) {	
	//Admin toolbar
		echo "<div class=\"toolBar\">";
		
		if (access("manageOrganizationGroups")) {
			echo URL("Add New Group", "manage_group.php", "toolBarItem new");
		}
		
		echo URL("Back to Users", "index.php", "toolBarItem back");
		echo "</div>"; 
		
	//Display message updates
		message("inserted", "group", "success", "The group was created");
		message("updated", "group", "success", "The group was modified");
		
	//Grouping table
		$count = 1;
		
		if (exist("groups", "organization", $userData['organization'])) {
			$groupGrabber = query("SELECT * FROM `groups` WHERE `organization` = '{$userData['organization']}' ORDER BY `id` ASC", "raw");
			
			echo "<table class=\"dataTable\"><tbody><tr><th class=\"tableHeader\" width=\"200\">Name</th><th class=\"tableHeader\">Comments</th><th class=\"tableHeader\"  width=\"400\">Participants</th>";
			
			if (access("manageOrganizationGroups")) {
				echo "<th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th>";
			}
			
			echo "</tr>";
			
			while ($groupData = mysql_fetch_array($groupGrabber)) {
				echo "<tr";
				if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<td width=\"200\">" . URL(commentTrim(20, $groupData['name']), "group.php?id=" . $groupData['id']) . "</td>";
				echo "<td>";
				
				if (!empty($groupData['comments'])) {
					echo commentTrim(50, $groupData['comments']);
				} else {
					echo "<span class=\"notAssigned\">None</span>";
				}
				
				echo "</td>";
				echo "<td width=\"400\">";
				
				$user = "";
				
				foreach (explode(",", $groupData['participants']) as $participant) {
					$participantGrabber = query("SELECT * FROM `users` WHERE `id` = '{$participant}'");
					
					if ($participantGrabber) {
						$user .= $participantGrabber['firstName'] . " " . $participantGrabber['lastName'] . ", ";
					}
				}
					
				
				echo commentTrim(50, rtrim($user, ", ")) . "</td>";
				
				if (access("manageOrganizationGroups")) {
					echo "<td width=\"50\">" . URL(false, "manage_group.php?id=" . $groupData['id'], "action edit", false, "Edit the <strong>" .  prepare($groupData['title'], true, true) . "</strong> group") . "</td>";
					echo "<td width=\"50\">" . URL(false, "group.php?action=delete&id=" . $groupData['id'], "action delete", false, "Delete the <strong>" . prepare($groupData['title'], true, true) . "</strong> group", true) . "</td>";
				}
				
				echo "</tr>";
				
				$count++;
			}
			
			echo "</tbody></table>";
		} else {
			echo "<div class=\"noResults\">This organization does not have any groups.";
			
			if (access("manageOrganizationGroups")) {
				echo " " . URL("Create a group now", "manage_group.php") . ".";
			}
			
			echo "</div>";
		}
//Group details
	} else {
	//Admin toolbar
		echo "<div class=\"toolBar\">";
		
		if (access("manageOrganizationGroups")) {
			echo URL("Edit Group", "manage_group.php?id=" . $_GET['id'], "toolBarItem editTool");
		}
		
		echo URL("Back to Groups", "group.php", "toolBarItem back");
		echo "</div><br />"; 
		
	//Group details
		echo "<blockquote>";
		directions("Name");
		echo "<blockquote><p>";
		echo prepare($groupData['name'], false, true);
		echo "</p></blockquote>";
		
		if (!empty($groupData['comments'])) {
			directions("Comments");
			echo "<blockquote><p>";
			echo prepare($groupData['comments'], false, true);
			echo "</p></blockquote>";
		}
		
		directions("Participants");
		echo "<blockquote><p>";
		
		foreach (explode(",", $groupData['participants']) as $participant) {
			$participantGrabber = query("SELECT * FROM `users` WHERE `id` = '{$participant}'");
			
			if ($participantGrabber) {
				$user .= URL($participantGrabber['firstName'] . " " . $participantGrabber['lastName'], "profile.php?id=" . $participant) . "<br />";
			}
		}
			
		echo rtrim($user, "<br />");
		echo "</p></blockquote></blockquote>";
	}
	
//Include the footer
	footer();
?>