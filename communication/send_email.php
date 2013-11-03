<?php
//Header functions
	require_once('../system/connections/connDBA.php');
	headers("Send an Email", "Site Administrator", "tinyMCEAdvanced,validate,optionTransfer", true, " onload=\"opt.init(document.forms[0])\"");
	
//Grab the required values for the selection fields
	if (isset($_GET['type'])) {
		$potentialValuesPrep = "";
		$potentialIDsPrep = "";
		
		switch ($_GET['type']) {
			case "users" :                       
				$notToUsersGrabber = query("SELECT * FROM `users` ORDER BY `lastName` ASC", "raw");
				$toUsersGrabber = query("SELECT * FROM `users` ORDER BY `lastName` ASC", "raw");
				
				while($users = mysql_fetch_array($notToUsersGrabber)) {
					$potentialValuesPrep .= $users['firstName'] . " " . $users['lastName'] . ",";
					$potentialIDsPrep .= $users['firstName'] . " " . $users['lastName'] . " <" . $users['emailAddress1'] . ">,";
				}
				
				break;
			
			case "organizations" :                       
				$notToOrganizationsGrabber = query("SELECT * FROM `organizations` ORDER BY `organization` ASC", "raw");
				$toOrganizationsGrabber = query("SELECT * FROM `organizations` ORDER BY `organization` ASC", "raw");
				
				while($organizations = mysql_fetch_array($notToOrganizationsGrabber)) {
					$potentialValuesPrep .= $organizations['organization'] . ",";
					$potentialIDsPrep .= $organizations['billingEmail'] . ",";
				}
				
				break;
			
			case "roles" :
				$potentialValuesPrep = "Administrative Assistants,Instructorial Assisstants,Instructors,Organization Administrators,Site Administrators,Site Managers,Students,";
				$potentialIDsPrep = "Administrative Assistant,Instructorial Assisstant,Instructor,Organization Administrator,Site Administrator,Site Manager,Student,";
				
				break;
		}
		
		if (empty($potentialValuesPrep) || empty($potentialIDsPrep)) {
			$potentialValues = false;
			$potentialIDs = false;
		} else {
			$potentialValues = rtrim($potentialValuesPrep, ",");
			$potentialIDs = rtrim($potentialIDsPrep, ",");
		}
	}
	
