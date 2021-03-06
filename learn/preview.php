<?php
/*
LICENSE: See "license.php" located at the root installation

This is the preview script, which will selectively allow the Google Docs previewer server to access to secured files based on the user's credentials, access to the subject, and other conditions.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
//Register a magic access key for external access
	function registerKey() {
		$time = time();
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
	//Generate the URL to the file	
		if ($protocol == "https://") {
			$gatewayFilePrep = explode("?", "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		} else {
			$gatewayFilePrep = explode("?", "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
		
		$gatewayFile = "../data/learn/" . urldecode(str_replace($pluginRoot . "preview.php/", "", $gatewayFilePrep['0']));
		
	//Generate the preview URL, with necessary parameters
		$requestedURL = "";
		$parameters = explode("/", $_SERVER['PHP_SELF']);
		$limit = sizeof($parameters);
		
		for($count = 0; $count <= $limit - 2; $count ++) {
			$requestedURL .= $parameters[$count] . "/";
		}
		
		$requestedURL = rtrim($requestedURL, "/");
		
	//Check to see if the file exists
		if (!file_exists($gatewayFile) || is_dir($gatewayFile)) {
			redirect($root . "system/deny/index.php?error=404");
		}
		
	//Check to see if a supported file type is being requested
		if (!in_array(extension(end(explode("/", $_SERVER['PHP_SELF']))), array("pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx"))) {
			redirect(str_replace("preview.php", "gateway.php", "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']), false);
		}
		
	//Send the request to display the document, the gateway will decide whether or not access should be granted
		if ($protocol == "https://") {
			redirect("https://docs.google.com/viewer?url=" . urlencode(str_replace($pluginRoot . "preview.php", $pluginRoot . "gateway.php", "https://" . $_SERVER['HTTP_HOST'] . $requestedURL) . "/" . session_id() . "/" . registerKey() . "/" . $parameters[$limit - 1]) . "&embedded=true", false);
		} else {
			redirect("http://docs.google.com/viewer?url=" . urlencode(str_replace($pluginRoot . "preview.php", $pluginRoot . "gateway.php", "http://" . $_SERVER['HTTP_HOST'] . $requestedURL) . "/" . session_id() . "/" . registerKey() . "/" . $parameters[$limit - 1]) . "&embedded=true", false);
		}
	} else {
		die(errorMessage("A file was not provided."));
	}
?>