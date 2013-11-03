<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: November 28th, 2010
Last updated: Novemeber 28th, 2010

This is the plugin script for the learning plugin to 
display on the user's portal.
*/

//Categorize the contents by its plugin
	echo "<p class=\"homeDivider\">" . $name . "</p>\n";

//Display a chart with the overall progress of the user
	echo "<p class=\"directions\">This chart displays your current progress on your lesson-plan:</p>\n";
	chart("../learn/system/flash/bar2D.swf", "../learn/system/php/data.php?type=account");
	
//Display a calendar with the lesson plan of the user
	echo "<!-- Begin FullCalendar widget //-->\n";
	echo fullCalendar();
	echo "\n<script type=\"text/javascript\">\n\$(document).ready(function() { \n\$(\"#calendar\").fullCalendar({\ntheme: true, events: \"../learn/system/php/data.php?type=calendar\"});\n});\n</script>\n";
	echo "<p>&nbsp;</p>";
	echo "<p class=\"directions\">This calendar displays your lesson-plan:</p>\n";
	echo "<div align=\"center\"><div id=\"calendar\"></div></div>\n";
	echo "<!-- End FullCalendar widget //-->\n";
	
//Display progress in individual modules	
	$lessons = unserialize($userData['modules']);
	
	if (is_array(unserialize($lessons)) && !empty($lessons)) {
		echo "<p class=\"directions\">Information on modules you are currently enrolled:</p>\n";
		echo "<ul>\n";
		
		foreach ($lessons as $key => $value) {
			$moduleData = query("SELECT * FROM `moduledata` WHERE `id` = '{$key}'");
			
			echo "<li class=\"";
			
			if ($value['moduleStatus'] == "F" && $value['testStatus'] == "F") {
				echo "completed";
			} elseif ($value['moduleStatus'] == "C" && $value['testStatus'] == "C") {
				echo "notStarted";
			} else {
				echo "inProgress";
			}
			
			if ($value['moduleStatus'] == "F") {
				$moduleStatus = "Completed";
			} elseif ($value['moduleStatus'] == "C") {
				$moduleStatus = "Not Started";
			} else {
				$moduleStatus = "In Progress";
			}
			
			if ($value['testStatus'] == "F") {
				$testStatus = "Completed";
			} elseif ($value['testStatus'] == "C") {
				$testStatus = "Not Started";
			} else {
				$testStatus = "In Progress";
			}
			
			echo "\">" . tip("<strong>Lesson Progress</strong> - " . $moduleStatus . "<br /><strong>Test Progress</strong> - " . $testStatus,  URL($moduleData['name'], "../modules/lesson.php?id=" . $moduleData['id']));
			echo "</li>";
		}
		
		echo "</ul>";
	}
?>