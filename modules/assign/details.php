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
	
	headers($title, "Instructor");
	
//Title
	title($title, $description);
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Back to Overview", "index.php", "toolBarItem back");
	echo URL("Edit Schedule", "assign.php?type=" . $_GET['type'] . "&id=" . $_GET['id'], "toolBarItem editTool");
	echo URL("Reset Schedule", "assign.php?type=" . $_GET['type'] . "&id=" . $_GET['id'] . "&action=reset", "toolBarItem deleteTool", false, false, false, false, false, false, " onclick=\"alert('Warning: This action will clear this user\'s current schedule, and cannot be undone.'); return confirm('Click OK to reset this user\'s schedule.')\"");
	echo "</div><br />";

//Overview of assignments
	catDivider("Schedule Overview", "one", true);
	//Generate data
	$module = unserialize($data['modules']);
	$modules = count($module);
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
			$recentAttempt = query("SELECT `attempt` FROM `testdata_{$_GET['id']}` WHERE `testID` = '{$moduleCalc['item']}'", "selected");
			$testScoreGrabber = query("SELECT * FROM `testdata_{$_GET['id']}` WHERE `testID` = '{$moduleCalc['item']}'", "raw");
			$points = 0;
			$testTotal = 0;
			
			if ($testScoreGrabber) {
				while ($testScore = mysql_fetch_array($testScoreGrabber)) {
					if ($testScore['attempt'] == max($recentAttempt)) {
						$points = $points + $testScore['points'];
						$testTotal = $testTotal + $testScore['score'];
					}
				}
				
				if (sprintf(($points / $testTotal ) * 100) > $moduleSettings['score']) {
					$pass++;
				} else {
					$fail++;
				}
			} else {
				$pass++;
			}
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
	echo "<blockquote><p>" . $modules . "</p></blockquote>";
	directions("Start Date");
	echo "<blockquote><p>" . $start . "</p></blockquote>";
	directions("End Date");
	echo "<blockquote><p>" . $end . "</p></blockquote>";
	directions("Complete, Number Due, Number Finished, Number Passed, Number Failed");
	echo "<blockquote><p>" . $complete . ", " . $remaining . ", " . $completed . ", " . $pass . ", " . $fail . "</p></blockquote>";
	echo "</blockquote>";
	
//Detailed schedule of assignments
	catDivider("Schedule Details", "two");
	echo "<blockquote>";
	
	$modules = unserialize($data['modules']);
	
	if (!empty($modules)) {
		echo "<table class=\"dataTable\"><tr><th width=\"50\" class=\"tableHeader\">Order</th><th width=\"250\" class=\"tableHeader\">Module</th><th width=\"175\" class=\"tableHeader\">Start Date</th><th width=\"175\" class=\"tableHeader\">Due Date</th><th width=\"50\" class=\"tableHeader\">Lesson</th><th width=\"50\" class=\"tableHeader\">Test</th><th width=\"50\" class=\"tableHeader\">Score</th><th width=\"50\" class=\"tableHeader\">Pass</th></tr>";
		
		$count = 1;
		
		foreach($modules as $moduleData) {
		//Generate data
			$id = $moduleData['item'];
			$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$id}'");
			$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
			$numberArray = array("0","1","2","3","4","5","6","7","8","9");
			$time = str_replace($letterArray, "", $moduleInfo['timeFrame']);
			$timeLabel = str_replace($numberArray, "", $moduleInfo['timeFrame']);					
			$end = date("F j, Y", strtotime(date("F j, Y", $moduleData['startDate']) . " +" . $time . $timeLabel));
			
			function generateStatus($inputStatus, $compareStatus) {
				global $root;
				
				if ($inputStatus != "-") {	
					if ((!is_numeric($compareStatus) && ($inputStatus == $compareStatus)) || (is_numeric($compareStatus) && ($inputStatus > $compareStatus))) {
						return "<span class=\"checkmark\"></span>";
					} else {
						return "<span class=\"x\"></span>";
					}
				} else {
					return "<img src=\"" . $root . "system/images/admin_icons/help.png\" width=\"17\" height=\"17\">";
				}
			}
			
			$points = 0;
			$total = 0;
			
			if (exist("testdata_" . $_GET['id'])) {
				$recentAttempt = query("SELECT `attempt` FROM `testdata_{$_GET['id']}` WHERE `testID` = '{$id}'", "selected");
				$testScoreGrabber = query("SELECT * FROM `testdata_{$_GET['id']}` WHERE `testID` = '{$id}'", "raw");
				
				while ($testScore = mysql_fetch_array($testScoreGrabber)) {
					if ($testScore['attempt'] == max($recentAttempt)) {
						$points = $points + $testScore['points'];
						$total = $total + $testTotal['score'];
					}
				}
				
				if ($total != 0) {
					$score = sprintf(($points / $total) * 100) . "%";
					$pass = sprintf(($points / $total) * 100);
				} else {
					$score = "<img src=\"" . $root . "system/images/admin_icons/help.png\" width=\"17\" height=\"17\">";
					$pass = "-";
				}
			}
			
		//Display data
			echo "<tr";
			if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"50\">" . $count . ".</td>";
			echo "<td width=\"200\">" . URL(commentTrim(35, $moduleInfo['name']), "../lesson.php?id=" . $id, false, "_blank") . "</td>";
			echo "<td width=\"175\">" . date("F j, Y", $moduleData['startDate']) . "</td>";
			echo "<td width=\"175\">" . $end . "</td>";
			echo "<td width=\"50\">" . generateStatus($moduleData['moduleStatus'], "F") . "</td>";
			echo "<td width=\"50\">" . generateStatus($moduleData['testStatus'], "F") . "</td>";
			echo "<td width=\"50\">" . $score . "</td>";
			echo "<td width=\"50\">" . generateStatus($pass, $moduleInfo['score']) . "</td>";
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