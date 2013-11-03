<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Process the given data
	if (isset($_GET['linkID'])) {
		$questionData = query("SELECT * FROM `questionbank` WHERE `id` = '{$_GET['linkID']}'");
		
		if ($questionData) {
			if (exist("moduledata")) {
				$location = "<fieldset><legend>This question appears in the following test(s):</legend><ul>";
				$testDataGrabber = query("SELECT * FROM `moduledata`", "raw");
				
				while ($testData = mysql_fetch_array($testDataGrabber)) {
					$testID = $testData['id'];
					$testInfo = query("SELECT * FROM `moduletest_{$testID}` WHERE `linkID` = '{$_GET['linkID']}'", false, false);
					
					if ($testInfo) {
						$location .= "<li>" . $testData['name'] . ", Question Number " . $testInfo['position'] . "</li>";
					}
				}
				
				$location .= "</ul></fieldset>";
				$title = "Results for the " . $questionData['type'] . " Question";
			} else {
				$title = "No Results Found";
			}
		} else {
			$title = "No Results Found";
		}
	} else {
		redirect("index.php");
	}
	
//Top content
	headers($title, "Site Administrator");

//Title
	title($title, "This discovery page will show in which tests questions from the question bank are used.");
	
//Page content
	if (isset($location) && $location != "<fieldset><legend>This question appears in the following test(s):</legend><ul></ul></fieldset>") {	
		echo $location;
	} else {
		echo errorMessage("This question does not appear in any tests.");
	}
	
	echo "<blockquote><p>";
	button("finish", "finish", "Finish", "history");
	echo "</p></blockquote>";
	
//Include the footer
	footer();
?>