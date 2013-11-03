<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

/* 
Created by: Oliver Spryn
Created on: May 11th, 2010
Last updated: Novemeber 27th, 2010

This is the log out page.
*/

//Header functions
	require_once('system/core/index.php');	
	headers("Logout");
	
//Logout the user
	logout();
	
//Title
	if (isset($_GET['action']) && $_GET['action'] == "relogin") {
		title("Logout", "Your profile has been updated. Since your role in this site has changed, you must login again.");
		echo "<div class=\"spacer\">";
		echo button("continue", "continue", "Continue", "button", "login.php");
		echo "</div>";
	} else {
		title("Logout", "You have successfully logged out.");
		echo "<div class=\"spacer\">";
		echo button("continue", "continue", "Continue", "button", "index.php");
		echo "</div>";
	}
	
//Include the footer
	footer();
?>