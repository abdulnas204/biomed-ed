<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: August 13th, 2010
Last updated: Janurary 10th, 2011

This is the gateway script, which will selectively allow 
access to secured filed based on the user's credentials, 
access to the subject, and other conditions.
*/

//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Open the file
	function open() {
		global $gatewayFile, $fileSize;
		
		if (isset($_GET['force']) && $_GET['force'] == "true") {
			$mimeType = "application/octet-stream";
		} else {
			$mimeType = getMimeType($gatewayFile);
		}
		
		header('Content-Description: File Transfer');
		header("Content-type: " . $mimeType);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $fileSize);
		ob_clean();
		flush();
		readfile($gatewayFile);
		exit;
	}
	
//If a file was handed into the gateway
	if (sizeof(explode("/", $_SERVER['REQUEST_URI'])) > sizeof(explode("/", $strippedRoot))) {
		$requestedURL = "";
		$parameters = explode("/", $_SERVER['PHP_SELF']);
		$limit = sizeof($parameters);
		
		for($count = 0; $count <= $limit - 4; $count ++) {
			$requestedURL .= $parameters[$count] . "/";
		}
		
		$requestedURL .= $parameters[$limit - 1];
		
		if ($protocol == "https://") {
			$gatewayFilePrep = explode("?", "https://" . $_SERVER['HTTP_HOST'] . $requestedURL);
		} else {
			$gatewayFilePrep = explode("?", "http://" . $_SERVER['HTTP_HOST'] . $requestedURL);
		}
		
		$gatewayFile = urldecode(str_replace($pluginRoot . "gateway.php/", "", $gatewayFilePrep['0']));
		
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
			redirect($root . "system/deny/index.php?error=404");
		}
		
	open();
	
	//Site administrators will have access to lesson and answer files from modules
		if ($_SESSION['role'] == "Site Administrator") {
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($count == "1") {
					if ($directoryArray['1'] == "lesson" || $directoryArray['1'] == "test") {
						
					}
				}
			}
		}
		
	//Student will have access to lesson and answer files partaining to them
		if (access("Purchase Learning Unit")) {
			$fileAccess = unserialize($userData['learningunits']);
			$id = str_replace("unit_", "", $directoryArray['0']);
			
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($count == "2" && array_key_exists($id, $fileAccess)) {
					if ($directoryArray['1'] == "test") {
						$fileDataGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$id}' AND `type` = 'File Response'", "raw");
						$moduleInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$id}'");
						$userFiles = array();
						$testFiles = array();
						
						while($fileData = mysql_fetch_array($fileDataGrabber)) {
							foreach(unserialize($fileData['userAnswer']) as $file) {
								array_push($userFiles, $file);
							}
							
							array_push($testFiles, $fileData['testAnswer']);
						}
						
						foreach(unserialize($moduleInfo['display']) as $setting) {
							switch ($setting) {
								case "2" : $selectedAnswers = true; break;
								case "3" : $correctAnswers = true; break;
							}
						}
						
						if ($fileAccess[$id]['testStatus'] == "O") {
							if (isset($selectedAnswers) && $directoryArray['3'] == "responses" && in_array($directoryArray['3'], $userFiles)) {
								open();
							}
						}
						
						if ($fileAccess[$id]['testStatus'] == "A" || $fileAccess[$id]['testStatus'] == "F") {
							if (isset($selectedAnswers) && $directoryArray['3'] == "responses" && in_array($directoryArray['3'], $userFiles)) {
								open();
							}
							
							if (isset($correctAnswers) && $directoryArray['2'] == "answers" && in_array($directoryArray['3'], $testFiles)) {
								open();
							}
						}
					}
					
					if ($directoryArray['1'] == "lesson") {
						if (($fileAccess[$id]['testStatus'] == "C" || $fileAccess[$id]['testStatus'] == "F") || $directoryArray['3'] == "public") {
							open();
						}
					}
				}
			}
		}
		
	//Display only public directories to non-logged in users
		if (!loggedIn())  {
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($directoryArray['4'] == "public") {
					open();
				}
			}
		}
		
		redirect($root . "system/deny/index.php?error=403");
	} else {
		die(centerDiv("A file was not provided."));
	}
?>