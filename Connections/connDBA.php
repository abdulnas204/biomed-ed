<?php
session_start();
ob_start();

/* Begin core functions */
	//Root address for entire site
	$root = "http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/";
	$strippedRoot = str_replace("http://" . $_SERVER['HTTP_HOST'], "", $root);

	//Database connection
	$connDBA = mysql_connect("localhost", "root", "Oliver99");
	$dbSelect = mysql_select_db("biomed-ed", $connDBA);
	
	//Define time zone
	$timeZoneGrabber = mysql_query("SELECT * FROM `siteProfiles` WHERE `id` = '1'", $connDBA);
	$timeZone = mysql_fetch_array($timeZoneGrabber);
	date_default_timezone_set($timeZone['timeZone']);
/* End core functions */	

/* Begin messages functions */
	//Alerts
	function alert($errorContent = NULL) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"alert\">$errorContent</div></div></p><br />";
	}
	
	//Response for errors
	function errorMessage($errorContent = NULL) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"error\">$errorContent</div></div></p><br />";
	}

	//Response for secuess
	function successMessage($successContent) {
		echo "<p><div align=\"center\"><div align=\"center\" class=\"success\">$successContent</div></div></p><br />";
	}
	
	//A centrally located div
	function centerDiv($divContent) {
		echo "<p><div align=\"center\">" . $divContent . "</div></p><br />";
	}
/* End messages functions */

