<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Multiple Choice", "tinyMCESimple,validate,newObject");
	require_once('functions.php');
	$questionData = dataGrabber("Multiple Choice");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['choices']) && !empty($_POST['values'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$difficulty = $_POST['difficulty'];
		$category = $_POST['category'];
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
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `randomize` = '{$randomize}', `partialCredit` = '{$partialCredit}', `choiceType` = '{$interface}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `randomize` = '{$randomize}', `partialCredit` = '{$partialCredit}', `choiceType` = '{$interface}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Multiple Choice', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$category}', '{$link}', '{$randomize}', '0', '{$interface}', '1', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Multiple Choice', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$category}', '{$randomize}', '0', '{$interface}', '1', '{$tags}', '{$question}', '{$questionValue}', '', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
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
	type();
	difficulty();
	category();
	descriptionLink();
	partialCredit();
	randomize();
	tags();
	echo "</blockquote>";
	
	catDivider("Question Content", "three");
	echo "<blockquote>";
	directions("Question content (Fill then select correct answers)", true, "A multiple choice question will prompt a user to select the correct answer(s) from a list of choices.<br />When entering the information, the text will go in the text fields, and the correct answer(s) will be <br />provided by checking the check box next to the corresponding text field.");
	echo "<blockquote><table id=\"items\">";
	
	if (isset($questionData)) {
		$values = unserialize($questionData['questionValue']);
		$choices = unserialize($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($values) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\"><td>";
			
			if (in_array($value, $choices)) { 
				checkbox("choices[]", "choice" . $value, false, $value, true, "1", true);
			} else {
				checkbox("choices[]", "choice" . $value, false, $value, true, "1");
			}
			
			echo "</td><td>";
			textField("values[]", "value" . $value, false, false, false, false, false, $values[$count]);
			echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true)\"></span></td></tr>";
		}
	} else {
		echo "<tr id=\"1\" align=\"center\"><td>";
		checkbox("choices[]", "choice1", false, "1", true, "1");
		echo "</td><td>";
		textField("values[]", "value1");
		echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true, true)\"></span></td></tr><tr id=\"2\" align=\"center\"><td>";
		checkbox("choices[]", "choice2", false, "2", true, "1");
		echo "</td><td>";
		textField("values[]", "value2");
		echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true, true)\"></span></td></tr>";
	}
	
	echo "</table><p>";
	echo "<span class=\"smallAdd\" onclick=\"addMultipleChoice('items', '<label><input name=\'choices[]\' type=\'checkbox\' id=\'choice', '\' value=\'', '\' class=\'validate[required,minCheckbox[1]]\'></label>', '<input name=\'values[]\' type=\'text\' id=\'value', '\' autocomplete=\'off\' size=\'50\' class=\'validate[required]\' />', '<input name=\'answerValue[]\' type=\'text\' id=\'answerValue', '\' autocomplete=\'off\' size=\'50\' />')\">Add Another Item</span>";
	echo "</p></blockquote></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>