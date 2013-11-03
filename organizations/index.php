<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Content for site administrators
	if (access("manageAllOrganizations")) {
	//Export data to XML
		if (isset($_GET['data'])) {
			headers("Organizations Data Collection", "Site Administrator", false, false, false, false, false, false, false, "XML");
			header("Content-type: text/xml");
			$organizationGrabber = query("SELECT * FROM `organizations`", "raw");
			echo "<root>";
			
			while ($organizations = mysql_fetch_array($organizationGrabber)) {
				echo "<organization>";
				echo "<id>" . $organizations['id'] . "</id>";
				echo "<name>" . prepare($organizations['organization'], false, true) . "</name>";
				echo "<email>" . $organizations['billingEmail'] . "</email>";
				echo "<phone>" . $organizations['phone'] . "</phone>";
				echo "<administrators>" . $organizations['admin'] . "</administrators>";
				echo "</organization>";
			}
			
			echo "</root>";
			exit;
		}
		
	//Top content
		headers("Organizations", "Site Administrator", "liveData", true, false, false, false, false, false, false, "<script type=\"text/javascript\">var dsOrganizations = new Spry.Data.XMLDataSet(\"index.php?data=xml\", \"root/organization\"); var pvOrganizations = new Spry.Data.PagedView(dsOrganizations, {pageSize: 20}); var pvOrganizationsPagedInfo = pvOrganizations.getPagingInfo();</script>");
		
	//Delete an organization	
		if (isset ($_GET['action']) && isset ($_GET['id']) && $_GET['action'] == "delete") {
			$id = $_GET['id'];
			$organizationCheck = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
			if ($organization = mysql_fetch_array($organizationCheck)) {
				$organizationID = $organization['id'];
				mysql_query("DELETE FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
				mysql_query("DELETE FROM `users` WHERE `organization` = '{$organizationID}'", $connDBA);
				
				header ("Location: index.php");
				exit;
			} else {
				header ("Location: index.php");
				exit;
			}
		}
		
	//Title
		title("Organizations", "Below is a list of all organizations registered within this system. Organizations may be sorted according to a certain criteria by clicking on the text in the header row of the desired column.");
		
	//Admin toolbar
		echo "<div class=\"toolBar\">";
		echo URL("Add New Organization", "manage_organization.php", "toolBarItem new");
		echo URL("Manage Billing", "manage_billing.php", "toolBarItem billing");
		echo URL("Search for Organizations", "javascript:void", "toolBarItem search");
		echo "</div>";
	
	//Display message updates
		message("inserted", "organization", "success", "The organization was created");
		message("updated", "organization", "success", "The organization was created");
	
	//Organizations table
		if (exist("organizations")) {
		//Top navigation toolbar
			navigate("dsOrganizations", "top");	
		
		//The loading state
			echo "<div spry:region=\"pvOrganizations dsOrganizations\" spry:loadingstate=\"loadingData\" spry:readystate=\"loaded\">";
			echo "<div spry:state=\"loadingData\" class=\"noResults\">Loading Organizations...</div>";
			
		//Organizations table
			echo "<table spry:state=\"loaded\" spry:if=\"{ds_UnfilteredRowCount} > 0\" class=\"dataTable\"><tbody><tr><th width=\"200\" spry:sort=\"name\" class=\"tableHeader\">Name</th><th width=\"150\" spry:sort=\"emailAddress\" class=\"tableHeader\">Email Address</th><th width=\"175\" spry:sort=\"phone\" class=\"tableHeader\">Phone Number</th><th width=\"200\" spry:sort=\"administrators\" class=\"tableHeader\">Administrators</th><th width=\"50\" class=\"tableHeader\">Statistics</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
			echo "<tr spry:repeat=\"pvOrganizations\" spry:odd=\"odd\" spry:even=\"even\">";
			echo "<td width=\"200\">{pvOrganizations::name}</td>";
			echo "<td width=\"150\">" . URL("{pvOrganizations::email}", "../communication/send_email.php?type=organization&id={pvOrganizations::id}") . "</td>";
			echo "<td width=\"175\">{pvOrganizations::phone}</td>";
			echo "<td width=\"200\">{pvOrganizations::administrators}</td>";
			echo "<td width=\"50\">" . URL(false,"../statistics/index.php?type=user&period=overall&id={pvOrganizations::id}", "action statistics", false, "View <strong>{pvOrganizations::name}\'s</strong> statistics") . "</td>";
			echo "<td width=\"50\">" . URL(false, "manage_organization.php?id={pvOrganizations::id}", "action edit", false, "Edit <strong>{pvOrganizations::name}</strong>") . "</td>";
			echo "<td width=\"50\">" . URL(false, "index.php?action=delete&id={pvOrganizations::id}", "action delete", false, "Delete <strong>{pvOrganizations::name}</strong>", true) . "</td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			
		//Bottom navigation toolbar
			navigate("dsOrganizations", "bottom");
		} else {
			echo "<div class=\"noResults\">No organizations exist</div>";
		}
//Content for organization administrators
	} else {
	//Grab the organization information
		$userInfo = userData();
		$organizationInfo = query("SELECT * FROM `organizations` WHERE `id` = '{$userInfo['organization']}'");
		
	//Top content
		headers(prepare($organizationInfo['organization'], false, true), "Organization Administrator");
		
	//Title
		title(prepare($organizationInfo['organization'], false, true), "Below is information relevant to " . prepare($organizationInfo['organization'], false, true) . ".");
		
	//Admin toolbar
		echo "<div class=\"toolBar\">";
		echo URL("Manage Billing", "manage_billing.php", "toolBarItem billing");
		echo URL("Edit Content", "manage_organization.php", "toolBarItem editTool");
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
				case "Pacific/Honolulu" : echo "Hawaii-Aleutian Time Zone"; break;
			}
			
		echo "</p></blockquote>";
		
		echo "</blockquote>";
		
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
	}

//Include the footer
	footer();
?>