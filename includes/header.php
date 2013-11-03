<?php	
	$imagePaddingGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);	
	$imagePaddingArray = mysql_fetch_array($imagePaddingGrabber);
	$imagePaddingTop = $imagePaddingArray['paddingTop'];
	$imagePaddingBottom = $imagePaddingArray['paddingBottom'];
	$imagePaddingLeft = $imagePaddingArray['paddingLeft'];
	$imagePaddingRight = $imagePaddingArray['paddingRight'];
	$imageWidth = $imagePaddingArray['width'];
	$imageHeight = $imagePaddingArray['height'];

?>
<div style="padding-top:<?php echo $imagePaddingTop; ?>px; padding-bottom:<?php echo $imagePaddingBottom; ?>px; padding-left:<?php echo $imagePaddingLeft; ?>px; padding-right:<?php echo $imagePaddingRight; ?>px;">
<?php 
	if (isset ($_SESSION['MM_UserGroup'])) {
		switch($_SESSION['MM_UserGroup']) {
			case "Student": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/student/index.php\">"; break;
			case "Instructor": echo "<a href=\"http://\"" . $_SERVER['HTTP_HOST'] . "/biomed-ed/instructor/index.php\">"; break;
			case "Organization Administrator": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/administrator/index.php\">"; break;
			case "Site Administrator": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/site_administrator/index.php\">"; break;
			case "Advertiser": echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/advertiser/index.php\">"; break;
		}
	} else {
		echo "<a href=\"http://" . $_SERVER['HTTP_HOST'] . "/biomed-ed/index.php\">";
	}
?>
<img src="<?php echo "http://" .  $_SERVER['HTTP_HOST'] . "/biomed-ed/images/banner.png"?>" width="<?php echo $imageWidth; ?>" height="<?php echo $imageHeight; ?>" alt="<?php echo $siteName['siteName']; ?>" border="0">
</a>
</div>