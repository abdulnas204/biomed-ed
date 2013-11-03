<?php require_once('../../system/connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Select a question
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		
		$questionCheck = mysql_query("SELECT * FROM `feedback` WHERE `id` = '{$id}' LIMIT 1", $connDBA);
		if (mysql_fetch_array($questionCheck)) {
			//Do nothing
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
<?php title("Preview Feedback Question"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
</head>

<body class="overrideBackground">
<h2 class="preview">Preview Feedback Question</h2>
<?php  
//Echo the opening table HTML
	  echo "<table width=\"100%\" class=\"dataTable\">";
	  
	  $testDataGrabber = mysql_query("SELECT * FROM `feedback` WHERE `id` = '{$id}' LIMIT 1", $connDBA);
	  $count = 1;
//Loop through the items
	  while ($testData = mysql_fetch_array($testDataGrabber)) {
	  //Detirmine what kind of question is being displayed
		  switch ($testData ['type']) {
		  //If the question is a description
			  case "Description" : 
			  //Echo the description, without the any numbering or point value
				  echo "<tr><td valign=\"top\" colspan=\"2\">" . stripslashes($testData['question']) . "</td></tr>"; break;
				  
		  //If the question is a multiple choice
			  case "Multiple Choice" : 
			  //Provide a buffer on the left
				  echo "<tr><td width=\"100\" valign=\"top\"><p>&nbsp;</p></td>";
			  //Echo the multiple choice content
				  echo "<td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br />";
				  $question = unserialize($testData['questionValue']);
				  
				  $start = sizeof ($question);
				  if ($testData['choiceType'] == "radio") {
					  while (list($questionKey, $questionArray) = each($question)) {
						  $labelLink = $questionKey+1;
						  echo "<label><input id=\"" . $testData['id'] . $labelLink . "\" name=\"" . $testData['id'] . "\" value=\"" . $questionArray . "\" type=\"radio\" class=\"validate[required] radio\">" . $questionArray . "</label><br />";
					  }
				  } else {
					  while (list($questionKey, $questionArray) = each($question)) {
						  $labelLink = $questionKey+1;
						  echo "<label><input id=\"" . $testData['id'] . $labelLink . "\" name=\"" . $testData['id'] . "\" value=\"" . $questionArray . "\" type=\"checkbox\" class=\"validate[mincheckbox[1]]\">" . $questionArray . "</label><br />";
					  }
				  }
				  echo "<br /><br /></td></tr>"; break;
				  
		  //If the question is a short answer
			  case "Short Answer" : 
			  //Provide a buffer on the left
				  echo "<tr><td width=\"100\" valign=\"top\"><p>&nbsp;</p></td>";
			  //Echo the short answer content	
				  echo "<td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br /><input size=\"50\" id=\"" . $testData['id'] . "\" name=\"" . $testData['id'] . "\" type=\"text\" class=\"validate[required]\"><br /><br /></td></tr>"; break;
				  
		  //If the question is a written response
			  case "Written Response" : 
			  //Provide a buffer on the left
				  echo "<tr><td width=\"100\" valign=\"top\"><p>&nbsp;</p></td>";
			  //Echo the essay content
				  echo "<td valign=\"top\">" . stripslashes($testData['question']) . "<br /><br /><span id=\"checkEssay" . $testData['id'] . "\"><textarea id=\"" . $testData['id'] . "\" name=\"" . $testData['id'] . "\" style=\"width:450px;\"></textarea><span class=\"textareaRequiredMsg\"></span></span><br /><br /></td></tr>"; break;
		  }
	  }
//Echo the closing table HTML
	  echo "</table>";
?>
<?php
//Include the inline javascript validator instructions
	$validatorCheck = mysql_query("SELECT * FROM `feedback` WHERE `type` = 'Written Response'", $connDBA);
	
	if (mysql_fetch_array($validatorCheck)) {
		$validatorGrabber = mysql_query("SELECT * FROM `feedback` WHERE `type` = 'Written Response' ORDER BY `position` ASC", $connDBA);
		$count = 1;
		
		echo "<script type=\"text/javascript\">";
		
		while ($validator = mysql_fetch_array($validatorGrabber)) {			
			if ($validator['type'] == "Written Response" && $validator['id'] == $id) {
				echo "var sprytextarea" . $count++ . " = new Spry.Widget.ValidationTextarea(\"checkEssay" . $validator['id'] . "\");";
			}
		}
		
		echo "</script>";
	}
?>
</body>
</html>