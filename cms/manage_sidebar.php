<?php
/*
LICENSE: See "license.php" located at the root installation

This is the page for managing the public website.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
//Check to see if the item is being edited
	if (isset ($_GET['id'])) {
		if ($itemData = exist("sidebar", "id", $_GET['id'])) {
			//Do nothing
		} else {
			redirect("sidebar.php");
		}
	}
	
	if (isset($itemData)) {
		$title = "Edit the " . prepare($itemData['title'], true) . " Box";
	} else {
		$title =  "Create a New Box";
	}
	
	headers($title, "tinyMCESimple,validate", true);
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['type']) && !empty($_POST['content'])) {
		$title = escape($_POST['title']);
		$content = escape($_POST['content']);
		$type = $_POST['type'];
			
		if (!isset ($itemData)) {			
			$position = lastItem("sidebar");
				
			query("INSERT INTO `sidebar` (
				  `id`, `position`, `visible`, `type`, `title`, `content`
				  ) VALUES (
				  NULL, '{$position}', 'on', '{$type}', '{$title}', '{$content}'
				  )");
			
			redirect("sidebar.php?added=item");
		} else {
			query("UPDATE sidebar SET type = '{$type}', title = '{$title}', content = '{$content}' WHERE `id` = '{$_GET['id']}'");
			redirect("sidebar.php?updated=item");
		}
	}
	
//Title
	$description = "Use this page to ";
	
	if (isset ($pageData)) {
		$description .= "edit the content of the &quot;<strong>" . prepare($itemData['title']) . "</strong>&quot; box.";
	} else {
		$description .= "create a new box.";
	}
	
	title($title, $description); 
	
//Sidebar form
	echo form("manageItem");
	catDivider("Settings", "one", true);
	echo "<blockquote>\n";
	directions("Title", true, "The text that will display on the top-left of each box.");
	indent(textField("title", "title", false, false, false, true, false, false, "itemData", "title"));
	directions("Type", false, "The type of content that will be displayed in the text box.<br />Different ones will be avaliable at different times, <br />depending on their current use.<br /><br /><strong>Custom Content</strong> - A box which can contain any desired content.<br /><strong>Login</strong> - A box with a pre-built form to log in a user.<br /><strong>Register</strong> - A box which will link a visitor to the site registration page.");
	indent(dropDown("type", "type", "Custom Content,Login,Register", "Custom Content,Login,Register", false, true, false, false, "itemData", "type"));
	echo "</blockquote>\n";
	
	catDivider("Content", "two");
	echo "<blockquote>\n";
	directions("Content", true, "The main content or body of the box");
	indent(textArea("content", "content1", "small", true, false, false, "itemData", "content"));
	echo "</blockquote>\n";
	
	catDivider("Finish", "three");
	formButtons();
	echo closeForm();

//Include the footer
	footer();
?>