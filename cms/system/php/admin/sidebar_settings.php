<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Sidebar Settings", "Site Administrator"); 

//Grab the form data
	$sideBarGrabber = mysql_query("SELECT * FROM `siteprofiles` WHERE `id` = '1'", $connDBA);
	$sideBar = mysql_fetch_array($sideBarGrabber);

//Process the form
	if (isset($_POST['submit']) && isset($_POST['side'])) {
		$side = $_POST['side'];
		
		mysql_query("UPDATE `siteprofiles` SET `sideBar` = '{$side}'", $connDBA);
		header("Location: sidebar.php?updated=settings");
		exit;
	}

//Title
	title("Sidebar Settings", "Set which side of the page the sidebar will display.");

//Settings form
	form("settings");
	catDivider("Settings", "one", true);
	echo "<blockquote>";
	directions("Sidebar location");
	echo "<blockquote><p>";
	radioButton("side", "side", "Left,Right", false, true, true, false, "sideBar", "sideBar", "sideBar");
	echo "</p></blockquote></blockquote>";
	catDivider("Settings", "two");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("cancel", "cancel", "Cancel", "cancel", "sidebar.php");
	echo "</p>";
	closeForm(true, false);

//Include the footer
	footer();
?>