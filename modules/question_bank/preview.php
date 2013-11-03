<?php require_once('../../system/connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Select a question
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		
		$questionCheck = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$id}' LIMIT 1", $connDBA);
		if (mysql_fetch_array($questionCheck)) {
			//Do nothing
		} else {
			die("The test question does not exist.");
		}
	} else {
		die("A required parameter is missing.");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php title("Preview Test Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../javascripts/insert/newFileUpload.js" type="text/javascript"></script>
</head>

<body class="overrideBackground">
<?php tooltip(); ?>
<h2 class="preview">Preview Test Question</h2>
<?php  
//Echo the opening table HTML
	  echo "<table width=\"100%\" class=\"dataTable\">";
	  
	  $testDataGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$id}' LIMIT 1", $connDBA);
	  $count = 1;
//Loop through the items
	  while ($testData = mysql_fetch_array($testDataGrabber)) {
	  //Detirmine what kind of question is being displayed
		  switch ($testData ['type']) {
		  //If the question is a description
			  case "Description" : 
			  //Echo the description, without the any numbering or point value
				  echo "<tr><td colspan=\"2\" valign=\"top\">" . stripslashes($testData['question']) . "</td></tr>"; break;
				  
		  //If the question is an essay
			  case "Essay" : 
			  //Echo the point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><span class=\"questionPoints\">" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</span>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				  }
			  //Echo the essay content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br /><span id=\"checkEssay" . $testData['id'] . "\"><textarea id=\"" . $testData['id'] . "\" name=\"" . $testData['id'] . "\" style=\"width:450px;\"></textarea><span class=\"textareaRequiredMsg\"></span></span><br /><br /></td></tr>"; break;
				  
		  //If the question a file response
			  case "File Response" : 
			  //Echo the point value	
				  echo "<tr><td width=\"100\" valign=\"top\"><p><span class=\"questionPoints\">" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</span>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				  }
			  //Echo the file response content	
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br />";
				  if ($testData['totalFiles'] > 1) {
					  echo "<table name=\"upload" . $testData['id'] . "\" id=\"upload" . $testData['id'] . "\"><tr><td><input size=\"50\" id=\"" . $testData['id'] . "1\" name=\"" . $testData['id'] . "1\" type=\"file\" class=\"validate[required]\"></td></tr>";
					  
					  echo "</table><input value=\"Add Another File\" type=\"button\" id=\"button" . $testData['id'] . "\" onclick=\"appendRow('upload" . $testData['id'] . "', '<input id=\'" . $testData['id'] . "', '\' name=\'" . $testData['id'] . "', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "', 'button" . $testData['id'] . "');\" /><input value=\"Remove Last File\" type=\"button\" onclick=\"deleteLastRow('upload" . $testData['id'] . "', 'button" . $testData['id'] . "');\" /><br />";
				  } else {
					  echo "<input size=\"50\" id=\"" . $testData['id'] . $testData['totalFiles'] . "\" name=\"" . $testData['id'] . $testData['totalFiles'] . "\" type=\"file\" class=\"validate[required]\"><br />";
				  }
				  echo "<br /></td></tr>"; break;
				  
		  //If the question is a fill in the blank

			  case "Fill in the Blank" : 
			  //Echo the point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><span class=\"questionPoints\">" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</span>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				  }
			  //Echo the fill in the blank content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br />"; 
				  //Grab the necessary data
				  $blankQuestion = $testData['questionValue'];
				  $blank = unserialize($blankQuestion);
			  
				  //Do not display the last value if it is blank
				  $blankAnswers = unserialize($testData['answerValue']);
				  $blankSize = sizeof($blankAnswers);
				  $lastValue = $blankSize-1;
				  if ($blankAnswers[$lastValue] !== "") {
				  //Echo the fill in the blank content content with the last value
					  while (list($blankKey, $blankArray) = each($blank)) {
						  $subID = $blankKey+1;						
						  echo stripslashes($blankArray) . " <input id=\"" . $testData['id'] . $subID . "\" name=\"" . $testData['id'] . $subID . "\" autocomplete=\"off\" type=\"text\" size=\"25\" class=\"validate[required]\" /> ";
					  }
				  } else {
				  //Echo the fill in the blank content content without the last value
					  while (list($blankKey, $blankArray) = each($blank)) {						
						  if ($blankKey !== $lastValue) {
							  $subID = $blankKey+1;
							  echo stripslashes($blankArray) . " <input id=\"" . $testData['id'] . $subID . "\" name=\"" . $testData['id'] . $subID . "\" autocomplete=\"off\" type=\"text\" size=\"25\" class=\"validate[required]\" /> ";
						  } else {
							  echo stripslashes($blankArray);
						  }
					  }
				  }
				  
				  echo "<br /><br /></td></tr>"; break;
			  
		  //If the question is a matching value
			  case "Matching" : 
			  //Echo the point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><span class=\"questionPoints\">" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</span>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				  }
			  //Echo the matching content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br />";
				  //Grab the necessary data
				  $question = unserialize($testData['questionValue']);
				  $answer = unserialize($testData['answerValue']);
				  $answerValues = shuffle($answer);
				  $valueNumbers = sizeof($question);
				  //Display the left column
				  echo "<table width=\"100%\"><tr><td width=\"200\">";
				  echo "<table width=\"200\">";
				  $matchingCount = 1;
				  while (list($matchingKey, $matchingArray) = each($question)) {
					  echo "<tr><td width=\"20\"><select id=\"" . $testData['id'] . $matchingCount . "\" type=\"select\" name=\"" . $testData['id'] . $matchingCount++ . "\" class=\"validate[required]\"/><option value=\"\" selected=\"selected\">-</option>"; 
					  for ($value = 1; $value <= $valueNumbers; $value++) {
						  echo "<option value=\"". $value . "\">" . $value . "</option>";
					  }
					  echo"</td><td>" . $matchingArray . "</td></tr>";
				  }
				  echo "</table>";
				  echo "</td><td>";
				  //Display the right column
				  echo "<table>";
				  while (list($matchingKey, $matchingArray) = each($answer)) {
					  $number = $matchingKey+1;
					  echo "<tr><td>" . $number . ". " . stripslashes($matchingArray) . "</td></tr>";
				  }
			  echo "</table>";
			  echo "</td></tr></table>";
			  echo"<br /><br /></td></tr>"; break;
			  
		  //If the question is a multiple choice
			  case "Multiple Choice" : 
			  //Echo the point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><span class=\"questionPoints\">" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</span>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				  }
			  //Echo the multiple choice content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br />";
				  $answers = unserialize($testData['answerValue']);
				  if ($testData['randomize'] == "1") {
					  $answersDisplay = shuffle($answers);
				  } else {
					  $answersDisplay = $answers;
				  }
				  
				  $start = sizeof ($answers);
				  if ($testData['choiceType'] == "radio") {
					  while (list($answerKey, $answerArray) = each($answers)) {
						  $labelLink = $answerKey+1;
						  echo "<label><input id=\"" . $testData['id'] . $labelLink . "\" name=\"" . $testData['id'] . "\" value=\"" . $answerArray . "\" type=\"radio\" class=\"validate[required] radio\">" . $answerArray . "</label><br />";
					  }
				  } else {
					  while (list($answerKey, $answerArray) = each($answers)) {
						  $labelLink = $answerKey+1;
						  echo "<label><input id=\"" . $testData['id'] . $labelLink . "\" name=\"" . $testData['id'] . "\" value=\"" . $answerArray . "\" type=\"checkbox\" class=\"validate[mincheckbox[1]]\">" . $answerArray . "</label><br />";
					  }
				  }
				  echo "<br /><br /></td></tr>"; break;
				  
		  //If the question is a short answer
			  case "Short Answer" : 
			  //Echo the point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><span class=\"questionPoints\">" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</span>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				  }
			  //Echo the short answer content	
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br /><input size=\"50\" id=\"" . $testData['id'] . "\" name=\"" . $testData['id'] . "\" type=\"text\" class=\"validate[required]\"><br /><br /></td></tr>"; break;
				  
		  //If the question is true or false
			  case "True False" : 
				  //Echo the point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><span class=\"questionPoints\">" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</span>";
				  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				  }
				  //Echo the true or false content	
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br /><label><input id=\"" . $testData['id'] . "1\" name=\"" . $testData['id'] . "\" value=\"1\" type=\"radio\" class=\"validate[required] radio\">True</label><br /><label><input id=\"" . $testData['id'] . "0\" name=\"" . $testData['id'] . "\" value=\"0\" type=\"radio\" class=\"validate[required] radio\">False</label><br /><br /></td></tr>"; break;
		  }
	  }
//Echo the closing table HTML
	  echo "</table>";
?>
<?php
//Include the inline javascript validator instructions
	$validatorCheck = mysql_query("SELECT * FROM `questionbank` WHERE `type` = 'Essay'", $connDBA);
	
	if (mysql_fetch_array($validatorCheck)) {
		$validatorGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `type` = 'Essay'", $connDBA);
		$count = 1;
		
		echo "<script type=\"text/javascript\">";
		
		while ($validator = mysql_fetch_array($validatorGrabber)) {
			if ($validator['type'] == "Essay" && $validator['id'] == $id) {
				echo "var sprytextarea" . $count++ . " = new Spry.Widget.ValidationTextarea(\"checkEssay" . $validator['id'] . "\");";
			}
		}
		
		echo "</script>";
	}
?>
</body>
</html>