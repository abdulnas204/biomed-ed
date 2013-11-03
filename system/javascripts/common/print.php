<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: December 13th, 2010
Last updated: December 13th, 2010

This script is to print the contents of a targeted DIV.
*/

//Header functions
	require_once('../../core/index.php');
	
//Return a javascript file
	header("Content-type: text/javascript");
?>
//Print the contents of a targeted DIV, courtesy of Webdeveloper.com [http://www.webdeveloper.com/forum/showthread.php?t=162079]

function printContents(containerID) {
	var element = document.getElementById(containerID);
	var contents = element.innerHTML;
	var printer = window.open("", "Print", "status=yes,scrollbars=yes,resizable=yes,width=640,height=480");
	
	printer.document.open();
	printer.document.write("<html><head><title>Payment Complete</title><link rel='stylesheet' type='text/css' href='<?php echo $root; ?>system/styles/common/universal.css' /></head><body onUnload='window.print()'>" + contents + "</body></html>");
    setTimeout(function(){printer.close();},1);
}