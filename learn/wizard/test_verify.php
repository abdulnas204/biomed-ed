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
Last updated: December 3rd, 2010

This is the test verification page for the test generator, 
which allows creators to fully sample their test prior to 
deployment.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	$monitor = monitor("Verify Test Content", "navigationMenu,newObject,tinyMCESimple");
	
//Display a randomizing alert if any part of this test randomizes
	$randomizeTest = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentUnit']}'");
	
	if ($randomizeTest['randomizeAll'] == "Randomize" || exist($monitor['testTable'], "randomize", "1")) {
		$message = " <strong>Since you are only previewing this test, note the questions may appear in a different order if the page is refreshed, or left and returned to later.</strong>";
	} else {
		$message = "";
	}

//Title
	navigation("Verify Test Content", "Content may be reviewed in the section below. Changes can be made to  the lesson by clicking the &quot;Make Changes&quot; button, and modifying the test." . $message);

//Display the test
	test($monitor['testTable'], $monitor['gatewayPath'], true);
	
//Display navigation buttons
	echo "<blockquote>\n";
	echo button("back", "back", "&lt;&lt;  Previous Step", "button", "test_content.php");
	echo button("next", "next", "Next Step &gt;&gt;", "button", "complete.php");
	
	if (isset ($_SESSION['review'])) {
		echo button("submit", "submit", "Finish", "button", "../index.php?updated=module");
	}
	
	echo "</blockquote>\n";
	
//Include the footer
	footer();
?>