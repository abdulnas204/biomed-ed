<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this step has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			//case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//If the page is updating an item
	if (isset ($_GET['id'])) {
		$update = $_GET['id'];
		$currentModule = str_replace(" ", "", $_SESSION['currentModule']);
		$pageDataGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentModule}` WHERE id = '{$update}'", $connDBA);
		
		if (isset ($_GET['type'])) {
			switch($_GET['type']) {
				case "custom" : $type = "Custom Content"; break;
				case "embedded" : $type = "Embedded Content"; break;
			}
		
			if ($pageDataCheck = mysql_fetch_array($pageDataGrabber)) {
				if ($pageDataCheck['type'] == $type) {
					$pageData = $pageDataCheck;
				} else {
					header("Location: lesson_content.php");
					exit;
				}
			} else {
				header("Location: lesson_content.php");
				exit;
			}
		} else {
			header("Location: lesson_content.php");
			exit;
		}
	}
?>
<?php
//Process the type form
	if (isset ($_POST['submitType']) && !empty ($_POST['type'])) {
		$type = $_POST['type'];
		$currentTable = str_replace(" ","", $_SESSION['currentModule']);
		$lastPageGrabber = mysql_query("SELECT * FROM `moduledata_'{$currentTable}'` ORDER BY `position` ASC LIMIT 1", $connDBA);
		$lastPageArray = mysql_fetch_array($lastPageGrabber);
		$lastPage = $lastPageArray['position']+1;
		
		switch ($type) {
			case "Custom Content" : $redirect = "custom"; break;
			case "Embedded Content" : $redirect = "embedded"; break;
		}
		
		mysql_query("INSERT INTO `modulelesson_'{$currentTable}'` (
					`id`, `position`, `type`, `title`, `content`, `attachment`, `comments`
					) VALUES (
					NULL, '{$lastPage}', 'Custom Content', '', '', '', ''
					)");
					
		header ("Location: manage_content.php?type=" . $redirect);
		exit;
	}
?>
<?php
//Process the custom content form
	if (isset($_GET['type']) && $_GET['type'] == "custom" && isset ($_POST['submitCustom']) && !empty($_POST['customTitle']) && !empty($_POST['content'])) {		
	//If the page is being updated
		if (isset ($_GET['id'])) {
			$currentModule = str_replace(" ", "", $_SESSION['currentModule']);
			$id = $_GET['id'];
			$title = mysql_real_escape_string($_POST['customTitle']);
			$content = mysql_real_escape_string($_POST['content']);
			
			$editPageQuery = "UPDATE `modulelesson_{$currentModule}` SET `title` = '{$title}', `content` = '{$content}' WHERE `id` = '{$id}'";
							
			mysql_query($editPageQuery, $connDBA);
			header("Location: lesson_content.php?updated=custom");
			exit;
	//If the page is being inserted	
		} else {
			$currentModule = str_replace(" ", "", $_SESSION['currentModule']);
			$title = mysql_real_escape_string($_POST['customTitle']);
			$content = mysql_real_escape_string($_POST['content']);
			$lastPageGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentModule}` ORDER BY `position` DESC LIMIT 1");
			$lastPageArray = mysql_fetch_array($lastPageGrabber);
			$lastPage = $lastPageArray['position']+1;
			
			$newPageQuery = "INSERT INTO `modulelesson_{$currentModule}` (
							`id`, `position`, `type`, `title`, `content`, `attachment`, `comments`
							) VALUES (
							NULL, '{$lastPage}', 'Custom Content', '{$title}', '{$content}', '', ''
							)";
							
			mysql_query($newPageQuery, $connDBA);
			header("Location: lesson_content.php?inserted=custom");
			exit;
		}
	}
