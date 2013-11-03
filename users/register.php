<?php
/*
LICENSE: See "license.php" located at the root installation

This is user registration page.
*/

//Header functions
	require_once('../system/server/index.php');
	require_once('system/server/index.php');
	
//Set globally used variables	
	if (isset($_SESSION['id'])) {
		$registration = query("SELECT * FROM `users` WHERE `id` = '{$_SESSION['id']}'");
	}
	
	$registrationInfo = query("SELECT * FROM `registration` WHERE `id` = '1'");
	$siteInfo = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
	$paymentInfo = query("SELECT * FROM `payment` WHERE `id` = '1'");
	
//Listen for requests to validate data
	function validateRegistration($name, $value, $optional = false, $redirectOnFalse = false) {
		switch($name) {
		//Validate the user name
			case "userName" :
				$escaped = escape($value);
			
				if (exist("users", "userName", $escaped)) {
					$return = false;
				} else {
					$return = $value;
				}
				
				break;
		
		//Validate the password
			case "passWord" : 
				if (strlen($value) < 6) {
					$return = false;
				} else {
					$return = $value;
				}
				
				break;
		
		//Validate the email
			case "primaryEmail" : 
			case "secondaryEmail" : 
			case "tertiaryEmail" : 
				if (!preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $value)) {
					$return = false;
				} else {
					$return = $value;
				}
				
				break;
		
		//Validate the phone number
			case "phoneHome"  : 
			case "phoneMobile" : 
			case "phoneWork" : 
			case "phoneFax" : 
			case "phonePager" : 
				if (!preg_match('/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i', $value)) {
					$return = false;
				} else {
					$return = $value;
				}
				
				break;
				
		//Validate a coupon code
			case "coupon" : 
				if (!empty($registrationInfo['codes'])) {
					$codes = arrayRevert($registrationInfo['codes']);
					
					if (empty($codes) || !is_array($codes) || !in_array($value, $codes)) {
						$return = false;
					} else {
						$return = $value;
					}
				} else {
					$return = false;
				}
				
				break;
		}
		
		if ($return == false) {
			if (empty($value) && $optional == true) {
				return $value;
			} else {
				if ($redirectOnFalse == true) {
					redirect($_SERVER['REQUEST_URI']);
				} else {
					return false;
				}
			}
		} else {
			return $value;
		}
	}
	