/* Begin site layout functions */	
	//Call site title
	function title($title) {
		global $connDBA;
		global $root;
		
		$strippedTitle = stripslashes($title);
		$siteNameGrabber = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		$siteName = stripslashes($siteNameGrabber['siteName']);
		$value = "<title>{$siteName} | {$strippedTitle}</title>";
		echo $value;
	}
	
	//Include a stylesheet and basic javascripts
	function headers() {
		global $connDBA;
		global $root;
		
		$siteStyleGrabber = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		$siteStyle = $siteStyleGrabber['style'];
		
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "styles/common/universal.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "styles/themes/" . $siteStyle . "\" /><link type=\"";
		
		$iconExtensionGrabber = mysql_query("SELECT * FROM siteProfiles", $connDBA);
		$iconExtension = mysql_fetch_array($iconExtensionGrabber);
		
		switch ($iconExtension['iconType']) {
			case "ico" : echo "image/x-icon"; break;
			case "jpg" : echo "image/jpeg"; break;
			case "gif" : echo "image/gif"; break;
		}
		
		echo "\" rel=\"shortcut icon\" href=\"" . $root . "images/icon." . $iconExtension['iconType'] . "\" />";
		
		$requestURL = $_SERVER['REQUEST_URI'];
		if (strstr($requestURL, "enable_javascript.php")) {
			//Do nothing
		} else {
			echo "<noscript><meta http-equiv=\"refresh\" content=\"0; url=" . $root . "enable_javascript.php\"></noscript>";
		}
		$requestURL = $_SERVER['REQUEST_URI'];
		if (strstr($requestURL, "enable_javascript.php")) {
			echo "<script type=\"text/javascript\">window.location = \"index.php\"</script>
";
		}
	}
	
	//Include user login status
	function loginStatus() {
		global $connDBA;
		global $root;
			
		if (isset ($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			$nameGrabber = mysql_query ("SELECT * FROM users WHERE userName = '{$userName}'", $connDBA);
			$name = mysql_fetch_array($nameGrabber);
			$firstName = $name['firstName'];
			$lastName = $name['lastName'];
			
			switch($_SESSION['MM_UserGroup']) {
				case "Student" : $profileURL = "<a href=\"" . $root . "student/user/index.php\">"; break;
				case "Instructorial Assisstant" : $profileURL = "<a href=\"" . $root . "instructorial_assisstant/users/index.php\">"; break;
				case "Instructor" : $profileURL = "<a href=\"" . $root . "instructor/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Administrative Assisstant" : $profileURL = "<a href=\"" . $root . "administrative_assisstant/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Organization Administrator" : $profileURL = "<a href=\"" . $root . "organization_administrator/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Site Manager" : $profileURL = "<a href=\"" . $root . "site_manager/users/profile.php?id=" . $name['id'] . "\">"; break;
				case "Site Administrator" : $profileURL = "<a href=\"" . $root . "site_administrator/users/profile.php?id=" . $name['id'] . "\">"; break;
			}
			
			echo "You are logged in as " . $profileURL . $firstName . " " . $lastName . "</a> <a href=\"" . $root . "logout.php\">(Logout)</a>";
		} else {
			echo "You are not logged in. <a href=\"" . $root . "login.php\">(Login)</a>";
		}
	}
	
	//Include the logo
	function logo() {
		global $connDBA;
		global $root;
		
		$imageInfoGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);	
		$imageInfo = mysql_fetch_array($imageInfoGrabber);
	
		echo "<div style=\"padding-top:" . $imageInfo['paddingTop'] . "px; padding-bottom:" . $imageInfo['paddingBottom'] . "px; padding-left:" .  $imageInfo['paddingLeft'] . "px; padding-right:" . $imageInfo['paddingRight'] . "px;\">";
		if (isset ($_SESSION['MM_UserGroup'])) {
			switch($_SESSION['MM_UserGroup']) {
				case "Student" : echo "<a href=\"" . $root . "student/index.php\">"; break;
				case "Instructorial Assisstant" : echo "<a href=\"" . $root . "instructorial_assisstant/index.php\">"; break;
				case "Instructor" : echo "<a href=\"" . $root . "instructor/index.php\">"; break;
				case "Administrative Assisstant" : echo "<a href=\"" . $root . "administrative_assisstant/index.php\">"; break;
				case "Organization Administrator" : echo "<a href=\"" . $root . "organization_administrator/index.php\">"; break;
				case "Site Manager" : echo "<a href=\"" . $root . "site_manager/index.php\">"; break;
				case "Site Administrator" : echo "<a href=\"" . $root . "site_administrator/index.php\">"; break;
			}
		} else {
			echo "<a href=\"" . $root . "index.php\">";
		}
		
		echo "<img src=\"" . "" . $root . "images/banner.png\"";
		if ($imageInfo['auto'] !== "on") {
			echo " width=\"" . $imageInfo['width'] . "\" height=\"" . $imageInfo['height'] . "\"";
		} 
		
		echo " alt=\"" . $imageInfo['siteName'] . "\" title=\"" . $imageInfo['siteName'] . "\"></a></div>";
	}
	
	//Meta information
	function meta() {
		global $connDBA;
		global $root;
		
		$meta = mysql_fetch_array(mysql_query ("SELECT * FROM siteprofiles", $connDBA));
	
		echo "<meta name=\"author\" content=\"" . stripslashes($meta['author']) . "\" />
		<meta http-equiv=\"content-language\" content=\"" . stripslashes($meta['language']) . "\" />
		<meta name=\"copyright\" content=\"" . stripslashes($meta['copyright']) . "\" />
		<meta name=\"description\" content=\"" . stripslashes($meta['description']) . "\" />
		<meta name=\"keywords\" content=\"" . stripslashes($meta['meta']) . "\" />
		<meta name=\"generator\" content=\"Ensigma Pro\" />
		<meta name=\"robots\" content=\"index,follow\">";
	}

	//Include a navigation bar
	function navigation($URL) {
		global $connDBA;
		global $root;
		
		$requestURL = $_SERVER['REQUEST_URI'];
		echo "<div id=\"navbar_bg\"><div class=\"navbar clearfix\"><div class=\"breadcrumb\"><div class=\"menu\"><ul>";
		
		switch ($URL) {
		//If this is the public website navigation bar
			case "includes/top_menu.php" :
				$pageData = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);	
				$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages ORDER BY position DESC LIMIT 1", $connDBA));
				
				if (isset ($_GET['page'])) {
					$currentPage = $_GET['page'];
				}
				
				while ($pageInfo = mysql_fetch_array($pageData)) {
					if (isset ($currentPage)) {
						if ($pageInfo['visible'] == "on") {
							if ($currentPage == $pageInfo['id']) {
								echo "<li><a class=\"currentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<li><a class=\"pageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "</li><span class=\"arrow sep\">&#x25BA;</span>";
							} else {
								echo "</li>";
							}
						}
					} else {
						if ($pageInfo['visible'] == "on") {
							if ($pageInfo['position'] == "1") {
								echo "<li><a class=\"currentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<li><a class=\"pageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>"; 
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "</li><span class=\"arrow sep\">&#x25BA;</span>";
							} else {
								echo "</li>";
							}
						}
					}
				}
				break;
				
		//If this is the site administrator navigation bar
			case "site_administrator/includes/top_menu.php" : 
				echo "<li><a class=\"";
				if (!strstr($requestURL, "site_administrator/users") && !strstr($requestURL, "site_administrator/organizations") && !strstr($requestURL, "site_administrator/communication") && !strstr($requestURL, "site_administrator/modules") && !strstr($requestURL, "site_administrator/statistics") && !strstr($requestURL, "site_administrator/cms")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/index.php";
				echo "\">Home</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/users")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/users/index.php";
				echo "\">Users</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/organizations")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/organizations/index.php";
				echo "\">Organizations</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/communication")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/communication/index.php";
				echo "\">Communication</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/modules")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/modules/index.php";
				echo "\">Modules</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/statistics")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/statistics/index.php";
				echo "\">Statistics</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"";
				if (strstr($requestURL, "site_administrator/cms")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/cms/index.php";
				echo "\">Public Website</a></li><span class=\"arrow sep\">&#x25BA;</span>";
				
				echo "<li><a class=\"pageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a></li>";
				break;
		}
		
		echo "</ul></div></div></div></div>";
	}
	
	//Include all top-page items
	function topPage($URL) {
		global $connDBA;
		global $root;
		
		$siteName = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
		
		echo "<div id=\"page\">
		<div id=\"header_bg\">
		<div id=\"header\" class=\"clearfix\"><h1 class=\"headermain\">";
		echo $siteName['siteName'];
		echo "</h1><div class=\"headermenu\"><div class=\"logininfo\">";
		loginStatus();
		echo "</div></div></div><div id=\"banner_bg\"><div id=\"banner\">";
		logo();
		echo "</div></div>";
		navigation($URL);
		echo "</div>";
		echo "<div id=\"content\"><div class=\"box generalbox generalboxcontent boxaligncenter boxwidthwide\">";		
	}
	
	//Include a footer
	function footer($URL) {
		global $connDBA;
		global $root;
		$requestURL = $_SERVER['REQUEST_URI'];
		
		echo "</div></div><div id=\"footer\"><div>&nbsp;</div><div class=\"breadcrumb\">";
		
		switch ($URL) {
		//If this is the public website footer bar
			case "includes/bottom_menu.php" :
				$pageData = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);	
				$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages ORDER BY position DESC LIMIT 1", $connDBA));
				
				if (isset ($_GET['page'])) {
					$currentPage = $_GET['page'];
				}
			
				while ($pageInfo = mysql_fetch_array($pageData)) {
					if (isset ($currentPage)) {
						if ($pageInfo['visible'] != "") {
							if ($currentPage == $pageInfo['id']) {
								echo "<a class=\"currentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"cpageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "<span class=\"arrow sep\">&bull;</span>";
							}
						}
					} else {
						if ($pageInfo['visible'] != "") {
							if ($pageInfo['position'] == "1") {
								echo "<a class=\"currentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							} else {
								echo "<a class=\"cpageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
							}
							
							if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
								echo "<span class=\"arrow sep\">&bull;</span>";
							}
						}
					}
				}
				break;
			
		//If this is the site administrator footer bar
			case "site_administrator/includes/bottom_menu.php" : 
				echo "<a class=\"";
				if (!strstr($requestURL, "site_administrator/users") && !strstr($requestURL, "site_administrator/organizations") && !strstr($requestURL, "site_administrator/communication") && !strstr($requestURL, "site_administrator/modules") && !strstr($requestURL, "site_administrator/statistics") && !strstr($requestURL, "site_administrator/cms")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/index.php";
				echo "\">Home</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/users")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/users/index.php";
				echo "\">Users</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/organizations")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/organizations/index.php";
				echo "\">Organizations</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/communication")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/communication/index.php";
				echo "\">Communication</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/modules")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/modules/index.php";
				echo "\">Modules</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/statistics")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/statistics/index.php";
				echo "\">Statistics</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"";
				if (strstr($requestURL, "site_administrator/cms")) {echo "currentPageNav";} else {echo "pageNav";}
				echo "\" href=\"";
				echo $root . "site_administrator/cms/index.php";
				echo "\">Public Website</a><span class=\"arrow sep\">&bull;</span>";
				
				echo "<a class=\"pageNav\" href=\"";
				echo $root . "logout.php"; 
				echo "\">Logout</a>";
				break;
		}
		
		echo "</div><div class=\"footer\">";
		
		$footerGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);	
		$footer= mysql_fetch_array($footerGrabber);
		
		echo stripslashes($footer['siteFooter']) . "</div></div></div>";
		
		stats("true");
	}
