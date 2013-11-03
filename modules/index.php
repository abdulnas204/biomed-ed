<?php require_once('../Connections/connDBA.php'); ?>
<?php
//Grab all module data
	$moduleDataCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE avaliable = 'on' ORDER BY position ASC", $connDBA);
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
<?php title("Modules"); ?>
<?php headers(); ?>
<?php meta(); ?>
<script src="../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<?php
//Display the directions and tool bar for those with editing capabilities
	  if (isset($_SESSION['MM_UserGroup']) && $_SESSION['MM_UserGroup'] == "Site Administrator") {
		  echo "<h2>Modules</h2><p>Below is the list of modules avaliable to organizations.</p><p>&nbsp;</p><div class=\"toolBar\"><a class=\"toolBarItem back\"href=\"../site_administrator/modules/index.php\">Back to Module Administration</a></div><br />";
	  }
	  
//Display all modules			  
	  if ($modules == "exist") {
		  echo "<table class=\"dataTable\">";
			  echo "<tbody>";
				  echo "<tr>";
					  echo "<th width=\"200\" class=\"tableHeader\">Module Name</th>";
					  echo "<th width=\"100\" class=\"tableHeader\">Difficulty</th>";
					  echo "<th class=\"tableHeader\">Directions</th>";
				  echo "</tr>";
			  //Select data for the loop
				  $moduleDataGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `avaliable` = 'on' ORDER BY `position` ASC", $connDBA);
				  
				  while ($moduleData = mysql_fetch_array($moduleDataGrabber)){
					  echo "<tr";
					  if ($moduleData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					  ">";									
						  echo "<td width=\"200\"><a href=\"lesson.php?id=" . $moduleData['id'] . "\" onmouseover=\"Tip('Launch the <strong> " . $moduleData['name'] . " </strong> module')\" onmouseout=\"UnTip()\">" . $moduleData['name'] . "</a></td>";
						  
						  echo "<td width=\"100\">" . $moduleData['difficulty'] . "</td>";
						  
						  echo "<td>";
							  if ($moduleData['comments'] == "") {
								  echo "<i>None</i>";
							  } else {
								  echo commentTrim(85, $moduleData['comments']);
							  }
						  echo "</td>";
					  echo "</tr>";
				  }
			  echo "</tbody>";
		  echo "</table>";
	  } else {
			echo "<div class=\"noResults\">There are no modules currently avaliable.</div>";
	  }
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>