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
		//header ("Location: modify.php");
		//exit;
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
			die("This page does not exist");
		}
	} else {
		die("The page number is not specified");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php title("Preview Page : " . $lesson['title']); ?>
<?php headers(); ?>
</head>

<body class="overrideBackground">
<?php
//Display the lesson
	if (isset ($_SESSION['currentModule'])) {
	//Display the title
		echo "<h2 class=\"preview\">Preview Page : " . 	$lesson['title'] . "</h2>";
		
	//Display the navigation, only on the lesson preview page
		/*
		echo "<br /><br /><div class=\"layoutControl\">";
		if (isset($previousPage)) {
			echo "<div class=\"contentLeft\"><div class=\"previousPage\" align=\"left\"><a href=\"preview_page.php?page=" . $previousPage['position'] . "\">&lt;&lt; Previous Page<br /><span class=\"pageTitle\">" . $previousPage['title'] . "</span></a></div></div>";
		}
		
		if (isset($nextPage)) {
			echo "<div class=\"dataRight\"><div class=\"nextPage\"><a href=\"preview_page.php?page=" . $nextPage['position'] . "\">Next Page &gt;&gt;<br /><span class=\"pageTitle\">" . $nextPage['title'] . "</span></a></div></div>";
		}
		echo "</div>";
		*/
		
	//Display content
		echo "<br /><br />";
		
		if ($lesson['type'] == "Custom Content") {
			echo $lesson['content'];
		}
		
		if ($lesson['type'] == "Embedded Content") {
		//Display comments
			if ($lesson['comments'] !== "") {
				echo $lesson['comments'];
				echo "<br />";
			}
			
			echo "<div align=\"center\">";
			
		//Prepare the directory string for future use
			$location = str_replace(" ","", $_SESSION['currentModule']);
			$file = "../../../modules/{$location}/lesson/" . $lesson['attachment'];
		
			if (file_exists($file)) {						
				$fileType = extension($file);
				switch ($fileType) {
				//If it is a PDF
					case "pdf" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				//If it is a Word Document
					case "doc" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../../../images/programIcons/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
					case "docx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../../../images/programIcons/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is a PowerPoint Presentation
					case "ppt" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../../../images/programIcons/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
					case "pptx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../../../images/programIcons/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is an Excel Spreadsheet
					case "xls" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../../../images/programIcons/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
					case "xlsx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../../../images/programIcons/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is a Standard Text Document
					case "txt" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
					case "rtf" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../../../images/programIcons/text.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is a WAV audio file
					case "wav" : echo "<object width=\"640\" height=\"16\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"16\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
				//If it is an MP3 audio file
					case "mp3" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../../../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../../../modules/{$location}/lesson/" . $lesson['attachment'] . "&amp;autostart=true\" width=\"640\" height=\"16\"></embed>"; break;
				//If it is an AVI video file
					case "avi" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
				//If it is an WMV video file
					case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
				//If it is an FLV file
					case "flv" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../../../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../modules/{$location}/lesson/" . $lesson['attachment'] . "&amp;autostart=true\" width=\"640\" height=\"480\"></embed>"; break;
				//If it is an MOV video file
					case "mov" : echo "<object width=\"640\" height=\"480\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"480\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
				//If it is an MP4 video file
					case "mp4" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../../../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../modules/{$location}/lesson/" . $lesson['attachment'] . "&amp;autostart=true\" width=\"640\" height=\"480\"></embed>"; break;
				//If it is a SWF video file
					case "swf" : echo "<object width=\"640\" height=\"480\" data=\"" . $file . "\" type=\"application/x-shockwave-flash\">
<param name=\"src\" value=\"" . $file . "\" /></object>"; break;
				}
			}
			
			echo "</div>";
		}
		
		
	//Display the navigation, only on the lesson preview page
		/*
		echo "<br /><br /><div class=\"layoutControl\">";
		if (isset($previousPage)) {
			echo "<div class=\"contentLeft\"><div class=\"previousPage\"><a href=\"preview_page.php?page=" . $previousPage['position'] . "\">&lt;&lt; Previous Page<br /><span class=\"pageTitle\">" . $previousPage['title'] . "</span></a></div></div>";
		}
		
		if (isset($nextPage)) {
			echo "<div class=\"dataRight\"><div class=\"nextPage\"><a href=\"preview_page.php?page=" . $nextPage['position'] . "\">Next Page &gt;&gt;<br /><span class=\"pageTitle\">" . $nextPage['title'] . "</span></a></div></div>";
		}
		echo "</div>";
		*/
	}
?>
</body>
</html>