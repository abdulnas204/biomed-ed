<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Export data to XML
	if (isset($_GET['data'])) {
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
	headers("Organizations", "Site Administrator", "liveData", true, false, false, false, false, false, false, "<script type=\"text/javascript\">var dsOrganizations = new Spry.Data.XMLDataSet(\"index.php?data=xml\", \"root/organization\"); var pvOrganizations = new Spry.Data.PagedView(dsOrganizations, { pageSize: 20 }); var pvOrganizationsPagedInfo = pvOrganizations.getPagingInfo();</script>");
	
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

//If no organizations exist	
	if (exist("organizations")) {
	//Top navigation toolbar
		echo "<div class=\"pagesBox\" spry:region=\"pvOrganizationsPagedInfo dsOrganizations\" spry:if=\"{ds_UnfilteredRowCount} > 0\" spry:repeatchildren=\"pvOrganizationsPagedInfo\">";
		echo URL("{ds_PageNumber}", "javascript:void", false, false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber}\" onclick=\"pvOrganizations.goToPage('{ds_PageNumber}'); return false;\"");
		echo "<span spry:if=\"{ds_CurrentRowNumber} == {ds_RowNumber}\" class=\"currentPage\">{ds_PageNumber}</span>";
		echo "</div><br />";
		
	//Organizations table
		echo "<table spry:region=\"pvOrganizations dsOrganizations\" spry:if=\"{ds_UnfilteredRowCount} > 0\" class=\"dataTable\"><tbody><tr><th width=\"200\" spry:sort=\"name\" class=\"tableHeader\">Name</th><th width=\"150\" spry:sort=\"emailAddress\" class=\"tableHeader\">Email Address</th><th width=\"175\" spry:sort=\"phone\" class=\"tableHeader\">Phone Number</th><th width=\"200\" spry:sort=\"administrators\" class=\"tableHeader\">Administrators</th><th width=\"50\" class=\"tableHeader\">Statistics</th><th width=\"50\" class=\"tableHeader\">Edit</th><th width=\"50\" class=\"tableHeader\">Delete</th></tr>";
		echo "<tr spry:repeat=\"pvOrganizations\" spry:odd=\"odd\" spry:even=\"even\">";
		echo "<td width=\"200\">{pvOrganizations::name}</td>";
		echo "<td width=\"150\">" . URL("{pvOrganizations::email}", "../communication/send_email.php?type=organization&id={pvOrganizations::id}") . "</td>";
		echo "<td width=\"175\">{pvOrganizations::phone}</td>";
		echo "<td width=\"200\">{pvOrganizations::administrators}</td>";
		echo "<td width=\"50\">" . URL(false,"../statistics/index.php?type=user&period=overall&id={pvOrganizations::id}", "action statistics", false, "Tip('View <strong>{pvOrganizations::name}\'s</strong> statistics')") . "</td>";
		echo "<td width=\"50\">" . URL(false, "manage_organization.php?id={pvOrganizations::id}", "action edit", false, "Edit <strong>{pvOrganizations::name}</strong>')") . "</td>";
		echo "<td width=\"50\">" . URL(false, "index.php?action=delete&id={pvOrganizations::id}", "action delete", false, "Delete <strong>{pvOrganizations::name}</strong>'", true) . "</td>";
		echo "</tr>";
		echo "</table>";
		
	//Bottom navigation toolbar
		echo "<br /><div class=\"pagesBox\" spry:region=\"pvOrganizationsPagedInfo dsOrganizations\" spry:if=\"{ds_UnfilteredRowCount} > 0\" spry:repeatchildren=\"pvOrganizationsPagedInfo\">";
		echo URL("{ds_PageNumber}", "javascript:void", false, false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber}\" onclick=\"pvOrganizations.goToPage('{ds_PageNumber}'); return false;\"");
		echo "<span spry:if=\"{ds_CurrentRowNumber} == {ds_RowNumber}\" class=\"currentPage\">{ds_PageNumber}</span>";
		echo "</div>";
	} else {
		echo "<div class=\"noResults\">No organizations exist</div>";
	}

//Include the footer
	footer();
?>