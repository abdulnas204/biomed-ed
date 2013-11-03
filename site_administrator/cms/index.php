<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Reorder pages
	//Check to see if pages exist
	$pageCheck = mysql_query("SELECT * FROM pages WHERE `position` = 1", $connDBA);
	if (mysql_fetch_array($pageCheck)) {
		$pageGrabber = mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position ASC", $connDBA);
	} else {
		$pageGrabber = 0;
	}
	
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

//Set page visibility
	if (isset ($_GET['visible']) && isset($_GET['id']) && isset($_GET['currentPosition']) && isset($_GET['visible'])) {
		$id = $_GET['id'];
		$page = $_GET['currentPosition'];
		$visible = $_GET['visible'];
				
		$editPageQuery = "UPDATE pages SET visible = {$visible} WHERE id = {$id}";
		$editSubMenuQuery = "UPDATE pages SET visible = {$visible} WHERE item = {$page}";
		
		$editPageQueryResult = mysql_query($editPageQuery, $connDBA);
		$editSubMenuQueryResult = mysql_query($editSubMenuQuery, $connDBA);
		
		if (isset ($_GET['redirect']) && $_GET['redirect'] == "home") {
			header ("Location: ../../index.php?page=" . $page);
			exit;
		} else {
			header ("Location: index.php");
			exit;
		}
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
			$deleteSubMenuQuery = "DELETE FROM pages WHERE item = {$pageLift}";
			$deletePageQueryResult = mysql_query($deletePageQuery, $connDBA);
			$deleteSubMenuQueryResult = mysql_query($deleteSubMenuQuery, $connDBA);
			
			$subMenuUpdateQuery = "UPDATE pages SET item = item-1 WHERE item > '{$pagePosition}'";
			$subMenuQueryResult = mysql_query($subMenuUpdateQuery, $connDBA);
			
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
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
    <h2>Pages Control Panel</h2>
	<div align="left"><p>This is the pages control panel, where you can add, edit, delete, and reorder pages.</p>
</div>
<?php 
	if (isset ($_GET['added']) && $_GET['added'] == "page") {successMessage("The page was successfully added");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "page") {successMessage("The page was successfully updated");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "logo") {successMessage("The logo was successfully updated. It may take a few moments to update across the system.");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "icon") {successMessage("The browser icon was successfully updated. It may take a few moments to update across the system.");}
    if (isset ($_GET['updated']) && $_GET['updated'] == "siteInfo") {successMessage("The site information was successfully updated");}
	if (isset ($_GET['updated']) && $_GET['updated'] == "theme") {successMessage("The theme was successfully updated");}
	if (!isset ($_GET['updated']) && !isset ($_GET['added'])) {echo "<p>&nbsp;</p>";}
?>
	<div class="toolBar"><a href="manage_page.php"><img src="../../images/admin_icons/new.png" alt="Add" width="24" height="24" border="0" /></a> <a href="manage_page.php">Create New Page</a><span style="padding:3px">
      <?php
			if ($pageGrabber !== 0) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"../../index.php\"><img src=\"../../images/admin_icons/search.png\" alt=\"Preview\" width=\"24\" height=\"24\" border=\"0\" /></a> <a href=\"../../index.php\">Preview this Site</a>";
			}
	  ?>
</span></div><br />
<div class="layoutControl">    
<div class="dataLeft">
<div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Site Settings</h2>
        </div>
      </div>
        <div class="content">
          <p>Modify  settings within this site:</p>
          <ul>
            <li class="arrowBullet"><a href="http://localhost/biomed-ed/site_administrator/cms/site_settings.php?type=logo">Site   Logo</a></li>
            <li class="arrowBullet"><a href="http://localhost/biomed-ed/site_administrator/cms/site_settings.php?type=icon">Browser   Icon</a></li>
            <li class="arrowBullet"><a href="http://localhost/biomed-ed/site_administrator/cms/site_settings.php?type=meta">Site   Information</a></li>
            <li class="arrowBullet"><a href="http://localhost/biomed-ed/site_administrator/cms/site_settings.php?type=theme">Theme</a></li>
          </ul>
      </div>
    </div>
