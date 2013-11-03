<?php
/*
LICENSE: See "license.php" located at the root installation

This is the overview page for the learning units in this system.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
//Check to see which JavaScripts are required, depending on user privileges
	if (access("Edit Learning Unit")) {
		$functions = "customVisible,library,tinyMCESimple";
	} else {
		$functions = "library";
	}
	
/*
Course management
---------------------------------------------------------
*/
	
//Create a course
	if (!isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && access("Create Course")) {
		$position = lastItem("courses");
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		$price = escape($_POST['price']);
		$organization = $userData['organization'];
		
		if (empty($price) || !is_numeric($price) || intval($price) == 0) {
			$price = 0;
		}
		
		query("INSERT INTO `courses` (
			  `id`, `position`, `visible`, `name`, `description`, `price`, `organization`
			  ) VALUES (
			  NULL, '{$position}', 'on', '{$name}', '{$description}', '{$price}', '{$organization}'
			  )");
			  
		$id = primaryKey();
		
		if (empty($price) || !is_numeric($price) || intval($price) == 0) {
			$price = "Free of Charge";
		} else {
			$price = "\$" . number_format($price, 2);
		}
		
		echo "<div class=\"showTools\" style=\"background-color: #FFF380;\" id=\"" . $id . "\" name=\"" . $position . "\">
<p class=\"homeDivider\" id=\"" . $id . "\"><span>" . URL($_POST['name'], "index.php?course=" . $id) . "</span>\n";

		if (access("Edit Course")) {
			echo URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide visible", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide action mediumEdit editCourse", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}
		
		if (access("Delete Course")) {
			echo URL("", "javascript:;", "contentHide action smallDelete deleteCourse", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}
		
echo "</p>
<blockquote>
<div id=\"description\">\n" . 
$_POST['description'] . "
</div>
<br />
<p><em>No learning units currently avaliable</em></p>
<p id=\"price\"><strong>Price:</strong> <span>" . $price . "</span></p>
</blockquote>
</div>";
		exit;
	}
	
//Reorder courses
	if (isset($_POST['id']) && isset($_POST['currentPosition']) && isset($_POST['newPosition']) && !isset($_GET['course']) && access("Edit Course")) {
		$id = $_POST['id'];
		$currentPosition = $_POST['currentPosition'];
		$newPosition = $_POST['newPosition'];
		
		if ($currentPosition > $newPosition) {
			query("UPDATE `courses` SET `position` = position + 1 WHERE `position` >= '{$newPosition}' AND `position` <= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} elseif ($currentPosition < $newPosition) {
			query("UPDATE `courses` SET `position` = position - 1 WHERE `position` <= '{$newPosition}' AND `position` >= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} else {
			exit;
		}
		
		query("UPDATE `courses` SET `position` = '{$newPosition}' WHERE `id` = '{$id}' AND `organization` = '{$userData['organization']}'");
		exit;
	}
	
//Set course availability
	if (!isset($_GET['course'])) {
		avaliability("courses", "index.php", "Edit Course");
	}
	
//Edit a course
	if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && access("Edit Course")) {
		$id = $_POST['id'];
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		$price = escape($_POST['price']);
		
		if (empty($price) || !is_numeric($price) || intval($price) == 0) {
			$price = 0;
		}
		
		query("UPDATE `courses` SET `name` = '{$name}', `description` = '{$description}', `price` = '{$price}' WHERE `id` = '{$id}'");
		
		$position = query("SELECT * FROM `courses` WHERE `id` = '{$id}'");
		
		if (empty($price) || !is_numeric($price) || intval($price) == 0) {
			$price = "Free of Charge";
		} else {
			$price = "\$" . number_format($price, 2);
		}
		
echo "<p class=\"homeDivider\" id=\"" . $id . "\"><span>" . URL($_POST['name'], "index.php?course=" . $id) . "</span>\n" . 
URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide visible", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n" . 
URL("", "javascript:;", "contentHide action mediumEdit editCourse", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		if (access("Delete Course")) {
			echo URL("", "javascript:;", "contentHide action smallDelete deleteCourse", false, false, false, false, false, false, " id=\"" . $id . "\"") . "\n";
		}
		
echo "</p>
<blockquote>
<div id=\"description\">\n" . 
$_POST['description'] . "
</div>
<br />\n";

		if (exist("learningunits", "course", $id)) {
			$units = query("SELECT * FROM `learningunits` WHERE `course` = '{$id}'", "raw");
			
			echo "<p><strong>Learning units:</strong></p>\n";
			echo "<blockquote>\n";
			
			while($unit = fetch($units)) {
				echo URL($unit['name'], "lesson.php?id=" . $unit['id']) . "<br />\n";
			}
			
			echo "</blockquote>";
		} else {
			echo "<p><em>No learning units currently avaliable</em></p>";
		}

echo "\n<p id=\"price\"><strong>Price:</strong> <span>" . $price . "</span></p>
</blockquote>";
		exit;
	}
	
//Delete a course
	if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {
		delete("courses", "index.php", "Delete Course");
	}
	
/*
Learning unit management
---------------------------------------------------------
*/
	
//Reorder learning units
	if (isset($_POST['id']) && isset($_POST['currentPosition']) && isset($_POST['newPosition']) && isset($_GET['course']) && access("Edit Learning Unit")) {
		$id = $_POST['id'];
		$currentPosition = $_POST['currentPosition'];
		$newPosition = $_POST['newPosition'];
		
		if ($currentPosition > $newPosition) {
			query("UPDATE `learningunits` SET `position` = position + 1 WHERE `position` >= '{$newPosition}' AND `position` <= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} elseif ($currentPosition < $newPosition) {
			query("UPDATE `learningunits` SET `position` = position - 1 WHERE `position` <= '{$newPosition}' AND `position` >= '{$currentPosition}' AND `organization` = '{$userData['organization']}'");
		} else {
			exit;
		}
		
		query("UPDATE `learningunits` SET `position` = '{$newPosition}' WHERE `id` = '{$id}' AND `organization` = '{$userData['organization']}'");
		exit;
	}
	
//Set learning unit avaliability
	if (isset($_GET['course'])) {
		avaliability("learningunits", "index.php", "Edit Learning Unit");
	}
	
//Edit a learning unit
	if (isset ($_GET['id']) && $_GET['edit'] == "true" && access("Edit Learning Unit")) {
		$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$_GET['id']}'");
		
		if (exist("learningunits", "id", $_GET['id'])) {
			if ($unitData['organization'] == $userData['organization']) {
				$_SESSION['currentUnit'] = $unitData['id'];
				$_SESSION['review'] = "review";
				
				redirect("wizard/lesson_settings.php");
			} else {
				redirect("overview.php?id=" . $_GET['id']);
			}
		} else {
			redirect($_SERVER['PHP_SELF']);
		}
	}
	
//Delete a learning unit
	if (isset($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['type']) && $_GET['type'] == "unit" && isset($_GET['id'])) {
		delete("learningunits", "index.php", "Delete Learning Unit", true, false, "../data/learn/unit_{$_GET['id']}", "lesson_{$_GET['id']},test_{$_GET['id']}");
	}
	
/*
Other processors
---------------------------------------------------------
*/
	
//Unset active sessions
	unset($_SESSION['currentUnit'], $_SESSION['review']);
	
//Add courses to the cart
	if (isset($_POST['addCourse']) && access("Purchase Learning Unit") && exist("courses", "id", $_POST['addCourse'])) {
		if (!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = array();
		}
		
		if (array_push($_SESSION['cart'], $_POST['addCourse'])) {
			echo "success";
		}
		
		exit;
	}
	
//Enroll the user in the specified, free-of-charge course
	if (isset($_POST['enroll'])) {
		$enrollRequest = $_POST['enroll'];
		$currentUnits = arrayRevert($userData['learningunits']);
		
		if (!is_array($currentUnits)) {
			$currentUnits = array();
		}
		
		$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$enrollRequest}'");
		$courseData = query("SELECT * FROM `courses` WHERE `id` = '{$unitData['course']}'");
		$unitData = query("SELECT * FROM `learningunits` WHERE `course` = '{$unitData['course']}'", "raw");
		
		if (empty($courseData['price']) || intval($courseData['price']) == 0) {
			while($unit = fetch($unitData)) {
				$unitInfo = array("item" => $unit['id'], "lessonStatus" => "C", "testStatus" => "C", "startDate" => strtotime("now"), "submitted" => "");
				$currentUnits[$unit['id']] = $unitInfo;
			}
		} else {
			echo "failure";
			exit;
		}
		
		$units = arrayStore($currentUnits);
		
		query("UPDATE `users` SET `learningunits` = '{$units}' WHERE `id` = '{$userData['id']}'");
		
		echo "success";
		exit;
	}
	
//Top content
	headers("Learning Module", $functions, true);
	
//Title
	title("Learning Unit", false);
	
//Admin toolbar
	if (access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit", "Create Question Bank Questions", "Edit Question Bank Questions", "Delete Question Bank Questions", "Create Feedback Questions", "Edit Feedback Questions", "Delete Feedback Questions", "View Grades", "Assign Users to Learning Unit")) {
		echo "<div class=\"toolBar\">\n";
		
		if (!isset($_GET['course'])) {
			echo toolBarURL("Add New Course", "javascript:;", "toolBarItem new createCourse", false, "Create Course");
		} else {
			echo toolBarURL("Back to Courses", "index.php", "toolBarItem back");
			echo toolBarURL("Add New Module", "wizard/index.php", "toolBarItem new", false, "Create Learning Unit");
		}
		
		echo toolBarURL("Question Bank", "question_bank/index.php", "toolBarItem bank", false, "Create Question Bank Questions", "Edit Question Bank Questions", "Delete Question Bank Questions");
		echo toolBarURL("Feedback", "feedback/index.php", "toolBarItem feedback", false, "Create Feedback Questions", "Edit Feedback Questions", "Delete Feedback Questions");
		echo toolBarURL("View Grades", "gradebook/index.php", "toolBarItem bank", false, "View Grades");
		echo toolBarURL("View Billing History", "billing/index.php", "toolBarItem billing", false, "View Own Billing History");
		echo toolBarURL("Manage Lesson Plan", "planner/index.php", "toolBarItem calendar", false, "Manage Own Lesson Plan");
		echo toolBarURL("Assign Users", "assign/index.php", "toolBarItem user", false, "Assign Users to Learning Unit");
		echo "</div>\n<br />\n";
	}
	
//Display the list of courses
	if (!isset($_GET['course'])) {
		if (exist("courses")) {
			$courses = query("SELECT * FROM `courses` ORDER BY `position`", "raw");
			
			echo "<div id=\"sortable\">\n";
			
			while($course = fetch($courses)) {				
				echo "<div class=\"showTools\" style=\"background-color:#FFFFFF\" id=\"" . $course['id'] . "\" name=\"" . $course['position'] . "\">\n";
				echo "<p class=\"homeDivider\" id=\"" . $course['id'] . "\">\n<span>" . URL($course['name'], "index.php?course=" . $course['id']) . "</span>\n";
				
				if (access("Edit Course")) {
					if ($course['visible'] == "on") {
						$class = "contentHide visible";
					} else {
						$class = "contentHide visible hidden";
					}
					
					echo URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $course['id'] . "\"") . "\n";
					echo URL("", "javascript:;", $class, false, false, false, false, false, false, " id=\"" . $course['id'] . "\"") . "\n";
				}
				
				if (access("Edit Course")) {
					echo URL("", "javascript:;", "contentHide action mediumEdit editCourse", false, false, false, false, false, false, " id=\"" . $course['id'] . "\"") . "\n";
				}
				
				if (access("Delete Course")) {
					echo URL("", "javascript:;", "contentHide action smallDelete deleteCourse", false, false, false, false, false, false, " id=\"" . $course['id'] . "\"") . "\n";
				}
				
				echo "</p>\n";
				echo "<blockquote>\n";
				echo "<div id=\"description\">\n";
				echo $course['description'];
				echo "\n</div>\n";
				echo "<br />\n";
				
				if ((is_array(arrayRevert($userData['learningunits'])) && array_key_exists($course['id'], arrayRevert($userData['learningunits']))) || access("Edit Unowned Learning Units")) {
					if (exist("learningunits", "course", $course['id'])) {
						$units = query("SELECT * FROM `learningunits` WHERE `course` = '{$course['id']}'", "raw");
						
						echo "<p><strong>Learning units:</strong></p>\n";
						echo "<blockquote>\n";
						
						while($unit = fetch($units)) {
							echo URL($unit['name'], "lesson.php?id=" . $unit['id']) . "<br />\n";
						}
						
						echo "</blockquote>\n";
					} else {
						echo "<p><em>No learning units currently avaliable</em></p>\n";
					}
					
					if (access("Edit Unowned Learning Units")) {
						echo intval($course['price']) == 0 ? "<p id=\"price\"><strong>Price:</strong> <span>Free of Charge</span></p>\n" : "<p id=\"price\"><strong>Price:</strong> <span>$" . number_format($course['price'], 2) . "</span></p>\n";
					}
				} else {
					if (access("Purchase Learning Unit")) {
						if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && in_array($course['id'], $_SESSION['cart'])) {
							$class = "cartIn";
						} else {
							$class = "cartOut";
						}
						
						echo "<span id=\"" . $course['id'] . "\" class=\"cartBase " . $class . "\"></span>\n";
						echo intval($course['price']) == 0 ? "<p id=\"price\"><strong>Price:</strong> <span>Free of Charge</span></p>\n" : "<p id=\"price\"><strong>Price:</strong> <span>$" . number_format($course['price'], 2) . "</span></p>\n";
					}
				}
				
				echo "</blockquote>\n";
				echo "</div>\n";
			}
			
			echo "</div>\n";
			
		//The course editor dialog
			if (access("Edit Course")) {
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
				echo "<tr>\n";
				echo cell("<div align=\"right\">Price:</div>", "100");
				echo cell(textField("price", "price", "7", "7") . " " . checkBox("priceEnabled", "priceEnabled", "Enabled"));
				echo "</tr>\n";
				echo "</table>\n";
				echo "</div>\n";
			}
		} else {
			 echo "<div class=\"noResults\">There are no courses currently avaliable.";
			  
			 if (access("Create Course")) {
				 echo " " . URL("Create one now", "courses/index.php") . ".";
			 }
			  
			 echo "</div>\n";
		}
	} else {
//Display the list of learning units within a course
		if (access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit")) {
			$additionalSQL = " WHERE `course` = '{$_GET['course']}'";
		} else {
			$additionalSQL = " WHERE `visible` = 'on' AND `course` = '{$_GET['course']}'";
		}
		
	//Display this course's description
		$courseDescription = query("SELECT * FROM `courses` WHERE `id` = '{$_GET['course']}'");
		
		echo $courseDescription['description'] . "\n";
		
	//Display the list of learning units
		if ((access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit") && query("SELECT * FROM `learningunits`{$additionalSQL} ORDER BY `position` ASC", "raw")) || (!access("Create Learning Unit", "Edit Learning Unit", "Delete Learning Unit") && query("SELECT * FROM `learningunits`{$additionalSQL} ORDER BY `position` ASC", "raw"))) {
			echo "<p class=\"homeDivider\"></p>\n";
			
			$unitsGrabber = query("SELECT * FROM `learningunits`{$additionalSQL} ORDER BY `position` ASC", "raw"); 
			$organization = $userData['organization'];
			$units = array();
			
			if (access("Purchase Learning Unit")) {
				echo form("purchase", false, false, "enroll/cart.php");
			}
			
			echo "<div id=\"sortable\">\n";
			
			while($unit = fetch($unitsGrabber)) {				
				echo "<div class=\"showTools container\" style=\"background-color:#FFFFFF\" id=\"" . $unit['id'] . "\" name=\"" . $unit['position'] . "\">\n";
				echo "<p class=\"homeDivider\" id=\"" . $unit['id'] . "\">\n<span>" . URL($unit['name'], "lesson.php?id=" . $unit['id']) . "</span>\n";
				
				if (access("Edit Learning Unit")) {
					if ($unit['visible'] == "on") {
						$class = "contentHide visible";
					} else {
						$class = "contentHide visible hidden";
					}
					
					echo URL("", "javascript:;", "contentHide action draggable", false, false, false, false, false, false, " id=\"" . $unit['id'] . "\"") . "\n";
					echo URL("", "javascript:;", $class, false, false, false, false, false, false, " id=\"" . $unit['id'] . "\"") . "\n";
				}
				
				if (access("Edit Learning Unit")) {
					echo URL("", "index.php?edit=true&id=" . $unit['id'], "contentHide action mediumEdit editUnit") . "\n";
				}
				
				if (access("Delete Learning Unit")) {
					echo URL("", "javascript:;", "contentHide action smallDelete deleteUnit", false, false, false, false, false, false, " id=\"" . $unit['id'] . "\"") . "\n";
				}
				
				echo "</p>\n";
				echo "<blockquote>\n";
				echo "<div id=\"description\">\n";
				echo $unit['comments'];
				echo "\n</div>\n";				
				echo intval($unit['price']) == 0 ? "<p id=\"price\"><strong>Price:</strong> <span>Free of Charge</span></p>\n" : "<p id=\"price\"><strong>Price:</strong> <span>$" . number_format($unit['price'], 2) . "</span></p>\n";
				echo "</blockquote>\n";
				echo "</div>\n";
			}
			
			echo "</div>\n";
			
			/*echo "<table class=\"dataTable\">\n<tr>\n";
			echo column("", false, "Edit Learning Unit");
			echo column("Name", "350");
			echo column("Comments");
			echo column("Statistics", "50", "Access Learning Unit Statistics");
			echo column("Edit", "50", "Edit Learning Unit");
			
			if (exist("learningunits", "organization", $organization)) {
				echo column("Delete", "50", "Delete Learning Unit");
			}
			
			echo column("", "100", "Purchase Learning Unit");
			echo "</tr>\n";
			
			while ($data = fetch($dataGrabber)) {			
				echo "<tr";
				if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
				
				if (!exist("lesson_" . $data['id'], "position", "1")) {
					echo cell("<div align=\"center\">" . tip("There isn't any lesson content to this learning <br />unit. Please add content before displaying.", false, "noShow") . "</div>", "25", "Edit Learning Unit");
				} else {
					echo option("learningunits", $data['id'], false, false, "Edit Learning Unit");
				}
				
				echo preview(commentTrim(60, $data['name']), "lesson.php?id=" . $data['id'], "lesson", "350");
				echo cell(commentTrim(80, $data['comments']));
				echo statsURL("statistics/index.php?period=overall&id=" . $data['id'], $data['name'], false, "Access Learning Unit Statistics");
				
				if (access("Edit Learning Unit")) {
					if (access("Edit Unowned Learning Units")) {
						echo editURL("index.php?edit=true&id=" . $data['id'], $data['name'], "lesson", false, "Edit Learning Unit");
					} else {
						if ($data['locked'] == "0") {
							echo editURL("index.php?edit=true&id=" . $data['id'], $data['name'], "lesson", false, "Edit Learning Unit");
						} else {
							echo cell(tip("This item cannot be edited", false, "action noEdit"), "50", "Edit Learning Unit");
						}
					}
				}
				
				if (exist("learningunits", "organization", $organization) && $data['organization'] == $organization) {
					echo deleteURL("index.php?action=delete&id=" . $data['id'], $data['name'], "lesson", false, false, "Delete Learning Unit");
				} elseif (exist("learningunits", "organization", $organization)) {
					echo cell(tip("This item cannot be deleted", false, "action noDelete"), "50", "Delete Learning Unit");
				}
				
				if (!is_array(arrayRevert($userData['learningunits'])) || !array_key_exists($data['id'], arrayRevert($userData['learningunits']))) {
					if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && in_array($data['id'], $_SESSION['cart'])) {
						$selected = true;
					} else {
						$selected = false;
					}
					
					if (!empty($data['price'])) {
						echo cell("<div align=\"left\">" . checkBox("purchase[]", "purchase_" . $data['id'], " $" . $data['price'], $data['id'], false, false, $selected) . "</div>", "100", "Purchase Learning Unit");
					} else {
						echo cell(URL("Free of Charge", "javascript:void", false, false, false, false, false, false, false, "onclick=\"enroll(this.id)\" id=\"" . $data['id'] . "\""), "100", "Purchase Learning Unit");
					}
				} else {
					echo cell("<span class=\"notAssigned\">Enrolled</span>", "100", "Purchase Learning Unit");
				}
				  
				echo "</tr>\n";
				
				array_push($units, $data['id']);
				$count++;
			 }
			 
			 echo "</table>\n";*/
			 
			 if (access("Purchase Learning Unit")) {
				 foreach($units as $unit) {
					 if (!is_array(arrayRevert($userData['learningunits'])) || !array_key_exists($unit, arrayRevert($userData['learningunits']))) {
						 $displayButton = true;
					 }
				 }
				 
				 if (isset($displayButton)) {
					 //echo "<hr />\n";
					 echo "<div align=\"right\">";
					 
					 if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
						//echo button("submit", "submit", "Add Selected Items to Cart", "submit");
					 } else {
						 //echo button("submit", "submit", "Update Cart", "submit");
					 }
					 
					 echo "</div>";
				 }
				 
				 echo closeForm(false);
			 }
		} else {
			 echo "<div class=\"noResults\">There are no learning units currently avaliable.";
			  
			 if (access("Create Learning Unit")) {
				 echo " " . URL("Create one now", "wizard/index.php") . ".";
			 }
			  
			 echo "</div>\n";
		}
	}
	 
//Include the footer
	footer();
?>