//Command to send a validation email
	function validationEmail() {
		global $root, $siteInfo, $registration, $registrationInfo;
		
		if (isset($_SESSION['step']) && ($_SESSION['step'] == '3' && empty($registrationInfo['price'])) || ($_SESSION['step'] == '4' && !empty($registrationInfo['price']))) {			
			$id = $_SESSION['id'];
			$timeStamp = time();
			$key = randomValue(25);
			
			if (!exist("activation", "id", $_SESSION['id'])) {
				query("INSERT INTO `activation` (
				`id`, `timeStamp`, `key`
				) VALUES (
				'{$id}', '{$timeStamp}', '{$key}'
				)");
			} else {
				query("UPDATE `activation` SET `timeStamp` = '{$timeStamp}', `key` = '{$key}' WHERE `id` = '{$id}'");
			}
			
			$key = query("SELECT * FROM `activation` WHERE `id` = '{$id}'");
			
			autoEmail($registration['firstName'] . " " . $registration['lastName'] . "<" . $registration['emailAddress1'] . ">", $siteInfo['siteName'] . " Registration Confirmation", "Thanks for interest in " . $siteInfo['siteName'] . "!
In order to use your new account, you will need to verify your email address. To do so, copy and paste the following link into your browser:

" . $root . "users/activate.htm?id=" . $key['key'] . "

If you are having trouble with the above link, then copy and paste this link into your browser, and then copy and paste the activation key into the text field provided on the activation page.

Alternative link: " .  $root . "users/activate.htm
Activation key: " . $key['key'] . "

Please be aware that this link will expire in 24 hours, so be sure to activate your account before then.

Thanks,
The " . $siteInfo['siteName'] . " Team

=====================================================

We respect your privacy, and will send you no further emails, unless we are required to by an urgent matter.");
			
			return true;
		} else {
			return false;
		}
	}
	
//Listen for advanced validation requests from jQuery
	if (sizeof($_POST) == 2 && isset($_POST['name']) && isset($_POST['value'])) {
		$name = $_POST['name'];
		$value = $_POST['value'];
		
		echo validateRegistration($name, $value) ? "valid" : "invalid";
		exit;
	}
	
//Send the current step to the user
	if (isset($_GET['getStep'])) {
		if (!empty($registrationInfo['price'])) {
			$registration = "true";
		} else {
			$registration = "false";
		}
		
		if (!isset($_SESSION['step'])) {
			echo json_encode(array("step" => "1", "registration" => $registration));
		} else {
			echo json_encode(array("step" => $_SESSION['step'], "registration" => $registration));
		}
		
		exit;
	}
	
//Resend validation emails
	if (isset($_GET['sendEmail'])) {
		echo validationEmail() ? 'success' : 'failure';
		exit;
	}
	
//Process the form content
//Process the first page
	if (!empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['userName']) && !empty($_POST['passWord']) && !empty($_POST['primaryEmail'])) {
		$firstName = escape($_POST['firstName']);
		$lastName = escape($_POST['lastName']);
		$userName = escape(validateRegistration("userName", $_POST['userName'], false, true));
		$passWord = escape(md5($_POST['passWord'] . $salt));
		$primaryEmail = escape(validateRegistration("primaryEmail", $_POST['primaryEmail'], false, true));
		$secondaryEmail = escape(validateRegistration("secondaryEmail", $_POST['secondaryEmail'], true, true));
		$tertiaryEmail = escape(validateRegistration("tertiaryEmail", $_POST['tertiaryEmail'], true, true));
		
		if (!isset($_SESSION['id'])) {
			query("INSERT INTO `users` (
			`id`, `locked`, `firstName`, `lastName`, `userName`, `passWord`, `emailAddress1`, `emailAddress2`, `emailAddress3`, `role`, `organization`
			) VALUES (
			NULL, '1', '{$firstName}', '{$lastName}', '{$userName}', '{$passWord}', '{$primaryEmail}', '{$secondaryEmail}', '{$tertiaryEmail}', 'Student', '0'
			)");
			
			$_SESSION['id'] = primaryKey();
		} else {
			$id = $_SESSION['id'];
			
			query("UPDATE `users` SET `firstName` = '{$firstName}', `lastName` = '{$lastName}', `userName` = '{$userName}', `passWord` = '{$passWord}', `emailAddress1` = '{$primaryEmail}', `emailAddress2` = '{$secondaryEmail}', `emailAddress3` = '{$tertiaryEmail}' WHERE `id` = '{$id}'");
		}
		
		$_SESSION['passWord'] = $_POST['passWord'];
		$_SESSION['step'] = '2';
		
		echo "success";
		exit;
	}
	
//Process the second page
	if (isset($_SESSION['id']) && !empty($_POST['phoneHome']) && !empty($_POST['phoneMobile']) && !empty($_POST['phoneWork']) && !empty($_POST['workLocation']) && !empty($_POST['jobTitle']) && !empty($_POST['staffID']) && !empty($_POST['department']) && !empty($_POST['departmentID'])) {
		$id = $_SESSION['id'];
		$phoneHome = escape(validateRegistration("phoneHome", $_POST['phoneHome'], false, true));
		$phoneMobile = escape(validateRegistration("phoneMobile", $_POST['phoneMobile'], false, true));
		$phoneWork = escape(validateRegistration("phoneWork", $_POST['phoneWork'], false, true));
		$phoneFax = escape(validateRegistration("phoneFax", $_POST['phoneFax'], true, true));
		$phonePager = escape(validateRegistration("phonePager", $_POST['phonePager'], true, true));
		$workLocation = escape($_POST['workLocation']);
		$jobTitle = escape($_POST['jobTitle']);
		$staffID = escape($_POST['staffID']);
		$department = escape($_POST['department']);
		$departmentID = escape($_POST['departmentID']);
		
		query("UPDATE `users` SET `phoneHome` = '{$phoneHome}', `phoneMobile` = '{$phoneMobile}', `phoneWork` = '{$phoneWork}', `phoneFax` = '{$phoneFax}', `phonePager` = '{$phonePager}', `workLocation` = '{$workLocation}', `jobTitle` = '{$jobTitle}', `staffID` = '{$staffID}', `department` = '{$department}', `departmentID` = '{$departmentID}' WHERE `id` = '{$id}'");
		
		if (empty($registrationInfo['price'])) {
			validationEmail();
		}
		
		$_SESSION['step'] = '3';
		
		echo "success";
		exit;
	}
	
