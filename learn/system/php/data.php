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
Last updated: Novemeber 28th, 2010

This script contains additional functions relevent to this 
plugin only.
*/

//Header functions
	require_once('../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");

//Generate JSON data for calendar
	if (isset($_GET['type']) && $_GET['type'] == "calendar") {
		$calendarReturn = array();
		
		foreach(unserialize($data['modules']) as $event) {
			$detailsGrabber = query("SELECT * FROM `moduledata` WHERE `id` = '{$event['item']}'");
			$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
			$numberArray = array("0","1","2","3","4","5","6","7","8","9");
			$time = str_replace($letterArray, "", $detailsGrabber['timeFrame']);
			$timeLabel = str_replace($numberArray, "", $detailsGrabber['timeFrame']);	
			
			$eventReturn = array("id" => $event['item'], "title" => prepare($detailsGrabber['name'], false, true), "start" => date("Y-m-d", $event['startDate']), "end" => date("Y-m-d", strtotime(date("Y-m-d", $event['startDate']) . " +" . $time . $timeLabel)));
			
			array_push($calendarReturn, $eventReturn);
		}
		
		echo json_encode($calendarReturn);		
		exit;
	}

//Generate a chart with the overall data
	if (isset($_GET['type']) && $_GET['type'] == "account") {
		$statistics = userData();
		$modules = unserialize($statistics['modules']);
		$totalModules = sprintf(count($modules) * 4);
		
		echo "<graph caption=\"Overall Status of Completion\" subcaption=\"Total number of modules: " . sizeof($modules) . "\" yAxisMinValue=\"0\" yAxisMaxValue=\"100\" yAxisName=\"Percentage of Completion\" numberSuffix=\"%25\" showNames=\"0\" decimalPrecision=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\">";
		
		$completionPrep = 0;
		
		if (is_array($modules)) {
			foreach($modules as $key => $value) {
				if (is_array($value) && $value['moduleStatus'] == "F") {
					$completionPrep = $completionPrep + 2;
				} elseif (is_array($value) && $value['moduleStatus'] == "C") {
					$completionPrep = $completionPrep + 0;
				} elseif (is_array($value) && $value['moduleStatus'] == "O") {
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
		
		$completion = ($completionPrep/$totalModules) * 100;
		
		if ($completion  > 0) {
			echo "<set name=\"Percentage Complete\" value=\"" . round($completion) . "\" /></graph>";
		} else {
			echo "<set /></graph>";
		}
		
		exit;
	}
?>