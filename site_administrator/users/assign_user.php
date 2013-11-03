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
				header ("Location: index.php?message=errorAssign");
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
					header ("Location: index.php?message=errorAssign");
					exit;
				}
			} else {
				header ("Location: index.php?message=noUser");
				exit;
			}
			
			$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'", $connDBA);
			if (mysql_fetch_array($organizationGrabber)) {
			} else {
				header ("Location: index.php?message=noOrganization");
				exit;
			}
			
			mysql_query("UPDATE `users` SET `organization` = '$organization' WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
			
			header ("Location: index.php?message=assignedUser");
			exit;
		}
	} else {
		if (isset ($_POST['submit']) && !empty($_POST['organization'])) {
			$organization = $_POST['organization'];
			
			$userGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$id}'", $connDBA);
			if ($user = mysql_fetch_array($userGrabber)) {
				if ($user['role'] == "Site Administrator" || $user['role'] == "Site Manager") {
					header ("Location: index.php?message=errorAssign");
				}
			}
			
			$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'", $connDBA);
			if (mysql_fetch_array($organizationGrabber)) {
			} else {
				header ("Location: index.php?message=noOrganization");
				exit;
			}
			
			mysql_query("UPDATE `users` SET `organization` = '$organization' WHERE `id` = '{$id}'", $connDBA);
			header ("Location: index.php?message=assignedUser");
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
<p>&nbsp;</p>
<form name="assignUser" method="post" action="assign_user.php<?php if ($user !== false) {echo"?id=" . $id;} ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider"><img src="../../images/numbering/1.gif" alt="1." width="22" height="22" /> Assign User</div>
<div class="stepContent">
  <blockquote>
    <p>User:</p>
    <blockquote>
      <p>
    <?php
	//If no user is set to assign, then force the administrator to pick from a list
		if ($user == false) {
			echo "<div id=\"userSuggest\"><input name=\"user\" id=\"user\" type=\"text\" size=\"50\" autocomplete=\"off\" class=\"validate[required]\" /><div><div id=\"nameSuggestions\" spry:region=\"nameData\"><span spry:repeat=\"nameData\" spry:suggest=\"{name}\"><div>{name}</div></span></div></div></div>";
		} else {
			echo "<strong>" . $user['firstName'] . " " . $user['lastName'] . "</strong>";
		}
	?>
      </p>
      <script type="text/javascript">
		 var nameSuggestions = new Spry.Widget.AutoSuggest("userSuggest", "nameSuggestions", "nameData", "name");
	  </script>
    </blockquote>
    <p>Organization:</p>
    <blockquote>
      <div id="organizationSuggest">
        <input name="organization" id="organization" type="text" size="50" autocomplete="off" class="validate[required]" />
        <div>
        <div id="organizationSuggestions" spry:region="organizationData">
        <span spry:repeat="organizationData" spry:suggest="{organization}">
            <div>{organization}</div>
        </span>
        </div>
        </div>
      </div>
      <script type="text/javascript">
		 var organizationSuggestions = new Spry.Widget.AutoSuggest("organizationSuggest", "organizationSuggestions", "organizationData", "organization");
	  </script>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider"><img src="../../images/numbering/2.gif" alt="2." width="22" height="22" /> Submit</div>
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