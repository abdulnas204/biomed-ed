<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Process the form
	if (isset ($_POST['submit'])) {
	//Get all of the form fields
		$from = $_POST['from'];
		$toDetirmine = $_POST['toDetirmine'];
		$toImport = $_POST['toImport'];
		$subject = $_POST['subject'];
		$priority = $_POST['priority'];
		$body = $_POST['message'];
		$attachment = $_FILES['attachment'];
		
	//Whom the email is from
		$header = "From: " . $from;
		
	//Detirmine what kind of mass email is being sent
		if ($toDetirmine == "users") {
			$to = $toImport;
		} elseif ($toDetirmine == "all") {
			$toGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
			$to = "";
			
			while($toData = mysql_fetch_array($toGrabber)) {
				$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
			}
		} elseif ($toDetirmine == "organizations") {
			$toArray = explode(",", $toImport);
			$toArraySize = sizeof($toArray);
			$to = "";
			
			for ($count = 0; $count <= $toArraySize; $count++) {
				$organization = $toArraySize[$count];
				$toGrabber = mysql_query("SELECT * FROM `users` WHERE `organization` = '{$organization}'", $connDBA);
				
				while($toData = mysql_fetch_array($toGrabber)) {
					$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
				}
			}
		} elseif ($toDetirmine == "allOrganizations") {
			$toGrabber = mysql_query("SELECT * FROM `users` WHERE `organization` != '1'", $connDBA);
			
			while($toData = mysql_fetch_array($toGrabber)) {
				$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
			}
		} elseif ($toDetirmine == "roles") {
			$toArray = explode(",", $toImport);
			$toArraySize = sizeof($toArray);
			$to = "";
			
			for ($count = 0; $count <= $toArraySize; $count++) {
				$role = $toArraySize[$count];
				$toGrabber = mysql_query("SELECT * FROM `users` WHERE `role` = '{$role}'", $connDBA);
				
				while($toData = mysql_fetch_array($toGrabber)) {
					$to .= $toData['firstName'] . " " . $toData['lastName'] . " <" . $toData['emailAddress1'] . ">,";
				}
			}
		}
		
	//Grab the attachment
		$fileTempName = $_FILES['attachment']['tmp_name'];
		$fileType = $_FILES['attachment']['type'];
		$fileName = basename($_FILES['attachment'] ['name']);
		
	//Check to see that the file was uploaded properly
		if (is_uploaded_file($fileTempName)) {
		//Pre-processing
			// Grab the attachment info
				$file = fopen($fileTempName, 'rb');
				$data = fread($file, filesize($fileTempName));
				fclose ($file);
			
			//Generate the boundary line string
				$random = md5(time());
				$mimeBoundary = "==Multipart_Boundary_x{$random}x";
			
		//Processing			
			//The header information of the email, including the "From" field					
				$header .= "\nMIME-Version: 1.0\n" .
						  "Content-Type: multipart/mixed;\n" .
						  "X-Mailer: PHP/" . phpversion() . "\n" . 
						  "X-Priority: " . $priority . "\n" . 
						  " boundary=\"{$mimeBoundary}\"";
				
			//The message of the email
				$message = "This is a multi-part message in MIME format.\n\n" . 
							"--{$mimeBoundary}\n" . 
							"Content-Type: text/html; charset=\"iso-8859-1\"\n" . 
							"Content-Transfer-Encoding: 7bit\n\n" . 
							$body . "\n\n";
							
				$data = chunk_split(base64_encode($data));
				$message .= "--{$mimeBoundary}\n" .
							"Content-Type: {$fileType};\n" . 
							" name = \"{$fileName}\"\n" . 
							"Content-Transfer-Encoding: base64\n\n" . 
							$data . "\n\n" .    
							"--{$mimeBoundary}--\n";
		} else {
		//Pre-processing
			//Generate the boundary line string
				$random = md5(time());
				$mimeBoundary = "==Multipart_Boundary_x{$random}x";
			
		//Processing			
			//The header information of the email, including the "From" field					
				$header .= "\nMIME-Version: 1.0\n" .
						  "Content-Type: multipart/mixed;\n" .
						  "X-Mailer: PHP/" . phpversion() . "\n" . 
						  "X-Priority: " . $priority . "\n" . 
						  " boundary=\"{$mimeBoundary}\"";
				
			//The message of the email
				$message = "This is a multi-part message in MIME format.\n\n" . 
							"--{$mimeBoundary}\n" . 
							"Content-Type: text/html; charset=\"iso-8859-1\"\n" . 
							"Content-Transfer-Encoding: 7bit\n\n" . 
							$body . "\n\n" .    
							"--{$mimeBoundary}--\n";
		}
		
		//Send the email!
			mail("Oliver Spryn <wot200@gmail.com>", $subject, $message, $header);
			
		//Display a confirmation
			header ("Location: index.php?result=success");
			exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
<head>
<?php title("Send Email"); ?>
<?php headers(); ?>
<?php validate(); ?>
<?php tinyMCEAdvanced(); ?>
<script src="../../../javascripts/common/optionTransfer.js" type="text/javascript"></script>
<script src="../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?><?php if (isset($_GET['type'])) {if ($_GET['type'] == "users" || $_GET['type'] == "organizations" || $_GET['type'] == "roles") {echo " onload=\"opt.init(document.forms[0])\"";}} ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Send an Email</h2>
<p>Send an email to multiple users, or organizations within this system. Please note that this is not an online email system. This is only used to send a mass email.</p>
<p>&nbsp;</p>
<?php
//If the type of user is being selected
	if (!isset($_GET['type'])) {
		echo "<blockquote><p><a href=\"index.php?type=users\">Selected Users</a> - Only selected users will recieve this email<br /><a href=\"index.php?type=all\">All Users</a> - All registered users will recieve this email<br /><a href=\"index.php?type=organizations\">Selected Organizations</a> - All users within selected organizations will recieve this email<br /><a href=\"index.php?type=allOrganizations\">All Organizations</a> - All registered organizations will recieve this email<br /><a href=\"index.php?type=roles\">Selected Roles</a> - All users with a selected role will recieve this email</p></blockquote>";
	} else {
?>
<form action="index.php?type=users" method="post" enctype="multipart/form-data" name="sendEmail" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider one">Settings</div>
<div class="stepContent">
<blockquote>
  <p>From:</p>
  <blockquote>
    <?php
	//Select the from email address
		$userName = $_SESSION['MM_Username'];
		$fromGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		$from = mysql_fetch_array($fromGrabber);
		
		if ($from['emailAddress2'] == "" && $from['emailAddress3'] == "") {
			echo "<input type=\"hidden\" name=\"from\" id=\"from\" value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress1'] . ">\" /><p><strong>" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress1'] . "&gt;</strong></p>";
		} else {
			echo "<select name=\"from\" id=\"from\"><option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress1'] . ">\" selected=\"selected\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress1'] . "&gt;</option>";
			
			if ($from['emailAddress2'] != "") {
				echo "<option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress2'] . ">\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress2'] . "&gt;</option>";
			}
			
			if ($from['emailAddress3'] != "") {
				echo "<option value=\"" . $from['firstName'] . " " . $from['lastName'] . " <" . $from['emailAddress3'] . ">\">" . $from['firstName'] . " " . $from['lastName'] . " &lt;" . $from['emailAddress3'] . "&gt;</option>";
			}
			
			echo "</select>";
		}
	?>
  </blockquote>
  <p>To<?php if (isset($_GET['type'])) {if ($_GET['type'] == "users" || $_GET['type'] == "organizations" || $_GET['type'] == "roles") {echo "<span class=\"require\">*</span>";}} ?>:</p>
  <blockquote>
    <?php
    //Grab all required values
        switch ($_GET['type']) {
            case "users" : 
				$usersGrabber = mysql_query("SELECT * FROM `users` ORDER BY `firstName` ASC", $connDBA);
				
				echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"all\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential users:</h3><div style=\"visibility:hidden;\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notTo\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\">";
				while($users = mysql_fetch_array($usersGrabber)) {
					echo "<option value=\"" . $users['firstName'] . " " . $users['lastName'] . " <" . $users['emailAddress1'] . ">\">" . $users['firstName'] . " " . $users['lastName'] . "</option>";
				}
				echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected users:</h3><div style=\"visibility:hidden;\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\"></div><div align=\"center\"><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\" class=\"validate[required]\"></select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>"; break;
				
			case "all" : echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"all\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"all\" /><p><strong>This email will be sent to all registered users.</strong></p>"; break;
			
			case "organizations" : 
				$organizationsGrabber = mysql_query("SELECT * FROM `organizations` ORDER BY `organization` ASC", $connDBA);
				
				echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"all\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential organizations:</h3><div style=\"visibility:hidden;\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notToList\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\">";
				while($organizations = mysql_fetch_array($organizationsGrabber)) {
					echo "<option value=\"" . $organizations['organization'] . "\">" . $organizations['organization'] . "</option>";
				}
				echo "</select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected organizations:</h3><div align=\"center\"><div style=\"visibility:hidden;\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\"></div><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\"></select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>"; break;
				
			case "allOrganizations" : echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"allOrganizations\" /><input type=\"hidden\" name=\"toImport\" id=\"toImport\" value=\"allOrganizations\" /><p><strong>This email will be sent to all registered organizations.</strong></p>"; break;
			
			case "roles" : 
				echo "<input type=\"hidden\" name=\"toDetirmine\" id=\"toDetirmine\" value=\"roles\" /><div class=\"layoutControl\"><div class=\"halfLeft\"><h3>Potential roles:</h3><div style=\"visibility:hidden;\"><input type=\"text\" name=\"placeHolder\" id=\"placeHolder\"></div><div align=\"center\"><select name=\"notToList\" id=\"notToList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferRight()\"><option value=\"Administrative Assistant\">Administrative Assistant</option><option value=\"Instructor\">Instructor</option><option value=\"Instructorial Assisstant\">Instructorial Assisstant</option><option value=\"Organization Administrator\">Organization Administrator</option><option value=\"Site Administrator\">Site Administrator</option><option value=\"Site Manager\">Site Manager</option><option value=\"Student\">Student</option></select><br /><br /><input type=\"button\" name=\"right\" value=\"All &gt;&gt;\" onclick=\"opt.transferAllRight()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&gt;&gt;\" onclick=\"opt.transferRight()\"></div></div><div class=\"halfRight\"><h3>Selected roles:</h3><div style=\"visibility:hidden;\"><input type=\"text\" name=\"toImport\" id=\"toImport\" class=\"validate[required]\"></div><div align=\"center\"><select name=\"toList\" id=\"toList\" class=\"multiple\" multiple=\"multiple\" ondblclick=\"opt.transferLeft()\"></select><br /><br /><input type=\"button\" name=\"right\" value=\"&lt;&lt;\" onclick=\"opt.transferLeft()\">&nbsp;&nbsp;<input type=\"button\" name=\"right\" value=\"&lt;&lt; All\" onclick=\"opt.transferAllLeft()\"></div></div></div>"; break;
        }
    ?>
    </blockquote>
  <p>Subject<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <input name="subject" type="text" id="subject" size="50" />
    </p>
    </blockquote>
  <p>Priority:</p>
  <blockquote>
    <p>
      <select name="priority" id="priority">
        <option value="5">Low</option>
        <option value="3" selected="selected">Normal</option>
        <option value="1">High</option>
        </select>
    </p>
  </blockquote>
</blockquote>
</div>
<div class="catDivider two">Message</div>
<div class="stepContent">
  <blockquote>
    <p>
      Enter the message of the email below<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <textarea name="message" id="message" cols="45" rows="5" style="width:640px; height:320px;" /></textarea>
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider three">Attachments</div>
<div class="stepContent">
  <blockquote>
    <p>
      Add an attachment:</p>
    <blockquote>
      <p>
        <input name="attachment" type="file" id="attachment" size="50" />
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider four">Submit</div>
<div class="stepContent">
  <blockquote>
    <p>
      <?php submit("submit", "Send Email"); ?>
      <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
      <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
    </p>
    <?php formErrors(); ?>
  </blockquote>
</div>
</form>
<?php
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>