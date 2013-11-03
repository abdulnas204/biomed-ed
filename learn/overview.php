<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the module exists
	if (isset ($_GET['id'])) {
		$organization = userData();
		$moduleData = exist("moduledata", "id", $_GET['id']);
		
		if (exist("moduledata", "id", $_GET['id']) && $moduleData['organization'] !== $organization['organization']) {
			if ($moduleData['locked'] == "1" || empty($moduleData['visible'])) {
				redirect("index.php");
			}
		} else {
			redirect("index.php");
		}
	}
	
	$title = "Overview of " . prepare($moduleData['name'], false, true);
	
	headers($title, "Organization Administrator", "tinyMCESimple");
	
//Create a copy of this module
	if (isset($_GET['action']) && $_GET['action'] == "modify") {
		query("INSERT INTO `moduledata` SELECT NULL, `position`, `locked`, `visible`, `name`, `category`, `employee`, `difficulty`, `timeFrame`, `comments`, `price`, `enablePrice`, `selected`, `skip`, `feedback`, `tags`, `searchEngine`, `test`, `testName`, `directions`, `score`, `attempts`, `forceCompletion`, `completionMethod`, `reference`, `delay`, `gradingMethod`, `penalties`, `time`, `timer`, `randomizeAll`, `questionBank`, `display`, `organization` FROM `moduledata` WHERE `id` = '{$_GET['id']}'");
		
		$id = mysql_insert_id();
		$position = lastItem("moduledata");
		
		query("UPDATE `moduledata` SET `position` = '{$position}' WHERE `id` = '{$id}'");
		
		$organizationPrep = userData();
		$organization = $organizationPrep['organization'];
		
		query("UPDATE `moduledata` SET `organization` = '{$organization}' WHERE `id` = '{$id}'");
		
		query("CREATE TABLE `modulelesson_{$id}` (
					  `id` int(255) NOT NULL AUTO_INCREMENT,
					  `position` int(100) NOT NULL,
					  `type` longtext NOT NULL,
					  `title` longtext NOT NULL,
					  `content` longtext NOT NULL,
					  `attachment` longtext NOT NULL,
					  PRIMARY KEY (`id`)
					 )
			  ");
					  
		query("INSERT INTO `modulelesson_{$id}` SELECT * FROM `modulelesson_{$_GET['id']}`");
			  
		query("CREATE TABLE `moduletest_{$id}` (
					  `id` int(255) NOT NULL AUTO_INCREMENT,
					  `questionBank` int(1) NOT NULL,
					  `linkID` int(255) NOT NULL,
					  `position` int(100) NOT NULL,
					  `type` longtext NOT NULL,
					  `points` int(3) NOT NULL,
					  `extraCredit` text NOT NULL,
					  `partialCredit` int(1) NOT NULL,
					  `difficulty` longtext NOT NULL,
					  `category` int(11) NOT NULL,
					  `link` longtext NOT NULL,
					  `randomize` int(1) NOT NULL,
					  `totalFiles` int(2) NOT NULL,
					  `choiceType` text NOT NULL,
					  `case` int(1) NOT NULL,
					  `tags` longtext NOT NULL,
					  `question` longtext NOT NULL,
					  `questionValue` longtext NOT NULL,
					  `answer` longtext NOT NULL,
					  `answerValue` longtext NOT NULL,
					  `fileURL` longtext NOT NULL,
					  `correctFeedback` longtext NOT NULL,
					  `incorrectFeedback` longtext NOT NULL,
					  `partialFeedback` longtext NOT NULL,
					  PRIMARY KEY (`id`)
					 )
			  ");
					 
		query("INSERT INTO `moduletest_{$id}` SELECT * FROM `moduletest_{$_GET['id']}`");
		
		mkdir($id, 0777);
		mkdir($id . "/lesson", 0777);
		mkdir($id . "/test", 0777);
		mkdir($id . "/test/answers", 0777);
		mkdir($id . "/test/responses", 0777);
		$lessonHandler = opendir($_GET['id'] . "/lesson");
		$testHandler = opendir($_GET['id'] . "/test/answers");
		
		while($lesson = readdir($lessonHandler)) {
			copy($_GET['id'] . "/lesson/" . $lesson, $id . "/lesson/" . $lesson);
		}
		
		while($test = readdir($testHandler)) {
			copy($_GET['id'] . "/test/answers/" . $test, $id . "/test/answers/" . $test);
		}
		
		$_SESSION['currentModule'] = $id;
		$_SESSION['review'] = "review";
		
		redirect("module_wizard/lesson_settings.php");
	}
	