</div>
<div class="contentRight">
<div align="center">
<?php
	//Table header, only displayed if pages exist.
		if ($pageGrabber !== 0) {
		echo "<div align=\"center\"><table class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"><strong>Order</strong></th><th width=\"25\" class=\"tableHeader\"><strong>Visible</strong></th><th class=\"tableHeader\"><strong>Title</strong></th><th width=\"25\" class=\"tableHeader\"><strong>Edit</strong></th><th width=\"25\" class=\"tableHeader\"><strong>Delete</strong></th></tr>";
		//Loop through each page.
			while($pageData = mysql_fetch_array($pageGrabber)) {
				echo "<tr";
			//Alternate the color of each row.
				if ($pageData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<form name=\"pages\" action=\"index.php\"><input type=\"hidden\" name=\"id\" value=\"" . $pageData['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $pageData['position'] .  "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><td width=\"25\"><div align=\"center\"><select name=\"position\" onchange=\"this.form.submit();\">";
				
				$pageCount = mysql_num_rows($pageGrabber);
				for ($count=1; $count <= $pageCount; $count++) {
					echo "<option value=\"{$count}\"";
					if ($pageData ['position'] == $count) {
						echo " selected=\"selected\"";
					}
					echo ">" . $count . "</option>";
				}
				
				echo "</select></div></td><td width=\"25\"><div align=\"center\"><select name=\"visible\" onchange=\"this.form.submit();\"><option value=\"1\""; 
				if ($pageData['visible'] == 1) {echo " selected=\"selected\"";} 
				echo ">Yes</option><option value=\"0\""; 
				if ($pageData['visible'] == 0) {echo " selected=\"selected\"";} if ($pageData['position'] == 1) {echo " onclick=\"alert ('The page you are currently hiding is the site entry point. Hiding this page will not lock visitors out of the page, but will only hide it from the menu.');\"";}
				echo ">No</option></select></div></td><td width=\"200\"><div align=\"center\">";
				if ($pageData['position'] == "1") {
					echo "<img src=\"../../images/admin_icons/home.png\" alt=\"Home\" border=\"0\" onmouseover=\"Tip('Home page')\" onmouseout=\"UnTip()\">";
				}
				echo " <a href=\"../../index.php?page=" . $pageData['position'] . "\">" . $pageData['title'] . "</a></div></td><td width=\"25\"><div align=\"center\"><a href=\"manage_page.php?id=" . $pageData['id'] . "\"><img src=\"../../images/admin_icons/edit.png\" alt=\"Edit\" border=\"0\" onmouseover=\"Tip('Edit the <strong>" . $pageData['title'] . "</strong> page')\" onmouseout=\"UnTip()\"></a></div></td><td width=\"25\"><div align=\"center\">"; echo "<a href=\"index.php?action=delete&page=" . $pageData['position'] . "&id=" . $pageData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\"><img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" border=\"0\" onmouseover=\"Tip('Delete the <strong>" . $pageData['title'] . "</strong> page')\" onmouseout=\"UnTip()\"></a></div></td></form>";
				
				//List sub-menu items, currently not used
					$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageData['position']}", $connDBA);
					$subMenu = mysql_fetch_array($subMenuGrabber);
					if ($subMenu['item'] == $pageData['position']) {
						echo "</tr><tr><td colspan=\"5\"><div id=\"subMenu" .  $subMenuLoop['item'] . "\" class=\"CollapsiblePanel\"> <div class=\"CollapsiblePanelTab\" tabindex=\"0\">Expand Sub-Menu Items<img src=\"../../images/down.gif\"></div><div class=\"CollapsiblePanelContent\"><table width=\"100%\" class=\"dataTable\"><tbody><tr><th width=\"25\" class=\"tableHeader\"><strong>Parent</strong></th><th width=\"25\" class=\"tableHeader\"><strong>Order</strong></th><th width=\"25\" class=\"tableHeader\"><strong>Visible</strong></th><th class=\"tableHeader\"><strong>Title</strong></th><th width=\"25\" class=\"tableHeader\"><strong>Edit</strong></th><th width=\"25\" class=\"tableHeader\"><strong>Delete</strong></th></tr>";
						while($subMenu = mysql_fetch_array($subMenuCheck)) {
							if ($subMenu['item'] == $pageData['position']) {
								echo "<form name=\"subMenu\" action=\"index.php\"><tr";
								if ($subMenu['subPosition'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
								echo "<input type=\"hidden\" name=\"id\" value=\"" .  $subMenu['id'] . "\"><input type=\"hidden\" name=\"currentPosition\" value=\"" .  $subMenu['subPosition'] . "\"><input type=\"hidden\" name=\"action\" value=\"modifySettings\"><input type=\"hidden\" name=\"subMenu\" value=\"true\"><td width=\"100\"><div align=\"center\"><select name=\"parent\">";
								$menuGrabber = mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position ASC", $connDBA);

								while ($menu = mysql_fetch_array($menuGrabber)){
									echo "<option value=\"" .  $menu['position'] .  "\"";
									if ($subMenu['item'] == $menu['position']) {
										echo " selected=\"selected\"";
									}
									echo ">" .  $menu['title'] . "</option>";
								}
								
								echo "</select></div></td><td width=\"50\"><div align=\"center\"><select name=\"subPosition\" onchange=\"this.form.submit();\">";
								
								$subMenuNumberGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageData['position']} ORDER BY subPosition ASC", $connDBA);
								$subMenuNumber = mysql_num_rows($subMenuNumberGrabber);
								for ($count=1; $count <= $subMenuNumber; $count++) {
									echo "<option value=\"{$count}\"";
									if ($subMenu['subPosition'] == $count) {
										echo " selected=\"selected\"";
									}
									echo ">" . $count . "</option>";
								}
								
								echo "</select></div></td><td width=\"100\"><div align=\"center\"><select name=\"visible\" onchange=\"this.form.submit();\"><option value=\"1\""; 
								if ($subMenu['visible'] == 1) {echo " selected=\"selected\"";} echo ">Yes</option><option value=\"0\">No</option></select></div></td><td width=\"200\"><a href=\"../../index.php?page=" . $pageData['position'] . "&subMenu=" . $subMenu['subPosition'] . "\">" . $subMenu['title'] . "</a></td><td width=\"25\"><div align=\"center\"><a href=\"edit_page.php?subMenu=" . $subMenu['subPosition'] . "\"><img src=\"../../admin_icons/images/edit.png\" alt=\"Edit\" border=\"0\" onmouseover=\"Tip('Edit the <strong>" . $subMenu['title'] . "</strong> page.')\" onmouseout=\"UnTip()\"></a></div></td><td width=\"25\" align=\"center\"><div align=\"center\"><a href=\"index.php?action=delete&subMenu=" . $subMenu['subPosition'] . "&id=" . $subMenu['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\"><img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" border=\"0\" onmouseover=\"Tip('Delete the <strong>" . $subMenu['title'] . "</strong> page.')\" onmouseout=\"UnTip()\"></a></div></td>";
							}
						}
					echo "</div></div></tr></form></tbody></table></td>";
					}
				}
			echo "</tr></tbody></table></div>";
		 } else {
		 	echo "<br /><br /><div align=\"center\">This site has no pages. <a href=\"manage_page.php\">Create a new page now</a>.</div><br /><br />";
		 } 
	?>
</div>
</div>
</div>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>