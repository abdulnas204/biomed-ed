<?php
//Header functions
	require_once('../../Connections/connDBA.php');
	
//Check to see if the item is being edited
	if (isset ($_GET['id'])) {
		if ($itemData = exist("sidebar", "id", $_GET['id'])) {
			//Do nothing
		} else {
			header("Location: index.php");
			exit;
		}
	}
	
	if (isset($itemData)) {
		$title = "Edit the " . prepare($itemData['title'], true) . " Box";
	} else {
		$title =  "Create a New Box";
	}
	
	headers($title, "Site Administrator", "tinyMCESimple,validate,showHide", true);
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['type'])) {
		$title = mysql_real_escape_string($_POST['title']);
		$content = mysql_real_escape_string($_POST['content']);
		$type = $_POST['type'];
			
		if (!isset ($itemData)) {			
			$positionGrabber = mysql_query ("SELECT * FROM sidebar ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newItemQuery = "INSERT INTO sidebar (
								`id`, `position`, `visible`, `type`, `title`, `content`
							) VALUES (
								NULL, '{$position}', 'on', '{$type}', '{$title}', '{$content}'
							)";
			
			mysql_query($newItemQuery, $connDBA);
			header ("Location: sidebar.php?added=item");
			exit;
		} else {
			$item = $_GET['id'];
			
			mysql_query("UPDATE sidebar SET type = '{$type}', title = '{$title}', content = '{$content}' WHERE `id` = '{$item}'", $connDBA);
			header ("Location: sidebar.php?updated=item");
			exit;
		}
	}
	
//Title
	$description = "Use this page to ";
	
	if (isset ($pageData)) {
		$description .= "edit the content of the &quot;<strong>" . prepare($pageData['title']) . "</strong>&quot; box.";
	} else {
		$description .= "create a new box.";
	}
	
	title($title, $description); 
	
//Sidebar form
	form("manageItem");
	catDivider("Settings", "one", true);
	echo "<blockquote>";
	directions("Title", true, "The text that will display on the top-left of each box.");
	echo "<blockquote><p>";
	textField("title", "title", false, false, false, true, false, "itemData", "itemData", "title");
	echo "</p></blockquote>";
	directions("Type", false, "The type of content that will be displayed in the text box.<br />Different ones will be avaliable at different times, <br />depending on their current use.<br /><br /><strong>Custom Content</strong> - A box which can contain any desired content.<br /><strong>Login</strong> - A box with a pre-built form to log in a user.<br /><strong>Register</strong> - A box which will link a visitor to the site registration page.");
	echo "<blockquote><p>";
	dropDown("type", "type", "Custom Content,Login,Register", "Custom Content,Login,Register", "Custom Content", false, true, false, "itemData", "itemData", "type", " onchange=\"toggleTypeDiv(this.value);\"");
	echo "</p></blockquote></blockquote>";
	catDivider("Content", "two");
	echo "<div id=\"contentAdvanced\"";
	
	if (isset ($itemData)) {
		if ($itemData['type'] != "Login") {
			echo " class=\"contentShow\"";
		} else {
			echo " class=\"contentHide\"";
		}
	}
	
	echo "><blockquote>";
	directions("Content", false, "The main content or body of the box");
	echo "<blockquote>";
	textArea("content", "content1", "small", false, false, "itemData", "itemData", "content");
	echo "</blockquote></blockquote></div>";
	echo "<div id=\"contentMessage\"";
	
	if (isset ($itemData)) {
		if ($itemData['type'] == "Login") {
			echo " class=\"noResults contentShow\">";
		} else {
			echo " class=\"contentHide\">";
		}
	} else {
		echo " class=\"contentHide\">";
	}
	
	echo "<p>The system has filled out the rest of the needed information. No further input is needed.</p></div>";
	catDivider("Finish", "three");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "sidebar.php");
	echo "</p>";
	closeForm(true, true);

//Include the footer
	footer();
?>