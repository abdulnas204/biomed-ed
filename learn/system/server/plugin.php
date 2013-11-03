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
Last updated: February 14th, 2011

This is the plugin script for the learning plugin to 
display on the user's portal.
*/

	if (is_array(arrayRevert($userData['learningunits'])) && sizeof(arrayRevert($userData['learningunits'])) > 0) {
	//Categorize the contents by its plugin
		echo "<p class=\"homeDivider\">" . $name . "</p>\n";
		echo "<div class=\"layoutControl\">\n";
		echo "<div class=\"halfLeft\">\n";
	
	//Display a chart with the overall progress of the user
		$lessons = arrayRevert($userData['learningunits']);
		
		if (is_array($lessons) && !empty($lessons)) {
			$return = "<table class=\"dataTable\">\n";
			$return .= "<tr align=\"center\">\n";
			$return .= "<th>Learning Unit</th>\n";
			$return .= "<th>Lesson Progress</th>\n";
			$return .= "<th>Test Progress</th>\n";
			$return .= "</tr>\n";
			
			foreach ($lessons as $key => $value) {
				$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$key}'");
				
				if ($value['lessonStatus'] == "F") {
					$lessonStatus = "completed";
				} elseif ($value['lessonStatus'] == "C") {
					$lessonStatus = "notStarted";
				} else {
					$lessonStatus = "inProgress";
				}
				
				if ($value['testStatus'] == "F") {
					$testStatus = "completed";
				} elseif ($value['testStatus'] == "C") {
					$testStatus = "notStarted";
				} else {
					$testStatus = "inProgress";
				}
				
				$return .= "<tr align=\"center\">\n";
				$return .= "<td>" . URL($unitData['name'], "../learn/lesson.php?id=" . $unitData['id']) . "</td>\n";
				$return .= "<td><span class=\"action " . $lessonStatus . "\"></span></td>\n";
				
				if (exist("test_" . $key) || exist("testdata_" . $userData, "testID", $key)) {
					$return .= "<td><span class=\"action " . $testStatus . "\"></span></td>\n";
				} else {
					$return .= "<td><span class=\"notAssigned\">None</span></td>\n";
				}
				
				$return .= "</tr>\n";
			}
			
			$return .= "</table>\n";
		}
		
		sideBox("Lesson-plan progress", "Custom Content", "<p align=\"center\">" . chart("../learn/system/flash/bar2D.swf", "../learn/system/php/data.htm?type=account", "500", "240") . "</p>\n" . $return);
		
		echo "</div>\n<div class=\"halfRight\">\n";
		
	//Display a calendar with the lesson plan of the user
		$return = "";
		
		foreach(arrayRevert($userData['learningunits']) as $event) {
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
	} else {
		echo "<div class=\"noResults\">You are not currently assigned to any learning units. " . URL("You may assign yourself to one or more learning units, but purchasing some now", "../learn/index.php") . ".</div>\n";
	}
?>