<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: February 5th, 2010
Last updated: February 9th, 2010

This is the logo management page.
*/

//Header functions
	require_once('../../../../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Logo Management", "validate", true);
	lockAccess();
	
//Process the logo form
	if (isset($_POST['submit'])) {
		$tempFile = $_FILES['bannerUpload']['tmp_name'];
		$targetFile = basename($_FILES['bannerUpload']['name']);
		$uploadDir = "../../../../../system/images";

		if (extension($targetFile) == "png" || extension($targetFile) == "bmp" || extension($targetFile) == "jpg" || extension($targetFile) == "gif") {
			if (move_uploaded_file($tempFile, $uploadDir . "/" . "banner.png")) {
				redirect("index.php?updated=logo");
			} else {
				redirect($_SERVER['PHP_SELF'] . "?error=upload");
			}
		} else {
			redirect($_SERVER['PHP_SELF'] . "?error=fileType");
		}
	}
	
	if (isset ($_POST['updatePlacement'])) {
		$id = $_POST['idHidden'];
		$imagePaddingTopEdit = $_POST['paddingTopSelect'];
		$imagePaddingBottomEdit = $_POST['paddingBottomSelect'];
		$imagePaddingLeftEdit = $_POST['paddingLeftSelect'];
		$imagePaddingRightEdit = $_POST['paddingRightSelect'];
				
		$imagePaddingQuery = "UPDATE siteprofiles SET paddingTop = '{$imagePaddingTopEdit}', paddingBottom = '{$imagePaddingBottomEdit}', paddingLeft = '{$imagePaddingLeftEdit}', paddingRight = '{$imagePaddingRightEdit}' WHERE id = '{$id}'";
		
		$imagePaddingQueryResult = mysql_query($imagePaddingQuery, $connDBA);
		
		if (mysql_affected_rows() == 1) {
			header("Location: site_settings.php?type=logo");
			exit;
		}
	}
	
	if (isset ($_POST['updateSize'])) {		
		if (isset ($_POST['automatic'])) {
			$auto = $_POST['automatic'];
			$imageSizeQuery = "UPDATE siteprofiles SET auto = '{$auto}'";
		} else {
			$imageHeight = $_POST['height'];
			$imageWidth = $_POST['width'];
			$imageSizeQuery = "UPDATE siteprofiles SET height = '{$imageHeight}', width = '{$imageWidth}', auto = '{$auto}'";
		}
		
		mysql_query($imageSizeQuery, $connDBA);
		
		if (mysql_affected_rows() == 1) {
			header("Location: site_settings.php?type=logo");
			exit;
		}
	}
	
//Title
	title("Logo Management", "Here you can make changes to the logo by changing its size, position, as well as the logo itself.");
	
//Display message updates
	message("error", "fileType", "error", "This is an unsupported file type. Supported types have one of the following extensions: &quot.PNG&quot;, &quot.BMP&quot;, &quot.JPG&quot;, or &quot.GIF&quot;.");
	message("error", "upload", "error", "There was an error when uploading the file. Ensure you did not cancel the upload before it was finished, and be sure your file is smaller than the maxmium file size displayed below the file field.");
	
//Logo upload form
	catDivider("Site Logo", "alignLeft", true);
	echo form("logoUpload");
	echo "<blockquote>\n";
	fileUpload("bannerUpload", "bannerUpload", false, true, "
?>
<div class="layoutControl"> 
    <div class="dataLeft">
    <div class="block_course_list sideblock">
          <div class="header">
            <div class="title">
              <h2>Modify the banner displayed at the of each page. Navigation</h2>
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
      <div class="catDivider alignLeft">Site Logo</div>
      <div class="stepContent">
      <blockquote>
        <form action="site_settings.php?type=logo" method="post" enctype="multipart/form-data" id="uploadBanner" onsubmit="return errorsOnSubmit(this, 'false', 'bannerUploader', 'true', 'png.bmp.jpg.gif');">
          <div align="left">
            <?php
			//Display current banner if it exists
				$directory = "../../images";
			
				if (file_exists($directory)) {
					$imageDirectory = opendir("../../images");
					$image = readdir($imageDirectory);
					while (false !== ($image = readdir($imageDirectory))) {
						if (($image == "banner.png")) {
							echo "<p>";
								echo "Current file: <a href=\"../../images/banner.png\" target=\"_blank\">banner.png</a>";
							echo "</p>";
						} 
					} 
				}
			?>
            <input name="bannerUploader" type="file" id="bannerUploader" size="50" />
            <br />
            Max file size: <?php echo ini_get('upload_max_filesize'); ?><br />
            <br />
            <p>
            <?php submit("submitBanner", "Upload"); ?>
            <input name="cancelBanner" type="button" id="cancelBanner" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
            </p>
          </div>
          <?php formErrors(); ?>
        </form>
      </blockquote>
      </div>
      <div class="catDivider alignLeft">Banner Placement</div>
      <div class="stepContent">
      <blockquote>
      <form action="site_settings.php?type=logo" method="post" name="padding" id="padding">
        <div align="left">
          <input type="hidden" name="idHidden" value="1" />
          	  <?php
			  //Select the image padding information
			  		$imagePaddingGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
					$imagePadding = mysql_fetch_array($imagePaddingGrabber);
					$imagePaddingTop = $imagePadding['paddingTop'];
					$imagePaddingLeft = $imagePadding['paddingLeft'];
					$imagePaddingRight = $imagePadding['paddingRight'];
					$imagePaddingBottom = $imagePadding['paddingBottom'];
			  ?>
          <p><strong>Top:</strong>
            <input name="paddingTopSelect" type="text" id="paddingTopSelect" value="<?php echo $imagePaddingTop; ?>" size="3" maxlength="3" autocomplete="off" />
px<br />
<strong>Left:</strong>
<input name="paddingLeftSelect" type="text" id="paddingLeftSelect" value="<?php echo $imagePaddingLeft; ?>" size="3" maxlength="3"  autocomplete="off" />
px<br />
<strong>Right:</strong>
<input name="paddingRightSelect" type="text" id="paddingRightSelect" value="<?php echo $imagePaddingRight; ?>" size="3" maxlength="3"  autocomplete="off" />
px<br />
<strong>Bottom:</strong>
<input name="paddingBottomSelect" type="text" id="paddingBottomSelect" value="<?php echo $imagePaddingBottom; ?>" size="3" maxlength="3" autocomplete="off" />
px</p>
          <p>
            <?php submit("updatePlacement", "Update"); ?>
            <input name="cancelPlacement" type="button" id="cancelPlacement" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
            <br />
          </p>
          <h6>px = Pixels from respective edge</h6>
        </div>
      </form>
      </blockquote>
      </div>
      <div class="catDivider alignLeft">Banner Size</div>
      <div class="stepContent">
      <blockquote>
      <form action="site_settings.php?type=logo" method="post" name="size" id="size">
      <?php
	  //Select the image size information
			$imageSizeGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
			$imageData = mysql_fetch_array($imageSizeGrabber);
	  ?>
      Width:
      <input name="width" type="text" id="width" value="<?php echo $imageData['width']; ?>" size="3" maxlength="3" autocomplete="off"<?php if ($imageData['auto'] == "on") {echo " disabled=\"disabled\"";} ?> />
px <br />
Height:
<input name="height" type="text" id="height" value="<?php echo $imageData['height']; ?>" size="3" maxlength="3" autocomplete="off"<?php if ($imageData['auto'] == "on") {echo " disabled=\"disabled\"";} ?> />
px
<p>
        <label><input type="checkbox" name="automatic" id="automatic" onclick="flvFTFO1('size','width,t','height,t')"<?php
			if ($imageData['auto'] == "on") {
				echo " checked=\"checked\"";
			}
		?> /> Automatic</label>
      </p>
      <p>
        <?php submit("updateSize", "Update"); ?>
        <input name="cancelSize" type="button" id="cancelSize" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
      </p>
      </form>
      </blockquote>
      </div>
</div>
</div>