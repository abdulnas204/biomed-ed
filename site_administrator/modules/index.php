<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Grab all module data
	$moduleDataCheckGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position ASC", $connDBA);
	$moduleDataCheck = mysql_fetch_array($moduleDataCheckGrabber);
	
//Check to see if any modules exist
	$moduleCheck = $moduleDataCheck['id'];
	if (!$moduleCheck) {
		$modules = "empty";
	} else {
		$modules = "exist";
	}
	
?>
<?php
//Reorder the items
	if (isset($_GET['id']) && isset($_GET['currentPosition']) && $_GET['action'] == "reorder") {
	//Grab all necessary data
		//Grab the id of the moving item
		$id = $_GET['id'];
		//Grab the new position of the item
		$newPosition = $_GET['position'];
		//Grab the old position of the item
		$currentPosition = $_GET['currentPosition'];
			
	//Do not process if item does not exist
		//Get item name by URL variable
		$getItemID = $_GET['position'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE position = {$getItemID}", $connDBA);
		$itemCheckArray = mysql_fetch_array($itemCheckGrabber);
		$itemCheckResult = $itemCheckArray['position'];
			 if (isset ($itemCheckResult)) {
				 $itemCheck = 1;
			 } else {
				$itemCheck = 0;
			 }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE moduledata SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE moduledata SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE moduledata SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE moduledata SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
		}
	}
?>
<?php
//Forward to editor
	if (isset ($_GET['id']) && $_GET['action'] == "edit") {
		$id = $_GET['id'];
		$sessionSetGrabber = mysql_query("SELECT * FROM moduledata WHERE id = '{$id}'", $connDBA);
		$sessionSet = mysql_fetch_array($sessionSetGrabber);
		if (!$sessionSet) {
			header ("Location: index.php");
			exit;
		}
		
		$_SESSION['currentModule'] = $sessionSet['name'];
		$_SESSION['review'] = "review";
		$_SESSION['difficulty'] = $sessionSet['difficulty'];
		$_SESSION['category'] = $sessionSet['category'];
		$_SESSION['testSettings'] = "modify";
		
		header ("Location: module_wizard/modify.php");
		exit;
	}
?>
<?php
//Set module avaliability
	if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
		$id = $_POST['id'];
		if (!$_POST['option']) {
			$option = "";
		} else {
			$option = $_POST['option'];
		}
		
		$setAvaliability = "UPDATE moduledata SET `avaliable` = '{$option}' WHERE id = '{$id}'";
		mysql_query($setAvaliability, $connDBA);
		
		header ("Location: index.php");
		exit;
	}
