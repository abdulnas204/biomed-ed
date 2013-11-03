<?php require_once('../Connections/connDBA.php'); ?>
<?php loginCheck("Student,Instructor,Organization Administrator,Site Manager,Site Administrator"); ?>
<?php
//Select the module id, to fill in all test data
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
		$testCheck= mysql_query ("SELECT * FROM moduledata WHERE id = '{$id}'", $connDBA);
		
		if (mysql_fetch_array($testCheck)) {
			$testInfoGrabber = mysql_query ("SELECT * FROM moduledata WHERE id = '{$id}'", $connDBA);
			$testInfo = mysql_fetch_array($testInfoGrabber);
			$currentTable = str_replace(" ", "", $testInfo['name']);
		} else {
			header ("Location: index.php");
			exit;
		}
	} else {
		header ("Location: index.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	$title = $testInfo['testName'];
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<script src="../javascripts/insert/newFileUpload.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2><?php echo $title; ?></h2>
<div class="toolBar">
<b>Directions</b>: <?php echo commentTrim(100000, $testInfo['directions']); ?>
<?php
//Display a forced completion alert
	if ($testInfo['forceCompletion'] == "on") {
		echo "<br /><b>Force Completion</b>: This test must be completed now, otherwise penalties will be applied";
	}
?>
<?php
//Display a timer alert
	if ($testInfo['timer'] == "on") {
		$time = unserialize($testInfo['time']);
		if ($testInfo['time'] !== "") {
			$testH = $time['0'];
			$testM = $time['1'];
		}
		
		echo "<br /><b>Time limit</b>: This test must be completed within <strong>" . $time['0'];
		if ($time['0'] == "1") {
			echo " hour and ";
		} elseif ($testH !== "1") {
			echo " hours and ";
		}
		
		echo $time['1'] . " minutes</strong>, otherwise the test will close.";
	}
?>
</div>
<?php
//Display link back to the lesson, if premitted
	if ($testInfo['reference'] == "1") {
		echo "<br /><div class=\"layoutControl\"><div class=\"contentLeft\"><div align=\"left\"><a href=\"lesson.php?id=" . $id . "\">&lt;&lt; Back to Lesson<br /><strong>" . $testInfo['name'] . "</strong></a></div></div></div>";
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<form name="test" method="post" action="test.php?id=<?php echo $_GET['id']; ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<?php
//Grab the test data and display the info
  if ($testInfo['randomizeAll'] == "Randomize") {
	  $testDataGrabber = mysql_query ("SELECT * FROM moduletest_{$currentTable} ORDER BY RAND() ASC", $connDBA);
  } else {
	  $testDataGrabber = mysql_query ("SELECT * FROM moduletest_{$currentTable} ORDER BY position ASC", $connDBA);
  }
  
//Echo the opening table HTML
	  echo "<table width=\"100%\" class=\"dataTable\">";
	  
 //Define a base number to use to list each of the question numbers
	  $count = 1;
	  
 //Loop through the items
	  while ($testDataLoop = mysql_fetch_array($testDataGrabber)) {
	  //Detirmine whether or not this question will come from the question bank, and pull accordingly
		  if ($testDataLoop['questionBank'] == "1") {
			  $importID = $testDataLoop['linkID'];
			  $importQuestion = mysql_query("SELECT * FROM questionBank WHERE `id` = '{$importID}'", $connDBA);
			  $testData = mysql_fetch_array($importQuestion);
		  } else {
			  $testData = $testDataLoop;
		  }
		  
	  //Detirmine what kind of question is being displayed
		  switch ($testData ['type']) {
		  //If the question is a description
			  case "Description" : 
			  //Echo the description, without the any numbering or point value
				  echo "<tr><td colspan=\"2\" valign=\"top\">" . stripslashes($testData['question']) . "</td></tr>"; break;
				  
		  //If the question is an essay
			  case "Essay" : 
			  //Echo the number and point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><strong>Question " . $count++ . "</strong><br/><small><strong>" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</strong></small>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"><img src=\"../../../images/common/extraCredit.png\" width=\"16\" height=\"16\"></span>";
				  }
			  //Echo the essay content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br/ ><textarea id=\"" . $testData['id'] . "\" name=\"" . $testData['id'] . "\" style=\"width:450px;\"></textarea><br /><br/ ></td></tr>"; break;
				  
		  //If the question a file response
			  case "File Response" : 
			  //Echo the number and point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><strong>Question " . $count++ . "</strong><br/><small><strong>" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</strong></small>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"><img src=\"../../../images/common/extraCredit.png\" width=\"16\" height=\"16\"></span>";
				  }
			  //Echo the file response content	
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br/ >";
				  if ($testData['totalFiles'] > 1) {
					  echo "<table name=\"upload" . $testData['id'] . "\" id=\"upload" . $testData['id'] . "\"><tr><td><input type=\"file\" size=\"50\" id=\"" . $testData['id'] . ".1\" name=\"" . $testData['id'] . ".1\" style=\"width:450px;\"></td></tr>";
					  
					  echo "</table><input value=\"Add Another File\" type=\"button\" id=\"button" . $testData['id'] . "\" onclick=\"appendRow('upload" . $testData['id'] . "', '<input name=\'" . $testData['id'] . ".', '\' type=\'file\' size=\'50\' id=\'" . $testData['id'] . ".', '\' style=\'width:450px;\' />', '" . $testData['totalFiles'] . "', 'button" . $testData['id'] . "');\" /><input value=\"Remove Last File\" type=\"button\" onclick=\"deleteLastRow('upload" . $testData['id'] . "', 'button" . $testData['id'] . "');\" /><br />";
				  } else {
					  echo "<input type=\"file\" size=\"50\" id=\"" . $testData['id'] . "." . $testData['totalFiles'] . "\" name=\"" . $testData['id'] . "." . $testData['totalFiles'] . "\" style=\"width:450px;\"><br />";
				  }
				  echo "<br/ ></td></tr>"; break;
				  
		  //If the question is a fill in the blank
			  case "Fill in the Blank" : 
			  //Echo the number and point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><strong>Question " . $count++ . "</strong><br/><small><strong>" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</strong></small>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"><img src=\"../../../images/common/extraCredit.png\" width=\"16\" height=\"16\"></span>";
				  }
			  //Echo the fill in the blank content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br/ >"; 
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
						  echo stripslashes($blankArray) . " <input name=\"" . $testData['id'] . "." . $subID . "\" autocomplete=\"off\" type=\"text\" id=\"" . $testData['id'] . "." . $subID . "\" size=\"25\" /> ";
					  }
				  } else {
				  //Echo the fill in the blank content content without the last value
					  while (list($blankKey, $blankArray) = each($blank)) {						
						  if ($blankKey !== $lastValue) {
							  $subID = $blankKey+1;
							  echo stripslashes($blankArray) . " <input name=\"" . $testData['id'] . "." . $subID . "\" autocomplete=\"off\" type=\"text\" id=\"" . $testData['id'] . "." . $subID . "\" size=\"25\" /> ";
						  } else {
							  echo stripslashes($blankArray);
						  }
					  }
				  }
				  
				  echo "<br /><br/ ></td></tr>"; break;
			  
		  //If the question is a matching value
			  case "Matching" : 
			  //Echo the number and point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><strong>Question " . $count++ . "</strong><br/><small><strong>" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</strong></small>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"><img src=\"../../../images/common/extraCredit.png\" width=\"16\" height=\"16\"></span>";
				  }
			  //Echo the matching content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br/ >";
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
					  echo "<tr><td width=\"20\"><select name=\"" . $testData['id'] . "\" type=\"select\" id=\"" . $testData['id'] . "." . $matchingCount++ . "\"/><option value=\"\" selected=\"selected\">-</option>"; 
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
			  echo"<br /><br/ ></td></tr>"; break;
			  
		  //If the question is a multiple choice
			  case "Multiple Choice" : 
			  //Echo the number and point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><strong>Question " . $count++ . "</strong><br/><small><strong>" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</strong></small>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"><img src=\"../../../images/common/extraCredit.png\" width=\"16\" height=\"16\"></span>";
				  }
			  //Echo the multiple choice content
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br/ >";
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
						  echo "<label><input type=\"radio\" name=\"" . $testData['id'] . "\" id=\"" . $testData['id'] . "." . $labelLink . "\" value=\"" . $answerArray . "\">" . $answerArray . "</label><br />";
					  }
				  } else {
					  while (list($answerKey, $answerArray) = each($answers)) {
						  $labelLink = $answerKey+1;
						  echo "<label><input type=\"checkbox\" name=\"" . $testData['id'] . "\" id=\"" . $testData['id'] . "." . $labelLink . "\" value=\"" . $answerArray . "\">" . $answerArray . "</label><br />";
					  }
				  }
				  echo "<br /><br/ ></td></tr>"; break;
				  
		  //If the question is a short answer
			  case "Short Answer" : 
			  //Echo the number and point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><strong>Question " . $count++ . "</strong><br/><small><strong>" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</strong></small>";
			  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"><img src=\"../../../images/common/extraCredit.png\" width=\"16\" height=\"16\"></span>";
				  }
			  //Echo the short answer content	
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br/ ><input type=\"text\" size=\"50\" id=\"" . $testData['id'] . "\" name=\"" . $testData['id'] . "\"><br /><br/ ></td></tr>"; break;
				  
		  //If the question is true or false
			  case "True False" : 
				  //Echo the number and point value
				  echo "<tr><td width=\"100\" valign=\"top\"><p><strong>Question " . $count++ . "</strong><br/><small><strong>" . $testData['points'] . " ";
				  if ($testData['points'] == "1") {
					  echo "Point";
				  } else {
					  echo "Points";
				  }
				  echo "</strong></small>";
				  //State whether or not this question is extra credit	
				  if ($testData['extraCredit'] == "on") {
					  echo "<br /><br /><span onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"><img src=\"../../../images/common/extraCredit.png\" width=\"16\" height=\"16\"></span>";
				  }
				  //Echo the true or false content	
				  echo "</p></td><td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br/ ><label><input type=\"radio\" id=\"" . $testData['id'] . "1\" name=\"" . $testData['id'] . "\" value=\"1\">True</label><br /><label><input type=\"radio\" id=\"" . $testData['id'] . "0\" name=\"" . $testData['id'] . "\" value=\"0\">False</label><br /><br/ ></td></tr>"; break;
		  }
	  }
//Echo the closing information
	  echo "<tr><td colspan=\"2\"><blockquote>";
	  submit("submit", "Submit");
	  submit("save", "Save");
	  formErrors();
	  echo "</blockquote></td></tr></table>";	  
?>
<?php
//Display link back to the lesson, if premitted
	if ($testInfo['reference'] == "1") {
		echo "<br /><div class=\"layoutControl\"><div class=\"contentLeft\"><div align=\"left\"><a href=\"lesson.php?id=" . $id . "\">&lt;&lt; Back to Lesson<br /><strong>" . $testInfo['name'] . "</strong></a></div></div></div>";
	}
?>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>