<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this step has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			//case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//If the settings are being updated
	if (isset($_SESSION['currentModule'])) {
		$currentModule = strtolower($_SESSION['currentModule']);
		$moduleDataGrabber = mysql_query("SELECT * FROM moduledata WHERE name = '{$currentModule}'", $connDBA);
		$moduleData = mysql_fetch_array($moduleDataGrabber);
		
	//Process the form
		if (isset($_POST['submit']) && !empty($_POST['name']) && is_numeric($_POST['category']) && !empty($_POST['employee']) && !empty($_POST['difficulty']) && !empty($_POST['time']) && !empty($_POST['timeLabel']) && is_numeric($_POST['locked']) && is_numeric($_POST['selected']) && is_numeric($_POST['skip']) && is_numeric($_POST['feedback'])) {
		//Do not process if a module with the same name exists
			$name = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$moduleCheck = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$name}'", $connDBA);
			if (mysql_fetch_array($moduleCheck)) {
				if (strtolower($_SESSION['currentModule']) !== strtolower($name)) {
					header("Location:lesson_settings.php?error=identical");
					exit;
				}
			}
			
			if ($name == "Question Bank" || $name == "QuestionBank") {
				header("Location:lesson_settings.php?error=identical");
				exit;
			}
		
			$id = $moduleData['id'];
			$name = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$category = mysql_real_escape_string($_POST['category']);
			$employee = mysql_real_escape_string($_POST['employee']);
			$difficulty = $_POST['difficulty'];
			$time = $_POST['time'];
			$timeLabel = $_POST['timeLabel'];
			$comments = mysql_real_escape_string($_POST['comments']);
			$locked = $_POST['locked'];
			$selected = $_POST['selected'];
			$skip = $_POST['skip'];
			$feedback = $_POST['feedback'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$searchEngine = $_POST['searchEngine'];
			
			$timeFrame = $time . $timeLabel;
			
			$editModuleQuery = "UPDATE moduledata SET `locked` = '{$locked}', `name` = '{$name}', `category` = '{$category}', `employee` = '{$employee}', `difficulty` = '{$difficulty}', `timeFrame` = '{$timeFrame}', `comments` = '{$comments}', `selected` = '{$selected}', `skip` = '{$skip}', `feedback` = '{$feedback}', `tags` = '{$tags}', `searchEngine` = '{$searchEngine}' WHERE id = '{$id}'";
			
		//Update the session to manage the steps
			$_SESSION['step'] = "lessonContent";
			
		//Update the test table, if it exists
			$testCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$_SESSION['currentModule']}'", $connDBA);
			$testCheck = mysql_fetch_array($testCheckGrabber);
			
			if ($testCheck['test'] == "1") {
				$oldTableName = strtolower($testCheck['testName']);
				$newTableName = strtolower(str_replace(" ", "", $name));
				mysql_query("ALTER TABLE moduletest_{$oldTableName} RENAME TO moduletest_{$newTableName}", $connDBA);
			}
			
		//Update the lesson table
			$oldTableName = strtolower(str_replace(" ", "", $_SESSION['currentModule']));
			$newTableName = strtolower(str_replace(" ", "", $name));
			mysql_query("ALTER TABLE modulelesson_{$oldTableName} RENAME TO modulelesson_{$newTableName}", $connDBA);
			
		//Update the directory name, if it exists
			$directoryCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$_SESSION['currentModule']}'", $connDBA);
			$directoryCheck = mysql_fetch_array($directoryCheckGrabber);
			
			if ($testCheck['lessonURL'] !== "") {
				$oldDirectory = str_replace(" ", "" , $directoryCheck['name']);
				$newDirectory = str_replace(" ", "", $name);
				rename("../../../modules/{$oldDirectory}", "../../../modules/{$newDirectory}");
			}
			
		//Update the category types for the test
			if ($testCheck['test'] == "1") {
				mysql_query("UPDATE moduletest_{$newTableName} SET `category` = '{$category}' WHERE `category` != ''", $connDBA);
			}
			
		//Reset the session name
			$_SESSION['currentModule'] = $name;
			$_SESSION['category'] = $category;
			$_SESSION['difficulty'] = $difficulty;
			
		//Execute command on database			
			$editModuleQueryResult = mysql_query($editModuleQuery, $connDBA);
			
			if (isset ($_SESSION['review'])) {
				header ("Location: modify.php?updated=lessonSettings");
				exit;
			} else {	
				header ("Location: lesson_settings.php");
				exit;
			}
		}
