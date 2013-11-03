<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: July 30th, 2010
Last updated: December 3rd, 2010

This is the home page of the site, which contains content 
from the CMS portion of the site, as well as a customizeable 
sidebar.
*/

//Header functions
	require_once('system/core/index.php');
	login();
	
//If no page URL variable is defined, then choose the home page
	if (!isset($_GET['page']) || $_GET['page'] == "") { 
		$pageInfo = query("SELECT * FROM `pages` WHERE `position` = '1'");
	} else {		
		$pageInfo = query("SELECT * FROM `pages` WHERE `id` = {$_GET['page']}");	
	}
	
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
	
//Generate the title
	if (!exist("pages", "position", "1")) {
		$title = "Setup Required";
	} else {
		if (empty($pageInfo['content'])) {
			$title = "Page Not Found";
		} else {
			$title = $pageInfo['title'];
		}
	}
	
//Top content
	headers($title, false, false, false, true);

//Use the layout control if the page is displaying a sidebar		
	if (isset($sideBarResult)) {
		echo "<div class=\"layoutControl\">\n<div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "contentRight";
		} else {
			echo "contentLeft";
		}
		
		echo "\">\n";
	}

//Admin toolbar
	if (loggedIn() && !empty($pageInfo['content'])) {
		form("pages", "post", false, "cms/index.php");
		echo "<div class=\"toolBar noPadding\"><div align=\"center\">";
		echo URL("Edit This Page", "cms/manage_page.php?id=" . $pageInfo['id']);
		echo " | Visible: ";
		echo hidden("action", "action", "setAvaliability");
		echo hidden("id", "id", $pageInfo['id']);
		echo hidden("redirect", "redirect", "true");
		echo dropDown("option", "option", "Yes,No", "on,", false, false, false, false, "pageInfo", "visible", " onchange=\"this.form.submit();\"");
		echo " | ";
		echo URL("Back to Staff Home Page", "portal/index.php");
		echo " | ";
		echo URL("Back to Pages", "cms/index.php");
		echo " | ";
		echo URL("Back to Sidebar", "cms/sidebar.php");
		echo "</div></div>";
		closeForm(false, false);
	}
	
//Display the page content	
	if (empty($pageInfo['content']) && !exist("pages", "position", "1")) {
		if (!loggedIn()) {
			title("Setup Required", "Please " . URL("login", "login.php") . " to create your first page.");
		} else {
			title("Setup Required", "Please " . URL("create your first page", "cms/manage_page.php") . ".");
		}
	} else {
		if (empty($pageInfo['content'])) {
			title("Page Not Found", "The page you are looking for was not found on our system");
			echo "<p>&nbsp;</p><p align=\"center\">";
			button("continue", "continue", "Continue", "history");
			echo "</p>";
		} else {
			echo "<h2>" . $pageInfo['title'] . "</h2>" . $pageInfo['content'];
		}
	}
	
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
	footer(true);
?>