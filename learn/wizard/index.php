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
Last updated: Novemeber 30th, 2010

This is the welcome page for the learning unit generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	monitor("Welcome");
	
//Process the form
	if (isset($_POST['submit'])) {
		$directory = nextID("learningunits");
		
		if (file_exists("../" . $directory)) {
			deleteAll("../" . $directory);
		}
		
		mkdir("../" . $directory, 0777);
		mkdir("../" . $directory . "/lesson", 0777);
		mkdir("../" . $directory . "/lesson/browser", 0777);
		mkdir("../" . $directory . "/lesson/browser/public", 0777);
		mkdir("../" . $directory . "/lesson/browser/secure", 0777);
		mkdir("../" . $directory . "/test", 0777);
		mkdir("../" . $directory . "/test/answers", 0777);
		mkdir("../" . $directory . "/test/responses", 0777);
	}
	
//Title
	title("Welcome to the Learning Unit Setup Wizard", "This wizard will guide you through the process of setting up a lesson and test. Click &quot;Launch Wizard&quot; to begin.");

//Page content
	echo form("begin");
	echo "<div class=\"spacer\">\n";
	echo button("submit", "submit", "Launch Wizard", "submit");
	echo "</div>\n";
	echo closeForm();
	
//Include the footer
	footer();
?>