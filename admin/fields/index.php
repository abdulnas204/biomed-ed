<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	headers("Developer Administration: Module Generator Fields", "Site Administrator");
	developerAccess();
	
//Title
	title("Module Generator Fields", "This is the module generator field panel, which is used to add additional fields to the module and question generators.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Add Form Field", "manage_field.php", "toolBarItem new");
	echo URL("Back to Overview", "../index.php", "toolBarItem back");
	echo "</div>";
	
//Display all avaliable fields
//Module Generator
//Lesson Settings
	if (exist("fields", "page", "Lesson Settings")) {
		catDivider("Module Generator: Lesson Settings", "alignLeft");
	}
	
//Include the footer
	footer();
?>