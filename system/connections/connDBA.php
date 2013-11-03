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
	$timeZoneGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
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
	//Include the start of a page
		function headers($title, $role = false, $functions = false, $toolTip = false, $additionalParameters = false, $publicNavigation = false, $meta = false, $description = false, $additionalKeywords = false, $hideHTML = false, $customScript = false) {
		global $connDBA;
		global $root;
		
	//Maintain login status
		if ($role == true) {
			$MM_authorizedUsers = $role;
			$MM_donotCheckaccess = "false";
			
			function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
			  $isValid = false; 
			  
			  if (!empty($UserName)) {
				$arrUsers = explode(",", $strUsers); 
				$arrGroups = explode(",", $strGroups); 
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
			  unset($_SESSION['MM_Username']);
			  unset($_SESSION['MM_UserGroup']);
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
		
		if ($hideHTML == true && $hideHTML != "XML") {
			$additionalHTML = " class=\"overrideBackground\"" . $additionalParameters;
		} else {
			$additionalHTML = $additionalParameters;
		}
		
		if ($hideHTML !== "XML") {
		//Grab all site info	
			$siteInfo = mysql_fetch_array(mysql_query("SELECT * FROM siteprofiles", $connDBA));
			
		//Include the doctype	
			echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html xmlns=\"http://www.w3.org/1999/xhtml\"><head>";
		
		//Include the title	
			echo "<title>" . $siteInfo['siteName'] .  " | " . $title . "</title>";
		
		//Include necessary scripts
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "system/styles/common/universal.css\" /><link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "system/styles/themes/" . $siteInfo['style'] . "\" /><link type=\"";
		
		//Include the shortcut icon	
			switch ($siteInfo['iconType']) {
				case "ico" : echo "image/x-icon"; break;
				case "jpg" : echo "image/jpeg"; break;
				case "gif" : echo "image/gif"; break;
			}
			
			echo "\" rel=\"shortcut icon\" href=\"" . $root . "system/images/icon." . $siteInfo['iconType'] . "\" />";
			
		//Include additional functions
			if ($functions == true) {
				$functionsArray = explode(",", $functions);
				
				foreach ($functionsArray as $functions) {
					$functions();
				}
			}
			
		//Include a <noscript> redirect	
			$requestURL = $_SERVER['REQUEST_URI'];
			
			if (!strstr($requestURL, "enable_javascript.php")) {
				echo "<noscript><meta http-equiv=\"refresh\" content=\"0; url=" . $root . "enable_javascript.php\"></noscript>";
			} else {
				echo "<script type=\"text/javascript\">window.location = \"index.php\"</script>";
			}
			
		//Include meta information
			if ($meta == true) {
				echo "<meta name=\"author\" content=\"" . stripslashes($siteInfo['author']) . "\" />
				<meta http-equiv=\"content-language\" content=\"" . stripslashes($siteInfo['language']) . "\" />
				<meta name=\"copyright\" content=\"" . stripslashes($siteInfo['copyright']) . "\" />";
				
				if ($description == "") {
					echo "<meta name=\"description\" content=\"" . stripslashes($siteInfo['description']) . "\" />";
				} else {
					echo "<meta name=\"description\" content=\"" . stripslashes(strip_tags($description)) . "\" />";
				}
				
				if ($additionalKeywords == "") {
					echo "<meta name=\"keywords\" content=\"" . stripslashes($siteInfo['meta']) . "\" />";
				} else {
					echo "<meta name=\"keywords\" content=\"" . stripslashes($siteInfo['meta']) . ", " . $additionalKeywords . "\" />";
				}
					
				echo "<meta name=\"generator\" content=\"Ensigma Pro\" />
				<meta name=\"robots\" content=\"index,follow\">";
			}
			
		//Close the header
			echo $customScript . "</head><body" . $additionalHTML . ">";
			
		//Include a tooltip
			if ($toolTip == true) {
				echo "<script src=\"" . $root . "system/javascripts/common/tooltip.js\" type=\"text/javascript\"></script>";
			}
		}
			
		if ($hideHTML == false) {
		//Begin the body HTML
			echo "<div id=\"page\"><div id=\"header_bg\"><div id=\"header\" class=\"clearfix\"><h1 class=\"headermain\">" . $siteInfo['siteName'] . "</h1><div class=\"headermenu\"><div class=\"logininfo\">";
			
		//Include the user login status
			if (isset ($_SESSION['MM_Username'])) {
				$userName = $_SESSION['MM_Username'];
				$nameGrabber = mysql_query ("SELECT * FROM users WHERE userName = '{$userName}'", $connDBA);
				$name = mysql_fetch_array($nameGrabber);
				$firstName = $name['firstName'];
				$lastName = $name['lastName'];
				
				echo "You are logged in as <a href=\"" . $root . "users/profile.php?id=" . $name['id'] . "\">" . $firstName . " " . $lastName . "</a> <a href=\"" . $root . "logout.php\">(Logout)</a>";
			} else {
				echo "You are not logged in. <a href=\"" . $root . "login.php\">(Login)</a>";
			}
		
		//Continue HTML	
			echo "</div></div></div><div id=\"banner_bg\"><div id=\"banner\">";
			
		//Include the logo
			echo "<div style=\"padding-top:" . $siteInfo['paddingTop'] . "px; padding-bottom:" . $siteInfo['paddingBottom'] . "px; padding-left:" .  $siteInfo['paddingLeft'] . "px; padding-right:" . $siteInfo['paddingRight'] . "px;\">";
			
			if (isset ($_SESSION['MM_UserGroup'])) {
				echo "<a href=\"" . $root . "portal/index.php\">";
			} else {
				echo "<a href=\"" . $root . "index.php\">";
			}
			
			echo "<img src=\"" . "" . $root . "system/images/banner.png\"";
			
			if ($siteInfo['auto'] !== "on") {
				echo " width=\"" . $siteInfo['width'] . "\" height=\"" . $siteInfo['height'] . "\"";
			} 
			
			echo " alt=\"" . $siteInfo['siteName'] . "\" title=\"" . $siteInfo['siteName'] . "\"></a></div>";
			
		//Continue HTML
			echo "</div></div>";
		
		//Include the navigation bar
			$requestURL = $_SERVER['REQUEST_URI'];
			echo "<div id=\"navbar_bg\"><div class=\"navbar clearfix\"><div class=\"breadcrumb\"><div class=\"menu\"><ul>";
			
			if ($publicNavigation == false) {
				if (isset($_SESSION['MM_UserGroup'])) {
					switch($_SESSION['MM_UserGroup']) {
						case "Student" : $URL = "Student"; break;
						case "Instructorial Assisstant" : $URL = "Instructorial Assisstant"; break;
						case "Instructor" :$URL = "Instructor"; break;
						case "Administrative Assisstant" : $URL = "Administrative Assisstant"; break;
						case "Organization Administrator" :  $URL = "Organization Administrator"; break;
						case "Site Manager" : $URL = "Site Manager"; break;
						case "Site Administrator" : $URL = "Site Administrator"; break;
					}
				} else {
					$URL = "Public";
				}
			} else {
				$URL = "Public";
			}
			
			switch ($URL) {
			//If this is the public website navigation bar
				case "Public" :
					$pageData = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);	
					$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages ORDER BY position DESC LIMIT 1", $connDBA));
					
					if (isset ($_GET['page'])) {
						$currentPage = $_GET['page'];
					}
					
					while ($pageInfo = mysql_fetch_array($pageData)) {
						if (isset ($currentPage)) {
							if ($pageInfo['visible'] == "on") {
								if ($currentPage == $pageInfo['id']) {
									echo "<li><a class=\"topCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
								} else {
									echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
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
									echo "<li><a class=\"topCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
								} else {
									echo "<li><a class=\"topPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>"; 
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
				case "Site Administrator" : 
					echo "<li><a class=\"";
					if (!strstr($requestURL, "/users") && !strstr($requestURL, "/organizations") && !strstr($requestURL, "/communication") && !strstr($requestURL, "/modules") && !strstr($requestURL, "/statistics") && !strstr($requestURL, "/cms")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "portal/index.php";
					echo "\">Home</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/users")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "users/index.php";
					echo "\">Users</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/organizations")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "organizations/index.php";
					echo "\">Organizations</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/communication")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "communication/index.php";
					echo "\">Communication</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/modules")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "modules/index.php";
					echo "\">Modules</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/statistics")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "statistics/index.php";
					echo "\">Statistics</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/cms")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "cms/index.php";
					echo "\">Public Website</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"topPageNav\" href=\"";
					echo $root . "logout.php"; 
					echo "\">Logout</a></li>";
					break;
					
			//If this is the student navigation bar
				case "Student" :
					echo "<li><a class=\"";
					if (!strstr($requestURL, "/communication") && !strstr($requestURL, "/modules")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "portal/index.php";
					echo "\">Home</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/communication")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "communication/index.php";
					echo "\">Communication</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"";
					if (strstr($requestURL, "/modules")) {echo "topCurrentPageNav";} else {echo "topPageNav";}
					echo "\" href=\"";
					echo $root . "modules/index.php";
					echo "\">Modules</a></li><span class=\"arrow sep\">&#x25BA;</span>";
					
					echo "<li><a class=\"topPageNav\" href=\"";
					echo $root . "logout.php"; 
					echo "\">Logout</a></li>";
					break;
			}
			
			echo "</ul></div></div></div></div>";
		
		//Continue HTML	
			echo "</div>";
			echo "<div id=\"content\"><div class=\"box generalboxcontent boxaligncenter\">";
		}
		
	//Include a footer
		function footer($publicNavigation = false, $hideHTML = false) {
			global $connDBA;
			global $root;
			
			if ($hideHTML == false) {
			//Include the navigation bar
				$requestURL = $_SERVER['REQUEST_URI'];
				echo "<br /></div></div><div id=\"footer\"><div>&nbsp;</div><div class=\"breadcrumb\">";
				
				if ($publicNavigation == false) {
					if (isset($_SESSION['MM_UserGroup'])) {
						switch($_SESSION['MM_UserGroup']) {
							case "Student" : $URL = "Student"; break;
							case "Instructorial Assisstant" : $URL = "Instructorial Assisstant"; break;
							case "Instructor" :$URL = "Instructor"; break;
							case "Administrative Assisstant" : $URL = "Administrative Assisstant"; break;
							case "Organization Administrator" :  $URL = "Organization Administrator"; break;
							case "Site Manager" : $URL = "Site Manager"; break;
							case "Site Administrator" : $URL = "Site Administrator"; break;
						}
					} else {
						$URL = "Public";
					}
				} else {
					$URL = "Public";
				}
				
				switch ($URL) {
				//If this is the public website footer bar
					case "Public" :
						$pageData = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);	
						$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages ORDER BY position DESC LIMIT 1", $connDBA));
						
						if (isset ($_GET['page'])) {
							$currentPage = $_GET['page'];
						}
					
						while ($pageInfo = mysql_fetch_array($pageData)) {
							if (isset ($currentPage)) {
								if ($pageInfo['visible'] != "") {
									if ($currentPage == $pageInfo['id']) {
										echo "<a class=\"bottomCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
									} else {
										echo "<a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
									}
									
									if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
										echo "<span class=\"arrow sep\">&bull;</span>";
									}
								}
							} else {
								if ($pageInfo['visible'] != "") {
									if ($pageInfo['position'] == "1") {
										echo "<a class=\"bottomCurrentPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
									} else {
										echo "<a class=\"bottomPageNav\" href=\"index.php?page=" . $pageInfo['id'] . "\">" . stripslashes($pageInfo['title']) . "</a>";
									}
									
									if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
										echo "<span class=\"arrow sep\">&bull;</span>";
									}
								}
							}
						}
						break;
					
				//If this is the site administrator footer bar
					case "Site Administrator" : 
						echo "<a class=\"";
						if (!strstr($requestURL, "/users") && !strstr($requestURL, "/organizations") && !strstr($requestURL, "/communication") && !strstr($requestURL, "/modules") && !strstr($requestURL, "/statistics") && !strstr($requestURL, "/cms")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
						echo "\" href=\"";
						echo $root . "portal/index.php";
						echo "\">Home</a><span class=\"arrow sep\">&bull;</span>";
						
						echo "<a class=\"";
						if (strstr($requestURL, "/users")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
						echo "\" href=\"";
						echo $root . "users/index.php";
						echo "\">Users</a><span class=\"arrow sep\">&bull;</span>";
						
						echo "<a class=\"";
						if (strstr($requestURL, "/organizations")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
						echo "\" href=\"";
						echo $root . "organizations/index.php";
						echo "\">Organizations</a><span class=\"arrow sep\">&bull;</span>";
						
						echo "<a class=\"";
						if (strstr($requestURL, "/communication")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
						echo "\" href=\"";
						echo $root . "communication/index.php";
						echo "\">Communication</a><span class=\"arrow sep\">&bull;</span>";
						
						echo "<a class=\"";
						if (strstr($requestURL, "/modules")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
						echo "\" href=\"";
						echo $root . "modules/index.php";
						echo "\">Modules</a><span class=\"arrow sep\">&bull;</span>";
						
						echo "<a class=\"";
						if (strstr($requestURL, "/statistics")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
						echo "\" href=\"";
						echo $root . "statistics/index.php";
						echo "\">Statistics</a><span class=\"arrow sep\">&bull;</span>";
						
						echo "<a class=\"";
						if (strstr($requestURL, "/cms")) {echo "bottomCurrentPageNav";} else {echo "bottomPageNav";}
						echo "\" href=\"";
						echo $root . "cms/index.php";
						echo "\">Public Website</a><span class=\"arrow sep\">&bull;</span>";
						
						echo "<a class=\"bottomPageNav\" href=\"";
						echo $root . "logout.php"; 
						echo "\">Logout</a>";
						break;
				}
			
			//Include the footer text	
				echo "</div><div class=\"footer\">";
				
				$footerGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);	
				$footer= mysql_fetch_array($footerGrabber);
				
				echo stripslashes($footer['siteFooter']) . "</div></div></div>";
			}
			
		//Close the HTML
			echo "</body></html>";
		
		//Log stats and activity	
			stats("true");
			activity("true");
			
		}
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
				redirect("portal/index.php");
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
						$success .= "portal/index.php";
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
					$sysID = $userID['sysID'];
					
					mysql_query("UPDATE `users` SET `active` = '1' WHERE `sysID` = '{$sysID}'", $connDBA);
					
			  
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
/* End login management functions */

/* Begin constructor functions */
	//Page title and introductory text
	function title($title, $text = false, $break = true, $class = false) {
		echo "<h2";
		
		if ($class == true) {
			echo " class=\"" . $class . "\"";
		}
		
		echo ">" . $title . "</h2>";
		
		if ($text == true) {
			echo $text;
		}
		
		if ($break == true) {
			echo "<p>&nbsp;</p>";
		}
	}
	
	//Messages
	function message($trigger, $triggerValue, $type, $text) {
		global $messageBreakLimit;	
			
		if (isset($_GET[$trigger]) && $_GET[$trigger] == $triggerValue) {
			if ($type == "success") {
				successMessage($text);
			} elseif ($type == "error") {
				errorMessage($text);
			}
		} else {
			if (!isset($messageBreakLimit)) {
				echo "<br />";
			}
			
			$messageBreakLimit = "true";
		}
	}
	
	//Links
	function URL($text, $URL, $class = false, $target = false, $toolTip = false, $delete = false, $newWindow = false, $width = false, $height = false, $additionalParameters = false) {
		if ($newWindow == false || $width == false || $height == false) {
			$return = "<a href=\"" . $URL . "\"";
			
			if ($target == true) {
				$return .= " target=\"" . $target . "\"";
			}
			
			if ($class == true) {
				$return .= " class=\"" . $class . "\"";
			}
			
			if ($toolTip == true) {
				 $return .= " onmouseover=\"Tip('" . prepare($toolTip, true, false) . "')\" onmouseout=\"UnTip()\"";
			}
			
			if ($delete == true) {
				 $return .= " onclick=\" return confirm('This action cannot be undone. Continue?');" . $additionalParameters . "\"";
			} elseif ($additionalParameters == true) {
				 $return .= " onclick=\"" . $additionalParameters . "\"";
			}
			
			$return .= ">" . prepare($text) . "</a>";
		} else {
			$return = "<a href=\"javascript:void\" onclick=\"window.open('" . $URL . "','Window','status=yes,scrollbars=yes,resizable=yes,width=" . $width . ",height=" . $height . "')\"";
			
			if ($toolTip == true) {
				 $return .= " onmouseover=\"Tip('" . prepare($toolTip, true, false) . "')\" onmouseout=\"UnTip()\"";
			}
			
			$return .= ">" . $text . "</a>";
		}
		
		return $return;
	}
	
	//Forms and form layout
	function form($name, $method = "post", $validate = true, $containsFile = false, $action = false, $additionalParameters = false) {
		echo "<form name=\"" . $name . "\" method=\"" . $method . "\" id=\"validate\"";
		
		if ($containsFile == true) {
			echo " enctype=\"multipart/form-data\"";
		}
		
		echo " action=\"";
		
		if ($action == false) {
			$getParameters = $_GET;
			
			if (sizeof($getParameters) >= 1) {
				$parameters = "?";
				
				while(list($parameter, $value) = each($getParameters)) {
					$parameters .= $parameter . "=" . $value . "&";
				}
			}
			
			if (isset($parameters)) {
				echo $_SERVER['PHP_SELF'] . rtrim($parameters, "&");
			} else {
				echo $_SERVER['PHP_SELF'];
			}
		} else {
			echo $action;
		}
		
		echo "\"";
		
		if ($validate == true) {
			echo " onsubmit=\"return errorsOnSubmit(this);" . $additionalParameters . "\"";
		} else {
			if ($additionalParameters == true) {
				echo " onsubmit=\"" . $additionalParameters . "\"";
			}
		}
		
		echo ">";
	}
	
	function closeForm($advancedClose = true, $errors = true) {
		global $root;
		
		if ($errors == true) {
			echo "<div id=\"errorBox\" style=\"display:none;\">Some fields are incomplete, please scroll up to correct them.</div><div id=\"progress\" style=\"display:none;\"><p><span class=\"require\">Uploading in progress... </span><img src=\"" . $root . "images/common/loading.gif\" alt=\"Uploading\" width=\"16\" height=\"16\" /></p></div>";
		}
		
		if ($advancedClose == true) {
			echo "</div>";
		}
		
		echo "</form>";
	}
	
	function catDivider($content, $class, $first = false, $last = false) {
		if ($first == false) {
			echo "</div>";
		}
		
		echo "<div class=\"catDivider " . $class . "\">" . $content . "</div>";
		
		if ($last == false) {
			echo "<div class=\"stepContent\">";
		}
	}
	
	function directions($text, $required = false, $help = false) {
		global $root;
		
		echo "<p>" . $text;
		
		if ($required == true) {
			echo "<span class=\"require\">*</span>";
		}
		
		echo ": ";
		
		if ($help == true) {
			echo "<img src=\"" . $root . "system/images/admin_icons/help.png\" alt=\"Help\" width=\"17\" height=\"17\" onmouseover=\"Tip('" . $help . "')\" onmouseout=\"UnTip()\" />";
		}
		
		echo "</p>";
	}
	
	//Button
	function button($name, $id, $value, $type, $URL = false, $additionalParameters = false) {		
		switch ($type) {
			case "submit" : 
				echo "<input type=\"submit\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"" . ltrim($additionalParameters) . "tinyMCE.triggerSave();\">"; break;
			case "reset" : 
				echo "<input type=\"reset\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"return confirm('Are you sure you wish to reset all of the content in this form? Click OK to continue');$.validationEngine.closePrompt('#validate',true);" . $additionalParameters . "\">"; break;
			case "cancel" : 
				echo "<input type=\"reset\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"window.location='" . $URL . "';" . $additionalParameters . "\">"; break;
			case "history" : 
				echo "<input type=\"button\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"history.go(-1);" . $additionalParameters . "\">"; break;
			case "button" : 
				echo "<input type=\"button\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\"" . $additionalParameters;
				
				if ($URL == true) {
					echo " onclick=\"window.location='" . $URL . "';";
				}
				
				echo "\">"; break;
			case "image" :
				echo "<input type=\"image\" name=\"" . $name . "\" id=\"" . $id . "\" src=\"" . $URL . "\"";
				
				if ($additionalParameters == true) {
					echo " onclick=\"" . $additionalParameters . "\"";
				}
				
				echo ">"; break;
		}
	}
	
	//Checkbox
	function checkbox($name, $id, $label = false, $checkboxValue = false, $validate = true, $minValues = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $matchingValue = false, $additionalParameters = false) {
		echo "<label><input type=\"checkbox\" name=\"" . $name . "\" id=\"" . $id . "\"";
		
		if ($validate == true && $minValues == true) {
			echo " class=\"validate[required,minCheckbox[" . $minValues . "]]\"";
		}
		
		global $$editorTrigger;
		
		if ($manualSelect == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			echo " checked=\"checked\"";
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					if ($value[$arrayValue] == $matchingValue) {
						echo " checked=\"checked\"";
					}
				}
			}
		}
		
		if ($checkboxValue == true) {
			echo " value=\"" . $checkboxValue  . "\"";
		}
		
		echo $additionalParameters . "/>" . $label . "</label>";
	}
	
	//Dropdown menu
	function dropDown($name, $id, $values, $valuesID, $multiple = false, $validate = true, $validateAddition = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		$valuesArray = explode(",", $values);
		$valuesIDArray = explode(",", $valuesID);
		$valuesLimit = sizeof($valuesArray) - 1;
		
		if (sizeof($valuesArray) != sizeof($valuesIDArray)) {
			die(errorMessage("The values and IDs of the " . $name . " dropdown menu to not match"));
		} else {
			echo "<select name=\"" . $name . "\" id=\"" . $id . "\"";
			
			
			if ($multiple == false) {
				if ($validate == true) {
					echo " class=\"validate[required" . $validateAddition . "]\"";
				}
			} else {
				if ($validate == true) {
					echo " multiple=\"multiple\" class=\"multiple validate[required" . $validateAddition . "]\"";
				} else {
					echo " multiple=\"multiple\" class=\"multiple\"";
				}
			}
			
			echo $additionalParameters . ">";
			
			for ($count = 0; $count <= $valuesLimit; $count ++) {
				global $$editorTrigger;
				
				echo "<option value=\"" . $valuesIDArray[$count] . "\"";
				
				if (($manualSelect == true || $manualSelect == "0") && ($editorTrigger == false || !isset($$editorTrigger))) {
					if ($manualSelect == $valuesIDArray[$count]) {
						echo " selected=\"selected\"";
					}
				} else {
					if ($editorTrigger == true && isset($$editorTrigger)) {
						$value = $$editorTrigger;
						
						if (isset($$editorTrigger)) {
							if ($value[$arrayValue] == $valuesIDArray[$count]) {
								echo " selected=\"selected\"";
							}
						}
					}
				}
				
				echo ">" . $valuesArray[$count] . "</option>";
			}
			
			echo "</select>";
		}			
	}
	
	//File upload
	function fileUpload($name, $id, $size = false, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $fileURL = false, $uploadNote = false, $hideUploadSize = false, $additionalParameters = false) {
		global $$editorTrigger;
		
		if ($editorTrigger == true && isset($$editorTrigger)) {
			$value = $$editorTrigger;
			
			if (isset($$editorTrigger)) {
				echo "Current file: <a href=\"" . $fileURL . "/" . $value[$arrayValue] . "\" target=\"_blank\">" . $value[$arrayValue] . "</a><br />";
			}
		}
		
		if ($manualValue == true) {
			echo "Current file: <a href=\"" . $fileURL . "/" . urlencode($manualValue) . "\" target=\"_blank\">" . $manualValue . "</a><br />";
		}
		
		echo "<input type=\"file\" name=\"" . $name . "\" id=\"" . $id . "\" size=\"";
		
		if ($size == false) {
			echo "50";
		} else {
			echo $size;
		}
		
		echo "\"";
		
		if ($validate == true) {
			echo " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		echo ">";
		
		if ($hideUploadSize == false) {
			echo "<br />Max file size: " .  ini_get('upload_max_filesize');
		}
		
		if (($manualValue == true || (isset($$editorTrigger) && $editorTrigger == true)) && $uploadNote == true) {
			echo "<br /><strong>Note:</strong> Uploading a new file will replace the existing one.";
		}
	}
	
	//Hidden
	function hidden($name, $id, $value) {
		echo "<input type=\"hidden\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" />";
	}
	
	//Radio button
	function radioButton($name, $id, $buttonLabels, $buttonValues, $inLine = true, $validate = true, $validateAddition = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		$labelsArray = explode(",", $buttonLabels);
		$valuesArray = explode(",", $buttonValues);
		$valuesLimit = sizeof($labelsArray) - 1;
		
		for ($count = 0; $count <= $valuesLimit; $count ++) {
			global $$editorTrigger;
			
			echo "<label><input type=\"radio\" name=\"" . $name . "\" id=\"" . $id . "_" . $count . "\" value=\"" . $valuesArray[$count] . "\"";
			
			if (($manualSelect == true || $manualSelect === "0") && ($editorTrigger == false || !isset($$editorTrigger))) {
				if ($valuesArray[$count] == $manualSelect) {
					echo " checked=\"checked\"";
				}
			} else {
				if ($editorTrigger == true && isset($$editorTrigger)) {
					$value = $$editorTrigger;
					
					if (isset($$editorTrigger)) {
						if ($valuesArray[$count] == $value [$arrayValue]) {
							echo " checked=\"checked\"";
						}
					}
				}
			}
			
			if ($validate == true) {
				echo " class=\"validate[required" . $validateAddition . "] radio\"";
			}
			
			echo $additionalParameters . ">" . $labelsArray[$count] . "</label>";
			
			if ($count != $valuesLimit) {
				if ($inLine != true) {
					echo "<br />";
				}
			}
		}
	}
	
	//Textarea
	function textArea($name, $id, $size, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		echo "<textarea name=\"" . $name . "\" id=\"" . $id . "\" style=\"";
		
		if ($size == "large") {
			echo "width:640px; height:320px;";
		} elseif ($size == "small") {
			echo "width:450px;";
		}
		
		echo "\"";
		
		if ($validate == true) {
			echo " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		echo $additionalParameters . ">";
		
		global $$editorTrigger;
		
		if ($manualValue == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			echo $manualValue;
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					echo $value[$arrayValue];
				}
			}
		}
		
		echo "</textarea>";
	}
	
	//Text Fields
	function textField($name, $id, $size = false, $limit = false, $password = false, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		echo "<input type=\"";
		
		if ($password == false) {
			echo "text";
		} else {
			echo "password";
		}
			
		echo "\"";
		
		if ($limit == true) {
			echo " maxlength=\"" . $limit . "\"";
		}
		
		echo " name=\"" . $name . "\" id=\"" . $id . "\" size=\"";
		
		if ($size == false) {
			echo "50";
		} else {
			echo $size;
		}
		
		echo "\" autocomplete=\"off\"";
		
		if ($validate == true) {
			echo " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		global $$editorTrigger;
		
		if ($manualValue == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			echo "value=\"" . prepare($manualValue, true, true) . "\"";
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					echo "value=\"" . prepare($value[$arrayValue], true, true) . "\"";
				}
			}
		}
		
		echo $additionalParameters . " />";
	}
	
	//Sideboxes
	function sideBox($title, $type, $text, $allowRoles = false, $editID = false) {
		//Include the title
		echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\">" . $title;
		
		//Display the content
		$premitted = false;
		
		if (isset($_SESSION['MM_UserGroup']) && $allowRoles == true) {
			foreach (explode(",", $allowRoles) as $role) {
				if ($_SESSION['MM_UserGroup'] == $role) {
					$premitted = true;
				}
			}
		}
		
		switch ($type) {
			case "Custom Content" :				
				if (!isset($_SESSION['MM_UserGroup'])) {
					echo "</div></div><div class=\"content\">" . $text . "</div>";
				} elseif (isset($_SESSION['MM_UserGroup']) && $premitted == true) {
					echo "&nbsp;" . URL("", "site_administrator/cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div></div><div class=\"content\">" . $text . "</div>";
				} else {
					echo "</div></div><div class=\"content\">" . $text . "</div>";
				} break;
			case "Login" :
				$roles = explode(",", $allowRoles);
			
				if (!isset($_SESSION['MM_UserGroup'])) {
					echo "</div></div><div class=\"content\">";
					form("login");
					echo "<p>User name: ";
					textField("username", "username", "30");
					echo "<br />Password: ";
					textField("password", "password", "30", false, true);
					echo"</p><p>";
					button("submit", "submit", "Login", "submit");
					echo "</p>";
					closeForm(false, false);
					echo "</div>";
				} elseif (isset($_SESSION['MM_UserGroup']) && $premitted == true) {
					echo "&nbsp;" . URL("", "site_administrator/cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div></div>";
				} else {
					echo "</div></div>";
				} break;
				
			case "Register" :
				$roles = explode(",", $allowRoles);
			
				if (!isset($_SESSION['MM_UserGroup'])) {
					echo "</div></div><div class=\"content\">" . $text;
					form("login");
					echo "<div align=\"center\">";
					button("register", "register", "Register", "cancel", "register.php");
					echo "</div></div>";
				} elseif (isset($_SESSION['MM_UserGroup']) && $premitted == true) {
					echo "&nbsp;" . URL("", "site_administrator/cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div></div>";
				} else {
					echo "</div></div>";
				} break;
		}
		
		//Close the HTML
		echo "</div><br />";
	}
	
	//Live option
	function option($id, $state, $checkboxTrigger, $type) {
		if ($type == "visible") {			
			if ($state == "") {
				$class = " hidden";
			} else {
				$class = "";
			}
		} else {
			$type = "checked";
			
			if ($state == "") {
				$class = " unchecked";
			} else {
				$class = "";
			}
		}
		
		echo "<div align=\"center\">";
		form("avaliability");
		hidden("action", "action", "setAvaliability");
		hidden("id", "id", $id);
		echo URL("", "#option" . $id, $type . $class);
		echo "<div class=\"contentHide\">";
		checkbox("option", "option" . $id, false, false, false, false, $checkboxTrigger, $checkboxTrigger, "visible", "on", " onclick=\"Spry.Utils.submitForm(this.form);\"");
		echo "</div>";
		closeForm(false, false);
		echo "</div>";
	}
	
	//Reordering menu
	function reorderMenu ($id, $state, $menuTrigger, $table) {
		global $connDBA;
		
		$itemCountGrabber = mysql_query("SELECT * FROM {$table}", $connDBA);
		$itemCount = mysql_num_rows($itemCountGrabber);
		$values = "";
		
		for ($count = 1; $count <= $itemCount; $count++) {			
			if ($count < $itemCount) {
				$values .= $count . ",";
			} else {
				$values .= $count;
			}
		}
		
		form("reorder");
		hidden("id", "id", $id);
		hidden("currentPosition", "currentPosition", $state);
		hidden("action", "action", "modifyPosition");
		dropDown("position", "position", $values, $values, false, false, false, $state, false, false, " onchange=\"this.form.submit();\"");
		closeForm(false, false);
	}
	
	//Lesson content
	function lesson($id, $table, $preview = false) {
		global $monitor, $connDBA, $root;
		
		if ($preview == false) {
			$URL = $_SERVER['PHP_SELF'] . "?id=" . $id . "&";
		} else {
			$URL = $_SERVER['PHP_SELF'] . "?";
		}
		
	//Grab all of the lesson and module data
		$moduleData = query("SELECT * FROM `moduledata` WHERE `id` = '{$id}'");
	
		if (isset($_GET['page'])) {
			if (exist($table) == true) {
				$page = $_GET['page'];
				$lesson = exist($table, "position", $page);
				
				if ($lesson = exist($table, "position", $page)) {
					//Do nothing
				} else {
					redirect($URL . "page=1");
				}
			} else {
				redirect($_SERVER['PHP_SHELF']);
			}
		} else {
			redirect($URL . "page=1");
		}
		
		echo "<div class=\"layoutControl\">";
		
	//Display the title and navigation
		if ($preview !== "miniPreview") {
			$previousPage = intval($_GET['page']) - 1;
			$nextPage = intval($_GET['page']) + 1;
			
			echo "<div class=\"toolBar noPadding\">";
			title($lesson['title'], false, false, "lessonTitle");
			
			$navigation = "<div align=\"center\">";
			
			if (exist($table, "position", $previousPage) == true) {
				if ($preview == true && lastItem($table) - 1 == $_GET['page']) {
					$navigation .= URL("Previous Page", $URL . "page=" . $previousPage , "previousPage");
				} else {
					$navigation .= URL("Previous Page", $URL . "page=" . $previousPage , "previousPage") . " | ";
				}
			}
			
			if (exist($table, "position", $nextPage) == true) {
				$navigation .= URL("Next Page", $URL . "page=" . $nextPage , "nextPage");
			}
		}
		
		if ($preview == false) {
			if (exist($table, "position", $nextPage) == false && $moduleData['test'] == "1") {
				$navigation .= URL("Proceed to Test", "test.php?id=" . $id , "nextPage", false, false, false, false, false, false, "return confirm('This action will close and lock access to the lesson until you have completed the test. Continue?')");
			} elseif (exist($table, "position", $nextPage) == false && $moduleData['test'] == "0") {
				$navigation .= URL("Finish", $URL . "action=finish", "nextPage");
			}
		}
		
		if ($preview !== "miniPreview") {
			$navigation .= "</div>";
			
			echo $navigation . "</div><p>&nbsp;</p>";
		}
		
		if ($preview == false) {
			echo "<div class=\"dataLeft\">";
			$pagesGrabber = mysql_query("SELECT * FROM `{$table}` ORDER BY `position` ASC", $connDBA);
			$text = "";
			
			while($pages = mysql_fetch_array($pagesGrabber)) {
				if ($_GET['page'] != $pages['position']) {
					$text .= "<p>" . URL(prepare($pages['title'], false, true), "lesson.php?id=" . $_GET['id'] . "&page=" . $pages['position']) . "</p>";
				} else {
					$text .= "<p><span class=\"currentPage\">" . prepare($pages['title'], false, true) . "</span></p>";
				}
			}
			
			sideBox("Lesson Navigation", "Custom Content", $text);
			echo "</div><div class=\"contentRight\">";
		} else {
			echo "<div>";
		}
		
	//Display the content		
		if ($lesson['type'] == "Custom Content") {
			echo $lesson['content'];
		}
		
		if ($lesson['type'] == "Embedded Content") {
			echo $lesson['content'];
			echo "<br />";
			$location = str_replace(" ", "", $id);
			$file = $root . "gateway.php/modules/" . $location . "/lesson/" . $lesson['attachment'];
			$fileType = extension($file);
			echo "<div align=\"center\">";
			
			switch ($fileType) {
			//If it is a PDF
				case "pdf" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
			//If it is a Word Document
				case "doc" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "docx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a PowerPoint Presentation
				case "ppt" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "pptx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is an Excel Spreadsheet
				case "xls" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "xlsx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a Standard Text Document
				case "txt" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				case "rtf" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/text.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a WAV audio file
				case "wav" : echo "<object width=\"640\" height=\"16\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"16\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP3 audio file
				case "mp3" : echo "<object id=\"player\" width=\"640\" height=\"30\" data=\"" . $root . "system/player/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/player/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\", \"plugins\":{\"controls\":{\"autoHide\":false}}}' /></object>"; break;
			//If it is an AVI video file
				case "avi" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an WMV video file
				case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an FLV file
				case "flv" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "system/player/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/player/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is an MOV video file
				case "mov" : echo "<object width=\"640\" height=\"480\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"480\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP4 video file			
				case "mp4" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "system/player/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/player/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is a SWF video file
				case "swf" : echo "<object width=\"640\" height=\"480\" data=\"" . $file . "\" type=\"application/x-shockwave-flash\">
<param name=\"src\" value=\"" . $file . "\" /></object>"; break;
			}
			
			echo "</div>";
		}
		
		echo "</div></div>";
		
		if ($preview !== "miniPreview") {
			echo "<p>&nbsp;</p>" . $navigation;
		}
	}
	
	//Test content
	function test($table, $fileURL, $preview = false) {
		global $connDBA, $testValues, $monitor;
		
		form("test", "post", true, true);
		echo "<table width=\"100%\" class=\"dataTable\">";
		
		if ($preview == true) {
			if (is_numeric($preview)) {
				$additionalSQL = " WHERE `id` = '{$preview}'";
				$limit = " LIMIT 1";
			} else {
				$additionalSQL = "";
				$limit = "";
			}
		} else {
			$userData = userData();
			$testID = str_replace("moduletest_", "", $table);
			$selectionGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}'", "raw");
			$additionalSQLConstruct = " WHERE ";
			
			while ($selection = mysql_fetch_array($selectionGrabber)) {
				$additionalSQLConstruct .= "`id` = '{$selection['questionID']}' OR ";
			}
			
			$additionalSQL = rtrim($additionalSQLConstruct, " OR ");
			$limit = "";
		}
		
		if ($table != "questionbank" && $preview != false) {
			$order = " ORDER BY `position` ASC";
			$grab = "*";
			$join = "";
		} elseif (is_numeric($preview)) {
			$order = "";
			$grab = "*";
			$join = "";
		} else {
			$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}'");
			
			if ($moduleInfo['randomizeAll'] == "Randomize") {
				$order = " ORDER BY `randomPosition` ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = $table . ".*, testdata_" . $userData['id'] . ".randomPosition, testdata_" . $userData['id'] . ".answerValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $table . ".id = testdata_" . $userData['id'] . ".questionID";
		}
		
		if (!is_numeric($preview)) {
			$testID = str_replace("moduletest_", "", $table);
			$moduleInfo = query("SELECT * FROM	`moduledata` WHERE `id` = '{$testID}'");
		}
		
		$testDataGrabber = query("SELECT {$grab} FROM `{$table}`{$join}{$additionalSQL}{$order}{$limit}", "raw");
		$count = 1;
		$restrictImport = array();
		
	  	while ($testDataLoop = mysql_fetch_array($testDataGrabber)) {
			if ($preview == false) {
				$testValues = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}' AND `questionID` = '{$testDataLoop['id']}'");
			}
			
			if ($table != "questionbank" && $testDataLoop['questionBank'] == "1") {
				$importID = $testDataLoop['linkID'];
				$importQuestion = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$importID}'", $connDBA);
				$testData = mysql_fetch_array($importQuestion);
			} else {
				$testData = $testDataLoop;
			}
			
			if (!is_numeric($preview) && exist($table, "id", $testData['link']) && $moduleInfo['randomizeAll'] == "Randomize" && !empty($testData['link']) && $testDataLoop['link'] != "0" && !in_array($testDataLoop['link'], $restrictImport)) {
				$importDescription = query("SELECT * FROM `{$table}` WHERE `id` = '{$testDataLoop['link']}'");
				
				if ($importDescription['questionBank'] == "1") {
					$importDescription = query("SELECT * FROM `questionbank` WHERE `id` = '{$importDescription['linkID']}'");
				} else {
					$importDescription = $importDescription;
				}
				
				echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($importDescription['question'], false, true) . "</td></tr>";
				array_push($restrictImport, $testDataLoop['link']);
			}
			
			if ($testData['type'] != "Description") {
				echo "<tr><td width=\"100\" valign=\"top\"><p>";
				
				if (!is_numeric($preview)) {
					echo "<span class=\"questionNumber\">Question " . $count++ . "</span><br />";
				}
				
				echo "<span class=\"questionPoints\">" . $testData['points'] . " ";
				
				if ($testData['points'] == "1") {
					echo "Point";
				} else {
					echo "Points";
				}
				
				echo "</span>";
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "</p></td><td valign=\"top\">" . prepare($testData['question'], false, true) . "<br /><br />";
			}
			
			switch ($testData['type']) {
				case "Description" : 
					if (!in_array($testDataLoop['id'], $restrictImport)) {
						echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($testData['question'], false, true) . "</td></tr>";
						array_push($restrictImport, $testDataLoop['id']);
					}
					
					break;
				case "Essay" : 
					if (isset($testValues)) {
						textArea($testDataLoop['id'], $testDataLoop['id'], "small", true, false, unserialize($testValues['userAnswer']));
					} else {
						textArea($testDataLoop['id'], $testDataLoop['id'], "small", true);
					}
						
					break;
					
				case "File Response" : 
					if ($testData['totalFiles'] > 1 || sizeof(unserialize($testValues['userAnswer'])) > 1) {
						if (isset($monitor)) {
							$URL = $monitor['gatewayPath'] . "/test/responses";
						} else {
							$URL = "../gateway.php/modules/" . $_GET['id'] . "/test/responses";
							$fillValue = unserialize($testValues['userAnswer']);
						}
						
						echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">";
						
						if (isset($testValues) && !empty($fillValue)) {
							$fileID = 1;
							
							foreach ($fillValue as $key => $file) {
								echo "<tr id=\"" . $fileID . "\"><td>";
								
								fileUpload($testDataLoop['id'] . "_" . $fileID, $testDataLoop['id'] . "_" . $fileID, false, true, false, $fillValue[$key], false, false, $URL, false, true);
								echo "</td><td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=" . $fileID, "action smallDelete", false, false, false, false, false, false, " return confirm('This action will delete this file. Continue?')");
								echo "</td></tr>";
								
								$fileID++;
							}
							
							unset($fileID);
							
							echo "</table><p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p><p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>";
						} else {
							echo "<tr id=\"1\"><td>";
							fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, false, false, false, false, false, true);
							echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('upload_" . $testDataLoop['id'] . "', '1', '1', true)\"></span>";
							echo "</td></tr></table><p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p>";
						}
						
						echo "<p>Max file size (for single file): " . ini_get('upload_max_filesize') . "<br>Max file size (for all files): " . ini_get('post_max_size') . "</p>";
					} else {
						if (isset($testValues)) {
							$fillValue = unserialize($testValues['userAnswer']);
							
							if (!empty($fillValue)) {
								echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">";
								echo "<tr id=\"1\"><td>";
								fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, $fillValue['0'], false, false, "../gateway.php/modules/" . $_GET['id'] . "/test/responses", false, false);
								echo "</td><td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=1", "action smallDelete", false, false, false, false, false, false, " return confirm('This action will delete this file. Continue?')");
								echo "</td></tr></table>";
								echo "<p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>";
							} else {
								fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
							}
						} else {
							fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
						}
					}
					
					break;
					
				case "Fill in the Blank" : 
					$blankQuestion = unserialize($testData['questionValue']);
					$blank = unserialize($testData['answerValue']);
					$answerCompare = unserialize($testData['answerValue']);
					$valueNumbers = sizeof($blankQuestion);
					$matchingCount = 1;
					echo "<p>";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
					   echo prepare($blankQuestion[$list], false, true) . " ";
					   
					   if (!empty($blank[$list])) {
						   if (isset($testValues)) {
							   $value = unserialize($testValues['userAnswer']);
							   
							   if (is_array($value)) {
								   if (array_key_exists($list, $value)) {
									   echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true, false, $value[$list])  . " ";
								   } elseif (!array_key_exists($list, $value) && isset($answerCompare[$list])) {
									   echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
								   }
							   } else {
								    echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
							   }
						   } else {
						   		echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
						   }
					   }
					}
					
					echo "</p>";
					break;
				
				case "Matching" : 
					$question = unserialize($testData['questionValue']);
					$answer = unserialize($testValues['answerValueScrambled']);
					$answerCompare = unserialize($testData['answerValue']);
					$valueNumbers = sizeof($question);
					$matchingCount = 1;
					$fillValue = unserialize($testValues['userAnswer']);
					
					echo "<table width=\"100%\">";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
						echo "<tr><td width=\"20\">";
						$dropDownValue = "-,";
						$dropDownID = ",";
						
						for ($value = 1; $value <= $valueNumbers; $value++) {
							$dropDownValue .= $value . ",";
							$dropDownID .= $value . ",";
						}
						
						$values = rtrim($dropDownValue, ",");
						$IDs = rtrim($dropDownID, ",");
						
						if (isset($testValues)) {
							$value = unserialize($testValues['userAnswer']);
							 
							if (is_array($value)) {
								if (array_key_exists($list, $value)) {
									echo dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true, false, $fillValue[$list])  . " ";
								} elseif (!array_key_exists($list, $value) && isset($answerCompare[$list])) {
									dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
								}
							} else {
								dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
							}
						} else {
							dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true);
						}
						
						echo"</td><td width=\"200\"><p>" . prepare($question[$list], false, true) . "</p></td><td width=\"200\"><p>" . $matchingCount++ . ". " . prepare($answer[$list], false, true) . "</p></td></tr>";
					}
					
					echo"</table>";				  
					break;
				
				case "Multiple Choice" : 					
					if ($preview == true) {
						$questions = unserialize($testData['questionValue']);
						
						if ($testData['randomize'] == "1") {
							$questionsDisplay = $questions;
							shuffle($questionsDisplay);
						} else {
							$questionsDisplay = $questions;
						}
					} else {
						if ($testData['randomize'] == "1") {
							$questions = unserialize($testData['answerValueScrambled']);
						} else {
							$questions = unserialize($testData['questionValue']);
						}
					}
					
					if ($testData['choiceType'] == "radio") {
						$questionValue = "";
						$questionID = "";
					
						while (list($questionKey, $questionArray) = each($questions)) {
							$questionValue .= $questionArray . ",";
							$questionID .= $questionKey + 1 . ",";
						}
						
						$values = rtrim($questionValue, ",");
						$IDs = rtrim($questionID, ",");
						
						
						if (isset($testValues)) {
							radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $IDs, false, true, false, unserialize($testValues['userAnswer']));
						} else {
							radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $IDs, false, true);
						}
						
					} else {
						while (list($questionKey, $questionArray) = each($questions)) {
							$identifier = $questionKey + 1;
							if (isset($testValues)) {
								if (is_array(unserialize($testValues['userAnswer']))) {
									$fillValue = unserialize($testValues['userAnswer']);
								} else {
									$fillValue = array(unserialize($testValues['userAnswer']));
								}
								
								if (in_array($identifier, $fillValue)) {
									checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1", true);
								} else {
									checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1");
								}
							} else {
								checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1");
							}
							
							echo "<br />";
						}
					}
					
					break;
					
				case "Short Answer" : 
					if (isset($testValues)) {
						textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true, false, unserialize($testValues['userAnswer']));
					} else {
						textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true);
					}
					
					break;
					
				case "True False" : 
					if ($testData['randomize'] == "1") {						
						$id = implode(",", unserialize($testValues['answerValueScrambled']));
						$label = explode(",", $id);
					} else {
						$id = implode(",", unserialize($testValues['questionValue']));
						$label = explode(",", $id);
					}
					
					if ($label['0'] == "1") {
						$values = "True,";
					} else {
						$values = "False,";
					}
					
					if ($label['1'] == "1") {
						$values .= "True";
					} else {
						$values .= "False";
					}
					
					if (isset($testValues)) {
						radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true, false, unserialize($testValues['userAnswer']));
					} else {
						radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true);
					}
					
					break;
			}
			
			if ($testData['type'] != "Description") {
				echo "<br /><br /></td></tr>";
			}
		}
		
		echo "</table>";
		
		if ($preview == false) {
			echo "<blockquote><p>";
			button("save", "save", "Save", "submit", false);
			button("submit", "submit", "Submit", "submit", false, " return confirm('Once the test is submitted, it cannot be reopened. Continue?');");
			echo "</p></blockquote>";
		}
		
		closeForm(false, true);
	}
	
	//Statistics charting
	function chart($type, $source, $width = false, $height = false) {
		global $root;
		
		if ($width == false) {
			$width = "600";
		}
		
		if ($height == false) {
			$height = "350";
		}
		
		echo "<div align=\"center\"><embed type=\"application/x-shockwave-flash\" src=\"" . $root . "statistics/charts/" . $type . ".swf\" id=\"chart\" name=\"chart\" quality=\"high\" allowscriptaccess=\"always\" flashvars=\"chartWidth=" . $width . "&chartHeight=" . $height . "&debugMode=0&DOMId=overallstats&registerWithJS=0&scaleMode=noScale&lang=EN&dataURL=" . $root . "statistics/data/index.php?type=" . $source . "\" wmode=\"transparent\" width=\"" . $width . "\" height=\"" . $height . "\"></div>";

	}
