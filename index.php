<?php require_once('Connections/connDBA.php'); ?>
<?php login(); ?>
<?php
//Check to see if any pages exist
	$pagesExistGrabber = mysql_query("SELECT * FROM pages WHERE position = '1'", $connDBA);
	$pagesExistArray = mysql_fetch_array($pagesExistGrabber);
	$pagesExistResult = $pagesExistArray['position'];
	
	if (isset ($pagesExistResult)) {
		$pagesExist = 1;
	} else {
		$pagesExist = 0;
	}
	
//If no page URL variable is defined, then choose the home page
	if (!isset ($_GET['page']) || $_GET['page'] == "") {
	//Grab the page data	 
		$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE position = '1'", $connDBA));
		
	//Hide the admin menu if an incorrect page displays
		$pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE position = '1'", $connDBA);
		$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		$pageCheckResult = $pageCheckArray['position'];
		
		if (isset ($pageCheckResult)) {
			$pageCheck = 1;
		} else {
			$pageCheck = 0;
		}
	} else {		
	//Grab the page data
		$getPageID = $_GET['page'];
		$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE id = {$getPageID}", $connDBA));
		
	//Hide the admin menu if an incorrect page displays
		$pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE id = {$getPageID}", $connDBA);
		$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		$pageCheckResult = $pageCheckArray['position'];
		
		if (isset ($pageCheckResult)) {
			$pageCheck = 1;
		} else {
			$pageCheck = 0;
		}	
	}
//Grab the sidebar
	$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on'", $connDBA);
	if (mysql_fetch_array($sideBarCheck)) {
		$sideBarDataGrabber = mysql_query("SELECT * FROM sidebar WHERE visible = 'on'", $connDBA);
		$sideBarArray = array();
		
		while ($sideBarData = mysql_fetch_array($sideBarDataGrabber)) {
			switch ($sideBarData['type']) {
				case "Login" : $login = "true"; break;
				case "Register" : $register = "true"; break;
				case "Custom Content" : $customContent = "true"; break;
			}
		}
		
		if (isset($_SESSION['MM_Username'])) {
			if (isset($login) || isset($register) && !isset($customContent)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			} elseif (isset($login) && isset($register)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			} elseif (!isset($login) || !isset($register)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			} elseif (!isset($login) && !isset($register)) {
				if (isset($customContent)) {
					$sideBarResult = "true";
				}
			}
		} else {
			$sideBarResult = "true";
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	if ($pageInfo == 0 && $pagesExist == 0) {
		$title = "Setup Required";
	} else {
		if (empty($pageInfo['content'])) {
			$title = "Page Not Found";
		} else {
			$title = $pageInfo['title'];
		}
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php meta(); ?>
<script src="javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body>
<?php
	topPage("includes/top_menu.php");
?>
<?php
//Use the layout control if the page is displaying a sidebar
	$sideBarLocationGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
	$sideBarLocation = mysql_fetch_array($sideBarLocationGrabber);
		
	if (isset($sideBarResult)) {
		echo "<div class=\"layoutControl\"><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "contentRight";
		} else {
			echo "contentLeft";
		}
		echo "\">";
	}

//Display content based on login status
	if (isset($_SESSION['MM_Username']) && isset($pageCheck) && $pageCheck !== 0) {
	//The admin toolbox div
		echo "<form name=\"pages\" method=\"post\" action=\"site_administrator/cms/index.php\"><div class=\"toolBar noPadding\"><div align=\"center\"><a href=\"site_administrator/cms/manage_page.php?id=" . $pageInfo['id'] . "\">Edit This Page</a>  | Visible: <input type=\"hidden\" name=\"action\" value=\"setAvaliability\" /><input type=\"hidden\" name=\"id\" value=\"" .  $pageInfo['id'] . "\" /><input type=\"hidden\" name=\"redirect\" value=\"true\" /><select name=\"option\" onchange=\"this.form.submit();\"><option value=\"on\""; 
		if ($pageInfo['visible'] == "on") {echo " selected=\"selected\"";} 
		echo ">Yes</option><option value=\"\""; 
		if ($pageInfo['visible'] == "") {echo " selected=\"selected\"";} 
		echo ">No</option></select> | <a href=\"site_administrator/index.php\">Back to Staff Home Page</a> | <a href=\"site_administrator/cms/index.php\">Back to Pages</a> | <a href=\"site_administrator/cms/sidebar.php\">Back to Sidebar</a></div></div></form>";
	}
	
//Display the page content	
	if ($pageCheck == 0 && $pagesExist == 0) {
		echo "<h2>Setup Required</h2>";
		if (!isset($_SESSION['MM_Username'])) {
			alert("Please <a href=\"login.php\">login</a> to create your first page.");
		} else {
			alert("Please <a href=\"site_administrator/cms/manage_page.php\">create your first page</a>.");
		}
	} else {
		if (empty($pageInfo['content'])) {
			echo "<h2>Page Not Found</h2>";
			errorMessage("The page you are looking for was not found on our system");
			echo "<p>&nbsp;</p><p align=\"center\"><input type=\"button\" name=\"continue\" id=\"continue\" value=\"Continue\" onclick=\"history.go(-1)\" /></p>";
		} else {
			echo "<h2>" . $pageInfo['title'] . "</h2>" . $pageInfo['content'];
		}
	}
	
//Display the sidebar
	if (isset($sideBarResult)) {
		$sideBarCheck = mysql_query("SELECT * FROM sidebar WHERE visible = 'on' ORDER BY position ASC", $connDBA);
		
		echo "</div><div class=\"";
		
		if ($sideBarLocation['sideBar'] == "Left") {
			echo "dataLeft";
		} else {
			echo "dataRight";
		}
		
		echo "\"><br /><br /><br />";
		
		while ($sideBar = mysql_fetch_array($sideBarCheck)) {
			switch ($sideBar['type']) {
			//If this is a custom content box
				case "Custom Content" : 					
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\">" . $sideBar['content'] . "</div></div>";
					} else {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"site_administrator/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div><div class=\"content\">" . $sideBar['content'] . "</div></div>";
					} break;
			//If this is a login box	
				case "Login" : 
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\"><form id=\"login\" name=\"login\" method=\"post\" action=\"index.php\"><div align=\"center\"><div style=\"width:75%;\"><p>User name: <input type=\"text\" name=\"username\" id=\"username\" autocomplete=\"off\" /><br />Password: <input type=\"password\" name=\"password\" id=\"password\" autocomplete=\"off\" /></p><p><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Login\" /></p></div></div></form></div></div>";
					} else {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"site_administrator/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div></div>";
					} break;
			//If this is a registration box
				case "Register" : 
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\">" . $sideBar['content'] . "<div align=\"center\"><input type=\"button\" name=\"register\" id=\"register\" value=\"Register\" onclick=\"MM_goToURL('parent','register.php');return document.MM_returnValue\" /></div><p>&nbsp;</p></div></div>";
					} else {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"site_administrator/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div></div>";
					} break;
			}
		}
		
		echo "</div></div>";
	}
?>
<?php
	footer("includes/bottom_menu.php");
?>
</body>
</html>