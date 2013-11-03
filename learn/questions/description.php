<?php
/*
LICENSE: See "license.php" located at the root installation

This is the description management page for the test generator.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');	
	$monitor = monitor("Description", "tinyMCEAdvanced,tinyMCEMediaConfig,validate,autoSuggest");
	$questionData = dataGrabber("Description");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question'])) {
		$question = escape($_POST['question']);
		$type = $_POST['type'];
		$category = escape($_POST['category']);
		$tags = escape($_POST['tags']);
		
		if (isset($questionData)) {			
			updateQuery($type, "`question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}'", "`question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '', '{$lastQuestion}', 'Description', '0', '', '0', '{$category}', '0', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''", "NULL, 'Description', '0', '', '0', '{$category}', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''");
		}
	}
	
//Title
	title($monitor['title'] . "Description", "A description is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.");
	
//Description form
	echo form("description");
	catDivider("Content", "one", true);
	echo "<blockquote>\n";
	directions("Description content", true);
	indent(textArea("question", "questionContent", "large", true, false, false, "questionData", "question", "class=\"noEditorMedia\""));
	echo "</blockquote>\n";
	
	catDivider("Settings", "two");
	echo "<blockquote>\n";
	type();
	category();
	tags();
	customField("Question Generator", "questionData");
	echo "</blockquote>\n";
	
	catDivider("Submit", "three");
	formButtons();
	echo closeForm();

//Include the footer
	footer();
?>