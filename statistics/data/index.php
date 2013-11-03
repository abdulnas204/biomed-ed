<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Export as XML file
	header("Content-type: application/xml");
	
	if (isset($_GET['type'])) {
		switch ($_GET['type']) {
			case "overall" : 
				headers("Statistics Data Collection", "Organization Administrator,Site Administrator", false, false, false, false, false, false, false, "XML");
				$statisticsCheck = mysql_query("SELECT * FROM `overallstatistics`");
				$userData = userData();
				
				if ($userData['organization'] != "0") {
					$table = "organizationstatistics_" . $userData['organization'];
				} else {
					$table = "overallstatistics";
				}
				
				if (mysql_fetch_array($statisticsCheck)) {
					$firstItemGrabber = mysql_query("SELECT * FROM `{$table}` ORDER BY `id` ASC LIMIT 1");
					$firstItemArray = mysql_fetch_array($firstItemGrabber);
					$firstItem = $firstItemArray['date'];
					$lastItemGrabber = mysql_query("SELECT * FROM `{$table}` ORDER BY `id` DESC LIMIT 1");
					$lastItemArray = mysql_fetch_array($lastItemGrabber);
					$lastItem = $lastItemArray['date'];
					
					$statisticsGrabber = mysql_query("SELECT * FROM `{$table}` ORDER BY `id` ASC");
					
					echo "<graph caption=\"Overall Summary of Usage\" subcaption=\"From " . $firstItem . " to " . $lastItem . "\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" showAnchors=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\">";
			
					while($statistics = mysql_fetch_array($statisticsGrabber)) {
						echo "<set name=\"" . $statistics['date'] . "\" value=\"" . $statistics['hits'] . "\" hoverText=\"" . $statistics['date'] . "\" />";
					}
					
					echo "</graph>";
				} else {
					echo "<graph caption=\"Overall Summary of Usage\" subcaption=\"No Data\" yAxisMinValue=\"10\" yAxisName=\"hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" numberPrefix=\"\" showNames=\"0\" showValues=\"0\" showAnchors=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\">";
				}
				break;
				
			case "assignedUsers" : 
				headers("Assigned Users", "Instructor", false, false, false, false, false, false, false, "XML"); 
				$statistics = userData();
				$totalUsers = query("SELECT * FROM `users` WHERE `organization` = '{$statistics['organization']}' AND `role` = 'Student'", "num");
				$assignedUsersGrabber = query("SELECT * FROM `users` WHERE `organization` = '{$statistics['organization']}' AND `role` = 'Student'", "raw");
				$assigned = 0;
				
				while ($assignedUsers = mysql_fetch_array($assignedUsersGrabber)) {
					$value = unserialize($assignedUsers['modules']);
					
					if (!empty($value)) {
						$assigned = $assigned + 1;
					}
				}
				
				echo "<graph caption=\"Precentage of Users Assigned to One or More Modules\" yAxisMinValue=\"0\" yAxisMaxValue=\"100\" yAxisName=\"Percentage of Completion\" numberSuffix=\"%25\" showNames=\"0\" decimalPrecision=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\">";
				
				echo "<categories><category name=\"Users\"/></categories>";
				echo "<dataset seriesName=\"Assigned\" color=\"00CC33\"><set value=\"" . sprintf(($assigned / $totalUsers) * 100) . "\"/></dataset>";
				echo "<dataset seriesName=\"Un-Assigned\" color=\"FF3333\"><set value=\"" . sprintf((($totalUsers - $assigned) / $totalUsers) * 100) . "\"/></dataset>";
				echo "</graph>";
				break;
				
			case "account" :
				headers("Statistics Data Collection", "Student", false, false, false, false, false, false, false, "XML"); 
				$statistics = userData();
				$modules = unserialize($statistics['modules']);
				$totalModules = sprintf(count($modules) * 4);
				
				echo "<graph caption=\"Overall Status of Completion\" subcaption=\"Total number of modules: " . sizeof($modules) . "\" yAxisMinValue=\"0\" yAxisMaxValue=\"100\" yAxisName=\"Percentage of Completion\" numberSuffix=\"%25\" showNames=\"0\" decimalPrecision=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\">";
				
				$completionPrep = 0;
				
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
				
				$completion = ($completionPrep/$totalModules) * 100;
				
				if ($completion  > 0) {
					echo "<set name=\"Percentage Complete\" value=\"" . round($completion) . "\" /></graph>";
				} else {
					echo "<set /></graph>";
				}
				
				break;
		}
	}
?>