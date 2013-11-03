<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

/* 
Created by: Oliver Spryn
Created on: July 16th, 2010
Last updated: Novemeber 28th, 2010

This is the error page for the system, which will return a 
customized and user-friendly error for a 403 and 404 error.
*/

//Header functions
	require_once('../../system/core/index.php');
	headers("Access Denied");
	login(); 
	
//Detirmine whether or not to show the sidebar
	if (exist("sidebar", "visible", "on")) {
		$sideBarDataGrabber = query("SELECT * FROM `sidebar` WHERE `visible` = 'on'", "raw");
		$sideBarLocation = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
		
		while ($sideBarData = fetch($sideBarDataGrabber)) {
			switch ($sideBarData['type']) {
				case "Login" : $login = "true"; break;
				case "Register" : $register = "true"; break;
				case "Custom Content" : $customContent = "true"; break;
			}
		}
		
		$sideBarResult = "true";
	}
	
//Use the layout control if the page is displaying a sidebar		
	if (isset($sideBarResult)) {
		echo "<div class=\"layoutControl\"><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "contentRight";
		} else {
			echo "contentLeft";
		}
		
		echo "\">";
	}

//Display the error content
	echo "<h2>Access Denied</h2>";
	
	if (isset($_GET['error']) && $_GET['error'] == "403") {
		errorMessage("You do not have premission to access this content");
	} elseif (isset($_GET['error']) && $_GET['error'] == "404") {
		errorMessage("The page you are looking for was not found on our system");
	} else {
		errorMessage("You do not have premission to access this content");
	}
	
	echo "<p>&nbsp;</p><p align=\"center\">";
	echo button("continue", "continue", "Continue", "history");
	echo "</p>";

//Display the sidebar
	if (isset($sideBarResult)) {
		$sideBarCheck = query("SELECT * FROM `sidebar` WHERE `visible` = 'on' ORDER BY `position` ASC", "raw");
		
		echo "</div><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "dataLeft";
		} else {
			echo "dataRight";
		}
		
		echo "\">\n";
		
		while ($sideBar = fetch($sideBarCheck)) {
			sideBox($sideBar['title'], $sideBar['type'], $sideBar['content'], "Site Administrator,Site Manager", $sideBar['id']);
		}
		
		echo "</div>\n</div>\n";
	}
	
//Include the footer
	footer();
?>