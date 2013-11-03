<?php 
//Header functions
	require_once('../../../Connections/connDBA.php');
	$monitor = monitor("Verify Content", "navigationMenu");

//Update a session to go to different steps
	if (isset ($_POST['back'])) {
		$_SESSION['step'] = "lessonContent";
		redirect("lesson_content.php");
	}
	
	if (isset ($_POST['next'])) {
	//Check to see if a test exists, and set a session accordingly
		$testCheckGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'", $connDBA);
		$testCheckArray = mysql_fetch_array($testCheckGrabber);
		
		if ($testCheckArray['test'] == "1") {
			$_SESSION['step'] = "testSettings";
			redirect("test_settings.php");
		} elseif ($testCheckArray['test'] == "0") {
			$_SESSION['step'] = "testCheck";
			redirect("test_check.php");
		}
	}
	
//Title
	navigation("Verify Content", "Content may be reviewed in the section below. Changes can be made to the lesson by clicking the &quot;Make Changes&quot; button.");
	
//Lesson preview
	form("lessonContent");
	catDivider("Verify Module Content", "four", true);
	echo "<blockquote>";
	lesson($monitor['currentModule'], $monitor['lessonTable'], false);
	echo "</blockquote>";
	catDivider("Submit", "five");
	echo "<blockquote><p>";
	button("back", "back", "&lt;&lt;  Make Changes", "submit");
	button("next", "next", "Next Step &gt;&gt;", "submit");
	
	if (isset ($_SESSION['review'])) {
		button("submit", "submit", "Finish", "submit");
	}
	
	echo "</p></blockquote>";
	closeForm(true, false);

//Include the footer
	footer();
?>