<?php
/*
LICENSE: See "license.php" located at the root installation

This is the logo management page.
*/

//Header functions
	require_once('../../system/core/index.php');
	headers("Logo Management", "validate,enableDisable", true);
	lockAccess();
	
//Grab the needed information
	$logoData = query("SELECT * FROM `siteprofiles` WHERE id = '1'");
	
//Upload a logo
	if (isset($_POST['submitLogo'])) {
		$tempFile = $_FILES['logoUpload']['tmp_name'];
		$targetFile = basename($_FILES['logoUpload']['name']);
		$uploadDir = "../../system/images";
		$allowedMIMEs = array("png", "bmp", "jpg", "gif");
		
		if (in_array(extension($targetFile), $allowedMIMEs)) {
			if (move_uploaded_file($tempFile, $uploadDir . "/" . "banner.png")) {
				redirect("index.php?updated=logo");
			} else {
				redirect($_SERVER['PHP_SELF'] . "?error=upload");
			}
		} else {
			redirect($_SERVER['PHP_SELF'] . "?error=fileType");
		}
	}
	
//Modify the logo's padding attributes
	if (isset($_POST['updatePlacement'])) {
		$top = $_POST['top'];
		$bottom = $_POST['bottom'];
		$left = $_POST['left'];
		$right = $_POST['right'];
				
		query("UPDATE `siteprofiles` SET `paddingTop` = '{$top}', `paddingBottom` = '{$bottom}', `paddingLeft` = '{$left}', `paddingRight` = '{$right}' WHERE `id` = '1'");
		redirect("index.php?updated=logo");
	}
	
//Modify the logo's size
	if (isset ($_POST['updateSize'])) {		
		$auto = $_POST['automatic'];
		$imageHeight = $_POST['height'];
		$imageWidth = $_POST['width'];
		
		query("UPDATE `siteprofiles` SET `height` = '{$imageHeight}', `width` = '{$imageWidth}', `auto` = '{$auto}'");
		redirect("index.php?updated=logo");
	}
	
//Title
	title("Logo Management", "Here you can make changes to the logo by changing its size, position, as well as the logo itself.");
	
//Display message updates
	message("error", "fileType", "error", "This is an unsupported file type. Supported types have one of the following extensions: &quot.PNG&quot;, &quot.BMP&quot;, &quot.JPG&quot;, or &quot.GIF&quot;.");
	message("error", "upload", "error", "There was an error when uploading the file. Ensure you did not cancel the upload before it was finished, and be sure your file is smaller than the maxmium file size displayed below the file field.");
	
//Logo upload form
	echo catDivider("Site Logo", "alignLeft", true);
	echo form("logo", "post", true);
	echo "<blockquote>\n";
	echo directions("Site Logo", true, "Upload a logo to display in the site's header.<br />Acceptable formats are: &quot.PNG&quot;, &quot.BMP&quot;, &quot.JPG&quot;, or &quot.GIF&quot;.");
	indent("Current file: " . URL("banner.png", "../../system/images/banner.png", false, "_blank") . "<br />\n" . fileUpload("logoUpload", "logoUpload", false, true, "funcCall[logoCheck]"));
	echo button("submitLogo", "submitLogo", "Upload Logo", "submit");
	echo "</blockquote>\n";
	echo closeForm(false);
	
//Logo placement form
	echo catDivider("Logo Placement", "alignLeft");
	echo form("padding");
	echo "<blockquote>\n";
	echo directions("Logo Placement", true, "Set the distance in pixels the logo <br />should be placed from a respective edge.");
	echo "<blockquote>\n";
	echo "<p><strong>Top:</strong> " . textField("top", "top", "3", "3", false, true, "custom[onlyNumber]", false, "logoData", "paddingTop") . "px<br />\n";
	echo "<strong>Left:</strong> " . textField("left", "left", "3", "3", false, true, "custom[onlyNumber]", false, "logoData", "paddingLeft") . "px<br />\n";
	echo "<strong>Right:</strong> " . textField("right", "right", "3", "3", false, true, "custom[onlyNumber]", false, "logoData", "paddingRight") . "px<br />\n";
	echo "<strong>Bottom:</strong> " . textField("bottom", "bottom", "3", "3", false, true, "custom[onlyNumber]", false, "logoData", "paddingBottom") . "px</p>\n";
	echo "</blockquote>\n";
	echo button("updatePlacement", "updatePlacement", "Update Placement", "submit");
	echo "</blockquote>\n";
	echo closeForm(false);
	
//Logo height form
	if ($logoData['auto'] == "on") {
		$disabled = "disabled=\"disabled\"";
	} else {
		$disabled = "";
	}
	
	echo catDivider("Logo Size", "alignLeft");
	echo form("size");
	echo "<blockquote>\n";
	echo directions("Logo Size", true, "Set the height and width of the logo, <br />or have it size automatically.");
	echo "<blockquote>\n";
	echo "<p><strong>Width:</strong> " . textField("width", "width", "3", "3", false, false, "custom[onlyNumber]", false, "logoData", "width", $disabled) . "px<br />\n";
	echo "<strong>Height:</strong> " . textField("height", "height", "3", "3", false, false, "custom[onlyNumber]", false, "logoData", "height", $disabled) . "px\n<br /><br />\n";
	echo checkBox("automatic", "automatic", "Automatic", false, false, false, false, "logoData", "auto", "on", "onclick=\"flvFTFO1('size','width,t','height,t')\"");
	echo "</blockquote>\n";
	echo button("updateSize", "updateSize", "Update Size", "submit");
	echo "</blockquote>\n";
	echo closeForm(false);
	echo "</div>\n";

//Include the footer
	footer();
?>