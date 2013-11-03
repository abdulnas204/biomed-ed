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

This is the configuration script for the developer 
administration plugin.
*/

//Plugin information
	$name = "Developer Administration";
	$author = "Oliver Spryn";
	$infoURL = "http://apexdevelopment.businesscatalyst.com";
	$version = "1.0";
	$pluginRoot = $root . "admin/";

//Set the parent on the navigation menu hierarchy, use "top" for top-level, false for hidden, or select the $name to add this as a sub-menu of another plugin
	$menuParent = false;
	
//Define the privilege types required to access each page
	$privileges = array(/* None, as this is not dependant on configuration, but an additional login */);
?>