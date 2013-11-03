<?php 
//Header functions
	require_once('../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Grab all module data
	if (isset ($_GET['id'])) {
		$moduleLesson = "modulelesson_" . $_GET['id'];
		$lessonID = $_GET['id'];
		$userData = userData();
		$userID = $userData['id'];
		$modules = unserialize($userData['modules']);
		$moduleInfo = query("SELECT * FROM `learningunits` WHERE `id` = '{$lessonID}' LIMIT 1");
		$lessonUpdateGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userID}' LIMIT 1");
		$lessonUpdateArray = unserialize($lessonUpdateGrabber['modules']);
		
		if (exist("moduledata", "id", $lessonID) == false || ($_SESSION['MM_UserGroup'] != "Site Administrator" && empty($moduleInfo['visible']))) {
			//redirect("index.php");
		}
		
		if (isset($_GET['page']) && !access("modifyModule")) {			
			if (!array_key_exists($lessonID, $lessonUpdateArray)) {
				//redirect($_SERVER['PHP_SELF'] . "?id=" . $lessonID);
			}
			
			if ($lessonUpdateArray[$lessonID]['moduleStatus'] == "F" && $lessonUpdateArray[$lessonID]['moduleStatus'] != "F" && $lessonUpdateArray[$lessonID]['moduleStatus'] != "A" && $moduleInfo['reference'] == "0") {
				redirect("test.php?id=" . $lessonID);
			}
		}
	} else {
		redirect("index.php");
	}
	
//Allow access to the test
	if (loggedIn() && isset($_GET['action']) && $_GET['action'] == "retake") {		
		if ($modules[$lessonID]['moduleStatus'] == "F" && $modules[$lessonID]['testStatus'] == "F" && $attempts['attempt'] < $moduleInfo['attempts']) {
			$modules[$lessonID]['testStatus'] = "O";
			$update = serialize($modules);
			query("UPDATE `users` SET `modules` = '{$update}' WHERE `id` = '{$userID}'");
			redirect("test.php?id=" . $lessonID);
		}
	}

//Top content
	headers($moduleInfo['name'], "navigationMenu,plugins");
	

//Information bar
	if (!isset($_GET['page'])) {
		$category = query("SELECT * FROM `modulecategories` WHERE `id` ='{$moduleInfo['category']}'");
		$employee = query("SELECT * FROM `moduleemployees` WHERE `id` ='{$moduleInfo['employee']}'");
		
		$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$numberArray = array("0","1","2","3","4","5","6","7","8","9");
		$time = str_replace($letterArray, "", $moduleInfo['timeFrame']);
		$timeLabel = str_replace($numberArray, "", $moduleInfo['timeFrame']);
		
		title($moduleInfo['name'], false, false);
		echo "<div class=\"toolBar noPadding\"><strong>Due Date:</strong> " . $time . " " .$timeLabel . "<br /><strong>Category:</strong> " . stripslashes($category['category']) . "<br /><strong>Intended Employee Type:</strong> " . stripslashes($employee['employee']) . "<br /><strong>Difficulty:</strong> " . $moduleInfo['difficulty'] . "</div>";
	}
	
//Display the lesson
	lesson($lessonID, "lesson_29", false);
	
//Include the footer
	footer();
?>