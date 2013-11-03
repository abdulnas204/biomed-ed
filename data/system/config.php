<?php
/*
LICENSE: See "license.php" located at the root installation

This is the root configuration file for the entire application.
*/

	$config = array(
	//Database connection for main application
		"serverMain" => "localhost",
		"userMain" => "root",
		"passwordMain" => "Oliver99",
		"dbMain" => "biomed-ed",
		
	//Database connection for tabbed navigation
		"serverNavigation" => "localhost",
		"userNavigation" => "root",
		"passwordNavigation" => "Oliver99",
		"dbNavigation" => "navigation",
		
	//Installation directory configuration
		"installDomain" => $_SERVER['HTTP_HOST'] . "/biomed-ed/",
		"installPath" => "/xampp/htdocs/biomed-ed/",
		
	//Security configuration
		"salt" => "4f6938531f0bc8991f62da7bbd6f7de3fad44562b8c6f4ebf146d5b4e46f7c17",
		"sessionSuffix" => "HJF789HF6",
		"cookieLife" => "1200",
		"errorReporting" => "-1",
		
	//Credentials for system administrative login
		"userAdmin" => "spryno724",
		"passwordAdmin" => "Oliver99"
	);
?>