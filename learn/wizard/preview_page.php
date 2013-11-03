<?php
/*
LICENSE: See "license.php" located at the root installation

This is the page for previewing individual pages from the lesson generator.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');	
	$monitor = monitor("Preview Page", "plugins", true);

//Check to see if a page exists
	if (isset ($_GET['page'])) {
		if (!exist($monitor['lessonTable'], "position", $_GET['page'])) {
			die(errorMessage("The page does not exist."));
		}
	} else {
		die(errorMessage("A required parameter is missing."));
	}
	
//Title
	title("Preview Page", false, false, "preview");
	
//Display the page
	lesson($monitor['currentUnit'], $monitor['lessonTable'], true);
	
//Include the footer
	footer(false, true);
?>