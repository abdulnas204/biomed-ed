<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: November 28th, 2010
Last updated: February 13th, 2010

This is the overview page for the learning units in this 
system.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Check to see which JavaScripts are required, depending on user privileges
	if (access("Edit Learning Unit")) {
		$functions = "livesubmit,customVisible";
	} else {
		$functions = "";
	}
	
//Top content
	headers($name, $functions, true);
	
//Set learning unit avaliability
	avaliability("learningunits", "index.php", "Edit Learning Unit");
	
//Delete a learning unit
	if (isset($_GET['id'])) {
		delete("learningunits", "index.php", "Delete Learning Unit", false, false, $_GET['id'], "lesson_{$_GET['id']},test_{$_GET['id']}");
	}

//Forward to editor
	if (isset ($_GET['id']) && $_GET['edit'] == "true") {
		$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$_GET['id']}'");
		
		if (exist("learningunits", "id", $_GET['id'])) {
			if ($unitData['organization'] == $userData['organization']) {
				$_SESSION['currentUnit'] = $unitData['id'];
				$_SESSION['review'] = "review";
				
				redirect("wizard/lesson_settings.php");
			} else {
				redirect("overview.php?id=" . $_GET['id']);
			}
		} else {
			redirect($_SERVER['PHP_SELF']);
		}
	}
	
//Unset active sessions
	unset($_SESSION['currentUnit'], $_SESSION['review']);
	
//Title
	title($name, "Below is a list of all learning units.");
	
	if (loggedIn() && (access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit"))) {
		$additionalSQL = "";
	} else {
		$additionalSQL = " WHERE `visible` = 'on'";
	}
	
	$dataGrabber = query("SELECT * FROM `learningunits`{$additionalSQL} ORDER BY `id` ASC", "raw"); 
	
//Admin toolbar
	if (access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit", "Create Question Bank Questions", "Edit Question Bank Questions", "Delete Question Bank Questions", "Create Feedback Questions", "Edit Feedback Questions", "Delete Feedback Questions", "View Grades", "Assign Users to Learning Unit")) {
		echo "<div class=\"toolBar\">\n";
		echo toolBarURL("Add New Module", "wizard/index.php", "toolBarItem new", false, "Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit");
		echo toolBarURL("Question Bank", "question_bank/index.php", "toolBarItem bank", false, "Create Question Bank Questions", "Edit Question Bank Questions", "Delete Question Bank Questions");
		echo toolBarURL("Feedback", "feedback/index.php", "toolBarItem feedback", false, "Create Feedback Questions", "Edit Feedback Questions", "Delete Feedback Questions");
		echo toolBarURL("View Grades", "gradebook/index.php", "toolBarItem bank", false, "View Grades");
		echo toolBarURL("View Billing History", "billing/index.php", "toolBarItem billing", false, "View Own Billing History");
		echo toolBarURL("Manage Lesson Plan", "planner/index.php", "toolBarItem calendar", false, "Manage Own Lesson Plan");
		echo toolBarURL("Assign Users", "assign/index.php", "toolBarItem user", false, "Assign Users to Learning Unit");
		echo "</div>\n<br />\n";
	}
	
//Learning units table
	if ((access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit") && exist("learningunits")) || (!access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit") && exist("learningunits", "visible", "on"))) {
		$organization = $userData['organization'];
		$count = 1;
		$units = array();
		
		if (access("Purchase Learning Unit")) {
			echo form("purchase", false, false, "enroll/cart.php");
		}
		
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("", false, "Edit Learning Unit");
		echo column("Name", "200");
		echo column("Comments");
		echo column("Statistics", "50", "Access Learning Unit Statistics");
		echo column("Edit", "50", "Edit Learning Unit");
		
		if (exist("learningunits", "organization", $organization)) {
			echo column("Delete", "50", "Delete Learning Unit");
		}
		
		echo column("Purchase", "100", "Purchase Learning Unit");
		echo "</tr>\n";
		
		while ($data = fetch($dataGrabber)) {			
			echo "<tr";
			if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			
			if (!exist("lesson_" . $data['id'], "position", "1")) {
				echo cell("<div align=\"center\">" . tip("There isn't any lesson content to this learning <br />unit. Please add content before displaying.", false, "noShow") . "</div>", "25", "Edit Learning Unit");
			} else {
				echo option("learningunits", $data['id'], false, false, "Edit Learning Unit");
			}
			
			echo preview(commentTrim(30, $data['name']), "lesson.php?id=" . $data['id'], "lesson", "200");
			echo cell(commentTrim(80, $data['comments']));
			echo statsURL("statistics/index.php?period=overall&id=" . $data['id'], $data['name'], false, "Access Learning Unit Statistics");
			
			if (access("Edit Learning Unit")) {
				if (access("Edit Unowned Learning Units")) {
					echo editURL("index.php?edit=true&id=" . $data['id'], $data['name'], "lesson", false, "Edit Learning Unit");
				} else {
					if ($data['locked'] == "0") {
						echo editURL("index.php?edit=true&id=" . $data['id'], $data['name'], "lesson", false, "Edit Learning Unit");
					} else {
						echo cell(tip("This item cannot be edited", false, "action noEdit"), "50", "Edit Learning Unit");
					}
				}
			}
			
			if (exist("learningunits", "organization", $organization) && $data['organization'] == $organization) {
				echo deleteURL("index.php?action=delete&id=" . $data['id'], $data['name'], "lesson", false, false, "Delete Learning Unit");
			} elseif (exist("learningunits", "organization", $organization)) {
				echo cell(tip("This item cannot be deleted", false, "action noDelete"), "50", "Delete Learning Unit");
			}
			
			if (!is_array(unserialize($userData['learningunits'])) || !array_key_exists($data['id'], unserialize($userData['learningunits']))) {
				if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && in_array($data['id'], $_SESSION['cart'])) {
					$selected = true;
				} else {
					$selected = false;
				}
				
				if (!empty($data['price'])) {
					echo cell(checkBox("purchase[]", "purchase_" . $data['id'], " $" . $data['price'], $data['id'], false, false, $selected), "100", "Purchase Learning Unit");
				} else {
					echo cell(checkBox("purchase[]", "purchase_" . $data['id'], " $0.00", $data['id'], false, false, $selected), "100", "Purchase Learning Unit");
				}
			} else {
				echo cell("<span class=\"notAssigned\">Purchased</span>", "100", "Purchase Learning Unit");
			}
			  
			echo "</tr>\n";
			
			array_push($units, $data['id']);
			$count++;
		 }
		 
		 echo "</table>\n";
		 
		 if (access("Purchase Learning Unit")) {
			 foreach($units as $unit) {
				 if (!is_array(unserialize($userData['learningunits'])) || !array_key_exists($unit, unserialize($userData['learningunits']))) {
					 $displayButton = true;
				 }
			 }
			 
			 if (isset($displayButton)) {
				 echo "<hr />\n";
				 echo "<div align=\"right\">";
				 
				 if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
					echo button("submit", "submit", "Add Selected Items to Cart", "submit");
				 } else {
					 echo button("submit", "submit", "Update Cart", "submit");
				 }
				 
				 echo "</div>";
			 }
			 
			 echo closeForm(false);
		 }
	 } else {
		 echo "<div class=\"noResults\">There are no learning units currently avaliable.";
		  
		 if (access("Create Learning Unit")) {
			 echo " " . URL("Create one now", "wizard/index.php") . ".";
		 }
		  
		 echo "</div>\n";
	 }
	 
//Include the footer
	footer();
?>