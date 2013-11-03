<?php 
//Header functions
	require_once('system/connections/connDBA.php');
	
//Script to selectively allow access to files
//Create a function to open the file
	function open() {
		global $gatewayFile, $fileSize;
		
		$mimeType = getMimeType($gatewayFile);
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
	
//If a file extension was handed into the gateway
	if (sizeof(explode("/", $_SERVER['REQUEST_URI'])) > sizeof(explode("/", $strippedRoot))) {
		$gatewayFile = urldecode(str_replace($strippedRoot . "gateway.php/", "", urldecode($_SERVER['REQUEST_URI'])));
		
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
			redirect($root . "includes/access_deny.php?error=404");
		}
	
	//Site administrators will have access to lesson and answer files from modules
		if ($_SESSION['MM_UserGroup'] == "Site Administrator") {			
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($count == "2") {
					if ($directoryArray['2'] == "lesson" || $directoryArray['2'] == "test") {
						open();
					}
				}
			}
		}
		
	//Organization administrators will have access 
		if ($_SESSION['MM_UserGroup'] == "Organization Administrator") {
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($count == "2") {
					if ($directoryArray['2'] == "lesson") {
						open();
					}
				}
			}
		}
		
	//Student will have access to lesson and answer files partaining to them
		if ($_SESSION['MM_UserGroup'] == "Student") {
			$userData = userData();
			$moduleAccess = unserialize($userData['modules']);
			
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($count == "2" && array_key_exists($directoryArray['1'], $moduleAccess)) {
					if ($directoryArray['2'] == "test") {
						$fileDataGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$directoryArray['1']}' AND `type` = 'File Response'", "raw");
						$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$directoryArray['1']}'");
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
						
						if ($moduleAccess[$directoryArray['1']]['testStatus'] == "O") {
							if (isset($selectedAnswers) && $directoryArray['3'] == "responses" && in_array($directoryArray['4'], $userFiles)) {
								open();
							}
						}
						
						if ($moduleAccess[$directoryArray['1']]['testStatus'] == "A" || $moduleAccess[$directoryArray['1']]['testStatus'] == "F") {
							if (isset($selectedAnswers) && $directoryArray['3'] == "responses" && in_array($directoryArray['4'], $userFiles)) {
								open();
							}
							
							if (isset($correctAnswers) && $directoryArray['3'] == "answers" && in_array($directoryArray['4'], $testFiles)) {
								open();
							}
						}
					}
					
					if ($directoryArray['2'] == "lesson") {
						if ($moduleAccess[$directoryArray['1']]['testStatus'] == "C" || $moduleAccess[$directoryArray['1']]['testStatus'] == "F") {
							open();
						}
					}
				}
			}
		}
		
		redirect($root . "includes/access_deny.php?error=403");
	} else {
		die(centerDiv("A file was not provided."));
	}
?>