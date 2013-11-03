<?php
/*
LICENSE: See "license.php" located at the root installation

This script is used to construct the layout of a page.
*/

//Include the start of a page
	function headers($title, $functions = false, $toolTip = false, $bodyParameters = false, $publicNavigation = false, $hideHTML = false, $customScript = false) {
		global $root, $userData, $rootUserName, $protocol;
		
	//Check access to this page
		maintain();
		
	//Grab needed information
		$siteInfo = query("SELECT * FROM `siteprofiles`");
		$requestURL = $_SERVER['REQUEST_URI'];
		
	//Include a <noscript> redirect	
		$requestURL = $_SERVER['REQUEST_URI'];
		
		if (!strstr($requestURL, "enable_javascript.php")) {
			$noScript = "<noscript>
  <meta http-equiv=\"refresh\" content=\"0; url=" . $root . "enable_javascript.htm\">
</noscript>";
		} else {
			$noScript = "
<script type=\"text/javascript\">window.location = \"index.php\"</script>";
		}
		
	//Detirmine the MIME type of the shortcut icon	
		switch ($siteInfo['iconType']) {
			case "ico" : $MIME = "image/x-icon"; break;
			case "jpg" : $MIME = "image/jpeg"; break;
			case "gif" : $MIME = "image/gif"; break;
			case "png" : $MIME = "image/png"; break;
		}
		
	//Generate the meta information
		$metaInformation = "

<!-- The meta information for this page //-->
<meta http-equiv=\"content-language\" content=\"" . $siteInfo['language'] . "\" />
<meta name=\"robots\" content=\"index,follow\">
<meta name=\"generator\" content=\"Ensigma Pro\" />
<meta name=\"author\" content=\"" . $siteInfo['author'] . "\" />
<meta name=\"copyright\" content=\"" . $siteInfo['copyright'] . "\" />
<meta name=\"description\" content=\"" . $siteInfo['description'] . "\" />
<meta name=\"keywords\" content=\"" . $siteInfo['meta'] . "\" />";
		
	//Include additional functions
		$scripts = "";
		
		if ($functions == true) {
			$functionsArray = explode(",", $functions);
			
			$scripts .= sessionControl() . "
";
			
			foreach ($functionsArray as $functions) {
				$scripts .= $functions() . "
";
			}
		} else {
			$scripts .= sessionControl() . "
";
		}
		
	//Decide whether or not to display a white background
		if ($hideHTML == true && $hideHTML != "XML") {
			$additionalHTML = " class=\"overrideBackground\"" . $bodyParameters;
		} else {
			$additionalHTML = $bodyParameters;
		}
		
	//Include a tooltip
		if ($toolTip == true) {
			$toolTipScript = "
<!-- Tooltip javascript //-->
<script src=\"" . $root . "system/javascripts/common/tooltip.js\" type=\"text/javascript\"></script>";
		} else {
			$toolTipScript = "";
		}
		
	//Include additional HTML
		if ($customScript == true) {
			$script = $customScript . "
";
		} else {
			$script = "";
		}
		
	//Test to see which item to highlight on the navigation bar
		function headerHighLight($text, $URL, $position) {
			global $protocol;
			
			if ($position == "1") {
				$class = " first";
			} else {
				$class = false;
			}
			
			if (strstr($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], $URL)) {
				return URL($text, $URL . "index.php", "current" . $class);
			} else {
				return URL($text, $URL . "index.php", $class);
			}
		}
		
	//Generate the content at the top of the page
		if ($hideHTML == false) {
		//Begin layout of the page
			$HTML = "

<!-- The top content of the page //-->
<div class=\"header\">
  <!-- User's login status //-->
  <div class=\"loginStatus\">";
  
  //Include the user login status
	if (loggedIn()) {
		$HTML .= "
    " . URL($userData['firstName'] . " " . $userData['lastName'], $root . "users/profile.php?id=" . $userData['id']);
		
		if (isset($_SESSION['administration']) && $_SESSION['administration'] === $rootUserName) {
			$HTML .= " | " . URL("(Administration Logout)", $root . "admin/logout.php") . " | " . URL("(Logout)", $root . "admin/logout.php?action=complete");
		} else {
			$HTML .= " | " .  URL("Logout", "javascript:;", false, false, false, false, false, false, false, " id=\"logout\"");
		}
	} else {
		$HTML .= "
    " . URL("Register", $root . "users/register.php") . " | " . URL("Login", "javascript:;", false, false, false, false, false, false, false, " id=\"login\"");
	}
	
	$HTML .= "
  </div>
  
  <!-- Sister project navigation //-->
  <ul class=\"tabMenu\">
    <li><a href=\"http://localhost/biomed-ed/\" class=\"first\">Home</a></li><li><a class=\"current\">Certification Preparation</a></li><li><a>About Us</a></li>
  </ul>
	  
  <!-- The banner content //-->  
  <div style=\"padding-top:" . $siteInfo['paddingTop'] . "px; padding-bottom:" . $siteInfo['paddingBottom'] . "px; padding-left:" .  $siteInfo['paddingLeft'] . "px; padding-right:" . $siteInfo['paddingRight'] . "px;\">";
  
  if (loggedIn()) {
	  $home = "portal/index.php";
  } else {
	  $home = "index.php";
  }
			
  $bannerImage = "<img src=\"" . "" . $root . "system/images/banner.png\"";
			
  if ($siteInfo['auto'] !== "on") {
	  $bannerImage .= " width=\"" . $siteInfo['width'] . "\" height=\"" . $siteInfo['height'] . "\"";
  } 
			
  $bannerImage .= " alt=\"" . $siteInfo['siteName'] . "\" title=\"" . $siteInfo['siteName'] . "\">";
  $HTML .= "
    " . URL($bannerImage, $root . $home) . "
  </div>";
		
   //Include the navigation bar
			$HTML .= "
  
  <!-- Site navigation //-->
  <div class=\"navigation\">
    <p>
      ";
			
   if ($publicNavigation == false) {
	   if (loggedIn()) {
		   $URL = "Logged";
	   } else {
		   $URL = "Public";
	   }
   } else {
	   $URL = "Public";
   }
			
