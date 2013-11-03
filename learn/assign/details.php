<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Check to see if the requested data is valid
	if (isset($_GET['type']) && isset($_GET['id'])) {
		switch ($_GET['type']) {
			case "user" : 
				$table = "users";
				break;
			
			case "module" : 
				$table = "moduledata";
				break;
			
			default : 
				redirect("index.php");
				break;
		}
		
		if (exist($table, "id", $_GET['id'])) {
			$data = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
			
			if ($table == "users") {
				$title = prepare($data['firstName'], false, true) . " " . prepare($data['lastName'], false, true) . "'s Assignment Details";
				$description = "Below are the assignment details for " . prepare($data['firstName'], false, true) . " " . prepare($data['lastName'], false, true) . ".";
			} else {
				$title = prepare($data['name'], false, true) . " Assignment Details";
				$description = "Below are the assignment details for " . prepare($data['name'], false, true) . ".";
			}
		} else {
			redirect("index.php");
		}
	} else {
		redirect("index.php");
	}
	
//Generate JSON data for calendar
	if (isset($_GET['data'])) {
		$calendarReturn = array();
		
		foreach(arrayRevert($data['modules']) as $event) {
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
	
	headers($title, "Instructor", "fullCalendar", false, false, false, false, false, false, false, "<script type=\"text/javascript\">\$(document).ready(function() { \$(\"#calendar\").fullCalendar({theme: true, events: \"" . $_SERVER['REQUEST_URI'] . "&data=JSON\"});});</script>");
	
//Title
	title($title, $description);
	
	$modules = arrayRevert($data['modules']);
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Back to Overview", "index.php", "toolBarItem back");
	
	if (!empty($modules)) {
		echo URL("Edit Schedule", "assign.php?type=" . $_GET['type'] . "&id=" . $_GET['id'], "toolBarItem editTool");
		echo URL("Reset Schedule", "assign.php?type=" . $_GET['type'] . "&id=" . $_GET['id'] . "&action=reset", "toolBarItem deleteTool", false, false, false, false, false, false, " onclick=\"alert('Warning: This action will clear this user\'s current schedule, and cannot be undone.'); return confirm('Click OK to reset this user\'s schedule.')\"");
	}
	
	echo "</div><br />";
	
	if (!empty($modules)) {
	//Overview of assignments
		catDivider("Schedule Overview", "one", true);
		//Generate data
		$module = arrayRevert($data['modules']);
		$moduleTotal = count($module);
		$startPrep = reset($module);
		$endPrep = end($module);
		$start = date("F j, Y", $startPrep['startDate']);
		$lastModule = end($module);
		$lastDueDate = query("SELECT * FROM `moduledata` WHERE `id` = '{$lastModule['item']}'");
		$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$numberArray = array("0","1","2","3","4","5","6","7","8","9");
		$time = str_replace($letterArray, "", $lastDueDate['timeFrame']);
		$timeLabel = str_replace($numberArray, "", $lastDueDate['timeFrame']);					
		$end = date("F j, Y", strtotime(date("F j, Y", $endPrep['startDate']) . " +" . $time . $timeLabel));
		$finished = 0;
		$total = 0;
		$remaining = 0;
		$completed = 0;
		$pass = 0;
		$fail = 0;
		
		foreach ($module as $moduleCalc) {
			if ($moduleCalc['moduleStatus'] == "F") {
				$finished++;
			}
			
			if ($moduleCalc['testStatus'] == "F") {
				$finished++;
				$moduleSettings = query("SELECT * FROM `moduledata` WHERE `id` = '{$moduleCalc['item']}'");
				$attempts = query("SELECT `attempt` FROM `testdata_{$_GET['id']}` WHERE `testID` = '{$moduleCalc['item']}' ORDER BY `attempt` ASC", "selected");
				$testScoreGrabber = query("SELECT * FROM `testdata_{$_GET['id']}` WHERE `testID` = '{$moduleCalc['item']}' ORDER BY `testID` ASC", "raw");
				$points = 0;
				$testTotal = 0;
				
				if ($testScoreGrabber) {
					$pointsPrep = array();
					$score = array();
					
					for ($count = 0; $count <= sizeof($attempts) - 1; $count ++) {
						while ($testScore = mysql_fetch_array($testScoreGrabber)) {
							if ($testScore['attempt'] == $attempts[$count]) {
								$points = $points + $testScore['score'];
								$testTotal = $testTotal + $testScore['points'];
							}
						}
						
						array_push($pointsPrep, array($points, $testTotal));
						$points = 0;
						$testTotal = 0;
					}
					
					for ($count = 0; $count <= sizeof($pointsPrep) - 1; $count ++) {
						array_push($score, sprintf(($pointsPrep[$count]['0'] / $pointsPrep[$count]['1']) * 100));
					}
					
					switch ($moduleSettings['gradingMethod']) {
						case "Highest Grade" : 
							if (max($score) >= $moduleSettings['score']) {
								$pass++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"checkmark\"></span>";
							} else {
								$fail++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"x\"></span>";
							}
							
							${"score_" . $moduleCalc['item']} = round(max($score), 2) . "%";
							
							break;
						case "Average Grade" : 
							$averagePrep = 0;
							
							for ($count = 0; $count <= sizeof($score) - 1; $count ++) {
								$averagePrep = $averagePrep + $score[$count];
							}
							
							if (sprintf($averagePrep / sizeof($score)) >= $moduleSettings['score']) {
								$pass++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"checkmark\"></span>";
							} else {
								$fail++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"x\"></span>";
							}
							
							${"score_" . $moduleCalc['item']} = round(sprintf($averagePrep / sizeof($score)), 2) . "%";
							
							break;
							
						case "First Attempt" : 
							if (reset($score) >= $moduleSettings['score']) {
								$pass++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"checkmark\"></span>";
							} else {
								$fail++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"x\"></span>";
							}
							
							${"score_" . $moduleCalc['item']} = round(reset($score), 2) . "%";
							
							break;
							
						case "Last Attempt" : 
							if (end($score) >= $moduleSettings['score']) {
								$pass++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"checkmark\"></span>";
							} else {
								$fail++;
								${"pass_" . $moduleCalc['item']} = "<span class=\"x\"></span>";
							}
							
							${"score_" . $moduleCalc['item']} = round(end($score). 2) . "%";
							
							break;
					}
				} else {
					$pass++;
					${"pass_" . $moduleCalc['item']} = "<span class=\"checkmark\"></span>";
					${"score_" . $moduleCalc['item']} = "<span class=\"notAssigned\">None</span>";
				}
			} else {
				${"pass_" . $moduleCalc['item']} = "<img src=\"" . $root . "system/images/admin_icons/help.png\" width=\"17\" height=\"17\">";
				${"score_" . $moduleCalc['item']} = "<img src=\"" . $root . "system/images/admin_icons/help.png\" width=\"17\" height=\"17\">";
			}
			
			if ($moduleCalc['moduleStatus'] != "F" || $moduleCalc['testStatus'] != "F") {
				$remaining++;
			}
			
			if ($moduleCalc['moduleStatus'] == "F" && $moduleCalc['testStatus'] == "F") {
				$completed++;
			}
			
			$total = $total + 2;
		}
		
		$complete = sprintf(($finished / $total) * 100) . "%";
		
		//Display data
		echo "<blockquote>";
		directions("Number of Assignments");
		echo "<blockquote><p>" . $moduleTotal . "</p></blockquote>";
		directions("Start Date");
		echo "<blockquote><p>" . $start . "</p></blockquote>";
		directions("End Date");
		echo "<blockquote><p>" . $end . "</p></blockquote>";
		directions("Complete, Number Due, Number Finished, Number Passed, Number Failed");
		echo "<blockquote><p>" . $complete . ", " . $remaining . ", " . $completed . ", " . $pass . ", " . $fail . "</p></blockquote>";
		echo "</blockquote>";
		
	//Calendar schedule of assignments
		catDivider("Calendar Schedule", "two");
		echo "<blockquote>";
		echo "<div align=\"center\"><div id=\"calendar\"></div></div>";
		echo "</blockquote>";
		
	//Detailed schedule of assignments
		catDivider("Schedule Details", "three");
		echo "<blockquote>";
		echo "<table class=\"dataTable\"><tr><th width=\"50\" class=\"tableHeader\">Order</th><th width=\"250\" class=\"tableHeader\">Module</th><th width=\"175\" class=\"tableHeader\">Start Date</th><th width=\"175\" class=\"tableHeader\">Due Date</th><th width=\"50\" class=\"tableHeader\">Lesson</th><th width=\"50\" class=\"tableHeader\">Test</th><th width=\"50\" class=\"tableHeader\">Score</th><th width=\"50\" class=\"tableHeader\">Pass</th></tr>";
		
		$count = 1;
		
		function generateStatus($inputStatus, $compareStatus) {
			global $root;
			
			if ($inputStatus == $compareStatus) {
				return "<span class=\"checkmark\"></span>";
			} else {
				return "<span class=\"x\"></span>";
			}
		}
		
		foreach($modules as $moduleData) {
		//Generate data
			$id = $moduleData['item'];
			$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$id}'");
			$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
			$numberArray = array("0","1","2","3","4","5","6","7","8","9");
			$time = str_replace($letterArray, "", $moduleInfo['timeFrame']);
			$timeLabel = str_replace($numberArray, "", $moduleInfo['timeFrame']);					
			$end = date("F j, Y", strtotime(date("F j, Y", $moduleData['startDate']) . " +" . $time . $timeLabel));
			
		//Display data
			echo "<tr";
			if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"50\">" . $count . ".</td>";
			echo "<td width=\"200\">" . URL(commentTrim(35, $moduleInfo['name']), "../lesson.php?id=" . $id, false, "_blank") . "</td>";
			echo "<td width=\"175\">" . date("F j, Y", $moduleData['startDate']) . "</td>";
			echo "<td width=\"175\">" . $end . "</td>";
			echo "<td width=\"50\">" . generateStatus($moduleData['moduleStatus'], "F") . "</td>";
			echo "<td width=\"50\">" . generateStatus($moduleData['testStatus'], "F") . "</td>";
			echo "<td width=\"50\">" . ${"score_" . $moduleInfo['id']} . "</td>";
			echo "<td width=\"50\">" . ${"pass_" . $moduleInfo['id']} . "</td>";
			echo "</tr>";
			
			$count++;
		}
		
		echo "</blockquote>";
	} else {
		echo "<div class=\"noResults\">This user is not assigned to any modules. " . URL("Assign modules now", "assign.php?type=" . $_GET['type'] . "&id=" . $_GET['id']) . ".</div>";
	}
	
	echo "</table>";
	echo "</blockquote>";
	catDivider(false, false, false, true);

//Include the footer
	footer();
?>