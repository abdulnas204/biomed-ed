<?php
/*
LICENSE: See "license.php" located at the root installation

This is the account activation page the user will be directed to once they have registered.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
//Check to see if an activation key was passed in as a URL parameter, and if it was correct
	if (isset($_GET['id'])) {
		if (exist("activation", "key", $_GET['id'])) {
			$needKey =  $_GET['id'];
		} else {
			$needKey = "true";
		}
	} else {
		$needKey = "true";
	}
	
//Repond to jQuery requests to activate the user's account using the key
	if (isset($_POST['validate']) && isset($_POST['passWord'])) {
		if (isset($_POST['key'])) {
			$key = $_POST['key'];
		} elseif (isset($_GET['id'])) {
			$key = $_GET['id'];
		} else {
			echo "failure";
		}
		
		$passWord = md5($_POST['passWord'] . $salt);
		$keyCheck = query("SELECT * FROM `activation` WHERE `key` = '{$key}'", false, false);
		$passWordCheck = query("SELECT * FROM `users` WHERE `id` = '{$keyCheck['id']}'", false, false);
		
	//Log the user in if activation is a success. No point in making them enter their password twice.
		if (time() - $keyCheck['timeStamp'] < 86400 && $passWord == $passWordCheck['passWord']) {
			query("UPDATE `users` SET `locked` = '0' WHERE `id` = '{$passWordCheck['id']}'");
			$_SESSION['userName'] = $passWordCheck['userName'];
			$_SESSION['role'] = $passWordCheck['role'];
			
			echo "success";
		} else {
			echo "failure";
		}
		
		exit;
	}
	
//Top content
	headers("Account Activation", "activation");
	
//Title
	title("Account Activation", "Welcome to the account activation page! You should have recieved an email containing an activation link. Simply fill out the form below to activate your new account.");
	
//Display an alert message if an expired or invalid key was given in the URL
	if (isset($_GET['id']) && $needKey == "true") {
		errorMessage("An invalid activation key was provided. Make sure you have provided the correct activation key. If you are sure it is correct, then key has expired. You may " . URL("try logging into your account", "javascript:;", "login") . ", and from there, have the activation key resent to your primary email address.");
	}
	
//Account activation form
	echo "<div class=\"formContainer\">\n";
	echo "<table align=\"center\">\n";
	echo "<tr>\n";
	echo "<td width=\"200\" align=\"right\">Activation key</td>\n";
	echo "<td width=\"600\">";
	
	if ($needKey == "true") {
		echo textField("key", "key");
	} else {
		echo "\n<strong>" . $needKey . "</strong>\n";
	}
	
	echo "</td>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	echo "<td width=\"200\" align=\"right\">Your password</td>\n";
	echo "<td width=\"600\">" . textField("passWord", "passWord", false, false, true) . "</td>\n";
	echo "</tr>\n";
	
	echo "<tr>\n";
	echo "<td width=\"200\" align=\"right\"></td>\n";
	echo "<td width=\"600\">" . button("submit", "submit", "Activate Account", "button") . "</td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</div>\n";
	
//Include the footer
	footer();
?>