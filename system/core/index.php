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
Last updated: Janurary 10th, 2011

This script is the core of the system, which contains key 
information and definitions which will be used globally.
*/

//Start a session and output buffering
	session_set_cookie_params("1200");
	session_name("ENSIGMAPRO");
	session_start();
	ob_start();
	
//Root address for entire site
	if ($_SERVER['HTTPS'] == "on") {
		$protocol = "https://";
	} else {
		$protocol = "http://";
	}
	
	$root = $protocol . $_SERVER['HTTP_HOST'] . "/";
	$strippedRoot = str_replace($protocol . $_SERVER['HTTP_HOST'], "", $root);

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
	
//Set server configurations
	set_time_limit(3600);
	ini_set("expose_php", "Off");
	
	/*---------------------------------------------------- Developer use ONLY!!!! Disable during production!!!! ----------------------------------------------------*/
	error_reporting(-1);
	
//Create a relative address in order to access other system functions	
	function relativeAddress($addressAddition) {
		global $strippedRoot;
		
		$URL = array_merge(array_filter(explode("/", $_SERVER['PHP_SELF'])));
		$relativeAddress = "";
		
		foreach($URL as $directory) {
			if (isset($process)) {
				if (!strstr($directory, ".php")) {
					$relativeAddress .= "../";
				} else {
					$relativeAddress .= "../";
					break;
				}
			}
			
			if (strtolower(trim($strippedRoot, "/")) == "") {
				$process = true;
			} else {
				if (strtolower($directory) == strtolower(trim($strippedRoot, "/"))) {
					$process = true;
				}
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