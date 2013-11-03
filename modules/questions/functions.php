<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Ensure the page is handling the correct question type
	function dataGrabber($type) {
		global $connDBA, $monitor;
		
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$dataGrabber = mysql_query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$id}'", $connDBA);
			
			if ($dataGrabber) {
				$data = mysql_fetch_array($dataGrabber);
				
				if ($data['type'] == $type) {
					return $data;
				} else {
					redirect($monitor['redirect']);
				}
			} else {
				redirect($monitor['redirect']);
			}
		}
		
		if (isset($_GET['bankID'])) {
			$id = $_GET['bankID'];
			$dataGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$id}'", $connDBA);
			
			if ($dataGrabber) {
				$data = mysql_fetch_array($dataGrabber);
				
				if ($data['type'] == $type) {
					return $data;
				} else {
					redirect("../question_bank/index.php");
				}
			} else {
				redirect("../question_bank/index.php");
			}
		}
	}
	
//Display a question
	function question() {
		directions("Question", true);
		echo "<blockquote><p>";
		textArea("question", "question", "small", true, false, false, "questionData", "question");
		echo "</p></blockquote>";
	}
	
//Display the point value
	function points() {
		directions("Question points", true);
		echo "<blockquote><p>";
		textField("points", "points", "5", "5", false, true, ",custom[onlyNumber]", false, "questionData", "points");
		echo "&nbsp;";
		checkbox("extraCredit", "extraCredit", "Extra Credit", false, false, false, false, "questionData", "extraCredit", "on");
		echo "</p></blockquote>";
	}
	
//Include where this question is being inserted
	function type() {
		global $questionData;
		
		$active = 0;
		$valuesPrep = "";
		$valueIDsPrep = "";
		
		if (isset($_SESSION['currentModule'])) {
			$active = 1;
			$valuesPrep .= "Current Test,";
			$valueIDsPrep .= "Module,";
		}
		
		if (isset($_SESSION['questionBank'])) {
			$active = $active + 1;
			$valuesPrep .= "Question Bank,";
			$valueIDsPrep .= "Bank,";
		}
		
		if (isset($_SESSION['feedback'])) {
			$active = $active + 1;
			$valuesPrep .= "Feedback,";
			$valueIDsPrep .= "Feedback,";
		}
		
		$values = rtrim($valuesPrep, ",");
		$valueIDs = rtrim($valueIDsPrep, ",");
		
		if ($active > 1 && !isset($_GET['id']) && !isset($_GET['bankID']) && !isset($_GET['feedbackID'])) {
			directions("Insert question into");
			echo "<blockquote><p>";
			dropDown("type", "type", $values, $valueIDs, false, false, false, false, false, false, " onchange=\"toggleDescription(this.value);\"");
			echo "</p></blockquote>";
		} else {
			if (isset($questionData)) {
				if (!array_key_exists("position", $questionData)) {
					$valueIDs = "Bank";
				} elseif (!array_key_exists("link", $questionData)) {
					$valueIDs = "Feedback";
				} else {
					$valueIDs = "Module";
				}
			}
			
			hidden("type", "type", $valueIDs);
		}
	}
	
//Display all levels of difficulty
	function difficulty() {
		global $monitor;
		
		if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
			if (isset($_SESSION['currentModule'])) {
				$difficulty = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$_SESSION['currentModule']}'");
				directions("Difficulty", false);
				echo "<blockquote><p>";
				dropDown("difficulty", "difficulty", "Easy,Average,Difficult", "Easy,Average,Difficult", false, false, false, $difficulty['difficulty'], "questionData", "difficulty");
				echo "</p></blockquote>";
			} else {
				directions("Difficulty", false);
				echo "<blockquote><p>";
				dropDown("difficulty", "difficulty", "Easy,Average,Difficult", "Easy,Average,Difficult", false, false, false, "Average", "questionData", "difficulty");
				echo "</p></blockquote>";
			}
		} else {
			directions("Difficulty", false, "The overall difficulty of this module");
			echo "<blockquote><p>";
			dropDown("difficulty", "difficulty", "Easy,Average,Difficult", "Easy,Average,Difficult", false, false, false, "Average", "moduleData", "difficulty");
			echo "</p></blockquote>";
		}
	}
	
