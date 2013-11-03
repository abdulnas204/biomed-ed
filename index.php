<?php
//Header functions
	require_once('system/connections/connDBA.php');
	login();
	
//Pages processor
	//Check to see if any pages exist
	if (exist("pages", "position", "1") == true) {
		$pagesExist = 1;
	} else {
		$pagesExist = 0;
	}

	//If no page URL variable is defined, then choose the home page
	if (!isset ($_GET['page']) || $_GET['page'] == "") { 
		$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM `pages` WHERE `position` = '1'", $connDBA));
	} else {		
		$getPageID = $_GET['page'];
		$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM `pages` WHERE `id` = {$getPageID}", $connDBA));	
	}
	
//Sidebar processor
	$sideBarCheck = mysql_query("SELECT * FROM `sidebar` WHERE `visible` = 'on'", $connDBA);
	if (mysql_fetch_array($sideBarCheck)) {
		$sideBarDataGrabber = mysql_query("SELECT * FROM `sidebar` WHERE `visible` = 'on'", $connDBA);
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
	
	if (!$pageInfo && $pagesExist == 0) {
		$title = "Setup Required";
	} else {
		if (empty($pageInfo['content'])) {
			$title = "Page Not Found";
		} else {
			$title = $pageInfo['title'];
		}
	}
	
//Top content
	headers($title, false, false, false, false, true);

//Use the layout control if the page is displaying a sidebar
	$sideBarLocationGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
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

//Admin toolbar
	if (isset($_SESSION['MM_Username']) && !empty($pageInfo['content'])) {
	//The admin toolbox div
		form("pages", "post", false, false, "site_administrator/cms/index.php");
		echo "<div class=\"toolBar noPadding\"><div align=\"center\">";
		URL("Edit This Page", "site_administrator/cms/manage_page.php?id=" . $pageInfo['id']);
		echo " | Visible: ";
		hidden("action", "action", "setAvaliability");
		hidden("id", "id", $pageInfo['id']);
		hidden("redirect", "redirect", "true");
		dropDown("option", "option", "Yes,No", "on,", false, false, false, false, "pageInfo", "pageInfo", "visible", " onchange=\"this.form.submit();\"");
		echo " | ";
		URL("Back to Staff Home Page", "site_administrator/index.php");
		echo " | ";
		URL("Back to Pages", "site_administrator/cms/index.php");
		echo " | ";
		URL("Back to Sidebar", "site_administrator/cms/sidebar.php");
		echo "</div></div>";
		closeForm(false, false);
	}
	
//Display the page content	
	if (empty($pageInfo['content']) && $pagesExist == 0) {
		if (!isset($_SESSION['MM_Username'])) {
			title("Setup Required", "Please " . URL("login", "login.php") . " to create your first page.");
		} else {
			title("Setup Required", "Please " . URL("create your first page", "site_administrator/cms/manage_page.php") . ".");
		}
	} else {
		if (empty($pageInfo['content'])) {
			title("Page Not Found", "The page you are looking for was not found on our system");
			echo "<p>&nbsp;</p><p align=\"center\">";
			button("continue", "continue", "Continue", "history");
			echo "</p>";
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
			sideBox($sideBar['title'], $sideBar['type'], $sideBar['content'], "Site Administrator,Site Manager", $sideBar['id']);
		}
		
		echo "</div></div>";
	}
	
//Include the footer
	footer(true);
?>