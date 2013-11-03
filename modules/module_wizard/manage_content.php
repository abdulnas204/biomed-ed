<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	
	if (isset($_GET['type'])) {
		if ($_GET['type'] == "custom") {
			$functions = "tinyMCEAdvanced,validate";
		}
		
		if ($_GET['type'] == "embedded") {
			$functions = "tinyMCESimple,validate";
		}
	} else {
		$functions = "";
	}
	
	$monitor = monitor("Module Content", $functions);

//If the page is updating an item, ensure it is the type this page is desiged to handle
	if (isset ($_GET['id'])) {
		if (isset ($_GET['type'])) {
			switch($_GET['type']) {
				case "custom" : $type = "Custom Content"; break;
				case "embedded" : $type = "Embedded Content"; break;
			}
			
			if ($pageData = exist($monitor['lessonTable'], "id", $_GET['id'])) {
				if ($pageData['type'] == $type) {
					//Do nothing
				} else {
					redirect("lesson_content.php");
				}
			} else {
				redirect("lesson_content.php");
			}
		} else {
			redirect("lesson_content.php");
		}
	}

//Process the form
	if (isset($_POST['submit']) && isset($_POST['type']) && isset($_POST['title']) && isset($_POST['content'])) {
		$type = $_POST['type'];
		$title = mysql_real_escape_string($_POST['title']);
		$content = mysql_real_escape_string($_POST['content']);
		
	//Process the custom content
		if ($_POST['type'] == "Custom Content") {
			if (!isset($_GET['id'])) {
				$lastPageGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` ORDER BY `position` ASC LIMIT 1", $connDBA);
				$lastPageArray = mysql_fetch_array($lastPageGrabber);
				$lastPage = $lastPageArray['position']+1;
				
				mysql_query("INSERT INTO `{$monitor['lessonTable']}` (
							`id`, `position`, `type`, `title`, `content`, `attachment`
							) VALUES (
							NULL, '{$lastPage}', 'Custom Content', '{$title}', '{$content}', ''
							)", $connDBA);
							
				redirect("lesson_content.php?inserted=custom");		
			} else {
				$id = $_GET['id'];
				mysql_query("UPDATE `{$monitor['lessonTable']}` SET `title` = '{$title}', `content` = '{$content}' WHERE `id` = '{$id}'", $connDBA);
				
				redirect("lesson_content.php?updated=custom");	
			}
	//Process the embedded content
		} else {
			if (isset($_GET['id'])) {
				$id = $_GET['id'];
				$oldFileGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` WHERE `id` = '{$id}' AND `type` = 'Embedded Content'", $connDBA);
				$oldFile = mysql_fetch_array($oldFileGrabber);
			}
					
			if (is_uploaded_file($_FILES['file'] ['tmp_name'])) {
				$tempFile = $_FILES['file'] ['tmp_name'];
				$targetFile = basename($_FILES['file'] ['name']);
				$uploadDir = $monitor['directory'] . "lesson";
				$fileNameArray = explode(".", $targetFile);
				$targetFile = "";
				
				for ($count = 0; $count <= sizeof($fileNameArray) - 1; $count++) {
					if ($count == sizeof($fileNameArray) - 2) {
						$targetFile .= $fileNameArray[$count] . " " . randomValue(10, "alphanum") . ".";
					} elseif($count == sizeof($fileNameArray) - 1) {
						$targetFile .= $fileNameArray[$count];
					} else {
						$targetFile .= $fileNameArray[$count] . ".";
					}
				}
				
				$allowedFiles = array("pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "rtf", "wav", "mp3", "avi", "wmv", "flv", "mp4", "mov", "swf");
				
				if (in_array(extension($targetFile), $allowedFiles)) {
					if (move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile)) {
						if (!isset($_GET['id'])) {					
							$lastPageGrabber = mysql_query("SELECT * FROM `{$monitor['lessonTable']}` ORDER BY `position` ASC LIMIT 1", $connDBA);
							$lastPageArray = mysql_fetch_array($lastPageGrabber);
							$lastPage = $lastPageArray['position']+1;
							
							mysql_query("INSERT INTO `{$monitor['lessonTable']}` (
										`id`, `position`, `type`, `title`, `content`, `attachment`
										) VALUES (
										NULL, '{$lastPage}', 'Embedded Content', '{$title}', '{$content}', '{$targetFile}'
										)", $connDBA);
										
							redirect("lesson_content.php?inserted=embedded");
						} else {
							unlink($uploadDir . "/" . $oldFile['attachment']);	
							
							mysql_query("UPDATE `{$monitor['lessonTable']}` SET `title` = '{$title}', `content` = '{$content}', `attachment` = '{$targetFile}' WHERE `id` = '{$id}'", $connDBA);
					
							redirect("lesson_content.php?updated=embedded");
						}
					} else {
						redirect($_SERVER['REQUEST_URI'] . "&error=upload");
					}
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&error=fileType");
				}
			} else {
				if (isset($oldFile)) {
					mysql_query("UPDATE `{$monitor['lessonTable']}` SET `title` = '{$title}', `content` = '{$content}' WHERE `id` = '{$id}'", $connDBA);
					
					redirect("lesson_content.php?updated=embedded");
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&error=empty");
				}
			}
		}
	}
	
