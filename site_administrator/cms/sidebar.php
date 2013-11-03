<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Check to see if items exist
	$itemCheck = mysql_query("SELECT * FROM sidebar WHERE `position` = 1", $connDBA);
	if (mysql_fetch_array($itemCheck)) {
		$itemGrabber = mysql_query("SELECT * FROM sidebar ORDER BY position ASC", $connDBA);
	} else {
		$itemGrabber = 0;
	}

//Reorder items	
	if (isset ($_GET['action']) && $_GET['action'] == "modifySettings" && isset($_GET['id']) && isset($_GET['position']) && isset($_GET['currentPosition'])) {
	//Grab all necessary data	
	  //Grab the id of the moving item
	  $id = $_GET['id'];
	  //Grab the new position of the item
	  $newPosition = $_GET['position'];
	  //Grab the old position of the item
	  $currentPosition = $_GET['currentPosition'];
		  
	  //Do not process if item does not exist
	  //Get item name by URL variable
	  $getitemID = $_GET['position'];
  
	  $itemCheckGrabber = mysql_query("SELECT * FROM sidebar WHERE position = {$getitemID}", $connDBA);
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
			$otherPostionReorderQuery = "UPDATE sidebar SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE sidebar SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that item when done.
			header ("Location: sidebar.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE sidebar SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE sidebar SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that item when done.
			header ("Location: sidebar.php");
			exit;
		}
	}

//Set item avaliability
	if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
		$id = $_POST['id'];
		if (!$_POST['option']) {
			$option = "";
		} else {
			$option = $_POST['option'];
		}
		
		$setAvaliability = "UPDATE sidebar SET `visible` = '{$option}' WHERE id = '{$id}'";
		mysql_query($setAvaliability, $connDBA);
		
		header ("Location: sidebar.php");
		exit;
	}
	
//Delete an item
	if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['item']) && isset($_GET['id'])) {
		//Do not process if item does not exist
		//Get item name by URL variable
		$getItemID = $_GET['item'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM sidebar WHERE position = {$getItemID}", $connDBA);
		$itemCheckArray = mysql_fetch_array($itemCheckGrabber);
		$itemCheckResult = $itemCheckArray['position'];
			 if (isset ($itemCheckResult)) {
				 $itemCheck = 1;
			 } else {
				$itemCheck = 0;
			 }
	 
		if (!isset ($_GET['id']) || $_GET['id'] == 0 || $itemCheck == 0) {
			header ("Location: sidebar.php");
			exit;
		} else {
			$deleteItem = $_GET['id'];
			$itemLift = $_GET['item'];
			
			$itemPositionGrabber = mysql_query("SELECT * FROM sidebar WHERE position = {$itemLift}", $connDBA);
			$itemPositionFetch = mysql_fetch_array($itemPositionGrabber);
			$itemPosition = $itemPositionFetch['position'];
			
			$otherItemsUpdateQuery = "UPDATE sidebar SET position = position-1 WHERE position > '{$itemPosition}'";
			$deleteItemQueryResult = mysql_query($otherItemsUpdateQuery, $connDBA);
			
			$deleteItemQuery = "DELETE FROM sidebar WHERE id = {$deleteItem}";
			$deleteItemQueryResult = mysql_query($deleteItemQuery, $connDBA);
			
			header ("Location: sidebar.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Sidebar Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Sidebar Control Panel</h2>
<p>This is the sidebar control panel, where you can add, edit, delete, and reorder boxes. These boxes will contain content which can be accessed on a given side of every page on the public website.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem new" href="manage_sidebar.php">Create New Box</a><a class="toolBarItem settings" href="sidebar_settings.php">Manage Sidebar Settings</a><a class="toolBarItem back" href="index.php">Back to Pages</a>
  <?php
	  if ($itemGrabber !== 0) {
	  echo "<a class=\"toolBarItem search\" href=\"../../index.php\">Preview this Site</a>";
	  }
?>
</div>
<?php 
	if (isset ($_GET['added']) && $_GET['added'] == "item") {successMessage("The box was successfully added");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "item") {successMessage("The box was successfully updated");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "settings") {successMessage("The sidebar settings were successfully updated.");}
	if (!isset ($_GET['updated']) && !isset ($_GET['added'])) {echo "<br />";}
?>
<?php
	//Table header, only displayed if items exist.
		if ($itemGrabber !== 0) {
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\" width=\"150\">Type</th><th class=\"tableHeader\">Content</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		//Loop through each item.
			while($itemData = mysql_fetch_array($itemGrabber)) {
				echo "<tr";
			//Alternate the color of each row.
				if ($itemData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				 echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"sidebar.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $itemData['id'] . "\" class=\"visible"; if ($itemData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $itemData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $itemData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($itemData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
				echo "<td width=\"75\"><form name=\"items\" action=\"sidebar.php\"><input type=\"hidden\" name=\"id\" value=\"" . $itemData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $itemData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
				
				$itemCount = mysql_num_rows($itemGrabber);
				for ($count=1; $count <= $itemCount; $count++) {
					echo "<option value=\"{$count}\"";
					if ($itemData ['position'] == $count) {
						echo " selected=\"selected\"";
					}
					echo ">" . $count . "</option>";
				}
				
				echo "</select></form></td>";
				echo "<td width=\"200\">" . htmlentities($itemData['title']) . "</td>";
				echo "<td width=\"150\">" . $itemData['type'] . "</td>";
				echo "<td>";
				
				if ($itemData['type'] == "Login") {
					echo "<span class=\"notAssigned\">None</span>";
				} else {
					echo commentTrim(70, $itemData['content']);
				}
				
				echo "</td>";
				echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_sidebar.php?id=" . $itemData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($itemData['title']) . "</strong> box')\" onmouseout=\"UnTip()\"></a></td>"; 
				echo "<td width=\"50\"><a class=\"action delete\" href=\"sidebar.php?action=delete&item=" . $itemData['position'] . "&id=" . $itemData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($itemData['title']) . "</strong> box')\" onmouseout=\"UnTip()\"></a></td>";
				}
			echo "</tr></tbody></table>";
		 } else {
		 	echo "<div class=\"noResults\">This site has no items. <a href=\"manage_item.php\">Create one now</a>.</div>";
		 } 
	?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>