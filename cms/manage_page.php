<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Check to see if the page is being edited
	if (isset ($_GET['id'])) {
		if ($pageData = exist("pages", "id", $_GET['id'])) {
			//Do nothing
		} else {
			redirect("index.php");
		}
	}
	
	if (isset($pageData)) {
		$title = "Edit the " . prepare($pageData['title'], true) . " Page";
	} else {
		$title =  "Create a New Page";
	}
	
	headers($title, "Site Administrator", "tinyMCEAdvanced,validate", true);
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['content'])) {	
		$title = mysql_real_escape_string($_POST['title']);
		$content = mysql_real_escape_string($_POST['content']);
		
		if (!isset ($pageData)) {
			$positionGrabber = mysql_query ("SELECT * FROM pages ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newPageQuery = "INSERT INTO pages (
								`id`, `title`, `visible`, `position`, `content`
							) VALUES (
								NULL, '{$title}', 'on', '{$position}', '{$content}'
							)";
			
			mysql_query($newPageQuery, $connDBA);
			header ("Location: index.php?added=page");
			exit;
		} else {
			$page = $_GET['id'];
			
			mysql_query("UPDATE pages SET title = '{$title}', content = '{$content}' WHERE `id` = '{$page}'", $connDBA);
			header ("Location: index.php?updated=page");
			exit;
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
	form("managePage");
	catDivider("Content", "one", true);
	echo "<blockquote>";
	directions("Title", true, "The text that will display in big letters on the top-left of each page <br />and at the top of the browser window.");
	echo "<blockquote><p>";
	textField("title", "title", false, false, false, true, false, false, "pageData", "title");
	echo "</p></blockquote>";
	directions("Content", true, "The main content or body of the webpage");
	echo "<blockquote>";
	textArea("content", "content1", "large", true, false, false, "pageData", "content");
	echo "</blockquote></blockquote>";
	catDivider("Content", "two");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "submit");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "index.php");
	echo "</p>";
	closeForm(true, true);

//Include the footer
	footer();
?>