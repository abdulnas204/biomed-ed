<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Select a question
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		
		$questionCheck = mysql_query("SELECT * FROM `feedback` WHERE `id` = '{$id}' LIMIT 1", $connDBA);
		if (mysql_fetch_array($questionCheck)) {
		} else {
			die("The feedback question does not exist.");
		}
	} else {
		die("A required parameter is missing.");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Preview Feedback Question</title>
<?php tinyMCESimple(); ?>
</head>

<body>
<?php tooltip(); ?>
<h2>Preview Feedback Question</h2>
<?php  
//Echo the opening table HTML
	  echo "<table width=\"100%\" class=\"dataTable\">";
	  
	  $feedbackDataGrabber = mysql_query("SELECT * FROM `feedback` WHERE `id` = '{$id}' LIMIT 1", $connDBA);
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
			  //Provide a spacer
				  echo "<tr><td width=\"100\" valign=\"top\"></td>";
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
			  //Provide a spacer
				  echo "<tr><td width=\"100\" valign=\"top\"></td>";
			  //Echo the short answer content	
				  echo "<td valign=\"top\">" . stripslashes($feedbackData['question']) . "<br /><br/ ><input type=\"text\" size=\"50\" id=\"" . $feedbackData['id'] . "\" name=\"" . $feedbackData['id'] . "\"><br /><br/ ></td></tr>"; break;
				  
		   //If the question is an written response
			  case "Written Response" : 
			  //Provide a spacer
				  echo "<tr><td width=\"100\" valign=\"top\"></td>";
			  //Echo the written response content
				  echo "<td valign=\"top\">" . stripslashes($feedbackData['question']) . "<br /><br/ ><textarea id=\"" . $feedbackData['id'] . "\" name=\"" . $feedbackData['id'] . "\" style=\"width:450px;\"></textarea><br /><br/ ></td></tr>"; break;
		  }
	  }
//Echo the closing table HTML
	  echo "</table>";
?>
</body>
</html>