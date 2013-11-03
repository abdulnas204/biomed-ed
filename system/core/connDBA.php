<?php
/* Begin system functions */

	//A function to write the ajax navigation for a page
	function navigate($dataSource, $type) {
		if ($type == "bottom") {
			echo "<br />";
		}
		
		echo "<div align=\"center\">";
		echo "<span spry:region=\"" . $dataSource . "PagedInfo\" align=\"center\">";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 1\">";
		echo URL("Previous Page", "javascript:void", "spacerLeft previousPage", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 1 != '1'\" onclick=\"" . $dataSource . ".previousPage(); return false;\"");
		echo URL("1", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} >= 13 && {ds_PageCount} > 25\" onclick=\"" . $dataSource . ".goToPage('1'); return false;\"");
			
		echo "<span spry:if=\"{ds_CurrentRowNumber} >= 14 && {ds_PageCount} > 25 && {ds_PageCount} != 26\" class=\"currentSearchNumber\">...</span>";
		echo "</span>";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 0\" spry:repeatchildren=\"" . $dataSource . "PagedInfo\" class=\"search\">";
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 1 <= 13 && {ds_PageNumber} <= 25 && {ds_CurrentRowNumber} + 13 < {ds_PageCount}\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"");
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 1 > 13 && {ds_CurrentRowNumber} + 13 < {ds_PageCount} && ({ds_CurrentRowNumber} < {ds_PageNumber} + 12 && {ds_CurrentRowNumber} > {ds_PageNumber} - 14)\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"");
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 13 >= {ds_PageCount} && {ds_PageNumber} >= {ds_PageCount} - 24\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"");
		echo "<span spry:if=\"{ds_CurrentRowNumber} == {ds_RowNumber} && {ds_PageCount} > 1\" class=\"currentSearchNumber\">{ds_PageNumber}</span>";
		echo "</span>";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 0\">";
		echo "<span spry:if=\"{ds_CurrentRowNumber} + 14 < {ds_PageCount} && {ds_PageCount} > 25 && {ds_PageCount} != 26\" class=\"currentSearchNumber\">...</span>";
		echo URL("{ds_PageCount}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 14 <= {ds_PageCount} && {ds_PageCount} > 25\" onclick=\"" . $dataSource . ".goToPage('{ds_PageCount}'); return false;\"");
		echo URL("Next Page", "javascript:void", "spacerRight nextPage", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 2 <= {ds_PageCount}\" onclick=\"" . $dataSource . ".nextPage(); return false;\"");
		echo "</span>";
		echo "</span>";
		echo "</div>";
		
		if ($type == "top") {
			echo "<br />";
		}
	}
	
	//Find the difference between two dates
	function dateDifference($firstDate, $secondDate, $precision = false) {
		$date = $firstDate - $secondDate;
		$return = "";
		
		if ($date >= 31556926) {
			$return .= floor($date/31556926);
			
			if (floor($date/31556926) == "1") {
				$return .= " Year ";
			} else {
				$return .= " Years ";
			}
			
			$date = ($date%31556926);
		}
		
		if (strtolower($precision) == "years") {
			return $return;
			exit;
		}
		
		if ($date >= 2629744) {
			$return .= floor($date/2629744);
			
			if (floor($date/2629744) == "1") {
				$return .= " Month ";
			} else {
				$return .= " Months ";
			}
			
			$date = ($date%2629744);
		}
		
		if (strtolower($precision) == "months") {
			return $return;
			exit;
		}
		
		if ($date >= 86400) {
			$return .= floor($date/86400);
			
			if (floor($date/86400) == "1") {
				$return .= " Day ";
			} else {
				$return .= " Days ";
			}
			
			$date = ($date%86400);
		}
		
		if (strtolower($precision) == "days") {
			return $return;
			exit;
		}
		
		if ($date >= 3600) {
			$return .= floor($date/3600);
			
			if (floor($date/3600) == "1") {
				$return .= " Hour ";
			} else {
				$return .= " Hours ";
			}
			
			$date = ($date%3600);
		}
		
		if (strtolower($precision) == "hours") {
			return $return;
			exit;
		}
		
		if ($date >= 60) {
			$return .= floor($date/60);
			
			if (floor($date/60) == "1") {
				$return .= " Min ";
			} else {
				$return .= " Mins ";
			}
			
			$date = ($date%60);
		}
		
		if (strtolower($precision) == "minutes") {
			return $return;
			exit;
		}
		
		$return .= $date;
		
		if ($date == "1") {
			$return .= " Sec ";
		} else {
			$return .= " Secs ";
		}
		
		return $return;
	}
	
	//A function to safely mail automated emails
	function autoEmail($to, $subject, $message) {
		$domain = explode(":", $_SERVER['HTTP_HOST']);
		
		if (mail($to, $subject, $message, "From: No Reply<no-reply@" . $domain['0'] . ">")) {
			//Do nothing, the email was sent
		} else {
			die("Error sending automated email.");
		}
	}
/* End system functions */
	
