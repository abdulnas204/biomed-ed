<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: December 11th, 2010
Last updated: December 11th, 2010

This is the payment setup page.
*/

//Header functions
	headers("Payment Setup", "validate", true);
	
//Grab the payment information
	$paymentInfo = query("SELECT * FROM `payment` WHERE `id` = '1'");
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['transaction'])) {
		$email = escape($_POST['email']);
		$transaction = $_POST['transaction'];
		$errorEmail = $_POST['errorEmail'];
		
		query("UPDATE `payment` SET `email` = '{$email}', `transaction` = '{$transaction}', `errorEmail` = '{$errorEmail}' WHERE `id` = '1'");
		redirect("index.php?updated=payment");
	}
	
//Title
	title("Payment Setup", "Setup up the payment credentials for users to purchase learning units.");
	
//Payment form
	echo form("payment");
	catDivider("Payment Setup", "one", true);
	echo "<blockquote>\n";
	directions("PayPal&reg; merchant email", true, "The email used to access your PayPal&reg; merchant account");
	indent(textField("email", "email", false, false, false, true, "custom[email]", false, "paymentInfo", "email"));
	directions("[IMPORTANT] Transaction type", true, "Select the transaction type:<br /><br /><strong>Real</strong> - All payments are real, and actual money is transferred.<br /><strong>Practice</strong> - Users will access a sandbox store and &quot;pay&quot; for a <br />learning unit. Access will be granted to the learning unit, <br />but no actual money is involved. <em>In other words, this is for <br />testing purposes only, no money will be transfered using this <br />option, regardless of the learning unit\'s price.</em>");
	indent(radioButton("transaction", "transaction", "Real,Practice", "www.paypal.com,www.sandbox.paypal.com", true, true, false, false, "paymentInfo", "transaction"));
	directions("Error email", true, "The email used to send information on failed payments");
	indent(textField("errorEmail", "errorEmail", false, false, false, true, "custom[email]", false, "paymentInfo", "errorEmail"));
	echo "</blockquote>\n";
	
	catDivider("Submit", "two");
	formButtons();
	echo closeForm();
	
//Include the footer
	footer();
?>