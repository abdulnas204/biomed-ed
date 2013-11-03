<?php
/*
LICENSE: See "license.php" located at the root installation

This is the login page for the system administration panel.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	headers("System Administration Login", "validate");
	
//Process the login
	if (isset($_POST['submit']) && !empty($_POST['userName']) && !empty($_POST['passWord'])) {
		if ($_POST['userName'] === $rootUserName && $_POST['passWord'] === $rootPassWord) {
			$_SESSION['administration'] = $_POST['userName'];
			redirect("index.php");
		} else {
			redirect("login.php?alert=true");
		}
	}
	
//Title
	title("System Administration Login", "Login to access the system administration panel for this site.");
	
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