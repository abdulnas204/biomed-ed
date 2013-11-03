<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	require_once('functions.php');
	$monitor = monitor("Matching", "tinyMCEMedia,tinyMCEQuestion,validate,newObject,autoSuggest");
	$questionData = dataGrabber("Matching");
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['question']) && is_numeric($_POST['points']) && !empty($_POST['questionValue']) && !empty($_POST['answerValue'])) {
		$question = mysql_real_escape_string($_POST['question']);
		$points = $_POST['points'];
		$extraCredit = $_POST['extraCredit'];
		$type = $_POST['type'];
		$difficulty = $_POST['difficulty'];
		$category = $_POST['category'];
		$link = $_POST['link'];
		$partialCredit = $_POST['partialCredit'];
		$tags = mysql_real_escape_string($_POST['tags']);
		$questionValue = mysql_real_escape_string(serialize($_POST['questionValue']));
		$answerValue = mysql_real_escape_string(serialize($_POST['answerValue']));
		$feedBackCorrect = mysql_real_escape_string($_POST['feedBackCorrect']);
		$feedBackIncorrect = mysql_real_escape_string($_POST['feedBackIncorrect']);
		$feedBackPartial = mysql_real_escape_string($_POST['feedBackPartial']);
		
		if (isset ($questionData)) {
			updateQuery($type, "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `link` = '{$link}', `partialCredit` = '{$partialCredit}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'", "`question` = '{$question}', `points` = '{$points}', `extraCredit` = '{$extraCredit}', `difficulty` = '{$difficulty}', `category` = '{$category}', `partialCredit` = '{$partialCredit}', `tags` = '{$tags}', `questionValue` = '{$questionValue}', `answerValue` = '{$answerValue}', `correctFeedback` = '{$feedBackCorrect}', `incorrectFeedback` = '{$feedBackIncorrect}', `partialFeedback` = '{$feedBackPartial}'");
		} else {
			$lastQuestion = lastItem($monitor['testTable']);
			
			insertQuery($type, "NULL, '0', '0', '{$lastQuestion}', 'Matching', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$category}', '{$link}', '0', '0', '', '1', '{$tags}', '{$question}', '{$questionValue}', '{$answer}', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'", "NULL, 'Matching', '{$points}', '{$extraCredit}', '{$partialCredit}', '{$difficulty}', '{$category}', '0', '0', '', '1', '{$tags}', '{$question}', '{$questionValue}', '{$answer}', '{$answerValue}', '', '{$feedBackCorrect}', '{$feedBackIncorrect}', '{$feedBackPartial}'");
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
	type();
	difficulty();
	category();
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
			textArea("questionValue[]", "questionValue" . $value, "extraSmall", true, false, $questions[$count], false, false, " class=\"noEditorMedia editorQuestion answerValue" . $count . "\"");
			echo "</td><td>";
			textArea("answerValue[]", "answerValue" . $value, "extraSmall", true, false, $answers[$count], false, false, " class=\"noEditorMedia editorQuestion questionValue" . $count . "\"");
			echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '" . $value . "', '2')\"></span></td></tr>";
		}
		
		hidden("id", "id", $value);
	} else {
		echo "<tr id=\"1\" align=\"center\"><td>";
		textArea("questionValue[]", "questionValue1", "extraSmall", true, false, false, false, false, " class=\"noEditorMedia editorQuestion questionValue1\"");
		echo "</td><td>";
		textArea("answerValue[]", "answerValue1", "extraSmall", true, false, false, false, false, " class=\"noEditorMedia editorQuestion answerValue1\"");
		echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '2')\"></span></td></tr><tr id=\"2\" align=\"center\"><td>";
		textArea("questionValue[]", "questionValue2", "extraSmall", true, false, false, false, false, " class=\"noEditorMedia editorQuestion questionValue2\"");
		echo "</td><td>";
		textArea("answerValue[]", "answerValue2", "extraSmall", true, false, false, false, false, " class=\"noEditorMedia editorQuestion answerValue2\"");
		echo "</td><td width=\"50\"><span class=\"action smallDelete\" onclick=\"deleteObject('items', '2', '2')\"></span></td></tr>";
		hidden("id", "id", "2");
	}
	
	echo "</table><p>";
	echo "<span class=\"smallAdd\" onclick=\"addMatching('items')\">Add Another Item</span>";
	echo "</p></blockquote></blockquote>";
	
	catDivider("Feedback", "four");	
	feedback(true);
	
	catDivider("Finish", "five");
	buttons();
	closeForm(true, true);
	
//Include the footer
	footer();
?>