<?php
/*
LICENSE: See "license.php" located at the root installation

This is the page for managing the public website.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
//Check to see if the page is being edited
	if (isset ($_GET['id'])) {
		if ($pageData = exist("pages", "id", $_GET['id'])) {
			//Do nothing
		} else {
			redirect("index.php");
		}
	}
	
	if (isset($pageData)) {
		$title = "Edit the " . prepare($pageData['title'], false, true) . " Page";
	} else {
		$title =  "Create a New Page";
	}
	
	headers($title, "tinyMCEAdvanced,validate", true);
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['content'])) {	
		$title = escape($_POST['title']);
		$content = escape($_POST['content']);
		
		if (!isset ($pageData)) {
			$position = lastItem("pages");
			
			query("INSERT INTO `pages` (
				  `id`, `title`, `visible`, `position`, `content`
				  ) VALUES (
				  NULL, '{$title}', 'on', '{$position}', '{$content}'
				  )");
				  
			redirect ("index.php?added=page");
		} else {
			query("UPDATE `pages` SET title = '{$title}', content = '{$content}' WHERE `id` = '{$_GET['id']}'");
			redirect ("index.php?updated=page");
		}
	} 
	
//Title
	$description = "Use this page to ";
	
	if (isset ($pageData)) {
		$description .= "edit the content of &quot;<strong>" . prepare($pageData['title']) . "</strong>&quot;.";
	} else {
		$description .= "create a new page.";
	}
	
	title($title, $description);
	
//Pages form
	echo form("managePage");
	catDivider("Content", "one", true);
	echo "<blockquote>\n";
	directions("Title", true, "The text that will display in big letters on the top-left of each page <br />and at the top of the browser window.");
	indent(textField("title", "title", false, false, false, true, false, false, "pageData", "title"));
	directions("Content", true, "The main content or body of the webpage");
	indent(textArea("content", "content1", "large", true, false, false, "pageData", "content"));
	echo "</blockquote>\n";
	
	catDivider("Content", "two");
	formButtons();
	echo closeForm();

//Include the footer
	footer();
?>