<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Check to see if any feed back data exists
	$feedBackDataCheckGrabber = mysql_query("SELECT * FROM feedback", $connDBA);
	
	if ($feedBackDataCheck = mysql_fetch_array($feedBackDataCheckGrabber)) {
		$feedBackDataResult = $feedBackDataCheck['id'];
	}
	
	if (isset($feedBackDataResult)) {
		$feedBack = "exist";
	} else {
		$feedBack = "empty";
	}
?>
<?php
//Reorder data
	if (isset ($_GET['currentPosition']) && isset ($_GET['id'])) {
	//Grab all necessary data	
		//Grab the id of the moving item
		$id = $_GET['id'];
		//Grab the new position of the item
		$newPosition = $_GET['position'];
		//Grab the old position of the item
		$currentPosition = $_GET['currentPosition'];
			
	//Do not process if item does not exist
		//Get item name by URL variable
		$getQuestionID = $_GET['currentPosition'];
	
		$questionCheckGrabber = mysql_query("SELECT * FROM feedback WHERE position = {$getQuestionID}", $connDBA);
		$questionCheckArray = mysql_fetch_array($questionCheckGrabber);
		$questionCheckResult = $questionCheckArray['position'];
			 if (isset ($questionCheckResult)) {
				 $questionCheck = 1;
			 } else {
				$questionCheck = 0;
			 }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE feedback SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE feedback SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE feedback SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE feedback SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: index.php");
			exit;
		}
	}
?>
<?php
//Delete a feedback question
	if (isset ($_GET['question'])) {
	//Do not process if question does not exist
	//Get question by URL variable
		$getQuestionID = $_GET['question'];
	
		$questionCheckGrabber = mysql_query("SELECT * FROM feedback WHERE position = {$getQuestionID}", $connDBA);
		$questionCheckArray = mysql_fetch_array($questionCheckGrabber);
		$questionCheckResult = $questionCheckArray['position'];
		
	   if (isset ($questionCheckResult)) {
		   $questionCheck = 1;
	   } else {
		  $questionCheck = 0;
	   }
	}
 
    if (isset ($_GET['question']) && isset ($_GET['id']) && $questionCheck == "1") {
        $deleteQuestion = $_GET['id'];
		$questionLift = $_GET['question'];
		$currentTable = str_replace(" ", "", $_SESSION['currentModule']);
        
        $questionPositionGrabber = mysql_query("SELECT * FROM feedback WHERE position = {$questionLift}", $connDBA);
        $questionPositionFetch = mysql_fetch_array($questionPositionGrabber);
        $questionPosition = $questionPositionFetch['position'];
		
		switch ($questionPositionFetch['type']) {
			case "Description" : $type = "description"; break;
			case "Multiple Choice" : $type = "choice"; break;
			case "Short Answer" : $type = "answer"; break;
			case "Written Response" : $type = "written"; break;
		}
		
        $otherQuestionsUpdateQuery = "UPDATE feedback SET position = position-1 WHERE position > '{$questionPosition}'";
        $deleteQuestionQueryResult = mysql_query($otherQuestionsUpdateQuery, $connDBA);
        
        $deleteQuestionQuery = "DELETE FROM feedback WHERE id = {$deleteQuestion}";
        $deleteQuestionQueryResult = mysql_query($deleteQuestionQuery, $connDBA);
		
		header ("Location: index.php?deleted=" . $type);
		exit;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Feedback"); ?>
<?php headers(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      <h2>Feedback</h2>
      <p>Content may be added to the test by using the guide below.</p>
      <p>&nbsp;</p>
<div class="toolBar">
         <form name="jump" id="validate" onsubmit="return errorsOnSubmit(this);">
                  Add: 
                  <select name="menu" id="menu">
                    <option value="">- Select Question Type -</option>
                    <option value="questions/description.php">Description</option>
                    <option value="questions/multiple_choice.php">Multiple Choice</option>
                    <option value="questions/short_answer.php">Short Answer</option>
                    <option value="questions/written_response.php">Written Response</option>
                  </select>
                  <?php formErrors(); ?>
                  <input type="button" onclick="location=document.jump.menu.options[document.jump.menu.selectedIndex].value;" value="Go" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../index.php"><img src="../../../images/admin_icons/home.png" alt="Back" width="24" height="24"/></a> <a href="../index.php">Back to Modules</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="settings.php"><img src="../../../images/admin_icons/settings.png" alt="Settings" width="24" height="24" /></a> <a href="settings.php">Customize Settings</a>
         </form>
</div>
<?php
//If an inserted alert is shown
  if (isset ($_GET['inserted'])) {
	  echo "<br/ ><div align=\"center\"><div class=\"success\">The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['inserted']) {
		  case "description" : echo "description"; break;
		  case "choice" : echo "multiple choice"; break;
		  case "answer" : echo "short answer"; break;
		  case "written" : echo "written response"; break;
	  }
	  echo "</strong> question was successfully inserted</div></div><br />";
//If an updated alert is shown
  } elseif (isset ($_GET['updated'])) {
	  echo "<br/ ><div align=\"center\"><div class=\"success\">The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['updated']) {
		  case "description" : echo "description"; break;
		  case "choice" : echo "multiple choice"; break;
		  case "answer" : echo "short answer"; break;
		  case "written" : echo "written response"; break;
	  }
	  echo "</strong> question was successfully updated</div></div><br />";
//If an deleted alert is shown  
  } elseif (isset ($_GET['deleted'])) {
	  echo "<br/ ><div align=\"center\"><div class=\"success\">The <strong>";
	  //Detirmine what kind of alert this will be
	  switch ($_GET['deleted']) {
		  case "description" : echo "description"; break;
		  case "choice" : echo "multiple choice"; break;
		  case "answer" : echo "short answer"; break;
		  case "written" : echo "written response"; break;
	  }
	  echo "</strong> question was successfully deleted</div></div><br />";
  } else {
	  echo "&nbsp;";
  }