//Serve the form content to the page
	if (isset($_GET['step'])) {
	//Is the client requesting the server's descision to be overridden?
		if (isset($_GET['override'])) {
			if ($_GET['override'] < $_SESSION['step']) {
				$step = $_GET['override'];
			} else {
				$step = $_SESSION['step'];
			}
		} else {
			if (!isset($_SESSION['step'])) {
				$step = '1';
			} else if (isset($_SESSION['step'])) {
				$step = $_SESSION['step'];
			}
		}
		
		switch($step) {
			case '1' : 
			//Store the password in a session, since it is encrypted in the database
				if (isset($_SESSION['passWord'])) {
					$passWord = $_SESSION['passWord'];
				} else {
					$passWord = "";
				}
				
				echo form("register");
				echo "<table align=\"center\">\n";
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">First name</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox\">" . textField("firstName", "firstName", false, false, false, false, false, false, "registration", "firstName") . "<span class=\"tip\" id='{\"revert\" : \"Enter your first name\", \"empty\" : \"We need your first name\"}'>Enter your first name</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Last name</td>\n";
				echo "<td width=\"200\"><div class=\"glowBox\">" . textField("lastName", "lastName", false, false, false, false, false, false, "registration", "lastName") . "<span class=\"tip\" id='{\"revert\" : \"Enter your last name\", \"empty\" : \"We need your last name\"}'>Enter your last name</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Username</td>\n";
				echo "<td width=\"200\"><div class=\"glowBox custom noHide\">" . textField("userName", "userName", false, false, false, false, false, false, "registration", "userName") . "<span class=\"tip\" id='{\"revert\" : \"Username must be unique\", \"empty\" : \"Please provide a username\", \"valid\" : \"This name is avaliable\", \"invalid\" : \"Sorry, this name is taken\"}'>Username must be unique</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Password</td>\n";
				echo "<td width=\"200\"><div class=\"glowBox custom\">" . textField("passWord", "passWord", false, false, true, false, false, $passWord) . "<span class=\"tip\" id='{\"revert\" : \"6 characters or more!\", \"empty\" : \"You will need a password\", \"valid\" : \"Good password!\", \"invalid\" : \"Too short\"}'>6 characters or more!</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr class=\"spacer\">\n";
				echo "<td width=\"200\"></td>\n";
				echo "<td width=\"200\"></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Primary Email Address</td>\n";
				echo "<td width=\"200\"><div class=\"glowBox custom\">" . textField("primaryEmail", "primaryEmail", false, false, false, false, false, false, "registration", "emailAddress1") . "<span class=\"tip\" id='{\"revert\" : \"Enter your email address\", \"empty\" : \"An email is required\", \"valid\" : \"Email is valid\" ,\"invalid\" : \"Invalid email address\"}'>Enter your email address</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Secondary Email Address</td>\n";
				echo "<td width=\"200\"><div class=\"glowBox custom optional\">" . textField("secondaryEmail", "secondaryEmail", false, false, false, false, false, false, "registration", "emailAddress2") . "<span class=\"tip\" id='{\"revert\" : \"Enter your email address\", \"valid\" : \"Email is valid\" ,\"invalid\" : \"Invalid email address\"}'>Only if you have one</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Tertiary Email Address</td>\n";
				echo "<td width=\"200\"><div class=\"glowBox custom optional\">" . textField("tertiaryEmail", "tertiaryEmail", false, false, false, false, false, false, "registration", "emailAddress3") . "<span class=\"tip\" id='{\"revert\" : \"Enter your email address\", \"valid\" : \"Email is valid\" ,\"invalid\" : \"Invalid email address\"}'>Only if you have one</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\"></td>\n";
				echo "<td width=\"200\">" . button("submit", "submit", "Next Step", "button") . "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo closeForm();
				break;
				
			case '2' : 
				echo form("register");
				echo "<table align=\"center\">\n";
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Home phone number</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox custom\">" . textField("phoneHome", "phoneHome", false, false, false, false, false, false, "registration", "phoneHome") . "<span class=\"tip\" id='{\"revert\" : \"Your home phone number\", \"empty\" : \"Phone number is required\", \"valid\" : \"Phone number is valid\", \"invalid\" : \"Seperate with dashes\"}'>Your home phone number</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\">Mobile phone number</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox custom\">" . textField("phoneMobile", "phoneMobile", false, false, false, false, false, false, "registration", "phoneMobile") . "<span class=\"tip\" id='{\"revert\" : \"Your mobile phone number\", \"empty\" : \"Phone number is required\", \"valid\" : \"Phone number is valid\", \"invalid\" : \"Seperate with dashes\"}'>Your mobile phone number</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Work phone number</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox custom\">" . textField("phoneWork", "phoneWork", false, false, false, false, false, false, "registration", "phoneWork") . "<span class=\"tip\" id='{\"revert\" : \"Your mobile phone number\", \"empty\" : \"Phone number is required\", \"valid\" : \"Phone number is valid\", \"invalid\" : \"Seperate with dashes\"}'>Your work phone number</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Fax phone number</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox custom optional\">" . textField("phoneFax", "phoneFax", false, false, false, false, false, false, "registration", "phoneFax") . "<span class=\"tip\" id='{\"revert\" : \"Only if you have one\", \"valid\" : \"Phone number is valid\", \"invalid\" : \"Seperate with dashes\"}'>Only if you have one</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Pager phone number</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox custom optional\">" . textField("phonePager", "phonePager", false, false, false, false, false, false, "registration", "phonePager") . "<span class=\"tip\" id='{\"revert\" : \"Only if you have one\", \"valid\" : \"Phone number is valid\", \"invalid\" : \"Seperate with dashes\"}'>Only if you have one</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr class=\"spacer\">\n";
				echo "<td width=\"200\"></td>\n";
				echo "<td width=\"200\"></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Work location</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox\">" . textField("workLocation", "workLocation", false, false, false, false, false, false, "registration", "workLocation") . "<span class=\"tip\" id='{\"revert\" : \"Your place of work\", \"empty\" : \"Where do you work?\"}'>Your place of work</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Job title</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox\">" . textField("jobTitle", "jobTitle", false, false, false, false, false, false, "registration", "jobTitle") . "<span class=\"tip\" id='{\"revert\" : \"Your title or position\", \"empty\" : \"What is your position?\"}'>Your title or position</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Staff ID</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox\">" . textField("staffID", "staffID", false, false, false, false, false, false, "registration", "staffID") . "<span class=\"tip\" id='{\"revert\" : \"Your staff ID\", \"empty\" : \"What is your staff ID?\"}'>Your staff ID</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Department</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox\">" . textField("department", "department", false, false, false, false, false, false, "registration", "department") . "<span class=\"tip\" id='{\"revert\" : \"Your department name\", \"empty\" : \"What is your department?\"}'>Your department name</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<td width=\"200\" align=\"right\">Department ID</td>\n";
				echo "<td width=\"600\"><div class=\"glowBox\">" . textField("departmentID", "departmentID", false, false, false, false, false, false, "registration", "departmentID") . "<span class=\"tip\" id='{\"revert\" : \"Your department ID\", \"empty\" : \"What is your department ID?\"}'>Your department ID</span></div></td>\n";
				echo "</tr>\n";
				
				echo "<tr>\n";
				echo "<td width=\"200\" align=\"right\"></td>\n";
				echo "<td width=\"200\">" . button("submit", "submit", "Next Step", "button") . "</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				echo closeForm();
				break;
				
			case '3' : 
				if (!empty($registrationInfo['price'])) {
					echo "<div align=\"center\">\n";
					echo "<p>This site requires a payment for entry. In order to complete your registration review the shopping cart below, and click the &quot;Proceed to Secure Checkout&quot; button to pay.</p>";
					echo "</div>\n";
					
					echo form("checkout", "post", false, "https://" . $paymentInfo['transaction'] . "/cgi-bin/webscr");
					echo hidden("business", "business", $paymentInfo['email']);
					echo hidden("email", "email", $registration['emailAddress1']);
					echo hidden("currency_code", "currency_code", "USD");
					echo hidden("for_auction", "for_auction", "false");
					echo hidden("no_shipping", "no_shipping", "1");
					echo hidden("return", "return", $pluginRoot . "register.htm");
					echo hidden("cancel_return", "cancel_return", $root . "index.htm");
					echo hidden("cmd", "cmd", "_cart");
					echo hidden("upload", "upload", "1");
					echo hidden("item_name_1", "item_name_1", $siteInfo['siteName'] . " Site Registration");
					echo hidden("item_number_1", "item_number_1", "reg_" . $registration['id']);
					echo hidden("amount_1", "amount_1", $registrationInfo['price']);
					echo hidden("notify_url", "notify_url", $pluginRoot . "enroll/ipn.php?value=" . base64_encode(gzdeflate($registrationInfo['price'])) . "&user=" . base64_encode(gzdeflate($registration['userName'])));
	
					echo "<table class=\"checkout\" align=\"center\">\n";
					echo "<tr>\n";
					echo "<th align=\"left\" colspan=\"2\">Shopping Cart:</th>\n";
					echo "<th width=\"100\">Price:</th>\n";
					echo "<th width=\"80\">Qty:</th>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
					echo "<td width=\"800\" colspan=\"2\">" . $siteInfo['siteName'] . " Site Registration";
					echo "</td>\n";
					echo "<td align=\"center\"><span class=\"price\">\$" . $registrationInfo['price'] . "</span><br /><span id=\"discount\"></span></td>\n";
					echo "<td align=\"center\">1</td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
					echo "<td width=\"200\" align=\"right\">Coupon Code</td>\n";
					echo "<td width=\"600\" align=\"left\"><div class=\"glowBox custom optional\">" . textField("coupon", "coupon") . "<span class=\"tip\" id='{\"revert\" : \"Only if you have one\", \"valid\" : \"This coupon is valid!\", \"invalid\" : \"Sorry, invalid coupon\"}'>Only if you have one</span></div></td>\n";
					echo "<td align=\"center\"></td>\n";
					echo "<td align=\"center\"></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
					echo "<td colspan=\"4\" class=\"divider\"></td>\n";
					echo "</tr>\n";
					
					echo "<tr>\n";
					echo "<td colspan=\"4\" valign=\"top\">\n";
					echo "<div class=\"layoutControl\">\n";
					echo "<div class=\"halfLeft\">\n";
					echo "<p><img src=\"https://" . $paymentInfo['transaction'] . "/en_US/i/bnr/horizontal_solution_PPeCheck.gif\"></p>\n";
					echo "</div>\n";
					echo "<div class=\"halfRight\" align=\"right\">\n";
					echo button("submit", "preProcess", "Proceed to Secure Checkout", "button");
					echo "</div>\n";
					echo "</div>\n";
					echo "</td>\n";
					echo "</tr>\n";
					echo "</table>\n";
					echo closeForm();
				} else {
					echo "<div class=\"spacer\">\n";
					echo "<p>Your registration is almost complete. <br /><br />We have just sent a confirmation email to your primary email address. The email will contain an activation link which will expire in 24 hours, so be sure to check it soon! If you did not recieve the email, check you email's spam folder, or click the &quot;Resend Activation Email&quot; link below.</p>";
					echo "<span id=\"resend\" class=\"button\">Resend Activation Email</span>\n";
					echo "<span class=\"button login\">Login</span>\n";
					echo "</div>\n";
				}
				
				break;
		}
		
		exit;
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['userName']) && !empty($_POST['primaryEmail'])) {
		if (!exist("user", "userName", escape(strip($_POST['userName'], "lettersNumbers"))) && $_POST['verify'] === "DITEC") {
			$firstName = escape(strip($_POST['firstName'], "lettersNumbers"));
			$lastName = escape(strip($_POST['lastName'], "lettersNumbers"));
			$userName = escape(strip($_POST['userName'], "lettersNumbers"));
			$passWord = md5($_POST['passWord'] . $salt);
			$primaryEmail = $_POST['primaryEmail'];
			$secondaryEmail = $_POST['secondaryEmail'];
			$tertiaryEmail = $_POST['tertiaryEmail'];
			
			query("INSERT INTO `users`(
				  id, locked, active, staffID, firstName, lastName, userName, passWord, changePassword, emailAddress1, emailAddress2, emailAddress3, phoneWork, phoneHome, phoneMobile, phoneFax, workLocation, jobTitle, department, departmentID, role, organization
				  ) VALUES (
					  NULL, '', '', '', '{$firstName}', '{$lastName}', '{$userName}', '{$passWord}', '', '{$primaryEmail}', '{$secondaryEmail}', '{$tertiaryEmail}', '', '', '', '', '', '', '', '', 'Student', '0'
				  )");
			
			$id = primaryKey();
			$_SESSION['userName'] = $userName;
			$_SESSION['role'] = "Student";
			$sessionID = encrypt(session_id());
			
			query("UPDATE `users` SET `sessionID` = '{$sessionID}' WHERE `id` = '{$id}'");
			redirect("../portal/index.php");
		} else {
			redirect("register.php?error=identical");
		}
	}
	
