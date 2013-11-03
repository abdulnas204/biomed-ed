<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Check to see if a category name exists
	validateName("modulecategories", "category");
	
//Set a session to use when inserting questions in to the database
	if (isset($_GET['id'])) {
		$_SESSION['questionBank'] = $_GET['id'];
		$categoryPrep = query("SELECT * FROM `modulecategories` WHERE `id` = '{$_GET['id']}'");
		$category = mysql_real_escape_string($categoryPrep['category']);
	}
	
//Create a function to delete questions
	function questionBankDelete($id, $type) {
		if ($type == "single") {
			$delete = query("SELECT * FROM `questionbank_0` WHERE `id` = '{$id}'");
		} else {
			$delete = query("SELECT * FROM `questionbank_0` WHERE `category` = '{$id}'");
		}
		
		if ($delete) {
			if ($type == "single") {
			//Delete from tests in which this appears
				$questionBankDeleteGrabber = query("SELECT * FROM `moduledata`", "raw");
				
				if (exist("moduledata")) {
					while ($questionBankDelete = mysql_fetch_array($questionBankDeleteGrabber)) {
						$currentTable = $questionBankDelete['id'];
						$questionArray = query("SELECT * FROM `moduletest_{$currentTable}` WHERE `linkID` = '{$id}'", false, false);
						
						if ($questionArray) {
							$questionID = $questionArray['id'];
							$questionPosition = $questionArray['position'];
							
							query("UPDATE moduletest_{$currentTable} SET position = position-1 WHERE position > '{$questionPosition}'");
							query("DELETE FROM moduletest_{$currentTable} WHERE id = '{$questionID}'");
						}
					}
				}
				
			//Delete the question from the bank
				if ($delete['type'] == "File Response") {
					delete("questionbank_0", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'], true, "../questionbank/test/answers/" . $delete['fileURL']);
				} else {
					delete("questionbank_0", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'], true);
				}
			} else {
			//Delete from tests in which this appears
				$questionBankDeleteGrabber = query("SELECT * FROM `moduledata`", "raw");
				
				while ($questionBankDelete = mysql_fetch_array($questionBankDeleteGrabber)) {
					$currentTable = $questionBankDelete['id'];
					$questionArray = query("SELECT * FROM `moduletest_{$currentTable}` WHERE `linkID` = '{$id}'", false, false);
					
					if ($questionArray) {
						$questionID = $questionArray['id'];
						$questionPosition = $questionArray['position'];
						
						query("UPDATE moduletest_{$currentTable} SET position = position-1 WHERE position > '{$questionPosition}'");
						query("DELETE FROM moduletest_{$currentTable} WHERE id = '{$questionID}'");
					}
				}
				
			//Delete the question from the bank
				if ($redirect == true) {
					if ($delete['type'] == "File Response") {
						delete("questionbank_0", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'], true, "../questionbank/test/answers/" . $delete['fileURL']);
					} else {
						delete("questionbank_0", $_SERVER['PHP_SELF'] . "?id=" . $_GET['id'], true);
					}
				}
			}
		}
	}
	
//Delete a question
	if (isset ($_GET['questionID']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		questionBankDelete($_GET['questionID'], "single");
	}
	
//Add a new category
	if (isset($_POST['submit']) && !empty($_POST['category']) && !exist("modulecategories", "category", mysql_real_escape_string($_POST['category']))) {
		$category = $_POST['category'];
		
		query("INSERT INTO `modulecategories` (
					`id`, `category`
				) VALUES (
					NULL, '{$category}'
				)");
				
		redirect("index.php");
	}
	
//Delete a category
	if (isset($_GET['categoryID']) && isset ($_GET['action']) && $_GET['action'] == "delete") {
		/*if (exist("modulecategories", "id", $_GET['categoryID'])) {
			questionBankDelete($_GET['categoryID'], "category");
		}*/
		
		query("DELETE FROM `modulecategories` WHERE `id` = '{$_GET['categoryID']}'");
		redirect("index.php");
	}
	
//Top Content
	if (isset ($_GET['id'])) {
		$bankTitle = query("SELECT * FROM `modulecategories` WHERE `id` = '{$_GET['id']}'");
		$title = prepare($bankTitle['category'], false, true) . " Bank";
	} else {
		$title = "Question Bank";
	}
	
	headers($title, "Site Administrator", "showHide,validate", true, " onunload=\"opener.location.reload();\"");
	
//Title
	title($title, "Questions may be created here and be imported into tests when a module is being created. The questions are broken up by their category.");

//Admin toolbar
	if (isset ($_GET['id'])) {
		echo "<div class=\"toolBar noPadding\">";
		form("jump");
		echo "<span class=\"toolBarItem noLink\">Add: ";
		dropDown("menu", "nenu", "- Select Question Type -,Description,Essay,File Response, Fill in the Blank, Matching, Multiple Choice, Short Answer, True or False", ",../questions/description.php,../questions/essay.php,../questions/file_response.php,../questions/blank.php,../questions/matching.php,../questions/multiple_choice.php,../questions/short_answer.php,../questions/true_false.php");
		button("submit", "submit", "Go", "button", false, " onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\"");
		echo "</span>";
		echo URL("Back to Categories", "index.php", "toolBarItem back");
		
		if (exist("questionbank_0", "category", $category)) {
			echo URL("Search", "search.php?id=" . $_GET['id'], "toolBarItem search");
		}
		
		closeForm(false, false); 
		echo "</div>";
	 } else {
		 echo "<div class=\"toolBar\">";
		 echo URL("Back to Modules", "../index.php", "toolBarItem back");
		 
		 if (exist("questionbank_0")) {
		 	echo URL("Search", "search.php", "toolBarItem search");
		 }
		 
		 echo "</div>";
	 }
	 
//Display message updates
	message("inserted", "question", "success", "The question was successfully inserted");
	message("updated", "question", "success", "The question was successfully updated");
	
	if (exist("modulecategories")) {
	//Display the categories
		if (!isset ($_GET['id'])) {
			echo "<p>Please select a category from the list below.</p><blockquote>";
			
			$categoryGrabber = query("SELECT * FROM `modulecategories` ORDER BY `id` ASC", "raw");
			
			while ($categoryData = mysql_fetch_array($categoryGrabber)) {
				$currentCategory = $categoryData['category'];
				$questionValue = query("SELECT * FROM `questionbank_0` WHERE `category` = '{$currentCategory}'", "num");
				
				echo "<div onmouseover=\"rollOverTools('edit_" . $categoryData['id'] . "'); rollOverTools('delete_" . $categoryData['id'] . "');\" onmouseout=\"rollOverTools('edit_" . $categoryData['id'] . "'); rollOverTools('delete_" . $categoryData['id'] . "');\">";
				echo URL($categoryData['category'], "index.php?id=" . $categoryData['id']) . " : ";
				
				if ($questionValue == 1) {
					echo $questionValue . " Question ";
				} else {
					echo $questionValue . " Questions";
				}
				
				echo URL("", "javascript:void;", "contentHide action mediumEdit", false, "Edit the <strong>" . $categoryData['category'] . "</strong> category", false, false, false, false, " onclick=\"quickEdit()\" id=\"edit_" . $categoryData['id'] . "\"");
				//This action will remove this category and all of it\'s containing questions. If any of these questions are imported into a test, they will be removed from the test. To prevent the loss of these questions from a test, either move them to another category, or manually import the questions into the test. This action cannot be undone. Continue?
				echo URL("", "index.php?categoryID=" . $categoryData['id'] . "&action=delete", "contentHide action smallDelete", false, "Delete the <strong>" . $categoryData['category'] . "</strong> category", false, false, false, false, " onclick=\"return confirm('This action will only delete the category. All questions inside will still exist. This is just a temporary limitation. Please manually remove all questions inside before removing this category. Click OK when this category is empty.')\" id=\"delete_" . $categoryData['id'] . "\"");
				echo "</div>";
				echo "<br />";
			}
			
		//Allow addition of category types
			echo "<div id=\"newCategory\" class=\"contentHide\">";
			form("insertCategory");
			directions("Category Name", true);
			textField("category", "category", false, false, false, true, ",ajax[ajaxName]");
			echo "<br /><br />";
			button("submit", "submit", "Submit", "submit");
			closeForm(false, false);
			echo "<br /></div><span class=\"smallAdd\" onclick=\"toggleInfo('newCategory')\">Add New Category</span>";
			
			echo "</blockquote>";
		}
		
	//Display the questions within a category
		if (isset ($_GET['id'])) {						
			if (exist("questionbank_0", "category", $category)) {
				echo "<table class=\"dataTable\"><tbody><tr><th width=\"150\" class=\"tableHeader\">Type</th><th width=\"100\" class=\"tableHeader\">Point Value</th><th class=\"tableHeader\">Question</th><th width=\"50\" class=\"tableHeader\">Discover</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
				$count = 1;
				$testImport = query("SELECT * FROM `questionbank_0` WHERE `category` = '{$category}'", "raw");
				
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
			} elseif (exist("modulecategories", "category", $category)) {
				echo "<div class=\"noResults\">There are no questions in this bank. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>";
			} else {
				redirect($_SERVER['PHP_SELF']);
			}
		}
	} else {
		echo "<div class=\"noResults\">Please <a href=\"javascript:void()\" onclick=\"toggleInfo('newCategory')\">add at least one category</a> prior to entering questions.";
		
	//Allow addition of category types
		echo "<div id=\"newCategory\" class=\"contentHide\">";
		form("insertCategory");
		echo "<br />";
		textField("category", "category", false, false, false, true, ",ajax[ajaxName]");
		echo "<br /><br />";
		button("submit", "submit", "Submit", "submit");
		closeForm(false, false);
		echo "</div></div>";
	}
	
//Include the footer
	footer();
?>