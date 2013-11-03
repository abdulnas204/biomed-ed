<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: Novemeber 24th, 2010
Last updated: December 20th, 2010

This is the developer administration log out page.
*/

//Header functions
	require_once('../system/core/index.php');	
	headers("Developer Administration Logout");
	
//Logout the user
	unset($_SESSION['developerAdministration']);
	
	if (isset($_GET['action']) && $_GET['action'] == "complete") {
		redirect($root . "users/logout.php");
	}
	
//Title
	title("Logout", "You have successfully logged out.");
	echo "<div class=\"spacer\">\n";
	echo button("continue", "continue", "Continue", "button", $root . "portal/index.php");
	echo "</div>\n";
	
//Include the footer
	footer();
?>