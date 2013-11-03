<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grab all module data
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$moduleInfoGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `id` = '{$id}' LIMIT 1", $connDBA);
		$moduleInfo = mysql_fetch_array($moduleInfoGrabber);
		
		if (exist("moduledata", "id", $id) == false) {
			redirect("index.php");
		}
	} else {
		redirect("index.php");
	}

//Top content
	if (!isset($_GET['page'])) {
		headers($moduleInfo['name'], false);
	} else {
		headers($moduleInfo['name'], "Student,Site Administrator");
	}

//Information bar
	if (!isset($_GET['page'])) {
		$categoryID = $moduleInfo['category'];
		$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` WHERE `id` = '{$categoryID}'", $connDBA);
		$category = mysql_fetch_array($categoryGrabber);
		$employeeID = $moduleInfo['employee'];
		$employeeGrabber = mysql_query("SELECT * FROM `moduleemployees` WHERE `id` = '{$employeeID}'", $connDBA);
		$employee = mysql_fetch_array($employeeGrabber);
		
		$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$numberArray = array("0","1","2","3","4","5","6","7","8","9");
		$time = str_replace($letterArray, "", $moduleInfo['timeFrame']);
		$timeLabel = str_replace($numberArray, "", $moduleInfo['timeFrame']);
		
		title($moduleInfo['name'], false, false);
		echo "<div class=\"toolBar noPadding\"><strong>Due Date:</strong> " . $time . " " .$timeLabel . "<br /><strong>Category:</strong> " . stripslashes($category['category']) . "<br /><strong>Intended Employee Type:</strong> " . stripslashes($employee['employee']) . "<br /><strong>Difficulty:</strong> " . $moduleInfo['difficulty'] . "</div>";
	}
	
//Display the lesson
	if (isset($_GET['page'])) {
		lesson($_GET['id'], "modulelesson_1", false);
	} else {
		echo $moduleInfo['comments'] . "<div class=\"spacer\">";
		button("beginLesson", "beginLesson", "Begin Lesson", "button", $_SERVER['REQUEST_URI'] . "&page=1");
		echo "</div>";
	}
	
//Include the footer
	footer();
?>