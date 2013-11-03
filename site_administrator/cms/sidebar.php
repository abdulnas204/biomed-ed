<?php 
//Header functions
	require_once('../../Connections/connDBA.php');
	headers("Sidebar Control Panel", "Site Administrator", "liveSubmit,customVisible", true); 

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
	echo URL("Create New Box", "manage_sidebar.php", "toolBarItem new");
	echo URL("Manage Sidebar Settings", "sidebar_settings.php", "toolBarItem settings");
	echo URL("Back to Pages", "index.php", "toolBarItem back");

	if (exist("sidebar") == true) {
		echo URL("Preview this Site", "../../index.php", "toolBarItem search");
	}
	
	echo "</div>";
	
//Display message updates
	message("added", "item", "success", "The box was successfully added");
	message("updated", "item", "success", "The box was successfully updated");
	message("updated", "settings", "success", "The sidebar settings were successfully updated");

//Boxes table
	if (exist("sidebar") == true) {
		$itemGrabber = mysql_query("SELECT * FROM sidebar ORDER BY `position` ASC", $connDBA);
		
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\" width=\"150\">Type</th><th class=\"tableHeader\">Content</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		
		while($itemData = mysql_fetch_array($itemGrabber)) {
			echo "<tr";
			if ($itemData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"25\">"; option($itemData['id'], $itemData['visible'], "itemData", "visible"); echo "</td>";
			echo "<td width=\"75\">"; reorderMenu($itemData['id'], $itemData['position'], "itemData", "sideBar"); echo "</td>";
			echo "<td width=\"200\">" . prepare($itemData['title']) . "</td>";
			echo "<td width=\"150\">" . $itemData['type'] . "</td>";
			echo "<td>";
			
			if ($itemData['type'] == "Login") {
				echo "<span class=\"notAssigned\">None</span>";
			} else {
				echo commentTrim(70, $itemData['content']);
			}
			
			echo "</td>";
			echo "<td width=\"50\">" . URL("", "manage_sidebar.php?id=" . $itemData['id'], "action edit", false, "Edit the <strong>" . prepare($itemData['title'], true) . "</strong> box") . "</td>"; 
			echo "<td width=\"50\">" . URL("", "sidebar.php?action=delete&id=" . $itemData['id'], "action delete", false, "Delete the <strong>" . prepare($itemData['title'], true) . "</strong> box", true) . "</td>";
		}
		
		echo "</tbody></table>";
	} else {
		echo "<div class=\"noResults\">This site has no sidebar boxes. " . URL("Create one now", "manage_sidebar.php") . ".</div>";
	} 

//Include the footer
	footer();
?>