<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
	header("Content-type: text/xml");
	$organizationGrabber = mysql_query("SELECT * FROM organizations", $connDBA);
	
	echo "<root>";
	while ($organization = mysql_fetch_array($organizationGrabber)) {
		echo "<organization>" . $organization['organization'] . "</organization>";
	}
	echo "</root>";
?>