/* End constructor functions */

/* Begin processor functions */
	//Check item existance
	function exist($table, $column = false, $value = false) {
		global $connDBA;
		
		if ($column == true) {
			$additionalCheck = " WHERE `{$column}` = '{$value}'";
		} else {
			$additionalCheck = "";
		}
		
		$itemCheckGrabber = mysql_query("SELECT * FROM {$table}{$additionalCheck}", $connDBA);
		$itemCheck = mysql_num_rows($itemCheckGrabber);
		
		if ($itemCheck >= 1) {
			$itemGrabber = mysql_query("SELECT * FROM {$table}{$additionalCheck}", $connDBA);
			$item = mysql_fetch_array($itemGrabber);
			
			return $item;
		} else {
			return false;
		}
	}
	
	//Set avaliability
	function avaliability($table, $redirect) {
		global $connDBA;
		
		if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {			
			$id = $_POST['id'];
			
			if (!$_POST['option']) {
				$option = "";
			} else {
				$option = $_POST['option'];
			}
			
			mysql_query("UPDATE {$table} SET `visible` = '{$option}' WHERE id = '{$id}'", $connDBA);
			
			header ("Location: " . $redirect);
			exit;
		}
	}
	
	//Reorder items
	function reorder($table, $redirect) {
		global $connDBA;
		
		if (isset($_POST['action']) && $_POST['action'] == "modifyPosition" && isset($_POST['id']) && isset($_POST['position']) && isset($_POST['currentPosition'])) {
			$id = $_POST['id'];
			$newPosition = $_POST['position'];
			$currentPosition = $_POST['currentPosition'];
			
			$itemCheck = mysql_query("SELECT * FROM {$table} WHERE position = {$currentPosition}", $connDBA);
			
			if (!$itemCheck) {
				header ("Location: " . $redirect);
				exit;
			}
		  
			if ($currentPosition > $newPosition) {
				mysql_query("UPDATE {$table} SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'", $connDBA);
				mysql_query ("UPDATE {$table} SET position = '{$newPosition}' WHERE id = '{$id}'", $connDBA);
				
				header ("Location: " . $redirect);
				exit;
			} elseif ($currentPosition < $newPosition) {
				mysql_query("UPDATE {$table} SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'", $connDBA);
				mysql_query("UPDATE {$table} SET position = '{$newPosition}' WHERE id = '{$id}'", $connDBA);
				
				header ("Location: " . $redirect);
				exit;
			} else {
				header ("Location: " . $redirect);
				exit;
			}
		}
	}
	
	//Delete an item
	function delete($table, $redirect, $reorder = true, $file = false, $directory = false, $extraTables = false) {
		global $connDBA;
		
		if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {
			if (isset ($_GET['questionID'])) {
				$deleteItem = $_GET['questionID'];
			} else {
				$deleteItem = $_GET['id'];
			}
		
			$itemCheck = mysql_query("SELECT * FROM {$table} WHERE id = {$deleteItem}", $connDBA);
			
			if (!$itemCheck) {
				header ("Location: " . $redirect);
				exit;
			}
			
			if ($reorder = true) {
				$itemPositionGrabber = mysql_query("SELECT * FROM {$table} WHERE `id` = {$deleteItem}", $connDBA);
				$itemPositionFetch = mysql_fetch_array($itemPositionGrabber);
				$itemPosition = $itemPositionFetch['position'];
				
				mysql_query("UPDATE {$table} SET position = position-1 WHERE position > '{$itemPosition}'", $connDBA);
				mysql_query("DELETE FROM {$table} WHERE id = {$deleteItem}", $connDBA);
			} else {
				mysql_query("DELETE FROM {$table} WHERE id = {$deleteItem}", $connDBA);
			}
			
			if ($file == true) {
				unlink($file);
			}
			
			if ($directory == true) {
				deleteAll($directory);
			}
			
			if ($extraTables == true) {
				$tables = explode(",", $extraTables);
				
				foreach ($tables as $table) {
					mysql_query("DROP TABLE `{$table}`");
				}
			}
			
			header ("Location: " . $redirect);
			exit;
		}
	}
	
	//Redirect to page
	function redirect($URL) {
		header("Location: " . $URL);
		exit;
	}
