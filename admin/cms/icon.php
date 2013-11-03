<?php
/*
LICENSE: See "license.php" located at the root installation

This is the logo management page.
*/

//Header functions
	require_once('../../system/server/index.php');
	headers("Shortcut Icon Management", "validate", true);
	lockAccess();
	
//Grab the needed information
	$iconData = query("SELECT * FROM `siteprofiles` WHERE id = '1'");
	
//Upload a logo
	if (isset($_POST['submitIcon'])) {
		$tempFile = $_FILES['iconUpload']['tmp_name'];
		$targetFile = basename($_FILES['iconUpload']['name']);
		$uploadDir = "../../system/images";
		$allowedMIMEs = array("ico", "jpg", "gif");
		
		if (in_array(extension($targetFile), $allowedMIMEs)) {
			$iconType = extension($targetFile);
			
			if (move_uploaded_file($tempFile, $uploadDir . "/" . "icon." . $iconType)) {
				query("UPDATE `siteprofiles` SET `iconType` = '{$iconType}' WHERE id = '1'");
				redirect("index.php?updated=icon");
			} else {
				redirect($_SERVER['PHP_SELF'] . "?error=upload");
			}
		} else {
			redirect($_SERVER['PHP_SELF'] . "?error=fileType");
		}
	}
	
//Title
	title("Shortcut Icon Management", "Modify the shortcut icon displayed on the top-left of the browser window or the current tab. A shortcut icon may have one of the following extenstions : &quot;.ICO&quot;, &quot;.JPG&quot;, or &quot;.GIF&quot;.");
	
//Display message updates
	message("error", "fileType", "error", "This is an unsupported file type. Supported types have one of the following extensions: &quot.ICO&quot;, &quot.JPG&quot;, or &quot.GIF&quot;.");
	message("error", "upload", "error", "There was an error when uploading the file. Ensure you did not cancel the upload before it was finished, and be sure your file is smaller than the maxmium file size displayed below the file field.");
	
//Logo upload form
	echo catDivider("Shortcut Icon", "alignLeft", true);
	echo form("icon", "post", true);
	echo "<blockquote>\n";
	echo directions("Shortcut Icon", true, "Upload a shortcut icon to display on the top-left of the browser window.<br />Acceptable formats are: &quot.ICO&quot;, &quot.JPG&quot;, or &quot.GIF&quot;.");
	indent("Current file: " . URL("icon." . $iconData['iconType'], "../../system/images/icon." . $iconData['iconType'], false, "_blank") . "<br />\n" . fileUpload("iconUpload", "iconUpload", false, true, "funcCall[iconCheck]"));
	echo button("submitIcon", "submitIcon", "Upload Icon", "submit");
	echo "</blockquote>\n";
	echo closeForm(false);
	echo "</div>\n";
	
//Include the footer
	footer();
?>