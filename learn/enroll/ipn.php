<?php
/*
LICENSE: See "license.php" located at the root installation

Sections of the code are courtesy of PayPal.

This script is assign users to a learning unit after it has been purchased, and to confirm that the payment occurred.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');
	
//Prevent non-system access
	if (empty($_POST)) {
		redirect("../index.php");
	}
	
//Provide variables for global use
	$userDataName = gzinflate(base64_decode($_GET['user']));
	$userData = query("SELECT * FROM `users` WHERE `userName` = '{$userDataName}'");
	$paymentInfo = query("SELECT * FROM `payment` WHERE `id` = '1'");
	
//Create a function to fire when an error is present
	function error($type) {
		global $pluginRoot, $paymentInfo, $userData, $values, $item_name, $item_number, $payment_status, $total, $payment_amount, $payment_currency, $txn_id , $receiver_email, $payer_email;
		
		autoEmail($paymentInfo['errorEmail'], "Payment Gateway Error", $userData['firstName'] . " " . $userData['lastName'] . " attempted to process an order via the PayPal IPN, located at: " . $pluginRoot . "enroll/ipn.php. " . $type . " Below is all of the known information.
		
		---------------------------------------------------------		
		PAYMENT STATUS:
		(System): Completed (Gateway): " . $payment_status . "
		
		PAYMENT AMOUNT:
		(System): " . $total . " (Gateway): " . $payment_amount . "
		
		PAYMENT CURRENCY:
		(System): USD (Gateway): " . $payment_currency . "
		
		RECEIVER EMAIL:
		(System): " . $paymentInfo['email'] . " (Gateway): " . urldecode($receiver_email) . "
		
		PAYER EMAIL:
		(System): " . $userData['emailAddress1'] . " (Gateway): " . urldecode($payer_email) . "
		
		TRANSACTION ID:
		" . $txn_id . "
		---------------------------------------------------------
		Processed on: " . date('l, F jS, Y \a\\t h:i:s A'));
	}
	
//Begin assembly of post-data
	$values = "cmd=_notify-validate";
	
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$values .= "&" . $key . "=" . $value;
	}
	
//Send response to payment gateway
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($values) . "\r\n\r\n";
	$postSend = fsockopen("ssl://" . $paymentInfo['transaction'], 443, $errno, $errstr, 30);

//Assign response variables to local variables
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['payment_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$tax = $_POST['tax'];
	$payer_email = $_POST['payer_email'];
	$payment_fee = $_POST['payment_fee'];

//Process based on the given response
	if (!$postSend) {
		errorMessage("The system could not make a connection to the payment gateway. The status of your order is: " . $payment_status . ". Given the current status, you may or may not need to re-purchase your order once this error has been resolved. If your order has been processed, please ask a system administrator to enroll you in the learning units you just purchased. The webmaster has been notified of this error." . button("continue", "continue", "Continue", "button", "../index.php"));
		error("There was no response from the server when sending the post-data.");
		exit;
	} else {
		fputs($postSend, $header . $values);
		
		while (!feof($postSend)) {
			$res = fgets($postSend, 1024);
			$paymentArray = array();
			$total = number_format(gzinflate(base64_decode($_GET['value'])), 2);
			
			if (strcmp($res, "VERIFIED") == 0) {
				if ($payment_status === "Completed" && !exist("billing", "transactionID", $txn_id) && $payment_amount === $total && $payment_currency === "USD" && $paymentInfo['email'] === urldecode($receiver_email)) {
					$currentUnits = arrayRevert($userData['learningunits']);
					$userUnits = arrayRevert(gzinflate(base64_decode($_GET['product'])));
					
					if (!is_array($currentUnits)) {
						$currentUnits = array();
					}
					
					foreach ($userUnits as $item) {
						$unit = array("item" => $item, "lessonStatus" => "C", "testStatus" => "C", "startDate" => strtotime("now"), "submitted" => "");
						$currentUnits[$item] = $unit;
					}
					
					$purchasedUnits = array();
					
					foreach($userUnits as $id) {
						$priceData = query("SELECT * FROM `learningunits` WHERE `id` = '{$id}'");
						
						if (empty($priceData['price'])) {
							$price = "0.00";
						} else {
							$price = number_format($priceData['price'], 2);
						}
						
						$purchasedUnits[$id] = array("item" => $id, "price" => $price);
					}
					
					$userID = $userData['id'];
					$units = arrayStore($currentUnits);
					$purchasedUnits = escape(arrayStore($purchasedUnits));
					$date = strtotime("now");
					$business = escape(urldecode($_POST['business']));
					$payerEmail = escape($payer_email);
					
					query("UPDATE `users` SET `learningunits` = '{$units}' WHERE `id` = '{$userData['id']}'");
					query("INSERT INTO `billing` (
						  `id`, `ownerUser`, `ownerOrganization`, `items`, `tax`, `total`, `date`, `transactionID`, `businessEmail`, `payerEmail`, `paymentFee`
						  ) VALUES (
						  NULL, '{$userID}', '', '{$purchasedUnits}', '{$tax}', '{$total}', '{$date}', '{$txn_id}', '{$business}', '{$payerEmail}', '{$payment_fee}'
						  )");
				} else {
					errorMessage("The difference between the credentials from the payment gateway and the credentials from the system. The status of your order is: " . $payment_status . ". Given the current status, you may or may not need to re-purchase your order once this error has been resolved. If your order has been processed, please ask a system administrator to enroll you in the learning units you just purchased. The webmaster has been notified of this error." . button("continue", "continue", "Continue", "button", "../index.php"));
					error("The payment credentials from the gateway do not match the payment credentials from the system.");
					exit;
				}
			} else if (strcmp($res, "INVALID") == 0) {
				error("The payment gateway returned invalid.");
			}
		}
		
		fclose($postSend);
		redirect("../index.php");
	}
?>