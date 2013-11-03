<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Matching", "tinyMCESimple,validate,showHide,newObject");
	require_once('functions.php');
	$questionData = dataGrabber("Matching");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['questionValue']) && !empty($_POST['answerValue'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$difficulty = $_POST['difficulty'];
		$link = $_POST['link'];
		$partialCredit = $_POST['partialCredit'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
		$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
		if (isset ($questionData)) {
			updateQuery($monitor['type'], "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `link` = '{$link}', `partialCredit` = '{$partialCredit}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?updated=question");	
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($monitor['type'], "NULL, '0', '0', '{$lastQuestion}', 'Matching', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$link}', '0', '0', '', '1', '{$tags}', '{$question}', '{$questionValue}', '{$answer}', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?inserted=question");
		}
	}
	
//Title
	title($monitor['title'] . "Matching", "A matching question will ask a user to match a series of similar values from a list of values.");
	
//Matching form
	form("matching");
	catDivider("Question", "one", true);
	echo "<blockquote>";
	question();
	echo "</blockquote>";
	
	catDivider("Question Settings", "two");
	echo "<blockquote>";
	points();
	difficulty();
	descriptionLink();
	partialCredit();
	tags();
	echo "</blockquote>";
	
	catDivider("Question Content", "three");
	echo "<blockquote>";
	directions("Question content", true, "A matching question will ask a user to match a series of similar values   from a list of values. <br />When entering the information, the &quot;Left-Column Values&quot; column is the information which <br />the user will match with the &quot;Right-Column Values&quot; list. The &quot;Right-Column Values&quot; <br />column is automatically scrambled in the test for the user to match. When entering the <br />information entering the information, the correct values will go in the same row.");
	echo "<blockquote><table class=\"dataTable\" id=\"items\"><tr><th class=\"tableHeader\">Left-Column Values</th><th class=\"tableHeader\">Right-Column Values</th><th class=\"tableHeader\" width=\"50\"></th></tr>";
	
	if (isset($questionData)) {
		$questions = unserialize($questionData['questionValue']);
		$answers = unserialize($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($questions) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\"><td>";
			textField("questionValue[]", "questionValue" . $value, false, false, false, true, false, $questions[$count]);
			echo "</td><td>";
			textField("answerValue[]", "answerValue" . $value, false, false, false, true, false, $answers[$count]);
			echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '" . $value . "', '1')\"></span></td></tr>";
		}
	} else {
		echo "<tr id=\"1\" align=\"center\"><td>";
		textField("questionValue[]", "questionValue1");
		echo "</td><td>";
		textField("answerValue[]", "answerValue1");
		echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '2')\"></span></td></tr><tr id=\"2\" align=\"center\"><td>";
		textField("questionValue[]", "questionValue2");
		echo "</td><td>";
		textField("answerValue[]", "answerValue2");
		echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '2', '2')\"></span></td></tr>";
	}
	
	echo "</table><p>";
	echo "<span class=\"smallAdd\" onclick=\"addMatching('items', '<input name=\'questionValue[]\' type=\'text\' id=\'questionValue', '\' autocomplete=\'off\' size=\'50\' class=\'validate[required]\' />', '<input name=\'answerValue[]\' type=\'text\' id=\'answerValue', '\' autocomplete=\'off\' size=\'50\' class=\'validate[required]\' />')\">Add Another Item</span>";
	echo "</p></blockquote></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>