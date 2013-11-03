<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Essay", "tinyMCESimple,validate");
	require_once('functions.php');
	$questionData = dataGrabber("Essay");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$difficulty = $_POST['difficulty'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$answer = mysql_real_escape_string($_POST['answer']);
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
	
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Essay', '{$points}', '{$extraCredit}', '0', '{$difficulty}', '{$category}', '{$link}', '0', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Essay', '{$points}', '{$extraCredit}', '0', '{$difficulty}', '{$category}', '0', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
		}
	}
	
//Title
	title($monitor['title'] . "Essay", "An essay question is a question that requires a long, written response. Essays must be scored manually.");
	
//Essay form
	form("essay");
	catDivider("Question", "one", true);
	echo "<blockquote>";
	question();
	echo "</blockquote>";
	
	catDivider("Question Settings", "two");
	echo "<blockquote>";
	points();
	type();
	difficulty();
	category();
	descriptionLink();
	tags();
	echo "</blockquote>";
	
	catDivider("Answer", "three");
	echo "<blockquote>";
	directions("Provide an example of a correct answer");
	echo "<blockquote><p>";
	textArea("answer", "answer", "small", false, false, false, "questionData", "answer");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Feedback", "four");
	feedback();
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>