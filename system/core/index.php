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
Last updated: February 14th, 2011

This script is the core of the system, which contains key 
information and definitions which will be used globally.
*/
	
//Info address for entire site
	if ($_SERVER['HTTPS'] == "on") {
		$protocol = "https://";
	} else {
		$protocol = "http://";
	}
	
	$root = $protocol . $_SERVER['HTTP_HOST'] . "/biomed-ed/";
	$strippedRoot = str_replace($protocol . $_SERVER['HTTP_HOST'], "", $root);
	$salt = "4f6938531f0bc8991f62da7bbd6f7de3fad44562b8c6f4ebf146d5b4e46f7c17";
	
//Start a session and output buffering
	if ($strippedRoot != "/") {
		$sessionPath = $_SERVER['DOCUMENT_ROOT'] . $strippedRoot . "/system/sessions";
	} else {
		$sessionPath = $_SERVER['DOCUMENT_ROOT'] . "/system/sessions";
	}
	
	session_save_path($sessionPath);
	//session_set_cookie_params("1200");
	session_name("ENSIGMAPRO");
	session_start();
	ob_start();

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
		return "/xampp/xampp/htdocs/biomed-ed/" . $addressAddition . "/";
	}
	
	$relativeAddress = relativeAddress("system/core");
	
//Include additional core scripts, order is important!
	$require = array("global.php", "login_management.php", "constructor.php", "includes.php", "layout.php", "processor.php", "library.php");
	
	foreach($require as $script) {
		require_once($relativeAddress . $script);
	}
?>