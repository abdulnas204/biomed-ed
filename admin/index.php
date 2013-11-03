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
Created on: November 24th, 2010
Last updated: Novemeber 28th, 2010

This is the developer administration overview page, which 
displays a summary of developer-administered extensible 
content, and provides quick access to each of these tools.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("admin") . "config.php");
	require_once(relativeAddress("admin/system/php") . "index.php");
	headers("Developer Administration Panel");
	
//Title
	title("Developer Administration Panel", "This is the developer administration panel, designed for developers to administer extensible areas of the site.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Manage Roles", "roles/index.php", "toolBarItem user");
	echo URL("Module Generator Field Management", "fields/index.php", "toolBarItem sideBar");
	echo URL("Leave Administration", "logout.php", "toolBarItem back");
	echo "</div>";
	
//Include the footer
	footer();
?>