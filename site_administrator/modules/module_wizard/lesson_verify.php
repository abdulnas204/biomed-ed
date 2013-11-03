<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Restrict access to this page, if this is not has not yet been reached in the module setup
	if (isset ($_SESSION['step']) && !isset ($_SESSION['review'])) {
		switch ($_SESSION['step']) {
			case "lessonSettings" : header ("Location: lesson_settings.php"); exit; break;
			case "lessonContent" : header ("Location: lesson_content.php"); exit; break;
			//case "lessonVerify" : header ("Location: lesson_verify.php"); exit; break;
			case "testCheck" : header ("Location: test_check.php"); exit; break;
			case "testSettings" : header ("Location: test_settings.php"); exit; break;
			case "testContent" : header ("Location: test_content.php"); exit; break;
			case "testVerify" : header ("Location: test_verify.php"); exit; break;
		}
	} elseif (isset ($_SESSION['review'])) {
		header ("Location: modify.php");
		exit;
	} else {
		header ("Location: ../index.php");
		exit;
	}
?>
<?php
//Grab all of the lesson data
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$currentTable = str_replace(" ", "", $_SESSION['currentModule']);
		$lessonGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentTable}` WHERE `position` = '{$page}'", $connDBA);
		if ($lesson = mysql_fetch_array($lessonGrabber)) {
			$back = $lesson['position']-1;
			$previousPageGrabber =  mysql_query("SELECT * FROM `modulelesson_{$currentTable}` WHERE `position` = '{$back}'", $connDBA);
			if ($previousPageCheck = mysql_fetch_array($previousPageGrabber)) {
				$previousPage = $previousPageCheck;
			}
			
			$next = $lesson['position']+1;
			$nextPageGrabber =  mysql_query("SELECT * FROM `modulelesson_{$currentTable}` WHERE `position` = '{$next}'", $connDBA);
			if ($nextPageCheck = mysql_fetch_array($nextPageGrabber)) {
				$nextPage = $nextPageCheck;
			}
			
			$lastPageGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentTable}` ORDER BY `position` DESC LIMIT 1", $connDBA);
			$lastPageCheck = mysql_fetch_array($lastPageGrabber);
		} else {
			header ("Location:lesson_verify.php?page=1");
			exit;
		}
	} else {
		header("Location:lesson_verify.php?page=1");
		exit;
	}
