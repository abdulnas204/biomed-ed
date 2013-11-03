<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	headers("Developer Administration: Manage Field", "Site Administrator", "tinyMCESimple,validate,administrativeLibrary", true);
	developerAccess();
	
//Title
	title("Manage Field", "The page will manage individual fields, and their settings.");

//Fields form
	form("fields");
	catDivider("Field Name", "one", true);
	echo "<blockquote>";
	directions("Field name", true, "The name which will display above the field, <br />like the text at left");
	echo "<blockquote><p>";
	textField("name", "name");
	echo "</p></blockquote>";
	directions("Description", true, "A description used for back-end <br />notes, or as a helpful tip to users");
	echo "<blockquote><p>";
	textArea("description", "description", "small");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Field Settings", "two");
	echo "<blockquote>";
	directions("Required", true, "Set whether or not this field requires entry");
	echo "<blockquote><p>";
	radioButton("reqiure", "reqiure", "Yes,No", "1,0", true, false, false, "1");
	echo "</p></blockquote>";
	directions("Test builder filter", true, "This option will show as a filter when a user or <br />instructor is generating a customized test for a student");
	echo "<blockquote><p>";
	radioButton("testFilter", "testFilter", "Yes,No", "1,0", true, false, false, "1");
	echo "</p></blockquote>";
	echo "<div id=\"suggest\" class=\"contentHide\">";
	directions("Auto-suggest values", true, "Set whether or not a list of suggestions will <br />show for this field based on previous entries");
	echo "<blockquote><p>";
	radioButton("autoSuggest", "autoSuggest", "Yes,No", "1,0", true, false, false, "1");
	echo "</p></blockquote></div>";
	directions("Display tip", true, "Display the description above in <br />a tip like this one");
	echo "<blockquote><p>";
	radioButton("showTip", "showTip", "Yes,No", "1,0", true, false, false, "1");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Field Type", "three");
	echo "<blockquote>";
	directions("Input type", true);
	echo "<blockquote><p>";
	dropDown("fieldType", "fieldType", "- Select -,Text Field,Text Area,Dropdown,Bullet,Checkbox", ",textField,textArea,dropDown,radio,checkbox", false, true, false, false, false, false, " onchange=\"triggerAddition(this.value)\"");
	echo "</p></blockquote>";
	echo "<div id=\"addition\" class=\"contentHide\">";
	directions("Values", true);
	echo "<blockquote><table id=\"items\">";
	
	if (isset($fieldData)) {
		$values = unserialize($questionData['questionValue']);
		$choices = unserialize($questionData['answerValue']);
		
		for ($count = 0; $count <= sizeof($values) - 1; $count ++) {
			$value = $count + 1;
			
			echo "<tr id=\"" . $value . "\" align=\"center\"><td>";
			
			if (in_array($value, $choices)) { 
				checkbox("choices[]", "choice" . $value, false, $value, true, "1", true);
			} else {
				checkbox("choices[]", "choice" . $value, false, $value, true, "1");
			}
			
			echo "</td><td>";
			textArea("values[]", "value" . $value, "extraSmall", true, false, $values[$count], false, false, " class=\"noEditorMedia editorQuestion value" . $count . "\"");
			echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true)\"></span></td></tr>";
		}
		
		hidden("id", "id", $value);
	} else {
		echo "<tr id=\"1\" align=\"center\"><td>";
		textField("values[]", "value1", false, false, false, true);
		echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', '1', '2', true)\"></span></td></tr><tr id=\"2\" align=\"center\"><td>";
		textField("values[]", "value2", false, false, false, true);
		echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('items', '2', '2', true)\"></span></td></tr>";
		hidden("id", "id", "2");
	}
	
	echo "</table><p>";
	echo "<span class=\"smallAdd\" onclick=\"addValue('items')\">Add Another Item</span>";
	echo "</p></blockquote></div></blockquote>";
	
	catDivider("Field Location", "four");
	echo "<blockquote>";
	directions("Display field within", true, "Set where this field will display:<br /><br /><strong>Lesson Setting</strong> - The lesson settings portion of the module generator<br /><strong>Question Generator</strong> - The settings portion of each individual question generator");
	echo "<blockquote><p>";
	dropDown("section", "section", "- Select -,Lesson Settings,Question Generator", ",Lesson Settings,Question Generator", false, true);
	echo "</p></blockquote></blockquote>";	
	
	catDivider("Submit", "five");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "index.php");	
	echo "</p></blockquote>";
	closeForm(true, true);
	
//Include the footer
	footer();
?>