<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Verify Test Content", "navigationMenu,newObject,tinyMCESimple");
	
//Display a randomizing alert if any part of this test randomizes
	$randomizeTest = query("SELECT * FROM `{$monitor['parentTable']}` WHERE `id` = '{$monitor['currentModule']}'");
	$randomizeQuestion = query("SELECT * FROM `{$monitor['testTable']}` WHERE `randomize` = '1'");
	
	if ($randomizeQuestion) {
		$randomize = "true";
	} else {
		$randomize = "false";
	}
	
	if ($randomizeTest['randomizeAll'] == "Randomize" || $randomize == "true") {
		$message = " <strong>Since you are only previewing this test, note the questions may appear in a different order if the page is refreshed, or left and returned to later.</strong>";
	} else {
		$message = "";
	}

//Title
	navigation("Verify Test Content", "Content may be reviewed in the section below. Changes can be made to  the lesson by clicking the &quot;Make Changes&quot; button, and modifying the test." . $message);

//Display the test
	test($monitor['testTable'], $monitor['gatewayPath'], false);
	
//Display navigation buttons
	echo "<blockquote>";
	button("back", "back", "&lt;&lt;  Previous Step", "button", "test_content.php");
	button("next", "next", "Next Step &gt;&gt;", "button", "complete.php");
	
	if (isset ($_SESSION['review'])) {
		button("submit", "submit", "Finish", "button", "../index.php?updated=module");
	}
	
	echo "</blockquote>";
	
//Include the footer
	footer();
?>