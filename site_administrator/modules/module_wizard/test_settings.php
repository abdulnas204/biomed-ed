<?php
//Header functions
	require_once('../../../Connections/connDBA.php');
	$monitor = monitor("Test Settings", "tinyMCESimple,validate,liveError,showHide,enableDisable,navigationMenu");

//Grab the form data
	$testDataGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'", $connDBA);
	$testData = mysql_fetch_array($testDataGrabber);
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['testName']) && !empty($_POST['directions']) && is_numeric($_POST['score']) && !empty($_POST['attempts']) && is_numeric($_POST['delay']) && !empty($_POST['gradingMethod']) && is_numeric($_POST['penalties']) && is_numeric($_POST['reference']) && !empty($_POST['randomizeAll']) && is_numeric($_POST['questionBank'])) {
		$testName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['testName']));
		$directions = mysql_real_escape_string($_POST['directions']);
		$score = $_POST['score'];
		$attempts = $_POST['attempts'];
		$forceCompletion = $_POST['forceCompletion'];
		$completionMethod = $_POST['completionMethod'];
		$reference = $_POST['reference'];
		$delay = $_POST['delay'];
		$gradingMethod = $_POST['gradingMethod'];
		$penalties = $_POST['penalties'];
		$randomizeAll = $_POST['randomizeAll'];
		$questionBank = $_POST['questionBank'];
		$display = serialize($_POST['display']);
		
	//Check to see if the timer is set and if the time does not equal zero
		if (isset($_POST['timer']) && isset($_POST['timeHours']) && isset($_POST['timeMinutes'])) {
			if ($_POST['timer'] == "on" && $_POST['timeHours'] == "0" && $_POST['timeMinutes'] == "00") {
				$time = serialize(array("0", "00"));
				$timer = "0";
			} else {	
				$timeHours = $_POST['timeHours'];
				$timeMinutes = $_POST['timeMinutes'];
				$time = serialize(array($timeHours, $timeMinutes));
				$timer = "on";
			}
		} else {
			$timeValue = serialize(array("0", "00"));
			$timer = "0";
		}		
		
		//Execute command on database			
		mysql_query("UPDATE `{$monitor['parentTable']}` SET `testName` = '{$testName}', `directions` = '{$directions}', `score` = '{$score}', `attempts` = '{$attempts}', `forceCompletion` = '{$forceCompletion}', `completionMethod` = '{$completionMethod}', `reference` = '{$reference}', `delay` = '{$delay}', `gradingMethod` = '{$gradingMethod}', `penalties` = '{$penalties}', `time` = '{$time}', `timer` = '{$timer}', `randomizeAll` = '{$randomizeAll}', `questionBank` = '{$questionBank}', `display` = '{$display}' WHERE `id` = '{$monitor['currentModule']}'", $connDBA);
			
		redirect("question_merge.php");
	}
	
//Update a session to go to previous steps
	if (isset ($_GET['goTo']) && $_GET['goTo'] == "previous") {
		$_SESSION['step'] = "lessonVerify";
		redirect("lesson_verify.php");
	}
	
//Title
	navigation("Test Settings", "Setup the test's initial settings, such as the name, directions, and score.");
	
