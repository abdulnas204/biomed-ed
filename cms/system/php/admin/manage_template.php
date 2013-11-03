<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: Janurary 3rd, 2011
Last updated: Janurary 3rd, 2011

This is the CMS template management page.
*/

//Header functions
	require_once('../../../../system/core/index.php');
	require_once(relativeAddress("cms/system/php") . "index.php");
	require_once(relativeAddress("cms/system/php") . "functions.php");
	headers("Manage Template", "tinyMCEDesigner,validate", true);
	lockAccess();
	$_SESSION['designer'] = true;
	
//Title
	title("Manage Template", "The page will manage the site templates.");
	
//Template form
	echo form("templates");
	catDivider("Template Information", "one", true);
	echo "<blockquote>\n";
	directions("Template name", true);
	indent(textField("name", "name", false, false, false, true, false, false, "template", "name"));
	directions("Template content", true);
	indent(textArea("content", "content1", "large", true, false, false, "template", "content"));
	echo "</blockquote>\n";
	
	catDivider("Submit", "two");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>