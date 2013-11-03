<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	headers("Access Denied"); 
	
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

//Display the error content
	echo "<h2>Access Denied</h2>";
	
	if (isset($_GET['error']) && $_GET['error'] == "403") {
		errorMessage("You do not have premission to access this content");
	} elseif (isset($_GET['error']) && $_GET['error'] == "404") {
		errorMessage("The page you are looking for was not found on our system");
	} else {
		errorMessage("You do not have premission to access this content");
	}
	
	echo "<p>&nbsp;</p><p align=\"center\">";
	button("continue", "continue", "Continue", "history");
	echo "</p>";

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
	footer();
?>