/* End site layout functions */
	
/* Begin login management functions */
	//Login a user
	function login() {
		global $connDBA;
		global $root;
		
		if (isset ($_SESSION['MM_Username'])) {
			$requestedURL = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$homePageCheck = str_replace($root, "", $requestedURL);
			
			if ($homePageCheck !== "index.php") {
				$userRole = $_SESSION['MM_UserGroup'];
				
				switch ($userRole) {
					case "Student" : header ("Location: student/index.php"); exit; break;
					case "Instructorial Assisstant" : header("Location: instructorial_assisstant/index.php"); exit; break;
					case "Instructor" : header ("Location: instructor/index.php"); exit; break;
					case "Administrative Assisstant" : header("Location: administrative_assisstant/index.php"); exit; break;
					case "Organization Administrator" :  header ("Location: organization_administrator/index.php"); exit; break;
					case "Site Manager" : header ("Location: site_manager/index.php"); exit; break;
					case "Site Administrator" : header ("Location: site_administrator/index.php"); exit; break;
				}
			}
		} else {
			if (!function_exists("GetSQLValueString")) {
				function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") {
		  			$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
					$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);
		
					switch ($theType) {
					  case "text" : $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;    
					  case "long":
					  case "int": $theValue = ($theValue != "") ? intval($theValue) : "NULL"; break;
					  case "double": $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL"; break;
					  case "date": $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL"; break;
					  case "defined": $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue; break;
					}
					
					return $theValue;
				}
			}
		
			$loginFormAction = $_SERVER['PHP_SELF'];
			
			if (isset($_GET['accesscheck'])) {
				$_SESSION['PrevUrl'] = $_GET['accesscheck'];
			}
			
			if (isset($_POST['username'])) {
				$loginUsername=$_POST['username'];
				$password=$_POST['password'];
				$MM_fldUserAuthorization = "role";
				
				$userRoleGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$loginUsername}' AND `passWord` = '{$password}'");
				
				if ($userRole = mysql_fetch_array($userRoleGrabber)) {
					$success = "";
					$failure = "";
					
					if (isset($_GET['accesscheck'])) {
						$success .= "http://" . $_SERVER['HTTP_HOST'] . urldecode($_GET['accesscheck']);
					} else {
						switch ($userRole['role']) {
							case "Student" :  $success .= "student/index.php"; break;
							case "Instructorial Assisstant" :  $success .= "instructorial_assisstant/index.php"; break;
							case "Instructor" :  $success .= "instructor/index.php"; break;
							case "Administrative Assisstant" :  $success .= "administrative_assisstant/index.php"; break;
							case "Organization Administrator" :  $success .= "organization_administrator/index.php"; break;
							case "Site Manager" :  $success .= "site_manager/index.php"; break;
							case "Site Administrator" :  $success .= "site_administrator/index.php"; break;
						}
					}
				} else {
					$success = "";
					$failure = "login.php?alert";
				}
			  
				$MM_redirectLoginSuccess = $success;
				$MM_redirectLoginFailed = $failure;
				$MM_redirecttoReferrer = false;
				  
				$LoginRS__query=sprintf("SELECT userName, passWord, role FROM users WHERE userName=%s AND passWord=%s",
				GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
				 
				$LoginRS = mysql_query($LoginRS__query, $connDBA) or die(mysql_error());
				$loginFoundUser = mysql_num_rows($LoginRS);
				
				if ($loginFoundUser) {
					$loginStrGroup  = mysql_result($LoginRS,0,'role');
					
					$_SESSION['MM_Username'] = $loginUsername;
					$_SESSION['MM_UserGroup'] = $loginStrGroup;	
					
					$userIDGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$loginUsername}' AND `passWord` = '{$password}' LIMIT 1");
					$userID = mysql_fetch_array($userIDGrabber);
					setcookie("userStatus", $userID['sysID'], time()+1000000000); 
					
					$cookie = $userID['sysID'];
					mysql_query("UPDATE `users` SET `active` = '1' WHERE `sysID` = '{$cookie}'", $connDBA);
					
			  
				  if (isset($_SESSION['PrevUrl']) && false) {
					  $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
				  }
				  
				  if (!isset($_GET['accesscheck'])) {
					  header("Location: " . $root . $MM_redirectLoginSuccess);
					  exit;
				  } else {
					  header ("Location: " . $success);
					  exit;
				  }
				} else {
				  header("Location: " . $root . $MM_redirectLoginFailed);
				  exit;
				}
			}
		}
	}
	
	//Maintain login status
	function loginCheck($role) {
		global $connDBA;
		global $root;
		
		$MM_authorizedUsers = $role;
		$MM_donotCheckaccess = "false";
		
		function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
		  $isValid = False; 
		  
		  if (!empty($UserName)) { 
			$arrUsers = Explode(",", $strUsers); 
			$arrGroups = Explode(",", $strGroups); 
			if (in_array($UserName, $arrUsers)) { 
			  $isValid = true; 
			} 
			
			if (in_array($UserGroup, $arrGroups)) { 
			  $isValid = true; 
			} 
			if (($strUsers == "") && false) { 
			  $isValid = true; 
			} 
		  } 
		  return $isValid; 
		}
		
		$MM_restrictGoTo = "" . $root . "login.php";
		if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) { 
		  setcookie("userStatus", "", time()-1000000000);  
		  unset($_SESSION['MM_Username']);
		  unset($_SESSION['MM_Usergroup']);
		  $MM_qsChar = "?";
		  $MM_referrer = $_SERVER['PHP_SELF'];
		  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
		  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
		  $MM_referrer .= "?" . $QUERY_STRING;
		  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
		  header("Location: ". $MM_restrictGoTo); 
		  exit;
		}
	}
