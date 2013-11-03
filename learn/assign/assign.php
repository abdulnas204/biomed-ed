<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	headers("Module Assignment", "Instructor", "showHide,assignmentLibrary,validate,tableReorder", true);
	
//Check to see if the requested data is valid
	if (isset($_GET['id'])) {
		$userData = userData();
		
		if (query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}' AND `organization` = '{$userData['organization']}' AND `role` = 'Student'", "raw")) {
			$data = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}' AND `organization` = '{$userData['organization']}'");
		} else {
			redirect("index.php");
		}
	} else {
		redirect("index.php");
	}
	
//Updating a lesson plan
	if (isset($_GET['id'])) {	
	//Title
		title("Module Assignment", "Below is the user lesson management.");
		
	//Assignment form
		form("assignment");
		catDivider("Select Users", "one", true);
		echo "<blockquote>";
		directions("Select users to recieve this lesson plan");
		echo "<blockquote><p><strong>" . prepare($data['firstName'], false, true) . " " . prepare($data['lastName'], false, true) . "</strong></p></blockquote>";
		echo "</blockquote>";
		
		catDivider("Global Settings", "two");
		echo "<blockquote>";
		directions("Select scheduling method", false, "<strong>Linear</strong> - Modules are taken in instructor-specified order<br /><strong>Open</strong> - Students may choose ordering of modules");
		echo "<blockquote><p>";
		dropDown("method", "method", "Linear,Open", "Linear,Open", false, false, false, "Linear", "assignment", "method", " onchange=\"toggleDisplay(this.value, 'presentation')\"");
		prepare($data['firstName'], false, true) . " " . prepare($data['lastName'], false, true);
		echo "</p></blockquote>";
		echo "<div id=\"presentation\" class=\"contentHide\">";
		directions("Maxmium number of modules premitted at one time");
		echo "<blockquote><p>";
		dropDown("maxModules", "maxModules", "1,2,3,4,5,6,7,8,9,10", "1,2,3,4,5,6,7,8,9,10", false, false, false, "1", "assignment", "maxModules");
		echo "</p></blockquote>";
		echo "</div>";
		echo "</blockquote>";
		
		catDivider("Assignments", "three");
		
		$modulesGrabber = query("SELECT * FROM `moduledata`", "raw");
		$userInfo = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}'");
		$count = 0;
		
		if (query("SELECT * FROM `moduledata`", "num") == count(arrayRevert($userInfo['modules']))) {
			$masterCheck = true;
		} else {
			$masterCheck = false;
		}
		
		echo "<blockquote>";
		echo "<p>Below is the list of all modules avaliable to this student. Settings for each module can be customized individually.</p>";
		echo "<table class=\"dataTable\"><thead><th width=\"1\" class=\"tableHeader\"></th><th width=\"15\" class=\"tableHeader\">";
		checkbox("masterAssign", "masterAssign", false, false, false, false, $masterCheck, false, false, false, " onclick=\"checkAll(this, this.form, 'time,timeLabel,score,difficulty,questions,attempts')\"");
		echo "</th><th width=\"250\" class=\"tableHeader\">Module ";
		help("The name of the module, rollover <br />the names for details");
		echo "</th><th width=\"100\" class=\"tableHeader\">Avaliability ";
		help("The amount of time the user will have <br />to complete the module. <i>This feature may <br />or may not be altered, depending on the <br />individual module settings</i>.");
		echo "</th><th width=\"75\" class=\"tableHeader\">Min Score ";
		help("The minimium score a user must achieve <br />to pass. <i>This feature may or may not be <br />altered, depeding on the individual module settings.</i>");
		echo "</th><th width=\"75\" class=\"tableHeader\">Difficulty ";
		help("The level of difficulty <br />for the test questions");
		echo "</th><th width=\"50\" class=\"tableHeader\">Questions ";
		help("The number of test questions. This <br />value will change based on your <br />selection from &quot;difficulty&quot;.");
		echo "</th><th width=\"75\" class=\"tableHeader\">Attempts ";
		help("The number of attempts a user may make on a test");
		echo "</th></thead><tbody id=\"reorder\">";
		
		while($modules = mysql_fetch_array($modulesGrabber)) {
			$category = query("SELECT * FROM `modulecategories` WHERE `id` = '{$modules['category']}'");
			$employee = query("SELECT * FROM `moduleemployees` WHERE `id` = '{$modules['employee']}'");
			$count ++;
			
			
			if (is_array(arrayRevert($data['modules']))) {
				foreach(arrayRevert($data['modules']) as $module) {
					if ($module['item'] == $modules['id']) {
						$alter = true;
					}
				}
			}
			
			if (isset($alter)) {
				echo "<tr";
				echo " id=\"row_" . $modules['id'] . "\"";
				if ($count & 1) {echo " class=\"oddRollover marked\">";} else {echo " class=\"evenRollover marked\">";}
				
				$checked = true;
				$disabled = "";
			} else {
				echo "<tr";
				echo " id=\"row_" . $modules['id'] . "\"";
				if ($count & 1) {echo " class=\"oddRollover\">";} else {echo " class=\"evenRollover\">";}
				
				$checked = false;
				$disabled = " disabled=\"disabled\"";
			}
			
			echo "<td width=\"1\" id=\"drag\" style=\"cursor:move\"><img src=\"../../system/images/common/gripper.png\"></td>";
			echo "<td width=\"15\" id=\"" . $modules['id'] . "\" onclick=\"setClass(this)\">";
			checkbox("assign[]", "assign_" . $modules['id'], false, $modules['id'], true, "1", $checked, false, false, false, " onclick=\"allChecked(this, this.form); setClass(this); toggleFields(this, 'time,timeLabel,score,difficulty,questions,attempts')\"");
			echo "</td>";		
			echo "<td width=\"250\">" . tip("<strong>Category</strong>: " . prepare($category['category'], true, true) . "<br /><strong>Indended Employee Type</strong>: " . prepare($employee['employee'], true, true) . "<br /><strong>Overall difficulty</strong>: " . $modules['difficulty'], commentTrim(35, $modules['name'])). "</td>";
			echo "<td width=\"100\">";
			
			$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
			$numberArray = array("0","1","2","3","4","5","6","7","8","9");
			$time = str_replace($letterArray, "", $modules['timeFrame']);
			$timeLabel = str_replace($numberArray, "", $modules['timeFrame']);
			
			if ($modules['locked'] == "0") {
				dropDown("time_" . $modules['id'], "time_" . $modules['id'], "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25", "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25", false, false, false, $time, false, false, $disabled);
				echo " ";
				dropDown("timeLabel_" . $modules['id'], "timeLabel_" . $modules['id'], "Days,Weeks,Months,Years", "Days,Weeks,Months,Years", false, false, false, $timeLabel, false, false, $disabled);
			} else {				
				echo $time . " " . $timeLabel;
				hidden("time_" . $modules['id'], "time_" . $modules['id'], $time);
				hidden("timeLabel_" . $modules['id'], "timeLabel_" . $modules['id'], $timeLabel);
			}
			
			echo "</td>";
			echo "<td width=\"75\">";
			
			if (exist("moduletest_" . $modules['id'])) {
				if ($modules['locked'] == "0") {
					$valuesGenerate = "";
					
					for ($i = 1; $i <= 100; $i++) {
						$valuesGenerate .= $i . ",";
					}
					
					$values = rtrim($valuesGenerate, ",");
					
					dropDown("score_" . $modules['id'], "score_" . $modules['id'], $values, $values, false, false, false, $modules['score'], false, false, $disabled);
					echo " %";
				} else {
					$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
					$numberArray = array("0","1","2","3","4","5","6","7","8","9");
					$time = str_replace($letterArray, "", $modules['timeFrame']);
					$timeLabel = str_replace($numberArray, "", $modules['timeFrame']);
					
					echo $modules['score'] . "%";
					hidden("score_" . $modules['id'], "score_" . $modules['id'], $modules['score']);
				}
			} else {
				echo "-";
				hidden("score_" . $modules['id'], "score_" . $modules['id'], false);
			}
			
			echo "</td>";
			echo "<td width=\"75\">";
			
			if (exist("moduletest_" . $modules['id'])) {
				$valuesPrep = "All Levels,";
				
				if (exist("moduletest_" . $modules['id'], "difficulty", "Easy")) {
					$valuesPrep .= "Easy,";
				}
				
				if (exist("moduletest_" . $modules['id'], "difficulty", "Average")) {
					$valuesPrep .= "Average,";
				}
				
				if (exist("moduletest_" . $modules['id'], "difficulty", "Difficult")) {
					$valuesPrep .= "Difficult,";
				}
				
				$values = rtrim($valuesPrep, ",");
				
				dropDown("difficulty_" . $modules['id'], "difficulty_" . $modules['id'], $values, $values, false, false, false, $modules['difficulty'], false, false, $disabled);
			} else {
				echo "-";
				hidden("difficulty_" . $modules['id'], "difficulty_" . $modules['id'], false);
			}
			
			echo "</td>";
			echo "<td width=\"50\">";
			
			if (exist("moduletest_" . $modules['id'])) {
				$questions = query("SELECT * FROM `moduletest_{$modules['id']}`", "num");
				$valuesPrep = "";
				
				for($i = $questions; $i >= 1; $i--) {
					$valuesPrep .= $i . ",";
				}
				
				$values = rtrim($valuesPrep, ",");
				
				dropDown("questions_" . $modules['id'], "questions_" . $modules['id'], $values, $values, false, false, false, $questions, false, false, $disabled);
			} else {
				echo "-";
				hidden("questions_" . $modules['id'], "questions_" . $modules['id'], false);
			}
			
			echo "</td>";
			echo "<td width=\"75\">";
			
			if (exist("moduletest_" . $modules['id'])) {
				dropDown("attempts_" . $modules['id'], "attempts_" . $modules['id'], "Unlimited,1,2,3,4,5,6,7,8,9,10", "999,1,2,3,4,5,6,7,8,9,10", false, false, false, $modules['attempts'], false, false, $disabled);
			} else {
				echo "-";
				hidden("attempts_" . $modules['id'], "attempts_" . $modules['id'], false);
			}
			
			echo "</td>";
			echo "</tr>";
			
			unset($alter);
		}
		
		echo "</tbody></table>";
		echo "<br />";
		echo "<table><tr><td width=\"1\" class=\"tableHeader\"></td><td width=\"15\" class=\"tableHeader\">";
		textField("total", "total", "3", false, false, false, false, false, false, false, " class=\"calculate\"");
		echo "</td>";
		echo "</tr></table>";
		echo "</blockquote>";
		
		catDivider("Submit", "four");
		echo "<blockquote>";
		button("submit", "subimt", "Submit", "submit");
		button("reset", "reset", "Reset", "reset");
		button("cancel", "cancel", "Cancel", "button", "index.php");
		echo "</blockquote>";
		catDivider(false, false, false, true);
		closeForm(true, true);
	}
	
//Include the footer
	footer();
?>