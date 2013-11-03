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
Created on: July 30th, 2010
Last updated: Novemeber 27th, 2010

This is the home page of the site, which contains content 
from the CMS portion of the site, as well as a customizeable 
sidebar.
*/

//Header functions
	require_once('system/core/index.php');
	login();
	
//If no page URL variable is defined, then choose the home page
	if (!isset ($_GET['page']) || $_GET['page'] == "") { 
		$pageInfo = query("SELECT * FROM `pages` WHERE `position` = '1'");
	} else {		
		$pageInfo = query("SELECT * FROM `pages` WHERE `id` = {$_GET['page']}", $connDBA);	
	}
	
//Detirmine whether or not to show the sidebar
	if (exist("sidebar", "visible", "on")) {
		$sideBarDataGrabber = query("SELECT * FROM `sidebar` WHERE `visible` = 'on'", "raw");
		$sideBarArray = array();
		
		while ($sideBarData = mysql_fetch_array($sideBarDataGrabber)) {
			switch ($sideBarData['type']) {
				case "Login" : $login = "true"; break;
				case "Register" : $register = "true"; break;
				case "Custom Content" : $customContent = "true"; break;
			}
		}
		
		if (loggedIn()) {
			if (isset($login) || isset($register) && !isset($customContent)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			} elseif (isset($login) && isset($register)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			} elseif (!isset($login) || !isset($register)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			} elseif (!isset($login) && !isset($register)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			}
		} else {
			$sideBarResult = "true";
		}
	}
	
	if (!$pageInfo && !exist("pages", "position", "1")) {
		$title = "Setup Required";
	} else {
		if (empty($pageInfo['content'])) {
			$title = "Page Not Found";
		} else {
			$title = $pageInfo['title'];
		}
	}
	
//Top content
	headers($title);

//Use the layout control if the page is displaying a sidebar
	$sideBarLocationGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
	$sideBarLocation = mysql_fetch_array($sideBarLocationGrabber);
		
	if (isset($sideBarResult)) {
		echo "<div class=\"layoutControl\"><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "contentRight";
		} else {
			echo "contentLeft";
		}
		echo "\">";
	}

//Admin toolbar
	if (isset($_SESSION['MM_Username']) && !empty($pageInfo['content'])) {
	//The admin toolbox div
		form("pages", "post", false, false, "site_administrator/cms/index.php");
		echo "<div class=\"toolBar noPadding\"><div align=\"center\">";
		URL("Edit This Page", "site_administrator/cms/manage_page.php?id=" . $pageInfo['id']);
		echo " | Visible: ";
		hidden("action", "action", "setAvaliability");
		hidden("id", "id", $pageInfo['id']);
		hidden("redirect", "redirect", "true");
		dropDown("option", "option", "Yes,No", "on,", false, false, false, false, "pageInfo", "pageInfo", "visible", " onchange=\"this.form.submit();\"");
		echo " | ";
		URL("Back to Staff Home Page", "site_administrator/index.php");
		echo " | ";
		URL("Back to Pages", "site_administrator/cms/index.php");
		echo " | ";
		URL("Back to Sidebar", "site_administrator/cms/sidebar.php");
		echo "</div></div>";
		closeForm(false, false);
	}
	
//Display the page content	
	if (empty($pageInfo['content']) && $pagesExist == 0) {
		if (!isset($_SESSION['MM_Username'])) {
			title("Setup Required", "Please " . URL("login", "login.php") . " to create your first page.");
		} else {
			title("Setup Required", "Please " . URL("create your first page", "site_administrator/cms/manage_page.php") . ".");
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
		$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' ORDER BY position ASC", $connDBA);
		
		echo "</div><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "dataLeft";
		} else {
			echo "dataRight";
		}
		
		echo "\"><br /><br /><br />";
		
		while ($sideBar = mysql_fetch_array($sideBarCheck)) {
			sideBox($sideBar['title'], $sideBar['type'], $sideBar['content'], "Site Administrator,Site Manager", $sideBar['id']);
		}
		
		echo "</div></div>";
	}
	
//Include the footer
	footer();
?>