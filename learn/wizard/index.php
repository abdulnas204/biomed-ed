<?php
/*
LICENSE: See "license.php" located at the root installation

This is the welcome page for the learning unit generator.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');
	monitor("Welcome");
	
//Title
	title("Welcome to the Learning Unit Setup Wizard", "This wizard will guide you through the process of setting up a lesson and test. Click &quot;Launch Wizard&quot; to begin.");

//Page content
	echo form("begin");
	echo "<div class=\"spacer\">\n";
	echo button("submit", "submit", "Launch Wizard", "button", "lesson_settings.php");
	echo "</div>\n";
	echo closeForm(false);
	
//Include the footer
	footer();
?>