/* Begin statistics tracker */
	//Set the activity meter
	function activity($setActivity = "false") {
		global $root;
		global $connDBA;
		
		if ($setActivity == "true" && isset($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			$activityTimestamp = time();
			mysql_query("UPDATE `users` SET `active` = '{$activityTimestamp}' WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		}
	}
	
	//Overall statistics
	function stats($doAction = "false") {
		global $root;
		global $connDBA;
		
		if ($doAction == "true") {
			$date = date("M-d-Y");
			$statisticsCheck = mysql_query("SELECT * FROM `overallstatistics` WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			
			if ($result = mysql_fetch_array($statisticsCheck)) {
				$newHit = $result['hits']+1;
				mysql_query("UPDATE `overallstatistics` SET `hits` = '{$newHit}' WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			} else {
				mysql_query("INSERT INTO `overallstatistics` (
							`id`, `date`, `hits`
							) VALUES (
							NULL, '{$date}', '1'
							)");
			}
			
			if (loggedIn()) {
				$userData = userData();
					
				if ($userData['organization'] != "0") {
					$statisticsCheck = mysql_query("SELECT * FROM `organizationstatistics_{$userData['organization']}` WHERE `date` = '{$date}' LIMIT 1", $connDBA);
					
					if ($result = mysql_fetch_array($statisticsCheck)) {
						$newHit = $result['hits']+1;
						mysql_query("UPDATE `organizationstatistics_{$userData['organization']}` SET `hits` = '{$newHit}' WHERE `date` = '{$date}' LIMIT 1", $connDBA);
					} else {
						mysql_query("INSERT INTO `organizationstatistics_{$userData['organization']}` (
									`id`, `date`, `hits`
									) VALUES (
									NULL, '{$date}', '1'
									)");
					}
				}
			}
		}
	}
/* End statistics tracker */

//Force user to change password if required
	if (loggedIn()) {
		$userData = userData();
		$organizationStatus = query("SELECT * FROM `organizations` WHERE `id` = '{$userData['organization']}'");
		$URL = $_SERVER['REQUEST_URI'];
		
		if ($userData['changePassword'] == "on" && !strstr($URL, "logout.php")) {
		//Process the form
			if (isset ($_POST['submitPassword']) && !empty($_POST['oldPassword']) && !empty($_POST['newPassword']) && !empty($_POST['confirmPassword'])) {
				$userName = $_SESSION['MM_Username'];
				$oldPassword = encrypt($_POST['oldPassword']);
				$newPassword = encrypt($_POST['newPassword']);
				$confirmPassword = encrypt($_POST['confirmPassword']);
				$passwordGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' AND `passWord` = '{$oldPassword}'", $connDBA);
				$password = mysql_fetch_array($passwordGrabber);
				
				if ($password && $newPassword === $confirmPassword) {
					if ($password['passWord'] != $newPassword) {
						mysql_query("UPDATE `users` SET `passWord` = '{$newPassword}', `changePassword` = '' WHERE `userName` = '{$userName}' AND `passWord` = '{$oldPassword}'", $connDBA);
						
						redirect($root . "portal/index.php");
						exit;
					} else {
						redirect($_SERVER['PHP_SELF'] . "?password=identical");
						exit;
					}
				} else {
					redirect($_SERVER['PHP_SELF'] . "?password=error");
					exit;
				}
			}
			
		//Display the content	
		//Top content
			headers("Change Password", false, "validate");
			
		//Title
			if (!isset($_GET['password'])) {
				title("Change Password", "You are required to change your password before using this site.");
			} else {
				title("Change Password", "You are required to change your password before using this site.", false);
			}
			
		//Display message updates
			message("password", "error", "error", "Either your old password is incorrect, or your new password does not match.");
			message("password", "identical", "error", "Your old password may not be the same as your new password.");
			
		//Password form
			form("updatePassword");			
			echo "<blockquote>";
			directions("Current password", true);
			echo "<blockquote><p>";
			textField("oldPassword", "oldPassword", false, false, true, true);
			echo "</p></blockquote>";
			directions("New password", true);
			echo "<blockquote><p>";
			textField("newPassword", "newPassword", false, false, true, true, ",length[6,30]");
			echo "</p></blockquote>";
			directions("Confirm new password", true);
			echo "<blockquote><p>";
			textField("confirmPassword", "confirmPassword", false, false, true, true, ",length[6,30],confirm[newPassword]");
			echo "</p></blockquote>";
			echo "<blockquote><p>";
			button("submitPassword", "submitPassword", "Submit", "submit");
			echo "</p></blockquote></blockquote>";
			closeForm(false, true);
			
		//Display the footer
			footer();
			
		//Exit so the rest of the page is not loaded
			exit;
		}
	}
	
//Force administrator to setup an organization if needed
	if (loggedIn() && $_SESSION['MM_UserGroup'] == "Organization Administrator" && !strstr($_SERVER['REQUEST_URI'], "manage_organization.php") && !strstr($_SERVER['REQUEST_URI'], "logout.php") && (empty($organizationStatus['specialty']) || empty($organizationStatus['webSite']) || empty($organizationStatus['phone']) || empty($organizationStatus['fax']) || empty($organizationStatus['mailingAddress1']) || empty($organizationStatus['mailingCity']) || empty($organizationStatus['mailingState']) || empty($organizationStatus['mailingZIP']) || empty($organizationStatus['billingAddress1']) || empty($organizationStatus['billingCity']) || empty($organizationStatus['billingState']) || empty($organizationStatus['billingZIP']) || empty($organizationStatus['billingPhone']) || empty($organizationStatus['billingFax']) || empty($organizationStatus['billingEmail']) || empty($organizationStatus['timeZone']))) {
		redirect($root . "organizations/manage_organization.php");
	}
?>