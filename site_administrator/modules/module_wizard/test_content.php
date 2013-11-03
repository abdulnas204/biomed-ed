<?php
//Header functions
	require_once('../../../Connections/connDBA.php');
	$monitor = monitor("Test Content", "navigationMenu");

//Reorder questions
	reorder("{$monitor['testTable']}", "test_content.php");
	
//Delete a page
	if (isset ($_GET['id']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$deleteGrabber = mysql_query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$id}'", $connDBA);
		$delete = mysql_fetch_array($deleteGrabber);
		
		if ($deleteGrabber) {
			if ($delete['type'] == "File Response") {
				delete($monitor['testTable'], "test_content.php?deleted=question", true, $monitor['directory'] . "/test/answers/" . $delete['fileURL']);
			} else {
				delete($monitor['testTable'], "test_content.php?deleted=question", true);
			}
		}
	}
	
//Update a session to go to different steps
	if (isset ($_POST['back'])) {
		$_SESSION['step'] = "testSettings";
		header ("Location: test_settings.php");
		exit;
	}
	
	if (isset ($_POST['next'])) {
		$_SESSION['step'] = "testVerify";
		header ("Location: test_verify.php");
		exit;
	}
	
	if (isset ($_POST['modify'])) {
		header ("Location: modify.php?updated=testContent");
		exit;
	}

//Set a session to allow test settings to be modified
	$_SESSION['testSettings'] = "modify";
	
//Title
	navigation("Test Content", "Content may be added to the test by using the guide below.");
	
//Admin toolbar
	echo "<div class=\"toolBar noPadding\">";
	form("jump");
	echo "<span class=\"toolBarItem noLink\">Add: ";
	dropDown("menu", "nenu", "- Select Question Type -,Description,Essay,File Response, Fill in the Blank, Matching, Multiple Choice, Short Answer, True or False, Import from Question Bank", ",../questions/description.php,../questions/essay.php,../questions/file_response.php,../questions/blank.php,../questions/matching.php,../questions/multiple_choice.php,../questions/short_answer.php,../questions/true_false.php,../questions/question_bank.php");
	button("submit", "submit", "Go", "button", false, " onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\"");
	echo "</span>";
	echo URL("Help", "help.php", "toolBarItem help");
	closeForm(false, false); 
	echo "</div>";
	
//Display message updates
	message("inserted", "question", "success", "The question was successfully inserted");
	message("updated", "question", "success", "The question was successfully updated");
	message("deleted", "question", "success", "The question was successfully deleted");
	
//Questions table
	if (exist($monitor['testTable']) == true) {
		$testDataGrabber = mysql_query("SELECT * FROM `{$monitor['testTable']}` ORDER BY `position` ASC", $connDBA);
		
		echo "<table class=\"dataTable\"><tbody><tr><th width=\"100\" class=\"tableHeader\">Order</th><th width=\"150\" class=\"tableHeader\">Type</th><th width=\"100\" class=\"tableHeader\">Point Value</th><th class=\"tableHeader\">Question</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
			
		while ($testData = mysql_fetch_array($testDataGrabber)) {
		//Select the external data, if needed
			if ($testData['questionBank'] == "1") {
				$linkID = $testData['linkID'];
				$importedQuestionGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$linkID}' LIMIT 1", $connDBA);
				$importedQuestion = mysql_fetch_array($importedQuestionGrabber);
				
				$type = $importedQuestion['type'];
				$points = $importedQuestion['points'];
				$extraCredit = $importedQuestion['extraCredit'];
				$question = $importedQuestion['question'];
			} else {
				$type = $testData['type'];
				$points = $testData['points'];
				$extraCredit = $testData['extraCredit'];
				$question = $testData['question'];
			}
			
			echo "<tr";
			if ($testData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"75\">"; reorderMenu($testData['id'], $testData['position'], "testData", $monitor['testTable']); echo "</td>";
			echo "<td width=\"150\">" . URL($type, "preview_question.php?id=" . $testData['id'], false, false, "Preview this <strong>" . $type . "</strong> question", false, true, "640", "480") . "</td>";
			echo "<td width=\"100\"><div";
			
			if ($extraCredit == "on") {
				echo " class=\"extraCredit\"";
			}
			
			echo ">" . $points;
			
			if ($points == "1") {
				echo " Point";
			} else {
				echo " Points";
			}
			
			echo "</div></td>";
			echo "<td>" . commentTrim(55, $question) . "</td>";
			echo "<td width=\"50\">";
			
			if (isset($importedQuestion)) {
				echo URL(false, "question_merge.php?type=import&questionID=" . $testData['id'] . "&bankID=" . $testData['linkID'], "action edit", false, "Edit this <strong>" . $type . "</strong> question", false, false, false, false, " onclick=\"return confirm('This question is currently located in the question bank. Once you edit this question, it will no long be linked to the question bank. Do you want to import and edit this question inside of the test? Click OK to continue.')\"");
			} else {
				$URL = "../questions/";
				
				switch ($testData['type']) {
					case "Description" : $URL .= "description.php"; break;
					case "Essay" : $URL .= "essay.php"; break;
					case "File Response" : $URL .= "file_response.php"; break;
					case "Fill in the Blank" : $URL .= "blank.php"; break;
					case "Matching" : $URL .= "matching.php"; break;
					case "Multiple Choice" : $URL .= "multiple_choice.php"; break;
					case "Short Answer" : $URL .= "short_answer.php"; break;
					case "True False" : $URL .= "true_false.php"; break;
				}
				
				$URL .= "?id=" . $testData['id'];
				
				echo URL(false, $URL, "action edit", false, "Edit this <strong>" . $type . "</strong> question");
			}
				
			echo "<td width=\"50\">" . URL(false, "test_content.php?id=" .  $testData['id'] . "&action=delete", "action delete", false, "Delete this <strong>" . $type . "</strong> question", true) . "</td>";
			echo "</tr>";
			
		//Unset the $importedQuestion, $type, $points, $extraCredit, and $question variables
			unset($importedQuestion);
			unset($type);
			unset($points);
			unset($extraCredit);
			unset($question);
		}
		
		echo "</tbody></table>";
	} else {
		echo "<div class=\"noResults\">There are no test questions. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>";
	}
	
//Display navigation buttons
	form("navigate");
	echo "<blockquote>";

	if (isset ($_SESSION['review'])) {
		if (exist($monitor['testTable']) == true) {
			button("submit", "submit", "Modify Content", "submit");
			button("cancel", "cancel", "Cancel", "cancel", "modify.php");
		}
	} else {
		button("back", "back", "&lt;&lt; Previous Step", "submit");
		
		if (exist($monitor['testTable']) == true) {
			button("next", "next", "Next Step &gt;&gt;", "submit");
		}
	}
	
	echo "</blockquote>";
	closeForm(false, false);
	
//Include the footer
	footer();
?>