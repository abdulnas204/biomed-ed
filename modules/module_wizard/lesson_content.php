<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Module Content", "navigationMenu");
	
//Reorder pages	
	reorder($monitor['lessonTable'], "lesson_content.php");
	
//Delete a page
	if (isset ($_GET['id']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$deleteGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` WHERE `id` = '{$id}'", $connDBA);
		$delete = mysql_fetch_array($deleteGrabber);
		
		if ($deleteGrabber) {
			if (empty($delete['attachment'])) {
				delete($monitor['lessonTable'], "lesson_content.php", true, $monitor['directory'] . "lesson/" . $delete['attachment']);
			} else {
				delete($monitor['lessonTable'], "lesson_content.php", true);
			}
		}
	}
	
//Title
	navigation("Module Content", "All of the content for this lesson will be managed from this page.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Add New Page", "manage_content.php", "toolBarItem new");
	echo "</div>";
	
//Display message updates
	message("inserted", "page", "success", "The page was successfully inserted");
	message("updated", "page", "success", "The page was successfully updated");

//Pages table
	if (exist($monitor['lessonTable'])) {
		$pageGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` ORDER BY `position` ASC", $connDBA);
		
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"75\" class=\"tableHeader\">Order</th><th width=\"250\" class=\"tableHeader\">Title</th><th class=\"tableHeader\">Content</th><th width=\"75\" class=\"tableHeader\">Edit</th><th width=\"75\" class=\"tableHeader\">Delete</th></tr>";
		
		while($lessonData = mysql_fetch_array($pageGrabber)) {
			echo "<tr";
			if ($lessonData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"75\">"; reorderMenu($lessonData['id'], $lessonData['position'], "lessonData", $monitor['lessonTable']); echo "</td>";
			echo "<td width=\"250\">" . URL(commentTrim(30, $lessonData['title']), "preview_page.php?page=" . $lessonData['position'], false, false, "Preview the <strong>" . $lessonData['title'] . "</strong> page", false, true, "640", "480") . "</td>";
			echo "<td>" . commentTrim(55, $lessonData['content']) .  "</td>";			
			echo "<td width=\"75\">" . URL(false, "manage_content.php?id=" .  $lessonData['id'], "action edit", false, "Edit the <strong>" . $lessonData['title'] . "</strong> page", false) . "</td>";
			echo "<td width=\"75\">" . URL(false, "lesson_content.php?id=" .  $lessonData['id'] . "&action=delete", "action delete", false, "Delete the <strong>" . $lessonData['title'] . "</strong> page", true) . "</td>";
			closeForm(false, false);
			echo "</tr>";
		}
		
		echo "</tbody></table>";
	} else {
		echo "<div class=\"noResults\">There are no pages in this lesson. <a href=\"manage_content.php\">Create a new page now</a>.</div>";
	}
	
//Display navigation buttons
	echo "<blockquote>";
	button("back", "back", "&lt;&lt; Previous Step", "button", "lesson_settings.php");
	
	if (exist($monitor['lessonTable'])) {
		button("next", "next", "Next Step &gt;&gt;", "button", "lesson_verify.php");
		
		if (isset ($_SESSION['review'])) {
			button("submit", "submit", "Finish", "button", "../index.php?updated=module");
		}
	}
	
	echo "</blockquote>";
	
//Include the footer
	footer();
?>