<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grab all module and test data
	if (isset ($_GET['id'])) {
		$moduleInfo = query("moduledata", "id", $_GET['id'], false, false, false, "1");
		
		if (exist("moduledata", "id", $_GET['id']) == false) {
			redirect("index.php");
		}
	} else {
		redirect("index.php");
	}
	
	$userData = userData();
	
//If the test is left unconfigured, then prompt the user to configure it before taking the test
	if (query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = {$_GET['id']}")) {		
		/*
	//Top content
		headers($moduleInfo['name'] . " Configuration", "Student,Site Administrator");
		
	//Generate the test configuration
		$testQuestionsGrabber = mysql_query("SELECT * FROM `moduletest_{$_GET['id']}`", $connDBA);
		$testDifficultyGrabber = mysql_query("SELECT * FROM `moduletest_{$_GET['id']}`", $connDBA);
		$questions = "";
		$difficulty = "";
		$count = 1;
		
		while ($testDifficulty = mysql_fetch_array($testDifficultyGrabber)) {
			if ($testDifficulty['difficulty'] == "Easy") {
				$easy = true;
			} elseif ($testDifficulty['difficulty'] == "Average") {
				$average = true;
			} elseif ($testDifficulty['difficulty'] == "Difficult") {
				$difficult = true;
			}
		}
		
		if (isset($easy)) {
			$difficulty .= "Easy,";
		}
		
		if (isset($average)) {
			$difficulty .= "Average,";
		}
		
		if (isset($difficult)) {
			$difficulty .= "Difficult,";
		}
		
		if (strlen($difficulty) <= 10) {
			while ($testQuestions = mysql_fetch_array($testQuestionsGrabber)) {
				$questions .= $count++ . ",";
			}
			
			$lastQuestion = query("moduletest_" . $_GET['id'], false, false, "position", "DESC", false, "1");
		} else {
			if (strlen($difficulty) == 13) {
				$config = array("Easy", "Average");
			}
			
			if (strlen($difficulty) == 15) {
				$config = array("Easy", "Difficult");
			}
			
			if (strlen($difficulty) == 18) {
				$config = array("Average", "Difficult");
			}
			
			if (strlen($difficulty) > 18) {
				$config = array("Average", "Difficult");
			}
			
			while ($testQuestions = mysql_fetch_array($testQuestionsGrabber)) {				
				if (in_array($testQuestions['difficulty'], $config)) {
					$questions .= $count++ . ",";
				}
			}
		}
		
	//Title
		title($moduleInfo['name'] . " Configuration", "Please configure this test to best suit your needs prior to starting. Keep in mind that once these settings are set, then cannot be changed for this test.");
		
	//Configuration form
		form("configuration");
		catDivider("Configuration", "one", true);
		echo "<blockquote>";
		directions("Difficulty", false);
		echo "<blockquote><p>";
		dropDown("difficulty", "difficulty", rtrim($difficulty, ","), rtrim($difficulty, ","), false, false, false, false);
		echo "</p></blockquote>";
		directions("Number of questions", false);
		echo "<blockquote><p>";
		dropDown("questions", "questions", rtrim($questions, ","), rtrim($questions, ","), false, false, false, $lastQuestion['position']);
		echo "</p></blockquote></blockquote>";		
		
		exit;*/
	}
	
//Top content
	headers($moduleInfo['name'], "Student,Site Administrator", "tinyMCESimple");
	
//Title
	title($moduleInfo['name'], false, false);
	
//Information bar
	echo "<div class=\"toolBar noPadding\"><strong>Directions</strong>: " . strip_tags($moduleInfo['directions']);
	
//Display a forced completion alert
	if ($moduleInfo['forceCompletion'] == "on") {
		echo "<br /><strong>Force Completion</strong>: This test must be completed now, otherwise penalties will be applied";
	}

//Display a timer alert
	if ($moduleInfo['timer'] == "on") {
		$time = unserialize($moduleInfo['time']);
		
		if ($testInfo['time'] !== "") {
			$testH = $time['0'];
			$testM = $time['1'];
		}
		
		echo "<br /><strong>Time limit</strong>: This test must be completed within <strong>" . $time['0'];
		
		if ($time['0'] == "1") {
			echo " hour and ";
		} elseif ($testH !== "1") {
			echo " hours and ";
		}
		
		echo $time['1'] . " minutes</strong>, otherwise the test will close.";
	}
	
//Close the information bar
	echo "</div>";
	
//Display link back to the lesson, if premitted
	if ($moduleInfo['reference'] == "1") {
		echo "<br /><div align=\"left\">" . URL("Back to Lesson", "lesson.php?id=" . $_GET['id'], "previousPage") . "</div>";
	} else {
		echo "<p>&nbsp;</p>";
	}
	
//Display the test
	test("moduletest_" . $_GET['id'], "../../gateway.php/modules/" . $_GET['id'] . "/", false);

//Display link back to the lesson, if premitted
	if ($moduleInfo['reference'] == "1") {
		echo "<br /><div align=\"left\">" . URL("Back to Lesson", "lesson.php?id=" . $_GET['id'], "previousPage") . "</div>";
	} else {
		echo "<p>&nbsp;</p>";
	}
	
//Include the footer
	footer();
?>