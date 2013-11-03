<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Grab all category data
	$categoryDataCheckGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
	$categoryDataCheck = mysql_fetch_array($categoryDataCheckGrabber);
	
//Check to see if any categories exist
	$categoryCheck = $categoryDataCheck['id'];
	if (!$categoryCheck) {
		$categories = "empty";
	} else {
		$categories = "exist";
	}
	
?>
<?php
//Grab all employee data
	$employeeDataCheckGrabber = mysql_query("SELECT * FROM moduleemployees ORDER BY position ASC", $connDBA);
	$employeeDataCheck = mysql_fetch_array($employeeDataCheckGrabber);
	
//Check to see if any employees exist
	$employeeCheck = $employeeDataCheck['id'];
	if (!$employeeCheck) {
		$employees = "empty";
	} else {
		$employees = "exist";
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Customize Settings"); ?>
<?php headers(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Customize Settings</h2>
<p>
  <?php
	if (!isset ($_GET['type'])) {
?>Customize settings which will appear in the module setup wizard: <img src="../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('<strong>Category</strong> - A general subject a module is presenting<br /><strong>Employee Type</strong> - A general position for which the module is intended')" onmouseout="UnTip()" /></p>
<blockquote>
  <p><a href="settings.php?type=category">Manage Module Categories</a></p>
      <p><a href="settings.php?type=employee">Manage Employee Types</a></p>
</blockquote>
<?php
	} else {
		if ($_GET['type'] == "category") {
?>
<?php
    //Reorder the items
        if (isset($_GET['id']) && isset($_GET['currentPosition']) && $_GET['action'] == "reorder") {
        //Grab all necessary data
            //Grab the id of the moving item
            $id = $_GET['id'];
            //Grab the new position of the item
            $newPosition = $_GET['position'];
            //Grab the old position of the item
            $currentPosition = $_GET['currentPosition'];
                
        //Do not process if item does not exist
            //Get item name by URL variable
            $getItemID = $_GET['position'];
        
            $itemCheckGrabber = mysql_query("SELECT * FROM modulecategories WHERE position = {$getItemID}", $connDBA);
            $itemCheckArray = mysql_fetch_array($itemCheckGrabber);
            $itemCheckResult = $itemCheckArray['position'];
                 if (isset ($itemCheckResult)) {
                     $itemCheck = 1;
                 } else {
                    $itemCheck = 0;
                 }
        
        //If the item is moved up...
            if ($currentPosition > $newPosition) {
            //Update the other items first, by adding a value of 1
                $otherPostionReorderQuery = "UPDATE modulecategories SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
                
            //Update the requested item	
                $currentItemReorderQuery = "UPDATE modulecategories SET position = '{$newPosition}' WHERE id = '{$id}'";
                
            //Execute the queries
                $otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
                $currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
        
            //No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
                header ("Location: settings.php?type=category");
                exit;
        //If the item is moved down...
            } elseif ($currentPosition < $newPosition) {
            //Update the other items first, by subtracting a value of 1
                $otherPostionReorderQuery = "UPDATE modulecategories SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
        
            //Update the requested item		
                $currentItemReorderQuery = "UPDATE modulecategories SET position = '{$newPosition}' WHERE id = '{$id}'";
            
            //Execute the queries
                $otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
                $currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
                
            //No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
                header ("Location: settings.php?type=category");
                exit;
            }
        }
    ?>
<?php
//Delete an item 
	if (isset ($_GET['action']) && $_GET['action'] == "delete") {
	//Do not process if item does not exist
	//Get item data by URL variable
		$getItemID = $_GET['id'];
		$getItemPosition = $_GET['category'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM modulecategories WHERE id = {$getItemID}", $connDBA);
		$itemCheckArray = mysql_fetch_array($itemCheckGrabber);
		$itemCheckResult = $itemCheckArray['id'];
			 if (isset ($itemCheckResult)) {
				 $itemCheck = 1;
			 } else {
				$itemCheck = 0;
			 }
	 
		if (!isset ($_GET['id']) || $_GET['id'] == 0 || $itemCheck == 0) {
			header ("Location: settings.php?type=category");
			exit;
		} else {
		//Update the database
			$deleteItem = $_GET['id'];
			
			$itemPositionGrabber = mysql_query("SELECT * FROM modulecategories WHERE id = {$deleteItem}", $connDBA);
			$itemPositionFetch = mysql_fetch_array($itemPositionGrabber);
			$itemPosition = $itemPositionFetch['position'];
			
			$otherItemsUpdateQuery = "UPDATE modulecategories SET position = position-1 WHERE position > '{$getItemPosition}'";
			$deleteItemQueryResult = mysql_query($otherItemsUpdateQuery, $connDBA);
			
			$deleteItemQuery = "DELETE FROM modulecategories WHERE id = {$deleteItem} LIMIT 1";
			$deleteItemQueryResult = mysql_query($deleteItemQuery, $connDBA);
			
			header ("Location: settings.php?type=category&result=success&updateType=deleted");
			exit;
		}
	}
?>
<?php
			if (!isset ($_GET['action'])) {
			//Unset unused sessions
				unset($_SESSION['moduleSettings']);
?>
    <p>A category is a general subject a module is presenting.</p>
    <p>&nbsp;</p>
    
<div class="toolBar"><a class="toolBarItem new" href="settings.php?type=category&amp;action=insert">Add New Category</a></div>
    <?php
	//Display a success message
		if (isset ($_GET['result']) && isset ($_GET['updateType'])) {
			if ($_GET['result'] == "success") {
				if ($_GET['updateType'] == "insert") {
					successMessage("The category was inserted");
				} elseif ($_GET['updateType'] == "update") {
					successMessage("The category was updated");
				} elseif ($_GET['updateType'] == "deleted") {
					successMessage("The category was deleted");
				}
			}
		} else {
			echo "&nbsp;";
		}
	?>
 <div class="layoutControl">
 <div class="dataLeft">
  <div class="block_course_list sideblock">
        <div class="header">
          <div class="title">
            <h2>Navigation</h2>
          </div>
        </div>
      <div class="content">
        <p>Modify other settings:</p>
        <ul>
          <li class="homeBullet"><a href="index.php">Back to Modules</a></li>
          <li class="arrowBullet"><a href="settings.php?type=category">Categories</a></li>
          <li class="arrowBullet"><a href="settings.php?type=employee">Employee Types</a></li>
        </ul>
      </div>
  </div>
  </div>
  <div class="contentRight">
  <?php
      if ($categories == "exist") {
           echo "<table class=\"dataTable\">";
              echo "<tbody>";
                  echo "<tr>";
                      echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
                      echo "<th class=\"tableHeader\">Category</th>";
                      echo "<th width=\"75\" class=\"tableHeader\">Edit</th>";
                      echo "<th width=\"75\" class=\"tableHeader\">Delete</th>";
                  echo "</tr>";
              //Select data for the loop
                  $categoryDataGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
                  
              //Select data for drop down menu
                  $dropDownDataGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position ASC", $connDBA);
                  while ($categoryData = mysql_fetch_array($categoryDataGrabber)){
                      echo "<tr";
                      if ($categoryData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
                      ">";
                          echo "<td width=\"75\"><form name=\"modules\" action=\"settings.php\"><input type=\"hidden\" name=\"type\" value=\"category\"><input type=\"hidden\" name=\"action\" value=\"reorder\">";
                                  echo "<select name=\"position\" onchange=\"this.form.submit();\">";
                                  $categoryCount = mysql_num_rows($dropDownDataGrabber);
                                  for ($count=1; $count <= $categoryCount; $count++) {
                                      echo "<option value=\"{$count}\"";
                                      if ($categoryData ['position'] == $count) {
                                          echo " selected=\"selected\"";
                                      }
                                      echo ">$count</option>";
                                  }
                                  echo "</select>";
                              echo "<input type=\"hidden\" name=\"id\" value=\"";
                              echo $categoryData['id'];
                              echo "\">";
                              echo "<input type=\"hidden\" name=\"currentPosition\" value=\"";
                              echo $categoryData['position'];
                              echo "\"></form></td>";
                                  
                          echo "<td>" . stripslashes(htmlentities($categoryData['category'])) . "</td>";
                          echo "<td width=\"75\"><a class=\"action edit\" href=\"settings.php?type=category&action=edit&category=" . $categoryData['position'] . "&id=" . $categoryData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . stripslashes(htmlentities($categoryData['category'])) . "</strong> category')\" onmouseout=\"UnTip()\"></a></td>";
                          echo "<td width=\"75\"><a class=\"action delete\" href=\"settings.php?type=category&action=delete&category=" .  $categoryData['position'] . "&id=" .  $categoryData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. If any modules are assigned to this category, they will have a blank category name until they are changed. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . stripslashes(htmlentities($categoryData['category'])) . "</strong> category')\" onmouseout=\"UnTip()\"></a></td>";
                      echo "</tr>";
                  }
              echo "</tbody>";
          echo "</table>";
      } else {
          echo "<div class=\"noResults\">There are no categories.</div>";
      }
	?>
   </div>
</div>
<?php 
	} elseif (isset ($_GET['action']) && $_GET['action'] == "edit" || $_GET['action'] == "insert") {
		if ($_GET['action'] == "insert") {
			$_SESSION['moduleSettings'] = "insert";
		} elseif ($_GET['action'] == "edit") {
			if (!isset ($_GET['category']) || !isset ($_GET['id'])) {
				header ("Location: settings.php?type=employee");
				exit;
			}
			$_SESSION['moduleSettings'] = "edit";
		} else {
			header ("Location: settings.php?type=employee");
			exit;
		}
?>
<?php
//Process the form
	if (isset($_POST['submitCategory']) && !empty($_POST['categoryName'])) {
		if (isset ($_SESSION['moduleSettings'])) {
			if ($_SESSION['moduleSettings'] == "insert") {
				$category = mysql_real_escape_string($_POST['categoryName']);
				$positionGrabber = mysql_query("SELECT * FROM modulecategories ORDER BY position DESC LIMIT 1", $connDBA);
				$position = mysql_fetch_array($positionGrabber);
				$newPosition = $position['position']+1;
				
				mysql_query ("INSERT INTO modulecategories (position, category) VALUES ('{$newPosition}', '{$category}')", $connDBA);
				header ("Location: settings.php?type=category&result=success&updateType=insert");
				exit;
			} 
			
			if ($_SESSION['moduleSettings'] == "edit") {
				$category = mysql_real_escape_string($_POST['categoryName']);
				$position = $_POST['position'];
				$id = $_GET['id'];
				
				mysql_query ("UPDATE modulecategories SET `category` = '{$category}' WHERE id = '{$id}'", $connDBA);
				header ("Location: settings.php?type=category&result=success&updateType=update");
				exit;
			}
		} else {
			header ("Location: settings.php?type=category");
			exit;
		}
	}
?>
</p>
      <p>Categories can be customized using the form below.</p>
      <p>&nbsp;</p>
<form action="settings.php?type=category&action=
<?php
//Supply a value if the category is being edited
	if (isset ($_SESSION['moduleSettings'])) {
		if ($_SESSION['moduleSettings'] == "edit") {
			$id = $_GET['id'];
			$category = $_GET['category'];
			
			echo "edit&category=" . $category . "&id=" . $id;
		} elseif ($_SESSION['moduleSettings'] == "insert") {
			echo "insert";
		}
	}
?>
" method="post" name="category" id="validate" onsubmit="return errorsOnSubmit(this);">
  <div class="catDivider one">Assign Category Name</div>
  <div class="stepContent">
<blockquote>
      <p>
        <input name="categoryName" type="text" id="categoryName" size="50" autocomplete="off" class="validate[required]"<?php
		//Supply a value if the category is being edited
			if (isset ($_SESSION['moduleSettings'])) {
				if ($_SESSION['moduleSettings'] == "edit") {
					$id = $_GET['id'];
					$categoryGrabber = mysql_query("SELECT * FROM modulecategories WHERE id = '{$id}' LIMIT 1", $connDBA);
					$category = mysql_fetch_array($categoryGrabber);
					
					echo " value=\"" . stripslashes(htmlentities($category['category'])) . "\"";
				}
			}
		?> />
      </p>
</blockquote>
</div>
    <div class="catDivider two">Submit</div>
    <div class="stepContent">
<blockquote>
      <p>
        <?php submit("submitCategory", "Submit"); ?>
        <input type="reset" name="resetCategory" id="resetCategory" value="Reset" />
        <input name="cancelCategory" type="button" id="cancelCategory" onclick="MM_goToURL('parent','settings.php?type=category');return document.MM_returnValue" value="Cancel" />
      </p>
      <?php formErrors(); ?>
</blockquote>
</div>
</form>
<p>
  <?php
		}
	} elseif ($_GET['type'] == "employee") {
?>
<?php
//Reorder the items
	if (isset($_GET['id']) && isset($_GET['currentPosition']) && $_GET['action'] == "reorder") {
	//Grab all necessary data
		//Grab the id of the moving item
		$id = $_GET['id'];
		//Grab the new position of the item
		$newPosition = $_GET['position'];
		//Grab the old position of the item
		$currentPosition = $_GET['currentPosition'];
			
	//Do not process if item does not exist
		//Get item name by URL variable
		$getItemID = $_GET['position'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM moduleemployees WHERE position = {$getItemID}", $connDBA);
		$itemCheckArray = mysql_fetch_array($itemCheckGrabber);
		$itemCheckResult = $itemCheckArray['position'];
			 if (isset ($itemCheckResult)) {
				 $itemCheck = 1;
			 } else {
				$itemCheck = 0;
			 }
	
	//If the item is moved up...
		if ($currentPosition > $newPosition) {
		//Update the other items first, by adding a value of 1
			$otherPostionReorderQuery = "UPDATE moduleemployees SET position = position + 1 WHERE position >= '{$newPosition}' AND position <= '{$currentPosition}'";
			
		//Update the requested item	
			$currentItemReorderQuery = "UPDATE moduleemployees SET position = '{$newPosition}' WHERE id = '{$id}'";
			
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
	
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: settings.php?type=employee");
			exit;
	//If the item is moved down...
		} elseif ($currentPosition < $newPosition) {
		//Update the other items first, by subtracting a value of 1
			$otherPostionReorderQuery = "UPDATE moduleemployees SET position = position - 1 WHERE position <= '{$newPosition}' AND position >= '{$currentPosition}'";
	
		//Update the requested item		
			$currentItemReorderQuery = "UPDATE moduleemployees SET position = '{$newPosition}' WHERE id = '{$id}'";
		
		//Execute the queries
			$otherPostionReorder = mysql_query($otherPostionReorderQuery, $connDBA);
			$currentItemReorder = mysql_query ($currentItemReorderQuery, $connDBA);
			
		//No matter what happens, the user will see the updated result on the editing screen. So, just redirect back to that page when done.
			header ("Location: settings.php?type=employee");
			exit;
		}
	}
?>
<?php
//Delete an item 
	if (isset ($_GET['action']) && $_GET['action'] == "delete") {
	//Do not process if item does not exist
	//Get item data by URL variable
		$getItemID = $_GET['id'];
		$getItemPosition = $_GET['employee'];
	
		$itemCheckGrabber = mysql_query("SELECT * FROM moduleemployees WHERE id = {$getItemID}", $connDBA);
		$itemCheckArray = mysql_fetch_array($itemCheckGrabber);
		$itemCheckResult = $itemCheckArray['id'];
			 if (isset ($itemCheckResult)) {
				 $itemCheck = 1;
			 } else {
				$itemCheck = 0;
			 }
	 
		if (!isset ($_GET['id']) || $_GET['id'] == 0 || $itemCheck == 0) {
			header ("Location: settings.php?type=employee");
			exit;
		} else {
		//Update the database
			$deleteItem = $_GET['id'];
			
			$itemPositionGrabber = mysql_query("SELECT * FROM moduleemployees WHERE id = {$deleteItem}", $connDBA);
			$itemPositionFetch = mysql_fetch_array($itemPositionGrabber);
			$itemPosition = $itemPositionFetch['position'];
			
			$otherItemsUpdateQuery = "UPDATE moduleemployees SET position = position-1 WHERE position > '{$getItemPosition}'";
			$deleteItemQueryResult = mysql_query($otherItemsUpdateQuery, $connDBA);
			
			$deleteItemQuery = "DELETE FROM moduleemployees WHERE id = {$deleteItem} LIMIT 1";
			$deleteItemQueryResult = mysql_query($deleteItemQuery, $connDBA);
			
			header ("Location: settings.php?type=employee&result=success&updateType=deleted");
			exit;
		}
	}
?>
<?php
		if (!isset ($_GET['action'])) {
		//Unset unused sessions
			unset($_SESSION['moduleSettings']);
?>
</p>
<p> An employee type is general position for which the module is intended.</p>
<p>&nbsp;</p>
<div class="toolBar"><a class="toolBarItem new" href="settings.php?type=employee&amp;action=insert">Add New Employee Type</a></div>
    <?php
	//Display a success message
		if (isset ($_GET['result']) && isset ($_GET['updateType'])) {
			if ($_GET['result'] == "success") {
				if ($_GET['updateType'] == "insert") {
					successMessage("The employee type was inserted");
				} elseif ($_GET['updateType'] == "update") {
					successMessage("The employee type was updated");
				} elseif ($_GET['updateType'] == "deleted") {
					successMessage("The employee type was deleted");
				}
			}
		} else {
			echo "&nbsp;";
		}
	?>
<div class="layoutControl">
 <div class="dataLeft">
  <div class="block_course_list sideblock">
        <div class="header">
          <div class="title">
            <h2>Navigation</h2>
          </div>
        </div>
      <div class="content">
        <p>Modify other settings:</p>
        <ul>
          <li class="homeBullet"><a href="index.php">Back to Modules</a></li>
          <li class="arrowBullet"><a href="settings.php?type=category">Categories</a></li>
          <li class="arrowBullet"><a href="settings.php?type=category">Employee Types</a></li>
        </ul>
        </div>
  </div>
  </div>
  <div class="contentRight">
      <?php
	  		if ($employees == "exist") {
					echo "<table class=\"dataTable\">";
					echo "<tbody>";
						echo "<tr>";
							echo "<th width=\"75\" class=\"tableHeader\">Order</th>";
							echo "<th class=\"tableHeader\">Employee Type</th>";
							echo "<th width=\"75\" class=\"tableHeader\">Edit</th>";
							echo "<th width=\"75\" class=\"tableHeader\">Delete</th>";
						echo "</tr>";
					//Select data for the loop
						$employeeDataGrabber = mysql_query("SELECT * FROM moduleemployees ORDER BY position ASC", $connDBA);
						
					//Select data for drop down menu
						$dropDownDataGrabber = mysql_query("SELECT * FROM moduleemployees ORDER BY position ASC", $connDBA);
						while ($employeeData = mysql_fetch_array($employeeDataGrabber)){
							echo "<tr";
							if ($employeeData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
							">";
								echo "<td width=\"75\"><form name=\"modules\" action=\"settings.php\"><input type=\"hidden\" name=\"type\" value=\"employee\"><input type=\"hidden\" name=\"action\" value=\"reorder\">";
										echo "<select name=\"position\" onchange=\"this.form.submit();\">";
										$employeeCount = mysql_num_rows($dropDownDataGrabber);
										for ($count=1; $count <= $employeeCount; $count++) {
											echo "<option value=\"{$count}\"";
											if ($employeeData ['position'] == $count) {
												echo " selected=\"selected\"";
											}
											echo ">$count</option>";
										}
										echo "</select>";
									echo "<input type=\"hidden\" name=\"action\" value=\"reorder\">";
									echo "<input type=\"hidden\" name=\"id\" value=\"";
									echo $employeeData['id'];
									echo "\">";
									echo "<input type=\"hidden\" name=\"currentPosition\" value=\"";
									echo $employeeData['position'];
									echo "\"></form></td>";
										
								echo "<td>" . stripslashes(htmlentities($employeeData['employee'])) . "</td>";
								echo "<td width=\"75\"><a class=\"action edit\" href=\"settings.php?type=employee&action=edit&employee=" . $employeeData['position'] . "&id=" . $employeeData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" . stripslashes(htmlentities($employeeData['employee'])) . "</strong> employee type')\" onmouseout=\"UnTip()\"></a></td>";
								echo "<td width=\"75\"><a class=\"action delete\" href=\"settings.php?type=employee&action=delete&employee=" .  $employeeData['position'] . "&id=" .  $employeeData['id'] . "\" onclick=\"return confirm ('This action cannot be undone. If any modules are assigned to this employee type, they will have a blank employee type until they are changed. Continue?');\" onmouseover=\"Tip('Delete the <strong>" . stripslashes(htmlentities($employeeData['employee'])) . "</strong> employee type')\" onmouseout=\"UnTip()\"></a></td>";
							echo "</tr>";
						}
					echo "</tbody>";
				echo "</table>";
			} else {
				echo "<div class=\"noResults\">There are no employee types.</div>";
			}
	  ?>
  </div>
</div>
<?php 
	} elseif (isset ($_GET['action']) && $_GET['action'] == "edit" || $_GET['action'] == "insert") {
		if ($_GET['action'] == "insert") {
			$_SESSION['moduleSettings'] = "insert";
		} elseif ($_GET['action'] == "edit") {
			if (!isset ($_GET['employee']) || !isset ($_GET['id'])) {
				header ("Location: settings.php?type=employee");
				exit;
			}
			$_SESSION['moduleSettings'] = "edit";
		} else {
			header ("Location: settings.php?type=employee");
			exit;
		}
?>
      <?php
//Process the form
	if (isset($_POST['submitEmployee'])) {
		if (isset ($_SESSION['moduleSettings']) && !empty($_POST['employeeName'])) {
			if ($_SESSION['moduleSettings'] == "insert") {
				$employee = mysql_real_escape_string($_POST['employeeName']);
				$positionGrabber = mysql_query("SELECT * FROM moduleemployees ORDER BY position DESC LIMIT 1", $connDBA);
				$position = mysql_fetch_array($positionGrabber);
				$newPosition = $position['position']+1;
				
				mysql_query ("INSERT INTO moduleemployees (position, employee) VALUES ('{$newPosition}', '{$employee}')", $connDBA);
				header ("Location: settings.php?type=employee&result=success&updateType=insert");
				exit;
			} 
			
			if ($_SESSION['moduleSettings'] == "edit") {
				$employee = mysql_real_escape_string($_POST['employeeName']);
				$position = $_POST['position'];
				$id = $_GET['id'];
				
				mysql_query ("UPDATE moduleemployees SET `employee` = '{$employee}' WHERE id = '{$id}'", $connDBA);
				header ("Location: settings.php?type=employee&result=success&updateType=update");
				exit;
			}
		} else {
			header ("Location: settings.php?type=employee");
			exit;
		}
	}
?>
    <p>Employee types can be customized using the form below.</p>
<p>&nbsp;</p>
    <form action="settings.php?type=employee&amp;action=
<?php
//Supply a value if the category is being edited
	if (isset ($_SESSION['moduleSettings'])) {
		if ($_SESSION['moduleSettings'] == "edit") {
			$id = $_GET['id'];
			$employee = $_GET['employee'];
			
			echo "edit&employee=" . $employee . "&id=" . $id;
		} elseif ($_SESSION['moduleSettings'] == "insert") {
			echo "insert";
		}
	}
?>
" method="post" name="employee" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Assign Employee Type</div>
      <div class="stepContent">
      <blockquote>
        <p>
        <input name="employeeName" type="text" id="employeeName" size="50" autocomplete="off" class="validate[required]"<?php
		//Supply a value if the category is being edited
			if (isset ($_SESSION['moduleSettings'])) {
				if ($_SESSION['moduleSettings'] == "edit") {
					$id = $_GET['id'];
					$employeeGrabber = mysql_query("SELECT * FROM moduleemployees WHERE id = '{$id}' LIMIT 1", $connDBA);
					$employee = mysql_fetch_array($employeeGrabber);
					
					echo " value=\"" . stripslashes(htmlentities($employee['employee'])) . "\"";
				}
			}
		?> />
        </p>
      </blockquote>
      </div>
      <div class="catDivider two">Submit</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php submit("submitEmployee", "Submit"); ?>
          <input type="reset" name="resetEmployee" id="resetEmployee" value="Reset" />
          <input name="cancelEmployee" type="button" id="cancelEmployee" onclick="MM_goToURL('parent','settings.php?type=employee');return document.MM_returnValue" value="Cancel" />
        </p>
        <?php formErrors(); ?>
      </blockquote>
      </div>
</form>

<?php
		} else {
			header ("Location: settings.php");
			exit;
		}
	}
}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>