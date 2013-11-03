<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Complete");
	
//Process the form
	if (isset ($_POST['submit'])) {
		mysql_query("UPDATE `{$monitor['parentTable']}` SET `visible` = 'on' WHERE `id` = '{$monitor['currentModule']}'", $connDBA);
		
		if ($_POST['submit'] != "Finish") {
			redirect("index.php");
		} else {
			redirect("../index.php");
		}
	}
	
//Grab the module name
	$moduleNameGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'", $connDBA);
	$moduleName = mysql_fetch_array($moduleNameGrabber);
	
//Title
	title($monitor['title'] . "Complete", "The module &quot;<strong>" . prepare($moduleName['name'], false, true) . "</strong>&quot; has been successfully created.");
	
//Completion form
	form("finish");
	echo "<div class=\"spacer\">";
	button("submit", "submit", "Finish", "submit");
	button("submit", "submit", "Create Another Module", "submit");
	echo "</div>";
	closeForm(false, false);
	
//Include the footer
	footer();
?>