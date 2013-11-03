<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			//case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		header ("Location: modify.php");
		exit;	
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Select the module name, to fill in all test data
	//Use the session to find where to select the test data
	$currentModule = $_SESSION['currentModule'];
	$currentTable = str_replace(" ","", $currentModule);
	
	//Grab the test information
	$testInfoGrabber = mysql_query ("SELECT * FROM moduledata WHERE name = '{$currentModule}'", $connDBA);
	$testInfo = mysql_fetch_array($testInfoGrabber);
?>
<?php
//Update a session to go to previous steps
	if (isset ($_POST['back'])) {
		$_SESSION['step'] = "testContent";
		header ("Location: test_content.php");
		exit;
	}
?>
<?php
//Update a session to go to previous steps
	if (isset ($_POST['next'])) {
		header ("Location: complete.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Test"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../../javascripts/common/floatingLayer.js" type="text/javascript"></script>
<script src="../../../javascripts/insert/newFileUpload.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Module Setup Wizard : Verify Test Content</h2>
    <p>Content may be reviewed in the section below. Changes can be made to  the lesson by clicking the &quot;Make Changes&quot; button, and modifying the test.</p>
<div class="toolBar">
<b>Test Name</b>: <?php echo $testInfo['testName']; ?><br />
<b>Directions</b>: <?php $htmlArray = array("<p>", "</p>"); $directions = str_replace($htmlArray, "", $testInfo['directions']); echo $directions; ?>
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
//Display a randomizing alert if any part of this test randomizes
	$randomizeGrabber = mysql_query ("SELECT * FROM moduletest_{$currentTable} WHERE randomize = '1'", $connDBA);
	if (mysql_fetch_array($randomizeGrabber)) {
		$randomize = "true";
	} else {
		$randomize = "false";
	}
	
	if ($testInfo['randomizeAll'] == "Randomize" || $randomize == "true") {
		alert("Since you are only previewing this test, note the questions may appear in a differnet order if the page is refreshed, or left and returned to later.");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<form name="testVerify" method="post" action="test_verify.php" id="validate" onsubmit="return errorsOnSubmit(this);">
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
//Echo the closing table HTML
	  echo "</table>";
?>
      <blockquote>
        <p>
          <input type="submit" name="back" id="back" value="&lt;&lt; Make Changes" />
          <input type="submit" name="next" id="next" value="Next Step &gt;&gt;" />
        </p>
        <?php formErrors(); ?>
      </blockquote>
      </form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>