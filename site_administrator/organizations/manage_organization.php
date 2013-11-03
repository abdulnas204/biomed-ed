<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Decide whether an organization is being edited or created
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$organizationGrabber = mysql_query("SELECT * FROM organizations WHERE id = '{$id}'", $connDBA);
		if ($organizationCheck = mysql_fetch_array($organizationGrabber)) {
			$organization = $organizationCheck;
		} else {
			header("Location: index.php");
			exit;
		}
	}

//Process the form
	if (isset ($_POST['submit']) && !empty ($_POST['name']) && !empty ($_POST['admin'])) {
	//If the organization is being updated
		if (isset($id)) {
			$name = mysql_real_escape_string($_POST['name']);
			
			$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
			$organization = mysql_fetch_array($organizationGrabber);
			$admin = $organization['admin'];
			$adminArray = explode(" ", $admin);
			$firstName = $adminArray[0];
			$lastName = $adminArray[1];
			
			$adminGrabber = mysql_query("SELECT * FROM `users` WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
			if ($adminData = mysql_fetch_array($adminGrabber)) {
				if ($adminData['role'] == "Site Administrator" || $adminData['role'] == "Site Manager") {
					header ("Location: index.php?message=errorAssign");
					exit;
				} else {
					mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$name}' WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
				}
			} else {
				header ("Location: index.php?message=noUser");
				exit;
			}
			
			$oldNameGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
			$oldNameArray = mysql_fetch_array($oldNameGrabber);
			$oldName = $oldNameArray['organization'];
			
			mysql_query("UPDATE `organizations` SET `organization` =  '{$name}', `admin` = '{$admin}' WHERE `id` = '{$id}'", $connDBA);
			mysql_query("UPDATE `users` SET `organization` = '{$name}' WHERE `organization` = '{$oldName}'", $connDBA);
			header ("Location: index.php?message=organizationEdited");
			exit;
	//If the organization is being added
		} else {
			$sysID = "org_" . randomValue(11, 'alphanum');
			$name = $_POST['name'];
			$admin = $_POST['admin'];
			$adminArray = explode(" ", $admin);
			$firstName = $adminArray[0];
			$lastName = $adminArray[1];
			
			$adminGrabber = mysql_query("SELECT * FROM `users` WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
			if ($adminData = mysql_fetch_array($adminGrabber)) {
				if ($adminData['role'] == "Site Administrator" || $adminData['role'] == "Site Manager") {
					header ("Location: index.php?message=errorAssign");
					exit;
				} else {
					mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$name}' WHERE `firstName` = '{$firstName}' AND `lastName` = '{$lastName}'", $connDBA);
				}
			} else {
				header ("Location: index.php?message=noUser");
				exit;
			}
			
			mysql_query("INSERT INTO organizations (`id`, `sysID`, `changeInfo`, `organization`, `organizationID`, `admin`, `type`, `webSite`, `phone`, `mailingAddress1`, `mailingAddress2`, `mailingCity`, `mailingState`, `mailingZIP`, `billingAddress1`, `billingAddress2`, `billingCity`, `billingState`, `billingZIP`, `billingPhone`, `billingFax`, `billingEmail`, `contractStart`, `contractEnd`, `contractAgreement`, `active`) VALUES (NULL, '{$sysID}', 'on', '{$name}', '1', '{$admin}', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1')", $connDBA);
			header ("Location: index.php?message=organizationCreated")	;
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Create New Organization"); ?>
<?php headers(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/autoSuggest/runAutoSuggest.js" type="text/javascript"></script>
<script src="../../javascripts/autoSuggest/autoSuggestOptions.js" type="text/javascript"></script>
<script src="../../javascripts/autoSuggest/autoSuggestCore.js" type="text/javascript"></script>
<link type="text/css" href="../../styles/common/autoSuggest.css" rel="stylesheet" />
<script language="JavaScript" type="text/javascript">
  var data = new Spry.Data.XMLDataSet("suggest_users.php","/root/name", {sortOnLoad: "name"});
</script>
</head>

<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Create New Organization</h2>
<p>Create a new organization by filling in the information below. The organization's complete details and payment method will be setup when the organization administrator first logs in.</p>
<p>&nbsp;</p>
<form name="manageOrganization" method="post" action="manage_organization.php<?php if (isset($id)) {echo"?id=" . $id;} ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider"><img src="../../images/numbering/1.gif" alt="1." width="22" height="22" /> Assign Organization Name</div>
<div class="stepContent">
  <blockquote>
    <p>
      Assign the Organization a name:</p>
    <blockquote>
      <p>
        <input name="name" type="text" id="name" size="50" autocomplete="off" tabindex="1" class="validate[required]"<?php
			if (isset ($id)) {
				echo " value=\"" . stripslashes($organization['organization']) . "\"";
			}
		?> />
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider"><img src="../../images/numbering/2.gif" alt="2." width="22" height="22" /> Assign Administrator</div>
<div class="stepContent">
<blockquote>
<p>Assign the organization an administrator. Begin by typing the desired user's first name, and a list of suggested users will appear under the text field. Click on the user's name from the list to apply. If the user does not appear in the list, he or she may not be in the system, and may need to be <a href="../users/manage_user.php" onclick="GP_popupConfirmMsg('You are about to leave this page, click \&quot;OK\&quot; to continue.');return document.MM_returnValue">added to the system</a>.</p>
<p>The user you assign will be automatically give the &quot;Organization Administrator&quot; role.</p>
	<blockquote>
      <div id="adminSuggest">
        <input name="admin" id="admin" type="text" size="50" autocomplete="off" tabindex="2" class="validate[required]"<?php
			if (isset ($id)) {
				echo " value=\"" . $organization['admin'] . "\"";
			}
		?> />
        <div>
        <div id="suggestions" spry:region="data">
        <span spry:repeat="data" spry:hover="hover" spry:suggest="{name}">
            <div>{name}</div>
        </span>
        </div>
        </div>
      </div>
        <script type="text/javascript">
           var dataSuggestions = new Spry.Widget.AutoSuggest("adminSuggest", "suggestions", "data", "name");
        </script>
    </blockquote>
</blockquote>
</div>
<div class="catDivider"><img src="../../images/numbering/3.gif" alt="3." width="22" height="22" /> Submit</div>
<div class="stepContent">
        <blockquote>
          <p>
            <label>
            <?php submit("submit", "Submit"); ?>
            </label>
            <label>
            <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            </label>
            <label>
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
            </label>
		  </p>
	  <?php formErrors(); ?>
      </blockquote>
      </div>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
