<?php
/*
LICENSE: See "license.php" located at the root installation

This script is to review the items in a user's cart before buying access to a course.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');
	headers("Shopping Cart", false, true);
	
//Grab the payment credentials
	$paymentInfo = query("SELECT * FROM `payment` WHERE `id` = '1'");
	
//Remove items from the shopping cart
	if (isset($_SESSION['cart']) && isset($_GET['delete'])) {
		unset($_SESSION['cart'][$_GET['delete']]);
		$_SESSION['cart'] = array_merge($_SESSION['cart']);
		redirect("cart.php");
	}
	
//Title
	title("Shopping Cart", "Review the items in your shopping cart, before proceeding to the checkout.");
	
//Admin toolbar
	if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
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
		$count = 1;
		$paymentArray = array();
		$products = array();
		$sql = "";
		
		if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
			foreach($_SESSION['cart'] as $itemID) {
				$sql .= " OR `id` =  '{$itemID}'";
			}
		}
		
		$sql = ltrim($sql, " OR ");
		$dataGrabber = query("SELECT * FROM `courses` WHERE {$sql}", "raw");
		
		echo "<table class=\"dataTable\">\n<tr>\n";
		echo column("Name");
		echo column("Price", "100");
		echo column("Quantity", "75");
		echo column("", "25");
		echo "</tr>\n";
		
		while($data = fetch($dataGrabber)) {
			$price = str_replace(".", "", $data['price']);
			
			if ($price > 0) {				
				echo hidden("cmd", "cmd", "_cart");
				echo hidden("upload", "upload", "1");
				echo hidden("item_name_" . $count, "item_name_" . $count, prepare($data['name']));
				echo hidden("item_number_" . $count, "item_number_" . $count, "course_" . $data['id']);
				
				if (!empty($data['price']) && $price > 0) {
					echo hidden("amount_" . $count, "amount_" . $count, $data['price']);
				} else {
					echo hidden("amount_" . $count, "amount_" . $count, "0");
				}
				
				echo "<tr align=\"center\" id=\"" . $count ."\"";
				if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
				echo cell(prepare($data['name']));
				
				if (!empty($data['price'])) {
					echo cell("\$" . $data['price'], "100");
				} else {
					echo cell("$0.00", "100");
				}
				
				echo cell("1", "75");
				echo cell(URL("", "cart.php?delete=" . intval($count - 1), "action smallDelete", false, "Remove item"), "25");
				echo "</tr>\n";
				
				array_push($paymentArray, $data['price']);
			} else {
				echo "<tr align=\"center\" id=\"" . $count ."\"";
				if ($count & 1) {echo " class=\"odd\">\n";} else {echo " class=\"even\">\n";}
				echo cell(prepare($data['name']));
				
				if (!empty($data['price'])) {
					echo cell("\$" . $data['price'], "100");
				} else {
					echo cell("$0.00", "100");
				}
				
				echo cell("1", "75");
				echo cell(URL("", "cart.php?delete=" . intval($count - 1), "action smallDelete", false, "Remove item"), "25");
				echo "</tr>\n";
			}
		
		//Generate the list of learning units inside of each course
			$unitGrabber = query("SELECT * FROM `learningunits` WHERE `course` = '{$data['id']}'", "raw");
			
			while($unit = fetch($unitGrabber)) {
				array_push($products, $unit['id']);
			}
			
		//Increment the counter
			$count++;
			
		//Unset variables which may cause conflicts in the price/amount calculations
			unset($data, $price);
		}
		
		echo "</table>\n";
	}	
	
	if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
		$total = 0.00;
		
		foreach ($paymentArray as $paymentDue) {
			$total = $total + sprintf("%01.2f", $paymentDue);
		}
		
		echo hidden("notify_url", "notify_url", $pluginRoot . "enroll/ipn.php?value=" . base64_encode(gzdeflate($total)) . "&user=" . base64_encode(gzdeflate($_SESSION['userName'])) . "&product=" . base64_encode(gzdeflate(arrayStore($products))));
		
		echo "<hr />\n";
		echo "<table width=\"100%\">\n";
		echo "<tr align=\"center\">\n";
		echo cell("<div align=\"right\"><strong>Total:</strong></div>");
		echo cell("\$" . number_format($total, 2), "100");
		echo cell($count - 1, "75");
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
		echo "<div class=\"noResults\">Your shopping cart is empty, please " . URL("go back to the courses page", "../index.php") . " and select which ones you would like the purchase.</div>\n";
	}
	
	echo closeForm(false);
	
//Include the footer
	footer();
?>