//If no content types are defined
	if (!isset ($_GET['type'])) {
	//Title
		title($monitor['title'] . "Module Content", "Select what kind of page you will be inserting. A <strong>custom content page</strong> is just like a regular web page, with text and images. An <strong>embedded content page</strong> will contain something, such as a video or PDF, as the main content.");
		
	//Selection form
		form("pageType", "post", true);
		catDivider("Select Question Type", "one", true);
		echo "<blockquote><p>";
		dropDown("type", "type", "- Select -,Custom Content,Embedded Content",",manage_content.php?type=custom,manage_content.php?type=embedded", false, true);
		echo "</p></blockquote>";
		catDivider("Submit", "two");
		echo "<blockquote><p>";
		button("submit", "submit", "Submit", "button", false, " onclick=\"location=document.pageType.type.options[document.pageType.type.selectedIndex].value;\"");
		button("cancel", "cancel", "Cancel", "cancel", "lesson_content.php");
		echo "</p></blockquote>";
		closeForm(true, true);
	} else {
	//Custom content
		if ($_GET['type'] == "custom") {
		//Title
			title($monitor['title'] . "Module Content", "A custom content page is just like a regular web page, with text and images. When creating or modifying this page, think of it as a webpage or as a document.");
			
		//Custom content form
			form("customContent");
			hidden("type", "type", "Custom Content");
			catDivider("Content", "one", true);
			echo "<blockquote>";
			directions("Title", true, "The title of this page");
			echo "<blockquote><p>";
			textField("title", "title", false, false, false, true, false, false, "pageData", "title");
			echo "</p></blockquote>";
			directions("Content", true, "The main content of the page");
			echo "<blockquote><p>";
			textArea("content", "content1", "large", true, false, false, "pageData", "content");
			echo "</p></blockquote></blockquote>";
			
			catDivider("Submit", "two");
			echo "<blockquote><p>";
			button("submit", "submit", "Submit", "submit");
			button("reset", "reset", "Reset", "reset");
			button("cancel", "cancel", "Cancel", "cancel", "lesson_content.php");
			echo "</p></blockquote></blockquote>";
			closeForm(true, true);
	//Embedded content
		} elseif ($_GET['type'] == "embedded") {
		//Title
			title($monitor['title'] . "Module Content", "An embedded content page will contain something, such as a video or PDF, as the main content.");
			
		//Display message updates
			message("error", "empty", "error", "Please upload a file");
			message("error", "fileType", "error", "This is an unsupported file type. Supported types have one of the following extensions: \".PDF\", \".DOC\", \".DOCX\", \".XLS\", \".XLSX\", \".PPT\", \".PPTX\", \".TXT\", \".RTF\", \".WAV\", \".MP3\", \".AVI\", \".WMV\", \".FLV\", \".MOV\", \".MP4\", or \".SWF\".");
			message("error", "upload", "error", "There was an error when uploading the file. Ensure you did not cancel the upload before it was finished, and be sure your file is smaller than the max file size displayed below the file field.");
			
		//Embedded content form
			if (!isset($pageData)) {
				$required = "true";
			} else {
				$required = "false";
			}
			
			form("embeddedContent", "post", true, true, false, " return errorsOnSubmit(this, 'file', '" . $required . "', 'pdf.doc.docx.xls.xlsx.ppt.pptx.txt.rtf.wav.mp3.avi.wmv.flv.mov.mp4.swf');");
			hidden("type", "type", "Embedded Content");
			catDivider("Title and Comments", "one", true);
			echo "<blockquote>";
			directions("Title", true, "The title of this page");
			echo "<blockquote><p>";
			textField("title", "title", false, false, false, true, false, false, "pageData", "title");
			echo "</p></blockquote>";
			directions("Content", true, "The main content of the page");
			echo "<blockquote><p>";
			textArea("content", "content1", "small", true, false, false, "pageData", "content");
			echo "</p></blockquote></blockquote>";
			
			catDivider("Content", "two");
			echo "<blockquote>";
			directions("Upload content", true, "Upload a file containing the lesson content of the module. Accepted file formats are:<br /><br /><strong>PDF</strong> - Adobe&reg; Acrobat Document<br /><strong>DOC or DOCX</strong> - Microsoft&reg; Word Document<br /><strong>XLS or XLSX</strong> - Microsoft&reg; Excel Spreadsheet<br /><strong>PPT or PPTX</strong> - Microsoft&reg; PowerPoint Presentation<br /><strong>TXT or RTF</strong> - Standard Text Documents<br /><strong>WAV or MP3</strong> - Sound Files<br /><strong>AVI, WMV, FLV, MOV, or MP4</strong> - Video Files<br /><strong>SWF</strong> - Adobe&reg; Flash Application");
			echo "<blockquote><p>";
			
			if (!isset($_GET['id'])) {
				fileUpload("file", "file", false, true, false, "pageData", "attachment", $monitor['gatewayPath'] . "/lesson", true);
			} else {
				fileUpload("file", "file", false, false, false, "pageData", "attachment", $monitor['gatewayPath'] . "/lesson", true);
			}
			
			echo "</p></blockquote></blockquote>";
			
			catDivider("Submit", "three");
			echo "<blockquote><p>";
			button("submit", "submit", "Submit", "submit");
			button("reset", "reset", "Reset", "reset");
			button("cancel", "cancel", "Cancel", "cancel", "lesson_content.php");
			echo "</p></blockquote></blockquote>";
			closeForm(true, true);
		} else {
			redirect("manage_content.php");
		}
	}
	
//Include the footer
	footer();
?>