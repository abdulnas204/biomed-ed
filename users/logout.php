<?php
/*
LICENSE: See "license.php" located at the root installation

This is the log out page.
*/

//Header functions
	require_once('../system/server/index.php');	
	headers("Logout", false, false, false, true);
	
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
		echo button("continue", "continue", "Continue", "button", "../index.php");
		echo "</div>";
	}
	
//Include the footer
	footer();
?>