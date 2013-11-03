<?php
//Header functions
	require_once('../../system/connections/connDBA.php');

//Pull category or employee data for auto-suggestion
	if ((strstr($_SERVER['REQUEST_URI'], "module_wizard/lesson_settings.php") || strstr($_SERVER['REQUEST_URI'], "/questions/")) && isset($_GET['data']) && $_GET['data'] == "xml") {
		headers("Auto-Suggest Data Collection", "Organization Administrator,Site Administrator", false, false, false, false, false, false, false, "XML");
		header("Content-type: text/xml");
		echo "<root>";
		
		$userData = userData();		
		$categoryBank = query("SELECT * FROM `modulecategories` ORDER BY `category` ASC", "raw");
		$priorEntries = query("SELECT * FROM `moduledata` WHERE `organization` = '{$userData['organization']}'", "raw");
		$noRepeat = array(array(), array());
		
		if (access("accessAllSuggestions")) {
			while($category = mysql_fetch_array($categoryBank)) {
				echo "<group>";
				
				if (!in_array(prepare($category['category'], false, true), $noRepeat['0'])) {
					echo "<category>" . prepare($category['category'], false, true) . "</category>";
				}
				
				echo "<employee></employee>";
				echo "</group>";
				
				array_push($noRepeat['0'], prepare($category['category'], false, true));
			}
		}
		
		while($suggestion = mysql_fetch_array($priorEntries)) {
			echo "<group>";
			
			if (!in_array(prepare($suggestion['category'], false, true), $noRepeat['0'])) {
				echo "<category>" . prepare($suggestion['category'], false, true) . "</category>";
			}
			
			if (!in_array(prepare($suggestion['employee'], false, true), $noRepeat['1'])) {
				echo "<employee>" . prepare($suggestion['employee'], false, true) . "</employee>";
			}
			
			echo "</group>";
			
			array_push($noRepeat['0'], prepare($suggestion['category'], false, true));
			array_push($noRepeat['1'], prepare($suggestion['employee'], false, true));
		}
		
		echo "</root>";
		exit;
	}
	
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
			$dataGrabber = mysql_query("SELECT * FROM `questionbank_0` WHERE `id` = '{$id}'", $connDBA);
			
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
		textArea("question", "question", "small", true, false, false, "questionData", "question", " class=\"noEditorQuestion\"");
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
			
			if (exist($monitor['testTable'], "type", "Description")) {
				$descriptionGrabber = query("SELECT * FROM `{$monitor['testTable']}` WHERE `type` = 'Description' ORDER BY `position` ASC", "raw");
				$descriptionID = ",";			
				$descriptionName = "- Select -,";
				
				while ($description = mysql_fetch_array($descriptionGrabber)) {
					if ($description['type'] == "Description" && $description['questionBank'] != "1") {
						$descriptionID .= $description['id'] . ",";
						$descriptionName .= $description['position'] . ". " . commentTrim(25, $description['question']) . ",";
					}
					
					if ($description['questionBank'] == "1") {
						$importID = $description['linkID'];
						$descriptionImportGrabber = mysql_query("SELECT * FROM `questionbank_0` WHERE `id` = '{$importID}'", $connDBA);
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
		radioButton("partialCredit", "partialCredit", "Yes,No", "1,0", true, false, false, "0", "questionData", "partialCredit", " onchange=\"toggleFeedback(this.value)\"");
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
		global $monitor;
		
		if (!strstr($_SERVER['REQUEST_URI'], "module_wizard")) {
			if (isset($_SESSION['currentModule']) && isset($_SESSION['questionBank'])) {
				$category = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$_SESSION['currentModule']}'");
			} elseif (isset($_SESSION['currentModule'])) {
				$category = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$_SESSION['currentModule']}'");
			} elseif (isset($_SESSION['questionBank'])) {
				$category = query("SELECT * FROM `modulecategories` WHERE `id` = '{$_SESSION['questionBank']}'");
			}
			
			directions("Category", true);
			echo "<blockquote><p><div id=\"categoryMenu\">";
			textField("category", "category", false, false, false, true, false, $category['category'], "questionData", "category");
			echo "<div><div id=\"categorySuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{category}\">{category}</div></div></div></div></p></blockquote>";
		} else {
			echo "<div id=\"categoryMenu\">";
			textField("category", "category", false, false, false, true, false, false, "moduleData", "category");
			echo "<div><div id=\"categorySuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{category}\">{category}</div></div></div></div>";
		}
		
		echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"categoryMenu\", \"categorySuggestions\", \"data\", \"category\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
	}
	
//Display all of the employee types
	function employeeTypes() {
		echo "<div id=\"employeeMenu\">";
		textField("employee", "employee", false, false, false, true, false, false, "moduleData", "employee");
		echo "<div><div id=\"employeeSuggestions\" spry:region=\"data\"><div spry:repeat=\"data\" spry:suggest=\"{employee}\">{employee}</div></div></div></div>";
		
		echo "<script type=\"text/javascript\">var dataSuggestions = new Spry.Widget.AutoSuggest(\"employeeMenu\", \"employeeSuggestions\", \"data\", \"employee\", {containsString: true, moveNextKeyCode: 40, movePrevKeyCode: 38});</script>";
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