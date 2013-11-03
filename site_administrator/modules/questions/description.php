<?php 
//Header functions
	require_once('../../../Connections/connDBA.php');
	$monitor = monitor("Description", "tinyMCEAdvanced,validate");
	require_once('functions.php');
	$questionData = dataGrabber("Description");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$tags = mysql_real_escape_string($_POST['tags']);
		
		if (isset ($questionData)) {					
			updateQuery($monitor['type'], "`question` = '{$question}', `tags` = '{$tags}'", $connDBA);
			
			redirect($monitor['redirect'] . "?updated=question");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($monitor['type'], "NULL, '0', '', '{$lastQuestion}', 'Description', '0', '', '0', '', '0', '0', '0', '', '1', '{$tags}', '{$question}', '', '', '', '', '', '', ''");
			
			redirect($monitor['redirect'] . "?inserted=question");
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
	tags();
	echo "</blockquote>";
	
	catDivider("Submit", "three");
	buttons();
	closeForm(true, true);

//Include the footer
	footer();
?>