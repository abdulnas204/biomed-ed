<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grant access to this page if aa organization is defined and the organization exists
	if (exist("organizations", "id", $_GET['id'])) {
		$organization = query("SELECT * FROM `organizations` WHERE `id` = '{$_GET['id']}'");
	} else {
		redirect("../portal/index.php");
	}
	
	headers($organization['organization'], "Site Administrator");
	
//Title
	title($organization['organization'], false);
	
//Admin toolbar
	echo "<div class=\"toolBar\">";
	echo URL("Edit this Organization", "manage_organization.php?id=" . $organization['id'], "toolBarItem editTool");
	echo URL("Delete this Organization", "index.php?action=delete&id=" . $organization['id'], "toolBarItem deleteTool", false, false, true);
	echo "</div><br />";
	
//Organization Information
	catDivider("Organization Information", "one", true);
	echo "<blockquote>";
	directions("Administrators");
	echo "<blockquote>";
	
	$administrators = "<p>";
	
	foreach (explode(",", $organizationInfo['admin']) as $administratorID) {
		$administratorData = query("SELECT * FROM `users` WHERE `id` = '{$administratorID}'");
		$administrators .= URL($administratorData['firstName'] . " " . $administratorData['lastName'], "../users/profile.php?id=" . $administratorData['id']) . ", ";
	}
	
	echo rtrim($administrators, ", ") . "</p>";
	echo "</blockquote>";
	directions("Contract Start");
	echo "<blockquote><p>";
	echo date("F j, Y", $organizationInfo['contractStart']);
	echo "</p></blockquote>";
	directions("Contract End");
	echo "<blockquote><p>";
	echo date("F j, Y", $organizationInfo['contractEnd']);
	echo "<br />";
	echo dateDifference($organizationInfo['contractEnd'], strtotime("now"), "days") . " Until Expires";
	echo "</p></blockquote>";
	directions("Specialty");
	echo "<blockquote><p>";
	echo URL($organizationInfo['specialty'], "http://www.google.com/search?q=" . urlencode($organizationInfo['specialty']), false, "_blank");
	echo "</p></blockquote>";
	
	if (!empty($organizationInfo['webSite'])) {
		directions("Website");
		echo "<blockquote><p>";
		echo URL($organizationInfo['webSite'], $organizationInfo['webSite'], false, "_blank");
		echo "</p></blockquote>";
	}
	
	directions("Time Zone");
	echo "<blockquote><p>";
		
		switch($organizationInfo['timeZone']) {
			case "America/New_York" : echo "Eastern Time Zone"; break;
			case "America/Chicago" : echo "Central Time Zone"; break;
			case "America/Denver" : echo "Mountain Time Zone"; break;
			case "America/Los_Angeles" : echo "Pacific Time Zone"; break;
			case "America/Juneau" : echo "Alaskan Time Zone"; break;
			case "Pacific/Honolulu" : echo "Hawaii-Aleutian Time Zone"; break;
		}
		
	echo "</p></blockquote>";
	
	echo "</blockquote>";
	
//Organization contact information
	catDivider("Contact Information", "two");
	echo "<blockquote>";
	directions("Phone Number");
	echo "<blockquote><p>";
	echo $organizationInfo['phone'];
	echo "</p></blockquote>";
	directions("Fax Number");
	echo "<blockquote><p>";
	echo $organizationInfo['fax'];
	echo "</p></blockquote>";
	directions("Address");
	echo "<blockquote><p>";
	
	if ($organizationInfo['mailingAddress2'] != "") {
		$link = $organizationInfo['mailingAddress2'] . " ";
		$moreAddress = "<br />" . $organizationInfo['mailingAddress2'];
	}	else {
		$link = "";
		$moreAddress = "";
	}
	
	$URL = "http://maps.google.com/maps?q=" . urlencode($organizationInfo['mailingAddress1'] . " " . $link . $organizationInfo['mailingCity'] . ", " . $organizationInfo['mailingState'] . " " . $organizationInfo['mailingZIP']);
	$text = $organizationInfo['mailingAddress1'] . $moreAddress . "<br />" . $organizationInfo['mailingCity'] . ", " . $organizationInfo['mailingState'] . " " . $organizationInfo['mailingZIP'];
	
	echo URL($text, $URL, false, "_blank");
	echo "</p></blockquote>";
	directions("Billing Phone Number");
	echo "<blockquote><p>";
	echo $organizationInfo['billingPhone'];
	echo "</p></blockquote>";
	directions("Billing Fax Number");
	echo "<blockquote><p>";
	echo $organizationInfo['billingFax'];
	echo "</p></blockquote>";
	directions("Billing Address");
	echo "<blockquote><p>";
	
	if ($organizationInfo['billingAddress2'] != "") {
		$link = $organizationInfo['billingAddress2'] . " ";
		$moreAddress = "<br />" . $organizationInfo['billingAddress2'];
	}	else {
		$link = "";
		$moreAddress = "";
	}
	
	$URL = "http://maps.google.com/maps?q=" . urlencode($organizationInfo['billingAddress1'] . " " . $link . $organizationInfo['billingCity'] . ", " . $organizationInfo['billingState'] . " " . $organizationInfo['billingZIP']);
	$text = $organizationInfo['billingAddress1'] . $moreAddress . "<br />" . $organizationInfo['billingCity'] . ", " . $organizationInfo['billingState'] . " " . $organizationInfo['billingZIP'];
	
	echo URL($text, $URL, false, "_blank");
	echo "</p></blockquote>";
	directions("Billing Email Address");
	echo "<blockquote><p>";
	echo URL($organizationInfo['billingEmail'], "../communication/send_email.php?type=organization&id=" . $organizationInfo['id'] . "&limit=billing");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Submit", "five");
	echo "<blockquote><p>";
	button("cancel", "cancel", "Cancel", "cancel", "index.php");
	echo "</p></blockquote>";
	catDivider(false, false, false, true);
	
//Include the footer
	footer();
?>