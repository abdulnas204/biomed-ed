<?php
//Header functions
	require_once('system/connections/connDBA.php');	
	headers("Login", false, "validate");

//Login the user
	login();
	
//Title
	title("Login", "Login with your username and password to access your account.");
	
//Login form
	form("login");
	echo "<blockquote>";
	directions("User name", true);
	echo "<blockquote><p>";
	textField("username", "username");
	echo "</p></blockquote>";
	directions("Password", true);
	echo "<blockquote><p>";
	textField("password", "password", false, false, true);
	echo "</p></blockquote>";
	button("submit", "submit", "Login", "submit");
	echo "<p>" . URL("Forgot your password?", "forgot_password.php") . "</p>";
	echo "<p>" . URL("Register", "register.php") . "</p>";
	echo "</blockquote>";
	
//Include the footer
	footer();
?>