/* End login management functions */
	
/* Begin page scripting functions */
	//Include the tiny_mce simple widget
	function tinyMCESimple () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "javascripts/common/tiny_mce_simple.js\"></script>";
	}
	
	//Include the tiny_mce advanced widget
	function tinyMCEAdvanced () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "javascripts/common/tiny_mce_advanced.php\"></script><script type=\"text/javascript\" src=\"" . $root . "tiny_mce/plugins/tinybrowser/tb_tinymce.js.php\"></script>";
	}
	
	//Include a form validator
	function validate () {
		global $connDBA;
		global $root;
		
		echo "<link rel=\"stylesheet\" href=\"" . $root . "styles/validation/validatorStyle.css\" type=\"text/css\">";
		echo "<link rel=\"stylesheet\" href=\"" . $root . "styles/validation/validateTextarea.css\" type=\"text/css\">";
		echo "<script src=\"" . $root . "javascripts/validation/validatorCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/validatorOptions.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/runValidator.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/validateTextarea.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/validation/formErrors.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a life updater script
	function liveSubmit() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/liveSubmit/submitterCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "javascripts/liveSubmit/runSubmitter.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the custom checkbox script
	function customCheckbox($type) {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/customCheckbox/checkboxCore.js\" type=\"text/javascript\"></script>";
		if ($type == "checkbox") {
			echo "<script src=\"" . $root . "javascripts/customCheckbox/runCheckbox.js\" type=\"text/javascript\"></script>";
		} elseif ($type == "visible") {
			echo "<script src=\"" . $root . "javascripts/customCheckbox/runVisible.js\" type=\"text/javascript\"></script>";
		}
	}
	
	//Insert live error script
	function liveError() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/liveError/errorCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "javascripts/liveError/runNameError.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the body class
	function bodyClass() {
		global $connDBA;
		global $root;
		
		echo " class=\"theme course-1 dir-ltr lang-en_utf8\"";
	}

	//Include a tooltip	
	function tooltip() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "javascripts/common/tooltip.js\" type=\"text/javascript\"></script>";
	}
