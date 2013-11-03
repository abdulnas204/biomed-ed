<?php require_once('../Connections/connDBA.php'); ?>
<?php loginCheck("Student,Instructor,Organization Administrator,Site Administrator"); ?>
<?php
//Grab all module data
	$moduleDataCheckGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position ASC", $connDBA);
	$moduleDataCheck = mysql_fetch_array($moduleDataCheckGrabber);
	
//Check to see if any modules exist
	$moduleCheck = $moduleDataCheck['id'];
	if (!$moduleCheck) {
		$modules = "empty";
	} else {
		$modules = "exist";
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Modules"); ?>
<?php headers(); ?>
<script src="../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
     <?php
	  		if ($modules == "exist") {
			//If a module is not defined in the URL, then display all modules	
				if (!isset ($_GET['id'])) {
				//Display the directions	
					echo "<h2>Modules</h2><p>Modifing the table below will chage the default settings and appearance for instructors.</p><p>&nbsp;</p>";
					
				//Display the tool bar for those with editing capabilities
					if ($_SESSION['MM_UserGroup'] == "Site Administrator") {
						echo "<div class=\"toolBar\"><a href=\"../site_administrator/modules/module_wizard/index.php\"><img src=\"../images/admin_icons/new.png\" alt=\"Add\" width=\"24\" height=\"24\" border=\"0\" /></a> <a href=\"../site_administrator/modules/module_wizard/index.php\">Add New Module</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"../site_administrator/modules/index.php\"><img src=\"../images/admin_icons/back.gif\" alt=\"Back\" width=\"17\" height=\"15\" /></a> <a href=\"../site_administrator/modules/index.php\">Back to Module Administration</a></div><br />";
					}
				
				//Loop through the modules	
					echo "<div align=\"center\">";
						echo "<table align=\"center\" class=\"dataTable\">";
						echo "<tbody>";
							echo "<tr>";
								echo "<th width=\"200\" class=\"tableHeader\"><strong>Module Name</strong></th>";
								echo "<th width=\"100\" class=\"tableHeader\"><strong>Difficulty</strong></th>";
								echo "<th class=\"tableHeader\"><strong>Comments</strong></th>";
							echo "</tr>";
						//Select data for the loop
							$moduleDataGrabber = mysql_query("SELECT * FROM moduledata WHERE `avaliable` = '1' ORDER BY position ASC", $connDBA);
							
						//Select data for drop down menu
							$dropDownDataGrabber = mysql_query("SELECT * FROM moduledata ORDER BY position ASC", $connDBA);
							
							while ($moduleData = mysql_fetch_array($moduleDataGrabber)){
								echo "<tr";
								if ($moduleData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
								">";									
									echo "<td width=\"200\"><div align=\"center\"><a href=\"index.php?id=" . $moduleData['id'] . "\" onmouseover=\"Tip('Launch the <strong> " . $moduleData['name'] . " </strong>module.')\" onmouseout=\"UnTip()\">" . $moduleData['name'] . "</a></div></td>";
									
									echo "<td width=\"100\"><div align=\"center\">" . $moduleData['difficulty'] . "</div></td>";
									
									echo "<td align=\"center\"><div align=\"center\">";
										if ($moduleData['comments'] == "") {
											echo "<i>None</i>";
										} else {
											$htmlStrip = array("<p>", "</p>");
											$comments = str_replace ($htmlStrip, "", $moduleData['comments']);
											echo $comments;
										}
									echo "</div></td>";
								echo "</tr>";
							}
						echo "</tbody>";
					echo "</table></div>";
			//If a module is defined in the URL, then display that module	
				} elseif (isset ($_GET['id'])) {
					$id = $_GET['id'];
					$moduleInfoGrabber = mysql_query("SELECT * FROM moduledata WHERE id = '{$id}' LIMIT 1", $connDBA);
				//If a module is assoicated with the give ID, then display the information	
					if ($moduleInfo = mysql_fetch_array($moduleInfoGrabber)) {
					//Grab the due date
						$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
						$numberArray = array("0","1","2","3","4","5","6","7","8","9");						
						$time = str_replace($letterArray, "", $moduleInfo['timeFrame']);
						$timeLabel = str_replace($numberArray, "", $moduleInfo['timeFrame']);
						
					//Display the module information	
						echo "<h2>" . $moduleInfo['name'] . "</h2>" .  $moduleInfo['comments'] . "<div class=\"toolBar\"><strong>Due Date:</strong> " . $time . " " .$timeLabel . "<br /><strong>Category:</strong> " . $moduleInfo['category'] . "<br /><strong>Intended Employee Type:</strong> " . $moduleInfo['employee'] . "<br /><strong>Difficulty:</strong> " . $moduleInfo['difficulty'] . "</div>";
						
					//Display the lesson
						$directory = str_replace (" ", "", $moduleInfo['name']);
						$moduleDirectory = opendir("{$directory}/lesson");
						$module = readdir($moduleDirectory);
						
						//Detirmine the file type, and display it as needed	
							function findExtension ($targetFile) {
								$fileName = strtolower($targetFile) ;
								$entension = split("[/\\.]", $targetFile) ;
								$value = count($entension)-1;
								$entension = $entension[$value];
								return $entension;
							}
							
						echo "<div align=\"center\">";	
	
						while ($module = readdir($moduleDirectory)) {
							//Leave out the "." and the ".."
							if (($module != ".") && ($module != "..")) {							
								$fileType = findExtension($module);
								switch ($fileType) {
								//If it is a PDF
									case "pdf" : echo "<iframe src=\"" . "{$directory}/lesson/" . $module . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
								//If it is a Word Document
									case "doc" : echo "<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\"><img src=\"../../../images/common/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\">Click to open the module</a>"; break;
									case "docx" : echo "<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\"><img src=\"../../../images/common/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\">Click to open the module</a>"; break;
								//If it is a PowerPoint Presentation
									case "ppt" : echo "<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\"><img src=\"../../../images/common/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\">Click to open the module</a>"; break;
									case "pptx" : echo "<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\"><img src=\"../../../images/common/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\">Click to open the module</a>"; break;
								//If it is an Excel Spreadsheet
									case "xls" : echo "<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\"><img src=\"../../../images/common/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\">Click to open the module</a>"; break;
									case "xlsx" : echo "<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\"><img src=\"../../../images/common/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . "{$directory}/lesson/" . $module . "\" target=\"_blank\">Click to open the module</a>"; break;
								//If it is a Standard Text Document
									case "txt" : echo "<iframe src=\"" . "{$directory}/lesson/" . $module . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
									case "rtf" : echo "<iframe src=\"" . "{$directory}/lesson/" . $module . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
								//If it is a WAV audio file
									case "wav" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"16\"><param name=\"src\" value=\"" . "{$directory}/lesson/" . $module . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"audio/x-wav\" data=\"" . "{$directory}/lesson/" . $module . "\" width=\"640\" height=\"16\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
								//If it is an MP3 audio file
									case "mp3" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"16\"><param name=\"src\" value=\"" . "{$directory}/lesson/" . $module . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"audio/x-mpeg\" data=\"" . "{$directory}/lesson/" . $module . "\" width=\"640\" height=\"16\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
								//If it is an AVI video file
									case "avi" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"480\"><param name=\"src\" value=\"" . "{$directory}/lesson/" . $module . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"video/x-ms-asf-plugin\" data=\"" . "{$directory}/lesson/" . $module . "\" width=\"640\" height=\"480\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
								//If it is an WMV video file
									case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" heught=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"{$directory}/lesson/" . $module . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"false\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"{$directory}/lesson/" . $module . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"0\" showdisplay=\"0\" autostart=\"0\"></embed></object>"; break;
								//If it is an FLV file
									case "flv" : echo "<embed type=\"application/x-shockwave-flash\" src=\"../player/player.swf\" style=\"\" id=\"player\" name=\"player\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" wmode=\"opaque\" flashvars=\"file=../modules/{$directory}/lesson/" . $module . "&amp;autostart=true\" width=\"640\" height=\"480\"></embed>"; break;
								//If it is an MOV video file
									case "mov" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"480\"><param name=\"src\" value=\"" . "{$directory}/lesson/" . $module . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"video/quicktime\" data=\"" . "{$directory}/lesson/" . $module . "\" width=\"640\" height=\"480\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
								//If it is an MP4 video file
									case "mp4" : echo "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\" width=\"640\" height=\"480\"><param name=\"src\" value=\"" . "{$directory}/lesson/" . $module . "\"><param name=\"autoplay\" value=\"true\"><param name=\"autostart\" value=\"0\"><param name=\"controller\" value=\"true\"><param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/\"><!--[if !IE]><object type=\"video/mp4\" data=\"" . "{$directory}/lesson/" . $module . "\" width=\"640\" height=\"480\"><param name=\"pluginurl\" value=\"http://www.apple.com/quicktime/download/\"><param name=\"controller\" value=\"true\"><param name=\"autoplay\" value=\"false\"><param name=\"autostart\" value=\"1\"></object><![endif]--></object>"; break;
								//If it is a SWF video file
									case "swf" : echo "<object width=\"640\" height=\"480\" data=\"{$directory}/lesson/" . $module . "\" type=\"application/x-shockwave-flash\">
	<param name=\"src\" value=\"{$directory}/lesson/" . $module . "\" /></object>"; break;
								}
								
								echo "</div>";
							} 
						}
						
						echo "<br /><br /><blockquote><input name=\"test\" type=\"button\" id=\"test\" onclick=\"MM_goToURL('parent','test.php');return document.MM_returnValue\" value=\"Take the Test\" /><input name=\"cancel\" type=\"button\" id=\"cancel\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Cancel\" /></blockquote>";
				//If a module is not assoicated with the give ID, then redirect to the main page
					} else {
						header ("Location: index.php");
						exit;
					}
				}
			} else {
				if ($_SESSION['MM_UserGroup'] == "Site Administrator") {
					echo "<br /></br /><div align=\"center\">There are no modules. <a href=\"../site_administration/modules/module_wizard/index.php\">Create one now</a>.</div><br /></br /><br /></br /><br /></br />";
				}
			}
	  ?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>