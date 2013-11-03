<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	require_once('functions.php');
	$monitor = monitor("Description", "tinyMCEAdvanced,validate,autoSuggest");
	$questionData = dataGrabber("Description");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$type = $_POST['type'];
		$category = $_POST['category'];
		$tags = mysql_real_escape_string($_POST['tags']);
		
		if (isset ($questionData)) {					
			updateQuery($type, "`question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}'", "`question` = '{$question}', `category` = '{$category}', `tags` = '{$tags}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '', '{$lastQuestion}', 'Description', '0', '', '0', '', '{$category}', '0', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''", "NULL, 'Description', '0', '', '0', '', '{$category}', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''");
		}
	}
	
//Title
	title($monitor['title'] . "Description", "A description is not a question field, however, it allows test creators to insert text into the test without asking any questions or scoring the viewer on this content.");
	
//Description form
	form("description");
	catDivider("Content", "one", true);
	echo "<blockquote>";
	directions("Description content", true);
	echo "<blockquote><p>";
	textArea("question", "question", "large", true, false, false, "questionData", "question");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Settings", "two");
	echo "<blockquote>";
	type();
	category();
	tags();
	echo "</blockquote>";
	
	catDivider("Submit", "three");
	buttons();
	closeForm(true, true);

//Include the footer
	footer();
?>