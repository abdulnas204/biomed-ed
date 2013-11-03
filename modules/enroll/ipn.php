<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Prevent non-system access
	if (!isset($_POST) || !empty($_POST)) {
		redirect("../index.php");
	}
	
//Create a function to fire when an error is present
	function error($type) {
		$userData = userData();
		mail("wot200@gmail.com", "Payment Gateway Error", $userData['firstName'] . " " . $userData['lastName'] . " attempted to process an order via the PayPal IPN, located at:" . $root . "modules/enroll/ipn.php. " . $type . " Below is all of the known information.
		---------------------------------------------------------
		HTTP POST-DATA:
		" . $values . "
		---------------------------------------------------------
		---------------------------------------------------------
		ITEM NAME:
		" . $item_name . "
		
		ITEM NUMBER:
		" . $item_number . "
		
		PAYMENT STATUS:
		" . $payment_status . "
		
		PAYMENT AMOUNT:
		" . $payment_amount . "
		
		PAYMENT CURRENCY:
		" . $payment_currency . "
		
		TRANSACTION ID:
		" . $txn_id . "
		
		RECEIVER EMAIL:
		" . $receiver_email . "
		
		PAYER EMAIL:
		" . $payer_email . "
		---------------------------------------------------------
		" . date('l jS \of F Y h:i:s A'));
	}
	
//Begin assembly of post-data
	$values = 'cmd=_notify-validate';
	
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$values .= "&" . $key . "=" . $value;
	}
	
//Send response to payment gateway
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($values) . "\r\n\r\n";
	$postSend = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);

//Assign response variables to local variables
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];

//Process based on the given response
	if (!$postSend) {
		errorMessage("The system could not make a connection to the payment gateway. The status of your order is: " . $payment_status . ". Given the current status, you may or may not need to re-purchase your order once this error has been resolved. If your order has been processed, please ask a system administrator to enroll you in the modules you just purchased. The webmaster has been notified of this error.");
		echo "<div align=\"center\"><p>";
		button("continue", "continue", "Continue", "button", "../index.php");
		echo "</p></div>";
		error("There was no response from the server when sending the post-data.");
		exit;
	} else {
		fputs ($postSend, $header . $values);
		
		while (!feof($postSend)) {
			$res = fgets ($postSend, 1024);
			$paymentDataGrabber = mysql_query("SELECT * FROM `payment` WHERE `id` = '1'", $connDBA);
			$paymentData = mysql_fetch_array($paymentDataGrabber);
			$userData = userData();
			$paymentArray = array();
			$total = 0.00;
			
			foreach ($_SESSION['cart'] as $item) {
				$moduleDataGrabber = mysql_query("SELECT * FROM `moduledata` WHERE `id` = '{$item}'", $connDBA);
				$moduleData = mysql_fetch_array($moduleDataGrabber);				
				array_push($paymentArray, $moduleData['price']);
				unset($moduleDataGrabber, $moduleData, $price);
			}
			
			foreach ($paymentArray as $paymentDue) {
				$total = $total + sprintf("%01.2f", $paymentDue);
			}
			
			if (strcmp ($res, "VERIFIED") == 0) {
				if ($payment_status === "Completed" && exist("billing", "transactionID", $txn_id) == true && $payment_amount === $total && $payment_currency === "USD" && $paymentData['business'] === $receiver_email && $payer_email === $userData['emailAddress1']) {
					$userModules = unserialize($userData['modules']);
					
					foreach ($_SESSION['cart'] as $item) {
						if (is_array($userModules)) {
							array_push($userModules, $item);
						} else {
							$userModules = array();
							array_push($userModules, $item);
						}
					}
					
					$modules = serialize($userModules);
					$userID = $userData['id'];
					
					mysql_query("UPDATE `users` SET `modules` = '{$modules}' WHERE `id` = '{$userID}'", $connDBA);
					mysql_query("INSERT INTO `billing` (
								`id`, `ownerUser`, `ownerOrganization`, `items`, `price`, `transactionID`
								) VALUES (
								NULL, '{$userID}', '', '{$modules}', '{$total}', '{$txn_id}'
								)", $connDBA);
								
					redirect("../index.php");
				} else {
					error("The payment gateway credentials from the gateway do not match the payment credentials from the system.");
				}
			} else if (strcmp ($res, "INVALID") == 0) {
				error("The payment gateway returned invalid.");
			}
		}
		
		fclose ($postSend);
	}
?>