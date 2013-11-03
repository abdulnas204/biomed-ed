<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Module Settings", "tinyMCESimple,validate,enableDisable,navigationMenu");
	require_once('../questions/functions.php');
	
//Grab the form data
	if (isset($_SESSION['currentModule'])) {
		$moduleDataGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'", $connDBA);
		$moduleData = mysql_fetch_array($moduleDataGrabber);
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['name']) && is_numeric($_POST['category']) && is_numeric($_POST['employee']) && !empty($_POST['difficulty']) && !empty($_POST['time']) && !empty($_POST['timeLabel']) && is_numeric($_POST['locked']) && is_numeric($_POST['selected']) && is_numeric($_POST['skip']) && is_numeric($_POST['feedback'])) {
		$name = mysql_real_escape_string($_POST['name']);
		$category = mysql_real_escape_string($_POST['category']);
		$employee = mysql_real_escape_string($_POST['employee']);
		$difficulty = $_POST['difficulty'];
		$time = $_POST['time'];
		$timeLabel = $_POST['timeLabel'];
		$comments = mysql_real_escape_string($_POST['comments']);
		$price = mysql_real_escape_string($_POST['price']);
		$enablePrice = mysql_real_escape_string($_POST['enablePrice']);
		$locked = $_POST['locked'];
		$selected = $_POST['selected'];
		$skip = $_POST['skip'];
		$feedback = $_POST['feedback'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$searchEngine = $_POST['searchEngine'];
		$timeFrame = $time . $timeLabel;
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
								
			mysql_query("UPDATE `{$monitor['parentTable']}` SET `locked` = '{$locked}', `name` = '{$name}', `category` = '{$category}', `employee` = '{$employee}', `difficulty` = '{$difficulty}', `timeFrame` = '{$timeFrame}', `comments` = '{$comments}', `price` = '{$price}', `enablePrice` = '{$enablePrice}', `selected` = '{$selected}', `skip` = '{$skip}', `feedback` = '{$feedback}', `tags` = '{$tags}', `searchEngine` = '{$searchEngine}' WHERE `id` = '{$id}'", $connDBA);
		} else {
			$lastModule = lastItem($monitor['parentTable']);
			$id = lastItem($monitor['parentTable']);
			
			mkdir($monitor['directory'] . $id, 0777);
			mkdir($monitor['directory'] . $id . "/lesson", 0777);
			mkdir($monitor['directory'] . $id . "/test", 0777);
			mkdir($monitor['directory'] . $id . "/test/answers", 0777);
			mkdir($monitor['directory'] . $id . "/test/responses", 0777);
			
			mysql_query("INSERT INTO `{$monitor['parentTable']}` (
						`id`, `position`, `locked`, `visible`, `name`, `category`, `employee`, `difficulty`, `timeFrame`, `comments`, `price`, `enablePrice`, `selected`, `skip`, `feedback`, `tags`, `searchEngine`, `test`, `testName`, `directions`, `score`, `attempts`, `forceCompletion`, `completionMethod`, `reference`, `delay`, `gradingMethod`, `penalties`, `timer`, `time`, `randomizeAll`, `questionBank`, `display`
						) VALUES (
						'{$id}', '{$position}', '{$locked}', '', '{$name}', '{$category}', '{$employee}', '{$difficulty}', '{$timeFrame}', '{$comments}', '{$price}',  '{$enablePrice}', '{$selected}', '{$skip}', '{$feedback}', '{$tags}', '{$searchEngine}', '0', '', '', '80', '1', '', '0', '0', '0', 'Highest Grade', '1', '', 'a:2:{i:0;s:1:\"0\";i:1;s:2:\"00\";}', 'Sequential Order', '0', 'a:1:{i:0;s:1:\"1\";}'
						)", $connDBA);
			
			mysql_query("CREATE TABLE IF NOT EXISTS `{$monitor['prefix']}modulelesson_{$id}` (
						  `id` int(255) NOT NULL AUTO_INCREMENT,
						  `position` int(100) NOT NULL,
						  `type` longtext NOT NULL,
						  `title` longtext NOT NULL,
						  `content` longtext NOT NULL,
						  `attachment` longtext NOT NULL,
						  PRIMARY KEY (`id`)
						)");
						
			$_SESSION['currentModule'] = $id;
		}
		
		if ($_POST['submit'] == "Finish") {
			redirect("../index.php?updated=module");
		} else {
			redirect("lesson_content.php");
		}
	}
	
//Title
	navigation("Module Settings", "Setup the module's initial settings, such as the name, time frame, and any comments.");
	
