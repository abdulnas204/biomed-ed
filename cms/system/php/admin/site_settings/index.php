<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: February 5th, 2010
Last updated: February 5th, 2010

This is the overview for the site settings management.
*/

//Header functions
	require_once('../../../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Site Settings", "validate", true);
	lockAccess();	
	
//Title
	title("Site Settings", "Manage the interface and information for this site by one of the four options below.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo toolBarURL("Back to CMS Overview", "../index.php", "toolBarItem back") . "\n";
	echo "</div>\n";
	
//Display information about this site, and provide the links to manage each section
	$siteInfo = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
	
	echo "<p class=\"homeDivider\">Site Information (" . URL("Manange Site Information", "site_information.php") . ")</p>\n";
	echo "<blockquote>\n";
	echo "<p><strong>Site name:</strong> " . $siteInfo['siteName'] . "<br />\n";
	echo "<strong>Footer text:</strong> " . strip_tags($siteInfo['siteFooter']) . "<br />\n";
	echo "<strong>Site description:</strong> " . $siteInfo['description'] . "<br />\n";
	echo "<strong>Author:</strong> " . $siteInfo['author'] . "<br />\n";
	echo "<strong>Copyright statement:</strong> " . $siteInfo['copyright'] . "<br />\n";
	echo "<strong>Search engine keywords:</strong> " . $siteInfo['meta'] . "<br />\n";
	echo "<br />";
	echo "<strong>Timezone:</strong> ";
	
	switch($siteInfo['timeZone']) {
		case "America/New_York" : echo "Eastern Time Zone"; break;
		case "America/Chicago" : echo "Central Time Zone"; break;
		case "America/Denver" : echo "Mountain Time Zone"; break;
		case "America/Los_Angeles" : echo "Pacific Time Zone"; break;
		case "America/Juneau" : echo "Alaskan Time Zone"; break;
		case "Pacific/Honolulu" : echo "Hawaii-Aleutian Time Zone"; break;
	}
	
	echo "<br />\n";
	echo "<strong>Site language:</strong> " . $siteInfo['language'];
	echo "</p>\n";
	echo "</blockquote>\n";
	
	echo "<p class=\"homeDivider\">Logo (" . URL("Manange Logo", "logo.php") . ")</p>\n";
	echo "<blockquote>\n";
	
	if ($siteInfo['auto'] != "on") {
		echo "<img src=\"../../../../../system/images/banner.png\" width=\"" . $siteInfo['width'] . "\" height=\"" . $siteInfo['height'] . "\" alt=\"" . $siteInfo['siteName'] . "\" title=\"" . $siteInfo['siteName'] . "\" />\n";
		echo "<p><strong>Width:</strong> " . $siteInfo['width'] . "<br />\n";
		echo "<strong>Height:</strong> " . $siteInfo['height'] . "</p>\n";
	} else {
		echo "<img src=\"../../../../../system/images/banner.png\" alt=\"" . $siteInfo['siteName'] . "\" title=\"" . $siteInfo['siteName'] . "\" />\n";
		echo "<p>Width and height are automatic.</p>\n";
	}
	
	echo "<p><strong>Top padding:</strong> " . $siteInfo['paddingTop'] . "px<br />\n";
	echo "<strong>Left padding:</strong> " . $siteInfo['paddingLeft'] . "px<br />\n";
	echo "<strong>Right padding:</strong> " . $siteInfo['paddingRight'] . "px<br />\n";
	echo "<strong>Bottom padding:</strong> " . $siteInfo['paddingBottom'] . "px</p>\n";
	echo "</blockquote>\n";
	
	echo "<p class=\"homeDivider\">Shortcut Icon (" . URL("Manage Shortcut Icon", "icon.php") . ")</p>\n";
	echo "<blockquote>\n";
	echo "<img src=\"../../../../../system/images/icon." . $siteInfo['iconType'] . "\" />\n";
	echo "</blockquote>\n";
	
	echo "<p class=\"homeDivider\">Site Theme (" . URL("Manange Site Theme", "theme.php") . ")</p>\n";
	echo "<blockquote>\n";
	
	switch($siteInfo['style']) {
		case "american.css" : 
			echo "<p><strong>Selected theme:</strong> American</p>\n";
			echo "<img src=\"../../../../../system/images/themes/american/preview.jpg\" />\n";
			break;
			
		case "binary.css" : 
			echo "<p><strong>Selected theme:</strong> Binary</p>\n";
			echo "<img src=\"../../../../../system/images/themes/binary/preview.jpg\" />\n";
			break;
			
		case "business.css" : 
			echo "<p><strong>Selected theme:</strong> Business</p>\n";
			echo "<img src=\"../../../../../system/images/themes/business/preview.jpg\" />\n";
			break;
			
		case "digitalUniversity.css" : 
			echo "<p><strong>Selected theme:</strong> Digital University</p>\n";
			echo "<img src=\"../../../../../system/images/themes/digital_university/preview.jpg\" />\n";
			break;
			
		case "eLearning.css" : 
			echo "<p><strong>Selected theme:</strong> eLearning</p>\n";
			echo "<img src=\"../../../../../system/images/themes/e_learning/preview.jpg\" />\n";
			break;
			
		case "knowledgeLibrary.css" : 
			echo "<p><strong>Selected theme:</strong> Knowledge Library</p>\n";
			echo "<img src=\"../../../../../system/images/themes/knowledge_library/preview.jpg\" />\n";
			break;
	}
	
//Include the footer
	footer();
?>