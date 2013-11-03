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
Last updated: February 9th, 2011

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
			die();
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
	//Strip the paramters from the preview URL
		if (!loggedIn()) {
			$requestedURL = "";
			$parameters = explode("/", $_SERVER['REQUEST_URI']);
			$limit = sizeof($parameters);
			
			for($count = 0; $count <= $limit - 4; $count ++) {
				$requestedURL .= $parameters[$count] . "/";
			}
			
			$requestedURL = $requestedURL . $parameters[$limit - 1];
		} else {
			$requestedURL = $_SERVER['REQUEST_URI'];
		}
		
	//Generate the URL to the file	
		if ($protocol == "https://") {
			$gatewayFilePrep = explode("?", "https://" . $_SERVER['HTTP_HOST'] . $requestedURL);
		} else {
			$gatewayFilePrep = explode("?", "http://" . $_SERVER['HTTP_HOST'] . $requestedURL);
		}
		
		$gatewayFile = urldecode(str_replace($pluginRoot . "gateway.php/", "", $gatewayFilePrep['0']));
		$fileSize = filesize($gatewayFile);
		
	//Check to see if the file exists
		if (!file_exists($gatewayFile) || is_dir($gatewayFile)) {
			redirect($root . "system/deny/index.php?error=404");
		}
		
	//The document preview generator is the only time a document may be accessed without a login
		if (!loggedIn()) {
			$sessionID = encrypt($parameters[$limit - 3]);
			$magicKey = $parameters[$limit - 2];
			$time = time() - 60;
			
		//Check to see if a user with this sessionID exists, and that the provided magic key is still active
			if (exist("users", "sessionID", $sessionID) && exist("magickeys", "key", $magicKey)) {
				$keyData = query("SELECT * FROM `magickeys` WHERE `key` = '{$magicKey}'");
				
				if ($time < $keyData['timeStamp']) {
					open();
				}
			}
		} else {
			$directoryAttn = explode("gateway.php/", $requestedURL);
			$directoryArray = explode("/", $directoryAttn['1']);
			$directoryDepth = sizeof($directoryArray) - 1;
			
		//Site administrators will have access to lesson and answer files from learning units
			if (access("Edit Unowned Learning Units") && !access("Purchase Learning Unit")) {
				for ($count = 0; $count <= $directoryDepth; $count++) {	
					if ($count == "1") {
						if ($directoryArray['1'] == "lesson" || $directoryArray['1'] == "test") {
							open();
						}
					}
				}
			}
			
		//Students will have access to lesson and answer files partaining to them
			if (access("Purchase Learning Unit")) {
				$fileAccess = unserialize($userData['learningunits']);
				
				if (strstr($directoryArray['0'], "unit_")) {
					$id = str_replace("unit_", "", $directoryArray['0']);
				} else {
					
				}
				
				$unitInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$id}'");
				
				for ($count = 0; $count <= $directoryDepth; $count++) {
					if ($count == "2" && array_key_exists($id, $fileAccess)) {
					//Check if access is allowed to test-related files
						if ($directoryArray['1'] == "test") {
							$testAttempt = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$id}' ORDER BY `attempt` DESC LIMIT 1");
							$fileDataGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$id}' AND `type` = 'File Response'", "raw");
							$userFiles = array();
							$testFiles = array();
							
						//Fetch all files that the user uploaded for the current test
							while($fileData = fetch($fileDataGrabber)) {
								foreach(unserialize($fileData['userAnswer']) as $file) {
									array_push($userFiles, urlencode($file));
								}
								
								array_push($testFiles, urlencode($fileData['testAnswer']));
							}
							
						//After the test has been completed, check to see if the user will have access to their answers, and the correct answers
							foreach(unserialize($unitInfo['display']) as $setting) {
								switch ($setting) {
									case "2" : $selectedAnswers = true; break;
									case "3" : $correctAnswers = true; break;
								}
							}
							
						//During the test session only allow access to files that the user has uploaded for the current attempt
							if ($fileAccess[$id]['testStatus'] == "O") {
								if ($directoryArray['2'] == "responses" && in_array(urlencode($directoryArray['3']), $userFiles)) {
									open();
								}
							}
							
						//If the user's test requires them, not an instructor, to manually grade their input then, allow access to their answers, and the correct answers
							if ($fileAccess[$id]['testStatus'] == "A") {
								if (($directoryArray['2'] == "responses" && in_array($directoryArray['3'], $userFiles)) || ($directoryArray['2'] == "answers" && in_array($directoryArray['3'], $testFiles))) {
									open();
								}
							}
							
						//When the test has been finished, check to see the user's access to their answers, and the correct answers
							if ($fileAccess[$id]['testStatus'] == "F") {
								if (($selectedAnswers && $directoryArray['2'] == "responses" && in_array($directoryArray['3'], $userFiles)) || ($correctAnswers && $directoryArray['2'] == "answers" && in_array($directoryArray['3'], $testFiles))) {
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
		die(errorMessage("A file was not provided."));
	}
?>