//Test settings form
	form("testSettings");
	step("Test Information", "six", "one", true);
	echo "<blockquote>";
	directions("Test name", true, "The name of this test");
	echo "<blockquote><p>";
	
	if (empty($testData['testName'])) {
		textField("testName", "testName", false, false, false, true, false, false, "testData", "name");
	} else {
		textField("testName", "testName", false, false, false, true, false, false, "testData", "testName");
	}
	
	echo "</p></blockquote>";
	directions("Directions", true, "The directions of this test");
	echo "<blockquote><p>";
	textArea("directions", "directions", "small", true, false, false, "testData", "directions");
	echo "</p></blockquote></blockquote>";
	
	step("Test Settings", "seven", "two");
	echo "<blockquote>";
	directions("Passing score", false, "The minimum score a user must obtain to pass");
	echo "<blockquote><p>";
	$valuesGenerate = "";
	
	for ($count = 1; $count <= 100; $count++) {
		$valuesGenerate .= $count . ",";
	}
	
	$values = rtrim($valuesGenerate, ",");
	dropDown("score", "score", $values, $values, false, false, false, false, "testData", "score");
	echo "</p></blockquote>";
	directions("Number of attempts", false, "The number of times a user may take this test");
	echo "<blockquote><p>";
	dropDown("attempts", "attempts", "Unlimited,1,2,3,4,5,6,7,8,9,10", "999,1,2,3,4,5,6,7,8,9,10", false, false, false, false, "testData", "attempts", " onchange=\"toggleTestOptions(this.value);\"");
	echo "</p></blockquote><div id=\"contentHide\"";
	
	if ($testData['attempts'] == "1") {
		echo " class=\"contentHide\">";
	} else {
		echo " class=\"contentShow\">";
	}
	
	directions("Delay between attempts", false, "Set the amount of time a user must wait between attempts before retaking the test");
	echo "<blockquote><p>";
	dropDown("delay", "delay", "None,30 minutes,60 minutes,2 hours,3 hours,4 hours,5 hours,6 hours,7 hours,8 hours,9 hours,10 hours,11 hours,12 hours,13 hours,14 hours,15 hours,16 hours,17 hours,18 hours,19 hours,20 hours,21 hours,22 hours,23 hours,24 hours,2 days,3 days,4 days,5 days,6 days,7 days", "0,1800,3600,7200,10800,14400,18000,21600,25200,28800,32400,36000,39600,43200,46800,50400,54000,57600,61200,64800,68400,72000,75600,79200,82800,86400,172800,259200,345600,432000,518400,604800", false, false, false, false, "testData", "delay");
	echo "</p></blockquote>";
	directions("Grading method", false, "Set how the test will be scored");
	echo "<blockquote><p>";
	radioButton("gradingMethod", "gradingMethod", "Highest Grade,Average Grade,First Attempt,Last Attempt", "Highest Grade,Average Grade,First Attempt,Last Attempt", false, false, false, false, "testData", "gradingMethod");
	echo "</p></blockquote>";
	directions("Show penalties", false, "Set whether or not all attempts will show in the <br />gradebook, regardless of past scores");
	echo "<blockquote><p>";
	radioButton("penalties", "penalties", "Yes,No", "1,0", true, false, false, false, "testData", "penalties");
	echo "</p></blockquote></div>";
	directions("Timer", false, "Sets a timer, which will only allow the test to be open for a set duration");
	$time = unserialize($testData['time']);
	$testH = $time['0'];
	$testM = $time['1'];
	echo "<blockquote><p>Hours: ";
	
	if (empty($testData['timer'])) {
		dropDown("timeHours", "timeHours", "0,1,2,3,4,5", "0,1,2,3,4,5", false, false, false, false, "time", "0", " disabled=\"disabled\"");
		echo " Minutes: ";
		dropDown("timeMinutes", "timeMinutes", "00,05,10,15,20,25,30,35,40,45,50,55", "00,05,10,15,20,25,30,35,40,45,50,55", false, false, false, false, "time", "1", " disabled=\"disabled\"");
	} else {
		dropDown("timeHours", "timeHours", "0,1,2,3,4,5", "0,1,2,3,4,5", false, false, false, false, "time", "0");
		echo " Minutes: ";
		dropDown("timeMinutes", "timeMinutes", "00,05,10,15,20,25,30,35,40,45,50,55", "00,05,10,15,20,25,30,35,40,45,50,55", false, false, false, false, "time", "1");
	}
	
	echo " ";
	checkbox("timer", "timer", "Enable", false, false, false, false, "testData", "timer", "on", " onclick=\"flvFTFO1('testSettings','timeHours,t','timeMinutes,t')\"");
	echo "</p></blockquote>";
	directions("Force completion", false, "Set if this test must be completed the first time it is opened, <br />and what penalties will be applied");
	echo "<blockquote><p>";
	echo "Penalties: ";
	
	if (empty($testData['forceCompletion'])) {
		dropDown("completionMethod", "completionMethod", "The test will close,All answers will reset,Grade decreases 10%,Grade decreases 20%,Grade decreases 30%,Grade decreases 40%", "0,1,10,20,30,40", false, false, false, false, "testData", "completionMethod", " disabled=\"disabled\"");
	} else {
		dropDown("completionMethod", "completionMethod", "The test will close,All answers will reset,Grade decreases 10%,Grade decreases 20%,Grade decreases 30%,Grade decreases 40%", "0,1,10,20,30,40", false, false, false, false, "testData", "completionMethod");
	}
	echo " ";
	checkbox("forceCompletion", "forceCompletion", "Enable", false, false, false, false, "testData", "forceCompletion", "on", " onclick=\"flvFTFO1('testSettings','completionMethod,t')\"");
	echo "</p></blockquote>";
	directions("Allow lesson reference", false, "Allow users to reference the lesson during the test");
	echo "<blockquote><p>";
	radioButton("reference", "reference", "Yes,No", "1,0", true, false, false, false, "testData", "reference");
	echo "</p></blockquote>";
	directions("Randomize questions", false, "Allow users to reference the lesson during the test");
	echo "<blockquote><p>";
	radioButton("randomizeAll", "randomizeAll", "Sequential Order,Randomize", "Sequential Order,Randomize", false, false, false, false, "testData", "randomizeAll");
	echo "</p></blockquote>";
	directions("Automatically pull questions from bank", false, "Set whether questions will be automatically pulled from <br />the question bank with the same questions in the same category when new ones are added");
	echo "<blockquote><p>";
	radioButton("questionBank", "questionBank", "Yes,No", "1,0", true, false, false, false, "testData", "questionBank");
	echo "<br /><br />";
	
	if (exist("questionbank", "category", $testData['category']) == true) {
		echo "The question bank has test questions for this category.";
	} else {
		echo "The question bank does not have any test questions for this category.";
	}
	
	echo "</p></blockquote>";
	directions("After the test is taken display", false, "Select what information will be displayed when the test is completed:<br/><br/><strong>Score:</strong> Display a breakdown of points that the user recieved on each quesiton<br/><strong>Selected Answers:</strong> The answer(s) the user selected in the test<br/><strong>Correct Answers:</strong> The correct answer(s) for each problem<br/><strong>Feedback:</strong> The comments the user will recieve based off their answer</li>");
	echo "<blockquote><p>";
	$values = unserialize($testData['display']);
	$firstValue = false;
	$secondValue = false;
	$thirdValue = false;
	$fourthValue = false;
	
	if (is_array($values)) {
		foreach($values as $checkbox) {
			switch ($checkbox) {
				case "1" : $firstValue = true; break;
				case "2" : $secondValue = true; break;
				case "3" : $thirdValue = true; break;
				case "4" : $fourthValue = true; break;
			}
		}
	}
	
	checkbox("display[]", "display[]", "Score", "1", false, false, $firstValue);
	echo "<br />";
	checkbox("display[]", "display[]", "Selected Answers", "2", false, false, $secondValue);
	echo "<br />";
	checkbox("display[]", "display[]", "Correct Answers", "3", false, false, $thirdValue);
	echo "<br />";
	checkbox("display[]", "display[]", "Feedback", "4", false, false, $fourthValue);
	echo "</p></blockquote></blockquote>";
	
	step("Submit", "eight", "three");
	echo "<blockquote><p>";
	
	if (isset ($_SESSION['review'])) {
		button("submit", "submit", "Modify Settings", "submit");
		button("cancel", "cancel", "Cancel", "cancel", "modify.php");
	} else {
		button("back", "back", "&lt;&lt; Previous Step", "cancel", "test_settings.php?goTo=previous");
		button("submit", "submit", "Next Step &gt;&gt;", "submit");
	}
	
	echo "</p></blockquote>";
	closeForm(true, true);
	
//Include the footer
	footer();
?>