//Title
	title($title, "Below is an overview of the configuration and content of " . prepare($moduleData['name'], false, true) . ". The settings and content can be changed to best suit this organization's needs. Click the &quot;Modify Module&quot; link to begin, and <strong>a copy of this module will be created</strong> to be customized as needed.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Modify Module", $_SERVER['REQUEST_URI'] . "&action=modify", "toolBarItem editTool", false, false, false, false, false, false, " onclick=\"return confirm('A copy of this module will be created to be customized as needed. Any changes that are made to this module will not be displayed globally. Click OK to continue.')\"");
	echo URL("Back to Modules", "index.php", "toolBarItem back");
	echo "</div><br />";
    
//Lesson settings
	catDivider("Lesson settings", "one", true);
	echo "<blockquote>";
	directions("Name");
	echo "<blockquote><p>";
	echo prepare($moduleData['name'], false, true);
	echo "</p></blockquote>";
	directions("Comments");
	echo "<blockquote><p>";
	echo prepare($moduleData['comments'], false, true);
	echo "</p></blockquote>";
	
	$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$numberArray = array("0","1","2","3","4","5","6","7","8","9");
	$time = str_replace($letterArray, "", $moduleData['timeFrame']);
	$timeLabel = str_replace($numberArray, "", $moduleData['timeFrame']);
	
	directions("Due date");
	echo "<blockquote><p>";
	echo "<strong>" . $time . " " . $timeLabel . "</strong> from scheduled date";
	echo "</p></blockquote>";
	
	if (exist("moduleCategories", "id", $moduleData['category'])) {
		$category = query("SELECT * FROM `moduleCategories` WHERE `id` = '{$moduleData['category']}'");
	} else {
		$category = "<span class=\"notAssigned\">Not Assigned</span>";
	}
	
	directions("Category");
	echo "<blockquote><p>";
	echo prepare($category['category'], false, true);
	echo "</p></blockquote>";
	
	if (exist("moduleEmployees", "id", $moduleData['employee'])) {
		$employee = query("SELECT * FROM `moduleEmployees` WHERE `id` = '{$moduleData['employee']}'");
	} else {
		$employee = "<span class=\"notAssigned\">Not Assigned</span>";
	}
	
	directions("Intended employee type");
	echo "<blockquote><p>";
	echo prepare($employee['employee'], false, true);
	echo "</p></blockquote>";
	directions("Difficulty");
	echo "<blockquote><p>" . $moduleData['difficulty'] . "</p></blockquote>";
	
	if ($moduleData['force'] == "1") {
		$force = "Yes";
	} else {
		$force = "No";
	}
	
	directions("Force module");
	echo "<blockquote><p>" . $force . "</p></blockquote>";
	
	if ($moduleData['skip'] == "1") {
		$skip = "Yes";
	} else {
		$skip = "No";
	}
	
	directions("Skip module");
	echo "<blockquote><p>" . $skip . "</p></blockquote></blockquote>";
	
//Lesson information
	catDivider("Lesson Content", "two");
	
	$lessonInfoGrabber = query("SELECT * FROM `modulelesson_{$_GET['id']}`", "raw");
	$count = query("SELECT * FROM `modulelesson_{$_GET['id']}`", "num");
	
	echo "<blockquote><p>Only the titles of each page are displayed below, " . URL("click here", "lesson.php?id=" . $_GET['id'], false, "_blank") . " to preview the entire content of this lesson.</p>";
	echo "<p>Total number of pages: <strong>" . $count . "</strong></p><ol>";
	
	while ($lessonInfo = mysql_fetch_array($lessonInfoGrabber)) {
		echo "<li>" . prepare($lessonInfo['title'], false, true) . "</li>";
	}
	
	echo "</ol></blockquote>";
	
