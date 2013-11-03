<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: November 28th, 2010
Last updated: Novemeber 29th, 2010

This is the configuration script for the content management 
plugin.
*/

//Plugin information
	$name = "Content Management System";
	$menuName = "Public Website";
	$author = "Oliver Spryn";
	$infoURL = "http://apexdevelopment.businesscatalyst.com";
	$version = "1.0";
	$pluginRoot = $root . "cms/";
	
/*
 * Set the parent on the navigation menu hierarchy
 * Use "top" for top-level, false for hidden
*/
	$menuParent = "top";

/*
 * Set the children on the navigation menu hierarchy, can only contain pages within this plugin
 * Use false for none, or create an array containing the list of sub-pages, with the URL as the key (relative to the plugin root) and the value as the title
*/
	$menuChildren = array (
			"index.php" => "Website Pages",
			"sidebar.php" => "Sidebar Items",
			"site_settings.php" => "Site Settings"
			);

/*
 * Define the privilege types required to access each page
 * The keys are the page URLs (relative to the plugin root), and the value is the privilege name
 * To account for URL parameters or active sessions, start with the page URL, then wrap each URL paramerter with a (), and a session with a [], and seperate each item with a comma
 * Here is an example of a page allowing access when an "id" parameter is defined and a "edit" session is active: "page.php,(id),[edit]"
 * To assign multiple pages to one permission, simply include all of the pages in one string, and seperate the URLs with a semicolon
 * To allow public access to a page, simply construct the array as described above, but, instead, assign the value as "Public"
*/
	$privileges = array(
			//Pages
			"index.php" => "View Pages",
			"manage_page.php" => "Create Page",
			"manage_page.php,(id)" => "Edit Page",
			"index.php,(id),(action)" => "Delete Page",
			
			//Sidebar
			"sidebar.php" => "View Sidebar Items",
			"manage_sidebar.php" => "Create Sidebar Items",
			"manage_sidebar.php,(id)" => "Edit Sidebar Items",
			"sidebar.php,(id),(action)" => "Delete Sidebar Items",
			
			//Settings
			"site_settings.php" => "Manage Site Settings",
			"sidebar_settings.php" => "Manage Sidebar Settings"
			);
			
/*
 * Force a list of pages to use a secure (https) connection, if an SSL certificate is installed
 * The keys are the page URLs (relative to the plugin root), and the value is the level of importance
 * The values may either be "Optional" (use a secure connection if an SSL certificate is installed) or "Important" (deny access to the page if an SSL certificate is not installed)
 * To account for URL parameters or active sessions, start with the page URL, then wrap each URL paramerter with a (), and a session with a [], and seperate each item with a comma
 * Here is an example of a secured page when an "id" parameter is defined and a "edit" session is active: "page.php,(id),[edit]"
 * To assign multiple pages to a secure connection, simply include all of the pages in one string, and seperate the URLs with a semicolon
*/
	$SSL = array();
?>