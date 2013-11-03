<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Prevent non-system access
	if (empty($_POST) || empty($_GET)) {
		redirect("../index.php");
	}
	
//Provide a userData string for global use
	$userDataName = gzinflate(base64_decode($_GET['user']));
	$userDataGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userDataName}'", $connDBA);
	$userData = mysql_fetch_array($userDataGrabber);
	
//Create a function to fire when an error is present
	function error($type) {
		global $root, $userData, $values, $item_name, $item_number, $payment_status, $payment_amount, $payment_currency, $txn_id , $receiver_email, $payer_email;
		
		mail("wot200@gmail.com", "Payment Gateway Error", $userData['firstName'] . " " . $userData['lastName'] . " attempted to process an order via the PayPal IPN, located at: " . $root . "modules/enroll/ipn.php. " . $type . " Below is all of the known information.
		
		---------------------------------------------------------
		HTTP POST-DATA:
		" . $values . "
		
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
		Processed at: " . date('l, F jS, Y at h:i:s A'));
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
	$postSend = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

//Assign response variables to local variables
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
			$paymentArray = array();
			$total = gzinflate(base64_decode($_GET['value']));
			
			if (strcmp ($res, "VERIFIED") == 0) {
				if ($payment_status === "Completed" && exist("billing", "transactionID", $txn_id) == false && $payment_amount === $total && $payment_currency === "USD" && $paymentData['business'] === $receiver_email && $payer_email === $userData['emailAddress1']) {
					$currentModules = unserialize($userData['modules']);
					$userModules = unserialize(gzinflate(base64_decode($_GET['product'])));
					
					if (is_array($currentModules)) {
						foreach ($userModules as $item) {
							$module = array("item" => $item, "moduleStatus" => "C", "testStatus" => "C");
							$currentModules[$item] = $module;
						}
						
						$modules = serialize($currentModules);
					} else {
						$modules = serialize($userModules);
					}
					
					$purchasedModules = serialize($userModules); 					
					$userID = $userData['id'];
					
					mysql_query("UPDATE `users` SET `modules` = '{$modules}' WHERE `id` = '{$userID}'", $connDBA);
					mysql_query("INSERT INTO `billing` (
								`id`, `ownerUser`, `ownerOrganization`, `items`, `price`, `transactionID`
								) VALUES (
								NULL, '{$userID}', '', '{$purchasedModules}', '{$total}', '{$txn_id}'
								)", $connDBA);
								
					redirect("../index.php");
				} else {
					error("The payment gateway credentials from the gateway do not match the payment credentials from the system." . $payment_status . "Completed" . exist("billing", "transactionID", $txn_id) . $payment_amount . $total . $payment_currency . "USD" . $paymentData['business'] . $receiver_email . $payer_email . $userData['emailAddress1']);
				}
				
			} else if (strcmp ($res, "INVALID") == 0) {
				error("The payment gateway returned invalid.");
			}
		}
		
		fclose ($postSend);
	}
?>