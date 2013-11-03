<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: November 24th, 2010
Last updated: December 21st, 2010

This is the page which manages the learning units generator 
fields.
*/

//Header functions
	require_once('../../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Manage Field", "tinyMCESimple,validate,administrativeLibrary", true);
	lockAccess();
	
//Grab the form values
	if (isset($_GET['id']) && exist("fields", "id", $_GET['id'])) {
		$fields = query("SELECT * FROM `fields` WHERE `id` = '{$_GET['id']}'");
	}
	
//Process the form	
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['description']) && is_numeric($_POST['require']) && is_numeric($_POST['testFilter']) && is_numeric($_POST['autoSuggest']) && is_numeric($_POST['showTip']) && !empty($_POST['fieldType']) && !empty($_POST['section'])) {
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		$require = $_POST['require'];
		$testFilter = $_POST['testFilter'];
		$autoSuggest = $_POST['autoSuggest'];
		$showTip = $_POST['showTip'];
		$fieldType = $_POST['fieldType'];
		$selected = escape(serialize($_POST['selected']));
		$values = escape(serialize($_POST['values']));
		$section = escape(serialize($_POST['section']));
		
		if (!isset($form)) {
			$position = lastItem("fields");
			
			if (isset($fields)) {
				query("UPDATE `fields` SET `section` = '{$section}', `name` = '{$name}', `description` = '{$description}', `showTip` = '{$showTip}', `require` = '{$require}', `testFilter` = '{$testFilter}', `autoSuggest` = '{$autoSuggest}', `fieldType` = '{$fieldType}', `selected` = '{$selected}', `values` = '{$values}' WHERE `id` = '{$fields['id']}'");
				redirect("index.php?message=updated");
			} else {
				$units = query("SELECT * FROM `learningunits`", "raw");
				$organizations = query("SELECT * FROM `organizations`", "raw");
				
				query("INSERT INTO `fields` (
					  `id`, `position`, `section`, `name`, `description`, `showTip`, `require`, `testFilter`, `autoSuggest`, `fieldType`, `selected`, `values` 
					  ) VALUES (
					  NULL, '{$position}', '{$section}', '{$name}', '{$description}', '{$showTip}', '{$require}', '{$testFilter}', '{$autoSuggest}', '{$fieldType}', '{$selected}', '{$values}'
					  )");
					  
				$column = mysql_insert_id();
				
				while ($unit = fetch($units)) {
					query("ALTER TABLE `test_{$unit['id']}` ADD COLUMN `field_{$column}` longtext NOT NULL", false, false);
				}
				
				while ($organization = fetch($organizations)) {
					query("ALTER TABLE `questionbank_{$organization['id']}` ADD COLUMN `field_{$column}` longtext NOT NULL", false, false);
				}
				
				query("ALTER TABLE `questionbank_0` ADD COLUMN `field_{$column}` longtext NOT NULL", false, false);
				query("ALTER TABLE `learningunits` ADD COLUMN `field_{$column}` longtext NOT NULL", false, false); 
				redirect("index.php?message=inserted");
			}
		}
	}
	
//Title
	title("Manage Field", "The page will manage individual fields, and their settings.");