?>
<?php
//Delete a module
	if (isset ($_GET['id']) && isset ($_GET['module']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$deleteGrabber = mysql_query("SELECT * FROM moduledata WHERE id = '{$id}'", $connDBA);
		$delete = mysql_fetch_array($deleteGrabber);
		if (!$delete) {
			header ("Location: index.php");
			exit;
		}

	//Update the database	
		$position = $delete['position'];
		$tableName = str_replace(" ", "", $delete['name']);
		$directoryName = str_replace(" ", "", $delete['name']);
		mysql_query("DELETE FROM moduledata WHERE id = '{$id}' LIMIT 1", $connDBA);
		mysql_query("UPDATE moduledata SET position = position-1 WHERE position > '{$position}'", $connDBA);
		mysql_query("DROP TABLE `moduletest_{$tableName}`", $connDBA);
		mysql_query("DROP TABLE `modulelesson_{$tableName}`", $connDBA);
		
	//Delete the directories
		deleteAll("../../modules/" . $directoryName);
		
		header ("Location: index.php");
		exit;
	}
?>
<?php
//Unset old sessions
	unset($_SESSION['currentModule']);
	unset($_SESSION['difficulty']);
	unset($_SESSION['category']);
	unset($_SESSION['review']);
	unset($_SESSION['step']);
	unset($_SESSION['testSettings']);
	unset($_SESSION['bankCategory']);
	unset($_SESSION['categoryName']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Administration"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
<script type="text/javascript" src="../../javascripts/common/warningDelete.js"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Module Administration</h2>
    <p>Below is a list of all modules which are currently avaliable to organizations.</p>
    <p>&nbsp;</p>
      <div class="toolBar"><a class="toolBarItem new" href="module_wizard/index.php">Add New Module</a><a class="toolBarItem bank" href="question_bank/index.php">Question Bank</a><a class="toolBarItem settings" href="settings.php">Customize Settings</a><a class="toolBarItem feedback" href="feedback/index.php">Feedback</a>
        <?php
			if ($modules == "exist") {
			echo "<a class=\"toolBarItem search\" href=\"../../modules/index.php\">Preview Modules</a>";
			}
		?>
      </div>
<br />
</br />
<?php
	  if ($modules == "exist") {
		  echo "<table class=\"dataTable\">";
		  echo "<tbody>";
			  echo "<tr>";
				  echo "<th width=\"25\" class=\"tableHeader\"></th>";
				  echo "<th width=\"50\" class=\"tableHeader\">Order</th>";
				  echo "<th width=\"200\" class=\"tableHeader\">Module Name</th>";
				  echo "<th class=\"tableHeader\">Comments</th>";
				  echo "<th width=\"50\" class=\"tableHeader\">Statistics</th>";
				  echo "<th width=\"50\" class=\"tableHeader\">Edit</th>";
				  echo "<th width=\"50\" class=\"tableHeader\">Delete</th>";
			  echo "</tr>";
		  //Select data for the loop
			  $moduleDataGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position ASC", $connDBA);
			  
		  //Select data for drop down menu
			  $dropDownDataGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position ASC", $connDBA);
			  
		  //Loop through the items		
			  while ($moduleData = mysql_fetch_array($moduleDataGrabber)){
				  echo "<tr";
				  if ($moduleData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				  ">";
					  echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $moduleData['id'] . "\" class=\"visible"; if ($moduleData['avaliable'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $moduleData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $moduleData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($moduleData['avaliable'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
				  
					  echo "<td width=\"50\"><form name=\"modules\" action=\"index.php\">";
							  echo "<select name=\"position\" onchange=\"this.form.submit();\">";
							  $moduleCount = mysql_num_rows($dropDownDataGrabber);
							  for ($count=1; $count <= $moduleCount; $count++) {
								  echo "<option value=\"{$count}\"";
								  if ($moduleData ['position'] == $count) {
									  echo " selected=\"selected\"";
								  }
								  echo ">$count</option>";
							  }
							  echo "</select>";
						  echo "<input type=\"hidden\" name=\"action\" value=\"reorder\">";
						  echo "<input type=\"hidden\" name=\"id\" value=\"";
						  echo $moduleData['id'];
						  echo "\">";
						  echo "<input type=\"hidden\" name=\"currentPosition\" value=\"";
						  echo $moduleData['position'];
						  echo "\"></form></td>";
					  
					  echo "<td width=\"200\"><a href=\"../../modules/lesson.php?id=" . $moduleData['id'] . "\" onmouseover=\"Tip('Launch the <strong>" . $moduleData['name'] . "</strong> module')\" onmouseout=\"UnTip()\">" . $moduleData['name'] . "</a></td>";
					  
					  echo "<td>" . commentTrim(60, $moduleData['comments']) . "</td>";
					  echo "<td width=\"50\"><a class=\"action statistics\" href=\"../statistics/index.php?type=module&period=overall&id=" . $moduleData['id'] . "\" onmouseover=\"Tip('View the <strong>" . $moduleData['name'] . "</strong> module\'s statistics</strong>')\" onmouseout=\"UnTip()\"></a></td>";
					  echo "<td width=\"50\"><a class=\"action edit\" href=\"index.php?action=edit&module=" . $moduleData['position'] . "&id=" . $moduleData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . $moduleData['name'] . "</strong> module')\"  onmouseout=\"UnTip()\"></a></td>";
					  echo "<td width=\"50\"><a class=\"action delete\" href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&module=" . $moduleData['position'] . "&id=" . $moduleData['id'] . "', 'module');\" onmouseover=\"Tip('Delete the <strong>" . $moduleData['name'] . "</strong> module')\" onmouseout=\"UnTip()\"></a></td>";
				  echo "</tr>";
			  }
		  echo "</tbody></table>";
	  } else {
		  echo "<div class=\"noResults\">There are no modules. <a href=\"module_wizard/index.php\">Create one now</a>.</div>";
	  }
?> 
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>