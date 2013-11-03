<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
	if (access("modifyModule")) {
		$functions = "livesubmit,customVisible";
	} else {
		$functions = "";
	}
	
	headers("Modules", false, $functions, true);
	
	if (access("modifyModule")) {
	//Reorder modules	
		reorder("moduledata", "index.php");
	
	//Set module avaliability
		avaliability("moduledata", "index.php");
		
	//Delete a module
		if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete") {
			$id = $_GET['id'];
			
			delete("moduledata", "index.php", true, false, $id, "modulelesson_{$id},moduletest_{$id}");
		}
	
	//Forward to editor
		if (isset ($_GET['id']) && $_GET['action'] == "edit") {
			$id = $_GET['id'];
			$organizationPrep = userData();
			$organization = $organizationPrep['organization'];
			$moduleData = exist("moduledata", "id", $_GET['id']);
			
			if (exist("moduledata", "id", $_GET['id']) && $moduleData['organization'] == $organization) {
				$sessionSetGrabber = mysql_query("SELECT * FROM `moduledata` WHERE id = '{$id}'", $connDBA);
				$sessionSet = mysql_fetch_array($sessionSetGrabber);
				
				if (!$sessionSetGrabber) {
					redirect("index.php");
				}
				
				$_SESSION['currentModule'] = $sessionSet['id'];
				$_SESSION['review'] = "review";
				
				redirect("module_wizard/lesson_settings.php");
			} else {
				redirect("overview.php?id=" . $_GET['id']);
			}
		}
	}
	
//Unset active sessions
	unset($_SESSION['currentModule'], $_SESSION['review']);
	
//Title
	$access = array("Site Administrator", "Site Manager");
  
	if (loggedIn()) {
		if (!in_array($_SESSION['MM_UserGroup'], $access)) {
			$additionalSQL = " WHERE `visible` = 'on'";
		} else {
			 $additionalSQL = "";
		}
	} else {
		$additionalSQL = " WHERE `visible` = 'on'";
	}
	
	$moduleDataGrabber = mysql_query("SELECT * FROM `moduledata`{$additionalSQL} ORDER BY `position` ASC", $connDBA);
	$moduleNumberGrabber = mysql_query("SELECT * FROM `moduledata`{$additionalSQL} ORDER BY `position` ASC", $connDBA);
	$moduleNumber = mysql_num_rows($moduleNumberGrabber);
	
	if (loggedIn()) {
		$userData = userData();
		$modules = unserialize($userData['modules']);
	} else {
		$modules = array();
	}
	
	if (!access("buyModule") || (sizeof($modules) < $moduleNumber || !is_array($modules))) {
		$content = "Below is a list of all modules. Click the checkbox beside each module you would like the purchase, then click &quot;Add to Cart&quot;.";
	} else {
		$content = "Below is a list of all modules.";
	}
	 
	title("Modules", $content);
	
//Admin toolbar
	if (access("modifyModule") || access("assignUser")) {
		echo "<div class=\"toolBar\">";
		
		if (access("modifyModule")) {
			echo URL("Add New Module", "module_wizard/index.php", "toolBarItem new");
		}
		
		if (access("modifyAllModules")) {
			echo URL("Question Bank", "question_bank/index.php", "toolBarItem bank");
			echo URL("Customize Settings", "settings.php", "toolBarItem settings");
			echo URL("Feedback", "feedback/index.php", "toolBarItem feedback");
		}
		
		if (access("assignUser")) {
			echo URL("Assign Users", "assign/index.php", "toolBarItem user");
		}
		
		echo "</div><br />";
	}
	
