<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("True False", "tinyMCESimple,validate");
	require_once('functions.php');
	$questionData = dataGrabber("True False");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && is_numeric($_POST['answer'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$difficulty = $_POST['difficulty'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$randomize = $_POST['randomize'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$answer = $_POST['answer'];
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `randomize` = '{$randomize}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `randomize` = '{$randomize}', `tags` = '{$tags}', `answer` = '{$answer}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}'");	
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'True False', '{$points}', '{$extraCredit}', '', '{$difficulty}', '{$category}', '{$link}', '{$randomize}', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', ''", "NULL, 'True False', '{$points}', '{$extraCredit}', '', '{$difficulty}', '{$category}', '{$randomize}', '0', '', '1', '{$tags}', '{$question}', '', '{$answer}', '', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', ''");
		}
	}
	
//Title
	title($monitor['title'] . "True or False", "A true or false question will prompt a user to respond to a question as a true or false statement.");
	
//True false form
	form("trueFalse");
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
	randomize();
	tags();
	echo "</blockquote>";
	
	catDivider("Question Content", "three");
	echo "<blockquote>";
	directions("Select the correct answer", true);
	echo "<blockquote><p>";
	radioButton("answer", "answer", "True,False", "1,0", true, true, false, false, "questionData", "answer");
	echo "</p></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback();
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>