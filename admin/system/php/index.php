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

This is the configuration script for the developer 
administration plugin.
*/

//Plugin information
	$name = "Developer Administration";
	$menuName = "Developer Administration";
	$author = "Oliver Spryn";
	$infoURL = "http://apexdevelopment.businesscatalyst.com";
	$version = "1.0";
	$pluginRoot = $root . "admin/";

/*
 * Set the parent on the navigation menu hierarchy
 * Use "top" for top-level, false for hidden
*/
	$menuParent = false;

/*
 * Set the children on the navigation menu hierarchy, can only contain pages within this plugin
 * Use false for none, or create an array containing the list of sub-pages, with the URL as the key (relative to the plugin root) and the value as the title
*/
	$menuChildren = false;

/*
 * Define the privilege types required to access each page
 * The keys are the page URLs (relative to the plugin root), and the value is the privilege name
 * To account for URL parameters or active sessions, start with the page URL, then wrap each URL paramerter with a (), and a session with a [], and seperate each item with a comma
 * Here is an example of a page allowing access when an "id" parameter is defined and a "edit" session is active: "page.php,(id),[edit]"
 * To assign multiple pages to one permission, simply include all of the pages in one string, and seperate the URL with a semicolon
*/
	$privileges = array(/* None, as this is not dependant on configuration, but an additional login */);
?>