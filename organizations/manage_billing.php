<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	
//Grant access to this page if an organization is defined or if this is the present organization
	if (isset($_GET['id']) && access("manageAllOrganizations")) {
		if (exist("organizations", "id", $_GET['id'])) {
			$organization = query("SELECT * FROM `organizations` WHERE `id` = '{$_GET['id']}'");
			$title = prepare($organization['organization'], false, true) . " Billing";
		} else {
			redirect("index.php");
		}
	} else {
		$userInfo = userData();
		$organization = query("SELECT * FROM `organizations` WHERE `id` = '{$userInfo['organization']}'");
		
		if (!isset($_GET['detail'])) {
			$title = $organization['organization'] . " Billing";
			$description = "Below is the billing history for " . prepare($organization['organization'], false, true) . ".";
		} elseif (isset($_GET['detail']) && exist("billing", "id", $_GET['detail'])) {
			$billingInfo = query("SELECT * FROM `billing` WHERE `id` = '{$_GET['detail']}'");
			$title = "Transaction Details";
			$description = "Below are the transaction details for the item ID of " . $billingInfo['transactionID'] . ".";
		}
	}
	
	headers($title, "Organization Administrator,Site Administrator");
	
//Title
	title($title, $description);
	
//If the billing overview is being displayed
	if (!isset($_GET['detail'])) {	
	//Admin toolbar
		echo "<div class=\"toolBar\">";
		echo URL("Back to Organization Overview", "index.php", "toolBarItem back");
		echo "</div><br />";
		
	//Billing history overview
		if ($historyGrabber = query("SELECT * FROM `billing` WHERE `ownerOrganization` = '{$organization['id']}' ORDER BY `id` DESC", "raw")) {
			$historyCount = query("SELECT * FROM `billing` WHERE `ownerOrganization` = '{$organization['id']}' ORDER BY `id` DESC", "num");
			$count = 1;
			$total = 0.00;
			
			echo "<table class=\"dataTable\"><tbody><tr><th class=\"tableHeader\">Transaction Type</th><th width=\"200\" class=\"tableHeader\">Date</th><th width=\"200\" class=\"tableHeader\">Transaction ID</th><th width=\"100\" class=\"tableHeader\">Amount</th><th width=\"50\" class=\"tableHeader\">Details</th></tr>";
			
			while ($history = mysql_fetch_array($historyGrabber)) {
				echo "<tr";
				if ($count & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<td>" . unserialize($history['items']) . "</td>";
				echo "<td width=\"200\">" . date("F j, Y", $history['date']) . "</td>";
				echo "<td width=\"200\">" . $history['transactionID'] . "</td>";
				echo "<td width=\"100\">\$" . $history['price'] . "</td>";
				echo "<td width=\"50\">" . URL("", "manage_billing.php?detail=" . $history['id'], "action discover") . "</td>";
				echo "</tr>";
				
				$total = sprintf("%01.2f", $total + $history['price']);
				$count++;
			}
			
			echo "</table>";
			echo "<hr />";
			
			if ($historyCount == 1) {
				$word = " payment";
			} else {
				$word = " payments";
			}
			
			echo "<table width=\"100%\"><tr align=\"center\"><td></td><td width=\"200\"></td><td width=\"200\">" . $historyCount . $word . "</td><td width=\"100\">$" . $total . "</td><td width=\"50\">" . $quantity . "</td></tr></table>";
			
			if ($historyCount > 5) {
				echo "<br />";
				echo URL("View More Transactions", "manage_organization.php");
			}
		} else {
			echo "<div class=\"noResults\">This organization does not have any billing history.</div>";
		}
	} elseif (isset($_GET['detail']) && exist("billing", "id", $_GET['detail'])) {
	//Admin toolbar
		echo "<div class=\"toolBar\">";
		echo URL("Back to Billing History", "manage_billing.php", "toolBarItem back");
		echo "</div><br />";
		
	//Billing history details
		echo "<blockquote>";
		directions("Transaction Type");
		echo "<blockquote><p>Payment</p></blockquote>";
		directions("Product");
		echo "<blockquote><p>";
		echo unserialize($billingInfo['items']);
		echo "</p></blockquote>";
		directions("Amount");
		echo "<blockquote><p>";
		echo "$" . $billingInfo['price'] . " USD";
		echo "</p></blockquote>";
		directions("Transaction Date and Time");
		echo "<blockquote><p>";
		echo date("l, F jS, Y \a\\t h:i:s A", $billingInfo['date']);
		echo "</p></blockquote>";
		directions("Billed To");
		echo "<blockquote><p>";
		echo prepare($organization['organization'], false, true);
		echo "</p></blockquote>";
		directions("Transaction ID");
		echo "<blockquote><p>";
		echo $billingInfo['transactionID'];
		echo "</p></blockquote></blockquote>";
	} else {
		redirect("manage_billing.php");
	}
//Include the footer
	footer();
?>