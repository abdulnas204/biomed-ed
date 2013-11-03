<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
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
		$name = $_SESSION['currentModule'];
		$moduleDataGrabber = mysql_query("SELECT * FROM moduledata WHERE name = '{$name}'", $connDBA);
		$moduleData = mysql_fetch_array($moduleDataGrabber);
		
	//Process the form
		if (isset($_POST['submit'])) {
		//Do not process if a module with the same name exists
			$name = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$moduleCheck = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$name}'", $connDBA);
			if (mysql_fetch_array($moduleCheck)) {
				if ($moduleData['name'] !== $name) {
					header("Location:lesson_settings.php?error=identical");
					exit;
				}
			}
		
			$id = $moduleData['id'];
			$name = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$category = mysql_real_escape_string($_POST['category']);
			$employee = mysql_real_escape_string($_POST['employee']);
			$difficulty = $_POST['difficulty'];
			$time = $_POST['time'];
			$timeLabel = $_POST['timeLabel'];
			$comments = mysql_real_escape_string($_POST['comments']);
			$lock = $_POST['lock'];
			$selected = $_POST['selected'];
			$skip = $_POST['skip'];
			
			$timeFrame = $time . $timeLabel;
			
			$editModuleQuery = "UPDATE moduledata SET `lock` = '{$lock}', `name` = '{$name}', `category` = '{$category}', `employee` = '{$employee}', `difficulty` = '{$difficulty}', `timeFrame` = '{$timeFrame}', `comments` = '{$comments}', `selected` = '{$selected}', `skip` = '{$skip}' WHERE id = '{$id}'";
			
		//Update the session to manage the steps
			$_SESSION['step'] = "lessonContent";
			
		//Update the test table, if it exists
			$testCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$_SESSION['currentModule']}'", $connDBA);
			$testCheck = mysql_fetch_array($testCheckGrabber);
			
			if ($testCheck['test'] == "1") {
				$oldTableName = $testCheck['testName'];
				$newTableName = str_replace(" ", "", $name);
				mysql_query("ALTER TABLE moduletest_{$oldTableName} RENAME TO moduletest_{$newTableName}", $connDBA);
			}
			
		//Update the directory name, if it exists
			$directoryCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE `name` = '{$_SESSION['currentModule']}'", $connDBA);
			$directoryCheck = mysql_fetch_array($directoryCheckGrabber);
			
			if ($testCheck['lessonURL'] !== "") {
				$oldDirectory = str_replace(" ", "" , $directoryCheck['name']);
				$newDirectory = str_replace(" ", "", $name);
				rename("../../../modules/{$oldDirectory}", "../../../modules/{$newDirectory}");
			}
			
		//Reset the session name
			$_SESSION['currentModule'] = $name;
			
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
		if (isset($_POST['submit'])) {
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
			$lastModule = $lastModuleFetch['position']+1;
			
			$name = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$category = mysql_real_escape_string($_POST['category']);
			$employee = mysql_real_escape_string($_POST['employee']);
			$difficulty = $_POST['difficulty'];
			$time = $_POST['time'];
			$timeLabel = $_POST['timeLabel'];
			$comments = mysql_real_escape_string($_POST['comments']);
			$lock = $_POST['lock'];
			$selected = $_POST['selected'];
			$skip = $_POST['skip'];
			$time = serialize("1");
			
			$timeFrame = $time . $timeLabel;
				
			$newModuleQuery = "INSERT INTO moduledata (
							`id`, `position`, `lock`, `avaliable`, `name`, `category`, `employee`, `difficulty`, `timeFrame`, `comments`, `selected`, `skip`, `test`, `testName`, `directions`, `score`, `attempts`, `forceCompletion`, `completionMethod`, `delay`, `gradingMethod`, `penalties`, `timer`, `time`, `randomizeAll`, `randomizeQuestions`, `lessonURL`
							) VALUES (
							NULL, '{$lastModule}', '{$lock}', '0', '{$name}', '{$category}', '{$employee}', '{$difficulty}', '{$timeFrame}', '{$comments}', '{$selected}', '{$skip}', '0', 'T', 'D', '100', '0', 'F', 'C', '0', 'G', '0', '0', '{$time}', 'R', 'R', 'U'
							)";
			
		//Create a session with the name of the module in it, we will need it for later use
			$_SESSION['currentModule'] = $name;
			
		//Update the session to manage the steps
			$_SESSION['step'] = "lessonContent";
			
		//Execute command on database			
			$newModuleQueryResult = mysql_query($newModuleQuery, $connDBA);	
			header ("Location: lesson_content.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Module Settings"); ?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<?php validate(); ?>