/* End page scripting functions */
	
/* Begin form visual functions */		
	//Insert an error window, which will report errors live
	function errorWindow($type, $message, $phpGet = false, $phpError = false, $liveError = false) {
		global $connDBA;
		global $root;
		
		if ($type == "database") {
			if ($liveError == true) {
				if (isset($_GET[$phpGet]) && $_GET[$phpGet] == $phpError) {
						echo "<div align=\"center\" id=\"errorWindow\">" . errorMessage($message) . "</div>";
				} else {
					echo "<div align=\"center\" id=\"errorWindow\"><p>&nbsp;</p></div>";
				}
			} else {
				if ($_GET[$phpGet] == $phpError) {
						echo errorMessage($message);
				} else {
					echo "<p>&nbsp;</p>";
				}
			}
		}
		
		if ($type == "extension") {
			echo "<div align=\"center\"><div id=\"errorWindow\" class=\"error\" style=\"display:none;\">" .$message . "</div></div>";
		}
	}
	
	//If the user is editing the lesson, display a different series of numbering
	function step ($number, $text, $sessionNumber, $sessionText) {
		global $connDBA;
		global $root;
		
		if (isset ($_SESSION['review'])) {
			echo "<div class=\"catDivider " . $sessionNumber . "\">" . $sessionText . "</div>";
		} else {
			echo "<div class=\"catDivider " . $number . "\">" . $text . "</div>";
		}
	}
	
	//Submit a form and toggle the tinyMCE to save its content
	function submit($id, $value) {
		global $connDBA;
		global $root;
		
		echo "<input type=\"submit\" name=\"" . $id . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"tinyMCE.triggerSave();\" />";
	}
	
	//Insert a form errors box, which will report any form errors on submit
	function formErrors () {
		global $connDBA;
		global $root;
		
		echo "<div id=\"errorBox\" style=\"display:none;\">Some fields are incomplete, please scroll up to correct them.</div><div id=\"progress\" style=\"display:none;\"><p><span class=\"require\">Uploading in progress... </span><img src=\"" . $root . "images/common/loading.gif\" alt=\"Uploading\" width=\"16\" height=\"16\" /></p></div>";
	}
