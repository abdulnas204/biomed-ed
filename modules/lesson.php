<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grab all module data
	if (isset ($_GET['id'])) {
		$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}' LIMIT 1");
		
		if (exist("moduledata", "id", $_GET['id']) == false) {
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
		lesson($_GET['id'], "modulelesson_1", false);
	} else {
		if (loggedIn()) {
			$userData = userData();
		}
		
		echo $moduleInfo['comments'] . "<div class=\"spacer\">";
		
		if (loggedIn() == false || !in_array($moduleInfo['id'], unserialize($userData['modules']))) {
			$price = str_replace(".", "", $moduleInfo['price']);
			
			if (!empty($moduleInfo['enablePrice']) && !empty($moduleInfo['price']) && $price > 0) {
				form("purchase", "post", false, false, "enroll/cart.php");
				hidden("cart[]", "cart", $moduleInfo['id']);
				button("submit", "submit", false, "image", "../system/images/common/cartAdd.png");
				closeForm(false, false);
			}
		} else {
			button("beginLesson", "beginLesson", "Begin Lesson", "button", $_SERVER['REQUEST_URI'] . "&page=1");
		}
		
		echo "</div>";
	}
	
//Include the footer
	footer();
?>