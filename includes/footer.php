<div style="padding-right:10px;">
<?php
    require_once ('/../Connections/connDBA.php');
    
//Display the footer
	$footerGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
			if (!$footerGrabber) {
				errorMessage("Database query failed.");
			}
			
	$footerArray = mysql_fetch_array($footerGrabber);
	$footer = $footerArray['siteFooter'];
	echo stripslashes($footer);
?>
</div>