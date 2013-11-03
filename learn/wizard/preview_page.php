<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Preview Page", false, true);

//Check to see if a question exists
	if (isset ($_GET['page'])) {
		if (exist($monitor['lessonTable'], "position", $_GET['page']) == false) {
			die("The page does not exist.");
		}
	} else {
		die("A required parameter is missing.");
	}
	
//Title
	title("Preview Page", false, false, "preview");
	
//Display the page
	lesson($monitor['currentModule'], $monitor['lessonTable'], "miniPreview");
	
//Include the footer
	footer(false, true);
?>