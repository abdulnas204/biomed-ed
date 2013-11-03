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
Created on: August 14th, 2010
Last updated: Novemeber 27th, 2010

This is the dedicated login page.
*/

//Header functions
	require_once('system/core/index.php');	
	headers("Login", false, "validate");

//Login the user
	login();
	
//Title
	title("Login", "Login with your username and password to access your account.");
	
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
	echo "<p>" . URL("Forgot your password?", "forgot_password.php") . "</p>\n";
	echo "<p>" . URL("Register", "register.php") . "</p>";
	echo "</blockquote>";
	echo closeForm(false, false);
	
//Include the footer
	footer();
?>