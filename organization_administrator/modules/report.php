<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Organization Administrator"); ?>
<?php
//Grab all module data
	$moduleDataCheckGrabber = mysql_query("SELECT * FROM moduledata WHERE `avaliable` = '1' ORDER BY position ASC", $connDBA);
	$moduleDataCheck = mysql_fetch_array($moduleDataCheckGrabber);
	
//Check to see if any modules exist
	$moduleCheck = $moduleDataCheck['id'];
	if (!$moduleCheck) {
		$header ("Location: index.php");
		exit;
	}
?>
<?php
//Grab site name
	$siteNameGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$siteName = mysql_fetch_array($siteNameGrabber);
	
//Grab all of the site administrators and site managers
	$userGrabber = mysql_query("SELECT * FROM users WHERE `role` = 'Site Administrator' || 'Site Manager'", $connDBA);
	$userNumberGrabber = mysql_query("SELECT * FROM users WHERE `role` = 'Site Administrator' || 'Site Manager'", $connDBA);
	$userNumber = mysql_num_rows($userNumberGrabber);
	
	$count = 1;
	$to = "";
	while ($user = mysql_fetch_array($userGrabber)) {
		$to .= $user['firstName'] . " " . $user['lastName'] . " <" . $user['emailAddress1'] . ">";
		if ($count++ == $userNumber) {
			break;
		}
		
		$to .=  ", ";
	}
	
//Grab the email address of the existing user
	$userName = $_SESSION['MM_Username'];
	$senderGrabber = mysql_query("SELECT * FROM users WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
	$sender = mysql_fetch_array($senderGrabber);
	$fromAddress = $sender['emailAddress1'];
	$from = $sender['firstName'] . " " . $sender['lastName'] . " <" . $fromAddress . ">";

//Process outgoing mail
	if (isset ($_POST['faultyModule']) && isset ($_POST['faultyPart']) && isset ($_POST['comments']) && isset ($_POST['submit'])) {	
		$faultyModule = $_POST['faultyModule'];
		$faultyPart = $_POST['faultyPart'];
		$comments = $_POST['comments'];
	
		//The subject of the email
			$subject = $siteName . " Faulty Module Report";
			
		//The message of the email
			$message = $sender['firstName'] . " " . $sender['lastName'] . " from " . $sender['organization'] . " has reported faulty content in the " . $faultyPart . " of the " . $faultyModule . " module. Below are their comments on this issue:<br /> <br />" . $comments;
			
		//Header information, including the "From" field
			$random = md5(time());
			$mimeBoundary = "==Multipart_Boundary_x{$random}x";
		
			$header = "Return-Path: <$fromAddress>\n";
			$header .= "Reply-To: {$fromAddress}\n";
			$header .= "From: {$from}\n";
			$header .= "Bcc: $to\n";
			$header .= "Organization: {$fromName}\n";
			$header .= "X-Mailer: PHP/" . phpversion() . "\n";
			$header .= "X-priority: 1\n";
			$header .= "priority: Urgent\n";
			$header .= "Importance: high\n";
			$header .= "MIME-Version: 1.0\n";
			$header .= "Content-Type: text/html;";

		//Processor:
			$action = mail($to, $subject, $message, $header);
			
			header("Location: index.php?message=reportSent");
			exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Module Administration"); ?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?>>
<?php topPage("organization_administrator/includes/top_menu.php"); ?>
<h2>Report Faulty Content</h2>
<p>If there is faulty content in a particular module, please report this to the site staff.</p>
<p>&nbsp;</p>
<form name="reportContent" action="report.php" method="post" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider"><img src="../../images/numbering/1.gif" alt="1." width="22" height="22" /> Module Information</div>
<div class="stepContent">
  <blockquote>
    <p>Select the faulty module:</p>
    <blockquote>
      <p>
      <?php
	  //Display all of the modules in a drop-down menu
			$moduleDataGrabber = mysql_query("SELECT * FROM moduledata WHERE `avaliable` = '1' ORDER BY name ASC", $connDBA);
			
			echo "<select name=\"faultyModule\" id=\"faultyModule\" class=\"validate[required]\"><option value=\"\">- Select -</option>";
			while ($mouduleData = mysql_fetch_array($moduleDataGrabber)) {
				echo "<option value=\"" . $mouduleData['name']  . "\">" . $mouduleData['name'] . "</option>";
			}
			
			echo "</select>";
	  ?>
      </p>
    </blockquote>
    <p>Select which part of the module is faulty:</p>
    <blockquote>
        <select name="faultyPart" id="faultyPart" class="validate[required]">
          <option value="" selected="selected">- Select -</option>
          <option value="Lesson">Lesson</option>
          <option value="Test">Test</option>
        </select>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider"><img src="../../images/numbering/2.gif" alt="2." width="22" height="22" /> Comments</div>
<div class="stepContent">
  <blockquote>
    <p>Please provide a thorough description of the faulty content:</p>
    <blockquote><span id="commentsCheck">
      <textarea name="comments" id="comments" cols="45" rows="5" style="width:640px; height:320px;"></textarea>
      <span class="textareaRequiredMsg"></span></span></blockquote>
  </blockquote>
</div>
<div class="catDivider"><img src="../../images/numbering/3.gif" alt="3." width="22" height="22" /> Finish</div>
      <div class="stepContent">
      <blockquote>
        <p>
          <?php submit("submit", "Submit"); ?>
          <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
          <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','../test_content.php');return document.MM_returnValue" value="Cancel" />
        </p>
        <?php formErrors(); ?>
      </blockquote>
      </div>
</form>
<?php footer("organization_administrator/includes/bottom_menu.php"); ?>
<script type="text/javascript">
<!--
var sprytextarea1 = new Spry.Widget.ValidationTextarea("commentsCheck", {validateOn:["change"]});
//-->
</script>
</body>
</html>
