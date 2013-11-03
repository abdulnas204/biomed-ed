<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: Novemeber 28th, 2010
Last updated: Novemeber 28th, 2010

This script contains additional functions relevent to this 
plugin only.
*/

/*
Server-side functions
---------------------------------------------------------
*/

//Restrict access to developer-only parts of the site
	function developerAccess() {
		global $root;
		
		if (!strstr($_SERVER['PHP_SELF'], "login.php")) {
			if (!loggedIn() || !isset($_SESSION['developerAdministration'])) {
				redirect($root . "admin/login.php");
			}
		} else {
			if (loggedIn() && isset($_SESSION['developerAdministration'])) {
				redirect($root . "admin/index.php");
			}
		}
	}
	
	if (strstr($_SERVER['PHP_SELF'], "/admin")) {
		developerAccess();
	}
	
/*
Include JavaScripts and CSS for client-side modules
---------------------------------------------------------
*/
?>