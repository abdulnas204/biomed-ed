<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Verify Content", "navigationMenu");
	
//Test to see if a test exists
	$testCheckGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'", $connDBA);
	$testCheck = mysql_fetch_array($testCheckGrabber);
	
//Title
	navigation("Verify Content", "Content may be reviewed in the section below. Changes can be made to the lesson by clicking the &quot;Make Changes&quot; button.");
	
//Lesson preview
	lesson($monitor['currentModule'], $monitor['lessonTable'], true);
	echo "<blockquote><p>";
	button("back", "back", "&lt;&lt;  Make Changes", "button", "lesson_content.php");
	
	if ($testCheck['test'] == "1") {
		button("next", "next", "Next Step &gt;&gt;", "button", "test_settings.php");
	} else {
		button("next", "next", "Next Step &gt;&gt;", "button", "test_check.php");
	}
	
	if (isset ($_SESSION['review'])) {
		button("submit", "submit", "Finish", "button", "../index.php?updated=module");
	}
	
	echo "</p></blockquote>";

//Include the footer
	footer();
?>