/* End form visual functions */
	
/* Begin system functions */
	//Generate a random string
	function randomValue($length = 8, $seeds = 'alphanum') {
		global $connDBA;
		global $root;
		
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] = '0123456789';
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['hexidec'] = '0123456789abcdef';
		
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}
		
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		$string = '';
		$seeds_count = strlen($seeds);
		
		for ($i = 0; $length > $i; $i++) {
			$string .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $string;
	}
	
	//A function to limit the length of the directions
	function commentTrim ($length, $value) {
		global $connDBA;
		global $root;
		
	   $commentsStrip = preg_replace("/<img[^>]+\>/i", "(image)", $value);
	   $comments = strip_tags($commentsStrip);
	   $maxLength = $length;
	   $countValue = html_entity_decode($comments);
	   if (strlen($countValue) <= $maxLength) {
		  return stripslashes($comments);
	   }
	
	   $shortenedValue = substr($countValue, 0, $maxLength - 3) . "...";
	   return $shortenedValue;
	}
	
	//A function to check the extension of a file
	function extension ($targetFile) {
		$entension = explode(".", $targetFile);
		$value = count($entension)-1;
		$entension = $entension[$value];
		$output = strtolower($entension);
		return $output;
	}
	
	//A function to delete a folder and all of its contents
	function deleteAll($directory, $empty = false) {
		if(substr($directory,-1) == "/") {
			$directory = substr($directory,0,-1);
		}
	
		if(!file_exists($directory) || !is_dir($directory)) {
			return false;
		} elseif(!is_readable($directory)) {
			return false;
		} else {
			$directoryHandle = opendir($directory);
			
			while ($contents = readdir($directoryHandle)) {
				if($contents != '.' && $contents != '..') {
					$path = $directory . "/" . $contents;
					
					if(is_dir($path)) {
						deleteAll($path);
					} else {
						unlink($path);
					}
				}
			}
			
			closedir($directoryHandle);
	
			if($empty == false) {
				if(!rmdir($directory)) {
					return false;
				}
			}
			
			return true;
		}
	}
/* End system functions */
	
/* Begin statistics tracker */
	//Overall statistics
	function stats($doAction = "false") {
		global $root;
		global $connDBA;
		
		if ($doAction == "true") {
			$date = date("M-d-Y");
			$statisticsCheck = mysql_query("SELECT * FROM `overallstatistics` WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			if ($result = mysql_fetch_array($statisticsCheck)) {
				$newHit = $result['hits']+1;
				mysql_query("UPDATE `overallstatistics` SET `hits` = '{$newHit}' WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			} else {
				mysql_query("INSERT INTO `overallstatistics` (
							`id`, `date`, `hits`
							) VALUES (
							NULL, '{$date}', '1'
							)");
			}
		}
	}
/* End statistics tracker */
?>