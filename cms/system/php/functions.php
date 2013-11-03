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
Last updated: Janurary 3rd, 2011

This script contains additional functions relevent to this 
plugin only.
*/

/*
Server-side functions
---------------------------------------------------------
*/
	
/*
Include JavaScripts and CSS for client-side modules
---------------------------------------------------------
*/

//TinyMCE designer
	function tinyMCEDesigner() {
		global $root, $pluginRoot;
		
		return "<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/tiny_mce.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/AtD/editor_plugin.js\"></script>
<script type=\"text/javascript\" src=\"" . $pluginRoot . "system/javascripts/tiny_mce_designer.php\"></script>";
	}
?>