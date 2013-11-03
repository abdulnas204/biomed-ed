<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript">
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
</head>

<body>
<p>Modify the browser icon displayed on the top-left of the browser window or the current tab. A browser icon may have one of the following extenstions : &quot;.ico&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;. Below is an example of a browser icon: <br />
  <br />
  <img src="../../images/admin_icons/faviconExample.jpg" alt="Browser Icon" /></p>
<?php
//Identify the icon extension
	$iconExtensionGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$iconExtension = mysql_fetch_array($iconExtensionGrabber);

//Modify browser icon
	if (isset($_POST['submitIcon'])) {
		$tempFile = $_FILES['iconUploader'] ['tmp_name'];
		$targetFile = basename($_FILES['iconUploader'] ['name']);
		$uploadDir = "../../images";
	
		if (extension($targetFile) == "ico" || extension($targetFile) == "jpg" || extension($targetFile) == "gif") {
			
			$iconType = extension($targetFile);
			unlink("../../images/icon." . $iconExtension['iconType']);
			move_uploaded_file($tempFile, $uploadDir . "/" . "icon." . $iconType);
			mysql_query("UPDATE `siteprofiles` SET `iconType` = '{$iconType}' WHERE id = '1'", $connDBA);
			
			if (isset ($_POST['return'])) {
				header ("Location: site_setup_wizard.php");
				exit;
			} else {
				header ("Location: index.php?updated=icon");
				exit;
			}
		} else {
			errorMessage("This is an unsupported file type. Supported types have one of the following extensions: &quot;.ico&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;.");
		}
	}
?>
<?php errorWindow("extension", "This is an unsupported file type. Supported types have one of the following extensions: &quot;.ico&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;."); ?>
<br />
<div class="layoutControl">
  <div class="dataLeft">
    <div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Navigation</h2>
        </div>
      </div>
      <div class="content">
        <p>Modify other settings within this site:</p>
        <ul>
          <li class="homeBullet"><a href="index.php">Back to Home</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="contentRight">
    <form action="site_settings.php?type=icon" method="post" enctype="multipart/form-data" id="uploadIcon" onsubmit="return errorsOnSubmit(this, 'false', 'iconUploader', 'true', 'ico.jpg.gif');">
      <div class="catDivider one">Upload Icon</div>
      <div class="stepContent">
        <blockquote>
          <p>
            <?php
			//Display current banner if it exists
				$directory = "../../images";
			
				if (file_exists($directory)) {
					$imageDirectory = opendir("../../images");
					$image = readdir($imageDirectory);
					echo "<p>";
						echo "Current file: <a href=\"../../images/icon." . $iconExtension['iconType'] . "\" target=\"_blank\">icon." . $iconExtension['iconType'] . "</a>";
					echo "</p>";
				}
			?>
            <input name="iconUploader" type="file" id="iconUploader" size="50" />
            <br />
            Max file size: <?php echo ini_get('upload_max_filesize'); ?></p>
        </blockquote>
      </div>
      <div class="catDivider two">Submit</div>
      <div class="stepContent">
        <blockquote>
          <p>
            <?php submit("submitIcon", "Submit"); ?>
            <input name="cancelIcon" type="button" id="cancelIcon" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
          </p>
          <?php formErrors(); ?>
        </blockquote>
      </div>
    </form>
  </div>
</div>
<?php //If the theme page is requested
	} elseif (isset ($theme)) { 
?>
<?php 
	if (isset ($_GET['action']) && $_GET['action'] == "modifyTheme" && !empty($_GET['theme'])) {
		$theme = $_GET['theme'];
		
		$modifyThemeQuery = "UPDATE siteprofiles SET style = '{$theme}'";
		$modifyThemeQueryResult = mysql_query($modifyThemeQuery, $connDBA);
		header ("Location: index.php?updated=theme");
		exit;
	}
	
	$themeGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$theme = mysql_fetch_array($themeGrabber);
?>
</body>
</html>