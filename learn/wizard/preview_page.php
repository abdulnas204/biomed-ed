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
Last updated: December 4th, 2010

This is the page for previewing individual pages from the 
lesson generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
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