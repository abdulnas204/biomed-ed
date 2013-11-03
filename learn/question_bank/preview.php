<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: August 21st, 2010
Last updated: February 24th, 2011

This is the page for previewing individual questions from 
the question bank.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Preview Test Question", "tinyMCESimple,validate,newObject", true, " class=\"overrideBackground\"", false, true);

//Check to see if a question exists
	if (isset ($_GET['id'])) {
		if (!exist("questionbank_" . $userData['organization'], "id", $_GET['id'])) {
			die(errorMessage("The test question does not exist."));
		}
	} else {
		die(errorMessage("A required parameter is missing."));
	}
	
//Title
	title("Preview Test Question", false, false, "preview");
	
//Display the test question
	test("questionbank_" . $userData['organization'], true, $_GET['id']);
	
//Include the footer
	footer(false, true);
?>