//Process the form
	if (isset ($_POST['submit']) && !empty($_POST['from']) && !empty($_POST['toDetirmine']) && !empty($_POST['toImport']) && !empty($_POST['subject']) && !empty($_POST['priority']) && !empty($_POST['message'])) {
	//Get all of the form fields
		$from = $_POST['from'];
		$toDetirmine = $_POST['toDetirmine'];
		$toList = $_POST['toImport'];
		$subject = stripslashes($_POST['subject']);
		$priority = $_POST['priority'];
		$bodyGrabber = "<html><head><title>" . $subject . "</title></head><body>" . prepare($_POST['message'], false, true) . "</body></html>";
		$body = str_ireplace("\"" . $strippedRoot, "\"" . $root, $bodyGrabber);
		
	//Select the site name to conceal the "to" list
		$siteName = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");
		
	//Detirmine what kind of mass email is being sent
		if ($toDetirmine == "users") {
			$to = $toList;
		} elseif ($toDetirmine == "all") {
			$toGrabber = query("SELECT * FROM `users` ORDER BY `firstName` ASC", "raw");
			$to = "";
			
			while($toData = mysql_fetch_array($toGrabber)) {
				$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
			}
		} elseif ($toDetirmine == "organizations") {
			$toArray = explode(",", $toList);
			$toArraySize = sizeof($toArray);
			$to = "";
			
			for ($count = 0; $count <= $toArraySize; $count++) {
				$organization = $toArraySize[$count];
				$toGrabber = query("SELECT * FROM `users` WHERE `organization` = '{$organization}'", "raw");
				
				while($toData = mysql_fetch_array($toGrabber)) {
					$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
				}
			}
		} elseif ($toDetirmine == "allOrganizations") {
			$toGrabber = query("SELECT * FROM `users` WHERE `organization` != '1'", "raw");
			
			while($toData = mysql_fetch_array($toGrabber)) {
				$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
			}
		} elseif ($toDetirmine == "roles") {
			$toArray = explode(",", $toList);
			$toArraySize = sizeof($toArray);
			$to = "";
			
			for ($count = 0; $count <= $toArraySize; $count++) {
				$role = $toArraySize[$count];
				$toGrabber = query("SELECT * FROM `users` WHERE `role` = '{$role}'", "raw");
				
				while($toData = mysql_fetch_array($toGrabber)) {
					$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
				}
			}
		}
		
	//Generate the header
		$random = md5(time());
		$mimeBoundary = "==Multipart_Boundary_x{$random}x";
		
		$header = "From: " . $from . "\n" .
				  "Reply-To: " . $from . "\n"  .
				  "X-Mailer: PHP/" . phpversion() . "\n" .
				  "X-Priority: " . $priority . "\n" .
				  "MIME-Version: 1.0\n" .
				  "Content-Type: multipart/mixed;\n" .
				  " boundary=\"{$mimeBoundary}\"";
				  
	//The message of the email
		$message = "--{$mimeBoundary}\n" .
				   "Content-Type: text/html; charset=\"iso-8859-1\"\n" .
				   "Content-Transfer-Encoding: 7bit\n\n" .
				   $body . "\n\n";
		
		if (is_uploaded_file($_FILES['attachment']['tmp_name'])) {
		//Grab the attachment
			$fileTempName = $_FILES['attachment']['tmp_name'];
			$fileType = $_FILES['attachment']['type'];
			$fileName = basename($_FILES['attachment'] ['name']);	
		
		//Grab the attachment info
			$file = fopen($fileTempName, 'rb');
			$data = fread($file, filesize($fileTempName));
			fclose ($file);	
			
		//Processing			
			$data = chunk_split(base64_encode($data));
			$message .= "--{$mimeBoundary}\n" .
						"Content-Type: {$fileType};\n" . 
						" name = \"{$fileName}\"\n" . 
						"Content-Transfer-Encoding: base64\n\n" . 
						$data . "\n\n" .    
						"--{$mimeBoundary}--\n";
		} else {
			$message .= "--{$mimeBoundary}\n" .
						"Content-Type:  text/html;\n" . 
						" name = \"{$fileName}\"\n" . 
						"Content-Transfer-Encoding: base64\n\n" . 
						chunk_split(base64_encode($body)) . "\n\n" .    
						"--{$mimeBoundary}--\n";
		}
		
	//Processor
		$mailTo = explode(",", $to);
		
		foreach ($mailTo as $to) {
			mail($to, $subject, $message, $header);
		}
			
	//Display a confirmation
		redirect("index.php?email=success");
	}
	
//Title
	title("Send an Email", "Send an email to multiple users, or organizations within this system. Please note that this is not an online email system. This is only used to send a mass email.");
	