//If the settings are being inserted	
	} else {
	//Process the form
		if (isset($_POST['submit']) && !empty($_POST['name']) && is_numeric($_POST['category']) && !empty($_POST['employee']) && !empty($_POST['difficulty']) && !empty($_POST['time']) && !empty($_POST['timeLabel']) && is_numeric($_POST['locked']) && is_numeric($_POST['selected']) && is_numeric($_POST['skip']) && is_numeric($_POST['feedback'])) {
		//Do not process if a module with the same name exists
			$name = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$moduleCheck = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$name}'", $connDBA);
			if (mysql_fetch_array($moduleCheck)) {
				header("Location:lesson_settings.php?error=identical");
				exit;
			}
		
		//Get the last module position, and add one to the value for the next module
			$lastModuleGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position DESC", $connDBA);
			$lastModuleFetch = mysql_fetch_array($lastModuleGrabber);
			$position = $lastModuleFetch['position']+1;
			
			$name = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$category = mysql_real_escape_string($_POST['category']);
			$employee = mysql_real_escape_string($_POST['employee']);
			$difficulty = $_POST['difficulty'];
			$time = $_POST['time'];
			$timeLabel = $_POST['timeLabel'];
			$comments = mysql_real_escape_string($_POST['comments']);
			$locked = $_POST['locked'];
			$selected = $_POST['selected'];
			$skip = $_POST['skip'];
			$feedback = $_POST['feedback'];
			$tags = mysql_real_escape_string($_POST['tags']);
			$searchEngine = $_POST['searchEngine'];
			
			$timeFrame = $time . $timeLabel;
			
			$newModuleQuery = "INSERT INTO moduledata (
							`id`, `position`, `locked`, `avaliable`, `name`, `category`, `employee`, `difficulty`, `timeFrame`, `comments`, `selected`, `skip`, `feedback`, `tags`, `searchEngine`, `test`, `testName`, `directions`, `score`, `attempts`, `forceCompletion`, `completionMethod`, `delay`, `gradingMethod`, `penalties`, `timer`, `time`, `randomizeAll`
							) VALUES (
							NULL, '{$position}', '{$locked}', '', '{$name}', '{$category}', '{$employee}', '{$difficulty}', '{$timeFrame}', '{$comments}', '{$selected}', '{$skip}', '{$feedback}', '{$tags}', '{$searchEngine}', '', '', '', '', '', '', '', '', '', '', '', '', ''
							)";

		//Create a new table for the lesson content
			$dataBaseName = str_replace(" ", "", $name);
			mysql_query("CREATE TABLE IF NOT EXISTS `modulelesson_{$dataBaseName}` (
						  `id` int(255) NOT NULL AUTO_INCREMENT,
						  `position` int(100) NOT NULL,
						  `visible` int(1) NOT NULL,
						  `type` longtext NOT NULL,
						  `title` longtext NOT NULL,
						  `content` longtext NOT NULL,
						  `attachment` longtext NOT NULL,
						  `comments` longtext NOT NULL,
						  PRIMARY KEY (`id`)
						)");
			
		//Create a session with the name of the module in it, we will need it for later use
			$_SESSION['currentModule'] = $name;
			$_SESSION['category'] = $category;
			$_SESSION['difficulty'] = $difficulty;
			
		//Update the session to manage the steps
			$_SESSION['step'] = "lessonContent";
			
		//Execute command on database			
			$newModuleQueryResult = mysql_query($newModuleQuery, $connDBA);
			
			header ("Location: lesson_content.php");
			exit;
		}
	}
