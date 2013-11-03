<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Organization Administrator"); ?>
<?php
//Grab all module data
	$moduleDataCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE `avaliable` = '1' ORDER BY position ASC", $connDBA);
	$moduleDataCheck = mysql_fetch_array($moduleDataCheckGrabber);
	
//Check to see if any modules exist
	$moduleCheck = $moduleDataCheck['id'];
	if (!$moduleCheck) {
		$modules = "empty";
	} else {
		$modules = "exist";
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Administration"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("organization_administrator/includes/top_menu.php"); ?>
      
    <h2>Module Administration</h2>
    <p>Select which training modules will be avaliable for instructors to assign to students, by checking the check-box next to the desired modules.</p>
    <p>&nbsp;</p>
    <?php
	//Display the toolbar if any modules exist
		if ($modules == "exist") {
      		echo "<div class=\"toolBar\"><a href=\"../../modules/index.php\"><img src=\"../../images/admin_icons/search.png\" alt=\"Search\" width=\"24\" height=\"24\" border=\"0\" /></a> <a href=\"../../modules/index.php\">Preview the Modules</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"report.php\"><img src=\"../../images/admin_icons/warning.png\" alt=\"Report\" width=\"24\" height=\"24\" border=\"0\" /></a> <a href=\"report.php\">Report Faulty Content</a></div>";
			}
		?>
<br /></br />
      <?php
	  		if ($modules == "exist") {
				echo "<div align=\"center\"><table align=\"center\" class=\"dataTable\"><tbody><tr><th width=\"100\" class=\"tableHeader\"><strong>Avaliable</strong></th><th width=\"100\" class=\"tableHeader\"><strong>Module Name</strong></th><th width=\"150\" class=\"tableHeader\"><strong>Category</strong></th><th width=\"200\" class=\"tableHeader\"><strong>Intended Employee Type</strong></th><th class=\"tableHeader\"><strong>Comments</strong></th></tr>";
			//Select data for the loop
				$moduleDataGrabber = mysql_query("SELECT * FROM moduledata WHERE `avaliable` = '1' ORDER BY position ASC", $connDBA);
				
			//A function to limit the length of the comments
				function commentTrim ($value) {
				   $comments1 = str_replace("<p>", "", $value);
				   $comments = str_replace("</p>", "", $comments1);
				   $maxLength = 150;
				   $countValue = html_entity_decode($comments);
				   if (strlen($countValue) <= $maxLength) {
					  return $comments;
				   }
				
				   $shortenedValue = substr($countValue, 0, $maxLength - 3) . "...";
				   return $shortenedValue;
				}
					
				$count = 1;	
				while ($moduleData = mysql_fetch_array($moduleDataGrabber)){
					echo "<tr";
					if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"100\"><div align=\"center\"><input type=\"checkbox\" name=\"avaliable[]\" id=\"avaliable[]\"></div></td><td width=\"100\"><div align=\"center\"><a href=\"../../modules/index.php?id=" . $moduleData['id'] . "\" onmouseover=\"Tip('Preview the <strong>" . $moduleData['name'] . "</strong> module')\" onmouseout=\"UnTip()\">" . $moduleData['name'] . "</a></div></td><td width=\"150\"><div align=\"center\">" . $moduleData['category'] . "</div></td><td width=\"200\"><div align=\"center\">" . $moduleData['employee'] . "</div></td><td><div align=\"center\">" . commentTrim($moduleData['comments']) . "</div></td>";
				}
				
				echo "</tbody></table></div>";
			} else {
				echo "<br /></br /><div align=\"center\">No modules are currently avaliable.</div><br /></br /><br /></br /><br /></br />";
			}
	  ?>
      <p>&nbsp;</p>
<p align="left"></p>    
<?php footer("organization_administrator/includes/bottom_menu.php"); ?>
</body>
</html>