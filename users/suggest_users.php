<?php 
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("User Data Collection", "Site Administrator", false, true, false, false, false, false, false, "XML"); 
	
//Export as XML file
	header("Content-type: application/xml");
	
	$userGrabber = mysql_query("SELECT * FROM `users` WHERE `role` = 'Student' OR `role` = 'Instructorial Assisstant' OR `role` = 'Instructor' OR `role` = 'Administrative Assistant' OR `role` = 'Organization Administrator'", $connDBA);
	
	echo "<root>";
	while ($user = mysql_fetch_array($userGrabber)) {
		echo "<name>" . $user['firstName'] . " " . $user['lastName'] . "</name>";
	}
	echo "</root>";
?>