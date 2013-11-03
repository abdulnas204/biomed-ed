<?php
//Header functions
	require_once('../../system/connections/connDBA.php');
	headers("Shopping Cart", "Student");
	
//Process incomming information
	if (isset($_POST['cart']) && !empty($_POST['cart'])) {
		$_SESSION['cart'] = $_POST['cart'];
		
	//Redirect back to this page to avoid a browser resend alert on reload
		redirect("cart.php");
	}
	
//Remove items from an array
	if (isset($_SESSION['cart']) && isset($_GET['delete'])) {
		unset($_SESSION['cart'][$_GET['delete']]);
		$_SESSION['cart'] = array_merge($_SESSION['cart']);
		redirect("cart.php");
	}
	
//Title
	title("Shopping Cart", "Review the items in your shopping cart, before proceeding the checkout.");
	
//Display incomming information
	if (isset ($_SESSION['cart']) && !empty($_SESSION['cart'])) {
		$paymentDataGrabber = mysql_query("SELECT * FROM `payment` WHERE `id` = '1'", $connDBA);
		$paymentData = mysql_fetch_array($paymentDataGrabber);
		$count = "1";
		$paymentArray = array();
		$total = 0.00;
		
		form("checkout", "post", false, false, "https://www.sandbox.paypal.com/cgi-bin/webscr");
		hidden("business", "business", $paymentData['business']);
		hidden("currency_code", "currency_code", "USD");
		hidden("for_auction", "for_auction", "false");
		hidden("no_shipping", "no_shipping", "1");
		hidden("return", "return", $root . "modules/index.php");
		hidden("cancel_return", "cancel_return", $root . "modules/index.php");
		echo "<table class=\"dataTable\" id=\"items\"><tr><th class=\"tableHeader\">Module Name</th><th class=\"tableHeader\" width=\"100\">Price</th><th class=\"tableHeader\" width=\"75\">Quantity</th><th class=\"tableHeader\" width=\"25\"></th></tr>";
		
		if (sizeof($_SESSION['cart']) > 1) {
			foreach ($_SESSION['cart'] as $item) {
				$moduleDataGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `id` = '{$item}'", $connDBA);
				$moduleData = mysql_fetch_array($moduleDataGrabber);
				$price = str_replace(".", "", $moduleData['price']);
				$identifier = $count++;
				$arrayKey = $identifier - 1;
				
				hidden("cmd", "cmd", "_cart");
				hidden("upload", "upload", "1");
				hidden("item_name_" . $identifier, "item_name_" . $identifier, prepare($moduleData['name'], false, true));
				hidden("item_number_" . $identifier, "item_number_" . $identifier, "module_" .$item);
				
				if (!empty($moduleData['enablePrice']) && !empty($moduleData['price']) && $price > 0) {
					hidden("amount_" . $identifier, "amount_" . $identifier, prepare($moduleData['price'], false, true));
				} else {
					hidden("amount_" . $identifier, "amount_" . $identifier, "0");
				}
				
				echo "<tr align=\"center\" id=\"" . $identifier ."\"";
				if ($moduleData['position'] & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<td>" . prepare($moduleData['name'], false, true) . "</td><td width=\"100\">$" . $moduleData['price'] . "</td><td width=\"75\">1</td><td width=\"25\"><a class=\"action smallDelete\" href=\"cart.php?delete=" . $arrayKey . "\"></a></td></tr>";
				
				array_push($paymentArray, $moduleData['price']);
				unset($moduleDataGrabber, $moduleData, $price);
			}
			
			foreach ($paymentArray as $paymentDue) {
				$total = $total + sprintf("%01.2f", $paymentDue);
			}
		} else {
			$id = $_SESSION['cart']['0'];
			$moduleDataGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `id` = '{$id}'", $connDBA);
			$moduleData = mysql_fetch_array($moduleDataGrabber);
			$price = str_replace(".", "", $moduleData['price']);
			
			hidden("cmd", "cmd", "_xclick");
			hidden("item_name", "item_name", prepare($moduleData['name'], false, true));
			hidden("item_number", "item_number", "module_" . $_SESSION['cart'][0]);
			
			if (!empty($moduleData['enablePrice']) && !empty($moduleData['price']) && $price > 0) {
				hidden("amount", "amount", prepare($moduleData['price'], false, true));
			} else {
				hidden("amount", "amount", "0");
			}
			
			echo "<tr align=\"center\" id=\"1\"><td>" . prepare($moduleData['name'], false, true) . "</td><td width=\"100\">$" . $moduleData['price'] . "</td><td width=\"75\">1</td><td width=\"25\"><a class=\"action smallDelete\" href=\"cart.php?delete=0\"></a></td></tr>";
			
			$total = $moduleData['price'];
		}
		
		hidden("notify_url", "notify_url", $root . "modules/enroll/ipn.php?value=" . base64_encode(gzdeflate($total)) . "&user=" . base64_encode(gzdeflate($_SESSION['MM_Username'])) . "&product=" . base64_encode(gzdeflate(serialize($_SESSION['cart']))));
		
		if ($count != 1) {	
			$quantity = $count - 1;
		} else {
			$quantity = $count;
		}
		
		echo "</table><hr /><table width=\"100%\"><tr align=\"center\"><td><div align=\"right\"><strong>Total:</strong></div></td><td width=\"100\">$" . $total . "</td><td width=\"75\">" . $quantity . "</td><td width=\"25\"></td></tr></table><div align=\"right\"><p>";
		button("submit", "submit", "Proceed to Checkout", "submit");
		echo "</p></div>";
		closeForm(false, false);
	} else {
		echo "<div class=\"noResults\">Your shopping cart is empty, please " . URL("go back to the modules page", "../index.php") . " and select which ones you would like the purchase.</div>";
	}
	
//Include the footer
	footer();
?>