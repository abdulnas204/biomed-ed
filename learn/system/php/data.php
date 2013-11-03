<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: Novemeber 28th, 2010
Last updated: December 23rd, 2010

This script is used for data processing for the portal 
plugin.
*/

//Header functions
	require_once('../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");

//Generate JSON data for calendar
	if (isset($_GET['type']) && $_GET['type'] == "calendar") {
		$calendarReturn = array();
		
		foreach(unserialize($userData['learningunits']) as $event) {
			$detailsGrabber = query("SELECT * FROM `learningunits` WHERE `id` = '{$event['item']}'");
			$time = strip($detailsGrabber['timeFrame'], "numbersOnly");
			$timeLabel = strip($detailsGrabber['timeFrame'], "lettersOnly");	
			
			$eventReturn = array("id" => $event['item'], "title" => $detailsGrabber['name'], "start" => date("M d, Y", $event['startDate']), "finish" => date("M d, Y", strtotime(date("Y-m-d", $event['startDate']) . " +" . $time . $timeLabel)));
			
			array_push($calendarReturn, $eventReturn);
		}
		
		echo json_encode(array("entries" => $calendarReturn));		
		exit;
	}

//Generate a chart with the overall data
	if (isset($_GET['type']) && $_GET['type'] == "account") {
		header("Content-type:text/xml");
		header("Cache-Control: cache, must-revalidate");
		header("Pragma: public");
		
		$units = unserialize($userData['learningunits']);
		$totalUnits = sprintf(count($units) * 4);
		
		echo "<graph caption=\"Overall Status of Completion\" subcaption=\"Total number of learning units: " . sizeof($units) . "\" yAxisMinValue=\"0\" yAxisMaxValue=\"100\" yAxisName=\"Percentage of Completion\" numberSuffix=\"%25\" showNames=\"0\" decimalPrecision=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\">\n";
		
		$completionPrep = 0;
		
		if (is_array($units)) {
			foreach($units as $key => $value) {
				if (is_array($value) && $value['lessonStatus'] == "F") {
					$completionPrep = $completionPrep + 2;
				} elseif (is_array($value) && $value['lessonStatus'] == "C") {
					$completionPrep = $completionPrep + 0;
				} elseif (is_array($value) && $value['lessonStatus'] == "O") {
					$completionPrep = $completionPrep + 1;
				} else {
					$completionPrep = $completionPrep + 0;
				}
				
				if (is_array($value) && $value['testStatus'] == "F") {
					$completionPrep = $completionPrep + 2;
				} elseif (is_array($value) && $value['testStatus'] == "C") {
					$completionPrep = $completionPrep + 0;
				} elseif (is_array($value) && $value['testStatus'] == "A") {
					$completionPrep = $completionPrep + 1;
				} elseif (is_array($value) && $value['testStatus'] == "O") {
					$completionPrep = $completionPrep + 1;
				} else {
					$completionPrep = $completionPrep + 0;
				}
			}
		}
		
		$completion = ($completionPrep/$totalUnits) * 100;
		
		if ($completion  > 0) {
			echo "<set name=\"Percentage Complete\" value=\"" . round($completion) . "\" />\n</graph>";
		} else {
			echo "<set />\n</graph>";
		}
		
		exit;
	}
?>