//Fields form
	echo form("fields");
	catDivider("Field Name", "one", true);
	echo "<blockquote>\n";
	directions("Field name", true, "The name which will display above the field, <br />like the text at left");
	indent(textField("name", "name", false, false, false, true, false, false, "fields", "name"));
	directions("Description", true, "A description used for back-end <br />notes, or as a helpful tip to users");
	indent(textArea("description", "description", "small", true, false, false, "fields", "description"));
	echo "</blockquote>\n";
	
	catDivider("Field Settings", "two");
	echo "<blockquote>\n";
	directions("Required", true, "Set whether or not this field requires entry");
	indent(radioButton("require", "require", "Yes,No", "1,0", true, false, false, "1", "fields", "require"));
	directions("Test builder filter", true, "This option will show as a filter when a user or <br />instructor is generating a customized test for a student");
	indent(radioButton("testFilter", "testFilter", "Yes,No", "1,0", true, true, false, "1", "fields", "testFilter"));
	
	if (isset($fields) && $fields['fieldType'] == "textField") {
		$class = "contentShow";
	} else {
		$class = "contentHide";
	}
	
	echo "<div id=\"suggest\" class=\"" . $class . "\">\n";	
	directions("Auto-suggest values", true, "Set whether or not a list of suggestions will <br />show for this field based on previous entries");
	indent(radioButton("autoSuggest", "autoSuggest", "Yes,No", "1,0", true, true, false, "1", "fields", "autoSuggest"));
	echo "</div>\n";
	directions("Display tip", true, "Display the description above in <br />a tip like this one");
	indent(radioButton("showTip", "showTip", "Yes,No", "1,0", true, true, false, "1", "fields", "showTip"));
	echo "</blockquote>";
	
	catDivider("Field Type", "three");
	echo "<blockquote>\n";
	directions("Input type", true);
	indent(dropDown("fieldType", "fieldType", "- Select -,Text Field,Text Area,Dropdown,Bullet,Checkbox", ",textField,textArea,dropDown,radio,checkbox", false, true, false, false, "fields", "fieldType", "onchange=\"triggerAddition(this.value); changeSelected(this.value);\""));
	
	if (isset($fields) && $fields['fieldType'] == "dropDown" || $fields['fieldType'] == "radio" || $fields['fieldType'] == "checkbox") {
		$class = "contentShow";
	} else {
		$class = "contentHide";
	}
	
	echo "<div id=\"addition\" class=\"" . $class . "\">";
	directions("Values", true);
	echo "<blockquote>\n<table id=\"items\">\n";
	
	if (isset($fields)) {
		$selected = unserialize($fields['selected']);
		$values = unserialize($fields['values']);
		
		for ($count = 0; $count <= sizeof($values) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\">\n";
			
			if ($fields['fieldType'] == "checkbox") {
				if (is_array($selected) && in_array($value, $selected)) { 
					echo cell(checkbox("selected[]", "selected_" . $value, false, $value, false, false, true));
				} else {
					echo cell(checkbox("selected[]", "selected_" . $value, false, $value, false));
				}
			} else {
				if (is_array($selected) && in_array($value, $selected)) { 
					echo cell(radioButton("selected[]", "selected", false, $value, false, false, false, true));
				} else {
					echo cell(radioButton("selected[]", "selected", false, $value, false, false));
				}
			}
			
			echo cell(textField("values[]", "value" . $value, false, false, false, true, false, $values[$count]));
			echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true)\"></span></td>");
			echo "</tr>\n";
		}
		
		hidden("id", "id", $value);
	} else {
		echo "<tr id=\"1\" align=\"center\">\n";
		echo cell(checkBox("selected[]", "selected_1", false, "1", false));
		echo cell(textField("values[]", "value1", false, false, false, true));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '2', true)\"></span>");
		echo "</tr>\n<tr id=\"2\" align=\"center\">\n";
		echo cell(checkBox("selected[]", "selected_2", false, "2", false));
		echo cell(textField("values[]", "value2", false, false, false, true));
		echo cell("<span class=\"action smallDelete\" onclick=\"deleteObject('items', '2', '2', true)\"></span>");
		echo "</tr>\n";
		echo hidden("id", "id", "2");
	}
	
	echo "</table>\n<p>\n";
	echo "<span class=\"smallAdd\" onclick=\"addValue('items')\">Add Another Item</span>\n";
	echo "</p>\n</div>\n</blockquote>";
	
	catDivider("Field Location", "four");
	echo "<blockquote>";
	directions("Display field within", true, "Set where this field will display:<br /><br /><strong>Lesson Settings</strong> - The lesson settings portion of the learning unit generator<br /><strong>Question Generator</strong> - The settings portion of each question generator");
	
	$firstValue = false;
	$secondValue = false;
	
	if (isset($fields)) {
		if (in_array("Lesson Settings", unserialize($fields['section']))) {
			$firstValue = true;
		}
		
		if (in_array("Question Generator", unserialize($fields['section']))) {
			$secondValue = true;
		}
	}
	
	indent(checkBox("section[]", "section_0", "Lesson Settings", "Lesson Settings", true, "1", $firstValue) . "<br />
	" . checkBox("section[]", "section_1", "Question Generator", "Question Generator", true, "1", $secondValue));
	echo "</blockquote>";	
	
	catDivider("Submit", "five");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>