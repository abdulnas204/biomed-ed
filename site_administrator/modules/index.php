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
		chmod("../../modules/" . $directoryName, 0777);
		$directory = "../../modules/" . $directoryName;
		$lesson = $directory . "/lesson";

		while ($directory = readdir(opendir($lesson))) {
			if ($directory !== "." && $directory !== "..") {
				$deleteLocation = $lesson . "/" . $directory;
				unlink($deleteLocation);
			}
		}
		
		rmdir ($lessonDirectory);
		
		if (file_exists($directory . "/test")) {
			$fileResponseAnswer = $directory . "/test/fileresponse/answer";
			$fileResponseResponses = $directory . "/test/fileresponse/responses";
		
			while ($answers = readdir(opendir($fileResponseAnswer))) {
				if ($answers !== "." && $answers !== "..") {
					$answersLocation = $fileResponseAnswer . "/" . $directory;
					unlink($deleteLocation);
				}
			}
			
			while ($responses = readdir(opendir($fileResponseResponses))) {
				if ($responses !== "." && $lessons !== "..") {
					$responsesLocation = $fileResponseResponses . "/" . $directory;
					unlink($deleteLocation);
				}
			}
			
			rmdir ("../../modules/" . $directoryName . "/test/fileresponse/answer");
			rmdir ("../../modules/" . $directoryName . "/test/fileresponse/responses");
			rmdir ("../../modules/" . $directoryName . "/test/fileresponse");
			rmdir ("../../modules/" . $directoryName . "/test/fileresponse");
		}

		rmdir ($directoryName);
		
		header ("Location: index.php");
		exit;
	}
?>
<?php
//Unset old sessions
	unset($_SESSION['currentModule']);
	unset($_SESSION['review']);
	unset($_SESSION['step']);
	unset($_SESSION['testSettings']);
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
    <p>Modifing the table below will chage the default settings and appearance for instructors.</p>
    <p>&nbsp;</p>
      <div class="toolBar"><a href="module_wizard/index.php"><img src="../../images/admin_icons/new.png" alt="Add" width="24" height="24" /></a> <a href="module_wizard/index.php">Add New Module</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="question_bank/index.php"><img src="../../images/admin_icons/bank.png" alt="Bank" width="18" height="23" /></a> <a href="question_bank/index.php">Create Question Bank</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="settings.php"><img src="../../images/admin_icons/settings.png" alt="Settings" width="24" height="24" /></a> <a href="settings.php">Customize Module Settings</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="feedback/index.php"><img src="../../images/admin_icons/feedback.png" alt="Feedback" width="24" height="24" /></a> <a href="feedback/index.php">Feedback</a>
        <?php
			if ($modules == "exist") {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"../../modules/index.php\"><img src=\"../../images/admin_icons/search.png\" alt=\"Search\" width=\"24\" height=\"24\" /></a> <a href=\"../../modules/index.php\">Preview the Modules</a>";
			}
		?>
      </div>
<br /></br />
<?php
	  		if ($modules == "exist") {
				echo "<div align=\"center\">";
					echo "<table align=\"center\" class=\"dataTable\">";
					echo "<tbody>";
						echo "<tr>";
							echo "<th width=\"25\" class=\"tableHeader\"></th>";
							echo "<th width=\"50\" class=\"tableHeader\"><strong>Order</strong></th>";
							echo "<th width=\"200\" class=\"tableHeader\"><strong>Module Name</strong></th>";
							echo "<th class=\"tableHeader\"><strong>Comments</strong></th>";
							echo "<th width=\"50\" class=\"tableHeader\"><strong>Edit</strong></th>";
							echo "<th width=\"50\" class=\"tableHeader\"><strong>Delete</strong></th>";
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
								echo "<td width=\"25\"><div align=\"center\">" . "<form name=\"avaliability\" action=\"index.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setAvaliability\"><a href=\"#option" . $moduleData['id'] ."\" class=\"visible"; if ($moduleData['avaliable'] == "") {echo " hidden";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $moduleData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $moduleData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($moduleData['avaliable'] == "on") {echo " checked=\"checked\"";} echo "></div></form>" . "</div></td>";
							
								echo "<td width=\"50\"><form name=\"modules\" action=\"index.php\"><div align=\"center\">";
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
									echo "</div>";
									echo "<input type=\"hidden\" name=\"action\" value=\"reorder\">";
									echo "<input type=\"hidden\" name=\"id\" value=\"";
									echo $moduleData['id'];
									echo "\">";
									echo "<input type=\"hidden\" name=\"currentPosition\" value=\"";
									echo $moduleData['position'];
									echo "\"></form></td>";
								
								echo "<td width=\"200\"><div align=\"center\"><a href=\"../../modules/index.php?id=" . $moduleData['id'] . "\" onmouseover=\"Tip('Launch the <strong> " . $moduleData['name'] . " </strong>module')\" onmouseout=\"UnTip()\">" . $moduleData['name'] . "</a></div></td>";
								
								echo "<td align=\"center\"><div align=\"center\">" . commentTrim(60, $moduleData['comments']) . "</div></td>";
								echo "<td width=\"50\"><div align=\"center\">" . "<a href=\"index.php?action=edit&module=" . $moduleData['position'] . "&id=" . $moduleData['id'] . "\">" . "<img src=\"../../images/admin_icons/edit.png\" alt=\"Edit\" onmouseover=\"Tip('Edit the <strong> " . $moduleData['name'] . "</strong> module')\" onmouseout=\"UnTip()\">" . "</a>" . "</div></td>";
								echo "<td width=\"50\"><div align=\"center\">" . "<a href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&module=" . $moduleData['position'] . "&id=" . $moduleData['id'] . "', 'module');\">" . "<img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" onmouseover=\"Tip('Delete the <strong> " . $moduleData['name'] . "</strong> module')\" onmouseout=\"UnTip()\">" . "</a></div></td>";
							echo "</tr>";
						}
					echo "</tbody>";
				echo "</table></div>";
			} else {
				echo "<br /></br /><div align=\"center\">There are no modules. <a href=\"module_wizard/index.php\">Create one now</a>.</div><br /></br /><br /></br /><br /></br />";
			}
	  ?>
      <p>&nbsp;</p>
<p align="left"></p>    
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>