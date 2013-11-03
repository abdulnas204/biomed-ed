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
Last updated: Novemeber 30th, 2010

This is the role administration overview page, which 
displays a summary of all of the roles within the system.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("admin/system/php") . "index.php");
	require_once(relativeAddress("admin/system/php") . "functions.php");
	headers("Role Administration Panel", false, true);
	
//Reorder roles
	reorder("roles", "index.php");
	
//Delete roles
	delete("roles", "index.php");
	
//Title
	title("Role Administration Panel", "This is the role administration panel, designed for developers to create and manage user roles.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo URL("Add New Role", "manage_role.php", "toolBarItem new") . "\n";
	echo URL("Back to Overview", "../index.php", "toolBarItem back") . "\n";
	echo "</div>\n";
	
//Display message updates
	message("message", "inserted", "success", "The role was successfully inserted");
	message("message", "updated", "success", "The role was successfully updated");
	
//Roles table
	if (exist("roles")) {
		$rolesGrabber = query("SELECT * FROM `roles` ORDER BY `position` ASC", "raw");
		$count = 1;
		
		echo "<br />\n<table class=\"dataTable\">\n<tbody>\n<tr>\n<th width=\"75\" class=\"tableHeader\">Order</th>\n<th width=\"200\" class=\"tableHeader\">Role</th>\n<th class=\"tableHeader\">Description</th>\n<th width=\"50\" class=\"tableHeader\">Edit</th>\n<th width=\"50\" class=\"tableHeader\">Delete</th>\n</tr>\n";
		
		while($roles = fetch($rolesGrabber)) {
			echo "<tr";
			if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			echo "<td width=\"75\">"; reorderMenu($roles['id'], $roles['position'], "roles", "roles"); echo "</td>\n";
			echo "<td width=\"200\">" . URL($roles['name'], "details.php?id=" . $roles['id'], false, false, "Click to view the details of the <strong>" . $roles['name'] . "</strong> role") . "</td>\n";
			echo "<td>" . commentTrim(150, $roles['description']) . "</td>\n";
			echo "<td width=\"50\">" . URL(false, "manage_role.php?id=" . $roles['id'], "action edit", false, "Edit the <strong>" . $roles['name'] . "</strong> role") . "</td>\n";
			echo "<td width=\"50\">" . URL(false, "index.php?action=delete&id=" . $roles['id'], "action delete", false, "Delete the <strong>" . $roles['name'] . "</strong> role", true) . "</td>\n";
			echo "</tr>\n";
			
			$count++;
		}
		
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">There are no roles within this system.</div>\n";
	}
	
//Include the footer
	footer();
?>