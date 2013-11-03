<?php require_once('../../system/connections/connDBA.php'); ?>
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
//Set feedback requirements
	if (isset($_POST['id']) && $_POST['action'] == "setRequirements") {
		$id = $_POST['id'];
		if (!$_POST['option']) {
			$option = "0";
		} else {
			$option = "1";
		}
		
		$setAvaliability = "UPDATE moduledata SET `feedback` = '{$option}' WHERE id = '{$id}'";
		mysql_query($setAvaliability, $connDBA);
		
		header ("Location: settings.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Feedback Settings"); ?>
<?php headers(); ?>
<?php liveSubmit(); ?>
<?php customCheckbox("checkbox"); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Feedback Settings</h2>
<p>Set which modules require a user to respond to the feedback questions. These settings are also customizable in the Module Setup Wizard.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem back" href="index.php">Back to Feedback</a><a class="toolBarItem home" href="../index.php">Back to Modules</a>
         </form>
</div><br /><br />
<?php
	  if ($modules == "exist") {
		  echo "<div class=\"catDivider one\">Set Feedback Options</div><div class=\"stepContent\"><blockquote>";
			  echo "<table>";
			  echo "<tbody>";
			  
			  //Select data for the loop
				  $moduleDataGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position ASC", $connDBA);
				  
			  //Loop through the items		
				  while ($moduleData = mysql_fetch_array($moduleDataGrabber)){
					  echo "<tr>";
						  echo "<td width=\"25\"><div align=\"center\">" . "<form name=\"avaliability\" action=\"settings.php\" method=\"post\"><input type=\"hidden\" name=\"action\" value=\"setRequirements\"><a href=\"#option" . $moduleData['id'] ."\" class=\"checked "; if ($moduleData['feedback'] == "0") {echo " unchecked ";} echo "\"></a><input type=\"hidden\" name=\"id\" value=\"" . $moduleData['id'] . "\"><div class=\"contentHide\"><input type=\"checkbox\" name=\"option\" id=\"option" . $moduleData['id'] . "\" onclick=\"Spry.Utils.submitForm(this.form);\""; if ($moduleData['feedback'] == "1") {echo " checked=\"checked\"";} echo "></div></form>" . "</div></td>";
						  echo "<td><div align=\"left\">" . $moduleData['name'] . "</div></td>";
					  echo "</tr>";
				  }
			  echo "</tbody>";
		  echo "</table></blockquote></div><div class=\"catDivider two\">Submit</div><div class=\"stepContent\"><blockquote><input name=\"submit\" type=\"button\" id=\"submit\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Submit\" /><input name=\"cancel\" type=\"button\" id=\"cancel\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Cancel\" /></blockquote></div>";
	  } else {
		  echo "<div class=\"noResults\">There are no modules to configure.</div>";
	  }
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>