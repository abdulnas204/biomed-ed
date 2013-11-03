<?php
/*
LICENSE: See "license.php" located at the root installation

This is the question bank management page.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');
	
//Delete a series of imported questions from their places in a test
	function questionBankDelete($category) {
		global $userData;
		
		if (exist("learningunits", "organization", $userData['organization'])) {
			$testDeleteGrabber = query("SELECT * FROM `learningunits` WHERE `organization` = '{$userData['organization']}'", "raw");
			
			while($testDelete = fetch($testDeleteGrabber)) {
				$questionGrabber = query("SELECT test_{$testDelete['id']}.id, test_{$testDelete['id']}.linkID, test_{$testDelete['id']}.position, questionbank_{$userData['organization']}.category, questionbank_{$userData['organization']}.fileURL FROM `test_{$testDelete['id']}` LEFT JOIN `questionbank_{$userData['organization']}` ON test_{$testDelete['id']}.linkID = questionbank_{$userData['organization']}.id WHERE `questionBank` != '0'", "raw", false);
				
				if ($questionGrabber) {
					while($questions = fetch($questionGrabber)) {
						if ($questions['category'] == $category) {
							query("DELETE FROM `test_{$testDelete['id']}` WHERE `id` = '{$questions['id']}'", false, false);
							query("UPDATE `test_{$currentTable}` SET `position` = position-1 WHERE `position` > '{$questions['position']}'");
							
							if (!empty($questions['fileURL'])) {
								unlink("../../data/learn/questionbank_" . $userData['organization'] . "/test/answers/" . $questions['fileURL']);
							}
						}
					}
				}
			}
		}
		
		query("DELETE FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$category}'");
	}
	
/*
Super category management
---------------------------------------------------------
*/
	