//Show if the type of email is not set
	if (!isset($_GET['type']) && !isset($_GET['id'])) {		
	//List of possible values
		echo "<blockquote><p>";
		echo URL("Selected Users", "send_email.php?type=users") . " - Only selected users will recieve this email<br />";
		echo URL("All Users", "send_email.php?type=all") . " - All registered users will recieve this email<br />";
		echo URL("Selected Organizations", "send_email.php?type=organizations") . " - All users within selected organizations will recieve this email<br />";
		echo URL("All Organizations", "send_email.php?type=allOrganizations") . " - All registered organizations will recieve this email<br />";
		echo URL("Selected Roles", "send_email.php?type=roles") . " - All users with a selected role will recieve this email<br />";
		echo "</p></blockquote>";
//Show if the type of email is selected
	} elseif ($_GET['type'] == ("users" || "all" || "organizations" || "allOrganizations" || "roles") || isset($_GET['id'])) {
		function singleEmail($type) {
			if ($type == "from") {
				$userData = userData();
			} else {
				$userData = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}'");
				
				if (!$userData) {
					redirect("send_email.php");
				}
			}
			
			if ($userData['emailAddress2'] == "" && $userData['emailAddress3'] == "") {
				hidden("toList", "toList", $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress1'] . ">");
				hidden("userData", "userData", $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress1'] . ">");
				echo "<strong>" . $userData['firstName'] . " " . $userData['lastName'] . " &lt;" . $userData['emailAddress1'] . "&gt;</strong>";
			} else {
				$userDataValuesPrep = $userData['firstName'] . " " . $userData['lastName'] . " &lt;" . $userData['emailAddress1'] . "&gt;,";
				$userDataIDsPrep = $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress1'] . ">,";
				$selected = $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress1'] . ">";
				
				if ($userData['emailAddress2'] != "") {
					$userDataValuesPrep .= $userData['firstName'] . " " . $userData['lastName'] . " &lt;" . $userData['emailAddress2'] . "&gt;,";
					$userDataIDsPrep .= $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress2'] . ">,";
					
					if (isset($_GET['address']) && $_GET['address'] == "2") {
						$selected = $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress2'] . ">";
					}
				}
				
				if ($userData['emailAddress3'] != "") {
					$userDataValuesPrep .= $userData['firstName'] . " " . $userData['lastName'] . " &lt;" . $userData['emailAddress3'] . "&gt;,";
					$userDataIDsPrep .= $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress3'] . ">,";
					
					if (isset($_GET['address']) && $_GET['address'] == "3") {
						$selected = $userData['firstName'] . " " . $userData['lastName'] . " <" . $userData['emailAddress3'] . ">";
					}
				}
				
				$userDataValues = rtrim($userDataValuesPrep, ",");
				$userDataIDs = rtrim($userDataIDsPrep, ",");
				
				dropDown($type, $type, $userDataValues, $userDataIDs, false, false, false, $selected);
			}
		}
		
		form("sendEmail", "post", true);
		catDivider("Settings", "one", true);
		echo "<blockquote>";
		directions("From", true);
		echo "<blockquote><p>";
		singleEmail("from");
		echo "</p></blockquote>";
		
		if (!isset($_GET['id'])) {
			if ($_GET['type'] == "users" || $_GET['type'] == "organizations" || $_GET['type'] == "roles") {
				hidden("toDetirmine", "toDetirmine", $_GET['type']);
				directions("To", true);
				echo "<blockquote><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential options:</h3><div class=\"collapseElement\">";
				textField("placeHolder", "placeHolder", false, false, false, false);
				echo "</div><div align=\"center\">";
				dropDown("notToList", "notToList", $potentialValues, $potentialIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferRight()\"");
				echo "<br /><br />";
				button("allRight", "allRight", "All &gt;&gt;", "button", false, " onclick=\"opt.transferAllRight()\"");
				echo " ";
				button("right", "right", "&gt;&gt;", "button", false, " onclick=\"opt.transferRight()\"");
				echo "</div></div><div class=\"halfRight\"><h3>Selected options:</h3><div class=\"collapseElement\">";
				textField("toImport", "toImport", false, false, false, true, false, false, false, false, " readonly=\"readonly\"");
				echo "</div><div align=\"center\">";
				dropDown("toList", "toList", $selectedValues, $selectedIDs, true, false, false, false, false, false, " ondblclick=\"opt.transferLeft()\"");
				echo "<br /><br />";
				button("left", "left", "&lt;&lt;", "button", false, " onclick=\"opt.transferLeft()\"");
				echo " ";
				button("allLeft", "allLeft", "&lt;&lt; All", "button", false, " onclick=\"opt.transferAllLeft()\"");
				echo "</div></div></div></blockquote>";
			} else {
				$type = str_replace("all", "", $_GET['type']);
				
				if (empty($type)) {
					$type = "users";
				}
				
				directions("To", false);
				hidden("toDetirmine", "toDetirmine", $_GET['type']);
				hidden("toList", "toList", "all" . ucfirst($type));
				echo "<blockquote><p><strong>This email will be sent to all registered " . strtolower($type) . ".</strong></p></blockquote>";
			}
		} else {
			directions("To", false);
			hidden("toDetirmine", "toDetirmine", "Selected " . ucfirst($_GET['type']));
			echo "<blockquote><p>";
			singleEmail("toList");
			echo "</p></blockquote>";
		}
		
		directions("Subject", true);
		echo "<blockquote><p>";
		textField("subject", "subject");
		echo "</p></blockquote>";
		directions("Priority", false);
		echo "<blockquote><p>";
		dropDown("priority", "priority", "Low,Normal,High", "5,3,1", false, false, false, "3");
		echo "</p></blockquote></blockquote>";
		
		catDivider("Message", "two");
		echo "<blockquote>";
		directions("Enter the message of the email below", true);
		echo "<blockquote><p>";
		textArea("message", "message", "large");
		echo "</p></blockquote></blockquote>";
		
		catDivider("Attachment", "three");
		echo "<blockquote>";
		directions("Add an attachment", false);
		echo "<blockquote><p>";
		fileUpload("attachment", "attachment", false, false);
		echo "</p></blockquote></blockquote>";
		
		catDivider("Finish", "four");
		echo "<blockquote><p>";
		button("submit", "submit", "Submit", "submit");
		button("reset", "reset", "Reset", "reset");
		button("cancel", "cancel", "Cancel", "cancel", "index.php");
		echo "</p>";
		closeForm(true, true);
//Redirect if the requested type doesn't exist
	} else {
		redirect("send_email.php");
	}
	
//Include the footer
	footer();
?>