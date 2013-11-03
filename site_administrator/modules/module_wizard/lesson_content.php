<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
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
//Process the form
	if (isset ($_POST['submit']) || isset ($_POST['back'])) {
	//Create a directory based off the current session
		//First strip any spaces from the session name for use as directory name
		$location = str_replace(" ", "", $_SESSION['currentModule']);
		
		//Now make the directory
		mkdir("../../../modules/{$location}", 0777);
		mkdir("../../../modules/{$location}/lesson", 0777);
		
		//Prepare the directory string for future use
		$directory = "../../../modules/{$location}/lesson";
	
	//Grab the uploaded file
		$tempFile = $_FILES['file'] ['tmp_name'];
		$targetFile = basename($_FILES['file'] ['name']);
		$uploadDir = $directory;
	
	//Check to see if the file is supported
		function findexts ($targetFile) {
			$filename = strtolower($targetFile);
			$exts = split("[/\\.]", $targetFile);
			$n = count($exts)-1;
			$exts = $exts[$n];
			return $exts;
		}
		
	//Check to see if a current file URL exists
		$currentModule = $_SESSION['currentModule'];
		$fileGrabber = mysql_query("SELECT * FROM moduledata WHERE name = '{$currentModule}'", $connDBA);
		if ($fileGrabber) {
			$file = mysql_fetch_array($fileGrabber);
		}
	
	//If the file is supported, then move it to its final destination
		if (findexts ($targetFile) == "pdf" || findexts ($targetFile) == "doc" || findexts ($targetFile) == "docx" || findexts ($targetFile) == "ppt" || findexts ($targetFile) == "pptx" || findexts ($targetFile) == "xls" || findexts ($targetFile) == "xlsx" || findexts ($targetFile) == "txt" || findexts ($targetFile) == "rtf" || findexts ($targetFile) == "wav" || findexts ($targetFile) == "mp3" || findexts ($targetFile) == "avi" || findexts ($targetFile) == "wmv" || findexts ($targetFile) == "flv" || findexts ($targetFile) == "mp4" || findexts ($targetFile) == "mov" || findexts ($targetFile) == "swf") {
			
		//Delete any existing files
			$currentModule = $_SESSION['currentModule'];
			$fileGrabber = mysql_query("SELECT * FROM moduledata WHERE name = '{$currentModule}'", $connDBA);
			if ($fileGrabber) {
				$file = mysql_fetch_array($fileGrabber);
				
				//Delete the existing file
				$directory = "../../../modules/{$location}/lesson";
				$openDir = opendir($directory);
		
				while ($directory = readdir($openDir)) {
					if ($directory !== "." && $directory !== "..") {
						$deleteLocation = "../../../modules/{$location}/lesson/" . $directory;
						unlink($deleteLocation);
					}
				}
			}
			
		//Move the uploaded file
			move_uploaded_file($tempFile, $uploadDir . "/" . $targetFile);
		
		//Provide lesson link in database
			$lessonURL = "UPDATE moduledata SET lessonURL = '{$targetFile}' WHERE name = '{$currentModule}'";
			
			//Execute command on database			
			$lessonURLResult = mysql_query($lessonURL, $connDBA);
			
		//Update the session to manage the steps
			if (isset ($_POST['submit'])) {
				$_SESSION['step'] = "lessonVerify";
			} elseif (isset ($_POST['back'])) {
				$_SESSION['step'] = "lessonSettings";
			}		
			
			if (isset ($_SESSION['review'])) {
				header ("Location: modify.php?updated=lessonContent");
				exit;
			} else {
				if (isset ($_POST['submit'])) {
					header ("Location: lesson_verify.php");
					exit;
				} elseif (isset ($_POST['back'])) {
					header ("Location: lesson_settings.php");
					exit;
				}
			}
	//If the file field is empty, then provide an error
		} elseif (findexts ($targetFile) == "" && !isset ($file) && $file['lessonURL'] == "") {
			header ("Location: lesson_content.php?error=empty");
			exit;
	//If the file field is empty, but a file exists in its place, then do not prompt the user to upload a file
		} elseif (findexts ($targetFile) == "" && isset ($file) && $file['lessonURL'] !== "") {
		//Update the session to manage the steps
			if (isset ($_POST['submit'])) {
				$_SESSION['step'] = "lessonVerify";
			} elseif (isset ($_POST['back'])) {
				$_SESSION['step'] = "lessonSettings";
			}
			
			if (isset ($_SESSION['review'])) {
				header ("Location: modify.php?updated=lessonContent");
				exit;
			} else {
				if (isset ($_POST['submit'])) {
					header ("Location: lesson_verify.php");
					exit;
				} elseif (isset ($_POST['back'])) {
					header ("Location: lesson_settings.php");
					exit;
				}
			}
	//If the file is not supported, then provide an error	
		} else {
			header ("Location: lesson_content.php?error=fileType");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Module Content"); ?>
<?php headers(); ?>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../javascripts/insert/newFileUpload.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../javascripts/common/loaderProgress.js"></script>
<?php validate(); ?>
</head>
<body onload="MM_showHideLayers('progress','','hide')"<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
    <h2>Module Setup Wizard : Module Content</h2>
<p>Content may be added by uploading a file. 
	<?php
	//Display an error if the file is not supported
		if (isset ($_GET['error']) && $_GET['error'] == "fileType") {
			echo errorMessage("The uploaded file must be in \".pdf\", \".doc\", \".docx\", \".ppt\", \".pptx\", \".xls\", \".xlsx\", \".txt\", \".rtf\", \".wav\", \".mp3\", \".avi\", \".wmv\", \".flv\", \".mp4\", \".mov\", or \".swf\"  format.");
		} else {
			echo "<p>&nbsp;</p>";
		}
		
	//Display an error if the file field is empty
		if (isset ($_GET['error']) && $_GET['error'] == "empty") {
			echo errorMessage("Please upload a file.");
		}
	?>
</p>
<form name="content" action="lesson_content.php" method="post" enctype="multipart/form-data" onsubmit="MM_showHideLayers('progress','','show'); return errorsOnSubmit(this);" id="validate">
<div class="catDivider">
<?php
	step("4", "Add Content", "1" , "Add Content")
?>
</div>
<div class="stepContent">
<blockquote>
      <p>Upload content<span class="require">*</span>: <img src="../../../images/admin_icons/help.png" alt="Help" width="16" height="16" onmouseover="Tip('Upload a file containing the lesson content of the module. Accepted file formats are:<br /><br /><strong>PDF</strong> - Adobe&reg; Acrobat Document<br /><strong>DOC or DOCX</strong> - Microsoft&reg; Word Document<br /><strong>XLS or XLSX</strong> - Microsoft&reg; Excel Spreadsheet<br /><strong>PPT or PPTX</strong> - Microsoft&reg; PowerPoint Presentation<br /><strong>TXT or RTF</strong> - Standard Text Documents<br /><strong>WAV or MP3</strong> - Sound Files<br /><strong>AVI, WMV, FLV, MOV, or MP4</strong> - Video Files<br /><strong>SWF</strong> - Adobe&reg; Flash Application<br />')" onmouseout="UnTip()" /></p>
      <blockquote>
        <?php
		//First strip any spaces from the session name for use as directory name
			$location = str_replace(" ","", $_SESSION['currentModule']);
			
		//Prepare the directory string for future use
			$directory = "../../../modules/{$location}/lesson";
		
			if (file_exists($directory)) {
				$moduleDirectory = opendir("../../../modules/{$location}/lesson");
				echo "<p>Current file: ";
				while ($module = readdir($moduleDirectory)) {
					//Leave out the "." and the ".."
					if (($module != ".") && ($module != "..") && ($module != "Resource id #3")) {
						echo "<a href=\"../../../modules/{$location}/lesson/" . $module . "\" target=\"_blank\">" . $module . "</a> <a href=\"lesson_content.php?action=delete&file=" . urlencode($module) . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\"><img src=\"../../../images/common/x.png\" border=\"0\" alt=\"Delete file\"></a>";
					} 
				} 
			}
		?>
        <table width="100%" border="0" id="files">
          <tr>
            <td>
              <input name="file1" type="file" id="file1" size="50" class="validate[required]" />
            </td>
          </tr>
        </table>
        <p>Max file size: <?php echo ini_get('upload_max_filesize'); ?>
        </p>
        <p>
          <input value="Add Another File" type="button" onclick="appendRow('files', '<input name=\'file', '\' type=\'file\' id=\'file', '\' size=\'50\' class=\'validate[required]\' />')" />
          <input value="Remove Last File" type="button" onclick="deleteLastRow('files')" />
        </p>
        <div id="progress">
          <p><span class="require">Uploading in progress... </span><img src="../../../images/common/loading.gif" alt="Uploading" width="16" height="16" /></p>
        </div>
      </blockquote>
    </blockquote>
</div>
<div class="catDivider">
<?php
	step("5", "Submit", "2" , "Submit")
?>
</div>
<div class="stepContent">
<blockquote>
<?php
//Selectively display the buttons
	if (isset ($_SESSION['review'])) {
		submit("submit", "Modify Content");
		echo "<input type=\"button\" name=\"cancel\" id=\"cancel\" value=\"Cancel\" onclick=\"MM_goToURL('parent','modify.php');return document.MM_returnValue\" />";
	} else {
		submit("back", "&lt;&lt; Previous Step");
		submit("submit", "Next Step &gt;&gt;");
	}
?>
<?php formErrors(); ?>
</blockquote>
</div>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>