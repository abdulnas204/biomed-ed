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
Last updated: December 2nd, 2010

This is the welcome page for the learning unit generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	monitor("Welcome");
	
//Title
	title("Welcome to the Learning Unit Setup Wizard", "This wizard will guide you through the process of setting up a lesson and test. Click &quot;Launch Wizard&quot; to begin.");

//Page content
	echo form("begin");
	echo "<div class=\"spacer\">\n";
	echo button("submit", "submit", "Launch Wizard", "button", "lesson_settings.php");
	echo "</div>\n";
	echo closeForm(false);
	
//Include the footer
	footer();
?>