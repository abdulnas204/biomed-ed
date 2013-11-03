<?php require_once('Connections/connDBA.php'); ?>
<?php
//Script to selectively allow access to files
//If a file extension was handed into the gateway
	if (isset($_GET['file'])) {
		$gatewayFile = $_GET['file'];
		
	//Check to see if the file exists
		if (!file_exists($gatewayFile)) {
			errorMessage("The file does not exist");
			return false;
		}
		
	//Define the extension to detirmine what kind of file is being used
		$gatewayArray = explode(".", $gatewayFile);
		$extension = $gatewayArray['1'];
		
	//Define the folder name
		$directory = explode("/", $gatewayFile);
		
	//Count the number of letters in the extension for verification
		if (strlen($extension) !== "2" && strlen($extension) !== "3") {
			errorMessage("There was an error identifying the file type");
			return false;
		}
		
	//Selectivly grant access to system files
		//CSS
		if ($directory['0'] == "styles") {
			if ($directory['1'] == "common") {
				header ("Content-type: text/css");
				require_once($gatewayFile);
			}
		}
//If a file extension was not handed into the gateway	
	} else {
		errorMessage("A file was not handed into the gateway");
		return false;
	}
?>