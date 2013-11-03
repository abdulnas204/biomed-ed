<?php 
//Header functions
	require_once('../../Connections/connDBA.php');
	headers("Module Administration", "Site Administrator", "liveSubmit,customVisible", true); 

//Reorder modules	
	reorder("moduledata", "index.php");

//Set module avaliability
	avaliability("moduledata", "index.php");
	
//Delete a module
	if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		
		delete("moduledata", "index.php", true, false, "../../modules/" . $id, "modulelesson_{$id},moduletest_{$id}");
	}

//Forward to editor
	if (isset ($_GET['id']) && $_GET['action'] == "edit") {
		$id = $_GET['id'];
		
		$sessionSetGrabber = mysql_query("SELECT * FROM `moduledata` WHERE id = '{$id}'", $connDBA);
		$sessionSet = mysql_fetch_array($sessionSetGrabber);
		
		if (!$sessionSetGrabber) {
			redirect("index.php");
		}
		
		$_SESSION['currentModule'] = $sessionSet['id'];
		$_SESSION['review'] = "review";
		$_SESSION['difficulty'] = $sessionSet['difficulty'];
		$_SESSION['testSettings'] = "modify";
		
		redirect("module_wizard/lesson_settings.php");
	}

//Unset old sessions
	unset($_SESSION['currentModule']);
	unset($_SESSION['difficulty']);
	unset($_SESSION['review']);
	unset($_SESSION['step']);
	unset($_SESSION['testSettings']);
	unset($_SESSION['bankCategory']);
	unset($_SESSION['categoryName']);
	
//Title
	title("Module Administration", "Below is a list of all modules.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Add New Module", "module_wizard/index.php", "toolBarItem new");
	echo URL("Question Bank", "question_bank/index.php", "toolBarItem bank");
	echo URL("Customize Settings", "settings.php", "toolBarItem settings");
	echo URL("Feedback", "feedback/index.php", "toolBarItem feedback");
	
	if (exist("moduledata") == true) {
		echo URL("Preview Modules", "../../modules/index.php", "toolBarItem search");
	}
	
	echo "</div>";
	
//Modules table
	if (exist("moduledata") == true) {
		 $moduleDataGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position ASC", $connDBA);
		 
		 echo "<br /><table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"50\" class=\"tableHeader\">Order</th><th width=\"200\" class=\"tableHeader\">Module Name</th><th class=\"tableHeader\">Comments</th><th width=\"50\" class=\"tableHeader\">Statistics</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		 
		 while ($moduleData = mysql_fetch_array($moduleDataGrabber)) {
			  echo "<tr";
			  if ($moduleData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			  $currentLesson = $moduleData['id'];
			  $lessonCheckGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentLesson}`", $connDBA);
			  $lessonCheck = mysql_num_rows($lessonCheckGrabber);
			  
			  if ($lessonCheck < 1) {
				  echo "<td width=\"25\"><div align=\"center\"><span onmouseover=\"Tip('There isn\'t any lesson content to this module. <br />Please add content before displaying this module.')\" onmouseout=\"UnTip()\" class=\"noShow\"></span></div></td>";
			  } else {
				  echo "<td width=\"25\">"; option($moduleData['id'], $moduleData['visible'], "moduleData", "visible"); echo "</td>";
			  }
			  
			  echo "<td width=\"75\">"; reorderMenu($moduleData['id'], $moduleData['position'], "moduleData", "moduledata"); echo "</td>";
			  echo "<td width=\"200\"><a href=\"../../modules/lesson.php?id=" . $moduleData['id'] . "\" onmouseover=\"Tip('Launch the <strong>" . $moduleData['name'] . "</strong> module')\" onmouseout=\"UnTip()\">" . $moduleData['name'] . "</a></td>";
			  echo "<td>" . commentTrim(60, $moduleData['comments']) . "</td>";
			  echo "<td width=\"50\">" . URL(false, "../statistics/index.php?type=module&period=overall&id=" . $moduleData['id'], "action statistics", false, "View the <strong>" . $moduleData['name'] . "</strong> module's statistics") . "</td>";
			  echo "<td width=\"50\">" . URL(false, "index.php?action=edit&id=" . $moduleData['id'], "action edit", false, "Edit the <strong>" . $moduleData['name'] . "</strong> module") . "</td>";
			  echo "<td width=\"50\">" . URL(false, "index.php?action=delete&id=" . $moduleData['id'], "action delete", false, "Delete the <strong>" . $moduleData['name'] . "</strong> module", true) . "</td>";
			echo "</tr>";
		 }
		 
		 echo "</tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no modules. " . URL("Create one now", "module_wizard/index.php") . ".</div>";
	 }
	 
//Include the footer
	footer();
?>