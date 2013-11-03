<?php require_once('Connections/connDBA.php'); ?>
<?php
//Script to selectively allow access to files
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
	
	//Check ot see if the file exists
		if (!file_exists($gatewayFile) || is_dir($gatewayFile)) {
			header("Location: includes/access_deny.php?error=404");
			exit;
		}
	
	//Site administrators will have access to lesson and answer files from modules
		if ($_SESSION['MM_UserGroup'] == "Site Administrator") {
			header('Content-Description: File Transfer');
			
			for ($count = 0; $count <= $directoryDepth; $count++) {
				if ($count == "2") {
					if (in_array("test", $directoryArray)) {
						$mimeType = getMimeType($gatewayFile);
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
					
					if (in_array("lesson", $directoryArray)) {
						switch (extension($directoryArray[$directoryDepth])) {
							case "pdf" : header("Content-type: application/pdf"); break;
							case "doc" : header("Content-type: application/msword"); break;
							case "docx" : header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document"); break;
							case "ppt" : header("Content-type: application/vnd.ms-powerpoint");; break;
							case "pptx" : header("Content-type: application/vnd.openxmlformats-officedocument.presentationml.presentation"); break;
							case "xls" : header("Content-type: application/vnd.ms-excel"); break;
							case "xlsx" : header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"); break;
							case "txt" : header("Content-type: text/plain"); break;
							case "rtf" : header("Content-type: text/rtf"); break;
							case "wav" : header("Content-type: audio/x-wav"); break;
							case "mp3" : header("Content-type: audio/mpeg"); break;
							case "avi" : header("Content-type: video/x-msvideo"); break;
							case "wmv" : header("Content-type: video/x-ms-wmv"); break;
							case "flv" : header("Content-type: video/x-flv"); break;
							case "mov" : header("Content-type: video/quicktime"); break;
							case "mp4" : header("Content-type: video/mp4"); break;
							case "swf" : header("Content-type: application/x-shockwave-flash"); break;
						}
						
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
				}
			}
		}
	} else {
		die(centerDiv("A file was not provided"));
	}
?>