<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: June 30th, 2010
Last updated: Janurary 13th, 2011

This is the script to generate the link list for TinyMCE.
*/

//Header functions
	require_once('../../../core/index.php');

//Define this as a javascript file
	header ("Content-type: text/javascript");

//Grab all of the pages	
	if (exist("pages")) {
		$pageDataGrabber = query("SELECT * FROM `pages` ORDER BY `position` ASC", "raw");
		$pageCount = query("SELECT * FROM `pages` ORDER BY `position` ASC", "num");
		
		echo "var tinyMCELinkList = new Array(";
		
		while ($page = fetch($pageDataGrabber)) {
			echo "[\"" . $page['title'] . "\", \"" . $root . "index.htm?page=" . $page['id'] . "\"]";
			
			if ($page['position'] != $pageCount) {
				echo ", ";
			}
		}
		
		echo ");";
	} else {
		echo "var tinyMCELinkList = new Array([\"Home Page\", \"" . $root . "index.htm\"]);";
	}
?>