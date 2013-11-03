<?php
/*
LICENSE: See "license.php" located at the root installation

This is the overview page for each user's gradebook.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');
	headers("Gradebook");
	
//Title
	title("Gradebook", "Below are your grades for each learning unit.");

//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo toolBarURL("Back to Learning Units", "../index.php", "toolBarItem back");
	echo "</div>\n<br />\n";
	
//Learning units table
	$assignedUnits = arrayRevert($userData['learningunits']);
	
	if (is_array($assignedUnits) && !empty($assignedUnits)) {
		$units = array();
		$sortIDs = array();
		
		foreach($assignedUnits as $key => $unit) {
			$sortIDs[$key] = $unit['submitted'];
		}
		
		asort($sortIDs);
		
		foreach($sortIDs as $key => $unit) {
			$units[$key] = $assignedUnits[$key];
		}
		
		$count = 1;
		
		echo "<table class=\"dataTable\">\n";
		echo "<tr>\n";
		echo column("Name");
		echo column("Date Submitted", "250");
		echo column("Due Date", "250");
		echo column("Grade", "50");
		echo column("Possible Points", "125");
		//echo column("Global Average", "200");
		echo "</tr>\n";
		
		foreach($units as $key => $unit) {
			$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$key}'");
			
			if (exist("testdata_" . $userData['id'], "testID", $key)) {
				$testInfo = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$key}' ORDER BY `attempt` ASC", "raw");
				$attempts = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$key}' ORDER BY `attempt` DESC LIMIT 1");
				$score = array();
				$points = array();
				
				for ($i = 1; $i <= $attempts['attempt']; $i++) {
					$score[$i] = 0;
					$points[$i] = 0;
				}
				
				while($test = fetch($testInfo)) {
					$score[$test['attempt']] += $test['score'];
					$points[$test['attempt']] += $test['points'];
				}
				
				switch($unitData['gradingMethod']) {
					case "Highest Grade" : 
						$score = max($score);
						$points = max($points);
						break;
						
					case "Average Grade" : 
						$score = round(array_sum($score)/$attempts['attempt']);
						$points = round(array_sum($points)/$attempts['attempt']);
						break;
					
					case "First Attempt" : 
						$score = $score['1'];
						$points = $points['1'];
						break;
					
					case "Last Attempt" : 
						$score = $score[$attempts['attempt']];
						$points = $points[$attempts['attempts']];
						break;
				}
			} else {
				$score = "<span class=\"notAssigned\">None</span>";
				
				if (exist("test_" . $key)) {
					$points = "<span class=\"notAssigned\">Variable</span>";
				} else {
					$points = "<span class=\"notAssigned\">None</span>";
				}
			}
			
			echo "<tr";
			if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			
			if (exist("testdata_" . $userData['id'], "testID", $key)) {
				echo cell(URL(commentTrim(30, $unitData['name']), "../review.php?id=" . $key . "&return=gradebook"));
			} else {
				echo cell($unitData['name']);
			}
			
			if (is_numeric($unit['submitted'])) {
				echo cell(date("l, F jS, Y", $unit['submitted']), "250");
			} else {
				if (exist("test_" . $key)) {
					echo cell("<span class=\"notAssigned\">Not submitted</span>", "250");
				} else {
					echo cell("<span class=\"notAssigned\">Not completed</span>", "250");
				}
			}
			
			echo cell(date("l, F jS, Y", strtotime(date("Y-m-d", $unit['startDate']) . " +" . strip($unitData['timeFrame'], "numbersOnly") . " " . strip($unitData['timeFrame'], "lettersOnly"))), "250");
			echo cell($score, "50");
			echo cell($points, "125");
			//echo cell("<div class=\"loadStats\" id=\"" . $key . "\">Click to calculate</div>", "200");
			echo "</tr>\n";
			
			$count++;
		}
	
		echo "</table>\n";
	} else {
		echo "<div class=\"noResults\">There are no items currently avaliable.</div>\n";
	}
		
//Include the footer
	footer();
?>