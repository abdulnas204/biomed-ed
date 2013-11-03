<?php
/*
LICENSE: See "license.php" located at the root installation

This is the overview page for managing the sidebar on the website.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	headers("Sidebar Control Panel", "liveSubmit,customVisible", true); 

//Reorder boxes	
	reorder("sidebar", "sidebar.php");

//Set boxes avaliability
	avaliability("sidebar", "sidebar.php");
	
//Delete a boxes
	delete("sidebar", "sidebar.php");
	
//Title
	title("Sidebar Control Panel", "This is the sidebar control panel, where you can add, edit, delete, and reorder boxes. These boxes will contain content which can be accessed on a given side of every page on the public website.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo toolBarURL("Create New Box", "manage_sidebar.php", "toolBarItem new");
	echo toolBarURL("Back to Pages", "index.php", "toolBarItem back");

	if (exist("sidebar") == true) {
		echo URL("Preview this Site", "../index.php", "toolBarItem search");
	}
	
	echo "</div>\n";
	
//Display message updates
	message("added", "item", "success", "The box was successfully added");
	message("updated", "item", "success", "The box was successfully updated");
	message("updated", "settings", "success", "The sidebar settings were successfully updated");

//Boxes table
	if (exist("sidebar") == true) {
		$itemGrabber = mysql_query("SELECT * FROM sidebar ORDER BY `position` ASC", $connDBA);
		
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("", "25");
		echo column("Order", "75");
		echo column("Title", "200");
		echo column("Type", "150");
		echo column("Content");
		echo column("Edit", "50");
		echo column("Delete", "50");
		echo "</tr>";
		
		while($itemData = mysql_fetch_array($itemGrabber)) {
			echo "<tr";
			if ($itemData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo option("sidebar", $itemData['id'], "visible");
			echo reorderMenu("sidebar", $itemData['id']);
			echo preview(commentTrim(30, $itemData['title']), "../index.php", "box", "200");
			echo cell($itemData['type'], "150");
			echo cell(commentTrim(70, $itemData['content']));
			echo editURL("manage_sidebar.php?id=" . $itemData['id'], $itemData['title'], "box");
			echo deleteURL("sidebar.php?action=delete&id=" . $itemData['id'], $itemData['title'], "box");
			echo "</tr>\n";
		}
		
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">This site has no sidebar boxes. " . URL("Create one now", "manage_sidebar.php") . ".</div>\n";
	} 

//Include the footer
	footer();
?>