</head>
<body>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
        <h2>Module Setup Wizard : Module Settings</h2>
<p>Setup the module's initial settings, such as the name, time frame, and any comments.</p>
<p>
<?php
//Display an error message
	if (isset ($_GET['error'])) {
		if ($_GET['error'] == "identical") {
			errorMessage("A module with this name already exists.");
		}
	} else {
		echo "&nbsp;";
	}
?>
</p>
      <form name="lessonSettings" action="lesson_settings.php" method="post" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider">
        <?php
			step("1", "Module Information", "1" , "Module Information")
		?>
      </div>
      <div class="stepContent">
        <blockquote>
          <p>Module Name<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The name of the module')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              <label>
              <input class="validate[required,custom[onlyLetter]] text-input" maxlength="100" autocomplete="off" name="name" type="text" id="name" size="50"<?php
				//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						echo " value=\"" . stripslashes($moduleData['name']) . "\"";
					}
				?> />
              </label>
           </p>
          </blockquote>
          <p>Category<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The category that this modules covers')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              <label>
              <select name="category" id="category" class="validate[required]">
              	<?php
				//Select all of the category items
					$categoryGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
					//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						echo "<option value=\"\">- Select -</option>";
						while ($category = mysql_fetch_array($categoryGrabber)) {
							echo "<option value=\"" . stripslashes($category['category']) . "\"";
							
							if ($category['category'] == $moduleData['category']) {
								echo " selected=\"selected\"";
							}
							
							echo ">" . stripslashes($category['category']) . "</option>";
						}
					} else {
						echo "<option selected=\"selected\" value=\"\">- Select -</option>";
						while ($category = mysql_fetch_array($categoryGrabber)) {
							echo "<option value=\"" . stripslashes($category['category']) . "\">" . stripslashes($category['category']) . "</option>";
						}
					}
				?>
              </select>
              </label></p>
          </blockquote>
          <p>Intended Employee Type<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The employee position for which this module is intended')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              <label>
              <select name="employee" id="employee" class="validate[required]">
              <?php
				//Select all of the employee types
					$employeeGrabber = mysql_query("SELECT * FROM moduleemployees ORDER BY position ASC", $connDBA);
					//If the module is being edited
					if (isset($_SESSION['currentModule'])) {
						echo "<option value=\"\">- Select -</option>";
						while ($employee = mysql_fetch_array($employeeGrabber)) {
							echo "<option value=\"" . stripslashes($employee['employee']) . "\"";
							
							if ($employee['employee'] == $moduleData['employee']) {
								echo " selected=\"selected\"";
							}
							
							echo ">" . stripslashes($employee['employee']) . "</option>";
						}
					} else {
						echo "<option selected=\"selected\" value=\"\">- Select -</option>";
						while ($employee = mysql_fetch_array($employeeGrabber)) {
							echo "<option value=\"" . stripslashes($employee['employee']) . "\">" . stripslashes($employee['employee']) . "</option>";
						}
					}

				?>
              </select>
              </label>
            </p>
          </blockquote>
          <p>Difficulty: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The overall difficulty of this module')" onmouseout="UnTip()" /></p>
          <blockquote>
            <p>
              <label>
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
              </label>
            </p>
          </blockquote>
          <p>Time Frame: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The amount of time the student will have to complete the module from the date it is assigned')" onmouseout="UnTip()" /></p>
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
					
					echo "<select name=\"timeLabel\" id=\"timeLabel\">
						<option value=\"Days\""; if ($timeLabel == "Days") {echo " selected=\"selected\"";} echo ">Days</option>
						<option value=\"Weeks\""; if ($timeLabel == "Weeks") {echo " selected=\"selected\"";} echo ">Weeks</option>
						<option value=\"Months\""; if ($timeLabel == "Months") {echo " selected=\"selected\"";} echo ">Months</option>
						<option value=\"Years\""; if ($timeLabel == "Years") {echo " selected=\"selected\"";} echo ">Years</option>
					  </select>";
				} else {
					echo "<select name=\"time\" id=\"time\">";					
					for ($count=1; $count <= 24; $count++) {
						echo "<option value=\"" . $count . "\"";
						if ($count == "3") {
							echo " selected=\"selected\"";
						}
						echo ">" . $count . "</option>";
					}
					echo "</select>";
					
					echo "<select name=\"timeLabel\" id=\"timeLabel\">
						<option value=\"Days\">Days</option>
						<option value=\"Weeks\">Weeks</option>
						<option value=\"Months\" selected=\"selected\">Months</option>
						<option value=\"Years\">Years</option>
					  </select>";
				}
			?>
            </p>
          </blockquote>
          <p>Comments: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Any comments or directions regarding the content of this module')" onmouseout="UnTip()" /></p>
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
          <p>&nbsp;</p>
        </blockquote>
        </div>
        <div class="catDivider">
        <?php
			step("2", "Module Settings", "2" , "Module Settings")
		?>
        </div>
        <div class="stepContent">
          <blockquote>
            <p>Lock Module: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Prevent organizations from customizing these settings for their needs. <br />When an organization customizes these settings, it will only be applied to the corresponding organization.')" onmouseout="UnTip()" /></p>
            <blockquote>
              <p>
                <select name="lock" id="lock">
                  <option value="1"<?php
				//Select the locked settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['lock'] == "1") {
							echo " selected=\"selected\"";
						}
					} else {
						echo " selected=\"selected\"";
					}
				?>>Unlocked</option>
                  <option value="0"<?php
				//Select the selected settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['lock'] == "0") {
							echo " selected=\"selected\"";
						}
					}
				?>>Locked</option>
                </select>
              </p>
            </blockquote>
            <p>Selected during Setup: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('This module will be selected by default when organization instructors are assigning modules to students.&lt;br /&gt;(i.e.: This module contains important information, and should be included in every training course.)')" onmouseout="UnTip()" /></p>
            <blockquote>
              <p>
                <select name="selected" id="selected">
                  <option value="0"<?php
				//Select the selected settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['selected'] == "0") {
							echo " selected=\"selected\"";
						}
					} else {
						echo " selected=\"selected\"";
					}
				?>>No</option>
                  <option value="1"<?php
				//Select the selected settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['selected'] == "1") {
							echo " selected=\"selected\"";
						}
					}
				?>>Yes</option>
                </select>
              </p>
            </blockquote>
            <p>Permit user to skip module: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Premit students to skip this module and come back to it later. <br/ >This option allows students to take modules out of the presented order.')" onmouseout="UnTip()" /></p>
            <blockquote>
              <p>
                <select name="skip" id="skip">
                  <option value="0"<?php
				//Select the skip settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['skip'] == "0") {
							echo " selected=\"selected\"";
						}
					} else {
						echo " selected=\"selected\"";
					}
				?>>No</option>
                  <option value="1"<?php
				//Select the skip settings
					if (isset($_SESSION['currentModule'])) {							
						if ($moduleData['skip'] == "1") {
							echo " selected=\"selected\"";
						}
					}
				?>>Yes</option>
                </select>
              </p>
            </blockquote>
          </blockquote>        
        </div>
        <div class="catDivider">
        <?php
			step("3", "Submit", "3" , "Submit")
		?>
        </div>
        <div class="stepContent">
        <blockquote>
          <?php
		  //Selectively display the buttons
		  		if (isset ($_SESSION['review'])) {
					submit("submit", "Modify Settings");
					echo "<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" onclick=\"MM_goToURL('parent','modify.php');return document.MM_returnValue\" />";
				} else {
					submit("submit", "Next Step &gt;&gt;");
				}
		  ?>
          <?php formErrors(); ?>
          </blockquote> 
        </div>
</form>    
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>