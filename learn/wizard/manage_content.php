<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: November 4th, 2010
Last updated: February 9th, 2010

This is the lesson content management page for the learning 
unit generator.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");	
	$monitor = monitor("Page Content", "tinyMCEAdvanced,validate");

//Grab the page data
	if (isset ($_GET['id'])) {
		if (exist($monitor['lessonTable'], "id", $_GET['id'])) {
			$pageData = query("SELECT * FROM `{$monitor['lessonTable']}` WHERE `id` = '{$_GET['id']}'");
		} else {
			redirect("lesson_content.php");
		}
	}

//Process form
	if (isset($_POST['submit']) && isset($_POST['title']) && isset($_POST['content'])) {
		$title = escape($_POST['title']);
		$content = escape($_POST['content']);
		$targetFile = fileProcess("file", $monitor['directory'] . "lesson", false, false, $monitor['lessonTable'], "attachment", false, "error=upload", "error=fileType", array("pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "rtf", "wav", "mp3", "avi", "wmv", "flv", "mp4", "mov", "swf"));
		
		if (!isset($_GET['id'])) {
			$lastPage = lastItem($monitor['lessonTable']);
			
			query("INSERT INTO `{$monitor['lessonTable']}` (
				  `id`, `position`, `title`, `content`, `attachment`
				  ) VALUES (
				  NULL, '{$lastPage}', '{$title}', '{$content}', '{$targetFile}'
				  )");
			
			redirect("lesson_content.php?inserted=page");
		} else {		
			query("UPDATE `{$monitor['lessonTable']}` SET `title` = '{$title}', `content` = '{$content}', `attachment` = '{$targetFile}' WHERE `id` = '{$_GET['id']}'");
	
			redirect("lesson_content.php?updated=page");
		}	
	}
	
//Title
	title($monitor['title'] . "Page Content", "This page will manage the content of the pages within this lesson. The text editor below can be used to add text, images, and files to the lesson. <i>If you are using the editor to upload images or other files, ensure that they are uploaded inside the &quot;secure&quot; folder. This will prevent users from accessing these files when they are not permitted.</i> Multimedia, PDFs, and other documents can be uploaded under the &quot;Embedded Content&quot; section. These files will be securely embedded inside the content of the page, directly below the content placed inside of the text editor.", "error");
	
//Display message updates
	message("error", "fileType", "error", "This is an unsupported file type. Supported types have one of the following extensions: &quot;.PDF&quot;, &quot;.DOC&quot;, &quot;.DOCX&quot;, &quot;.XLS&quot;, &quot;.XLSX&quot;, &quot;.PPT&quot;, &quot;.PPTX&quot;, &quot;.TXT&quot;, &quot;.RTF&quot;, &quot;.WAV&quot;, &quot;.MP3&quot;, &quot;.AVI&quot;, &quot;.WMV&quot;, &quot;.FLV&quot;, &quot;.MOV&quot;, &quot;.MP4&quot;, or &quot;.SWF&quot;.");
	message("error", "upload", "error", "There was an error when uploading the file. Ensure you did not cancel the upload before it was finished, and be sure your file is smaller than the maxmium file size displayed below the file field.");
	
//Embedded content form	
	echo form("content", "post", true);
	catDivider("Title and Content", "one", true);
	echo "<blockquote>\n";
	directions("Title", true, "The title of this page");
	indent(textField("title", "title", false, false, false, true, false, false, "pageData", "title"));
	directions("Content", true, "The main content of the page");
	indent(textArea("content", "content1", "large", true, false, false, "pageData", "content"));
	echo "</blockquote>\n";
	
	catDivider("Embedded Content", "two");
	echo "<blockquote>\n";
	directions("Upload content", false, "Upload a file containing the lesson content of this page. Accepted file formats are:<br /><br /><strong>PDF</strong> - Adobe&reg; Acrobat Document<br /><strong>DOC or DOCX</strong> - Microsoft&reg; Word Document<br /><strong>XLS or XLSX</strong> - Microsoft&reg; Excel Spreadsheet<br /><strong>PPT or PPTX</strong> - Microsoft&reg; PowerPoint Presentation<br /><strong>TXT or RTF</strong> - Standard Text Documents<br /><strong>WAV or MP3</strong> - Sound Files<br /><strong>AVI, WMV, FLV, MOV, or MP4</strong> - Video Files<br /><strong>SWF</strong> - Adobe&reg; Flash Application");
	indent(fileUpload("file", "file", false, false, "funcCall[uploadCheck]", false, "pageData", "attachment", $monitor['gatewayPath'] . "lesson", true));
	echo "</blockquote>\n";
	
	catDivider("Submit", "three");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>