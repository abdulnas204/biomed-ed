<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: December 12th, 2010
Last updated: February 14th, 2011

This script enables to view an overview of relevant 
transactions, as well as the details of each transaction.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Transactions", "printContents");
	
//Display the overview of the all relevant transactions
	if (!isset($_GET['transaction'])) {		
		if (exist("billing", "ownerUser", $userData['id'])) {	
		//Title
			title("Transactions", "Below is an overview of all financial transactions that have taken place for this account.");
				
		//Admin toolbar
			echo "<div class=\"toolBar\">\n";
			echo toolBarURL("Back to Learning Units", "../index.php", "toolBarItem back");
			echo "</div>\n<br />\n";
				
		//Transaction table
			$paymentInfo = query("SELECT * FROM `billing` WHERE `ownerUser` = '{$userData['id']}' ORDER BY `id` DESC", "raw");
			$count = 1;
			
			echo "<table class=\"dataTable\">\n";
			echo "<tr>\n";
			echo column("Items");
			echo column("Transaction ID", "150");
			echo column("Date", "250");
			echo column("Total", "100");
			echo column("", "50");
			echo "</tr>\n";
			
			while($payment = fetch($paymentInfo)) {
				echo "<tr";
				if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
				
				$items = "";
				
				foreach(unserialize($payment['items']) as $item) {
					if ($itemName = exist("learningunits", "id", $item['item'])) {
						$items .= $itemName['name'] . ", ";
					} else {
						$items .= "<em>Unit Deleted</em>, ";
					}
				}
				
				echo cell(commentTrim(150, rtrim($items, ", "), "<em>"));
				echo cell($payment['transactionID']);
				echo cell(date("F j, Y", $payment['date']));
				echo cell("\$" . $payment['total']);
				echo cell(URL("", "index.php?transaction=" . $payment['transactionID'], "action discover"));
				echo "</tr>\n";
				
				$count++;
			}
			
			echo "</table>\n";
		} else {
		//Title
			title("Transactions", "Below is an overview of all financial transactions that have taken place for this account.");
				
		//Admin toolbar
			echo "<div class=\"toolBar\">\n";
			echo toolBarURL("Back to Learning Units", "../index.php", "toolBarItem back");
			echo "</div>\n<br />\n";
			
			echo "<div class=\"noResults\">You do not currently have any billing history.</div>\n";
		}
	} else {		
	//Grab the payment information
		if ($paymentInfo = query("SELECT * FROM `billing` WHERE `ownerUser` = '{$userData['id']}' AND `transactionID` = '{$_GET['transaction']}'", false, false)) {
			//Do nothing
		} else {
			redirect("index.php");
		}
		
	//Title
		title("Transactions", "Below are the details of order number <strong>" . $_GET['transaction'] . "</strong>.");
		
	//Admin toolbar
		echo "<div class=\"toolBar\">\n";
		echo toolBarURL("Back to Billing History", "index.php", "toolBarItem back");
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
		
		foreach (unserialize($paymentInfo['items']) as $item) {
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
		$quantity = sizeof(unserialize($paymentInfo['items']));
		
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
	}

//Include the footer
	footer();
?>