/* End processor functions */

/* Begin page scripting functions */
	//Include the tiny_mce simple widget
	function tinyMCESimple () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/atd-tinymce/editor_plugin.js\"></script><script type=\"text/javascript\" src=\"" . $root . "system/javascripts/common/tiny_mce_simple.php\"></script>";
	}
	
	//Include the tiny_mce advanced widget
	function tinyMCEAdvanced () {
		global $connDBA;
		global $root;
		
		echo "<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/tiny_mce.js\"></script><script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/atd-tinymce/editor_plugin.js\"></script><script type=\"text/javascript\" src=\"" . $root . "system/javascripts/common/tiny_mce_advanced.php\"></script><script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php\"></script>";
	}
	
	//Include a form validator
	function validate () {
		global $connDBA;
		global $root;
		
		echo "<link rel=\"stylesheet\" href=\"" . $root . "system/styles/validation/validatorStyle.css\" type=\"text/css\">";
		echo "<script src=\"" . $root . "system/javascripts/validation/validatorCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "system/javascripts/validation/validatorOptions.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "system/javascripts/validation/runValidator.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "system/javascripts/validation/formErrors.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a life updater script
	function liveSubmit() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/liveSubmit/submitterCore.js\" type=\"text/javascript\"></script>";
		echo "<script src=\"" . $root . "system/javascripts/liveSubmit/runSubmitter.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the custom checkbox script
	function customVisible() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/customCheckbox/checkboxCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/customCheckbox/runVisible.js\" type=\"text/javascript\"></script>";
	}
	
	function customCheckbox() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/customCheckbox/checkboxCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/customCheckbox/runCheckbox.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a live error script
	function liveError() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/liveError/errorCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/liveError/runNameError.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a live update script
	function liveUpdate() {
		global $connDBA;
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/liveUpdate/liveUpdateCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/liveUpdate/liveUpdateOptions.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a show or hide script
	function showHide() {
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/common/showHide.js\" type=\"text/javascript\"></script>";
	}
	
	//Include an enable/disable script
	function enableDisable() {
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/common/enableDisable.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a navigation menu style
	function navigationMenu() {
		global $root;
		
		echo "<link rel=\"stylesheet\" href=\"" . $root . "system/styles/common/navigationMenu.css\" type=\"text/css\">";
	}
	
	//Include a new object script
	function newObject() {
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/common/newObject.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a script to calculate the score of a question
	function calculate() {
		global $root;
		
		echo "<script src=\"" . $root . "system/javascripts/common/scoreCalculate.js\" type=\"text/javascript\"></script>";
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
	
	//A function to prepare to display values from a database
	function prepare($item, $htmlEncode = false, $stripSlashes = true) {
		if ($stripSlashes == true) {
			if ($htmlEncode == true) {
				return htmlentities(stripslashes($item));
			} else {
				return stripslashes($item);
			}
		} else {
			if ($htmlEncode == true) {
				return htmlentities($item);
			} else {
				return $item;
			}
		}
	}
	
	//A function to check the extension of a file
	function extension ($targetFile) {
		$entension = explode(".", $targetFile);
		$value = count($entension)-1;
		$entension = $entension[$value];
		$output = strtolower($entension);
		
		if($output == "php" || $output == "php3" || $output == "php4" || $output == "php5" || $output == "tpl" || $output == "php-dist" || $output == "phtml" || $output == "htaccess" || $output == "htpassword") {
			die(errorMessage("Your file is a potential threat to this system, in which case, it was not uploaded"));
			return false;
			exit;
		} else {
			return $output;
		}
	}
	
	//A function to shuffle an array, while preserving the keys
	function shufflePreserve($array) {
		$preserveArray = array();
		
		while (list($arrayKey, $arrayValue) = each($array)) {
			$preserveArray[] = array("key" => $arrayKey, "value" => $arrayValue);
		}
		
		shuffle($preserveArray);
		$returnArray = array();
		
		while (list($returnArrayKey, $returnArrayValue) = each($returnArray)) {
			$returnArray[$returnArrayKey] = $returnArrayValue;
		}
		
		return $returnArray;
	}
	
	//A case-insensitive version of in_array()
	function inArray($needle, $haystack) {
		if (is_array($haystack)) {
			foreach($haystack as $value) {
				if (is_array($value)) {
					if (inArray($needle, $value)) {
						return true;
						exit;
					}
				} else {
					if (strtolower($value) == strtolower($needle)) {
						return true;
						exit;
					}
				}
			}
			
			return false;
		} else {
			return false;
		}
	}
	
	//A function to flatten a nested array
	function flatten($array) {
		if (!is_array($array)) {
			return array($array);
		}
	
		$result = array();
		
		foreach ($array as $value) {
			$result = array_merge($result, flatten($value));
		}
	
		return array_unique($result);
	}

	//A function to return of the size of an array, even if several values are left empty
	function size($array) {
		$return = end($array);
		return key($return);
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
	
	//A function to grab grab the previous item in the database
	function lastItem($table, $whereColumn = false, $whereValue = false, $column = false) {
		global $connDBA;
		
		if ($column == false) {
			$column = "position";
		} else {
			$column = $column;
		}
		
		if ($whereColumn == true && $whereValue == true) {
			$where = " WHERE `{$whereColumn}` = '{$whereValue}' ";
		} else {
			$where = "";
		}
		
		$lastItemGrabber = query("SELECT * FROM {$table}{$where} ORDER BY {$column} DESC", "raw", false);
		
		if ($lastItemGrabber) {
			$lastItem = mysql_fetch_array($lastItemGrabber);
			return $lastItem[$column] + 1;
		} else {
			return "1";
		}
	}
	
	//A function to return the mime type of a file
	function getMimeType($filename, $debug = false) {
		if ( function_exists( 'finfo_open' ) && function_exists( 'finfo_file' ) && function_exists( 'finfo_close' ) ) {
			$fileinfo = finfo_open( FILEINFO_MIME );
			$mime_type = finfo_file( $fileinfo, $filename );
			finfo_close( $fileinfo );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'fileinfo' );
				return $mime_type;
			}
		}
		if ( function_exists( 'mime_content_type' ) ) {
			$mime_type = mime_content_type( $filename );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'mime_content_type' );
				return $mime_type;
			}
		}
		
		$mime_types = array(
			'ai'      => 'application/postscript',
			'aif'     => 'audio/x-aiff',
			'aifc'    => 'audio/x-aiff',
			'aiff'    => 'audio/x-aiff',
			'asc'     => 'text/plain',
			'asf'     => 'video/x-ms-asf',
			'asx'     => 'video/x-ms-asf',
			'au'      => 'audio/basic',
			'avi'     => 'video/x-msvideo',
			'bcpio'   => 'application/x-bcpio',
			'bin'     => 'application/octet-stream',
			'bmp'     => 'image/bmp',
			'bz2'     => 'application/x-bzip2',
			'cdf'     => 'application/x-netcdf',
			'chrt'    => 'application/x-kchart',
			'class'   => 'application/octet-stream',
			'cpio'    => 'application/x-cpio',
			'cpt'     => 'application/mac-compactpro',
			'csh'     => 'application/x-csh',
			'css'     => 'text/css',
			'dcr'     => 'application/x-director',
			'dir'     => 'application/x-director',
			'djv'     => 'image/vnd.djvu',
			'djvu'    => 'image/vnd.djvu',
			'dll'     => 'application/octet-stream',
			'dms'     => 'application/octet-stream',
			'dvi'     => 'application/x-dvi',
			'dxr'     => 'application/x-director',
			'eps'     => 'application/postscript',
			'etx'     => 'text/x-setext',
			'exe'     => 'application/octet-stream',
			'ez'      => 'application/andrew-inset',
			'flv'     => 'video/x-flv',
			'gif'     => 'image/gif',
			'gtar'    => 'application/x-gtar',
			'gz'      => 'application/x-gzip',
			'hdf'     => 'application/x-hdf',
			'hqx'     => 'application/mac-binhex40',
			'htm'     => 'text/html',
			'html'    => 'text/html',
			'ice'     => 'x-conference/x-cooltalk',
			'ief'     => 'image/ief',
			'iges'    => 'model/iges',
			'igs'     => 'model/iges',
			'img'     => 'application/octet-stream',
			'iso'     => 'application/octet-stream',
			'jad'     => 'text/vnd.sun.j2me.app-descriptor',
			'jar'     => 'application/x-java-archive',
			'jnlp'    => 'application/x-java-jnlp-file',
			'jpe'     => 'image/jpeg',
			'jpeg'    => 'image/jpeg',
			'jpg'     => 'image/jpeg',
			'js'      => 'application/x-javascript',
			'kar'     => 'audio/midi',
			'kil'     => 'application/x-killustrator',
			'kpr'     => 'application/x-kpresenter',
			'kpt'     => 'application/x-kpresenter',
			'ksp'     => 'application/x-kspread',
			'kwd'     => 'application/x-kword',
			'kwt'     => 'application/x-kword',
			'latex'   => 'application/x-latex',
			'lha'     => 'application/octet-stream',
			'lzh'     => 'application/octet-stream',
			'm3u'     => 'audio/x-mpegurl',
			'man'     => 'application/x-troff-man',
			'me'      => 'application/x-troff-me',
			'mesh'    => 'model/mesh',
			'mid'     => 'audio/midi',
			'midi'    => 'audio/midi',
			'mif'     => 'application/vnd.mif',
			'mov'     => 'video/quicktime',
			'movie'   => 'video/x-sgi-movie',
			'mp2'     => 'audio/mpeg',
			'mp3'     => 'audio/mpeg',
			'mp4'     => 'video/mp4',
			'mpe'     => 'video/mpeg',
			'mpeg'    => 'video/mpeg',
			'mpg'     => 'video/mpeg',
			'mpga'    => 'audio/mpeg',
			'ms'      => 'application/x-troff-ms',
			'msh'     => 'model/mesh',
			'mxu'     => 'video/vnd.mpegurl',
			'nc'      => 'application/x-netcdf',
			'odb'     => 'application/vnd.oasis.opendocument.database',
			'odc'     => 'application/vnd.oasis.opendocument.chart',
			'odf'     => 'application/vnd.oasis.opendocument.formula',
			'odg'     => 'application/vnd.oasis.opendocument.graphics',
			'odi'     => 'application/vnd.oasis.opendocument.image',
			'odm'     => 'application/vnd.oasis.opendocument.text-master',
			'odp'     => 'application/vnd.oasis.opendocument.presentation',
			'ods'     => 'application/vnd.oasis.opendocument.spreadsheet',
			'odt'     => 'application/vnd.oasis.opendocument.text',
			'ogg'     => 'application/ogg',
			'otg'     => 'application/vnd.oasis.opendocument.graphics-template',
			'oth'     => 'application/vnd.oasis.opendocument.text-web',
			'otp'     => 'application/vnd.oasis.opendocument.presentation-template',
			'ots'     => 'application/vnd.oasis.opendocument.spreadsheet-template',
			'ott'     => 'application/vnd.oasis.opendocument.text-template',
			'pbm'     => 'image/x-portable-bitmap',
			'pdb'     => 'chemical/x-pdb',
			'pdf'     => 'application/pdf',
			'pgm'     => 'image/x-portable-graymap',
			'pgn'     => 'application/x-chess-pgn',
			'png'     => 'image/png',
			'pnm'     => 'image/x-portable-anymap',
			'ppm'     => 'image/x-portable-pixmap',
			'ps'      => 'application/postscript',
			'qt'      => 'video/quicktime',
			'ra'      => 'audio/x-realaudio',
			'ram'     => 'audio/x-pn-realaudio',
			'ras'     => 'image/x-cmu-raster',
			'rgb'     => 'image/x-rgb',
			'rm'      => 'audio/x-pn-realaudio',
			'roff'    => 'application/x-troff',
			'rpm'     => 'application/x-rpm',
			'rtf'     => 'text/rtf',
			'rtx'     => 'text/richtext',
			'sgm'     => 'text/sgml',
			'sgml'    => 'text/sgml',
			'sh'      => 'application/x-sh',
			'shar'    => 'application/x-shar',
			'silo'    => 'model/mesh',
			'sis'     => 'application/vnd.symbian.install',
			'sit'     => 'application/x-stuffit',
			'skd'     => 'application/x-koan',
			'skm'     => 'application/x-koan',
			'skp'     => 'application/x-koan',
			'skt'     => 'application/x-koan',
			'smi'     => 'application/smil',
			'smil'    => 'application/smil',
			'snd'     => 'audio/basic',
			'so'      => 'application/octet-stream',
			'spl'     => 'application/x-futuresplash',
			'src'     => 'application/x-wais-source',
			'stc'     => 'application/vnd.sun.xml.calc.template',
			'std'     => 'application/vnd.sun.xml.draw.template',
			'sti'     => 'application/vnd.sun.xml.impress.template',
			'stw'     => 'application/vnd.sun.xml.writer.template',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc'  => 'application/x-sv4crc',
			'swf'     => 'application/x-shockwave-flash',
			'sxc'     => 'application/vnd.sun.xml.calc',
			'sxd'     => 'application/vnd.sun.xml.draw',
			'sxg'     => 'application/vnd.sun.xml.writer.global',
			'sxi'     => 'application/vnd.sun.xml.impress',
			'sxm'     => 'application/vnd.sun.xml.math',
			'sxw'     => 'application/vnd.sun.xml.writer',
			't'       => 'application/x-troff',
			'tar'     => 'application/x-tar',
			'tcl'     => 'application/x-tcl',
			'tex'     => 'application/x-tex',
			'texi'    => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tgz'     => 'application/x-gzip',
			'tif'     => 'image/tiff',
			'tiff'    => 'image/tiff',
			'torrent' => 'application/x-bittorrent',
			'tr'      => 'application/x-troff',
			'tsv'     => 'text/tab-separated-values',
			'txt'     => 'text/plain',
			'ustar'   => 'application/x-ustar',
			'vcd'     => 'application/x-cdlink',
			'vrml'    => 'model/vrml',
			'wav'     => 'audio/x-wav',
			'wax'     => 'audio/x-ms-wax',
			'wbmp'    => 'image/vnd.wap.wbmp',
			'wbxml'   => 'application/vnd.wap.wbxml',
			'wm'      => 'video/x-ms-wm',
			'wma'     => 'audio/x-ms-wma',
			'wml'     => 'text/vnd.wap.wml',
			'wmlc'    => 'application/vnd.wap.wmlc',
			'wmls'    => 'text/vnd.wap.wmlscript',
			'wmlsc'   => 'application/vnd.wap.wmlscriptc',
			'wmv'     => 'video/x-ms-wmv',
			'wmx'     => 'video/x-ms-wmx',
			'wrl'     => 'model/vrml',
			'wvx'     => 'video/x-ms-wvx',
			'xbm'     => 'image/x-xbitmap',
			'xht'     => 'application/xhtml+xml',
			'xhtml'   => 'application/xhtml+xml',
			'xml'     => 'text/xml',
			'xpm'     => 'image/x-xpixmap',
			'xsl'     => 'text/xml',
			'xwd'     => 'image/x-xwindowdump',
			'xyz'     => 'chemical/x-xyz',
			'zip'     => 'application/zip',
			'doc'     => 'application/msword',
			'dot'     => 'application/msword',
			'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'docm'    => 'application/vnd.ms-word.document.macroEnabled.12',
			'dotm'    => 'application/vnd.ms-word.template.macroEnabled.12',
			'xls'     => 'application/vnd.ms-excel',
			'xlt'     => 'application/vnd.ms-excel',
			'xla'     => 'application/vnd.ms-excel',
			'xlsx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'xlsm'    => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'xltm'    => 'application/vnd.ms-excel.template.macroEnabled.12',
			'xlam'    => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'xlsb'    => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'ppt'     => 'application/vnd.ms-powerpoint',
			'pot'     => 'application/vnd.ms-powerpoint',
			'pps'     => 'application/vnd.ms-powerpoint',
			'ppa'     => 'application/vnd.ms-powerpoint',
			'pptx'    => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'potx'    => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'ppsx'    => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'ppam'    => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'pptm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'potm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'ppsm'    => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
		);
		
		$ext = strtolower( array_pop( explode( '.', $filename ) ) );
		
		if ( ! empty( $mime_types[$ext] ) ) {
			if ( true === $debug )
				return array( 'mime_type' => $mime_types[$ext], 'method' => 'from_array' );
			return $mime_types[$ext];
		}
		
		if ( true === $debug )
			return array( 'mime_type' => 'application/octet-stream', 'method' => 'last_resort' );
		return 'application/octet-stream';
	}
	
	//A function to montior access to the module generator
	function monitor($title, $functions = false, $hideHTML = false) {
		global $connDBA, $strippedRoot;
		
		$titlePrefix = "Module Setup Wizard : ";
		
		if ($hideHTML == true) {
			$class = " class=\"overrideBackground\"";
		} else {
			$class = "";
		}
		
		headers($titlePrefix . $title, "Site Administrator", $functions, true, $class, false, false, false, false, $hideHTML);
		$parentTable = "moduledata";
		$prefix = "";
		
		if (isset($_SESSION['currentModule'])) {
			$lessonTable = $prefix . "modulelesson" . "_" . $_SESSION['currentModule'];
			$testTable = $prefix . "moduletest" . "_" . $_SESSION['currentModule'];
			$directory = "../" . $_SESSION['currentModule'] . "/";
			$gatewayPath = "../../gateway.php/modules/" . $_SESSION['currentModule'] . "/";
			$redirect = "../module_wizard/test_content.php";
			$type = "Module";
			
			if (isset($_SESSION['currentModule'])) {
				$currentModule = $_SESSION['currentModule'];
				$currentTable = $_SESSION['currentModule'];
			} else {
				$currentModule = "";
				$currentTable = "";
			}
			
			$monitor = array("parentTable" => $parentTable, "lessonTable" => $lessonTable, "testTable" => $testTable, "prefix" => $prefix, "directory" => $directory, "gatewayPath" => $gatewayPath, "currentModule" => $currentModule, "currentTable" => $currentTable, "title" => $titlePrefix, "redirect" => $redirect, "type" => $type);
		} else {
			$id = lastItem($parentTable);
			$directory = "../" . $id . "/";
			
			$monitor = array("parentTable" => $parentTable, "title" => $titlePrefix, "prefix" => $prefix, "directory" => $directory);
		}
		
		$pageFile = end(explode("/", $_SERVER['SCRIPT_NAME']));
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
			$moduleDataTestGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'", $connDBA);
			$moduleDataTest = mysql_fetch_array($moduleDataTestGrabber);
			
			if ($moduleDataTestGrabber && empty($moduleDataTest['name'])) {
				$allowedArray = array("index.php", "lesson_settings.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_settings.php");
				}
			}
				
			if (!exist($monitor['lessonTable'], "position", "1")) {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "manage_content.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_content.php");
				}
			}
			
			if ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "0") {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "complete.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("test_check.php");
				}
			} elseif ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "1") {
				if (empty($moduleDataTest['testName'])) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_settings.php");
					}
				}
				
				if (!empty($moduleDataTest['testName']) && ! exist($monitor['testTable'], "position", "1")) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_content.php");
					}
				}
				
				if (!empty($moduleDataTest['testName']) && exist($monitor['testTable'], "position", "1")) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview.php", "test_verify.php", "complete.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_verify.php");
					}
				}
			}
		} elseif (!isset($_SESSION['currentModule']) && !strstr($_SERVER['REQUEST_URI'], "lesson_settings.php")) {
			$allowedArray = array("index.php", "lesson_settings.php");
			
			if (!in_array($pageFile, $allowedArray)) {
				redirect("lesson_settings.php");
			}
		}
		
		return $monitor;
	}
	
	//A function to keep track of steps in a module
	function navigation($title, $text, $break = true) {
		global $connDBA, $monitor;
		
		echo "<div class=\"layoutControl\"><div class=\"contentLeft\">";
		title($monitor['title'] . $title, $text, $break);
		echo "</div><div class=\"dataRight\" style=\"padding-top:15px;\">";
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
			$moduleDataTestGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'", $connDBA);
			$moduleDataTest = mysql_fetch_array($moduleDataTestGrabber);
		}
		
		echo "<ul id=\"navigationmenu\"><li class=\"toplast\"><a name=\"navigation\"><span>Navigation</span></a><ul><li>";
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php") . "</li>";
		} else {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php") . "</li>";
		}
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Content", "lesson_content.php") . "</li>";
		}
				
		if (exist($monitor['lessonTable'], "position", "1")) {
			echo "<li>" . URL("Verify Lesson", "lesson_verify.php") . "</li>";
		}
		
		if ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "0") {
			echo "<li>" . URL("Add Test", "test_check.php", "incomplete") . "</li>";
			echo "<li>" . URL("Complete", "complete.php", "complete") . "</li>";
		} elseif ($moduleDataTestGrabber && exist($monitor['lessonTable']) == true && $moduleDataTest['test'] == "1") {
			if ($moduleDataTestGrabber && $moduleDataTest['test'] == "1") {
				echo "<li>" . URL("Test Settings", "test_settings.php") . "</li>";
			}
			
			if (!empty($moduleDataTest['testName'])) {
				echo "<li>" . URL("Test Content", "test_content.php") . "</li>";
			}
			
			if (exist($monitor['testTable'], "position", "1")) {
				echo "<li>" . URL("Verify Test", "test_verify.php") . "</li>";
			}
			
			if (!empty($moduleDataTest['testName']) && exist($monitor['testTable'], "position", "1")) {
				echo "<li>" . URL("Complete", "complete.php", "complete") . "</li>";
			}
		}
		
		echo "</ul></li></ul></div></div>";

	}
	
	//A function to prevent access to question question types if certain sessions are set
	function questionAccess() {
		if (isset($_SESSION['currentModule'])) {
			die(errorMessage("The question bank cannot be opened while the module wizard is open. Please finish the module before opening the question bank."));
		}
		
		if (isset($_SESSION['feedback'])) {
			die(errorMessage("The question bank cannot be opened while the feedback generator is open. Please finish the close the feedback generator before opening the question bank."));
		}
		
		//$_SESSION['currentModule']
	}
	
	//A function to regulate the how questions are inserted and updated
	function insertQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		switch ($type) {
			case "Module" :
				mysql_query("INSERT INTO `{$monitor['testTable']}` (
							`id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
							) VALUES (
							{$moduleQuery}
							)", $connDBA);
				break;
				
		}
	}
	
	function updateQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		$update = $_GET['id'];
		
		switch ($type) {
			case "Module" :
				mysql_query("UPDATE `{$monitor['testTable']}` SET {$moduleQuery} WHERE `id` = '{$update}'", $connDBA);
				break;
				
		}
		
	}
	
	//Live check if data exists
	function checkName($label, $table, $column) {
		global $connDBA;
		
		if (isset($_GET['checkName'])) {
			$inputNameSpaces = $_GET['checkName'];
			$inputNameNoSpaces = str_replace(" ", "", $_GET['checkName']);
			$checkName = mysql_query("SELECT * FROM `{$table}` WHERE `{$column}` = '{$inputNameSpaces}'", $connDBA);
			
			if ($name = mysql_fetch_array($checkName)) {					
				if (isset($_SESSION['currentModule'])) {
					if (strtolower($name['name']) != strtolower($_SESSION['currentModule'])) {
						echo "<div class=\"error\" id=\"errorWindow\">A " . $label . " with this name already exists</div>";
					} else {
						echo "<p>&nbsp;</p>";
					}
				} else {
					echo "<div class=\"error\" id=\"errorWindow\">A " . $label . " with this name already exists</div>";
				}
			} else {
				echo "<p>&nbsp;</p>";
			}
			
			echo "<script type=\"text/javascript\">validateName()</script>";
			die();
		}
	}
	
	//Check the user's access to a particular item
	function access($access = false) {
		//modifyModule
		//moduleStatistics
		
		if (isset($_SESSION['MM_UserGroup'])) {
			switch ($_SESSION['MM_UserGroup']) {
				case "Site Administrator" :
					return true;
					break;
				
				case "Student" :
					return false;
					break;
			}
		} else {
			return false;
		}
	}
	
	//Check to see if a user is logged in 
	function loggedIn() {
		if (isset($_SESSION['MM_Username'])) {
			return true;
		} else {
			return false;
		}
	}
	
	//Run a mysql_query
	function query($query, $returnType = false, $showError = true) {
		global $connDBA;
		
		$action = mysql_query($query, $connDBA);
		
		if (!$action) {
			if ($showError == true) {
				$error = debug_backtrace();
				die(errorMessage("There is an error with your query: " . $query . "<br /><br />" . mysql_error() . "<br /><br />Error on line: " . $error['0']['line'] . "<br />Error in file: " . $error['0']['file']));
			} else {
				return false;
			}
		} else {
			if (!strstr($query, "INSERT INTO") && !strstr($query, "UPDATE") && !strstr($query, "SET") && !strstr($query, "CREATE TABLE")) {
				if ($returnType == false || $returnType == "array") {
					$result = mysql_fetch_array($action);
					
					if (is_array($result) && !empty($result)) {
						array_merge_recursive($result);
						$return = array();
						
						foreach ($result as $key => $value) {
							$return[$key] = prepare($value, false, true);
						}
						
						return $result;
					} else {
						return false;
					}
					
					unset($query, $action, $result);
					exit;
				} elseif ($returnType == "raw") {
					$actionTest = mysql_query($query, $connDBA);
					$result = mysql_fetch_array($actionTest);
					
					if ($result) {
						return $action;
					} else {
						return false;
					}
					
					unset($query, $action, $result);
					exit;
				} elseif ($returnType == "num") {
					$result = mysql_num_rows($action);
					return $result;
					unset($query, $action, $result);
					exit;
				} elseif ($returnType == "selected") {
					$return = array();
					
					while ($result = mysql_fetch_array($action)) {
						array_push($return, $result);
					} 
					
					return flatten($return,array());
					unset($query, $action, $result);
					exit;
				}
			}
		}
	}
	
	//Grab the user's data
	function userData() {
		global $connDBA;
		
		$userInfoGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$_SESSION['MM_Username']}'", $connDBA);
		$userInfo = mysql_fetch_array($userInfoGrabber);
		return $userInfo;
	}
/* End system functions */
	
/* Begin statistics tracker */
	//Set the activity meter
	function activity($setActivity = "false") {
		global $root;
		global $connDBA;
		
		if ($setActivity == "true" && isset($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			$activityTimestamp = time();
			mysql_query("UPDATE `users` SET `active` = '{$activityTimestamp}' WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		}
	}
	
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