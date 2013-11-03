<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: November 24th, 2010
Last updated: December 1st, 2010

This is the developer administration login page.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("admin/system/php") . "index.php");
	require_once(relativeAddress("admin/system/php") . "functions.php");
	headers("Developer Administration Login", "validate");
	
//Process the login
	if (isset($_POST['submit']) && !empty($_POST['userName']) && !empty($_POST['passWord'])) {
		if ($_POST['userName'] === $rootUserName && $_POST['passWord'] === $rootPassWord) {
			$_SESSION['developerAdministration'] = $_POST['userName'];
			redirect("index.php");
		} else {
			redirect("login.php?alert=true");
		}
	}
	
//Title
	title("Developer Administration Login", "Login to access the developer panel for this site.");
	
//Display message updates
	message("alert", "true", "error", "Your username and/or password is incorrect.");
	
//Login form
	echo form("login");
	echo "<blockquote>";
	directions("User name", true);
	indent(textField("userName", "userName"));
	directions("Password", true);
	indent(textField("passWord", "passWord", false, false, true));
	echo button("submit", "submit", "Login", "submit");
	echo "</blockquote>";
	echo closeForm(false);
	
//Include the footer
	footer();
?>