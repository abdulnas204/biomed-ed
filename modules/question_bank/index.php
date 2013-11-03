<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Set a session to use when inserting questions in to the database
	if (isset($_GET['id'])) {
		$_SESSION['questionBank'] = $_GET['id'];
	}
	
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
	
//Delete a test question	
	if (isset ($_GET['questionID']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		$delete = query("SELECT * FROM `questionbank` WHERE `id` = '{$_GET['questionID']}'");
		
		if ($delete) {			
		//Delete from tests in which this appears
			$questionBankDeleteGrabber = query("SELECT * FROM `moduledata`", "raw");
			
			while ($questionBankDelete = mysql_fetch_array($questionBankDeleteGrabber)) {
				$currentTable = $questionBankDelete['id'];
				$questionArray = query("SELECT * FROM `moduletest_{$currentTable}` WHERE `linkID` = '{$_GET['questionID']}'", false, false);
				
				if ($questionArray) {
					$questionID = $questionArray['id'];
					$questionPosition = $questionArray['position'];
					
					query("UPDATE moduletest_{$currentTable} SET position = position-1 WHERE position > '{$questionPosition}'");
					query("DELETE FROM moduletest_{$currentTable} WHERE id = '{$questionID}'");
				}
			}
			
		//Delete the question from the bank
			if ($delete['type'] == "File Response") {
				delete("questionbank", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'], true, "../questionbank/test/answers/" . $delete['fileURL']);
			} else {
				delete("questionbank", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'], true);
			}
		}
	}
	
//Top Content
	if (isset ($_GET['id'])) {
		if ($_GET['id'] == 0) {
			$title = "Uncategorized Bank";
		} else {
			$title = prepare($bankTitle['category'], false, true) . " Bank";
		}
	} else {
		$title = "Question Bank";
	}
	
	headers($title, "Site Administrator", false, true, " onunload=\"opener.location.reload();\"");
	
//Title
	title($title, "Questions may be created here and be imported into tests when a module is being created. The questions are broken up by their category.");

//Admin toolbar
	if (isset ($_GET['id'])) {
		if ($_GET['id'] != "0") {
			echo "<div class=\"toolBar noPadding\">";
			form("jump");
			echo "<span class=\"toolBarItem noLink\">Add: ";
			dropDown("menu", "nenu", "- Select Question Type -,Description,Essay,File Response, Fill in the Blank, Matching, Multiple Choice, Short Answer, True or False", ",../questions/description.php,../questions/essay.php,../questions/file_response.php,../questions/blank.php,../questions/matching.php,../questions/multiple_choice.php,../questions/short_answer.php,../questions/true_false.php");
			button("submit", "submit", "Go", "button", false, " onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\"");
			echo "</span>";
			echo URL("Back to Categories", "index.php", "toolBarItem back");
			
			if ($testImport) {
				echo URL("Search", "search.php", "toolBarItem search");
			}
			
			closeForm(false, false); 
			echo "</div>";
		} else {
			echo "<div class=\"toolBar\">";
			echo URL("Back to Categories", "index.php", "toolBarItem back");
			
			if ($testImport) {
				echo URL("Search", "search.php", "toolBarItem search");
			}
			
			echo "</div>";
		}
	 } else {
		 echo "<div class=\"toolBar\">";
		 echo URL("Back to Modules", "../index.php", "toolBarItem home");
		 echo URL("Manage Categories", "../settings.php?type=category", "toolBarItem settings");
		 
		 if ($categoryResult != "0") {
		 	echo URL("Search", "search.php", "toolBarItem search");
		 }
		 
		 echo "</div>";
	 }
	 
//Display message updates
	message("inserted", "question", "success", "The question was successfully inserted");
	message("updated", "question", "success", "The question was successfully updated");
	
	if ($categoryResult != "0") {
		if (!isset ($_GET['id'])) {
			echo "<p>Please select a category from the list below.</p><blockquote>";
			$categoryGrabber = query("SELECT * FROM `modulecategories` ORDER BY position ASC", "raw");
			
			while ($category = mysql_fetch_array($categoryGrabber)) {
				$currentCategory = $category['id'];
				$questionValue = query("SELECT * FROM `questionbank` WHERE `category` = '{$currentCategory}'", "num");
				
				echo URL($category['category'], "index.php?id=" . $category['id']) . " : ";
				
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
				echo URL ("Uncategorized", "index.php?id=0") . " : ";
				
				if ($count == 1) {
					echo $count . " Question<br /><br />";
				} else {
					echo $count . " Questions<br /><br />";
				}
			}
			
			echo "</blockquote>";
		}
		
		if (isset ($_GET['id'])) {								
			if ($testImport) {
				echo "<table class=\"dataTable\"><tbody><tr><th width=\"150\" class=\"tableHeader\">Type</th><th width=\"100\" class=\"tableHeader\">Point Value</th><th class=\"tableHeader\">Question</th><th width=\"50\" class=\"tableHeader\">Discover</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
				$count = 1;	
				
				while ($testData = mysql_fetch_array($testImport)) {
					echo "<tr";
					if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
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
					echo "<td>" . commentTrim(85, $testData['question']) . "</td>";
					echo "<td width=\"50\">" . URL ("", "discover.php?linkID=" . $testData['id'], "action discover", false, "Discover in which tests this <strong>" . $testData['type'] . "</strong> question is used") . "</td>";
					echo "<td width=\"50\">";
					
					$URL = "../questions/";
				
					switch ($testData['type']) {
						case "Description" : $URL .= "description.php"; break;
						case "Essay" : $URL .= "essay.php"; break;
						case "File Response" : $URL .= "file_response.php"; break;
						case "Fill in the Blank" : $URL .= "blank.php"; break;
						case "Matching" : $URL .= "matching.php"; break;
						case "Multiple Choice" : $URL .= "multiple_choice.php"; break;
						case "Short Answer" : $URL .= "short_answer.php"; break;
						case "True False" : $URL .= "true_false.php"; break;
					}
					
					$URL .= "?bankID=" . $testData['id'];
					
					echo URL(false, $URL, "action edit", false, "Edit this <strong>" . $testData['type'] . "</strong> question");
					
					echo "</td>";
					echo "<td width=\"50\">" . URL(false, "index.php?id=" . $_GET['id'] . "&questionID=" . $testData['id'] . "&action=delete", "action delete", false, "Delete this <strong>" . $testData['type'] . "</strong> question", false, false, false, false, " onclick=\"return confirm('This action will delete this question from the question bank, and from any tests which it is currently located. If you wish to keep this question inside its current tests, click the &quot;discover&quot; button, find in which tests this question is located, then import the question into the test.')\")") . "</td>";
					echo "</tr>";
				}
				
				echo "</tbody></table>";
			} else {
				echo "<div class=\"noResults\">There are no questions in this bank. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>";
			}
		}
	} else {
		echo "<div class=\"noResults\">Please <a href=\"../settings.php?type=category\">add at least one category</a> prior to entering questions.</div>";
	}
	
//Include the footer
	footer();
?>