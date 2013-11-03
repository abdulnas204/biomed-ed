<?php 
//Header functions
	require_once('../../../Connections/connDBA.php');
	$monitor = monitor("Fill in the Blank", "tinyMCESimple,validate,showHide,newObject");
	require_once('functions.php');
	$questionData = dataGrabber("Fill in the Blank");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['questionValue']) && !empty($_POST['answerValue'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$difficulty = $_POST['difficulty'];
		$link = $_POST['link'];
		$partialCredit = $_POST['partialCredit'];
		$case = $_POST['case'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
		$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
			
		if (isset ($questionData)) {
			updateQuery($monitor['type'], "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `link` = '{$link}', `partialCredit` = '{$partialCredit}', `case` = '{$case}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?updated=question");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($monitor['type'], "NULL, '0', '0', '{$lastQuestion}', 'Fill in the Blank', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$link}', '0', '0', '', '{$case}', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
				
			redirect($monitor['redirect'] . "?inserted=question");
		}
	}
	
//Title
	title($monitor['title'] . "Fill in the Blank", "A fill in the blank question will prompt a user to complete a sentence with missing values by filling in the blanks.");
	
//Fill in the blank form
	form("blank");
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
	directions("Ignore case");
	echo "<blockquote><p>";
	radioButton("case", "case", "Yes,No", "1,0", true, false, false, "1", "questionData", "case");
	echo "</p></blockquote>";
	tags();
	echo "</blockquote>";
	
	catDivider("Question Content", "three");
	echo "<blockquote>";
	directions("Question content", true, "A fill in the blank question will prompt a user to complete a sentence with missing values by filling in the blanks. <br />When entering the information, the &quot;Sentence&quot; column is the information the user will see. The &quot;Values&quot; column <br />is what the user will be prompted to fill in, in order to complete the incomplete sentence. If the last value in the <br />&quot;Values&quot; column is left blank, the system will understand that this is the end of the sentence, and will not <br />include it in the test.");
	echo "<table class=\"dataTable\" id=\"items\"><tr><th class=\"tableHeader\">Sentence</th><th class=\"tableHeader\">Values</th><th class=\"tableHeader\" width=\"50\"></th></tr>";
	
	if (isset($questionData)) {
		$questions = unserialize($questionData['questionValue']);
		$answers = unserialize($questionData['answerValue']);
		$count = 1;
		
		for ($count = 0; $count <= sizeof($questions) - 1; $count ++) {
			echo "<tr id=\"" . $count . "\" align=\"center\"><td>";
			textField("questionValue[]", "questionValue" . $count, false, false, false, true, false, $questions[$count]);
			echo "</td><td>";
			textField("answerValue[]", "answerValue" . $count, false, false, false, false, false, $answers[$count]);
			echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '" . $count . "', '1')\"></span></td></tr>";
		}
	} else {
		echo "<tr id=\"1\" align=\"center\"><td>";
		textField("questionValue[]", "questionValue1");
		echo "</td><td>";
		textField("answerValue[]", "answerValue1", false, false, false, false);
		echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '1')\"></span></td></tr>";
	}
	
	echo "</table><p>";
	echo "<span class=\"smallAdd\" onclick=\"addBlank('items', '<input name=\'questionValue[]\' type=\'text\' id=\'questionValue', '\' autocomplete=\'off\' size=\'50\' class=\'validate[required]\' />', '<input name=\'answerValue[]\' type=\'text\' id=\'answerValue', '\' autocomplete=\'off\' size=\'50\' />')\">Add Another Item</span>";
	echo "</p></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>