<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	headers("Preview Test Question", "Site Administrator", "tinyMCESimple,validate,newObject", true, " class=\"overrideBackground\"", false, false, false, false, true);

//Check to see if a question exists
	if (isset ($_GET['id'])) {
		if (exist("questionbank", "id", $_GET['id']) == false) {
			die("The test question does not exist.");
		}
	} else {
		die("A required parameter is missing.");
	}
	
//Title
	title("Preview Test Question", false, false, "preview");
	
//Display the test question
	test("questionbank", true, $_GET['id']);
	
//Include the footer
	footer(false, true);
?>