//Modules table
	if (exist("moduledata")) {	
		 $organizationPrep = userData();
		 $organization = $organizationPrep['organization'];
		
		 if (access("buyModule")) {
			 form("purchaseModules", "post", true, "enroll/cart.php");
		 }
		 
		 echo "<table class=\"dataTable\"><tbody><tr>";
		 
		 if (access("modifyModule") || access("moduleAvailability")) {
			 echo "<th width=\"25\" class=\"tableHeader\"></th><th width=\"50\" class=\"tableHeader\">Order</th>";
		 }
		 
		 echo "<th width=\"200\" class=\"tableHeader\">Module Name</th>";
		 
		 if (access("moduleDetails")) {
			 echo "<th width=\"200\" class=\"tableHeader\">Category</th>";
		 }
		 
		 echo "<th class=\"tableHeader\">Comments</th>";
		 
		 if (access("moduleStatistics")) {
			 echo "<th width=\"50\" class=\"tableHeader\">Statistics</th>";
		 }
		 
		 if (access("modifyModule")) {
			 echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
			 
			 if (exist("moduledata", "organization", $organization)) {
			 	echo "<th width=\"50\" class=\"tableHeader\">Delete</th>";
			 }
		 }
		 
		 if (access("buyModule") && (sizeof($modules) < $moduleNumber || !is_array($modules))) {
			 echo "<th width=\"100\" class=\"tableHeader\">Buy</th>";
		 }
		
		 echo "</tr>";
		 
		 while ($moduleData = mysql_fetch_array($moduleDataGrabber)) {
			  echo "<tr";
			  if ($moduleData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			  
			  if (access("modifyModule")) {
				  $currentLesson = $moduleData['id'];
				  $lessonCheckGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentLesson}`", $connDBA);
				  $lessonCheck = mysql_num_rows($lessonCheckGrabber);
				  
				  if ($lessonCheck < 1) {
					  echo "<td width=\"25\"><div align=\"center\"><span onmouseover=\"Tip('There isn\'t any lesson content to this module. <br />Please add content before displaying this module.')\" onmouseout=\"UnTip()\" class=\"noShow\"></span></div></td>";
				  } else {
					  echo "<td width=\"25\">"; option($moduleData['id'], $moduleData['visible'], "moduleData", "visible"); echo "</td>";
				  }
				  
				  echo "<td width=\"75\">"; reorderMenu($moduleData['id'], $moduleData['position'], "moduleData", "moduledata"); echo "</td>";
			  }
			  
			  echo "<td width=\"200\">" . URL(commentTrim(30, $moduleData['name']), "lesson.php?id=" . $moduleData['id'], false, false, "Launch the <strong>" . $moduleData['name'] . "</strong> module')") . "</td>";
			  
			  if (access("moduleDetails")) {
				  $categoryGrabber = mysql_query("SELECT * FROM `modulecategories` WHERE `id` = '{$moduleData['category']}'", $connDBA);
				  $category = mysql_fetch_array($categoryGrabber);
				  
				  echo "<td width=\"200\">" . prepare($category['category'], false, true) . "</td>";
				  echo "<td>" . commentTrim(60, $moduleData['comments']) . "</td>";
			  } else {
				  echo "<td>" . commentTrim(80, $moduleData['comments']) . "</td>"; 
			  }
			  
			  if (access("moduleStatistics")) {
				  echo "<td width=\"50\">" . URL(false, "../statistics/index.php?type=module&period=overall&id=" . $moduleData['id'], "action statistics", false, "View the <strong>" . $moduleData['name'] . "</strong> module's statistics") . "</td>";
			  }
			  
			  if (access("modifyModule")) {
				  if (access("modifyAllModules")) {
					  echo "<td width=\"50\">" . URL(false, "index.php?action=edit&id=" . $moduleData['id'], "action edit", false, "Edit the <strong>" . $moduleData['name'] . "</strong> module") . "</td>";
				  } else {
					  if ($moduleData['locked'] == "0") {
						  echo "<td width=\"50\">" . URL(false, "index.php?action=edit&id=" . $moduleData['id'], "action edit", false, "Edit the <strong>" . $moduleData['name'] . "</strong> module") . "</td>";
					  } else {
						  echo "<td width=\"50\">" . tip("This module cannot be edited", false, "action noEdit") . "</td>";
					  }
				  }
				  
				  if (exist("moduledata", "organization", $organization) && $moduleData['organization'] == $organization) {
					  echo "<td width=\"50\">" . URL(false, "index.php?action=delete&id=" . $moduleData['id'], "action delete", false, "Delete the <strong>" . $moduleData['name'] . "</strong> module", true) . "</td>";
				  } elseif (exist("moduledata", "organization", $organization)) {
					  echo "<td width=\"50\">" . tip("This module cannot be deleted", false, "action noDelete") . "</td>";
				  }
			  }
			  
			  if (access("buyModule") && (sizeof($modules) < $moduleNumber || !is_array($modules))) {
				 echo "<td width=\"100\">";
				 $price = str_replace(".", "", $moduleData['price']);
				 
				 if (!empty($moduleData['enablePrice']) && !empty($moduleData['price']) && $price > 0) {
					 if (!is_array($modules) || !array_key_exists($moduleData['id'], $modules)) {
						 checkbox("cart[]", "option" . $moduleData['id'], " $" . $moduleData['price'] , $moduleData['id'], true, "1"); echo "</td>";
					 }
				 } else {
					 if (!is_array($modules) || !array_key_exists($moduleData['id'], $modules)) {
						 echo URL("No Charge", "index.php?id=" . $moduleData['id'] . "&action=enroll", false, false, "Click to enroll");
					 }
				 }
			  }
			  
			echo "</tr>";
		 }
		 
		 echo "</tbody></table>";
		 
		 if (access("buyModule") && (sizeof($modules) < $moduleNumber || !is_array($modules))) {
			 echo "<div align=\"right\"><p>"; button("submit", "submit", "Add Selected Modules to Cart", "submit"); echo "</p></div>";
		 }
		 
		 if (access("modifyModule") == false) {
			 closeForm(false, false);
		 }
	 } else {
		 echo "<div class=\"noResults\">This site has no modules.";
		  
		 if (access("modifyModule")) {
			 echo " " . URL("Create one now", "module_wizard/index.php") . ".";
		 }
		  
		 echo "</div>";
	 }
	 
//Include the footer
	footer();
?>