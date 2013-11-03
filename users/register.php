<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: September 30th, 2010
Last updated: February 26th, 2011

This is user registration page.
*/

//Header functions
	require_once('../system/core/index.php');
	
//Check to see if the username exists
	validateName("users", "userName");
	
//Top content
	headers("Register", "validate", true);
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['userName']) && !empty($_POST['primaryEmail'])) {
		if (!exist("user", "userName", escape(strip($_POST['userName'], "lettersNumbers"))) && $_POST['verify'] === "DITEC") {
			$firstName = escape(strip($_POST['firstName'], "lettersNumbers"));
			$lastName = escape(strip($_POST['lastName'], "lettersNumbers"));
			$userName = escape(strip($_POST['userName'], "lettersNumbers"));
			$passWord = md5($_POST['passWord'] . $salt);
			$primaryEmail = $_POST['primaryEmail'];
			$secondaryEmail = $_POST['secondaryEmail'];
			$tertiaryEmail = $_POST['tertiaryEmail'];
			
			query("INSERT INTO `users`(
				  id, locked, active, staffID, firstName, lastName, userName, passWord, changePassword, emailAddress1, emailAddress2, emailAddress3, phoneWork, phoneHome, phoneMobile, phoneFax, workLocation, jobTitle, department, departmentID, role, organization
				  ) VALUES (
					  NULL, '', '', '', '{$firstName}', '{$lastName}', '{$userName}', '{$passWord}', '', '{$primaryEmail}', '{$secondaryEmail}', '{$tertiaryEmail}', '', '', '', '', '', '', '', '', 'Student', '0'
				  )");
			
			$id = mysql_insert_id();
			$_SESSION['userName'] = $userName;
			$_SESSION['role'] = "Student";
			$sessionID = encrypt(session_id());
			
			query("UPDATE `users` SET `sessionID` = '{$sessionID}' WHERE `id` = '{$id}'");
			redirect("../portal/index.php");
		} else {
			redirect("register.php?error=identical");
		}
	}

//Title
	if (!isset($_GET['error'])) {
		title("Register", "Please register to gain access to all of our courses.");
	} else {
		title("Register", "Please register to gain access to all of our courses.", false);
	}
	
//Display message updates
	message("error", "identical", "error", "A user with this user name already exists.");
	
//Registration form
	echo form("register");
	catDivider("User Information", "one", true);
    echo "<blockquote>\n";
	directions("First name", true);
	indent(textField("firstName", "firstName", false, false, false, true));
	directions("Last name", true);
	indent(textField("lastName", "lastName", false, false, false, true));
	directions("User name", true);
	indent(textField("userName", "userName", false, false, false, true, ",length[6,30],custom[noSpecialCharactersSpaces],ajax[ajaxName]"));
	directions("Password", true);
	indent(textField("passWord", "passWord", false, false, true, true, ",length[6,30]"));
	echo "</blockquote>\n";
	
	catDivider("Contact Information", "two");
	echo "<blockquote>\n";
	directions("Primary email address", true);
	indent(textField("primaryEmail", "primaryEmail", false, false, false, true, ",custom[email]"));
	directions("Secondary email address");
	indent(textField("secondaryEmail", "secondaryEmail", false, false, false, false, ",custom[email]"));
	directions("Tertiary email address");
	indent(textField("tertiaryEmail", "tertiaryEmail", false, false, false, false, ",custom[email]"));
	echo "</blockquote>\n";
	
	catDivider("Verification Code", "three");
	echo "<blockquote>\n";
	directions("Verification Code", true, "Please enter the verification code provided <br />by your instructor in order to register.");
	indent(textField("verify", "verify", false, false, false, true));
	echo "</blockquote>\n";
	
	catDivider("Submit", "four");
	indent(button("submit", "submit", "Register", "submit"));
	echo closeForm();
	
//Include the footer
	footer();
?>