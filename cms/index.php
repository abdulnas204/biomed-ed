<?php 
//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("cms") . "config.php");
	require_once(relativeAddress("cms/system/php") . "index.php");
	headers("Pages Control Panel", "liveSubmit,customVisible", true); 

//Reorder pages	
	reorder("pages", "index.php");

//Set page avaliability
	avaliability("pages", "index.php");
	
//Delete a page
	delete("pages", "index.php");
	
//Title
	title("Pages Control Panel", "This is the pages control panel, where you can add, edit, delete, and reorder pages.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Create New Page", "manage_page.php", "toolBarItem new");
	echo URL("Manage Site Settings", "site_settings.php", "toolBarItem settings");
	echo URL("Manage Sidebar", "sidebar.php", "toolBarItem sideBar");

	if (exist("pages")) {
		echo URL("Preview this Site", "../../index.php", "toolBarItem search");
	}
	
	echo "</div>";

//Display message updates
	message("added", "page", "success", "The page was successfully added");
	message("updated", "page", "success", "The page was successfully updated");
	message("updated", "logo", "success", "The logo was successfully updated. It may take a few moments to update across the system.");
	message("updated", "icon", "success", "The browser icon was successfully updated. It may take a few moments to update across the system.");
	message("updated", "siteInfo", "success", "The site information was successfully updated");
	message("updated", "page", "theme", "The theme was successfully updated");

//Pages table
	if (exist("pages")) {
		$pageGrabber = mysql_query("SELECT * FROM pages ORDER BY `position` ASC", $connDBA);
		
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		
		while($pageData = mysql_fetch_array($pageGrabber)) {
			echo "<tr";
			if ($pageData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"25\">"; option($pageData['id'], $pageData['visible'], "pageData", "visible"); echo "</td>";
			echo "<td width=\"75\">"; reorderMenu($pageData['id'], $pageData['position'], "pageData", "pages"); echo "</td>";
			echo "<td width=\"200\">";
			
			if ($pageData['position'] == "1") {
				$class = "homePage";
			} else {
				$class = "";
			}
			
			echo URL($pageData['title'], "../../index.php?page=" . $pageData['id'], $class, false, "Preview the <strong>" . $pageData['title'] . "</strong> page");
			
			echo "</td>";
			echo "<td>" . commentTrim(100, $pageData['content']) . "</td>";
			echo "<td width=\"50\">" . URL(false, "manage_page.php?id=" . $pageData['id'], "action edit", false, "Edit the <strong>" . $pageData['title'] . "</strong> page") . "</td>";
			echo "<td width=\"50\">" . URL(false, "index.php?action=delete&id=" . $pageData['id'], "action delete", false, "Delete the <strong>" . $pageData['title'] . "</strong> page", true) . "</td>";
			echo "</tr>";
		}
		
		echo "</tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no pages. " . URL("Create one now", "manage_page.php") . ".</div>";
	 }
	  
//Include the footer
	footer();
?>