//Public website navigation bar
	if ($URL == "Public") {
		$pageData = query("SELECT * FROM `pages` WHERE `visible` = 'on' ORDER BY `position` ASC", "raw");	
		
		if (isset ($_GET['page'])) {
			$currentPage = $_GET['page'];
		}
		
		if ($pageData) {
			while ($pageInfo = fetch($pageData)) {
				if (isset ($currentPage)) {
					if ($currentPage == $pageInfo['id']) {
						$class = "current";
					} else {
						$class = false;
					}
					
					if ($pageInfo['position'] == "1") {
						$class .= " first";
					}
				} else {
					if ($pageInfo['position'] == "1") {
						$class = "current first";
					} else {
						$class = false;
					}
				}
				
				$HTML .= URL($pageInfo['title'], "index.php?page=" . $pageInfo['id'], $class);
			 }
		}
//Generate the navigation bar based on the user's privileges
	} else {
		$addonsGrabber = query("SELECT * FROM `addons` ORDER BY `position`", "raw");
		$roleInfo = query("SELECT * FROM `roles` WHERE `name` = '{$_SESSION['role']}'");
		$userPrivileges = arrayRevert($roleInfo['privileges']);
		
		while ($addon = fetch($addonsGrabber)) {
			$privileges = arrayRevert($addon['privileges']);
			
			if(!empty($privileges)) {
				foreach($privileges as $privilege) {
					$renamed = str_replace(" ", "_", $privilege);
					
					if (array_key_exists($renamed, $userPrivileges) && $userPrivileges[$renamed] == "1") {
						$show = true;
						break;
					}
				}
			} else {
				$show = true;
			}
			
			if (isset($show)) {
				$HTML .= headerHighLight($addon['menuName'], $root . $addon['pluginRoot'], $addon['position']);
			}
			
			unset($privileges, $show);
		}
	}
	
	$HTML =  rtrim($HTML) . "
    </p>
  </div>
</div>
    
<!-- Page content //-->
<div class=\"content\">
";
		} else {
			$HTML = "";
		}
		
	//Modify the $bodyParameters variable for ease of use
		if ($bodyParameters == true) {
			$bodyParameters = " " . $bodyParameters;
		}
		
	//Construct the HTML
		echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
		
<!--
This system is powered by Ensigma Pro, a ForwardFour
project.

System Developer: Oliver Spryn

For more information on this product visit:
http://forwardfour.com

---------------------------------------------------------
(C) Copyright 2011 ForwardFour - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.

