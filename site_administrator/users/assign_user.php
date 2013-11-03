<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Grant access to this page an id is defined and the user exists
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$userGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
		if ($userCheck = mysql_fetch_array($userGrabber)) {
			$user = $userCheck;
			if ($user['role'] == "Site Administrator" || $user['role'] == "Site Manager") {
				header ("Location: assign_user.php?message=errorAssign");
				exit;
			}
		} else {
			$user = false;
			header("Location: index.php");
			exit;
		}
	} else {
		$user = false;
	}
	
//Process the form
	if ($user == false) {
		if (isset ($_POST['submit']) && !empty ($_POST['user']) && !empty($_POST['organization'])) {
			$organization = $_POST['organization'];
			$user = $_POST['user'];
			$userArray = explode(" ", $user);
			$firstName = $userArray[0];
			$lastName = $userArray[1];
			
			$userGrabber = mysql_query("SELECT * FROM `users` WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
			if ($userData = mysql_fetch_array($userGrabber)) {
				if ($userData['role'] == "Site Administrator" || $userData['role'] == "Site Manager") {
					header ("Location: assign_user.php?message=errorAssign");
					exit;
				}
			} else {
				header ("Location: assign_user.php?message=noUser");
				exit;
			}
			
			$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'", $connDBA);
			if ($organizationInfo = mysql_fetch_array($organizationGrabber)) {
				$organizationID = $organizationInfo['id'];
			} else {
				header ("Location: assign_user.php?message=noOrganization");
				exit;
			}
			
			mysql_query("UPDATE `users` SET `organization` = '{$organizationID}' WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}' LIMIT 1", $connDBA);
			
			$userIDGrabber = mysql_query("SELECT * FROM `users` WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
			$userIDArray = mysql_fetch_array($userIDGrabber);
			$userID = $userIDArray['id'];
			$organizationIDGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'", $connDBA);
			$organizationIDArray = mysql_fetch_array($organizationIDGrabber);
			$organizationID = $organizationIDArray['id'];
			
			header ("Location: index.php?userID=" . $userID . "&orgID=" . $organizationID . "&message=assignedUser");
			exit;
		}
	} else {
		if (isset ($_POST['submit']) && !empty($_POST['organization'])) {
			$organization = $_POST['organization'];
			
			$userGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$id}'", $connDBA);
			if ($user = mysql_fetch_array($userGrabber)) {
				if ($user['role'] == "Site Administrator" || $user['role'] == "Site Manager") {
					if ($user != false) {
						header ("Location: assign_user.php?id=" . $id . "&message=errorAssign");
						exit;
					} else {
						header ("Location: assign_user.php?message=errorAssign");
						exit;
					}
				}
			}
			
			$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'", $connDBA);
			if ($organizationInfo = mysql_fetch_array($organizationGrabber)) {
				$organizationID = $organizationInfo['id'];
			} else {
				if ($user != false) {
					header ("Location: assign_user.php?id=" . $id . "&message=noOrganization");
					exit;
				} else {
					header ("Location: assign_user.php?message=noOrganization");
					exit;
				}
			}
			
			mysql_query("UPDATE `users` SET `organization` = '{$organizationID}' WHERE `id` = '{$id}' LIMIT 1", $connDBA);
			
			$organizationIDGrabber = mysql_query("SELECT * FROM `organizations` WHERE `name` = '{$organization}'", $connDBA);
			$organizationIDArray = mysql_fetch_array($organizationIDGrabber);
			$organizationID = $organizationIDArray['id'];
			
			header ("Location: index.php?userID=" . $id . "&orgID=" . $organizationID . "&message=assignedUser");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Assign User") ?>
<?php headers(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/autoSuggest/runAutoSuggest.js" type="text/javascript"></script>
<script src="../../javascripts/autoSuggest/autoSuggestOptions.js" type="text/javascript"></script>
<script src="../../javascripts/autoSuggest/autoSuggestCore.js" type="text/javascript"></script>
<link type="text/css" href="../../styles/common/autoSuggest.css" rel="stylesheet" />
<script language="JavaScript" type="text/javascript">
  var nameData = new Spry.Data.XMLDataSet("suggest_users.php","/root/name", {sortOnLoad: "name"});
  var organizationData = new Spry.Data.XMLDataSet("suggest_organizations.php","/root/organization", {sortOnLoad: "organization"});
</script>
</head>

<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Assign User</h2>
<?php
//If no user is set to assign, then force the administrator to pick from a list
	if ($user == false) {
		echo "<p>Assign an existing user to an organization.</p>";
	} else {
		echo "<p>Assign <strong>" . $user['firstName'] . " " . $user['lastName'] . "</strong> to an organization.</p>";
	}
?>
<?php
//If the user is given an error when assigning an administrator to an orgainzation
	if (isset($_GET['message']) && $_GET['message'] == "errorAssign") {
		errorMessage("Site administrators and site managers cannot be assigned to an organization. Please change their role if you wish to assign them.");
//If the user is given an error that an assigned user does not exist
	} elseif (isset($_GET['message']) && $_GET['message'] == "noUser") {
		errorMessage("The user you are attempting to assign to an organization does not exist.");
//If the user is given an error that an assigned organization does not exist
	} elseif (isset($_GET['message']) && $_GET['message'] == "noOrganization") {
		errorMessage("The organization you are attempting to assign to a user does not exist.");
	} else {
		echo "<p>&nbsp;</p>";
	}
?>
<form name="assignUser" method="post" action="assign_user.php<?php if ($user !== false) {echo"?id=" . $id;} ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider one">Assign User</div>
<div class="stepContent">
  <blockquote>
    <p>User<?php if ($user == false) {echo "<span class=\"require\">*</span>";}?>:</p>
    <blockquote>
    <?php
	//If no user is set to assign, then force the administrator to pick from a list
		if ($user == false) {
			echo "<div id=\"userSuggest\"><input name=\"user\" id=\"user\" type=\"text\" size=\"50\" autocomplete=\"off\" class=\"validate[required]\" /><div><div id=\"nameSuggestions\" spry:region=\"nameData\"><div spry:repeat=\"nameData\" spry:suggest=\"{name}\">{name}</div></div></div></div>";
		} else {
			echo "<strong>" . $user['firstName'] . " " . $user['lastName'] . "</strong>";
		}
	?>
      <script type="text/javascript">
		 var nameSuggestions = new Spry.Widget.AutoSuggest("userSuggest", "nameSuggestions", "nameData", "name", {containsString: true});
	  </script>
    </blockquote>
    <p>Organization<span class="require">*</span>:</p>
    <blockquote>
      <div id="organizationSuggest">
        <input name="organization" id="organization" type="text" size="50" autocomplete="off" class="validate[required]" />
        <div>
        <div id="organizationSuggestions" spry:region="organizationData">
        <div spry:repeat="organizationData" spry:suggest="{organization}">{organization}</div>
        </div>
        </div>
      </div>
      <script type="text/javascript">
		 var organizationSuggestions = new Spry.Widget.AutoSuggest("organizationSuggest", "organizationSuggestions", "organizationData", "organization", {containsString: true});
	  </script>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider two">Submit</div>
<div class="stepContent">
  <blockquote>
    <p>
		<?php submit("submit", "Submit"); ?>
        <input name="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" type="reset">
        <input name="cancel" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" type="button">

    </p>
  <?php formErrors(); ?>
  </blockquote>
</div>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>