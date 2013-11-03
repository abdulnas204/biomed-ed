<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: August 13th, 2010
Last updated: February 24th, 2011

This is the page for previewing individual questions from 
the test generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Preview Test Question", "tinyMCESimple,validate,newObject", true);

//Check to see if a question exists
	if (isset ($_GET['id'])) {
		if (!exist($monitor['testTable'], "id", $_GET['id'])) {
			die(errorMessage("The test question does not exist."));
		}
	} else {
		die(errorMessage("A required parameter is missing."));
	}
	
//Title
	title("Preview Test Question", false, false, "preview");
	
//Display the test question
	test($monitor['testTable'], true, $_GET['id']);
	
//Include the footer
	footer(false, true);
?>