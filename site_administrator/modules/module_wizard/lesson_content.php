<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			//case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php	
//Check to see if any lesson content data exists
	$currentTable = str_replace(" ","", $_SESSION['currentModule']);
	$lessonDataCheckGrabber = mysql_query ("SELECT * FROM modulelesson_{$currentTable}", $connDBA);
	
	if ($lessonDataCheck = mysql_fetch_array($lessonDataCheckGrabber)) {
		$lessonDataResult = $lessonDataCheck['id'];
	}
	
	if (isset($lessonDataResult)) {
		$lesson = "exist";
	} else {
		$lesson = "empty";
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
		//Assign the module name
		$currentModule = str_replace(" ","", $_SESSION['currentModule']);
			
	//Do not process if item does not exist
		//Get item name by URL variable
		$getItemID = $_GET['position'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM modulelesson_{$currentModule} WHERE position = {$getItemID}", $connDBA);
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
			$otherPostionReorderQuery = "UPDATE modulelesson_{$currentModule} SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE modulelesson_{$currentModule} SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: lesson_content.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE modulelesson_{$currentModule} SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE modulelesson_{$currentModule} SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: lesson_content.php");
			exit;
		}
	}
?>
<?php
//Delete a page
	if (isset ($_GET['id']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$currentModule= str_replace(" ","", $_SESSION['currentModule']);
		$deleteGrabber = mysql_query("SELECT * FROM modulelesson_{$currentModule} WHERE id = '{$id}'", $connDBA);
		$delete = mysql_fetch_array($deleteGrabber);
		if (!$delete) {
			header ("Location: lesson_content.php");
			exit;
		}

	//Update the database	
		$position = $delete['position'];
	
		mysql_query("DELETE FROM modulelesson_{$currentModule} WHERE id = '{$id}' LIMIT 1", $connDBA);
		mysql_query("UPDATE modulelesson_{$currentModule} SET position = position-1 WHERE position > '{$position}'", $connDBA);
		
	//Delete a file, if it is an embedded content page
		if ($delete['attachment'] !== "") {
			$file = "../../../modules/" . $currentModule . "/lesson/" . $delete['attachment'];
			unlink($file);
			
			header ("Location: lesson_content.php?deleted=embedded");
			exit;
		} else {
			header ("Location: lesson_content.php?deleted=custom");
			exit;
		}
	}
?>
<?php
//Update a session to go to different steps
	if (isset ($_POST['submit'])) {
		header ("Location: modify.php?updated=lessonContent");
		exit;
	}

	if (isset ($_POST['back'])) {
		$_SESSION['step'] = "lessonSettings";
		header ("Location: lesson_settings.php");
		exit;
	}
	
	if (isset ($_POST['next'])) {
		$_SESSION['step'] = "lessonVerify";
		header ("Location: lesson_verify.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Module Content"); ?>
<?php headers(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Module Setup Wizard : Module Content</h2>
<p>All of the content for this lesson will be managed from this page.  A <strong>custom content page</strong> is just like a regular web page,   with text and images. An <strong>embedded media page</strong> will   contain something, such as a video or PDF, as the main content.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem custom" href="manage_content.php?type=custom">Add Custom Content</a><a class="toolBarItem embedded" href="manage_content.php?type=embedded">Add Embedded Content</a></div>
<?php
//If an updated alert is shown
  if (isset ($_GET['updated'])) {
	  $message = "The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['updated']) {
		  case "custom" : $message .= "custom content"; break;
		  case "embedded" : $message .= "embedded content"; break;
	  }
	  $message .= "</strong> page was successfully updated<br />";
	  
	  successMessage($message);
  } elseif (isset ($_GET['inserted'])) {
	  $message = "The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['inserted']) {
		  case "custom" : $message .= "custom content"; break;
		  case "embedded" : $message .= "embedded content"; break;
	  }
	  $message .= "</strong> page was successfully inserted<br />";
	  
	  successMessage($message);
  } elseif (isset ($_GET['deleted'])) {
	  $message = "The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['deleted']) {
		  case "custom" : $message .= "custom content"; break;
		  case "embedded" : $message .= "embedded content"; break;
	  }
	  $message .= "</strong> page was successfully deleted<br />";
	  
	  successMessage($message);
  } else {
	  echo "&nbsp;";
  }
