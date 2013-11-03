<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: August 18th, 2010
Last updated: February 26th, 2011

This script is to review the items in a user's cart before 
buying a learning unit.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	headers("Shopping Cart", false, true);
	
//Grab the payment credentials
	$paymentInfo = query("SELECT * FROM `payment` WHERE `id` = '1'");
	
//Process incomming information
	if (isset($_POST['submit'])) {
		if (isset($_POST['purchase']) && !empty($_POST['purchase'])) {
			$_SESSION['cart'] = $_POST['purchase'];
			sort($_SESSION['cart']);
		} else {
			unset($_SESSION['cart']);
		}
		
	//Redirect back to this page to avoid a browser resend alert on reload
		redirect("cart.php");
	}
	
//Process the outgoing information
	if (isset($_POST['enroll'])) {
		$total = 0;
		
		foreach ($_SESSION['cart'] as $item) {
			$data = query("SELECT * FROM `learningunits` WHERE `id` = '{$item}'");
			$total = $total + $data['item'];
		}
		
		if (number_format($total, 2) > 0) {
			$post = curl_init();
			curl_setopt($post, CURLOPT_URL, 'http://fullurl/page2.php');
			curl_setopt($post, CURLOPT_POST, true);
			curl_setopt($post, CURLOPT_POSTFIELDS, 'firstName=John&lastName=Doe ');
			curl_exec($post);
			curl_close($post); 
		}
	}
	
//Remove items from an array
	if (isset($_SESSION['cart']) && isset($_GET['delete'])) {
		unset($_SESSION['cart'][$_GET['delete']]);
		$_SESSION['cart'] = array_merge($_SESSION['cart']);
		redirect("cart.php");
	}
	
//Title
	title("Shopping Cart", "Review the items in your shopping cart, before proceeding to the checkout.");
	
	$count = 1;
	$columnCount = 1;
	$total = 0.00;
	$paymentArray = array();
	
	if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
		$products = array();
	} else {
		$products = $_SESSION['cart'];
	}
	
	$sql = "";
	
	if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
		foreach($_SESSION['cart'] as $itemID) {
			$sql .= " AND `id` !=  '{$itemID}'";
		}
	}
	
	if (is_array(arrayRevert($userData['learningunits']))) {
		foreach(arrayRevert($userData['learningunits']) as $key => $value) {
			$sql .= " AND `id` !=  '{$key}'";
		}
	}
	
	$autoAdd = query("SELECT * FROM `learningunits` WHERE `selected` = '1' AND `visible` = 'on'{$sql}", "raw");
	
//Admin toolbar
	if (isset($_SESSION['cart']) && !empty($_SESSION['cart']) || $autoAdd) {
		echo "<div class=\"toolBar\">\n";
		echo toolBarURL("Continue Shopping", "../index.php", "toolBarItem back");
		echo "</div>\n<br />\n";
	}
	
	echo form("checkout", "post", false, "https://" . $paymentInfo['transaction'] . "/cgi-bin/webscr");
	echo hidden("business", "business", $paymentInfo['email']);
	echo hidden("email", "email", $userData['emailAddress1']);
	echo hidden("currency_code", "currency_code", "USD");
	echo hidden("for_auction", "for_auction", "false");
	echo hidden("no_shipping", "no_shipping", "1");
	echo hidden("return", "return", $pluginRoot . "billing/transaction.php");
	echo hidden("cancel_return", "cancel_return", $pluginRoot . "index.php");
	