//Display only if a test exists
	if ($moduleData['test'] == "1" && exist("moduletest_" . $_GET['id'], "position", "1")) {
		catDivider("Test Settings", "three");
		echo "<blockquote>";
		directions("Test Name");
		echo "<blockquote><p>";
		echo prepare($moduleData['testName'], false, true);
		echo "</p></blockquote>";
		directions("Directions");
		echo "<blockquote><p>";
		echo prepare($moduleData['directions'], false, true);
		echo "</p></blockquote>";
		directions("Passing score");
		echo "<blockquote><p>";
		echo $moduleData['score'] . "%";
		echo "</p></blockquote>";
		directions("Number of attempts");
		echo "<blockquote><p>";
		echo $moduleData['attempts'];
		echo "</p></blockquote>";
		
		if ($moduleData['attempts'] > 1) {
			$delay = array("0" => "None", "1800" => "30 minutes", "3600" => "1 hour", "7200" => "2 hours", "10800" => "3 hours", "14400" => "4 hours", "18000" => "5 hours", "21600" => "6 hours", "25200" => "7 hours", "28800" => "8 hours", "32400" => "9 hours", "36000" => "10 hours", "39600" => "11 hours", "43200" => "12 hours", "46800" => "13 hours", "50400" => "14 hours", "54000" => "15 hours", "57600" => "16 hours", "61200" => "17 hours", "64800" => "18 hours", "68400" => "19 hours", "72000" => "20 hours", "75600" => "21 hours", "79200" => "22 hours", "82800" => "23 hours", "86400" => "24 hours", "172800" => "2 days", "259200" => "3 days", "345600" => "4 days", "432000" => "5 days", "518400" => "6 days", "604800" => "7 days");
			
			directions("Delay between attempts");
			echo "<blockquote><p>";
			echo $delay[$moduleData['delay']];
			echo "</p></blockquote>";
			directions("Grading method");
			echo "<blockquote><p>";
			echo $moduleData['gradingMethod'];
			echo "</p></blockquote>";
			
			if ($moduleData['penalties'] == "1") {
				$penalties = "Yes";
			} else {
				$penalties = "No";
			}
			
			directions("Show penalties");
			echo "<blockquote><p>" . $penalties . "</p></blockquote>";
			
			if ($moduleData['timer'] == "on") {
				$time = arrayRevert($testData['time']);
				
				if ($time['0'] == 0) {
					$hours = "";
				} elseif ($time['0'] == 1) {
					$hours = "1 hour";
				} else {
					$hours = $time['0'] . " hours";
				}
				
				$timer = $hours . " and " . $time['1'] . " minutes";
			} else {
				$timer = "<span class=\"notAssigned\">Not set</span>";
			}
			
			directions("Timer");
			echo "<blockquote><p>" . $timer . "</p></blockquote>";
		}
		
		directions("Question order");
		echo "<blockquote><p>";
		echo $moduleData['randomizeAll'];
		echo "</p></blockquote>";
		
		$displayGrabber = arrayRevert($moduleData['display']);
		$displayPrep = "";
				
		if (is_array($displayGrabber) && !empty($displayGrabber)) {
			foreach($displayGrabber as $display) {
				switch ($display) {
					case "1" : $displayPrep .= "Score<br />"; break;
					case "2" : $displayPrep .= "Selected Answers<br />"; break;
					case "3" : $displayPrep .= "Correct Answers<br />"; break;
					case "4" : $displayPrep .= "Feedback"; break;
				}
			}
		} else {
			$displayPrep = "Nothing will be displayed";
		}
		
		$display = rtrim($displayPrep, "<br />");
		
		directions("After the test is taken display");
		echo "<blockquote><p>" . $display . "</p></blockquote></blockquote>";
		
		catDivider("Test Content", "four");
		echo "<blockquote>";
		test("moduletest_" . $_GET['id'], false, true);
		echo "</blockquote>";
	} else {
		catDivider("Test", "three");
		echo "<div class=\"noResults\">A test for this modules does not exist.</div>";
	}
	
	catDivider(false, false, false, true);
	
//Include the footer
	footer();
?>