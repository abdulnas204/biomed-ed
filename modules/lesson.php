<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grab all module data
	if (isset ($_GET['id'])) {
		$moduleLesson = "modulelesson_" . $_GET['id'];
		$lessonID = $_GET['id'];
		$userData = userData();
		$userID = $userData['id'];
		$modules = unserialize($userData['modules']);
		$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$lessonID}' LIMIT 1");
		$lessonUpdateGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userID}' LIMIT 1");
		$lessonUpdateArray = unserialize($lessonUpdateGrabber['modules']);
		
		if (exist("moduledata", "id", $lessonID) == false || ($_SESSION['MM_UserGroup'] != "Site Administrator" && empty($moduleInfo['visible']))) {
			redirect("index.php");
		}
		
		if (isset($_GET['page']) && $_SESSION['MM_UserGroup'] != "Site Administrator") {			
			if (!array_key_exists($lessonID, $lessonUpdateArray)) {
				redirect($_SERVER['PHP_SELF'] . "?id=" . $lessonID);
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
	if (!isset($_GET['page'])) {
		headers($moduleInfo['name'], false);
	} else {
		headers($moduleInfo['name'], "Student,Site Administrator");
	}
	
//Process the form	
	if ($_SESSION['MM_UserGroup'] != "Site Administrator") {
		if (isset($_GET['page']) && $lessonUpdateArray[$lessonID]['moduleStatus'] != "F") {
			$lessonUpdateArray[$lessonID]['moduleStatus'] = "O";
			$lessonUpdate = serialize($lessonUpdateArray);
			
			query("UPDATE `users` SET `modules` = '{$lessonUpdate}' WHERE `id` = '{$userID}'");
		}
		
		if (isset($_GET['action']) && $lessonUpdateArray[$lessonID]['testStatus'] == "C") {
			$lessonUpdateGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userID}' LIMIT 1");
			$lessonUpdateArray = unserialize($lessonUpdateGrabber['modules']);
			
			if (exist("moduletest_" . $lessonID)) {
				$lessonUpdateArray[$lessonID]['moduleStatus'] = "F";
				$lessonUpdate = serialize($lessonUpdateArray);
				
				query("UPDATE `users` SET `modules` = '{$lessonUpdate}' WHERE `id` = '{$userID}'");
				redirect("test.php?id=" . $lessonID);
			} else {
				$lessonUpdateArray[$lessonID]['moduleStatus'] = "F";
				$lessonUpdateArray[$lessonID]['testStatus'] = "F";
				$lessonUpdate = serialize($lessonUpdateArray);
				
				query("UPDATE `users` SET `modules` = '{$lessonUpdate}' WHERE `id` = '{$userID}'");
				redirect("index.php?complete=" . $lessonID);
			}
		}
	}

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
	if (isset($_GET['page'])) {
		lesson($lessonID, $moduleLesson, false);
	} else {		
		echo $moduleInfo['comments'] . "<div class=\"spacer\">";
		
		if (!loggedIn() || (loggedIn() && $_SESSION['MM_UserGroup'] != "Site Administrator" && (!is_array($modules) || !array_key_exists($moduleInfo['id'], $modules)))) {
			$price = str_replace(".", "", $moduleInfo['price']);
			
			if (!empty($moduleInfo['enablePrice']) && !empty($moduleInfo['price']) && $price > 0) {
				form("purchase", "post", false, "enroll/cart.php");
				hidden("cart[]", "cart", $moduleInfo['id']);
				button("submit", "submit", false, "image", "../system/images/common/cartAdd.png");
				closeForm(false, false);
			}
		} else {
			if ($_SESSION['MM_UserGroup'] != "Site Administrator") {
				if ($modules[$lessonID]['moduleStatus'] == "C") {
					button("beginLesson", "beginLesson", "Begin Lesson", "button", $_SERVER['REQUEST_URI'] . "&page=1");
				} elseif ($modules[$lessonID]['moduleStatus'] == "O") {
					button("continueLesson", "continueLesson", "Continue Lesson", "button", $_SERVER['REQUEST_URI'] . "&page=1");
				} elseif ($modules[$lessonID]['moduleStatus'] == "F" && $modules[$lessonID]['testStatus'] != "F") {
					button("Test", "continueTest", "Continue Test", "button", "test.php?id=" . $lessonID);
				} elseif ($modules[$lessonID]['moduleStatus'] == "F" && $modules[$lessonID]['testStatus'] == "F") {
					button("reviewLesson", "reviewLesson", "Review Lesson", "button", $_SERVER['REQUEST_URI'] . "&page=1");
					
					$attempts = query("SELECT * FROM `testdata_{$userID}` WHERE `testID` = '{$lessonID}' ORDER BY `attempt` DESC LIMIT 1");
					
					if ($attempts['attempt'] < $moduleInfo['attempts']) {
						if($moduleInfo['attempts'] == "999") {
							$message = "You make take this test an unlimited number of times. Click &quot;OK&quot; to continue.";
						} else {
							$attemptsLeft = $moduleInfo['attempts'] - $attempts['attempt'];
							$message = "You make take this test " . $attemptsLeft . " more times. Click &quot;OK&quot; to continue.";
						}
						
						button("submit", "submit", "Retake Test", "button", "lesson.php?id=" . $lessonID . "&action=retake", " onclick=\"return confirm('" . $message . "')\"");
					}
				}
			} else {
				button("previewLesson", "previewLesson", "Preview Lesson", "button", $_SERVER['REQUEST_URI'] . "&page=1");
			}
		}
		
		echo "</div>";
	}
	
//Include the footer
	footer();
?>