<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');	
	$monitor = monitor("Create a Test", "navigationMenu");

//Process the form
	if (isset ($_POST['submit'])) {		
		query("CREATE TABLE IF NOT EXISTS `{$monitor['testTable']}` (
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
					)");
							
		query("UPDATE `{$monitor['parentTable']}` SET `test` = '1' WHERE `id` = '{$monitor['currentModule']}'");	
			
		redirect("test_settings.php");
	}
	
	if (isset ($_POST['skipTest'])) {
		redirect("complete.php");
	}

//Title
	navigation("Create a Test", "Do you wish to create a test for this module?");
	
//Test check form
	echo "<div class=\"noResults\">";
	form("testCheck");
	button("submit", "submit", "Create a Test", "submit");
	button("skipTest", "skipTest", "Do not Create Test", "submit");
	closeForm(false, false);
	echo "</div>";

//Include the footer
	footer();
?>