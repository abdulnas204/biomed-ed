<?php
/*
LICENSE: See "license.php" located at the root installation

This script is the core of the system, which contains key information and definitions which will be used globally.
*/

//Include the configuration file
	strstr(dirname(__FILE__), "\\") ? $configScript = str_replace("system\server", "", dirname(__FILE__)) . "data\system\config.php" : $configScript = str_replace("system/server", "", dirname(__FILE__)) . "data/system/config.php";
	require_once($configScript);
	
//Info address for entire site
	if (!empty($_SERVER['HTTPS'])) {
		$protocol = "https://";
	} else {
		$protocol = "http://";
	}
	
	$root = $protocol . $config['installDomain'];
	$strippedRoot = str_replace($protocol . $_SERVER['HTTP_HOST'], "", $root);
	$salt = $config['salt'];
	
//Start a session
	session_save_path($config['installPath'] . "system/sessions");
	session_name("ENSIGMAPRO_" . $config['sessionSuffix']);
	session_start();
	setcookie(session_name(), session_id(), time() + $config['cookieLife'], "/");

//Database connection for main application
	$connDBA = mysql_connect($config['serverMain'], $config['userMain'], $config['passwordMain']);
	mysql_select_db($config['dbMain'], $connDBA);
	
//Database connection for tabbed navigation
	$connNavigation = mysql_connect($config['serverNavigation'], $config['userNavigation'], $config['passwordNavigation'], true);
	mysql_select_db($config['dbNavigation'], $connNavigation);
	
//Define time zone
	$timeZoneGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
	$timeZone = mysql_fetch_array($timeZoneGrabber);
	date_default_timezone_set($timeZone['timeZone']);
	
//Credentials for system administrative login
	$rootUserName = $config['userAdmin'];
	$rootPassWord = $config['passwordAdmin'];
	
//Set server configurations
	set_time_limit(3600);
	ini_set("expose_php", "Off");
	error_reporting($config['errorReporting']);
	
//Create a relative address in order to access other system functions	
	function relativeAddress($addressAddition) {
		global $config;
			
		return $config['installPath'] . $addressAddition . "/";
	}
	
	$relativeAddress = relativeAddress("system/server");
	
//Include additional core scripts, order is important!
	$require = array("global.php", "login_management.php", "constructor.php", "includes.php", "layout.php", "processor.php", "library.php");
	
	foreach($require as $script) {
		require_once($relativeAddress . $script);
	}
?>