//Lesson settings form
	form("lessonSettings");
	catDivider("Module Information", "one", true);
	echo "<blockquote>";
	directions("Module Name", true, "The name of the module");
	echo "<blockquote><p>";
	textField("name", "name", false, false, false, true, false, false, "moduleData", "name");
	echo "</p></blockquote>";
	directions("Directions" , true, "Comments or directions regarding the content of this module");
	echo "<blockquote><p>";
	textArea("comments", "comments", "small", true, false, false, "moduleData", "comments");
	echo "</p></blockquote>";
	directions("Time frame" , false, "The amount of time the user will have to complete the module from the assigned date");
	echo "<blockquote><p>";
	
	//Select the time frame
	if (isset($_SESSION['currentModule'])) {
		$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$numberArray = array("0","1","2","3","4","5","6","7","8","9");
		$time = str_replace($letterArray, "", $moduleData['timeFrame']);
		$timeLabel = str_replace($numberArray, "", $moduleData['timeFrame']);
	} else {
		$time = "2";
		$timeLabel = "Weeks";
	}
	
	dropDown("time", "time", "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25", "1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25", false, false, false, $time);
	dropDown("timeLabel", "timeLabel", "Days,Weeks,Months,Years", "Days,Weeks,Months,Years", false, false, false, $timeLabel);
	echo "</p></blockquote>";
	directions("Category", true, "The category that this modules covers");
	echo "<blockquote><p>";
	category();
	echo "</p></blockquote>";
	directions("Intended employee type", true, "The employee position for which this module is intended");
	echo "<blockquote><p>";
	employeeTypes();
	echo "</p></blockquote>";
	difficulty();
	echo "</blockquote>";
	
	catDivider("Module Settings", "two");
	echo "<blockquote>";
	directions("Price", false, "Set the price of this module, if a user purchases <br />them individually. Organzations will not be charged <br />this price, since they are paying on a monthly basis <br />for access to all avaliable modules.");
	echo "<blockquote><p>$ ";
	
	if (empty($moduleData['price'])) {
		textField("price", "price", "7", false, false, true, false, false, "moduleData", "price", " disabled=\"disabled\"");
	} else {
		textField("price", "price", "7", false, false, true, false, false, "moduleData", "price");
	}
	
	echo " ";
	checkbox("enablePrice", "enablePrice", "Enable", false, false, false, false, "moduleData", "enablePrice", "on", " onclick=\"flvFTFO1('lessonSettings','price,t')\"");
	echo "</p></blockquote>";
	directions("Lock module", false, "Prevent organizations from customizing these settings for their needs");
	echo "<blockquote><p>";
	radioButton("locked", "locked", "Yes,No", "1,0", true, false, false, "0", "moduleData", "locked");
	echo "</p></blockquote>";
	directions("Force module", false, "Force every user in this system to take this lesson");
	echo "<blockquote><p>";
	radioButton("selected", "selected", "Yes,No", "1,0", true, false, false, "0", "moduleData", "selected");
	echo "</p></blockquote>";
	directions("Permit user to skip module", false, "Premit users to skip this module and come back to it later, <br />if the user was assigned a series of modules");
	echo "<blockquote><p>";
	radioButton("skip", "skip", "Yes,No", "1,0", true, false, false, "0", "moduleData", "skip");
	echo "</p></blockquote>";
	directions("Force user to give feedback", false, "Force a user to provide feedback at the end of this module");
	echo "<blockquote><p>";
	radioButton("feedback", "feedback", "Yes,No", "1,0", true, false, false, "0", "moduleData", "feedback");
	echo "</p></blockquote>";
	directions("Search keywords (Seperate keywords with a comma and a space)", false, "Supply a list of key words to help narrow down results in searches.<br />These seach results can show up on a search engine, such as Google, to help boost sales.");
	echo "<blockquote><p>";
	textField("tags", "tags", false, false, false, false, false, false, "moduleData", "tags");
	echo "&nbsp;";
	checkbox("searchEngine", "searchEngine", "Accessible by search engines", false, false, false, false, "moduleData", "searchEngine", "on");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Submit", "three");
	echo "<blockquote><p>";
	
//Display navigation buttons
	button("submit", "submit", "Next Step &gt;&gt;", "submit");
	
	if (!isset($_SESSION['currentModule'])) {
		button("cancel", "cancel", "Cancel", "cancel", "modify.php");
	}

	if (isset ($_SESSION['review'])) {
		button("submit", "submit", "Finish", "submit");
	}
	
	echo "</p></blockquote>";
	closeForm(true, true);
	
//Include the footer
	footer();
?>