<?php 
//Header functions
	require_once('../../../Connections/connDBA.php');
	$monitor = monitor("Multiple Choice", "tinyMCESimple,validate,showHide,newObject");
	require_once('functions.php');
	$questionData = dataGrabber("Multiple Choice");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['choices']) && !empty($_POST['values'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$difficulty = $_POST['difficulty'];
		$link = $_POST['link'];
		$partialCredit = $_POST['partialCredit'];
		$randomize = $_POST['randomize'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$questionValue = serialize($_POST['values']);
		$answerValue = mysql_real_escape_string(serialize($_POST['choices']));
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
		if (sizeof($_POST['choices']) == "1") {
			$interface = "radio";
		} elseif (sizeof($_POST['choices']) > "1") {
			$interface = "checkbox";
		} elseif (sizeof($_POST['choices']) == "0") {
			redirect("multiple_choice.php");
		}
		
		if (isset ($questionData)) {
			updateQuery($monitor['type'], "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `link` = '{$link}', `randomize` = '{$randomize}', `partialCredit` = '{$partialCredit}', `choiceType` = '{$interface}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?updated=question");
	//If the page is inserting an item		
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($monitor['type'], "NULL, '0', '0', '{$lastQuestion}', 'Multiple Choice', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$link}', '{$randomize}', '0', '{$interface}', '1', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
							
			redirect($monitor['redirect'] . "?inserted=question");
		}
	}
	
//Title
	title($monitor['title'] . "Multiple Choice", "A multiple choice question will prompt a user to select the correct answer(s) from a list of choices.");
	
//Multiple choice form
	form("choice");
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
	directions("Randomize values");
	echo "<blockquote><p>";
	radioButton("randomize", "randomize", "Yes,No", "1,0", true, false, false, "1", "questionData", "randomize");
	echo "</p></blockquote>";
	tags();
	echo "</blockquote>";
	
	catDivider("Question Content", "three");
	echo "<blockquote>";
	directions("Question content (Fill then select correct answers)", true, "A multiple choice question will prompt a user to select the correct answer(s) from a list of choices.<br />When entering the information, the text will go in the text fields, and the correct answer(s) will be <br />provided by checking the check box next to the corresponding text field.");
	echo "<table id=\"items\">";
	
	if (isset($questionData)) {
		$values = unserialize($questionData['questionValue']);
		$choices = unserialize($questionData['answerValue']);
		$count = 1;
		
		for ($count = 1; $count <= sizeof($values) - 1; $count ++) {
			echo "<tr id=\"" . $count . "\" align=\"center\"><td>";
			
			checkbox("choices[]", "choice" . $count, false, false, true, "1", false, "choices", $count, "on");
			echo "</td><td>";
			textField("values[]", "value" . $count, false, false, false, false, false, $values[$count]);
			echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', '" . $count . "', '2', true)\"></span></td></tr>";
		}
	} else {
		echo "<tr id=\"1\" align=\"center\"><td>";
		checkbox("choices[]", "choice1", false, "1", false);
		echo "</td><td>";
		textField("values[]", "value1");
		echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '2', true, true)\"></span></td></tr><tr id=\"2\" align=\"center\"><td>";
		checkbox("choices[]", "choice2", false, "2", false);
		echo "</td><td>";
		textField("values[]", "value2");
		echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', '2', '2', true, true)\"></span></td></tr>";
	}
	
	echo "</table><p>";
	echo "<span class=\"smallAdd\" onclick=\"addMultipleChoice('items', '<label><input name=\'choices[]\' type=\'checkbox\' id=\'choice', '\' value=\'', '\'></label>', '<input name=\'values[]\' type=\'text\' id=\'value', '\' autocomplete=\'off\' size=\'50\' class=\'validate[required]\' />', '<input name=\'answerValue[]\' type=\'text\' id=\'answerValue', '\' autocomplete=\'off\' size=\'50\' />')\">Add Another Item</span>";
	echo "</p></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>