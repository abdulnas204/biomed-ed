<?php require_once('../Connections/connDBA.php'); ?>
<?php
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
<?php title("Access Denied"); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php
	if (isset ($_SESSION['MM_UserGroup'])) {
		switch($_SESSION['MM_UserGroup']) {
			case "Student": $topPage = "student/includes/top_menu.php"; break;
			case "Instructor": $topPage = "instructor/includes/top_menu.php"; break;
			case "Organization Administrator": $topPage = "administrator/includes/top_menu.php"; break;
			case "Site Administrator": $topPage = "site_administrator/includes/top_menu.php"; break;
		}
	} else {
		$topPage = "includes/top_menu.php";
	}
?>
<?php topPage($topPage); ?>
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

//Display the error content
	echo "<h2>Access Denied</h2>";
	
	if (isset($_GET['error']) && $_GET['error'] == "403") {
		errorMessage("You do not have premission to access this content");
	} elseif (isset($_GET['error']) && $_GET['error'] == "404") {
		errorMessage("The page you are looking for was not found on our system");
	} else {
		errorMessage("You do not have premission to access this content");
	}
	
	echo "<p>&nbsp;</p><p align=\"center\"><input type=\"button\" name=\"continue\" id=\"continue\" value=\"Continue\" onclick=\"history.go(-1)\" /></p>";

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
					} elseif (isset($_SESSION['MM_Username']) && $_SESSION['MM_UserGroup'] != "Site Administrator" && $_SESSION['MM_UserGroup'] != "Site Manager") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\">" . $sideBar['content'] . "</div></div>";
					} elseif (isset($_SESSION['MM_Username']) && $_SESSION['MM_UserGroup'] == "Site Administrator" || $_SESSION['MM_UserGroup'] == "Site Manager") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"site_administrator/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div><div class=\"content\">" . $sideBar['content'] . "</div></div>";
					} break;
			//If this is a login box	
				case "Login" : 
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\"><form id=\"login\" name=\"login\" method=\"post\" action=\"index.php\"><div align=\"center\"><div style=\"width:75%;\"><p>User name: <input type=\"text\" name=\"username\" id=\"username\" autocomplete=\"off\" /><br />Password: <input type=\"password\" name=\"password\" id=\"password\" autocomplete=\"off\" /></p><p><input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Login\" /></p></div></div></form></div></div>";
					} elseif (isset($_SESSION['MM_Username']) && $_SESSION['MM_UserGroup'] == "Site Administrator" || $_SESSION['MM_UserGroup'] == "Site Manager") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"site_administrator/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div></div>";
					} break;
			//If this is a registration box
				case "Register" : 
					if (!isset($_SESSION['MM_Username'])) {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "</h2></div></div><div class=\"content\">" . $sideBar['content'] . "<div align=\"center\"><input type=\"button\" name=\"register\" id=\"register\" value=\"Register\" onclick=\"MM_goToURL('parent','register.php');return document.MM_returnValue\" /></div><p>&nbsp;</p></div></div>";
					} elseif (isset($_SESSION['MM_Username']) && $_SESSION['MM_UserGroup'] == "Site Administrator" || $_SESSION['MM_UserGroup'] == "Site Manager") {
						echo "<div class=\"block_course_list sideblock\"><div class=\"header\"><div class=\"title\"><h2>" . $sideBar['title'] . "&nbsp;<a class=\"smallEdit\" href=\"site_administrator/cms/manage_sidebar.php?id=" . $sideBar['id'] . "\"></a></h2></div></div></div>";
					} break;
			}
		}
		
		echo "</div></div>";
	}

//Display the bottom
	if (isset ($_SESSION['MM_UserGroup'])) {
		switch($_SESSION['MM_UserGroup']) {
			case "Student": $bottomPage = "student/includes/bottom_menu.php"; break;
			case "Instructor": $bottomPage = "instructor/includes/bottom_menu.php"; break;
			case "Organization Administrator": $bottomPage = "administrator/includes/bottom_menu.php"; break;
			case "Site Administrator": $bottomPage = "site_administrator/includes/bottom_menu.php"; break;
		}
	} else {
		$bottomPage = "includes/bottom_menu.php";
	}
?>
<?php footer($bottomPage); ?>
</body>
</html>