?>
<?php
//The test questions
	if ($feedBack == "exist") {
		echo "<div align=\"center\">";
			echo "<table align=\"center\" class=\"dataTable\">";
			echo "<tbody>";
				echo "<tr>";
					echo "<th width=\"100\" class=\"tableHeader\"><strong>Order</strong></th>";
					echo "<th width=\"150\" class=\"tableHeader\"><strong>Type</strong></th>";
					echo "<th class=\"tableHeader\"><strong>Question</strong></th>";
					echo "<th width=\"50\" class=\"tableHeader\"><strong>Edit</strong></th>";
					echo "<th width=\"50\" class=\"tableHeader\"><strong>Delete</strong></th>";
				echo "</tr>";
				
			//Select data for the table	
				$feedBackDataGrabber = mysql_query ("SELECT * FROM feedBack ORDER BY position ASC", $connDBA);	
				
			//Select data for drop down menu
				$dropDownDataGrabber = mysql_query("SELECT * FROM feedBack ORDER BY position ASC", $connDBA);
				
				while ($feedBackData = mysql_fetch_array($feedBackDataGrabber)) {					
					echo "<tr";
					if ($feedBackData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					">";
						echo "<form action=\"index.php\">";
						echo "<input type=\"hidden\" name=\"currentPosition\" value=\"" . $feedBackData['position'] . "\" />";
						echo "<input type=\"hidden\" name=\"id\" value=\"" . $feedBackData['id'] . "\" />";
						echo "<td width=\"100\"><div align=\"center\"><div>";
								echo "<select name=\"position\" onchange=\"this.form.submit();\">";
								$feedBackCount = mysql_num_rows($dropDownDataGrabber);
								for ($count=1; $count <= $feedBackCount; $count++) {
									echo "<option value=\"{$count}\"";
									if ($feedBackData ['position'] == $count) {
										echo " selected=\"selected\"";
									}
									echo ">$count</option>";
								}
								echo "</select>";
							echo "</div></div></td>";
						echo "<td width=\"150\"><div align=\"center\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('preview.php?id=" . $feedBackData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\" onmouseover=\"Tip('Preview this <strong>" . $feedBackData['type'] . "</strong> question')\" onmouseout=\"UnTip()\">" . $feedBackData['type'] . "</a></div></td>";
						echo "<td align=\"center\"><div align=\"center\">" . commentTrim(55, $feedBackData['question']) . "</div></td>";
						echo "<td width=\"50\"><div align=\"center\">" . "<a href=\"";
						
						switch ($feedBackData['type']) {
							case "Description" : echo "questions/description.php"; break;
							case "Multiple Choice" : echo "questions/multiple_choice.php"; break;
							case "Short Answer" : echo "questions/short_answer.php"; break;
							case "Written Response" : echo "questions/written_response.php"; break;
						}
							
						echo "?question=" . $feedBackData['position'] . "&id=" . $feedBackData['id'] . "\">" . "<img src=\"../../../images/admin_icons/edit.png\" alt=\"Edit\" border=\"0\" onmouseover=\"Tip('Edit this <strong>" . $feedBackData['type'] . "</strong> question')\" onmouseout=\"UnTip()\">" . "</a>" . "</div></td>";
						echo "<td width=\"50\"><div align=\"center\">" . "<a href=\"index.php?question=" .  $feedBackData['position'] . "&id=" .  $feedBackData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\">" . "<img src=\"../../../images/admin_icons/delete.png\" alt=\"Delete\" border=\"0\" onmouseover=\"Tip('Delete this <strong>" . $feedBackData['type'] . "</strong> question')\" onmouseout=\"UnTip()\">" . "</a></div></td>";
					echo "</form>";
					echo "</tr>";
				}
			echo "</tbody>";
		echo "</table></div>";
	} else {
		echo "<br /></br /><br /></br /><div align=\"center\">There are no feedback questions. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div><br /></br /><br /></br /><br /></br />";
	}
?>
<p>&nbsp;</p>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>