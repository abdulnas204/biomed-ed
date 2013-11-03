<?php require_once('../system/connections/connDBA.php'); ?>
<?php loginCheck("Student,Instructor,Organization Administrator,Site Manager,Site Administrator"); ?>
<?php
//Select all questions
	$questionCheck = mysql_query("SELECT * FROM `feedback`", $connDBA);
	if (mysql_fetch_array($questionCheck)) {
	} else {
		header("Location: index.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Feedback"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Feedback</h2>
<p>Please provide feedback on what you think of our course.</p>
<p>&nbsp;</p>
<form name="feedback" method="post" action="feedback.php" id="validate" onsubmit="return errorsOnSubmit(this);">
<?php  
//Echo the opening table HTML
	  echo "<table width=\"100%\" class=\"dataTable\">";
	  
	  $feedbackDataGrabber = mysql_query("SELECT * FROM `feedback`", $connDBA);
	  $count = 1;
//Loop through the items
	  while ($feedbackData = mysql_fetch_array($feedbackDataGrabber)) {
	  //Detirmine what kind of question is being displayed
		  switch ($feedbackData ['type']) {
		  //If the question is a description
			  case "Description" : 
			  //Echo the description, without the any numbering or point value
				  echo "<tr><td colspan=\"2\" valign=\"top\">" . stripslashes($feedbackData['question']) . "</td></tr>"; break;
				  
		  //If the question is a multiple choice
			  case "Multiple Choice" : 
			  //Echo the question number
				  echo "<tr><td width=\"100\" valign=\"top\"><strong>Question " . $count++ . "</strong></td>";
			  //Echo the multiple choice content
				  echo "<td valign=\"top\">" . stripslashes($feedbackData['question']) . "<br /><br/ >";
				  $questions = unserialize($feedbackData['questionValue']);
				  $start = sizeof ($questions);
				  if ($feedbackData['choiceType'] == "radio") {
					  while (list($questionKey, $questionArray) = each($questions)) {
						  $labelLink = $questionKey+1;
						  echo "<label><input type=\"radio\" name=\"" . $feedbackData['id'] . "\" id=\"" . $feedbackData['id'] . "." . $labelLink . "\" value=\"" . $questionArray . "\">" . $questionArray . "</label><br />";
					  }
				  } else {
					  while (list($questionKey, $questionArray) = each($questions)) {
						  $labelLink = $questionKey+1;
						  echo "<label><input type=\"checkbox\" name=\"" . $feedbackData['id'] . "\" id=\"" . $feedbackData['id'] . "." . $labelLink . "\" value=\"" . $questionArray . "\">" . $questionArray . "</label><br />";
					  }
				  }
				  echo "<br /><br/ ></td></tr>"; break;
				  
		  //If the question is a short answer
			  case "Short Answer" : 
			  //Echo the question number
				  echo "<tr><td width=\"100\" valign=\"top\"><strong>Question " . $count++ . "</strong></td>";
			  //Echo the short answer content	
				  echo "<td valign=\"top\">" . stripslashes($feedbackData['question']) . "<br /><br/ ><input type=\"text\" size=\"50\" id=\"" . $feedbackData['id'] . "\" name=\"" . $feedbackData['id'] . "\"><br /><br/ ></td></tr>"; break;
				  
		   //If the question is an written response
			  case "Written Response" : 
			  //Echo the question number
				  echo "<tr><td width=\"100\" valign=\"top\"><strong>Question " . $count++ . "</strong></td>";
			  //Echo the written response content
				  echo "<td valign=\"top\">" . stripslashes($feedbackData['question']) . "<br /><br/ ><textarea id=\"" . $feedbackData['id'] . "\" name=\"" . $feedbackData['id'] . "\" style=\"width:450px;\"></textarea><br /><br/ ></td></tr>"; break;
		  }
	  }
//Echo the closing information
	  echo "<tr><td colspan=\"2\"><blockquote>";
	  submit("submit", "Submit");
	  submit("save", "Save");
	  formErrors();
	  echo "</blockquote></td></tr></table>";
?>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>