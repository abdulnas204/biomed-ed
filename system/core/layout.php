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
Last updated: Novemeber 27th, 2010

This script is used to construct the layout of a page.
*/

//Include the start of a page
	function headers($title, $functions = false, $toolTip = false, $additionalParameters = false, $publicNavigation = false, $meta = false, $description = false, $additionalKeywords = false, $hideHTML = false, $customScript = false) {
		global $root, $rootUserName;
		
	//Grab needed information
		$siteInfo = query("SELECT * FROM `siteprofiles`");
		$requestURL = $_SERVER['REQUEST_URI'];
		
	//Include a <noscript> redirect	
		$requestURL = $_SERVER['REQUEST_URI'];
		
		if (!strstr($requestURL, "enable_javascript.php")) {
			$noScript = "<noscript>
  <meta http-equiv=\"refresh\" content=\"0; url=" . $root . "enable_javascript.php\">
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
<meta name=\"copyright\" content=\"" . $siteInfo['copyright'] . "\" />";
		
		if ($description == "") {
			$metaInformation .= "
<meta name=\"description\" content=\"" . $siteInfo['description'] . "\" />";
		} else {
			$metaInformation .= "
<meta name=\"description\" content=\"" . prepare(strip_tags($description)) . "\" />";
		}
		
		if ($additionalKeywords == "") {
			$metaInformation .= "
<meta name=\"keywords\" content=\"" . $siteInfo['meta'] . "\" />";
		} else {
			$metaInformation .= "
<meta name=\"keywords\" content=\"" . $siteInfo['meta'] . ", " . prepare($additionalKeywords) . "\" />";
		}
		
	//Include additional functions
		$scripts = "";
		
		if ($functions == true) {
			$functionsArray = explode(",", $functions);
			
			foreach ($functionsArray as $functions) {
				$scripts .= $functions();
			}
		}
		
	//Decide whether or not to display a white background
		if ($hideHTML == true && $hideHTML != "XML") {
			$additionalHTML = " class=\"overrideBackground\"" . $additionalParameters;
		} else {
			$additionalHTML = $additionalParameters;
		}
		
	//Include a tooltip
		if ($toolTip == true) {
			$toolTipScript = "
<!-- Tooltip javascript //-->
<script src=\"" . $root . "system/javascripts/common/tooltip.js\" type=\"text/javascript\"></script>";
		} else {
			$toolTipScript = "";
		}
		
	//Test to see which item to highlight on the navigation bar
		function headerHighLight($text, $test) {
			global $root, $requestURL;
			
			if (strstr($requestURL, $test)) {
				return URL($text, $root . $test . "/index.php", "topCurrentPageNav");
			} else {
				return URL($text, $root . $test . "/index.php", "topPageNav");
			}
		}
		
	//Generate the content at the top of the page
		if ($hideHTML == false) {
		//Begin layout of the page
			$HTML = "
  
<!-- The top content of the page //-->
<div id=\"page\">
  <div id=\"header_bg\">
    <div id=\"header\" class=\"clearfix\">
      <h1 class=\"headermain\">" . $siteInfo['siteName'] . "</h1>
      <div class=\"headermenu\">
        <div class=\"logininfo\">
          <!-- Login status //-->";
			
		//Include the user login status
			if (loggedIn()) {
				$userName = $_SESSION['userName'];
				$name = query("SELECT * FROM `users` WHERE `userName` = '{$userName}'");
				
				$HTML .= "
          You are logged in as " . URL($name['firstName'] . " " . $name['lastName'], $root . "users/profile.php?id=" . $name['id']) . " 
          ";
				
				if (isset($_SESSION['developerAdministration']) && $_SESSION['developerAdministration'] === $rootUserName) {
					$HTML .= 
          URL("(Administration Logout)", $root . "admin/logout.php") . "
          " .  URL("(Logout)", "admin/logout.php?action=complete");
				} else {
					$HTML .= 
          URL("(Logout)", $root . "logout.php");
				}
			} else {
				$HTML .= "
          You are not logged in. " . URL("(Login)", $root . "login.php");
			}
		
			$HTML .= "
        </div>
      </div>
    </div>
	  
    <!-- The banner content //-->  
    <div id=\"banner_bg\">
      <div id=\"banner\">";
			
		//Include the logo
			$HTML .= "
        <div style=\"padding-top:" . $siteInfo['paddingTop'] . "px; padding-bottom:" . $siteInfo['paddingBottom'] . "px; padding-left:" .  $siteInfo['paddingLeft'] . "px; padding-right:" . $siteInfo['paddingRight'] . "px;\">
          ";
			
			if (loggedIn()) {
				$home = "portal/index.php";
			} else {
				$home = "index.php";
			}
			
			$bannerImage .= "<img src=\"" . "" . $root . "system/images/banner.png\"";
			
			if ($siteInfo['auto'] !== "on") {
				$bannerImage .= " width=\"" . $siteInfo['width'] . "\" height=\"" . $siteInfo['height'] . "\"";
			} 
			
			$bannerImage .= " alt=\"" . $siteInfo['siteName'] . "\" title=\"" . $siteInfo['siteName'] . "\">";
			
			$HTML .= 
          URL($bannerImage, $root . $home) . 
        "</div>
      </div>
    </div>";
		
		//Include the navigation bar
			$HTML .= "	
    <div id=\"navbar_bg\">
      <div class=\"navbar clearfix\">
        <div class=\"breadcrumb\">
          <div class=\"menu\">
            <!-- Site navigation //-->
            <ul>";
			
			if ($publicNavigation == false) {
				if (loggedIn()) {
					$URL = $_SESSION['role'];
				} else {
					$URL = "Public";
				}
			} else {
				$URL = "Public";
			}
			
			switch ($URL) {
			//Public website navigation bar
				case "Public" :
					$pageData = query("SELECT * FROM pages ORDER BY position ASC", "raw");	
					$lastPageCheck = query("SELECT * FROM pages ORDER BY position DESC LIMIT 1");
					
					if (isset ($_GET['page'])) {
						$currentPage = $_GET['page'];
					}
					
					while ($pageInfo = fetch($pageData)) {
						if (isset ($currentPage)) {
							if ($pageInfo['visible'] == "on") {
								if ($currentPage == $pageInfo['id']) {
									$class = "topCurrentPageNav";
								} else {
									$class = "topPageNav";
								}
							}
						} else {
							if ($pageInfo['visible'] == "on") {
								if ($pageInfo['position'] == "1") {
									$class = "topCurrentPageNav";
								} else {
									$class = "topPageNav";
								}
							}
						}
						
						$HTML .= "
              <li> " . URL($pageInfo['title'], "index.php?page=" . $pageInfo['id'], $class) . " </li>";
					}
					
					break;
					
			//Site administrator navigation bar
				case "Site Administrator" : 
					$HTML .= "
              <li> " . headerHighLight("Home", "portal") . " </li>
              <li> " . headerHighLight("Users", "users") . " </li>
              <li> " . headerHighLight("Organizations", "organizations") . " </li>
              <li> " . headerHighLight("Communication", "communication") . " </li>
              <li> " . headerHighLight("Modules", "modules") . " </li>
              <li> " . headerHighLight("Statistics", "statistics") . " </li>
              <li> " . headerHighLight("Public Webiste", "cms") . " </li>
              <li> " . URL("Logout", $root . "logout.php", "topPageNav") . " </li>";
				
					break;
					
			//Organization administrator navigation bar
				case "Organization Administrator" : 
					$HTML .= "
              <li> " . headerHighLight("Home", "portal") . " </li>
              <li> " . headerHighLight("Users", "users") . " </li>
              <li> " . headerHighLight("Organization", "organization") . " </li>
              <li> " . headerHighLight("Communication", "communication") . " </li>
              <li> " . headerHighLight("Modules", "modules") . " </li>
              <li> " . headerHighLight("Statistics", "statistics") . " </li>
              <li> " . URL("Logout", $root . "logout.php", "topPageNav") . " </li>";
				
					break;
					
			//Instrcutor navigation bar
				case "Instructor" : 
					$HTML .= "
              <li> " . headerHighLight("Home", "portal") . " </li>
              <li> " . headerHighLight("Users", "users") . " </li>
              <li> " . headerHighLight("Communication", "communication") . " </li>
              <li> " . headerHighLight("Modules", "modules") . " </li>
              <li> " . headerHighLight("Statistics", "statistics") . " </li>
              <li> " . URL("Logout", $root . "logout.php", "topPageNav") . " </li>";
				
					break;
					
			//If this is the student navigation bar
				case "Student" :
					$HTML .= "
              <li> " . headerHighLight("Home", "portal") . " </li>
              <li> " . headerHighLight("Modules", "modules") . " </li>
              <li> " . URL("Logout", $root . "logout.php", "topPageNav") . " </li>";
				
					break;
			}
			
			$HTML .= "
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
    
  <!-- Page content //-->
  <div id=\"content\">
    <div class=\"box generalboxcontent boxaligncenter\">";
		}
		
	//Construct the HTML
		echo "<!--
This system is powered by Ensigma Pro, an Apex Development
project.

System Developer: Oliver Spryn

For more information on this product visit:
http://apexdevelopment.businesscatalyst.com

---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.

Third-party works are accredited where necessary.
---------------------------------------------------------
//-->

<!--
Generated on " . date("F j, Y \a\\t g:i A") . "
//-->

<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head>
<!-- The title of the page //-->
<title>" . $siteInfo['siteName'] .  " | " . $title . "</title>
  
<!-- Handle browsers with disabled JavaScript //-->
" . $noScript . "
  
<!-- The shortcut icon //-->
<link type=\"" . $MIME . "\" rel=\"shortcut icon\" href=\"" . $root . "system/images/icon." . $siteInfo['iconType'] . "\" />" . $metaInformation . "
  
<!-- Include javascripts and stylesheets //-->
<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "system/styles/common/universal.css\" />
<link rel=\"stylesheet\" type=\"text/css\" href=\"" . $root . "system/styles/themes/" . $siteInfo['style'] . "\" />
" . $scripts . "

</head>
<body" . $additionalHTML . ">" . $toolTipScript . $HTML;
	}
	
//Include a footer
	function footer($publicNavigation = false, $hideHTML = false) {
		global $root;
		
	//Grab needed information
		$footer = query("SELECT * FROM siteprofiles");
		$requestURL = $_SERVER['REQUEST_URI'];
		
	//Test to see which item to highlight on the navigation bar
		function footerHighLight($text, $test) {
			global $root, $requestURL;
			
			if (strstr($requestURL, $test)) {
				return "
      " . URL($text, $root . $test . "/index.php", "bottomCurrentPageNav") . "
      <span class=\"arrow sep\">&bull;</span>";
			} else {
				return "
      " . URL($text, $root . $test . "/index.php", "bottomPageNav") . "
      <span class=\"arrow sep\">&bull;</span>";
			}
		}
		
		if ($hideHTML == false) {
			echo "
      <br />
    </div>
  </div>

  <!-- Branding //-->
  <div align=\"center\">
    " . URL("<img src=\"http://apexdevelopment.businesscatalyst.com/img/branding/apex_development_footer.png\">", "http://apexdevelopment.businesscatalyst.com/", false, "_blank") . "
    " . URL("<img src=\"http://apexdevelopment.businesscatalyst.com/img/branding/ensigma_pro.png\">", "http://apexdevelopment.businesscatalyst.com/", false, "_blank") . "
  </div>

  <div id=\"footer\">
    <div>&nbsp;</div>
    <!-- Footer navigation //-->
    <div class=\"breadcrumb\">";
				
			if ($publicNavigation == false) {
				if (loggedIn()) {
					$URL = $_SESSION['role'];
				} else {
					$URL = "Public";
				}
			} else {
				$URL = "Public";
			}
				
			switch ($URL) {
			//Public website footer bar
				case "Public" :
					$pageData = query("SELECT * FROM pages ORDER BY position ASC", "raw");	
					$lastPageCheck = query("SELECT * FROM pages ORDER BY position DESC LIMIT 1");
					
					if (isset ($_GET['page'])) {
						$currentPage = $_GET['page'];
					}
				
					while ($pageInfo = fetch($pageData)) {
						if (isset ($currentPage)) {
							if ($pageInfo['visible'] != "") {
								if ($currentPage == $pageInfo['id']) {
									$class = "bottomCurrentPageNav";
								} else {
									$class = "bottomPageNav";
								}
							}
						} else {
							if ($pageInfo['visible'] != "") {
								if ($pageInfo['position'] == "1") {
									$class = "bottomCurrentPageNav";
								} else {
									$class = "bottomPageNav";
								}
							}
						}
						
						if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
							echo "
      " . URL($pageInfo['title'], "index.php?page=" . $pageInfo['id'], $class) . "
      <span class=\"arrow sep\">&bull;</span>";
						} else {
							echo "
      " . URL($pageInfo['title'], "index.php?page=" . $pageInfo['id'], $class);
						}
					}
					
					break;
				
			//If this is the site administrator footer bar
				case "Site Administrator" : 
      echo footerHighLight("Home", "portal") . 
      footerHighLight("Users", "users") . 
      footerHighLight("Organizations", "organizations") . 
      footerHighLight("Communication", "communication") . 
      footerHighLight("Modules", "modules") . 
      footerHighLight("Statistics", "statistics") . 
      footerHighLight("Public Website", "cms") . 
	  URL("Logout", $root . "logout.php");
					break;
					
			//If this is the organization administrator footer bar
				case "Organization Administrator" : 
      echo footerHighLight("Home", "portal") . 
      footerHighLight("Users", "users") . 
      footerHighLight("Organization", "organization") . 
      footerHighLight("Communication", "communication") . 
      footerHighLight("Modules", "modules") . 
      footerHighLight("Statistics", "statistics") . 
	  URL("Logout", $root . "logout.php");
					break;
					
			//If this is the instructor footer bar
				case "Instructor" : 
      echo footerHighLight("Home", "portal") . 
      footerHighLight("Users", "users") . 
      footerHighLight("Communication", "communication") . 
      footerHighLight("Modules", "modules") . 
      footerHighLight("Statistics", "statistics") . 
	  URL("Logout", $root . "logout.php");
					break;
					
			//If this is the student footer bar
				case "Student" : 
      echo footerHighLight("Home", "portal") .  
      footerHighLight("Modules", "modules") . 
	  URL("Logout", $root . "logout.php");
					break;
			}
		}
			
			echo "
    </div>
	
    <!-- Footer text //-->
    <div class=\"footer\">
    " . $footer['siteFooter'] . "
    </div>
  </div>
</div>
<br />
</body>
</html>";
		}
?>
