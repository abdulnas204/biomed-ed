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
				echo "<email>";
				
				if (!empty($organizations['billingEmail'])) {
					echo $organizations['billingEmail'];
				} else {
					echo "None";
				}
				
				echo "</email>";
				echo "<phone>";
				
				if (!empty($organizations['phone'])) {
					echo $organizations['phone'];
				} else {
					echo "None";
				}
				
				echo "</phone>";
				
				$admin = "";
				
				foreach (explode(",", $organizations['admin']) as $administrator) {
					$adminGrabber = query("SELECT * FROM `users` WHERE `id` = '{$administrator}'");
					$admin .= prepare($adminGrabber['firstName'], false, true) . " " . prepare($adminGrabber['lastName'], false, true) . ", ";
				}
				
				echo "<administrators>" . commentTrim(35, rtrim($admin, ", ")) . "</administrators>";
				echo "</organization>";
			}
			
			echo "</root>";
			exit;
		}
		
	//Top content
		headers("Organizations", "Site Administrator", "liveData", true, false, false, false, false, false, false, "<script type=\"text/javascript\">var dsOrganizations = new Spry.Data.XMLDataSet(\"index.php?data=xml\", \"root/organization\"); var pvOrganizations = new Spry.Data.PagedView(dsOrganizations, {pageSize: 20}); var pvOrganizationsPagedInfo = pvOrganizations.getPagingInfo();</script>");
		
	//Delete an organization	
		if (isset ($_GET['action']) && isset($_GET['id']) && $_GET['action'] == "delete") {
			if (exist("organizations", "id", $_GET['id']) && $_GET['id'] != "0") {
				query("DELETE FROM `organizations` WHERE `id` = '{$_GET['id']}'");
				query("DELETE FROM `users` WHERE `organization` = '{$_GET['id']}'");
				delete("announcements_" . $_GET['id']);
				delete("organizationstatistics_" . $_GET['id']);
				
				if (exist("moduledata", "organization", $_GET['id'])) {
					$moduleGrabber = query("SELECT * FROM `moduledata` WHERE `organization` = '{$_GET['id']}'", "raw");
					
					while ($module = mysql_fetch_array($moduleGrabber)) {
						query("DELETE FROM `organizations` WHERE `id` = '{$_GET['id']}'");
						delete("moduletest_" . $module['id'], false, false, false, "../modules/" . $module['id'], "modulelesson_" . $module['id']);
					}
				}
				
				redirect("index.php");
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
		message("updated", "organization", "success", "The organization was updated");
	
	//Organizations table
		if (exist("organizations")) {
		//Top navigation toolbar
			navigate("pvOrganizations", "top");	
		
		//The loading state
			echo "<div spry:region=\"pvOrganizations dsOrganizations\" spry:loadingstate=\"loadingData\" spry:readystate=\"loaded\">";
			echo "<div spry:state=\"loadingData\" class=\"noResults\">Loading Organizations...</div>";
			
		//Organizations table
			echo "<table spry:state=\"loaded\" spry:if=\"{ds_UnfilteredRowCount} > 0\" class=\"dataTable\"><tr><th width=\"200\" spry:sort=\"name\" class=\"tableHeader\">Name</th><th width=\"150\" spry:sort=\"emailAddress\" class=\"tableHeader\">Email Address</th><th width=\"175\" spry:sort=\"phone\" class=\"tableHeader\">Phone Number</th><th width=\"200\" spry:sort=\"administrators\" class=\"tableHeader\">Administrators</th><th width=\"50\" class=\"tableHeader\">Statistics</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
			echo "<tr spry:repeat=\"pvOrganizations\" spry:odd=\"odd\" spry:even=\"even\">";
			echo "<td width=\"200\">" . URL("{pvOrganizations::name}", "profile.php?id={pvOrganizations::id}") . "</td>";
			echo "<td width=\"150\">{function::email}</td>";
			echo "<td width=\"175\">{function::phone}</td>";
			echo "<td width=\"200\">{pvOrganizations::administrators}</td>";
			echo "<td width=\"50\">" . URL(false,"../statistics/index.php?type=user&period=overall&id={pvOrganizations::id}", "action statistics", false, "View <strong>{pvOrganizations::name}\'s</strong> statistics") . "</td>";
			echo "<td width=\"50\">" . URL(false, "manage_organization.php?id={pvOrganizations::id}", "action edit", false, "Edit <strong>{pvOrganizations::name}</strong>") . "</td>";
			echo "<td width=\"50\">" . URL(false, "index.php?action=delete&id={pvOrganizations::id}", "action delete", false, "Delete <strong>{pvOrganizations::name}</strong>", true) . "</td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			
		//Bottom navigation toolbar
			navigate("pvOrganizations", "bottom");
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
		
		$speciality = "";
		
		foreach (explode(", ", $organizationInfo['specialty']) as $searchKeyword) {
			$speciality .= URL($searchKeyword, "http://www.google.com/search?q=" . urlencode($searchKeyword), false, "_blank") . ", ";
		}
		
		echo rtrim($speciality, ", ") . "</p></blockquote>";
		
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
		
	//Organization billing history
		catDivider("Billing History", "three");
		
		if ($historyGrabber = query("SELECT * FROM `billing` WHERE `ownerOrganization` = '{$organizationInfo['id']}' ORDER BY `id` DESC LIMIT 5", "raw")) {
			$historyCount = query("SELECT * FROM `billing` WHERE `ownerOrganization` = '{$organizationInfo['id']}' ORDER BY `id` DESC LIMIT 5", "num");
			$count = 1;
			
			if ($historyCount == 1) {
				$word = " payment";
			} else {
				$word = " payments";
			}
			
			echo "<blockquote>";
			echo "<p>Showing information from the last " . $historyCount . $word . ".</p>";
			echo "<table class=\"dataTable\"><tbody><tr><th class=\"tableHeader\">Product</th><th width=\"200\" class=\"tableHeader\">Date</th><th width=\"200\" class=\"tableHeader\">Transaction ID</th><th width=\"100\" class=\"tableHeader\">Amount</th><th width=\"50\" class=\"tableHeader\">Details</th></tr>";
			
			while ($history = mysql_fetch_array($historyGrabber)) {
				echo "<tr";
				if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<td>" . unserialize($history['items']) . "</td>";
				echo "<td width=\"200\">" . date("F j, Y", $history['date']) . "</td>";
				echo "<td width=\"200\">" . $history['transactionID'] . "</td>";
				echo "<td width=\"100\">\$" . $history['price'] . "</td>";
				echo "<td width=\"50\">" . URL("", "manage_billing.php?detail=" . $history['id'], "action discover") . "</td>";
				echo "</tr>";
				
				$count++;
			}
			
			echo "</table>";
			
			if ($historyCount > 5) {
				echo "<br />";
				echo URL("View More Transactions", "manage_organization.php");
			}
			
			echo "</blockquote>";
		} else {
			echo "<div class=\"noResults\">This organization does not have any billing history.</div>";
		}
		
		catDivider(false, false, false, true);
	}

//Include the footer
	footer();
?>