//Top content
	headers("Register", "validate,registration", true);
	
//Left out the title for a cleaner look
	
	echo "<div class=\"superContainer\">\n";
	
//A short message to display while the page is loading, and for a few seconds afterwords... for a nice effect	
	echo "<div id=\"loader\" class=\"loader\">\n";
	echo "<p>Thanks for your interest in " . $siteInfo['siteName'] . "! You are only minutes away from accessing our professionally created courses.<br />\n";
	echo "Please wait one moment while the registration form is generated.\n";
	echo "<br /><br />\n";
	echo "<img src=\"system/images/loader.gif\" alt=\"loader\" /></p>\n";
	echo "</div>\n";
	
	echo "<div id=\"formContainer\" class=\"contentHide\">\n";
	
//Display the ribbon bar
	echo "<div class=\"ribbonContainer\">\n";
	echo "<div class=\"ribbon\">\n";
	
	if (!empty($registrationInfo['price'])) {
		echo "<span class=\"stepBase step1IncompleteMore noAccess\" id=\"1\"></span>\n";
		echo "<span class=\"stepBase step2IncompleteMore noAccess\" id=\"2\"></span>\n";
		echo "<span class=\"stepBase step3IncompleteMore noAccess\" id=\"3\"></span>\n";
		echo "<span class=\"stepBase step4IncompleteMore noAccess\" id=\"4\"></span>\n";
	} else {
		echo "<span class=\"stepBase step1IncompleteLess noAccess\" id=\"1\"></span>\n";
		echo "<span class=\"stepBase step2IncompleteLess noAccess\" id=\"2\"></span>\n";
		echo "<span class=\"stepBase step3IncompleteLess noAccess\" id=\"3\"></span>\n";
	}
	
	echo "</div>\n";
	echo "</div>\n";
	
//Registration form
	echo "<div id=\"form\">nice</div>\n";
	
	echo "</div>\n";
	echo "</div>\n";
	
//Include the footer
	footer();
?>