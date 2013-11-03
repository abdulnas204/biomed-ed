<?php require_once('../Connections/connDBA.php'); ?>
<?php loginCheck("Student,Instructor,Organization Administrator,Site Manager,Site Administrator"); ?>
<?php
//Grab all module data
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$moduleInfoGrabber = mysql_query("SELECT * FROM moduledata WHERE id = '{$id}' LIMIT 1", $connDBA);
	
	//Check to see if any modules exist
		if (mysql_fetch_array($moduleInfoGrabber)) {
			$modules = "empty";
		} else {
			header("Location: index.php");
			exit;
		}
	}
	
//Grab all of the lesson data
	if (isset($_GET['page'])) {
	//Grab the lesson data
		$id = $_GET['id'];
		$page = $_GET['page'];
		$back = $_GET['page']-1;
		$next = $_GET['page']+1;
		$moduleInfoGrabber = mysql_query("SELECT * FROM moduledata WHERE id = '{$id}' LIMIT 1", $connDBA);
		$moduleInfo = mysql_fetch_array($moduleInfoGrabber);
		$currentLesson = str_replace(" ", "", $moduleInfo['name']);
		$lessonGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentLesson}` WHERE `position` = '{$page}'", $connDBA);
		
		if ($lesson = mysql_fetch_array($lessonGrabber)) {
			$back = $lesson['position']-1;
			$previousPageGrabber =  mysql_query("SELECT * FROM `modulelesson_{$currentLesson}` WHERE `position` = '{$back}'", $connDBA);
			if ($previousPageCheck = mysql_fetch_array($previousPageGrabber)) {
				$previousPage = $previousPageCheck;
			}
			
			$next = $lesson['position']+1;
			$nextPageGrabber =  mysql_query("SELECT * FROM `modulelesson_{$currentLesson}` WHERE `position` = '{$next}'", $connDBA);
			if ($nextPageCheck = mysql_fetch_array($nextPageGrabber)) {
				$nextPage = $nextPageCheck;
			}
			
			$lastPageGrabber = mysql_query("SELECT * FROM `modulelesson_{$currentLesson}` ORDER BY `position` DESC LIMIT 1", $connDBA);
			$lastPageCheck = mysql_fetch_array($lastPageGrabber);
		} else {
			header ("Location:lesson.php?id=" . $_GET['id']);
			exit;
		}
	} else {
		$moduleInfoGrabber = mysql_query("SELECT * FROM moduledata WHERE id = '{$id}' LIMIT 1", $connDBA);
		$moduleInfo = mysql_fetch_array($moduleInfoGrabber);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (!isset($_GET['page'])) {
		$title = $moduleInfo['name'];
	} else {
		$title = $moduleInfo['name'] . " : " . $lesson['title'];
	}
	
	title($title); 
?>
<?php headers(); ?>
<script src="../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<?php	
	if (!isset($_GET['page'])) {
	//Display the due date
		$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$numberArray = array("0","1","2","3","4","5","6","7","8","9");						
		$time = str_replace($letterArray, "", $moduleInfo['timeFrame']);
		$timeLabel = str_replace($numberArray, "", $moduleInfo['timeFrame']);
		
	//Display the module information	
		echo "<h2>" . $title . "</h2><div class=\"toolBar\"><strong>Due Date:</strong> " . $time . " " .$timeLabel . "<br /><strong>Category:</strong> " . $moduleInfo['category'] . "<br /><strong>Intended Employee Type:</strong> " . $moduleInfo['employee'] . "<br /><strong>Difficulty:</strong> " . $moduleInfo['difficulty'] . "</div>";
	} else {
		echo "<h2>" . $title . "</h2>";
	}
	
//Display the navigation
	echo "<div class=\"layoutControl\">";
	if (isset($previousPage)) {
		echo "<div class=\"contentLeft\"><div align=\"left\"><a href=\"lesson.php?id=" . $id . "&page=" . $previousPage['position'] . "\">&lt;&lt; Previous Page<br /><strong>" . $previousPage['title'] . "</strong></a></div></div>";
	}
	
	if (isset($nextPage)) {
		echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"lesson.php?id=" . $id . "&page=" . $nextPage['position'] . "\">Next Page &gt;&gt;<br /><strong>" . $nextPage['title'] . "</strong></a></div></div>";
	} else {
		if (isset($_GET['page'])) {
			if ($moduleInfo['test'] == "1") {
				echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"test.php?id=" . $id . "\">Next &gt;&gt;<br /><strong>Take the Test</strong></a></div></div>";
			} else {
				echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"index.php\">Next &gt;&gt;<br /><strong>Finish</strong></a></div></div>";
			}
		}
	}
	echo "</div>";
	
	if (isset($_GET['page'])) {					
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
			$location = $currentLesson;
			$file = "{$location}/lesson/" . $lesson['attachment'];
		
			if (file_exists($file)) {						
				$fileType = extension($file);
				switch ($fileType) {
				//If it is a PDF
					case "pdf" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				//If it is a Word Document
					case "doc" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../images/programIcons/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
					case "docx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../images/programIcons/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is a PowerPoint Presentation
					case "ppt" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../images/programIcons/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
					case "pptx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../images/programIcons/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is an Excel Spreadsheet
					case "xls" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../images/programIcons/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
					case "xlsx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../images/programIcons/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is a Standard Text Document
					case "txt" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
					case "rtf" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"../images/programIcons/text.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				//If it is a WAV audio file
					case "wav" : echo "<object width=\"640\" height=\"16\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"16\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
				//If it is an MP3 audio file
					case "mp3" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../modules/{$location}/lesson/" . $lesson['attachment'] . "&amp;autostart=true\" width=\"640\" height=\"16\"></embed>"; break;
				//If it is an AVI video file
					case "avi" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
				//If it is an WMV video file
					case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
				//If it is an FLV file
					case "flv" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../modules/{$location}/lesson/" . $lesson['attachment'] . "&amp;autostart=true\" width=\"640\" height=\"480\"></embed>"; break;
				//If it is an MOV video file
					case "mov" : echo "<object width=\"640\" height=\"480\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"480\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
				//If it is an MP4 video file
					case "mp4" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../modules/{$location}/lesson/" . $lesson['attachment'] . "&amp;autostart=true\" width=\"640\" height=\"480\"></embed>"; break;
				//If it is a SWF video file
					case "swf" : echo "<object width=\"640\" height=\"480\" data=\"" . $file . "\" type=\"application/x-shockwave-flash\">
	<param name=\"src\" value=\"" . $file . "\" /></object>"; break;
				}
			}
			
			echo "</div>";
		}
	} else {
		echo $moduleInfo['comments'] . "<p>&nbsp;</p><div align=\"center\"><input name=\"beginLesson\" id=\"beginLesson\" onclick=\"MM_goToURL('parent','lesson.php?id=" . $_GET['id'] . "&page=1');return document.MM_returnValue\" value=\"Begin Lesson\" type=\"button\"><p>&nbsp;</p><p>&nbsp;</p></div>";
	}
	
//Display the navigation
	echo "<br /><br /><div class=\"layoutControl\">";
	if (isset($previousPage)) {
		echo "<div class=\"contentLeft\"><div align=\"left\"><a href=\"lesson.php?id=" . $id . "&page=" . $previousPage['position'] . "\">&lt;&lt; Previous Page<br /><strong>" . $previousPage['title'] . "</strong></a></div></div>";
	}
	
	if (isset($nextPage)) {
		echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"lesson.php?id=" . $id . "&page=" . $nextPage['position'] . "\">Next Page &gt;&gt;<br /><strong>" . $nextPage['title'] . "</strong></a></div></div>";
	} else {
		if (isset($_GET['page'])) {
			if ($moduleInfo['test'] == "1") {
				echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"test.php?id=" . $id . "\">Next &gt;&gt;<br /><strong>Take the Test</strong></a></div></div>";
			} else {
				echo "<div class=\"dataRight\"><div align=\"right\"><a href=\"index.php\">Next &gt;&gt;<br /><strong>Finish</strong></a></div></div>";
			}
		}
	}
	echo "</div>";
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>