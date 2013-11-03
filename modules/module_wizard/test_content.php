<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Test Content", "navigationMenu");

//Reorder questions
	reorder("{$monitor['testTable']}", "test_content.php");
	
//Delete a test question
	if (isset ($_GET['id']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$deleteGrabber = mysql_query("SELECT * FROM `{$monitor['testTable']}` WHERE `id` = '{$id}'", $connDBA);
		$delete = mysql_fetch_array($deleteGrabber);
		
		if ($deleteGrabber) {
			if ($delete['type'] == "File Response") {
				delete($monitor['testTable'], "test_content.php", true, $monitor['directory'] . "/test/answers/" . $delete['fileURL']);
			} else {
				delete($monitor['testTable'], "test_content.php", true);
			}
		}
	}
	
//Title
	navigation("Test Content", "Content may be added to the test by using the guide below.");
	
//Admin toolbar
	echo "<div class=\"toolBar noPadding\">";
	form("jump");
	echo "<span class=\"toolBarItem noLink\">Add: ";
	dropDown("menu", "nenu", "- Select Question Type -,Description,Essay,File Response, Fill in the Blank, Matching, Multiple Choice, Short Answer, True or False, Import from Question Bank", ",../questions/description.php,../questions/essay.php,../questions/file_response.php,../questions/blank.php,../questions/matching.php,../questions/multiple_choice.php,../questions/short_answer.php,../questions/true_false.php,../questions/question_bank.php");
	button("submit", "submit", "Go", "button", false, " onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\"");
	echo "</span>";
	closeForm(false, false); 
	echo "</div>";
	
//Display message updates
	message("inserted", "question", "success", "The question was successfully inserted");
	message("updated", "question", "success", "The question was successfully updated");
	
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
			echo "<td width=\"75\"><div";
			
			if ($testData['questionBank'] == "1") {
				echo " class=\"questionBank\"";
			}
			
			echo ">"; reorderMenu($testData['id'], $testData['position'], "testData", $monitor['testTable']); echo "</div></td>";
			echo "<td width=\"150\">" . URL($type, "preview_question.php?id=" . $testData['id'], false, true, "Preview this <strong>" . $type . "</strong> question", false, true, "640", "480") . "</td>";
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
				echo URL(false, "question_merge.php?type=import&questionID=" . $testData['id'] . "&bankID=" . $testData['linkID'], "action edit", false, "Edit this <strong>" . $type . "</strong> question", false, false, false, false, "return confirm('This question is currently located in the question bank. Once you edit this question, it will no long be linked to the question bank. Do you want to import and edit this question inside of the test? Click OK to continue.')");
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
			
			echo "</td>";
			echo "<td width=\"50\">" . URL(false, "test_content.php?id=" .  $testData['id'] . "&action=delete", "action delete", false, "Delete this <strong>" . $type . "</strong> question", true) . "</td>";
			echo "</tr>";
			
		//Unset variables used in this loop
			unset($importedQuestion, $type, $points, $extraCredit, $question);
		}
		
		echo "</tbody></table>";
	} else {
		echo "<div class=\"noResults\">There are no test questions. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>";
	}
	
//Display navigation buttons
	echo "<blockquote>";
	button("back", "back", "&lt;&lt;  Previous Step", "button", "test_settings.php");
	
	if (exist($monitor['testTable']) == true) {
		button("next", "next", "Next Step &gt;&gt;", "button", "test_verify.php");
		
		if (isset ($_SESSION['review'])) {
			button("submit", "submit", "Finish", "button", "../index.php?updated=module");
		}
	}
	
	echo "</blockquote>";
	
//Include the footer
	footer();
?>