?>
<?php
	if (isset($_GET['checkName'])) {
		$inputNameSpaces = $_GET['checkName'];
		$inputNameNoSpaces = str_replace(" ", "", $_GET['checkName']);
		$checkName = mysql_query("SELECT * FROM `moduledata` WHERE `name` = '{$inputNameSpaces}'", $connDBA);
		
		if ($name = mysql_fetch_array($checkName)) {					
			if (isset($_SESSION['currentModule'])) {
				if (strtolower($name['name']) != strtolower($_SESSION['currentModule'])) {
					echo "<div class=\"error\" id=\"errorWindow\">A module with this name already exists</div>";
				} else {
					echo "<p>&nbsp;</p>";
				}
			} else {
				echo "<div class=\"error\" id=\"errorWindow\">A module with this name already exists</div>";
			}
		} else {
			echo "<p>&nbsp;</p>";
		}
		
		echo "<script type=\"text/javascript\">validateName()</script>";
		die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
<head>
<?php title("Module Setup Wizard : Module Settings"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<?php liveError(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
        <h2>Module Setup Wizard : Module Settings</h2>
<p>Setup the module's initial settings, such as the name, time frame, and any comments.</p>
<?php errorWindow("database", "A module with this name already exists", "error", "identical", "true"); ?>
      <form name="lessonSettings" action="lesson_settings.php" method="post" id="validate" onsubmit="return errorsOnSubmit(this);">
	  <?php
          step("one", "Module Information", "one" , "Module Information")
      ?>
      <div class="stepContent">
        <blockquote>
          <p>Module Name<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The name of the module')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              
              <input class="validate[required,custom[onlyLetter]] text-input" maxlength="100" autocomplete="off" name="name" type="text" id="name" size="50" onblur="checkName(this.name, 'lesson_settings')"<?php
				//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						echo " value=\"" . stripslashes($moduleData['name']) . "\"";
					}
				?> />
              
           </p>
          </blockquote>
          <p>Directions: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Comments or directions regarding the content of this module')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              <textarea name="comments" id="comments" cols="45" rows="5" style="font-family:Arial, Helvetica, sans-serif; width:450px;"><?php
		//Show the comments
			if (isset($_SESSION['currentModule'])) {							
				echo stripslashes($moduleData['comments']);
			}
		?></textarea>
            </p>
          </blockquote>
          <p>Time frame: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The amount of time the user will have to complete the module from the assigned date')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              <?php
			//Select the time frame
				if (isset($_SESSION['currentModule'])) {
					$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
					$numberArray = array("0","1","2","3","4","5","6","7","8","9");
											
					$time = str_replace($letterArray, "", $moduleData['timeFrame']);
					$timeLabel = str_replace($numberArray, "", $moduleData['timeFrame']);
					
					echo "<select name=\"time\" id=\"time\">";					
					for ($count=1; $count <= 24; $count++) {
						echo "<option value=\"" . $count . "\"";
						if ($time == $count) {
							echo " selected=\"selected\"";
						}
						echo ">" . $count . "</option>";
					}
					echo "</select>";
					
					echo "&nbsp;<select name=\"timeLabel\" id=\"timeLabel\">
						<option value=\"Days\""; if ($timeLabel == "Days") {echo " selected=\"selected\"";} echo ">Days</option>
						<option value=\"Weeks\""; if ($timeLabel == "Weeks") {echo " selected=\"selected\"";} echo ">Weeks</option>
						<option value=\"Months\""; if ($timeLabel == "Months") {echo " selected=\"selected\"";} echo ">Months</option>
						<option value=\"Years\""; if ($timeLabel == "Years") {echo " selected=\"selected\"";} echo ">Years</option>
					  </select>";
				} else {
					echo "<select name=\"time\" id=\"time\">";					
					for ($count=1; $count <= 24; $count++) {
						echo "<option value=\"" . $count . "\"";
						if ($count == "2") {
							echo " selected=\"selected\"";
						}
						echo ">" . $count . "</option>";
					}
					echo "</select>";
					
					echo "&nbsp;<select name=\"timeLabel\" id=\"timeLabel\">
						<option value=\"Days\">Days</option>
						<option value=\"Weeks\" selected=\"selected\">Weeks</option>
						<option value=\"Months\">Months</option>
						<option value=\"Years\">Years</option>
					  </select>";
				}
			?>
            </p>
          </blockquote>
          </blockquote>
        <blockquote>
          <p>Category<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The category that this modules covers')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              
              <select name="category" id="category" class="validate[required]">
                <?php
				//Select all of the category items
					$categoryGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
					//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						echo "<option value=\"\">- Select -</option>";
						while ($category = mysql_fetch_array($categoryGrabber)) {
							echo "<option value=\"" . $category['id'] . "\"";
							
							if ($category['id'] == $moduleData['category']) {
								echo " selected=\"selected\"";
							}
							
							echo ">" . stripslashes(htmlentities($category['category'])) . "</option>";
						}
					} else {
						echo "<option selected=\"selected\" value=\"\">- Select -</option>";
						while ($category = mysql_fetch_array($categoryGrabber)) {
							echo "<option value=\"" . $category['id'] . "\">" . stripslashes(htmlentities($category['category'])) . "</option>";
						}
					}
				?>
              </select>
            </p>
          </blockquote>
          <p>Intended employee type<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The employee position for which this module is intended')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              
              <select name="employee" id="employee" class="validate[required]">
                <?php
				//Select all of the employee types
					$employeeGrabber = mysql_query("SELECT * FROM moduleemployees ORDER BY position ASC", $connDBA);
					//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						echo "<option value=\"\">- Select -</option>";
						while ($employee = mysql_fetch_array($employeeGrabber)) {
							echo "<option value=\"" . $employee['id'] . "\"";
							
							if ($employee['id'] == $moduleData['employee']) {
								echo " selected=\"selected\"";
							}
							
							echo ">" . stripslashes(htmlentities($employee['employee'])) . "</option>";
						}
					} else {
						echo "<option selected=\"selected\" value=\"\">- Select -</option>";
						while ($employee = mysql_fetch_array($employeeGrabber)) {
							echo "<option value=\"" . $employee['id'] . "\">" . stripslashes(htmlentities($employee['employee'])) . "</option>";
						}
					}

				?>
              </select>
              
            </p>
          </blockquote>
          <p>Difficulty: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The overall difficulty of this module')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              
              <select name="difficulty" id="difficulty">
                <option value="Easy"<?php
				//Select difficulty level
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['difficulty'] == "Easy") {
							echo " selected=\"selected\"";
						}
					}
				?>>Easy</option>
                <option value="Average"<?php
				//Select difficulty level
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['difficulty'] == "Average") {
							echo " selected=\"selected\"";
						}
					} else {
						echo " selected=\"selected\"";
					}
				?>>Average</option>
                <option value="Difficult"<?php
				//Select difficulty level
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['difficulty'] == "Difficult") {
							echo " selected=\"selected\"";
						}
					}
				?>>Difficult</option>
                </select>
            </p>
          </blockquote>
        </blockquote>
        </div>
        <?php
          step("two", "Module Settings", "two" , "Module Settings")
      	?>
        <div class="stepContent">
          <blockquote>
            <p>Lock module: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Prevent organizations from customizing these settings for their needs')" onmouseout="UnTip()" /></p>
            <blockquote>
              <p>
                <label>
                  <input type="radio" name="locked" value="1" id="locked_0"<?php
				//Select the locked settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['locked'] == "1") {
							echo " checked=\"checked\"";
						}
					}
				?> />
                Yes</label>
                <label>
                  <input type="radio" name="locked" value="0" id="locked_1"<?php
				//Select the locked settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['locked'] == "0") {
							echo " checked=\"checked\"";
						}
					} else {
						echo " checked=\"checked\"";
					}
				?> />
                No</label>
                <br />
              </p>
            </blockquote>
            <p>Force module: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Force every user in this system to take this lesson')" onmouseout="UnTip()" /></p>
            <blockquote>
              <p>
                <label>
                  <input type="radio" name="selected" value="1" id="selected_0"<?php
				//Select the selected settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['selected'] == "1") {
							echo " checked=\"checked\"";
						}
					}
				?> />
                  Yes</label>
                <label>
                  <input type="radio" name="selected" value="0" id="selected_1"<?php
				//Select the selected settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['selected'] == "0") {
							echo " checked=\"checked\"";
						}
					} else {
						echo " checked=\"checked\"";
					}
				?> />
                No</label>
                <br />
              </p>
            </blockquote>
            <p>Permit user to skip module: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Premit users to skip this module and come back to it later')" onmouseout="UnTip()" /></p>
            <blockquote>
              <p>
                <label>
                  <input type="radio" name="skip" value="1" id="skip_0"<?php
				//Select the skip settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['skip'] == "1") {
							echo " checked=\"checked\"";
						}
					} else {
						echo " checked=\"checked\"";
					}
				?> />
                Yes</label>
                <label>
                  <input type="radio" name="skip" value="0" id="skip_1"<?php
				//Select the skip settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['skip'] == "0") {
							echo " checked=\"checked\"";
						}
					} else {
						echo " checked=\"checked\"";
					}
				?> />
                No</label>
              </p>
            </blockquote>
            <p>Force user to give feedback: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Force a user to provide feedback at the end of this module')" onmouseout="UnTip()" /></p>
            <blockquote>
              <p>
                <label>
                  <input type="radio" name="feedback" value="1" id="feedback_0"<?php
				//Select the skip settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['feedback'] == "1") {
							echo " checked=\"checked\"";
						}
					}
				?> />
                  Yes</label>
                <label>
                  <input type="radio" name="feedback" value="0" id="feedback_1"<?php
				//Select the skip settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['feedback'] == "0") {
							echo " checked=\"checked\"";
						}
					} else {
						echo " checked=\"checked\"";
					}
				?> />
                No</label>
                <br />
              </p>
            </blockquote>
            <p>Search keywords (Seperate keyword with a comma and a space): <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Supply a list of key words to help narrow down results in searches.<br />These seach results can show up on a search engine, such as Google, to help boost sales.')" onmouseout="UnTip()" /><br />
            </p>
            <blockquote>
              <p>
                <input name="tags" type="text" id="tags" size="50" autocomplete="off"<?php
				//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						echo " value=\"" . stripslashes(htmlentities($moduleData['tags'])) . "\"";
					}
				?> />
                <label>
                  <input type="checkbox" name="searchEngine" id="searchEngine"<?php
				//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						if ($moduleData['searchEngine'] == "on") {
							echo " checked=\"checked\"";
						}
					}
				?> />
                  Accessible by search engines</label>
              </p>
            </blockquote>
          </blockquote>        
        </div>
        <?php
			step("three", "Submit", "three" , "Submit")
		?>
        <div class="stepContent">
        <blockquote>
        <p>
          <?php
		  //Selectively display the buttons
		  		if (isset ($_SESSION['review'])) {
					submit("submit", "Modify Settings");
					echo "<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" onclick=\"MM_goToURL('parent','modify.php');return document.MM_returnValue\" />";
				} else {
					submit("submit", "Next Step &gt;&gt;");
					
					if (!isset($_SESSION['currentModule'])) {
						echo "<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" onclick=\"MM_goToURL('parent','../index.php');return document.MM_returnValue\" />";
					}
				}
		  ?>
          </p>
          <?php formErrors(); ?>
          </blockquote> 
        </div>
</form>    
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>