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

This is the completion page, stating that the learning unit 
has been set up, and it handles some final processing.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Complete", "navigationMenu");
	
//Process the form
	if (isset ($_POST['submit'])) {
		query("UPDATE `{$monitor['parentTable']}` SET `visible` = 'on' WHERE `id` = '{$monitor['currentUnit']}'");
		
		if ($_POST['submit'] == "Finish") {
			redirect("../index.php");
		} else {
			unset($_SESSION['currentUnit'], $_SESSION['review']);
			redirect("index.php");
		}
	}
	
//Grab the lesson name
	$name = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentUnit']}'");
	
//Title
	navigation($monitor['title'] . "Complete", "&quot;<strong>" . $name['name'] . "</strong>&quot; has been successfully created.");
	
//Completion form
	echo form("finish");
	echo "<div class=\"spacer\">\n";
	echo button("submit", "submit", "Finish", "submit");
	echo button("submit", "submit", "Create Another Learning Unit", "submit");
	echo "</div>\n";
	echo closeForm(false);
	
//Include the footer
	footer();
?>