<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	monitor("Welcome");
	
//Title
	title("Welcome to the Module Setup Wizard", "This wizard will guide you through the process of setting up a module. Click &quot;Launch Wizard&quot; to begin.");

//Page content
	echo "<div class=\"spacer\">";
	button("submit", "submit", "Launch Wizard", "button", "lesson_settings.php");
	echo "</div>";
	
//Include the footer
	footer();
?>