//Display all of the descriptions in this test
	function descriptionLink() {
		global $connDBA, $monitor;
		
		if (isset($_SESSION['currentModule']) && !isset($_GET['bankID']) && !isset($_GET['feedbackID'])) {
			echo "<div id=\"descriptionLink\">";
			
			$descriptionCheck = query("SELECT * FROM `{$monitor['testTable']}` WHERE `type` = 'Description' ORDER BY `position` ASC", "raw");
			
			if ($descriptionCheck) {
				$descriptionID = ",";			
				$descriptionName = "- Select -,";
				
				while ($description = mysql_fetch_array($descriptionCheck)) {
					if ($description['type'] == "Description" && $description['questionBank'] != "1") {
						$descriptionID .= $description['id'] . ",";
						$descriptionName .= $description['position'] . ". " . commentTrim(25, $description['question']) . ",";
					}
					
					if ($description['questionBank'] == "1") {
						$importID = $description['linkID'];
						$descriptionImportGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$importID}'", $connDBA);
						$descriptionImport = mysql_fetch_array($descriptionImportGrabber);
						
						if ($descriptionImport['type'] == "Description") {
							$descriptionID .= $description['id'] . ",";
							$descriptionName .= $description['position'] . ". " . commentTrim(25, $descriptionImport['question']) . ",";
						}
						
						unset($importID);
						unset($descriptionImportGrabber);
						unset($descriptionImport);
					}
				}
				
				$IDs = rtrim($descriptionID, ",");
				$values = rtrim($descriptionName, ",");
			} else {
				$IDs = "";			
				$values = "- None -";
			}
			
			directions("Link to description", false);
			echo "<blockquote><p>";
			dropDown("link", "link", $values, $IDs, false, false, false, false, "questionData", "link");
			echo "</p></blockquote></div>";
		} else {
			hidden("link", "link", "");
		}
	}
	
//Display partial credit option
	function partialCredit() {
		directions("Allow partial credit");
		echo "<blockquote><p>";
		radioButton("partialCredit", "partialCredit", "Yes,No", "1,0", true, false, false, "1", "questionData", "partialCredit", " onchange=\"toggleFeedback(this.value)\"");
		echo "</p></blockquote>";
	}
	
//Display a case sensitivity option
	function ignoreCase() {
		directions("Ignore case");
		echo "<blockquote><p>";
		radioButton("case", "case", "Yes,No", "1,0", true, false, false, "1", "questionData", "case");
		echo "</p></blockquote>";
	}
	
//Display a randomize option
	function randomize() {
		directions("Randomize values");
		echo "<blockquote><p>";
		radioButton("randomize", "randomize", "Yes,No", "1,0", true, false, false, "0", "questionData", "randomize");
		echo "</p></blockquote>";
	}
	
//Display search tags
	function tags() {
		directions("Tags (Seperate with commas)", false);
		echo "<blockquote><p>";
		textField("tags", "tags", false, false, false, false, false, false, "questionData", "tags");
		echo "</p></blockquote>";
	}
	