//Display incomming information
	if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("Name");
		echo column("Price", "100");
		echo column("Quantity", "75");
		echo column("", "25");
		echo "</tr>\n";
		
		foreach ($_SESSION['cart'] as $item) {
			$data = query("SELECT * FROM `learningunits` WHERE `id` = '{$item}'");
			$price = str_replace(".", "", $data['price']);
			$identifier = $count++;
			$arrayKey = $identifier - 1;
			
			if ($price > 0) {				
				echo hidden("cmd", "cmd", "_cart");
				echo hidden("upload", "upload", "1");
				echo hidden("item_name_" . $identifier, "item_name_" . $identifier, prepare($data['name']));
				echo hidden("item_number_" . $identifier, "item_number_" . $identifier, "unit_" . $item);
				
				if (!empty($data['enablePrice']) && !empty($data['price']) && $price > 0) {
					echo hidden("amount_" . $identifier, "amount_" . $identifier, prepare($data['price']));
				} else {
					echo hidden("amount_" . $identifier, "amount_" . $identifier, "0");
				}
				
				echo "<tr align=\"center\" id=\"" . $identifier ."\"";
				if ($columnCount & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
				echo cell(prepare($data['name']));
				
				if (!empty($data['price'])) {
					echo cell("\$" . $data['price'], "100");
				} else {
					echo cell("$0.00", "100");
				}
				
				echo cell("1", "75");
				echo cell(URL("", "cart.php?delete=" . $arrayKey, "action smallDelete", false, "Remove item"), "25");
				echo "</tr>\n";
				
				array_push($paymentArray, $data['price']);
				unset($data, $price);
				$columnCount++;
			} else {
				echo "<tr align=\"center\" id=\"" . $identifier ."\"";
				if ($columnCount & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
				echo cell(prepare($data['name']));
				
				if (!empty($data['price'])) {
					echo cell("\$" . $data['price'], "100");
				} else {
					echo cell("$0.00", "100");
				}
				
				echo cell("1", "75");
				echo cell(URL("", "cart.php?delete=" . $arrayKey, "action smallDelete", false, "Remove item"), "25");
				echo "</tr>\n";
			}
		}
		
		echo "</table>\n";
	}
	
	$columnCount = 1;
	
	if ($autoAdd) {
		echo "<p><strong>These learning units have been automatically added to your cart, as all users are required to take these units:</strong></p>\n";
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("Name");
		echo column("Price", "100");
		echo column("Quantity", "75");
		echo column("", "25");
		echo "</tr>\n";
		
		while ($item = fetch($autoAdd)) {
			$price = str_replace(".", "", $item['price']);
			$identifier = $count ++;
			$arrayKey = $identifier - 1;
			
			echo hidden("cmd", "cmd", "_cart");
			echo hidden("upload", "upload", "1");
			echo hidden("item_name_" . $identifier, "item_name_" . $identifier, prepare($item['name']));
			echo hidden("item_number_" . $identifier, "item_number_" . $identifier, "unit_" . $item['id']);
			
			if (!empty($item['enablePrice']) && !empty($item['price']) && $price > 0) {
				echo hidden("amount_" . $identifier, "amount_" . $identifier, prepare($item['price']));
			} else {
				echo hidden("amount_" . $identifier, "amount_" . $identifier, "0");
			}
			
			echo "<tr align=\"center\" id=\"" . $identifier ."\"";
			if ($columnCount & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
			echo cell(prepare($item['name']));
			
			if (!empty($item['price'])) {
				echo cell("\$" . $item['price'], "100");
			} else {
				echo cell("$0.00", "100");
			}
			
			echo cell("1", "75");
			echo cell("", "25");
			echo "</tr>\n";
			
			array_push($paymentArray, $item['price']);
			array_push($products, $item['id']);
			unset($data, $price);
			$columnCount ++;
		}
		
		echo "</table>\n";
	}	
	
	if (isset($_SESSION['cart']) && !empty($_SESSION['cart']) || $autoAdd) {
		foreach ($paymentArray as $paymentDue) {
			$total = $total + sprintf("%01.2f", $paymentDue);
		}
		
		echo hidden("notify_url", "notify_url", $pluginRoot . "enroll/ipn.php?value=" . base64_encode(gzdeflate($total)) . "&user=" . base64_encode(gzdeflate($_SESSION['userName'])) . "&product=" . base64_encode(gzdeflate(arrayStore($products))));
		
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
		echo cell("", "25");
		echo "</tr>\n";
		echo "</table>\n";
		echo "<div align=\"right\">\n";
		echo "<p>";
		echo button("submit", "submit", "Proceed to Secure Checkout", "submit");
		echo "</p>\n";
		echo "</div>\n";
		
		if (number_format($total, 2) > 0) {
			echo "<div align=\"right\">\n";
			echo "<p><img src=\"https://" . $paymentInfo['transaction'] . "/en_US/i/bnr/horizontal_solution_PPeCheck.gif\"></p>";
			echo "</div>\n";
		}
	} else {
		echo "<div class=\"noResults\">Your shopping cart is empty, please " . URL("go back to the learning units page", "../index.php") . " and select which ones you would like the purchase.</div>\n";
	}
	
	echo closeForm(false);
	
//Include the footer
	footer();
?>