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
Last updated: December 13th, 2010

This script is to review the learning units which were 
just purchased.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Payment Complete", "printContents");
	
//Empty the shopping cart
	unset($_SESSION['cart']);
	
//Grab the information from the most recent payment
	$paymentInfo = query("SELECT * FROM `billing` WHERE `ownerUser` = '{$userData['id']}' ORDER BY `id` DESC LIMIT 1");

//Title
	title("Payment Complete", "Below are the results of your order. You may now access the learning units you just purchased.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
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
	$paymentArray = array();
	
	foreach (unserialize($paymentInfo['items']) as $item) {
		$data = query("SELECT * FROM `learningunits` WHERE `id` = '{$item['item']}'");
		$price = str_replace(".", "", $data['price']);
		
		echo "<tr align=\"center\" id=\"" . $identifier ."\"";
		if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
		echo cell(URL(prepare($data['name']), "../lesson.php?id=" . $data['id']));
		
		if (!empty($data['price'])) {
			echo cell("\$" . $data['price'], "100");
		} else {
			echo cell("$0.00", "100");
		}
		
		echo cell("1", "75");
		echo "</tr>\n";
		
		array_push($paymentArray, $data['price']);
		unset($data, $price);
		$count++;
	}
	
	echo "</table>\n";
	
	foreach ($paymentArray as $paymentDue) {
		$total = $total + sprintf("%01.2f", $paymentDue);
	}
	
	if ($count != 1) {	
		$quantity = $count - 1;
	} else {
		$quantity = $count;
	}
	
	echo "<hr />\n";
	echo "<table width=\"100%\">\n";
	echo "<tr align=\"center\">\n";
	echo cell("<div align=\"right\"><strong>Total:</strong></div>");
	echo cell("\$" . number_format($total, 2), "100");
	echo cell($quantity, "75");
	echo "</tr>\n";
	echo "</table>\n";
	echo "</blockquote>\n";
	
	catDivider("Finish", "three");
	indent(button("finish", "finish", "Back to Learning Units", "button", "../index.php"));
	echo "</div>\n";
	echo "</div>\n";

//Include the footer
	footer();
?>