?>
<?php
//Update a session to go to previous or next steps
	if (isset ($_POST['back'])) {
		$_SESSION['step'] = "lessonContent";
		header ("Location: lesson_content.php");
		exit;
	}
	
	if (isset ($_POST['next'])) {
	//Check to see if a test exists, and set a session accordingly
		$name = $_SESSION['currentModule'];
		$testCheckGrabber = mysql_query("SELECT * FROM moduleData WHERE `name` = '{$name}'", $connDBA);
		$testCheckArray = mysql_fetch_array($testCheckGrabber);
		
		if ($testCheckArray['test'] == "1") {
			$_SESSION['step'] = "testSettings";
			header ("Location: test_settings.php");
			exit;
		} elseif ($testCheckArray['test'] == "0") {
			$_SESSION['step'] = "testCheck";
			header ("Location: test_check.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Setup Wizard : Verify Content"); ?>
<?php headers(); ?>
<?php validate(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Module Setup Wizard : Verify Content</h2>
<p>Content may be reviewed in the section below. Changes can be made to the lesson by clicking the &quot;Make Changes&quot; button.</p>
<p>&nbsp;</p>
<div class="catDivider">
<?php
	step("4", "Verify Module Content", "4" , "Verify Module Content")
?>
</div>
<form name="lessonContent" action="lesson_verify.php<?php if (isset($_GET['page'])) {echo "?page=" . $_GET['page'];} ?>" method="post" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="stepContent">
<blockquote>
<?php
//Display the lesson
	if (isset ($_SESSION['currentModule'])) {
	//Display the navigation
		echo "<div class=\"layoutControl\">";
		if (isset($previousPage)) {
			echo "<div class=\"contentLeft\"><div align=\"left\"><a href=\"lesson_verify.php?page=" . $previousPage['position'] . "\">&lt;&lt; Previous Page<br /><strong>" . $previousPage['title'] . "</strong></a></div></div>";
		}
		
		if (isset($nextPage)) {
			echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"lesson_verify.php?page=" . $nextPage['position'] . "\">Next Page &gt;&gt;<br /><strong>" . $nextPage['title'] . "</strong></a></div></div>";
		}
		echo "</div>";
		
	//Display content
		echo "<br /><br />";
		
		if ($lesson['type'] == "Custom Content") {
			echo $lesson['content'];
		}
		
		if ($lesson['type'] == "Embedded Content") {
		//Display comments
			if ($lesson['comments'] !== "") {
				echo $lesson['comments'];
				echo "<br /><div align=\"center\">";
			}
			
		//Prepare the directory string for future use
			$location = str_replace(" ","", $_SESSION['currentModule']);
			$file = "../../../modules/{$location}/lesson/" . $lesson['attachment'];
		
			if (file_exists($file)) {						
				$fileType = extension($file);
				switch ($fileType) {
				//If it is a PDF
					case "pdf" : echo "<iframe src=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				//If it is a Word Document
					case "doc" : echo "<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\"><img src=\"../../../images/common/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\">Click to open the module</a>"; break;
					case "docx" : echo "<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\"><img src=\"../../../images/common/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\">Click to open the module</a>"; break;
				//If it is a PowerPoint Presentation
					case "ppt" : echo "<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\"><img src=\"../../../images/common/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\">Click to open the module</a>"; break;
					case "pptx" : echo "<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\"><img src=\"../../../images/common/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\">Click to open the module</a>"; break;
				//If it is an Excel Spreadsheet
					case "xls" : echo "<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\"><img src=\"../../../images/common/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\">Click to open the module</a>"; break;
					case "xlsx" : echo "<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\"><img src=\"../../../images/common/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "../../../modules/{$location}/lesson/" . $file . "\" target=\"_blank\">Click to open the module</a>"; break;
				//If it is a Standard Text Document
					case "txt" : echo "<iframe src=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
					case "rtf" : echo "<iframe src=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				//If it is a WAV audio file
					case "wav" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"16\"><param name=\"src\" value=\"" . "../../../modules/{$location}/lesson/" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"audio/x-wav\" data=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"640\" height=\"16\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
				//If it is an MP3 audio file
					case "mp3" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"16\"><param name=\"src\" value=\"" . "../../../modules/{$location}/lesson/" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"audio/x-mpeg\" data=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"640\" height=\"16\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
				//If it is an AVI video file
					case "avi" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"480\"><param name=\"src\" value=\"" . "../../../modules/{$location}/lesson/" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"video/x-ms-asf-plugin\" data=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"640\" height=\"480\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
				//If it is an WMV video file
					case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" heught=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"../../../modules/{$location}/lesson/" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"false\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"../../../modules/{$location}/lesson/" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"0\" showdisplay=\"0\" autostart=\"0\"></embed></object>"; break;
				//If it is an FLV file
					case "flv" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../../../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../modules/{$location}/lesson/" . $file . "&amp;autostart=true\" width=\"640\" height=\"480\"></embed>"; break;
				//If it is an MOV video file
					case "mov" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"480\"><param name=\"src\" value=\"" . "../../../modules/{$location}/lesson/" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"video/quicktime\" data=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"640\" height=\"480\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
				//If it is an MP4 video file
					case "mp4" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"480\"><param name=\"src\" value=\"" . "../../../modules/{$location}/lesson/" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"video/mp4\" data=\"" . "../../../modules/{$location}/lesson/" . $file . "\" width=\"640\" height=\"480\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
				//If it is a SWF video file
					case "swf" : echo "<object width=\"640\" height=\"480\" data=\"../../../modules/{$location}/lesson/" . $file . "\" type=\"application/x-shockwave-flash\">
<param name=\"src\" value=\"../../../modules/{$location}/lesson/" . $file . "\" /></object>"; break;
				}
				
				echo "</div>";
			}
		}
		
	//Display the navigation
		echo "<br /><br /><div class=\"layoutControl\">";
		if (isset($previousPage)) {
			echo "<div class=\"contentLeft\"><div align=\"left\"><a href=\"lesson_verify.php?page=" . $previousPage['position'] . "\">&lt;&lt; Previous Page<br /><strong>" . $previousPage['title'] . "</strong></a></div></div>";
		}
		
		if (isset($nextPage)) {
			echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"lesson_verify.php?page=" . $nextPage['position'] . "\">Next Page &gt;&gt;<br /><strong>" . $nextPage['title'] . "</strong></a></div></div>";
		}
		echo "</div>";
	}
?>
</blockquote>
</div>
<div class="catDivider">
  <?php
      step("5", "Submit", "5" , "Submit")
  ?>
</div>
<div class="stepContent">
  <blockquote>
  <p>
    <?php 
      submit("back", "&lt;&lt;  Make Changes");
      submit("next", "Next Step &gt;&gt;");
    ?>
    <?php formErrors(); ?>
  </p>
  </blockquote>
</div>
</form>
      
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>