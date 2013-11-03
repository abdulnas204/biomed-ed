<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Check to see if pages exist
	$pageCheck = mysql_query("SELECT * FROM pages WHERE `position` = 1", $connDBA);
	if (mysql_fetch_array($pageCheck)) {
		$pageGrabber = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);
	} else {
		$pageGrabber = 0;
	}

//Reorder pages	
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
	  $getPageID = $_GET['position'];
  
	  $pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE position = {$getPageID}", $connDBA);
	  $pageCheckArray = mysql_fetch_array($pageCheckGrabber);
	  $pageCheckResult = $pageCheckArray['position'];
		   if (isset ($pageCheckResult)) {
			   $pageCheck = 1;
		   } else {
			  $pageCheck = 0;
		   }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE pages SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE pages SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE pages SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE pages SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
		}
	}

//Set page avaliability
	if (isset($_POST['id']) && $_POST['action'] == "setAvaliability") {
		$id = $_POST['id'];
		
		if (!$_POST['option']) {
			$option = "";
		} else {
			$option = $_POST['option'];
		}
		
		$setAvaliability = "UPDATE pages SET `visible` = '{$option}' WHERE id = '{$id}'";
		mysql_query($setAvaliability, $connDBA);
		
		if (isset($_POST['redirect'])) {
			header ("Location:" . $root . "index.php?page=" . $id);
			exit;
		}
		header ("Location: index.php");
		exit;
	}
	
//Delete a page
	if (isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['page']) && isset($_GET['id'])) {
		//Do not process if page does not exist
		//Get page name by URL variable
		$getPageID = $_GET['page'];
	
		$pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE position = {$getPageID}", $connDBA);
		$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
		$pageCheckResult = $pageCheckArray['position'];
			 if (isset ($pageCheckResult)) {
				 $pageCheck = 1;
			 } else {
				$pageCheck = 0;
			 }
	 
		if (!isset ($_GET['id']) || $_GET['id'] == 0 || $pageCheck == 0) {
			header ("Location: index.php");
			exit;
		} else {
			$deletePage = $_GET['id'];
			$pageLift = $_GET['page'];
			
			$pagePositionGrabber = mysql_query("SELECT * FROM pages WHERE position = {$pageLift}", $connDBA);
			$pagePositionFetch = mysql_fetch_array($pagePositionGrabber);
			$pagePosition = $pagePositionFetch['position'];
			
			$otherPagesUpdateQuery = "UPDATE pages SET position = position-1 WHERE position > '{$pagePosition}'";
			$deletePageQueryResult = mysql_query($otherPagesUpdateQuery, $connDBA);
			
			$deletePageQuery = "DELETE FROM pages WHERE id = {$deletePage}";
			$deletePageQueryResult = mysql_query($deletePageQuery, $connDBA);
			
			header ("Location: index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Pages Control Panel"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("visible"); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Pages Control Panel</h2>
<p>This is the pages control panel, where you can add, edit, delete, and reorder pages.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem new" href="manage_page.php">Create New Page</a><a class="toolBarItem settings" href="site_settings.php">Manage Site Settings</a><a class="toolBarItem sideBar" href="sidebar.php">Manage Sidebar</a>
<?php
	  if ($pageGrabber !== 0) {
	  echo "<a class=\"toolBarItem search\" href=\"../../index.php\">Preview this Site</a>";
	  }
?></div>
<?php 
	if (isset ($_GET['added']) && $_GET['added'] == "page") {successMessage("The page was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "page") {successMessage("The page was successfully updated");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "logo") {successMessage("The logo was successfully updated. It may take a few moments to update across the system.");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "icon") {successMessage("The browser icon was successfully updated. It may take a few moments to update across the system.");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "siteInfo") {successMessage("The site information was successfully updated");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "theme") {successMessage("The theme was successfully updated");}
	if (!isset ($_GET['updated']) && !isset ($_GET['added'])) {echo "<br />";}
?>
<?php
//Table header, only displayed if pages exist.
	if ($pageGrabber !== 0) {
	echo "<table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"></th><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\" width=\"200\">Title</th><th class=\"tableHeader\">Content</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
	//Loop through each page.
		while($pageData = mysql_fetch_array($pageGrabber)) {
			echo "<tr";
		//Alternate the color of each row.
			if ($pageData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			 echo "<td width=\"25\"><div align=\"center\"><form name=\"avaliability\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $pageData['id'] . "\" class=\"visible"; if ($pageData['visible'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $pageData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $pageData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($pageData['visible'] == "on") {echo " checked=\"checked\"";} echo "></div></form></div></td>";
			echo "<td width=\"75\"><form name=\"pages\" action=\"index.php\"><input type=\"hidden\" name=\"id\" value=\"" . $pageData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $pageData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><select name=\"position\" onchange=\"this.form.submit();\">";
			
			$pageCount = mysql_num_rows($pageGrabber);
			for ($count=1; $count <= $pageCount; $count++) {
				echo "<option value=\"{$count}\"";
				if ($pageData ['position'] == $count) {
					echo " selected=\"selected\"";
				}
				echo ">" . $count . "</option>";
			}
			
			echo "</select></form></td><td width=\"200\"><a";
			if ($pageData['position'] == "1") {
				echo " class=\"homePage\"";
			}
			echo " href=\"../../index.php?page=" . $pageData['id'] . "\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\">" . stripslashes($pageData['title']) . "</a></td>";
			echo "<td>" . commentTrim(100, $pageData['content']) . "</td>";
			echo "<td width=\"50\"><a class=\"action edit\" href=\"manage_page.php?id=" . $pageData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\"></a></td>"; 
			echo "<td width=\"50\"><a class=\"action delete\" href=\"index.php?action=delete&page=" . $pageData['position'] . "&id=" . $pageData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . htmlentities($pageData['title']) . "</strong> page')\" onmouseout=\"UnTip()\"></a></td>";
			}
		echo "</tr></tbody></table>";
	 } else {
		echo "<div class=\"noResults\">This site has no pages. <a href=\"manage_page.php\">Create one now</a>.</div>";
	 } 
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>