//Display all of the category items
	function category() {
		global $connDBA, $questionData, $moduleData, $monitor;
				
		if (access("modifyAllModules")) {
			$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY position ASC", $connDBA);
			$valuePrep = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'");
			$value = $valuePrep['category'];
			$categoryID = ",";
			$categoryName = "- Select -,";
			
			while ($category = mysql_fetch_array($categoryGrabber)) {
				$categoryID .= $category['id'] . ",";
				$categoryName .= prepare($category['category'], true) . ",";
			}
			
			$IDs = rtrim($categoryID, ",");
			$values = rtrim($categoryName, ",");
			
			if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
				directions("Category");
				echo "<blockquote><p>";
				dropDown("category", "category", ltrim($values, "- Select -,"), ltrim($IDs, ","), false, true, false, $value, "questionData", "category");
				echo "</p></blockquote>";
			} else {
				dropDown("category", "category", $values, $IDs, false, true, false, false, "moduleData", "category");
			}
		} else {
			if (isset($questionData)) {
				$parentVariable = $questionData;
				$trigger = "questionData";
			} elseif (isset($moduleData)) {
				$parentVariable = $moduleData;
				$trigger = "moduleData";
			}
			
			if (isset($parentVariable) && is_numeric($parentVariable['category']) && exist("modulecategories", "id", $parentVariable['category'])) {
				$valuePrep = query("SELECT * FROM `modulecategories` WHERE `id` = '{$parentVariable['category']}'");
				$value = $valuePrep['category'];
			} elseif (isset($parentVariable) && (!is_numeric($parentVariable['category']) || !exist("modulecategories", "id", $parentVariable['category']))) {
				$value = $questionData['category'];
			} else {
				if (array_key_exists("currentModule", $monitor)) {
					$valuePrep = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'");
					$value = $valuePrep['category'];
				} else {
					$value = "";
				}
			}
			
			if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
				directions("Category", true);
				echo "<blockquote><p>";
				textField("category", "category", false, false, false, true, false, $value, $trigger, "category");
				echo "</p></blockquote>";
			} else {
				textField("category", "category", false, false, false, true, false, $value, $trigger, "category");
			}
		}
	}
	
//Display all of the employee types
	function employeeTypes() {
		global $connDBA;
		
		$employeeGrabber = mysql_query("SELECT * FROM moduleemployees ORDER BY position ASC", $connDBA);
		$employeeID = ",";
		$employeeName = "- Select -,";
		
		while ($employee = mysql_fetch_array($employeeGrabber)) {
			$employeeID .= $employee['id'] . ",";
			$employeeName .= prepare($employee['employee'], true) . ",";
		}
		
		$IDs = rtrim($employeeID, ",");
		$values = rtrim($employeeName, ",");
		
		if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
			$editorTrigger = "questionData";
		} else {
			$editorTrigger = "moduleData";
		}
		
		dropDown("employee", "employee", $values, $IDs, false, true, false, false, $editorTrigger, "employee");
	}
	
//Display the feedback
	function feedback($hidePartial = false) {
		global $connDBA, $questionData;
		
		echo "<blockquote>";
		directions("Feedback for correct answer");
		echo "<blockquote><p>";
		textArea("feedBackCorrect", "feedBackCorrect", "small", false, false, false, "questionData", "correctFeedback");
		echo "</p></blockquote>";
		
		if ($hidePartial == true) {
			if (isset($questionData)) {
				if ($questionData['partialCredit'] == "1") {
					$class = "contentShow";
				} else {
					$class = "contentHide";
				}
			} else {
				$class = "contentHide";
			}
			
			echo "<div class=\"" . $class . "\" id=\"toggleFeedback\">";
			directions("Feedback for partially correct answer");
			echo "<blockquote><p>";
			textArea("feedBackPartial", "feedBackPartial", "small", false, false, false, "questionData", "partialFeedback");
			echo "</p></blockquote>";
			echo "</div>";
		} else {
			directions("Feedback for partially correct answer");
			echo "<blockquote><p>";
			textArea("feedBackPartial", "feedBackPartial", "small", false, false, false, "questionData", "partialFeedback");
			echo "</p></blockquote>";
		}
		
		directions("Feedback for incorrect answer");
		echo "<blockquote><p>";
		textArea("feedBackIncorrect", "feedBackIncorrect", "small", false, false, false, "questionData", "incorrectFeedback");
		echo "</p></blockquote></blockquote>";
	}
	
//Display the buttons at the bottom of the form
	function buttons() {
		echo "<blockquote><p>";
		button("submit", "submit", "Submit", "submit");
		button("reset", "reset", "Reset", "reset");
		button("cancel", "cancel", "Cancel", "history");
		echo "</p></blockquote>";
	}
?>