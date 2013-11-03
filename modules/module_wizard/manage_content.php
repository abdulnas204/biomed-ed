<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');	
	$monitor = monitor("Module Content", "tinyMCEAdvanced,validate,uploadify");

//If the page is updating an item
	if (isset ($_GET['id'])) {
		if ($pageData = exist($monitor['lessonTable'], "id", $_GET['id'])) {
			//Do nothing
		} else {
			redirect("lesson_content.php");
		}
	}

//Process the file field form
	if (isset($_POST['file'])) {
		$targetFile = fileProcess("file", $monitor['directory'] . "lesson", false, false, $monitor['lessonTable'], "attachment", false, "error=upload", "error=fileType", array("pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "rtf", "wav", "mp3", "avi", "wmv", "flv", "mp4", "mov", "swf"));
		
		if (!isset($_GET['id'])) {
			$lastPage = lastItem($monitor['lessonTable']);
			
			mysql_query("INSERT INTO `{$monitor['lessonTable']}` (
							`id`, `position`, `title`, `content`, `attachment`
						) VALUES (
							NULL, '{$lastPage}', '{$title}', '{$content}', '{$targetFile}'
						)", $connDBA);
			
			redirect("lesson_content.php?inserted=page");
		} else {		
			mysql_query("UPDATE `{$monitor['lessonTable']}` SET `title` = '{$title}', `content` = '{$content}', `attachment` = '{$targetFile}' WHERE `id` = '{$_GET['id']}'", $connDBA);
	
			redirect("lesson_content.php?updated=page");
		}
	}

//Process the title and content
	if (isset($_POST['submit']) && isset($_POST['title']) && isset($_POST['content'])) {
		$title = mysql_real_escape_string($_POST['title']);
		$content = mysql_real_escape_string($_POST['content']);
		$targetFile = fileProcess("file", $monitor['directory'] . "lesson", false, false, $monitor['lessonTable'], "attachment", false, "error=upload", "error=fileType", array("pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "rtf", "wav", "mp3", "avi", "wmv", "flv", "mp4", "mov", "swf"));
		
		if (!isset($_GET['id'])) {
			$lastPage = lastItem($monitor['lessonTable']);
			
			mysql_query("INSERT INTO `{$monitor['lessonTable']}` (
							`id`, `position`, `title`, `content`, `attachment`
						) VALUES (
							NULL, '{$lastPage}', '{$title}', '{$content}', '{$targetFile}'
						)", $connDBA);
			
			redirect("lesson_content.php?inserted=page");
		} else {		
			mysql_query("UPDATE `{$monitor['lessonTable']}` SET `title` = '{$title}', `content` = '{$content}', `attachment` = '{$targetFile}' WHERE `id` = '{$_GET['id']}'", $connDBA);
	
			redirect("lesson_content.php?updated=page");
		}	
	}
	
//Title
	if (!isset($_GET['error'])) {
		title($monitor['title'] . "Module Content", "This page will manage the content of the pages within this lesson. The text editor below can be used to add text, images, and files to the lesson. <i>If you are using the editor to upload images or other files, ensure that they are uploaded inside the &quot;secure&quot; folder. This will prevent users from accessing these files during a test.</i> Multimedia, PDFs, and other documents can be uploaded under the &quot;Embedded Content&quot; section. These files will be secure embedded inside the content of the page, directly below the content placed inside of the text editor.");
	} else {
		title($monitor['title'] . "Module Content", "This page will manage the content of the pages within this lesson. The text editor below can be used to add text, images, and files to the lesson. <i>If you are using the editor to upload images or other files, ensure that they are uploaded inside the &quot;secure&quot; folder. This will prevent users from accessing these files during a test.</i> Multimedia, PDFs, and other documents can be uploaded under the &quot;Embedded Content&quot; section. These files will be secure embedded inside the content of the page, directly below the content placed inside of the text editor.", false);
	}
	
//Display message updates
	message("error", "fileType", "error", "This is an unsupported file type. Supported types have one of the following extensions: \".PDF\", \".DOC\", \".DOCX\", \".XLS\", \".XLSX\", \".PPT\", \".PPTX\", \".TXT\", \".RTF\", \".WAV\", \".MP3\", \".AVI\", \".WMV\", \".FLV\", \".MOV\", \".MP4\", or \".SWF\".");
	message("error", "upload", "error", "There was an error when uploading the file. Ensure you did not cancel the upload before it was finished, and be sure your file is smaller than the max file size displayed below the file field.");
	
//Embedded content form	
	form("content", "post", true);
	catDivider("Title and Content", "one", true);
	echo "<blockquote>";
	directions("Title", true, "The title of this page");
	echo "<blockquote><p>";
	textField("title", "title", false, false, false, true, false, false, "pageData", "title");
	echo "</p></blockquote>";
	directions("Content", true, "The main content of the page");
	echo "<blockquote><p>";
	textArea("content", "content1", "small", true, false, false, "pageData", "content");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Embedded Content", "two");
	echo "<blockquote>";
	directions("Upload content", false, "Upload a file containing the lesson content of the module. Accepted file formats are:<br /><br /><strong>PDF</strong> - Adobe&reg; Acrobat Document<br /><strong>DOC or DOCX</strong> - Microsoft&reg; Word Document<br /><strong>XLS or XLSX</strong> - Microsoft&reg; Excel Spreadsheet<br /><strong>PPT or PPTX</strong> - Microsoft&reg; PowerPoint Presentation<br /><strong>TXT or RTF</strong> - Standard Text Documents<br /><strong>WAV or MP3</strong> - Sound Files<br /><strong>AVI, WMV, FLV, MOV, or MP4</strong> - Video Files<br /><strong>SWF</strong> - Adobe&reg; Flash Application");
	uploadifyTrigger("file", "validate");
	echo "<blockquote><p>";
	fileUpload("file", "file", false, false, false, false, "pageData", "attachment", $monitor['gatewayPath'] . "lesson", true);
	echo "</p></blockquote></blockquote>";
	
	catDivider("Submit", "three");
	echo "<blockquote><p>";
	button("submit", "submit", "Submit", "button", false, " onclick=\"$('#file').uploadifyUpload()\"");
	button("reset", "reset", "Reset", "reset");
	button("cancel", "cancel", "Cancel", "cancel", "lesson_content.php");
	echo "</p></blockquote></blockquote>";
	closeForm(true, true);
	
//Include the footer
	footer();
?>