<?php require_once('../../system/connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Process the given data
	if (isset($_GET['linkID'])) {
		$linkID = $_GET['linkID'];
		$questionDataGrabber = mysql_query("SELECT * FROM `questionbank` WHERE `id` = '{$linkID}'", $connDBA);
		
		if ($questionData = mysql_fetch_array($questionDataGrabber)) {
			$testCheck = mysql_query("SELECT * FROM `moduledata`");
			
			if (mysql_fetch_array($testCheck)) {
				$location = "<fieldset><legend>This question appears in the following test(s):</legend><ul>";
				$testDataGrabber = mysql_query("SELECT * FROM `moduledata`");
				
				while ($testData = mysql_fetch_array($testDataGrabber)) {
					$testName = strtolower(str_replace(" ", "", $testData['name']));
					$testInfoGrabber = mysql_query("SELECT * FROM `moduletest_{$testName}` WHERE `linkID` = '{$linkID}'", $connDBA);
					
					if ($testInfo = mysql_fetch_array($testInfoGrabber)) {
						$location .= "<li>" . $testData['name'] . ", Question Number " . $testInfo['position'] . "</li>";
					}
				}
				
				$location .= "</ul></fieldset>";
				$title = "Results for the " . $questionData['type'] . " Question";
			} else {
				$title = "No Results Found";
			}
		} else {
			$title = "No Results Found";
		}
	} else {
		header("Location: index.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title($title); ?>
<?php headers(); ?>
</head>

<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<?php
//Display the page content
	echo "<h2>" . $title . "</h2><p>This discovery page will show in which tests questions from the question bank are used.</p><p>&nbsp;</p>";

	if (isset($location) && $location != "<fieldset><legend>This question appears in the following test(s):</legend><ul></ul></fieldset>") {	
		echo $location;
	} else {
		echo errorMessage("This question does not appear in any tests.");
	}
?>
<blockquote>
	<p>
		<input name="finish" id="finish" onclick="history.go(-1)" value="Finish" type="button">
	</p>
</blockquote>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>