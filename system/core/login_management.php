<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

/* 
Created by: Oliver Spryn
Created on: Novemeber 27th, 2010
Last updated: Novemeber 28th, 2010

This script is used to process, maintain, and secure all 
login actions.
*/

/*
Data encryption
---------------------------------------------------------
*/

//Encrypt a string, opposite of decrypt()
	function encrypt($string) {
		$search = str_split(" ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890`~!@#$%^&*()-_=+[{]}|;:',<.>/?\\\"");
		$replace = str_split(" B)3Z/~8tr;`y%oJ{X(Mx}2kDc=7<AaSCzNh&5n\"[Il!@gRP]\\$mwb?#4p*0eK6QLHdEv^,Uj:-|9O'qsufY>1iFTGVW.+_");
		$encrypt = "";
		
		foreach(str_split($string) as $segement) {
			if ($segement == "") {
				$encrypt .= " ";
			} else {
				$key = array_keys($search, $segement);
				$encrypt .= $replace[$key['0']];
			}
		}
		
		return base64_encode(gzdeflate($encrypt));
	}
	
//Decrypt a string, opposite of encrypt()
	function decrypt($string) {
		$search = str_split(" B)3Z/~8tr;`y%oJ{X(Mx}2kDc=7<AaSCzNh&5n\"[Il!@gRP]\\$mwb?#4p*0eK6QLHdEv^,Uj:-|9O'qsufY>1iFTGVW.+_");
		$replace = str_split(" ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890`~!@#$%^&*()-_=+[{]}|;:',<.>/?\\\"");
		$decrypt = "";
		
		foreach(str_split(gzinflate(base64_decode($string))) as $segement) {
			if ($segement == "") {
				$decrypt .= " ";
			} else {
				$key = array_keys($search, $segement);
				$decrypt .= $replace[$key['0']];
			}
		}
		
		return $decrypt;
	}
	
/*
Login management
---------------------------------------------------------
*/

//Check to see if a user is logged in
	function loggedIn() {
		if (isset($_SESSION['userName']) && isset($_SESSION['role'])) {
			return true;
		} else {
			return false;
		}
	}

//Process a login request
	function login() {
		global $root;
		
	//Do not allow access to the login page if the user is already logged in
		if (loggedIn()) {
			$requestedURL = $_SERVER['PHP_SELF'];
			
			if (strstr($requestedURL, "login.php")) {
				redirect($root . "portal/index.php");
			}
	//If the user in not logged in, then login the user
		} else {
			if (isset($_POST['submit']) && !empty($_POST['userName']) && !empty($_POST['passWord'])) {
				$userName = $_POST['userName'];
				$passWord = encrypt($_POST['passWord']);
				$userInfo = query("SELECT * FROM `users` WHERE `userName` = '{$userName}' AND `passWord` = '{$passWord}'");
				
				if ($userInfo) {
					$timeStamp = time();
					
					query("UPDATE `users` SET `active` = '{$timeStamp}' WHERE `id` = '{$userInfo['id']}'", $connDBA);
					
					$_SESSION['userName'] = $userInfo['userName'];
					$_SESSION['role'] = $userInfo['role'];	
					
					if (isset($_GET['redirect'])) {
						redirect("http://" . $_SERVER['HTTP_HOST'] . urldecode($_GET['redirect']));
					} else {
						redirect($root . "portal/index.php");
					}
				} else {
					if (!isset($_GET['redirect'])) {
						redirect($root . "login.php?alert=true");
					} else {
						redirect($root . "login.php?redirect=" . $_GET['redirect'] . "&alert=true");
					}
				}
			}
		}
	}
	
//Process a logout request
	function logout($total = true) {
	//If $total == true, the destroy the session data
		if ($total == true) {
			session_destroy();
	//If $total == false, then simply log out of the developer administration area
		} else {
			unset($_SESSION['developerAdministration']);
		}
	}
	
//Maintain login status
	function maintain($role) {
		global $root;
		
		if (!loggedIn() || $_SESSION['role'] !== $role) {
			unset($_SESSION['userName'], $_SESSION['role'], $_SESSION['developerAdministration']);
			$redirect = urlencode($_SERVER['REQUEST_URI']);
			redirect($root . "login.php?redirect=" . $redirect);
		  }
	  }
	  
//Grab the user's data
	function userData() {
		return query("SELECT * FROM `users` WHERE `userName` = '{$_SESSION['userName']}'");
	}
?>