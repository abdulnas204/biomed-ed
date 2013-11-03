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
Last updated: December 23rd, 2010

This is the plugin script for the learning plugin to 
display on the user's portal.
*/

	if (is_array(unserialize($userData['learningunits'])) && sizeof(unserialize($userData['learningunits'])) > 0) {
	//Categorize the contents by its plugin
		echo "<p class=\"homeDivider\">" . $name . "</p>\n";
		echo "<div class=\"layoutControl\">\n";
		echo "<div class=\"halfLeft\">\n";
	
	//Display a chart with the overall progress of the user
		$lessons = unserialize($userData['learningunits']);
		
		if (is_array($lessons) && !empty($lessons)) {
			$return = "<ul>\n";
			
			foreach ($lessons as $key => $value) {
				$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$key}'");
				
				$return .= "<li class=\"";
				
				if ($value['lessonStatus'] == "F" && $value['testStatus'] == "F") {
					$return .= "completed";
				} elseif ($value['lessonStatus'] == "C" && $value['testStatus'] == "C") {
					$return .= "notStarted";
				} else {
					$return .= "inProgress";
				}
				
				if ($value['lessonStatus'] == "F") {
					$lessonStatus = "Completed";
				} elseif ($value['lessonStatus'] == "C") {
					$lessonStatus = "Not Started";
				} else {
					$lessonStatus = "In Progress";
				}
				
				if ($value['testStatus'] == "F") {
					$testStatus = "Completed";
				} elseif ($value['testStatus'] == "C") {
					$testStatus = "Not Started";
				} else {
					$testStatus = "In Progress";
				}
				
				$return .= "\">" . tip("<strong>Lesson Progress</strong> - " . $lessonStatus . "<br /><strong>Test Progress</strong> - " . $testStatus,  URL($unitData['name'], "../learn/lesson.php?id=" . $unitData['id']));
				$return .= "</li>\n";
			}
			
			$return .= "</ul>\n";
		}
		
		sideBox("Lesson-plan progress", "Custom Content", "<p align=\"center\">" . chart("../learn/system/flash/bar2D.swf", "../learn/system/php/data.htm?type=account", "500", "240") . "</p>" . $return);
		
		echo "</div>\n<div class=\"halfRight\">\n";
		
	//Display a calendar with the lesson plan of the user
		$return = "";
		
		foreach(unserialize($userData['learningunits']) as $event) {
			$detailsGrabber = query("SELECT * FROM `learningunits` WHERE `id` = '{$event['item']}'");
			$time = strip($detailsGrabber['timeFrame'], "numbersOnly");
			$timeLabel = strip($detailsGrabber['timeFrame'], "lettersOnly");
			$start = $event['startDate'];
			$end = strtotime(date("Y-m-d", $event['startDate']) . " +" . $time . $timeLabel);
			
			if ($start < strtotime("now") && strtotime("now") < $end) {
				$return .= "<p><strong>" . URL($detailsGrabber['name'], "../learn/lesson.htm?id=" . $detailsGrabber['id']) . "</strong><br />\n";
				$return .= "Start: <em>" . date("l, F d, Y", $start) . "</em><br />\n";
				$return .= "End: <em>" . date("l, F d, Y", $end) . "</em><br />\n</p>\n";
			}
		}
		
		if (!empty($return)) {
			$return = $return;
		} else {
			$return = "<em>There is nothing on today's agenda!</em>";
		}
		
		echo eventCalendar();
		sideBox("Lesson Plan Calendar", "Custom Content", "
<div class=\"layoutControl\">
<div class=\"halfLeft\">
<div align=\"center\">
<div id=\"eventCal\"></div>
</div>
</div>
<div class=\"halfRight\">
<div id=\"eventsInfo\">\n" . $return . "\n</div>
</div>
</div>");
	
		echo "</div>\n</div>\n";
	}
?>