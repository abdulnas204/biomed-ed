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
Created on: November 28th, 2010
Last updated: Novemeber 28th, 2010

This is the configuration script for the content management 
plugin.
*/

//Plugin information
	$name = "Content Management System";
	$author = "Oliver Spryn";
	$infoURL = "http://apexdevelopment.businesscatalyst.com";
	$version = "1.0";
	$pluginRoot = $root . "cms/";

//Set the parent on the navigation menu hierarchy, use "top" for top-level, false for hidden, or select the $name to add this as a sub-menu of another plugin
	$menuParent = "top";
	
//Define the privilege types required to access each page
	$privileges = array(
			//Pages
			"index.php" => "View Pages",
			"manage_page.php" => "Create Page",
			"manage_page.php?id=" => "Edit Page",
			
			//Sidebar
			"sidebar.php" => "View Sidebar Items",
			"manage_sidebar.php" => "Create Sidebar Items",
			"manage_sidebar.php?id=" => "Edit Sidebar Items",
			
			//Settings
			"site_settings.php" => "Manage Site Settings",
			"sidebar_settings.php" => "Manage Sidebar Settings"
			);
?>