?>
<?php
//Process the embedded content form
	if (isset($_GET['type']) && $_GET['type'] == "embedded" && isset ($_POST['submitEmbedded']) && !empty($_POST['embeddedTitle'])) {
		$currentModule = str_replace(" ", "", $_SESSION['currentModule']);
		
	//Create a directory based off the current session
		//First strip any spaces from the session name for use as directory name
		$location = str_replace(" ", "", $_SESSION['currentModule']);
		
		//Now make the directory, if necessary
		if(!file_exists("../../../modules/{$location}")) {
			mkdir("../../../modules/{$location}", 0777);
		}
		
		if(!file_exists("../../../modules/{$location}/lesson")) {
			mkdir("../../../modules/{$location}/lesson", 0777);
		}
		
		//Prepare the directory string for future use
		$directory = "../../../modules/{$location}/lesson";
	
	//Grab the uploaded file
		$tempFile = $_FILES['file'] ['tmp_name'];
		$targetFile = basename($_FILES['file'] ['name']);
		$uploadDir = $directory;
		
	//Check to see if a current file URL exists
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$fileGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentModule}` WHERE `id` = '{$id}'", $connDBA);
			if ($fileCheck = mysql_fetch_array($fileGrabber)) {
				$file = $fileCheck;
			}
		}
	
	//If the file is supported, then move it to its final destination
		if (extension ($targetFile) == "pdf" || extension ($targetFile) == "doc" || extension ($targetFile) == "docx" || extension ($targetFile) == "ppt" || extension ($targetFile) == "pptx" || extension ($targetFile) == "xls" || extension ($targetFile) == "xlsx" || extension ($targetFile) == "txt" || extension ($targetFile) == "rtf" || extension ($targetFile) == "wav" || extension ($targetFile) == "mp3" || extension ($targetFile) == "avi" || extension ($targetFile) == "wmv" || extension ($targetFile) == "flv" || extension ($targetFile) == "mp4" || extension ($targetFile) == "mov" || extension ($targetFile) == "swf") {
			
		//Move the uploaded file
			move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile);
		
		//Provide lesson link in database
			if (isset($_GET['id'])) {
				$id = $_GET['id'];
				$title = mysql_real_escape_string($_POST['embeddedTitle']);
				$comments = mysql_real_escape_string($_POST['comments']);
				$oldFileGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentModule}` WHERE `id` = '{$id}'", $connDBA);
				$oldFile = mysql_fetch_array($oldFileGrabber);
				
			//Delete the old file
				unlink("../../../modules/{$location}/lesson/{$oldFile['attachment']}");
				
				$lessonQuery = "UPDATE `modulelesson_{$currentModule}` SET `title` = '{$title}', `comments` = '{$comments}', `attachment` = '{$targetFile}' WHERE `id` = '{$id}'";
				
				//Execute command on database			
				$lessonQueryResult = mysql_query($lessonQuery, $connDBA);	
				
				header ("Location: lesson_content.php?updated=embedded");
				exit;
			} else {
				$title = mysql_real_escape_string($_POST['embeddedTitle']);
				$comments = mysql_real_escape_string($_POST['comments']);
				$lastPageGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentModule}` ORDER BY `position` DESC LIMIT 1");
				$lastPageArray = mysql_fetch_array($lastPageGrabber);
				$lastPage = $lastPageArray['position']+1;
				
				$lessonQuery = "INSERT INTO `modulelesson_{$currentModule}` (
							`id`, `position`, `type`, `title`, `content`, `attachment`, `comments`
							) VALUES (
							NULL, '{$lastPage}', 'Embedded Content', '{$title}', '', '{$targetFile}', '{$comments}'
							)";
							
				//Execute command on database			
				$lessonQueryResult = mysql_query($lessonQuery, $connDBA);	
				
				header ("Location: lesson_content.php?inserted=embedded");
				exit;
			}			
	//If the file field is empty, then provide an error
		} elseif (extension ($targetFile) == "" && !isset ($file) && $file['attachment'] == "") {
			if (isset($_GET['id'])) {
				header ("Location: manage_content.php?type=embedded&id=" . $id . "&error=empty");
				exit;
			} else {
				header ("Location: manage_content.php?type=embedded&error=empty");
				exit;
			}
	//If the file field is empty, but a file exists in its place, then do not prompt the user to upload a file
		} elseif (extension ($targetFile) == "" && isset ($file) && $file['attachment'] !== "") {
			$id = $_GET['id'];
			$title = mysql_real_escape_string($_POST['embeddedTitle']);
			$comments = mysql_real_escape_string($_POST['comments']);
				
			$lessonQuery = "UPDATE `modulelesson_{$currentModule}` SET `title` = '{$title}', `comments` = '{$comments}' WHERE `id` = '{$id}'";
			
			//Execute command on database			
			$lessonQueryResult = mysql_query($lessonQuery, $connDBA);	
			
			header ("Location: lesson_content.php?updated=embedded");
			exit;
	//If the file is not supported, then provide an error	
		} else {
			if (isset($_GET['id'])) {
				header ("Location: manage_content.php?type=embedded&id=" . $id . "&error=fileType");
				exit;
			} else {
				header ("Location: manage_content.php?type=embedded&error=fileType");
				exit;
			}
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Module Content"); ?>
<?php headers(); ?>
<?php
	if (isset($_GET['type'])) {
		if ($_GET['type'] == "custom") {
			tinyMCEAdvanced();
		}
		
		if ($_GET['type'] == "embedded") {
			tinyMCESimple();
		}
	}
?>
<?php validate(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Module Setup Wizard : Module Content</h2>
<?php 
	if (!isset ($_GET['type'])) {
?>
<p>Select what kind of page you will be inserting. A <strong>custom content page</strong> is just like a regular web page, with text and images. An <strong>embedded media page</strong> will contain something, such as a video or PDF, as the main content.</p>
<p>&nbsp;</p>
<form action="manage_content.php" method="post" name="pageType" id="validate" onsubmit="return errorsOnSubmit(this);">
  <div class="catDivider one">Select Question Type</div>
<div class="stepContent">
  <blockquote>
    <p>
      <select name="type" id="type" class="validate[required]">
        <option value="" selected="selected">- Select -</option>
        <option value="Custom Content">Custom Content</option>
        <option value="Embedded Content">Embedded Content</option>
      </select>
    </p>
  </blockquote>
</div>
<div class="catDivider two">Submit</div>
<div class="stepContent">
  <blockquote>
    <p><?php submit("submitType", "Submit"); ?> 
      <input type="reset" name="resetType" id="resetType" value="Reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" />
      <input type="button" name="cancelType" id="cancelType" value="Cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" />
    </p>
    <?php formErrors(); ?>
  </blockquote>
</div>
</form>

<?php
	}
	
	if (isset ($_GET['type']) && $_GET['type'] == "custom") {
?>
<p>A custom content page is just like a regular web page, with text and images. When creating or modifying this page, think of it as a webpage or as a document.</p>
<p>&nbsp;</p>
<form action="manage_content.php?type=custom<?php if (isset ($_GET['id'])) {echo "&id=" . $_GET['id'];} ?>" method="post" name="customContent" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider one">Content</div>
<div class="stepContent">
  <blockquote>
    <p>Title<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The title of this page')" onmouseout="UnTip()" /></p>
    <blockquote>
      <p>
        <input name="customTitle" type="text" id="customTitle" size="50" class="validate[required]" autocomplete="off"<?php
		//If the page is being edited
			if (isset($update)) {
				echo " value=\"" . stripslashes(htmlentities($pageData['title'])) . "\"";
			}
		?> />
      </p>
    </blockquote>
    <p>Content<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The main content of th page')" onmouseout="UnTip()" /></p>
    <blockquote>
    <p><span id="contentCheck">
      <textarea name="content" id="content2" cols="45" rows="5" /><?php
		//If the page is being edited
			if (isset($update)) {
				echo stripslashes($pageData['content']);
			}
		?></textarea>
      <span class="textareaRequiredMsg"></span></span></p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider two">Submit</div>
<div class="stepContent">
  <blockquote>
    <p>
      <?php submit("submitCustom", "Submit"); ?>
      <input type="reset" name="resetCustom" id="resetCustom" value="Reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" />
      <input type="button" name="cancelCustom" id="cancelCustom" value="Cancel" onclick="MM_goToURL('parent','lesson_content.php');return document.MM_returnValue" />
    </p>
    <?php formErrors(); ?>
  </blockquote>
</div>
</form>
<?php
	}
	
	if (isset ($_GET['type']) && $_GET['type'] == "embedded") {
?>
<p>An embedded content page will contain something, such as a video or PDF, as the main content.</p>
<?php
//Display error messages
	if (isset ($_GET['error'])) {
		switch($_GET['error']) {
			case "empty" : errorMessage("Please upload a file"); break;
			case "fileType" : errorMessage("This is an unsupported file type. Supported types have one of the following extensions: \".PDF\", \".DOC\", \".DOCX\", \".XLS\", \".XLSX\", \".PPT\", \".PPTX\", \".TXT\", \".RTF\", \".WAV\", \".MP3\", \".AVI\", \".WMV\", \".FLV\", \".MOV\", \".MP4\", or \".SWF\"."); break;
		}
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<form action="manage_content.php?type=embedded<?php if (isset ($_GET['id'])) {echo "&id=" . $_GET['id'];} ?>" method="post" name="embeddedContent" id="validate" enctype="multipart/form-data" onsubmit="return errorsOnSubmit(this, 'false', 'file', <?php if (!isset($pageData)) {echo "'true'";} else {echo "'false'";} ?>, 'pdf.doc.docx.xls.xlsx.ppt.pptx.txt.rtf.wav.mp3.avi.wmv.flv.mov.mp4.swf');">
<div class="catDivider one">Title and Comments</div>
<div class="stepContent">
  <blockquote>
    <p>Title<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('The title of this page')" onmouseout="UnTip()" /></p>
    <blockquote>
      <p>
        <input name="embeddedTitle" type="text" id="embeddedTitle" size="50" autocomplete="off" class="validate[required]"<?php
		//If the page is being edited
			if (isset($update)) {
				echo " value=\"" . stripslashes(htmlentities($pageData['title'])) . "\"";
			}
		?> />
      </p>
    </blockquote>
    <p>Comments: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Comments can be added here to display at the bottom of the page.<br />For example, if the embedded content is pointing a user to a web link, the link can be entered here for easy access.')" onmouseout="UnTip()" /></p>
    <blockquote>
      <p>
        <textarea name="comments" id="comments" cols="45" rows="5"><?php
		//If the page is being edited
			if (isset($update)) {
				echo stripslashes($pageData['comments']);
			}
		?></textarea>
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider two">Content</div>
<div class="stepContent">
<?php errorWindow("extension", "This is an unsupported file type. Supported types have one of the following extensions: &quot;.PDF&quot;, &quot;.DOC&quot;, &quot;.DOCX&quot;, &quot;.XLS&quot;, &quot;.XLSX&quot;, &quot;.PPT&quot;, &quot;.PPTX&quot;, &quot;.TXT&quot;, &quot;.RTF&quot;, &quot;.WAV&quot;, &quot;.MP3&quot;, &quot;.AVI&quot;, &quot;.WMV&quot;, &quot;.FLV&quot;, &quot;.MOV&quot;, &quot;.MP4&quot;, or &quot;.SWF&quot;."); ?>
  <blockquote>
    <p>Upload content<?php if (!isset($pageData)) {echo "<span class=\"require\">*</span>";} ?>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Upload a file containing the lesson content of the module. Accepted file formats are:<br /><br /><strong>PDF</strong> - Adobe&reg; Acrobat Document<br /><strong>DOC or DOCX</strong> - Microsoft&reg; Word Document<br /><strong>XLS or XLSX</strong> - Microsoft&reg; Excel Spreadsheet<br /><strong>PPT or PPTX</strong> - Microsoft&reg; PowerPoint Presentation<br /><strong>TXT or RTF</strong> - Standard Text Documents<br /><strong>WAV or MP3</strong> - Sound Files<br /><strong>AVI, WMV, FLV, MOV, or MP4</strong> - Video Files<br /><strong>SWF</strong> - Adobe&reg; Flash Application<br />')" onmouseout="UnTip()" /></p>
    <blockquote>
      <?php
		//First strip any spaces from the session name for use as directory name
			if (isset($_GET['id'])) {
				$page = $_GET['id'];
				$location = str_replace(" ","", $_SESSION['currentModule']);
				$fileGrabber = mysql_query("SELECT * FROM `modulelesson_{$location}` WHERE `id` = '{$page}'");
				$file = mysql_fetch_array($fileGrabber);
			
			//Prepare the directory string for future use
				$directory = "../../../modules/{$location}/lesson";
			
				if (file_exists($directory)) {
					$lessonDirectory = opendir("../../../modules/{$location}/lesson");
					echo "<p>Current file: ";
					while ($lesson = readdir($lessonDirectory)) {
						//Leave out the "." and the ".."
						if ($lesson != "." && $lesson != ".." && $lesson != "Resource id #3" && $file['attachment'] == $lesson) {
							echo "<a href=\"../../../modules/{$location}/lesson/" . $lesson . "\" target=\"_blank\">" . $lesson . "</a><br /><strong>Note:</strong> Uploading a new file will delete the current file.<br />";
						} 
					} 
				}
			}
		?>
      <p><input name="file" type="file" id="file" size="50"<?php if (!isset($pageData)) {echo " class=\"validate[required]\"";} ?> /><br />
      Max file size: <?php echo ini_get('upload_max_filesize'); ?> </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider three">Submit</div>
<div class="stepContent">
  <blockquote>
    <p>
      <?php submit("submitEmbedded", "Submit"); ?>
      <input type="reset" name="resetEmbedded" id="resetEmbedded" value="Reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" />
      <input type="button" name="cancelEmbedded" id="cancelEmbedded" value="Cancel" onclick="MM_goToURL('parent','lesson_content.php');return document.MM_returnValue" />
    </p>
    <?php formErrors(); ?>
  </blockquote>
</div>
</form>
<?php
	} 
	
	if (isset ($_GET['type']) && $_GET['type'] !== "embedded" && $_GET['type'] !== "custom") {
		header ("Location: manage_content.php");
		exit;
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
<script type="text/javascript">
<!--
var sprytextarea1 = new Spry.Widget.ValidationTextarea("contentCheck");
//-->
</script>
</body>
</html>