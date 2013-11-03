<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: Janurary 10th, 2011
Last updated: Janurary 10th, 2011

This is the preview script, which will selectively allow 
access to secured filed based on the user's credentials, 
access to the subject, and other conditions.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Register a magic access key for external access
	function registerKey() {
		$time = strtotime("now");
		$key = session_id() . $time . randomValue(15);
		
		query("INSERT INTO `magickeys` (
			  `id`, `timeStamp`, `key`
			  ) VALUES (
			  NULL, '{$time}', '{$key}'
			  )");
			  
		return $key;
	}
	
//If a file was handed into the gateway
	if (sizeof(explode("/", $_SERVER['REQUEST_URI'])) > sizeof(explode("/", $strippedRoot))) {	
		$requestedURL = "";
		$parameters = explode("/", $_SERVER['PHP_SELF']);
		$limit = sizeof($parameters);
		
		for($count = 0; $count <= $limit - 2; $count ++) {
			$requestedURL .= $parameters[$count] . "/";
		}
		
		$requestedURL = rtrim($requestedURL, "/");
			
		if ($protocol == "https://") {
			$gatewayFilePrep = explode("?", "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		} else {
			$gatewayFilePrep = explode("?", "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
		
		$gatewayFile = urldecode(str_replace($pluginRoot . "preview.php/", "", $gatewayFilePrep['0']));
		
	//Expose the directory path and file type
		$directoryArray = explode("/", $gatewayFile);
		$directoryDepth = sizeof($directoryArray) - 1;
		$filePath = explode("/", $gatewayFile);
		$fileDepth = sizeof($filePath) - 1;
		$fileSize = filesize($gatewayFile);
		
		for ($count = 0; $count <= $fileDepth; $count++) {
			if ($count == $directoryDepth) {
				$fileName = $filePath[$count];
			}
		}
		
	//Check to see if the file exists
		if (!file_exists($gatewayFile) || is_dir($gatewayFile)) {
			//redirect($root . "system/deny/index.php?error=404");
		}
		
	//Site administrators will have access to lesson and answer files from modules
		if ($_SESSION['role'] == "Site Administrator") {
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($count == "1") {
					if ($directoryArray['1'] == "lesson" || $directoryArray['1'] == "test") {
						if($protocol == "https://") {
							redirect("https://docs.google.com/viewer?url=" . urlencode(str_replace($pluginRoot . "preview.php", $pluginRoot . "gateway.php", "https://" . $_SERVER['HTTP_HOST'] . $requestedURL) . "/" . session_id() . "/" . registerKey() . "/" . $parameters[$limit - 1]) . "&embedded=true", false);
						} else {
							redirect("http://docs.google.com/viewer?url=" . urlencode(str_replace($pluginRoot . "preview.php", $pluginRoot . "gateway.php", "http://" . $_SERVER['HTTP_HOST'] . $requestedURL) . "/" . session_id() . "/" . registerKey() . "/" . $parameters[$limit - 1]) . "&embedded=true", false);
						}
					}
				}
			}
		}
		
		redirect($root . "system/deny/index.php?error=403");
	} else {
		die(centerDiv("A file was not provided."));
	}
?>