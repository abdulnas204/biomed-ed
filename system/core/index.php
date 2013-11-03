<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: Novemeber 27th, 2010
Last updated: December 1st, 2010

This script is the core of the system, which contains key 
infomation and definitions which will be used globally.
*/

//Start a session and output buffering
	session_start();
	ob_start();

//Root address for entire site
	$root = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/";
	$strippedRoot = str_replace("http://" . $_SERVER['HTTP_HOST'], "", $root);

//Database connection
	$databaseType = "mysql";
	$connDBA = mysql_connect("localhost", "root", "Oliver99");
	$dbSelect = mysql_select_db("biomed-ed", $connDBA);
	
//Define time zone
	$timeZoneGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
	$timeZone = mysql_fetch_array($timeZoneGrabber);
	date_default_timezone_set($timeZone['timeZone']);
	
//Credentials for developer login
	$rootUserName = "spryno724";
	$rootPassWord = "Oliver99";
	
//Set upload time limit
	set_time_limit(3600);
	
//Create a relative address in order to access other system functions	
	function relativeAddress($addressAddition) {
		global $strippedRoot;
		
		$URL = array_filter(explode("/", $_SERVER['PHP_SELF']));
		$relativeAddress = "";
		
		foreach($URL as $directory) {
			if (isset($process)) {
				if (!strstr($directory, ".php")) {
					$relativeAddress .= "../";
				}
			}
			
			if (strtolower($directory) == strtolower(trim($strippedRoot, "/"))) {
				$process = true;
			}
		}
		
		$relativeAddress .= $addressAddition . "/";
		
		return $relativeAddress;
	}
	
	$relativeAddress = relativeAddress("system/core");
	
//Include additional core scripts, order is important!
	$require = array("global.php", "login_management.php", "constructor.php", "includes.php", "layout.php", "processor.php", "library.php");
	
	foreach($require as $script) {
		require_once($relativeAddress . $script);
	}
?>