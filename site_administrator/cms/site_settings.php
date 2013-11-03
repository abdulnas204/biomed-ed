<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//If the site logo page is requested
	if (isset ($_GET['type']) && ($_GET['type'] == "logo")) {
		$logo = "logo is requested";
//If the site logo page is requested
	} else if (isset ($_GET['type']) && ($_GET['type'] == "icon")) {
		$icon = "icon is requested";
//If the theme page is requested
	} elseif (isset ($_GET['type']) && ($_GET['type'] == "theme")) {
		$theme = "theme is requested";
//If the site meta page is requested
	} elseif (isset ($_GET['type']) && ($_GET['type'] == "meta")) {
		$meta = "meta is requested";
//If the list of settings is requested
	} elseif (!isset ($_GET['type'])) {
		$settings = "settings are requested";
	} else {
		header("Location: site_settings.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
//If the site logo page is requested
	if (isset ($logo)) {
		$title = "Modify Site Logo";
//If the site logo page is requested
	} elseif (isset ($icon)) {
		$title = "Browser Icon";
//If the theme page is requested
	} elseif (isset ($theme)) {
		$title = "Modify Site Theme";
//If the site meta page is requested
	} elseif (isset ($meta)) {
		$title = "Modify Site Information";
	} else {
		$title = "Select Settings";
	}
?>
<?php title($title); ?>
<?php headers(); ?>
<?php validate(); ?>
<?php tinyMCESimple(); ?>
<script src="../../javascripts/common/enableDisable.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2><?php echo $title; ?></h2>
<?php 
//If the list of settings is requested
	if (isset($settings)) {
?>
<p>Modify  settings within this site:</p>
<p>&nbsp;</p>
<blockquote>
<ul>
  <li class="homeBullet"><a href="index.php">Back to Home</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
  <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
</ul>
</blockquote>
<?php
//If the site logo page is requested
	} elseif (isset ($logo)) { 
?>
<p>Modify the banner displayed at the of each page.</p>
<?php
//Modify logo
	if (isset($_POST['submitBanner'])) {
		$tempFile = $_FILES['bannerUploader'] ['tmp_name'];
		$targetFile = basename($_FILES['bannerUploader'] ['name']);
		$uploadDir = "../../images";

		if (extension($targetFile) == "png" || extension($targetFile) == "bmp" || extension($targetFile) == "jpg" || extension($targetFile) == "gif") {
			move_uploaded_file($tempFile, $uploadDir . "/" . "banner.png");
			if (isset ($_POST['return'])) {
				header ("Location: site_setup_wizard.php");
				exit;
			} else {
				header ("Location: index.php?updated=logo");
				exit;
			}
		} else {
			errorMessage("This is an unsupported file type. Supported types have one of the following extensions: &quot;.png&quot;, &quot;.bmp&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;.");
		}
	} else {
		echo "<p>&nbsp;</p>";
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
?>
<?php errorWindow("extension", "This is an unsupported file type. Supported types have one of the following extensions: &quot;.png&quot;, &quot;.bmp&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;."); ?>
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
<?php
//If the browiser icon page is requested
	} elseif (isset ($icon)) { 
?>
      <p>Modify the browser icon displayed on the top-left of the browser window or the current tab. A browser icon may have one of the following extenstions : &quot;.ico&quot;, &quot;.jpg&quot;, or &quot;.gif&quot;. Below is an example of a browser icon:
      <br />
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
          Max file size: <?php echo ini_get('upload_max_filesize'); ?> </p>
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
<p>This page will  modify the site colors, text styles, and splash image for this site.</p>
<p>&nbsp;</p>
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
<form action="site_settings.php?type=theme" method="post">
<div class="catDivider alignLeft">Select a Theme</div>
    <div class="stepContent">
    <blockquote>
      <p><strong>American</strong></p><?php if ($theme['style'] == "american.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
      <p><img src="../../images/themes/american/preview.jpg" alt="American Theme Preview" width="252" height="124" />
        <input type="button" name="chooseAmerican" id="chooseAmerican" value="Choose American Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=american.css');return document.MM_returnValue" />
      </p>
      <p><strong>Binary</strong></p><?php if ($theme['style'] == "binary.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
      <p><img src="../../images/themes/binary/preivew.jpg" alt="Binary Theme Preview" width="251" height="119" />
        <input type="button" name="chooseBinary" id="chooseBinary" value="Choose Binary Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=binary.css');return document.MM_returnValue" />
      </p>
      <p><strong>Business</strong></p><?php if ($theme['style'] == "business.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
      <p><img src="../../images/themes/business/preview.jpg" alt="Business Theme Preview" width="251" height="125" />
        <input type="button" name="chooseBusiness" id="chooseBusiness" value="Choose Business Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=business.css');return document.MM_returnValue" />
      </p>
      <p><strong>Digital University</strong></p><?php if ($theme['style'] == "digitalUniversity.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
      <p><img src="../../images/themes/digital_university/preview.jpg" alt="Digitial University Theme Preview" width="252" height="111" />
        <input type="button" name="chooseDigital" id="chooseDigital" value="Choose Digital University Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=digitalUniversity.css');return document.MM_returnValue" />
      </p>
      <p><strong>e-Learning</strong></p><?php if ($theme['style'] == "eLearning.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
      <p><img src="../../images/themes/e_learning/preview.jpg" alt="e-Learning Theme Preview" width="252" height="111" />
        <input type="button" name="chooseLearning" id="chooseLearning" value="Choose e-Learning Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=eLearning.css');return document.MM_returnValue" />
      </p>
      <p><strong>Knowledge Library</strong></p><?php if ($theme['style'] == "knowledgeLibrary.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
      <p><img src="../../images/themes/knowledge_library/preview.jpg" alt="Knowledge Library Theme Preview" width="252" height="111" />
        <input type="button" name="chooseLibrary" id="chooseLibrary" value="Choose Knowledge Library Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&action=modifyTheme&theme=knowledgeLibrary.css');return document.MM_returnValue" />
      </p>
      <p>&nbsp;</p>
    </blockquote>
     </div>
     </form>
  </div>
</div>
<?php 
//If the site meta page is requested
	} elseif (isset ($meta)) { 
?>
<?php 
	if (isset ($_POST['modifyMeta']) || isset ($_POST['modifyMeta2']) && !empty($_POST['name'])) {
		if (!empty($_POST['name'])) {
			$name = mysql_real_escape_string($_POST['name']);
			$footer = mysql_real_escape_string($_POST['footer']);
			$author = mysql_real_escape_string($_POST['author']);
			$language = mysql_real_escape_string($_POST['language']);
			$timeZone = mysql_real_escape_string($_POST['timeZone']);
			$copyright = mysql_real_escape_string($_POST['copyright']);
			$description = mysql_real_escape_string($_POST['description']);
			$meta = mysql_real_escape_string($_POST['meta']);
			
			$modifyMetaQuery = "UPDATE siteprofiles SET siteName = '{$name}', siteFooter = '{$footer}', author = '{$author}', language = '{$language}', copyright = '{$copyright}', description = '{$description}', meta = '{$meta}', timeZone = '{$timeZone}'";
			$modifyMetaQueryResult = mysql_query($modifyMetaQuery, $connDBA);
			header ("Location: index.php?updated=siteInfo");
			exit;
		}
	}
	
	$themeGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$theme = mysql_fetch_array($themeGrabber);
?>
<p>Modify the site name and footer, as well as information which will help search engines better locate your site.</p>
<p>&nbsp;</p>
<form action="site_settings.php?type=meta" method="post" name="information" id="validate" onsubmit="return errorsOnSubmit(this);">
<?php
//Select the image padding information
	$siteInfoGrabber = mysql_query("SELECT * FROM siteprofiles WHERE id = '1'", $connDBA);
	$siteInfo = mysql_fetch_array($siteInfoGrabber);
?>
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
      		  <div class="catDivider alignLeft">Site Name &amp; Footer</div>
              <div class="stepContent">
      		  <blockquote>
      		    <p>The site name will appear in the title of your site<span class="require">*</span>:</p>
                <blockquote>
                  <p>
                    <input name="name" type="text" id="name" size="50" value="<?php echo stripslashes($siteInfo['siteName']); ?>" autocomplete="off" class="validate[required]" />
                  </p>
                </blockquote>
                <p>The footer is displayed at the bottom-left of each page:</p>
                <blockquote>
                  <p>
                    <textarea name="footer" id="footer" rows="5" cols="45" style="width:450px;"><?php echo stripslashes($siteInfo['siteFooter']); ?></textarea>
                  </p>
                </blockquote>
                <p>
                  <?php submit("modifyMeta", "Submit"); ?>
                  <input name="cancelMeta" type="button" id="cancelMeta" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
				</p>
	  </blockquote>
      </div>
      <div class="catDivider alignLeft">Search Keywords and Information</div>
      <div class="stepContent">
        <blockquote>
          <p>The author of this site, or the name of this organization or company:</p>
          <blockquote>
            <p>
              <input name="author" type="text" id="author" size="50" value="<?php echo stripslashes($siteInfo['author']); ?>" autocomplete="off" />
            </p>
          </blockquote>
          <p>The language of this site (changing this option will not change the language pack of this system):</p>
          <blockquote>
            <p>
              <select name="language" id="language">
                <option <?php if ($siteInfo['language'] == "en-US") {echo " selected=\"selected\"";} ?> value="en-US">English</option>
              </select>
            </p>
          </blockquote>
          <p>Time zone:</p>
          <blockquote>
            <p>
              <select name="timeZone" id="timeZone">
                <option value="America/New_York"<?php if ($siteInfo['timeZone'] == "America/New_York") {echo " selected=\"selected\"";} ?>>Eastern Time Zone</option>
                <option value="America/Chicago"<?php if ($siteInfo['timeZone'] == "Central Time Zone") {echo " selected=\"selected\"";} ?>>Central Time Zone</option>
                <option value="America/Denver"<?php if ($siteInfo['timeZone'] == "America/Denver") {echo " selected=\"selected\"";} ?>>Mountain Time Zone</option>
                <option value="America/Los_Angeles"<?php if ($siteInfo['timeZone'] == "America/Los_Angeles") {echo " selected=\"selected\"";} ?>>Pacific Time Zone</option>
                <option value="America/Juneau"<?php if ($siteInfo['timeZone'] == "America/Juneau") {echo " selected=\"selected\"";} ?>>Alaskan Time Zone</option>
                <option value="Pacific/Honolulu"<?php if ($siteInfo['timeZone'] == "Pacific/Honolulu") {echo " selected=\"selected\"";} ?>>Hawaii-Aleutian Time Zone</option>
              </select>
            </p>
          </blockquote>
          <p>Copyright statement:</p>
          <blockquote>
            <p>
              <textarea name="copyright" id="copyright" cols="45" rows="5" class="noEditorSimple"><?php echo stripslashes($siteInfo['copyright']); ?></textarea>
            </p>
          </blockquote>
          <p>List keywords in the text box below, and <strong>separate each phrase with a comma and a space (e.g. website, my website, www)</strong>:</p>
          <blockquote>
            <p>
              <textarea name="meta" id="meta" cols="45" rows="5" class="noEditorSimple"><?php echo stripslashes($siteInfo['meta']); ?></textarea>
            </p>
          </blockquote>
          <p>Site description:</p>
          <blockquote>
            <p>
              <textarea name="description" id="description" cols="45" rows="5" class="noEditorSimple"><?php echo stripslashes($siteInfo['description']); ?></textarea>
            </p>
          </blockquote>
          <p>
            <input type="submit" name="modifyMeta2" id="modifyMeta2" value="Submit" />
            <input name="cancelMeta2" type="button" id="cancelMeta2" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
          </p>
        </blockquote>
      </div>
      </div>
      </div>
      </form>
<?php } ?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>