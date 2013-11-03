<?php
//Header functions
	require_once('../../../Connections/connDBA.php');
	
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
	
//Display all levels of difficulty
	function difficulty() {		
		if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
			directions("Difficulty", false);
			echo "<blockquote><p>";
			dropDown("difficulty", "difficulty", "Easy,Average,Difficult", "Easy,Average,Difficult", false, false, false, $_SESSION['difficulty'], "questionData", "difficulty");
			echo "</p></blockquote>";
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
		
		$descriptionCheck = mysql_query("SELECT * FROM `{$monitor['testTable']}`", $connDBA);
		
		if ($descriptionCheck) {
			$descriptionID = ",";			
			$descriptionName = "- Select -,";
			
			while ($description = mysql_fetch_array($descriptionCheck)) {
				if ($description['type'] == "Description") {
					$descriptionID .= $description['id'] . ",";
					$descriptionName .= $description['position'] . ". " . commentTrim(25, $description['question']) . ",";
				}
				
				if ($description['questionBank'] == "1") {
					$importID = $description['linkID'];
					$descriptionImportGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$importID}'", $connDBA);
					$descriptionImport = mysql_fetch_array($descriptionImportGrabber);
					
					if ($descriptionImport['type'] == "Description") {
						echo "<option value=\"" . $description['id'] ."\"";
						$descriptionID .= $description['id'] . ",";
						$descriptionName .= $description['position'] . ". " . commentTrim(25, $descriptionImport['question']) . ",";
					}
					
					unset($importID);
					unset($descriptionImportGrabber);
					unset($descriptionImport);
				}
			}
		} else {
			$descriptionID = "";			
			$descriptionName = "- Select -";
		}
		
		$IDs = rtrim($descriptionID, ",");
		$values = rtrim($descriptionName, ",");
		
		directions("Link to description", false);
		echo "<blockquote><p>";
		dropDown("link", "link", $values, $IDs, false, false, false, false, "questionData", "link");
		echo "</p></blockquote>";
	}
	
//Display partial credit option
	function partialCredit() {
		directions("Allow partial credit");
		echo "<blockquote><p>";
		radioButton("partialCredit", "partialCredit", "Yes,No", "1,0", true, false, false, "0", "questionData", "partialCredit", " onchange=\"toggleFeedback(this.value)\"");
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
		global $connDBA;
		
		$categoryGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
		$categoryID = ",";
		$categoryName = "- Select -,";
		
		while ($category = mysql_fetch_array($categoryGrabber)) {
			$categoryID .= $category['id'] . ",";
			$categoryName .= prepare($category['category'], true) . ",";
		}
		
		$IDs = rtrim($categoryID, ",");
		$values = rtrim($categoryName, ",");
		
		if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
			$editorTrigger = "questionData";
		} else {
			$editorTrigger = "moduleData";
		}
		
		dropDown("category", "category", $values, $IDs, false, true, false, false, $editorTrigger, "category");
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
	
//Ensure the page is handleing the correct question type
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
	}
?>