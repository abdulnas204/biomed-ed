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
Last updated: Novemeber 30th, 2010

This is the page which manages the learning units generator 
fields.
*/

//Header functions
	headers("Manage Field", "tinyMCESimple,validate,administrativeLibrary", true);
	
//Process the form	
	if (isset($_POST['submit']) && !empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['require']) && !empty($_POST['testFilter']) && !empty($_POST['autoSuggest']) && !empty($_POST['showTip']) && !empty($_POST['fieldType']) && !empty($_POST['section'])) {
		$name = escape($_POST['name']);
		$description = escape($_POST['description']);
		$require = $_POST['require'];
		$testFilter = $_POST['testFilter'];
		$autoSuggest = $_POST['autoSuggest'];
		$showTip = $_POST['showTip'];
		$fieldType = $_POST['fieldType'];
		$choices = escape(serialize($_POST['choices']));
		$section = escape(serialize($_POST['section']));
		
		if (!isset($form)) {
			$position = lastItem("fields");
			
			query("INSERT INTO `fields` (
				  `id`, `position`, `section`, `name`, `description`, `showTip`, `require`, `testFilter`, `autoSuggest`, `fieldType`, `values` 
				  ) VALUES (
				  NULL, '{$position}', '{$section}', '{$name}', '{$description}', '{$showTip}', '{$require}', '{$testFilter}', '{$autoSuggest}', '{$fieldType}', '{$choices}'
				  )");
				  
			redirect("index.php?message=inserted");
		}
	}
	
//Title
	title("Manage Field", "The page will manage individual fields, and their settings.");

//Fields form
	echo form("fields");
	catDivider("Field Name", "one", true);
	echo "<blockquote>\n";
	directions("Field name", true, "The name which will display above the field, <br />like the text at left");
	indent(textField("name", "name"));
	directions("Description", true, "A description used for back-end <br />notes, or as a helpful tip to users");
	indent(textArea("description", "description", "small"));
	echo "</blockquote>\n";
	
	catDivider("Field Settings", "two");
	echo "<blockquote>\n";
	directions("Required", true, "Set whether or not this field requires entry");
	indent(radioButton("require", "require", "Yes,No", "1,0", true, false, false, "1"));
	directions("Test builder filter", true, "This option will show as a filter when a user or <br />instructor is generating a customized test for a student");
	indent(radioButton("testFilter", "testFilter", "Yes,No", "1,0", true, false, false, "1"));
	echo "<div id=\"suggest\" class=\"contentHide\">\n";
	directions("Auto-suggest values", true, "Set whether or not a list of suggestions will <br />show for this field based on previous entries");
	indent(radioButton("autoSuggest", "autoSuggest", "Yes,No", "1,0", true, false, false, "1"));
	echo "</div>\n";
	directions("Display tip", true, "Display the description above in <br />a tip like this one");
	indent(radioButton("showTip", "showTip", "Yes,No", "1,0", true, false, false, "1"));
	echo "</blockquote>";
	
	catDivider("Field Type", "three");
	echo "<blockquote>\n";
	directions("Input type", true);
	indent(dropDown("fieldType", "fieldType", "- Select -,Text Field,Text Area,Dropdown,Bullet,Checkbox", ",textField,textArea,dropDown,radio,checkbox", false, true, false, false, false, false, " onchange=\"triggerAddition(this.value)\""));
	echo "<div id=\"addition\" class=\"contentHide\">";
	directions("Values", true);
	echo "<blockquote><table id=\"items\">\n";
	
	if (isset($fieldData)) {
		$values = unserialize($questionData['questionValue']);
		$choices = unserialize($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($values) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\">\n<td>";
			
			if (in_array($value, $choices)) { 
				echo checkbox("choices[]", "choice" . $value, false, $value, true, "1", true);
			} else {
				echo checkbox("choices[]", "choice" . $value, false, $value, true, "1");
			}
			
			echo "</td>\n<td>";
			echo textArea("values[]", "value" . $value, "extraSmall", true, false, $values[$count], false, false, " class=\"noEditorMedia editorQuestion value" . $count . "\"");
			echo "</td>\n<td><span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true)\"></span></td>\n</tr>\n";
		}
		
		hidden("id", "id", $value);
	} else {
		echo "<tr id=\"1\" align=\"center\">\n<td>";
		echo textField("values[]", "value1", false, false, false, true);
		echo "</td>\n<td><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '2', true)\"></span></td>\n</tr>\n<tr id=\"2\" align=\"center\">\n<td>";
		echo textField("values[]", "value2", false, false, false, true);
		echo "</td>\n<td><span class=\"action smallDelete\" onclick=\"deleteObject('items', '2', '2', true)\"></span></td>\n</tr>\n";
		echo hidden("id", "id", "2");
	}
	
	echo "</table>\n<p>\n";
	echo "<span class=\"smallAdd\" onclick=\"addValue('items')\">Add Another Item</span>\n";
	echo "</p>\n</div>\n</blockquote>";
	
	catDivider("Field Location", "four");
	echo "<blockquote>";
	directions("Display field within", true, "Set where this field will display:<br /><br /><strong>Lesson Setting</strong> - The lesson settings portion of the module generator<br /><strong>Question Generator</strong> - The settings portion of each individual question generator");
	indent(checkBox("section[]", "section_0", "Lesson Settings", "Lesson Settings", false, true) . "<br />
	" . checkBox("section[]", "section_1", "Question Generator", "Question Generator", true, "1"));
	echo "</blockquote>";	
	
	catDivider("Submit", "five");
	formButtons();
	echo closeForm(true);
	
//Include the footer
	footer();
?>