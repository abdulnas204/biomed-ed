<?php
/*
LICENSE: See "license.php" located at the root installation

This is the plugin script for the learning addon to display on the user's portal.
*/

//Display the learning unit related data only if the user is currently enrolled in at least one unit
	if (is_array(arrayRevert($userData['learningunits'])) && sizeof(arrayRevert($userData['learningunits'])) > 0) {
	//Set globally used variables
		$pluginURL = $addon['pluginRoot'];
		$lessons = arrayRevert($userData['learningunits']);
		$count = 1;
		$return = "";
	
		echo "<div class=\"layoutControl\">\n";
		echo "<div class=\"halfLeft\">\n";
	
	/*
	Display a chart and table with the overall progress of the user
	---------------------------------------------------------
	*/		
		
	//Begin generating the table
		$return .= "<table class=\"dataTable\">\n";
		$return .= "<tr align=\"center\">\n";
		$return .= column("Learning Unit");
		$return .= column("Lesson Progress");
		$return .= column("Test Progress");
		$return .= "</tr>\n";
		
	//Display specific information regarding each unit
		foreach ($lessons as $key => $value) {
		//Grab the name of the learning unit
			$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$key}'");
			
		//Generate the inline style of the icon resembling the user's overall progress
			if ($value['lessonStatus'] == "F") {
				$lessonStatus = "checkmark.png";
			} elseif ($value['lessonStatus'] == "C") {
				$lessonStatus = "x.png";
			} else {
				$lessonStatus = "percent.png";
			}
			
			if ($value['testStatus'] == "F") {
				$testStatus = "checkmark.png";
			} elseif ($value['testStatus'] == "C") {
				$testStatus = "x.png";
			} else {
				$testStatus = "percent.png";
			}
			
			$return .= "<tr align=\"center\"";
			if ($count & 1) {$return .= " class=\"odd\">\n";} else {$return .= " class=\"even\">\n";}
			
			$return .= cell(URL($unitData['name'], "../learn/lesson.php?id=" . $unitData['id']));
			$return .= cell("<span class=\"action\" style=\"background:url(../" . $pluginURL . "/system/images/common/" . $lessonStatus . ") center center no-repeat\"></span>");
			
		//Keep in mind this unit may not have a test!!!
			if (exist("test_" . $key) || exist("testdata_" . $userData, "testID", $key)) {
				$return .= cell("<span class=\"action\" style=\"background:url(../" . $pluginURL . "/system/images/common/" . $testStatus . ") center center no-repeat\"></span>");
			} else {
				$return .= cell("<span class=\"notAssigned\">None</span>");
			}
			
			$return .= "</tr>\n";
			
			$count++;
		}
		
		$return .= "</table>\n";
		
		echo "<div class=\"portlet\">\n";
		echo "<div class=\"portlet-header\">Lesson-plan progres</div>\n";
		echo "<div class=\"portlet-content\">\n";
		echo "<p align=\"center\">" . chart("../" . $addon['pluginRoot'] . "system/flash/bar2D.swf", "addons/" . $addon['pluginRoot'] . "data.htm?type=account", "500", "240") . "</p>\n";
		echo $return;
		echo "</div>\n";
		echo "</div>\n";
		
		echo "</div>\n<div class=\"halfRight\">\n";
		
	/*
	Display a calendar with the lesson plan of the user
	---------------------------------------------------------
	*/
	
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
		
		echo "<script type=\"text/javascript\" src=\"../" . $addon['pluginRoot'] . "system/javascripts/jQuery_Sparkle.js\"></script>\n";
		echo "<script type=\"text/javascript\" src=\"../" . $addon['pluginRoot'] . "system/javascripts/mini_calendar_config.js\"></script>\n";
		echo "<div class=\"portlet\">\n";
		echo "<div class=\"portlet-header\">Lesson-plan progres</div>\n";
		echo "<div class=\"portlet-content\">\n";
		echo "<div align=\"center\">\n";
		echo "<div id=\"eventCal\"></div>\n";
		echo "</div>\n";
		echo "<div id=\"eventsInfo\">\n" . $return . "\n</div>\n";
		echo "</div>\n";
		echo "</div>\n";

		echo "</div>\n";
		echo "</div>\n";
	} else {
		if (access("Purchase Learning Units")) {
			echo "<div class=\"noResults\">You are not currently assigned to any learning units. " . URL("You may assign yourself to one or more learning units, but purchasing some now", "../learn/index.php") . ".</div>\n";
		}
	}
?>