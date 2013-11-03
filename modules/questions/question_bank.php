<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	$monitor = monitor("Question Bank", "liveSubmit");
	require_once('functions.php');
	
//Select all categories
	if (exist("modulecategories")) {
	//Use the URL to narrow the categories down on request
		if (isset ($_GET['id'])) {
			$id = $_GET['id'];
			
		//Display any questions from categories whose questions may have been deleted
			if ($_GET['id'] == "0") {
				$currentCategories = query("SELECT `id` FROM `modulecategories` ORDER BY position ASC", "selected");
				$otherQuestionsGrabber = query("SELECT * FROM `questionbank` ORDER BY `id` ASC", "raw");
				$count = 0;
				$sql = "";
				
				while ($otherQuestions = mysql_fetch_array($otherQuestionsGrabber)) {
					if (in_array($otherQuestions['category'], $currentCategories)) {
						if ($count == 0) {
							$sql .= " WHERE`category` != '" . $otherQuestions['category'] . "'";
						} else {
							$sql .= " AND `category` != '" . $otherQuestions['category'] . "' ";
						}
						
						$count ++;
					}
				}
				
				$testImport = query("SELECT * FROM `questionbank`{$sql}ORDER BY id ASC", "raw");
					
				if (!$testImport) {
					redirect("index.php");
				}
			} else {
				$testImport = query("SELECT * FROM `questionbank` WHERE `category` = '{$id}' ORDER BY id ASC", "raw");
				
				if (!exist("modulecategories", "id", $id)) {
					redirect("index.php");
				} else {
					$bankTitle = query("SELECT * FROM `modulecategories` WHERE `id` = '{$id}'");
				}
			}
		}
	
		$categoryResult = 1;
	} else {
		$categoryResult = 0;
	}

//Process the form
	if (isset($_POST['id'])) {
		if ($_POST['import']) {
			if (exist("questionbank", "id", $_POST['id'])) {
				$id = $_POST['id'];
				$questionData = query("SELECT * FROM `questionbank` WHERE `id` = '{$id}'");
				$type = $questionData['type'];
				$lastQuestion = lastItem($monitor['testTable']);
				
				insertQuery("Module", "NULL, '1', '{$id}', '{$lastQuestion}', '{$type}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''");
				
				redirect("../module_wizard/test_content.php");
			}
		} else {
			$id = $_POST['id'];
			$questionPositionArray = query("SELECT * FROM `{$monitor['testTable']}` WHERE `linkID` = '{$id}'");
			$questionPosition = $questionPositionArray['position'];
			
			mysql_query("DELETE FROM `{$monitor['testTable']}` WHERE `linkID` = '{$id}'", $connDBA);
			mysql_query("UPDATE `{$monitor['testTable']}` SET position = position-1 WHERE position > '{$questionPosition}'", $connDBA);
			
			redirect("../module_wizard/test_content.php");
		}
	}
	
//Title
	title("Question Bank", "Questions may be imported into the current test via the question bank tool.");

//Admin toolbar
	if (isset ($_GET['id'])) {
		echo "<div class=\"toolBar\">";
		echo URL("Back to Module Categories", "question_bank.php", "toolBarItem back");
		echo "</div>";
	}

	if ($categoryResult != 0) {
		if (!isset ($_GET['id'])) {
			echo "<p>Please select a category from the list below.</p><blockquote>";
			$categoryGrabber = query("SELECT * FROM `modulecategories` ORDER BY position ASC", "raw");
			
			while ($category = mysql_fetch_array($categoryGrabber)) {
				$currentCategory = $category['id'];
				$questionValue = query("SELECT * FROM `questionBank` WHERE `category` = '{$currentCategory}'", "num");
				
				echo URL($category['category'], "question_bank.php?id=" . $category['id']) . " : ";
				
				if ($questionValue == 1) {
					echo $questionValue . " Question<br /><br />";
				} else {
					echo $questionValue . " Questions<br /><br />";
				}
			}
			
		//Display any questions from categories whose questions may have been deleted
			$currentCategories = query("SELECT `id` FROM `modulecategories` ORDER BY position ASC", "selected");
			$otherQuestionsGrabber = query("SELECT * FROM `questionbank` ORDER BY `id` ASC", "raw");
			$count = 0;
			
			if ($otherQuestionsGrabber) {
				while ($otherQuestions = mysql_fetch_array($otherQuestionsGrabber)) {
					if (!inArray($otherQuestions['category'], $currentCategories)) {
						$count++;
					}
				}
			}
			
			if ($count > 0) {
				echo URL ("Uncategorized", "question_bank.php?id=0") . " : ";
				
				if ($count == 1) {
					echo $count . " Question<br /><br />";
				} else {
					echo $count . " Questions<br /><br />";
				}
			}
			
			echo "<br /><br />";
			button("cancel", "cancel", "Back to Test Questions", "cancel", "../module_wizard/test_content.php");
			echo "</blockquote>";
		}
		
		if (isset ($_GET['id'])) {	
			echo "<br />";
			if ($testImport) {
				catDivider("Select Questions", "one", true);
				echo "<blockquote><table class=\"dataTable\"><tbody><tr><th width=\"50\" class=\"tableHeader\">Import</th><th width=\"150\" class=\"tableHeader\">Type</th><th width=\"100\" class=\"tableHeader\">Point Value</th><th class=\"tableHeader\">Question</th></tr>";
				$count = 1;	
				
				while ($testData = mysql_fetch_array($testImport)) {
					$checkboxImport = query("SELECT * FROM `{$monitor['testTable']}` WHERE `linkID` = '{$testData['id']}'", "raw");
					
					echo "<tr";
					if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"50\">";
					form("importForm");
					hidden("id", "id_" . $testData['id'], $testData['id']);
					
					if ($checkboxImport) {
						checkbox("import", "import_" . $testData['id'], false, false, false, false, "on", false, false, false, " onclick=\"Spry.Utils.submitForm(this.form);\"");
					} else {
						checkbox("import", "import_" . $testData['id'], false, false, false, false, false, false, false, false, " onclick=\"Spry.Utils.submitForm(this.form);\"");
					}
					
					closeForm(false, false);
					echo "</td>";
					echo "<td width=\"150\">" . URL($testData['type'], "preview.php?id=" . $testData['id'], false, false, "Preview this <strong>" . $testData['type'] . "</strong> question", false, true, "640", "480") . "</td>";
					
					echo "<td width=\"100\"><div";
					
					if ($testData['extraCredit'] == "on") {
						echo " class=\"extraCredit\"";
					}
					
					echo ">" . $testData['points'];
					
					if ($testData['points'] == "1") {
						echo " Point";
					} else {
						echo " Points";
					}
					
					echo "</div></td>";
					echo "<td>" . commentTrim(85, $testData['question']) . "</td></tr>";
				}
				
				echo "</tbody></table></blockquote>";
				
				catDivider("Submit", "two");
				echo "<blockquote><p>";
				button("submit", "submit", "Submit", "cancel", "../module_wizard/test_content.php");
				button("cancel", "cancel", "Cancel", "cancel", "../module_wizard/test_content.php");
				echo "</blockquote></p></div>";
			} else {
				echo "<div class=\"noResults\">There are no questions in this bank.</div>";
			}
		}
	} else {
		echo "<div class=\"noResults\">There are no categories in the question bank.</div></br /><br /><blockquote>";
		button("cancel", "cancel", "Cancel", "cancel", "../module_wizard/test_content.php");
		echo "</blockquote>";
	}
	
//Include the footer
	footer();
?>