?>
<?php
	  if ($lesson == "exist") {
		  echo "<table align=\"center\" class=\"dataTable\">";
			  echo "<tbody>";
				  echo "<tr>";
					  echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
					  echo "<th width=\"150\" class=\"tableHeader\">Type</th>";
					  echo "<th width=\"250\" class=\"tableHeader\">Title</th>";
					  echo "<th class=\"tableHeader\">Content or Comments</th>";
					  echo "<th width=\"75\" class=\"tableHeader\">Edit</th>";
					  echo "<th width=\"75\" class=\"tableHeader\">Delete</th>";
				  echo "</tr>";
			  //Select the module name, to fill in all test data
				  $currentModule = $_SESSION['currentModule'];
				  $currentTable = str_replace(" ","", $currentModule);
			  
				  $lessonDataGrabber = mysql_query ("SELECT * FROM modulelesson_{$currentTable} ORDER BY position ASC", $connDBA);	
				  
			  //Select data for drop down menu
				  $dropDownDataGrabber = mysql_query("SELECT * FROM modulelesson_{$currentTable} ORDER BY position ASC", $connDBA);
				  
				  while ($lessonData = mysql_fetch_array($lessonDataGrabber)){
					  echo "<tr";
					  if ($lessonData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					  ">";
						  echo "<form action=\"lesson_content.php\">";
						  echo "<input type=\"hidden\" name=\"currentPosition\" value=\"" . $lessonData['position'] . "\" />";
						  echo "<input type=\"hidden\" name=\"id\" value=\"" . $lessonData['id'] . "\" />";
						  echo "<input type=\"hidden\" name=\"action\" value=\"reorder\" />";
						  echo "<td width=\"75\"><div align=\"center\">";
								  echo "<select name=\"position\" onchange=\"this.form.submit();\">";
								  $lessonCount = mysql_num_rows($dropDownDataGrabber);
								  for ($count=1; $count <= $lessonCount; $count++) {
									  echo "<option value=\"{$count}\"";
									  if ($lessonData ['position'] == $count) {
										  echo " selected=\"selected\"";
									  }
									  echo ">$count</option>";
								  }
								  echo "</select>";
							  echo "</div></td>";
						  echo "<td width=\"150\">" . $lessonData['type'] . "</td>";
						  echo "<td width=\"250\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('preview_page.php?page=" . $lessonData['position'] . "','','status=yes,scrollbars=yes,resizable=yes,width=800,height=600')\" onmouseover=\"Tip('Preview the <strong>" . htmlentities($lessonData['title']) . "</strong> page')\" onmouseout=\"UnTip()\">" . stripslashes($lessonData['title']);
						  echo "</a></td>";
						  echo "<td>" ;
						  if ($lessonData['type'] == "Custom Content") {
							  echo commentTrim(55, $lessonData['content']);
						  } else {
							  echo commentTrim(55, $lessonData['comments']);
						  }
						  echo "</td>";
						  echo "<td width=\"75\"><a class=\"action edit\" href=\"";
						  switch ($lessonData['type']) {
							  case "Custom Content" : echo "manage_content.php?type=custom"; break;
							  case "Embedded Content" : echo "manage_content.php?type=embedded"; break;
						  }
						  echo "&id=" .  $lessonData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . htmlentities($lessonData['title']) . "</strong> page')\" onmouseout=\"UnTip()\">&nbsp;</a></td>";
						  echo "<td width=\"75\"><a class=\"action delete\" href=\"lesson_content.php?id=" .  $lessonData['id'] . "&action=delete\" onclick=\"return confirm ('This action cannot be undone. Continue?');\">&nbsp;</a></td>";
					  echo "</form>";
					  echo "</tr>";
				  }
			  echo "</tbody>";
		  echo "</table>";
		  echo "<br />";
	  } else {
		  echo "<br /></br /><br /></br /><div align=\"center\">There are no pages in this lesson. <a href=\"manage_content.php\">Create a new page now</a>.</div><br /></br /><br /></br /><br /></br />";
	  }
?>
<blockquote>
  <form id="navigate" name="navigate" method="post" action="lesson_content.php">
    <?php
      //Selectively display the buttons
            if (isset ($_SESSION['review'])) {
                submit("submit", "Modify Content");
                echo "<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" onclick=\"MM_goToURL('parent','modify.php');return document.MM_returnValue\" />";
            } else {
				submit("back", "&lt;&lt; Previous Step");
				if ($lesson !== "empty") {
					submit("next", "Next Step &gt;&gt;");
				}
            }
      ?>
  </form>
</blockquote>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>