Third-party works are accredited where necessary.
---------------------------------------------------------

" . $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . " was generated on " . date("F j, Y \a\\t g:i A") . "
//-->

<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<!-- The title of the page //-->
<title>" . $siteInfo['siteName'] .  " | " . $title . "</title>
  
<!-- Handle browsers with disabled JavaScript //-->
" . $noScript . "
  
<!-- The shortcut icon //-->
<link type=\"" . $MIME . "\" rel=\"shortcut icon\" href=\"" . $root . "system/images/icon." . $siteInfo['iconType'] . "\" />" . $metaInformation . "
  
<!-- Include JavaScripts and StyleSheets //-->
<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "system/styles/common/universal.css\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "system/styles/themes/" . $siteInfo['style'] . "\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "learn/system/styles/style.css\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "cms/system/styles/style.css\" />
";

echo $scripts . $script . "
</head>
<body" . $bodyParameters . ">" . $toolTipScript . $HTML;
	}
	
//Include a footer
	function footer($publicNavigation = false, $hideHTML = false) {
		global $root;
		
	//Grab needed information
		$footer = query("SELECT * FROM siteprofiles");
		$requestURL = $_SERVER['REQUEST_URI'];
		
		if ($hideHTML == false) {
			echo "
</div>";
		}
		
		echo "
  <div id=\"sessionTimeoutWarning\" style=\"display: none\"></div>";
  		if ($hideHTML == false) {
			echo "
  <div class=\"footer\">
    <!-- Footer navigation //-->
    <div class=\"navigation\">";
				
			if ($publicNavigation == false) {
				if (loggedIn()) {
					$URL = $_SESSION['role'];
				} else {
					$URL = "Public";
				}
			} else {
				$URL = "Public";
			}
			
			function footerURL($title, $URL) {
				global $root;
				
				return URL($title, $URL . "index.php");
			}
			
		//Public website footer bar	
			if ($URL == "Public") {
				$pageData = query("SELECT * FROM `pages` WHERE `visible` = 'on' ORDER BY `position` ASC", "raw");	
				
				if (isset ($_GET['page'])) {
					$currentPage = $_GET['page'];
				}
			
				if ($pageData) {
					$lastPageCheck = query("SELECT * FROM `pages` WHERE `visible` = 'on' ORDER BY `position` DESC LIMIT 1");
					
					while ($pageInfo = fetch($pageData)) {
						if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
							echo "
	  " . URL($pageInfo['title'], "index.php?page=" . $pageInfo['id']) . "
	  &#183;";
						} else {
							echo "
	  " . URL($pageInfo['title'], "index.php?page=" . $pageInfo['id']);
						}
					}
				}
		//Generate the navigation bar based on the user's privileges
			} else {
				$addonsGrabber = query("SELECT * FROM `addons` ORDER BY `position`", "raw");
				$roleInfo = query("SELECT * FROM `roles` WHERE `name` = '{$_SESSION['role']}'");
				$userPrivileges = arrayRevert($roleInfo['privileges']);
				$footerNavigation = "
      ";
				
				while ($addon = fetch($addonsGrabber)) {
					$privileges = arrayRevert($addon['privileges']);
					
					if(!empty($privileges)) {
						foreach($privileges as $privilege) {
							$renamed = str_replace(" ", "_", $privilege);
							
							if (array_key_exists($renamed, $userPrivileges) && $userPrivileges[$renamed] == "1") {
								$show = true;
								break;
							}
						}
					} else {
						$show = true;
					}
					
					if (isset($show)) {
						$footerNavigation .= footerURL($addon['menuName'], $root . $addon['pluginRoot']) . " &bull; 
      ";
					}
					
					unset($privileges, $show);
				}
				
				echo rtrim(rtrim($footerNavigation), "&bull;");
			}
			
			echo "
    </div>
	
    <div class=\"layoutControl\">
      <!-- Powered by stamp //-->
      <div class=\"powered\">Application Design: <a href=\"http://forwardfour.com\" target=\"_blank\">ForwardFour.com</a></div>
	  
      <!-- Footer text //-->
      <div class=\"text\">" . $footer['siteFooter'] . "</div>
    </div>
  </div>
</div>
<br />";
		}
		
		echo "
</body>
</html>";

		ob_end_flush();
	}
?>
