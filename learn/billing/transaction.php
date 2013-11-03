<?php
/*
LICENSE: See "license.php" located at the root installation

This script enables to view an overview of relevant transactions, as well as the details of each transaction.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');
	
//Empty the shopping cart
	unset($_SESSION['cart']);
	
	if (!isset($_GET['txn_id'])) {
		redirect("index.php");
	}
	
//Check the status of a payment
	if (isset($_GET['action']) && $_GET['action'] == "checkStatus") {
		//Grab the payment information
			if ($paymentInfo = query("SELECT * FROM `billing` WHERE `ownerUser` = '{$userData['id']}' AND `transactionID` = '{$_GET['txn_id']}'", false, false)) {
			//Title
				title(false, "Below are the results of your order. You may now access the learning units you just purchased.");
				
			//Admin toolbar
				echo "<div class=\"toolBar\">\n";
				echo toolBarURL("Back to Learning Units", "../index.php", "toolBarItem back");
				echo URL("Print Recipt", "javascript:void", "toolBarItem print", false, false, false, false, false, false, "onclick=\"printContents('printTarget');\"");
				echo "</div>\n<br />\n";
				
			//Payment review
				echo "<div id=\"printTarget\">\n";
				catDivider("Transaction Information", "one", true);
				echo "<blockquote>\n";
				directions("Name used during payment");
				indent($userData['firstName'] . " " . $userData['lastName']);
				directions("Total");
				indent("\$" . $paymentInfo['total']);
				directions("Tax");
				indent("\$" . $paymentInfo['tax'] . " (" . round(($paymentInfo['tax'] / $paymentInfo['total']) * 100) . "%)");
				directions("Transaction date");
				indent(date("l, F jS, Y \a\\t h:i:s A", $paymentInfo['date']));
				directions("Payment gateway transaction ID");
				indent($paymentInfo['transactionID']);
				directions("Payer email");
				indent(URL($paymentInfo['payerEmail'], "mailto:" . $paymentInfo['payerEmail']));
				directions("Merchant email");
				indent(URL($paymentInfo['businessEmail'], "mailto:" . $paymentInfo['businessEmail']));
				echo "</blockquote>\n";
				
				catDivider("Purchased Items", "two");
				echo "<blockquote>\n";
				echo "<table class=\"dataTable\">\n<tr>\n";
				echo column("Name");
				echo column("Price", "100");
				echo column("Quantity", "75");
				echo "</tr>\n";
				
				$count = 1;
				
				foreach (arrayRevert($paymentInfo['items']) as $item) {
					$data = query("SELECT * FROM `learningunits` WHERE `id` = '{$item['item']}'");
					$price = str_replace(".", "", $data['price']);
					
					echo "<tr align=\"center\"\"";
					if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
					echo cell(URL(prepare($data['name']), "../lesson.php?id=" . $data['id']));
					
					if (!empty($data['price'])) {
						echo cell("\$" . $data['price'], "100");
					} else {
						echo cell("$0.00", "100");
					}
					
					echo cell("1", "75");
					echo "</tr>\n";
					
					$count++;
				}
				
				echo "</table>\n";
				
				$total = $paymentInfo['total'];
				$quantity = sizeof(arrayRevert($paymentInfo['items']));
				
				echo "<hr />\n";
				echo "<table width=\"100%\">\n";
				echo "<tr align=\"center\">\n";
				echo cell("<div align=\"right\"><strong>Total:</strong></div>");
				echo cell("\$" . number_format($total, 2), "100");
				echo cell($quantity, "75");
				echo "</tr>\n";
				echo "</table>\n";
				echo "</blockquote>\n";
				echo "</div>\n";
				echo "</div>\n";
			} else {
				echo "not processed";
			}
			
		exit;
	}
	
//Top content
	headers("Payment Details", "paymentStatus,printContents", false, "onload=\"paymentStatus();\"");
	
//Title
	title("Payment Details");
	
//Payment details, which will be loaded by jQuery once the payment has been processed
	echo "<div id=\"status\">\n<div align=\"center\">\nPlease wait while your payment is processed. This process can take up to one minute. Thank you for your patience.\n<br /><br />\n<img src=\"../system/images/common/loader.gif\" width=\"220\" height=\"19\" />\n<br /><br /><br /><br /><br /><br />\n</div>\n</div>\n";
	
//Include the footer
	footer();
?>