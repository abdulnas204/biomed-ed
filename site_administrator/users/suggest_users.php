<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
	header("Content-type: text/xml");
	$userGrabber = mysql_query("SELECT * FROM users WHERE `role` = 'Student' OR `role` = 'Instructorial Assisstant' OR `role` = 'Instructor' OR `role` = 'Administrative Assistant' OR `role` = 'Organization Administrator'", $connDBA);
	
	echo "<root>";
	while ($user = mysql_fetch_array($userGrabber)) {
		echo "<name>" . $user['firstName'] . " " . $user['lastName'] . "</name>";
	}
	echo "</root>";
?>