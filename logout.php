<?php
//Header functions
	require_once('system/connections/connDBA.php');	
	headers("Logout");
	
//Logout the user
	session_destroy();
	
//Title
	if (isset($_GET['action']) && $_GET['action'] == "relogin") {
		title("Logout", "Your profile has been updated. Since your role in this site has changed, you must login again.");
		echo "<div class=\"spacer\">";
		button("continue", "continue", "Continue", "button", "login.php");
		echo "</div>";
	} else {
		title("Logout", "You have successfully logged out.");
		echo "<div class=\"spacer\">";
		button("continue", "continue", "Continue", "button", "index.php");
		echo "</div>";
	}
	
//Include the footer
	footer();
?>