//Create a super category
	if (!isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && !isset($_GET['category']) && access("Create Question Bank Categories")) {
		$position = lastItem("supercategories");
		$organization = $userData['organization'];
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		
		if (exist("supercategories", "name", $_POST['name'])) {
			exit;
		}
		
		query("INSERT INTO `supercategories` (
			  `id`, `position`, `organization`, `name`, `description`
			  ) VALUES (
			  NULL, '{$position}', '{$organization}', '{$name}', '{$description}'
			  )");
			  
		$id = primaryKey();
		
		echo "<div class=\"showTools\" style=\"background-color: #FFF380;\" id=\"" . $id . "\" name=\"" . $position . "\">
<p class=\"homeDivider\" id=\"" . $id . "\"><span>" . URL($_POST['name'], "index.php?category=" . $id) . "</span>\n";

		if (access("Edit Question Bank Categories")) {
			echo URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide action mediumEdit editSuperCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}

		if (access("Delete Question Bank Categories")) {
			echo URL("", "javascript:;", "contentHide action smallDelete deleteSuperCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}
		
echo "</p>
<blockquote>
<div id=\"description\">\n" . 
$_POST['description'] . "
</div>
<br />
<p><em>Does not contain any categories</em></p>
</blockquote>
</div>";
		exit;
	}
	
//Reorder super categories
	if (isset($_POST['id']) && isset($_POST['currentPosition']) && isset($_POST['newPosition']) && !isset($_GET['category']) && access("Edit Question Bank Categories")) {
		$id = $_POST['id'];
		$currentPosition = $_POST['currentPosition'];
		$newPosition = $_POST['newPosition'];
		
		if ($currentPosition > $newPosition) {
			query("UPDATE `superCategories` SET `position` = position + 1 WHERE `position` >= '{$newPosition}' AND `position` <= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} elseif ($currentPosition < $newPosition) {
			query("UPDATE `superCategories` SET `position` = position - 1 WHERE `position` <= '{$newPosition}' AND `position` >= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} else {
			exit;
		}
		
		query("UPDATE `superCategories` SET `position` = '{$newPosition}' WHERE `id` = '{$id}' AND `organization` = '{$userData['organization']}'");
		exit;
	}
	
//Edit a super category
	if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && !isset($_GET['category']) && access("Edit Question Bank Categories")) {
		$id = $_POST['id'];
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		
		if ($previous = exist("supercategories", "name", $_POST['name'])) {
			if ($previous['id'] !== $id) {
				exit;
			}
		}
		
		query("UPDATE `supercategories` SET `name` = '{$name}', `description` = '{$description}' WHERE `id` = '{$id}'");
		
		$position = query("SELECT * FROM `supercategories` WHERE `id` = '{$id}'");
		
echo "<p class=\"homeDivider\" id=\"" . $id . "\"><span>" . URL($_POST['name'], "index.php?category=" . $id) . "</span>" . 
URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide action mediumEdit editSuperCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";

		if (access("Delete Question Bank Categories")) {
			echo URL("", "javascript:;", "contentHide action smallDelete deleteSuperCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}
		
echo "</p>
<blockquote>
<div id=\"description\">\n" . 
$_POST['description'] . "
</div>
<br />\n";

		if (exist("categories", "superCategory", $id)) {
			$categories = query("SELECT * FROM `categories` WHERE `superCategory` = '{$id}'", "num");
			
			echo "<p><strong>Categories:</strong> " . $categories . "</p>";
		} else {
			echo "<p><em>Does not contain any categories</em></p>";
		}
		
echo "\n</blockquote>
</div>";
		exit;
	}
	
//Delete a super category
	if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete" && !isset($_GET['category']) && access("Delete Question Bank Categories")) {
		if (exist("superCategories", "id", $_GET['id'])) {
			$categories = query("SELECT * FROM `categories` WHERE `superCategory` = '{$_GET['id']}'", "raw");
			
			while ($category = fetch($categories)) {
				questionBankDelete($category['name']);
				query("UPDATE `categories` SET `position` = position - 1 WHERE `position` >= '{$category['position']}' AND `organization` = '{$userData['organization']}'");
				query("DELETE FROM `categories` WHERE `superCategory` = '{$_GET['id']}'");
			}
			
			exit;
		}
	}
	
/*
Sub-category management
---------------------------------------------------------
*/
	
//Create a sub-category
	if (!isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_GET['category']) && access("Create Question Bank Categories")) {
		$position = lastItem("categories");
		$superCategory = $_GET['category'];
		$organization = $userData['organization'];
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		
		if (exist("categories", "name", $_POST['name'])) {
			exit;
		}
		
		query("INSERT INTO `categories` (
			  `id`, `position`, `superCategory`, `organization`, `name`, `description`
			  ) VALUES (
			  NULL, '{$position}', '{$superCategory}', '{$organization}', '{$name}', '{$description}'
			  )");
			  
		$id = primaryKey();
		
		echo "<div class=\"showTools\" style=\"background-color: #FFF380;\" id=\"" . $id . "\" name=\"" . $position . "\">
<p class=\"homeDivider\" id=\"" . $id . "\"><span>" . URL($_POST['name'], "index.php?category=" . $_GET['category'] . "&subcategory=" . $id) . "</span>\n";

		if (access("Edit Question Bank Categories")) {
			echo URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide action mediumEdit editSubCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}

		if (access("Delete Question Bank Categories")) {
			echo URL("", "javascript:;", "contentHide action smallDelete deleteSubCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}
		
echo "</p>
<blockquote>
<div id=\"description\">\n" . 
$_POST['description'] . "
</div>
<br />
<p><em>Does not contain any questions</em></p>
</blockquote>
</div>";
		exit;
	}
	
//Reorder sub-categories
	if (isset($_POST['id']) && isset($_POST['currentPosition']) && isset($_POST['newPosition']) && isset($_GET['category']) && access("Edit Question Bank Categories")) {
		$id = $_POST['id'];
		$currentPosition = $_POST['currentPosition'];
		$newPosition = $_POST['newPosition'];
		
		if ($currentPosition > $newPosition) {
			query("UPDATE `categories` SET `position` = position + 1 WHERE `position` >= '{$newPosition}' AND `position` <= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} elseif ($currentPosition < $newPosition) {
			query("UPDATE `categories` SET `position` = position - 1 WHERE `position` <= '{$newPosition}' AND `position` >= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} else {
			exit;
		}
		
		query("UPDATE `categories` SET `position` = '{$newPosition}' WHERE `id` = '{$id}' AND `organization` = '{$userData['organization']}'");
		exit;
	}
	
//Edit a sub-category
	if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_GET['category']) && access("Edit Question Bank Categories")) {
		$id = $_POST['id'];
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		
		if ($previous = exist("categories", "name", $_POST['name'])) {
			if ($previous['id'] !== $id) {
				exit;
			}
		}
		
		query("UPDATE `categories` SET `name` = '{$name}', `description` = '{$description}' WHERE `id` = '{$id}'");
		
		$position = query("SELECT * FROM `categories` WHERE `id` = '{$id}'");
		
echo "<p class=\"homeDivider\" id=\"" . $id . "\"><span>" . URL($_POST['name'], "index.php?category=" . $_GET['category'] . "&subcategory=" . $id) . "</span>" . 
URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide action mediumEdit editSubCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";

		if (access("Delete Question Bank Categories")) {
			echo URL("", "javascript:;", "contentHide action smallDelete deleteSubCategory", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}
		
echo "</p>
<blockquote>
<div id=\"description\">\n" . 
$_POST['description'] . "
</div>
<br />\n";

		if (exist("questionbank_" . $userData['organization'], "category", $position['name'])) {
			$questions = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$position['name']}'", "num");
			
			echo "<p><strong>Questions:</strong> " . $questions . "</p>";
		} else {
			echo "<p><em>Does not contain any questions</em></p>";
		}
		
echo "\n</blockquote>
</div>";
		exit;
	}
	
//Delete a super category
	if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['category']) && access("Delete Question Bank Categories")) {
		if (exist("categories", "id", $_GET['id'])) {
			$category = query("SELECT * FROM `categories` WHERE `id` = '{$_GET['id']}'");
			
			questionBankDelete($category['name']);
			query("UPDATE `categories` SET `position` = position - 1 WHERE `position` >= '{$category['position']}' AND `organization` = '{$userData['organization']}'");
			query("DELETE FROM `categories` WHERE `superCategory` = '{$_GET['id']}'");
			exit;
		}
	}
	
//Top Content
	if (isset($_GET['category'])) {
		if (isset($_GET['subcategory'])) {
			$superCategory = query("SELECT * FROM `supercategories` WHERE `id` = '{$_GET['category']}'");
			$subCategory = query("SELECT * FROM `categories` WHERE `id` = '{$_GET['subcategory']}'");
			$title = $subCategory['name'] . " Sub-category";
			$description = "These questions are located inside of the " . $subCategory['name'] . " sub-category, which is located inside of the " . $superCategory['name'] . " super category.";
		} else {
			$bankTitle = query("SELECT * FROM `supercategories` WHERE `id` = '{$_GET['category']}'");
			$title = $bankTitle['name'] . " Super Category";
			$description = "For further classification and ease of management, sub-categories can be created and managed inside of the " . $bankTitle['name'] . " super category.";
		}
	} else {
		$title = "Question Bank";
		$description = "Questions may be created here and be imported into tests when a learning unit is being created. The questions are broken up by their category.";
	}
	
	headers($title, "validate,library,tinyMCESimple", true, "onunload=\"opener.location.reload();\"");
	
//Title
	title($title, $description);

//Admin toolbar
	if (!isset($_GET['category'])) {
		echo "<div class=\"toolBar\">\n";
		echo toolBarURL("Back to Overview", "../index.php", "toolBarItem back");
		echo toolBarURL("Add New Super Category", "javascript:;", "toolBarItem new newSuperCategory");
		
		if (exist("questionbank_{$userData['organization']}")) {
			echo toolBarURL("Search", "search.php", "toolBarItem search");
		}
		
		echo "</div>\n";
	 } else {
		 if (!isset($_GET['subcategory'])) {
			echo "<div class=\"toolBar\">\n";
			echo toolBarURL("Back to Super Categories", "index.php", "toolBarItem back");
			echo toolBarURL("Add New Sub-category", "javascript:;", "toolBarItem new newSubCategory");
			echo toolBarURL("Search", "search.php", "toolBarItem search");
			echo "</div>\n";
		 } else {
			echo "<div class=\"toolBar noPadding\">\n";
			echo form("jump");
			echo "<span class=\"toolBarItem noLink\">Add: ";
			echo dropDown("menu", "nenu", "- Select Question Type -,Description,Essay,File Response, Fill in the Blank, Matching, Multiple Choice, Short Answer, True or False", ",../questions/description.php,../questions/essay.php,../questions/file_response.php,../questions/blank.php,../questions/matching.php,../questions/multiple_choice.php,../questions/short_answer.php,../questions/true_false.php");
			echo button("submit", "submit", "Go", "button", false, "onclick=\"location=document.jump.menu.options[document.jump.menu.selectedIndex].value;\"");
			echo "</span>\n";
			echo toolBarURL("Back to Categories", "index.php", "toolBarItem back");
			echo toolBarURL("Search", "search.php?id=" . $_GET['subcategory'], "toolBarItem search");
			echo closeForm(false); 
			echo "</div>\n";
		 }
	 }
	 
//Display message updates
	message("inserted", "question", "success", "The question was successfully inserted");
	message("updated", "question", "success", "The question was successfully updated");
	
	echo "<div id=\"sortable\">\n";
	
//Display super categories
	if (exist("supercategories", "organization", $userData['organization'])) {
		if (!isset($_GET['category'])) {
			$superCategories = query("SELECT * FROM `superCategories` WHERE `organization` = '{$userData['organization']}' ORDER BY `position` ASC", "raw");
			
			while($superCategory = fetch($superCategories)) {				
				echo "<div class=\"showTools\" style=\"background-color:#FFFFFF\" id=\"" . $superCategory['id'] . "\" name=\"" . $superCategory['position'] . "\">\n";
				echo "<p class=\"homeDivider\" id=\"" . $superCategory['id'] . "\">\n<span>" . URL($superCategory['name'], "index.php?category=" . $superCategory['id']) . "</span>\n";
				
				if (access("Edit Question Bank Categories")) {
					echo URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $superCategory['id'] . "\"") . "\n";
				
					echo URL("", "javascript:;", "contentHide action mediumEdit editSuperCategory", false, false, false, false, false, false, " id=\"" . $superCategory['id'] . "\"") . "\n";
				}
				
				if (access("Delete Question Bank Categories")) {
					echo URL("", "javascript:;", "contentHide action smallDelete deleteSuperCategory", false, false, false, false, false, false, " id=\"" . $superCategory['id'] . "\"") . "\n";
				}
				
				echo "</p>\n";
				echo "<blockquote>\n";
				echo "<div id=\"description\">\n";
				echo $superCategory['description'];
				echo "\n</div>\n";
				echo "<br />\n";
				
				if (exist("categories", "superCategory", $superCategory['id'])) {
					$categories = query("SELECT * FROM `categories` WHERE `superCategory` = '{$superCategory['id']}'", "num");
					
					echo "<p><strong>Categories:</strong> " . $categories . "</p>\n";
				} else {
					echo "<p><em>Does not contain any categories</em></p>\n";
				}
				
				echo "</blockquote>\n";
				echo "</div>\n";
			}
			
		//Allow management of super categories
			echo "<div id=\"manageDialog\" class=\"contentHide\">\n";
			echo "<div align=\"center\">\n<span id=\"message\"></span>\n</div>\n";
			echo "<table>\n";
			echo "<tr>\n";
			echo cell("<div align=\"right\">Name<span class=\"require\">*</span>:</div>", "100");
			echo cell(textField("name", "name", false, false, false, false, false, false, false, false, " class=\"required\"") . hidden("id", "id", ""));
			echo "</tr>\n";
			echo "<tr>\n";
			echo cell("<div align=\"right\">Description<span class=\"require\">*</span>:</div>", "100");
			echo "<td id=\"placeHolder\">\n";
			echo "</td>\n";
			echo "</tr>\n";
			echo "</table>\n";
			echo "</div>\n";
	//Display the sub-categories
		} else {
			if (exist("categories", "organization", $userData['organization'])) {
				if (!isset($_GET['subcategory'])) {
					$subCategories = query("SELECT * FROM `categories` WHERE `organization` = '{$userData['organization']}' ORDER BY `position` ASC", "raw");
					
					while($subCategory = fetch($subCategories)) {				
						echo "<div class=\"showTools\" style=\"background-color:#FFFFFF\" id=\"" . $subCategory['id'] . "\" name=\"" . $subCategory['position'] . "\">\n";
						echo "<p class=\"homeDivider\" id=\"" . $subCategory['id'] . "\">\n<span>" . URL($subCategory['name'], "index.php?category=" . $_GET['category'] . "&subcategory=" . $subCategory['id']) . "</span>\n";
						
						if (access("Edit Question Bank Categories")) {
							echo URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $subCategory['id'] . "\"") . "\n";
						
							echo URL("", "javascript:;", "contentHide action mediumEdit editSubCategory", false, false, false, false, false, false, " id=\"" . $subCategory['id'] . "\"") . "\n";
						}
						
						if (access("Delete Question Bank Categories")) {
							echo URL("", "javascript:;", "contentHide action smallDelete deleteSubCategory", false, false, false, false, false, false, " id=\"" . $subCategory['id'] . "\"") . "\n";
						}
						
						echo "</p>\n";
						echo "<blockquote>\n";
						echo "<div id=\"description\">\n";
						echo $subCategory['description'];
						echo "\n</div>\n";
						echo "<br />\n";
						
						if (exist("questionbank_" . $userData['organization'], "category", $subCategory['name'])) {
							$questions = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$subCategory['name']}'", "num");
							
							echo "<p><strong>Questions:</strong> " . $questions . "</p>\n";
						} else {
							echo "<p><em>Does not contain any questions</em></p>\n";
						}
						
						echo "</blockquote>\n";
						echo "</div>\n";
					}
					
				//Allow addition of sub-categories
					echo "<div id=\"manageDialog\" class=\"contentHide\">\n";
					echo "<div align=\"center\">\n<span id=\"message\"></span>\n</div>\n";
					echo "<table>\n";
					echo "<tr>\n";
					echo cell("<div align=\"right\">Name<span class=\"require\">*</span>:</div>", "100");
					echo cell(textField("name", "name", false, false, false, false, false, false, false, false, " class=\"required\"") . hidden("id", "id", ""));
					echo "</tr>\n";
					echo "<tr>\n";
					echo cell("<div align=\"right\">Description<span class=\"require\">*</span>:</div>", "100");
					echo "<td id=\"placeHolder\">\n";
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
					echo "</div>\n";
			//Display the questions within a sub-category
				} else {		
					if (exist("questionbank_{$userData['organization']}", "category", $category)) {
						$testImport = query("SELECT * FROM `questionbank_{$userData['organization']}` WHERE `category` = '{$category}'", "raw");
						$count = 1;
						
						echo "<table class=\"dataTable\">\n<tr>\n";
						echo column("Type", "150");
						echo column("Point Value", "100");
						echo column("Question");
						echo column("Discover", "50");
						echo column("Edit", "50");
						echo column("Delete", "50");
						echo "</tr>\n";
						
						while ($testData = fetch($testImport)) {
							echo "<tr";
							if ($count++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
							echo preview(commentTrim(30, $testData['type']), "preview.php?id=" . $testData['id'], "question", "150", true);
							
							if ($testData['extraCredit'] == "on") {
								$class = " class=\"extraCredit\"";
							} else {
								$class = "";
							}
							
							if ($testData['points'] == "1") {
								$point = " Point";
							} else {
								$point = " Points";
							}
							
							echo cell("<div" . $class  . ">" . $testData['points'] . $point . "</div>", "100");
							echo cell(commentTrim(85, $testData['question']));
							echo cell(URL("", "discover.php?linkID=" . $testData['id'], "action discover", false, "Discover in which tests this <strong>" . $testData['type'] . "</strong> question is used"));
							
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
							
							echo editURL($URL, $testData['type'], "question");
							echo deleteURL("index.php?id=" . $_GET['id'] . "&questionID=" . $testData['id'] . "&action=delete", $testData['type'], "question", "This action will delete this question from the question bank, and from any tests which it is currently located. If you wish to keep this question inside its current tests, click the &quot;discover&quot; button, find in which tests this question is located, then import the question into the test.");
							echo "</tr>\n";
						}
						
						echo "</table>\n";
					} elseif (exist("categories", "id", $_GET['id'])) {
						echo "\n<div class=\"noResults\">There are no questions in this bank. Questions can be created by selecting a question type from the drop down menu above, and pressing &quot;Go&quot;.</div>\n";
					}
				}
			} else {
				//echo "\n<div class=\"noResults\">Please <a href=\"javascript:void()\" onclick=\"toggleInfo()\">add at least one category</a> prior to entering questions.";
			}
		}
	} else {
		echo "\n<div class=\"noResults\">Please " . URL("add at least one super category", "javascript:;", "newSuperCategory") . " into this bank.";
		
	//Allow addition of super categories
		echo "<div id=\"manageDialog\" class=\"contentHide\">\n";
		echo "<div align=\"center\">\n<span id=\"message\"></span>\n</div>\n";
		echo "<table>\n";
		echo "<tr>\n";
		echo cell("<div align=\"right\">Name<span class=\"require\">*</span>:</div>", "100");
		echo cell(textField("name", "name", false, false, false, false, false, false, false, false, " class=\"required\"") . hidden("id", "id", ""));
		echo "</tr>\n";
		echo "<tr>\n";
		echo cell("<div align=\"right\">Description<span class=\"require\">*</span>:</div>", "100");
		echo "<td id=\"placeHolder\">\n";
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
	}
	
	echo "</div>\n";
	
//Include the footer
	footer();
?>