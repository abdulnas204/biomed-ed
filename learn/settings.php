<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Settings", "Student,Site Administrator", "validate", true); 
	
//Create a navigation box function
	function navigationBox() {
		sideBox("Navigation", "Custom Content", "<p>Modify other settings:</p><ul><li class=\"homeBullet\">" . URL("Back to Modules", "index.php") . "</li><li class=\"arrowBullet\">" . URL("Categories", "settings.php?type=category") . "</li><li class=\"arrowBullet\">" . URL("Employee Types", "settings.php?type=employee") . "</li></ul>");
	}
	
//Display the list of possible settings
	if (!isset ($_GET['type'])) {
	//Title
		title("Settings", "Customize settings which will appear in the module setup wizard");
		
	//Page content
		echo "<blockquote>";
		echo "<p>" . URL("Manage Module Categories", "settings.php?type=category") . " - A general subject a module is presenting</p>";
		echo "<p>" . URL("Manage Employee Types", "settings.php?type=employee") . " - A general position for which the module is intended</p>";
		echo "</blockquote>";
//Display the categories
	} elseif ($_GET['type'] == "category") {
	//Reorder the categories
		reorder("modulecategories", "settings.php?type=category");
		
	//Delete a category
		delete("modulecategories", "settings.php?type=category");
		
		if (!isset ($_GET['id']) && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == "delete"))) {
		//Title
			title("Settings", "A category is a general subject a module is presenting.");
			
		//Admin toolbar
			echo "<div class=\"toolBar\">";
			echo URL("Add New Category", "settings.php?type=category&action=insert", "toolBarItem new");
			echo "</div>";
			
		//Display message updates
			message("message", "inserted", "success", "The category was inserted");
			message("message", "updated", "success", "The category was updated");
			
		//Site layout
			echo "<div class=\"layoutControl\"><div class=\"dataLeft\">";
			
		//Navigation box
			navigationBox();
		
		//Site layout
		   	echo "</div><div class=\"contentRight\">";
			
		//Categories table
			if (exist("modulecategories") == true) {
			   $categoryDataGrabber = mysql_query("SELECT * FROM `modulecategories` ORDER BY `position` ASC", $connDBA);
				
			   echo "<table class=\"dataTable\"><tbody><tr><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\">Category</th><th width=\"75\" class=\"tableHeader\">Edit</th><th width=\"75\" class=\"tableHeader\">Delete</th></tr>";
              	
			   while ($categoryData = mysql_fetch_array($categoryDataGrabber)){
					echo "<tr";
					if ($categoryData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"75\">"; reorderMenu($categoryData['id'], $categoryData['position'], "categoryData", "modulecategories"); "</td>";
					echo "<td>" . prepare($categoryData['category'], false, true) . "</td>";
					echo "<td width=\"75\">" . URL("", "settings.php?type=category&id=" . $categoryData['id'], "action edit", false, "Edit the <strong>" . $categoryData['category'] . "</strong> category") . "</td>";
					echo "<td width=\"75\">" . URL("", "settings.php?type=category&action=delete&id=" . $categoryData['id'], "action delete", false, "Delete the <strong>" . $categoryData['category'] . "</strong> category", true, false, false, false) . "</td>";
					echo "</tr>";
			   }
			   
               echo "</tbody></table>";
			} else {
			   echo "<div class=\"noResults\">There are no categories.</div>";
			}
			
		//Close the site layout
			echo "</div></div>";
		} elseif (isset($_GET['id']) || (isset($_GET['action']) && $_GET['action'] == "insert")) {
		//Grab the category data
			if (isset($_GET['id'])) {
				if (exist("modulecategories", "id", $_GET['id']) == true) {
					$id = $_GET['id'];
					$categoryGrabber = mysql_query("SELECT * FROM `modulecategories` WHERE `id` = '{$id}' ORDER BY `position` ASC", $connDBA);
					$category = mysql_fetch_array($categoryGrabber);
				} else {
					redirect("settings.php?type=category");
				}
			}
			
		//Process the form
			if (isset($_POST['submit']) && !empty($_POST['category'])) {
				$category = mysql_real_escape_string($_POST['category']);
				$newPosition = lastItem("modulecategories");
				
				if (isset($_GET['id'])) {
					mysql_query ("UPDATE `modulecategories` SET `category` = '{$category}' WHERE `id` = '{$id}'", $connDBA);
								
					redirect("settings.php?type=category&message=updated");
				} else {
					mysql_query ("INSERT INTO `modulecategories` (
								`id`, `position`, `category`
								) VALUES (
								NULL, '{$newPosition}', '{$category}'
								)", $connDBA);
								
					redirect("settings.php?type=category&message=inserted");
				}
			}
			
		//Title
			title("Settings", "Categories can be customized using the form below.");
		
		//Categories form
			form("category");
			catDivider("Category Name", "one", true);
			echo "<blockquote><p>";
			textField("category", "category", false, false, false, true, false, false, "category", "category");
			echo "</p></blockquote>";
			
			catDivider("Submit", "two");
			echo "<blockquote><p>";
			button("submit", "submit", "Submit", "submit");
			button("reset", "reset", "Reset", "reset");
			button("cancel", "cancel", "Cancel", "cancel", "settings.php?type=category");
			echo "</p></blockquote>";
			closeForm(true, true);
		}
//Display the employee types
	} elseif ($_GET['type'] == "employee") {
	//Reorder the employee types
		reorder("moduleemployees", "settings.php?type=employee");
		
	//Delete an employee type
		delete("moduleemployees", "settings.php?type=employee");
		
		if (!isset ($_GET['id']) && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == "delete"))) {		
		//Title
			title("Settings", "An employee type is general position for which the module is intended.");
			
		//Admin toolbar
			echo "<div class=\"toolBar\">";
			echo URL("Add New Employee Type", "settings.php?type=employee&action=insert", "toolBarItem new");
			echo "</div>";
			
		//Display message updates
			message("message", "inserted", "success", "The employee type was inserted");
			message("message", "updated", "success", "The employee type was updated");
			
		//Site layout
			echo "<div class=\"layoutControl\"><div class=\"dataLeft\">";
			
		//Navigation box
			navigationBox();
		
		//Site layout
		   	echo "</div><div class=\"contentRight\">";
			
		//Employee types table
			if (exist("moduleemployees") == true) {
			   $employeeDataGrabber = mysql_query("SELECT * FROM `moduleemployees` ORDER BY `position` ASC", $connDBA);
				
			   echo "<table class=\"dataTable\"><tbody><tr><th width=\"75\" class=\"tableHeader\">Order</th><th class=\"tableHeader\">Category</th><th width=\"75\" class=\"tableHeader\">Edit</th><th width=\"75\" class=\"tableHeader\">Delete</th></tr>";
              	
			   while ($employeeData = mysql_fetch_array($employeeDataGrabber)){
					echo "<tr";
					if ($employeeData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
					echo "<td width=\"75\">"; reorderMenu($employeeData['id'], $employeeData['position'], "employeeData", "moduleemployees"); "</td>";
					echo "<td>" . prepare($employeeData['employee'], false, true) . "</td>";
					echo "<td width=\"75\">" . URL("", "settings.php?type=employee&id=" . $employeeData['id'], "action edit", false, "Edit the <strong>" . $employeeData['employee'] . "</strong> employee type") . "</td>";
					echo "<td width=\"75\">" . URL("", "settings.php?type=employee&action=delete&id=" . $employeeData['id'], "action delete", false, "Delete the <strong>" . $employeeData['employee'] . "</strong> employee types", true, false, false, false) . "</td>";
					echo "</tr>";
			   }
			   
               echo "</tbody></table>";
			} else {
			   echo "<div class=\"noResults\">There are no categories.</div>";
			}
			
		//Close the site layout
			echo "</div></div>";
		} elseif (isset($_GET['id']) || (isset($_GET['action']) && $_GET['action'] == "insert")) {
		//Grab the employee type data
			if (isset($_GET['id'])) {
				if (exist("moduleemployees", "id", $_GET['id']) == true) {
					$id = $_GET['id'];
					$employeeGrabber = mysql_query("SELECT * FROM `moduleemployees` WHERE `id` = '{$id}' ORDER BY `position` ASC", $connDBA);
					$employee = mysql_fetch_array($employeeGrabber);
				} else {
					redirect("settings.php?type=employee");
				}
			}
			
		//Process the form
			if (isset($_POST['submit']) && !empty($_POST['employee'])) {
				$employee = mysql_real_escape_string($_POST['employee']);
				$newPosition = lastItem("moduleemployees");
				
				if (isset($_GET['id'])) {
					mysql_query ("UPDATE `moduleemployees` SET `employee` = '{$employee}' WHERE `id` = '{$id}'", $connDBA);
								
					redirect("settings.php?type=employee&message=updated");
				} else {
					mysql_query ("INSERT INTO `moduleemployees` (
								`id`, `position`, `employee`
								) VALUES (
								NULL, '{$newPosition}', '{$employee}'
								)", $connDBA);
								
					redirect("settings.php?type=employee&message=inserted");
				}
			}
			
		//Title
			title("Settings", "Employee types can be customized using the form below.");
		
		//Categories form
			form("employee");
			catDivider("Employee Type Name", "one", true);
			echo "<blockquote><p>";
			textField("employee", "employee", false, false, false, true, false, false, "employee", "employee");
			echo "</p></blockquote>";
			
			catDivider("Submit", "two");
			echo "<blockquote><p>";
			button("submit", "submit", "Submit", "submit");
			button("reset", "reset", "Reset", "reset");
			button("cancel", "cancel", "Cancel", "cancel", "settings.php?type=employee");
			echo "</p></blockquote>";
			closeForm(true, true);
		}
	}
	
//Include the footer
	footer();
?>