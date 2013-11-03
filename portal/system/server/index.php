<?php
/*
LICENSE: See "license.php" located at the root installation

This script contains additional functions relevent to this addon only.
*/

/*
Global server-side declarations
---------------------------------------------------------
*/

	$pluginRootPrep = str_replace($root, "", $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
	$pluginRootArray = explode("/", $pluginRootPrep);
	$pluginRoot = $root . $pluginRootArray['0'] . "/";

/*
Server-side functions
---------------------------------------------------------
*/

/*
Include JavaScripts and CSS for client-side modules
---------------------------------------------------------
*/

//Include a script to convert selected divs to jQuery styled portlets
	function portlet() {
		global $pluginRoot;
		
		return "<script src=\"" . $pluginRoot . "system/javascripts/portlet.js\" type=\"text/javascript\"></script>";
	}
?>