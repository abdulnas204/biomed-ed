<?php
/*
LICENSE: See "license.php" located at the root installation

This is the system administration log out page.
*/

//Header functions
	require_once('../system/server/index.php');	
	headers("System Administration Logout");
	
//Logout the user
	unset($_SESSION['administration']);
	
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