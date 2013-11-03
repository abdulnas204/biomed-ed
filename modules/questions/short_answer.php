<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Short Answer", "tinyMCESimple,validate,newObject");
	require_once('functions.php');
	$questionData = dataGrabber("Short Answer");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['answerValue'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$difficulty = $_POST['difficulty'];
		$link = $_POST['link'];
		$case = $_POST['case'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
		if (isset ($questionData)) {
			updateQuery($monitor['type'], "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `link` = '{$link}', `case` = '{$case}', `tags` = '{$tags}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?updated=question");	
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
		
			insertQuery($monitor['type'], "NULL, '0', '0', '{$lastQuestion}', 'Short Answer', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$link}', '0', '0', '', '{$case}', '{$tags}', '{$question}', '', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?inserted=question");
		}
	}
	
//Title
	title($monitor['title'] . "Short Answer", "A short answer is a question in which a user must provide a one or two word response. These questions are scored automatically.");
	
//Short answer form
	form("shortAnswer");
	catDivider("Question", "one", true);
	echo "<blockquote>";
	question();
	echo "</blockquote>";
	
	catDivider("Question Settings", "two");
	echo "<blockquote>";
	points();
	difficulty();
	descriptionLink();
	ignoreCase();
	tags();
	echo "</blockquote>";
	
	catDivider("Answers", "three");
	echo "<blockquote>";
	directions("Provide correct answer(s)", true, "A short answer is a question in which a user must provide a one or two   word   response. <br />When entering the information, all possible answer(s) to a question be provided in the <br />test setup. However, there will only be one text field in the test to provide an answer, <br />regardless of the number of possible answers provided in the setup. The user must only <br />match one of these answers in order to get the correct answer.");
	echo "<blockquote><table id=\"items\">";
	
	if (isset($questionData)) {
		$answers = unserialize($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($answers) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\"><td>";
			textField("answerValue[]", "answerValue" . $value, false, false, false, true, false, $answers[$count]);
			echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '1', true)\"></span></td></tr>";
		}
	} else {
		echo "<tr id=\"1\" align=\"center\"><td>";
		textField("answerValue[]", "answerValue1");
		echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '1', true)\"></span></td></tr>";
	}
	
	echo "</table><p>";
	echo "<span class=\"smallAdd\" onclick=\"addShortAnswer('items', '<input name=\'answerValue[]\' type=\'text\' id=\'answerValue', '\' autocomplete=\'off\' size=\'50\' class=\'validate[required]\' />')\">Add Another Item</span>";
	echo "</p></blockquote></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback();
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>