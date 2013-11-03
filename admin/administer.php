<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: November 30th, 2010
Last updated: Novemeber 30th, 2010

This is the page which will load the administation plugin 
from other loaded plugins.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("admin/system/php") . "index.php");
	require_once(relativeAddress("admin/system/php") . "functions.php");
	
//Check to see if the proper values were passed in, and if the variables are correct, if so, set a session and proceed
	if (isset($_GET['plugin']) && !empty($_GET['plugin']) && file_exists("../" . $_GET['plugin'] . "/system/php/admin/index.php")) {
		$_SESSION['plugin'] = $_GET['plugin'];
		redirect("administer.php/index.php");
	} elseif (!isset($_SESSION['plugin']) && (!isset($_GET['plugin']) || empty($_GET['plugin']))) {
		unset($_SESSION['plguin']);
		die(errorMessage("No handlers were passed in for processing."));
	} elseif (isset($_SESSION['plugin']) || isset($_GET['plugin'])) {
		if (isset($_SESSION['plugin']) && !isset($_GET['plugin'])) {
			$URL = $_SESSION['plugin'];
		} elseif ($URL = $_GET['plugin']) {
			$URL = $_GET['plugin'];
		} else {
			unset($_SESSION['plguin']);
			die(errorMessage("No handlers were passed in for processing."));
		}
		
		if (!file_exists("../" . $URL . "/system/php/admin/index.php")) {
			unset($_SESSION['plguin']);
			die(errorMessage("This plugin does not exist."));
		}
	}
	
//Import the page
	require_once(relativeAddress($_SESSION['plugin'] . "/system/php") . "index.php");
	require_once(relativeAddress($_SESSION['plugin'] . "/system/php") . "functions.php");
	require_once("../" . $_SESSION['plugin'] . "/system/php/admin/" . str_replace(($_SERVER['SCRIPT_NAME']) . "/", "", $_SERVER['REQUEST_URI']));	
	exit;
?>
	
	