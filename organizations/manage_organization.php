<?php require_once('../system/connections/connDBA.php'); ?>
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
	if (isset ($_POST['submit']) && !empty ($_POST['name']) && !empty ($_POST['toImport'])) {
	//If the organization is being updated
		if (isset($id)) {
			$organization = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$admin = $_POST['toImport'];
			
			$organizationNameCheckGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'", $connDBA);
		
			if ($organizationNameCheck = mysql_fetch_array($organizationNameCheckGrabber)) {
				if (isset($_GET['id'])) {
					$organizationID = $_GET['id'];
					$currentOrganizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$organizationID}'", $connDBA);
					$currentOrganization = mysql_fetch_array($currentOrganizationGrabber);
					
					if (strtolower($currentOrganization['organization']) != strtolower($organization)) {
						header ("Location: manage_organization.php?id=" . $id . "&error=identical");
						exit;
					}
				} else {
					header ("Location: manage_organization.php?error=identical");
					exit;
				}
			}
			
			$oldDataGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
			$oldData = mysql_fetch_array($oldDataGrabber);
			$oldName = $oldData['organization'];
			$oldAdmin = explode(",", $oldData['admin']);
			$newAdmin = explode(",", $admin);
			
			mysql_query("UPDATE `organizations` SET `organization` =  '{$organization}', `admin` = '{$admin}' WHERE `id` = '{$id}'", $connDBA);
			
			foreach ($oldAdmin as $oldAdmin) {
				if (!in_array($oldAdmin, $newAdmin)) {
					mysql_query("UPDATE `users` SET `role` = 'Student' WHERE `id` = '{$oldAdmin}'", $connDBA);
				}
			}
			
			foreach ($newAdmin as $userID) {
				mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$id}' WHERE `id` = '{$userID}'", $connDBA);
			}
			
			header ("Location: index.php?message=organizationEdited");
			exit;
	//If the organization is being added
		} else {
			$sysID = "org_" . randomValue(11, 'alphanum');
			$organization = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['name']));
			$admin = $_POST['toImport'];
			
			$organizationNameCheckGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}'", $connDBA);
		
			if ($organizationNameCheck = mysql_fetch_array($organizationNameCheckGrabber)) {
				if (isset($_GET['id'])) {
					$organizationID = $_GET['id'];
					$currentOrganizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$organizationID}'", $connDBA);
					$currentOrganization = mysql_fetch_array($currentOrganizationGrabber);
					
					if (strtolower($currentOrganization['organization']) != strtolower($organization)) {
						header ("Location: manage_organization.php?id=" . $id . "&error=identical");
						exit;
					}
				} else {
					header ("Location: manage_organization.php?error=identical");
					exit;
				}
			}
			
			mysql_query("INSERT INTO organizations (`id`, `sysID`, `organization`, `organizationID`, `admin`, `type`, `webSite`, `phone`, `mailingAddress1`, `mailingAddress2`, `mailingCity`, `mailingState`, `mailingZIP`, `billingAddress1`, `billingAddress2`, `billingCity`, `billingState`, `billingZIP`, `billingPhone`, `billingFax`, `billingEmail`, `contractStart`, `contractEnd`, `contractAgreement`, `active`, `timeZone`) VALUES (NULL, '{$sysID}', '{$organization}', '', '{$admin}', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', '')", $connDBA);
						
			$organizationIDGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$organization}' LIMIT 1", $connDBA);
			$organizationIDArray = mysql_fetch_array($organizationIDGrabber);
			$organizationID = $organizationIDArray['id'];
			$newAdmin = explode(",", $admin);
			
			foreach ($newAdmin as $userID) {				
				mysql_query("UPDATE `users` SET `role` = 'Organization Administrator', `organization` = '{$organizationID}' WHERE `id` = '{$userID}'", $connDBA);
			}
			
			header ("Location: index.php?message=organizationCreated");
			exit;
		}
	}
?>
<?php
	if (isset($_GET['checkName'])) {
		$inputNameSpaces = $_GET['checkName'];
		$inputNameNoSpaces = str_replace(" ", "", $_GET['checkName']);
		$checkName = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$inputNameSpaces}'", $connDBA);
		
		if($name = mysql_fetch_array($checkName)) {	
			if (isset($_GET['id'])) {
				$organizationID = $_GET['id'];
				$currentOrganizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$organizationID}'", $connDBA);
				$currentOrganization = mysql_fetch_array($currentOrganizationGrabber);
				
				if (strtolower($currentOrganization['organization']) != strtolower($inputNameSpaces)) {
					echo "<div class=\"error\" id=\"errorWindow\">An organization with this name already exists</div>";
				} else {
					echo "<p>&nbsp;</p>";
				}
			} else {
				echo "<div class=\"error\" id=\"errorWindow\">An organization with this name already exists</div>";
			}
		} else {
			echo "<p>&nbsp;</p>";
		}
		
		echo "<script type=\"text/javascript\">validateName()</script>";
		die();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	if (isset($id)) {
		$title = "Edit the " . $organization['organization'] . " Organization";
	} else {
		$title = "Create New Organization";
	}
	
	title($title);
?>
<?php headers(); ?>
<?php validate(); ?>
<?php liveError(); ?>
<script src="../../javascripts/common/optionTransfer.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>

<body<?php bodyClass(); ?> onload="opt.init(document.forms[0])">
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2><?php echo $title; ?></h2>
<p>Create a new organization by filling in the information below. The organization's complete details and payment method will be setup when the organization administrator first logs in.</p>
<?php errorWindow("database", "An organization with this name already exists", "error", "identical", "true"); ?>
<form name="manageOrganization" method="post" action="manage_organization.php<?php if (isset($id)) {echo"?id=" . $id;} ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<div class="catDivider one">Organization Name</div>
<div class="stepContent">
  <blockquote>
    <p>
      Create the Organization name<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <input name="name" type="text" id="name" size="50" autocomplete="off" class="validate[required]" onblur="checkName(this.name, 'manage_organization'<?php if (isset ($id)) {echo ", 'id=" . $id . "'";}?>)"<?php
			if (isset ($id)) {
				echo " value=\"" . stripslashes($organization['organization']) . "\"";
			}
		?> />
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider two">Assign Administrator</div>
<div class="stepContent">
<blockquote>
<p>Assign the organization an administrator<span class="require">*</span>:
	<blockquote>
        <div class="layoutControl">
          <div class="halfLeft">
          <h3>Potential users:</h3>
          <div class="collapseElement"><input type="text" name="placeHolder" id="placeHolder"></div>
          <div align="center">
          <select name="notToList" id="notToList" class="multiple" multiple="multiple" ondblclick="opt.transferRight()">
          <?php
		  //Display all users
			  $potentialUserGrabber = mysql_query("SELECT * FROM `users` WHERE `role` != 'Site Administrator' OR 'Site Manager' ORDER BY `firstName` ASC", $connDBA);
		  
			  if (!isset ($id)) {
				  while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
					  if ($potentialUser['role'] != "Organization Administrator" && $potentialUser['id'] != "1") {
						  echo "<option value=\"" . $potentialUser['id'] . "\">" . $potentialUser['firstName'] . " " . $potentialUser['lastName'] . "</option>";
					  }
				  }
			  } else {
				  $currentAdministrator = explode(",", $organization['admin']);
				  
				  while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
					  if (!in_array($potentialUser['id'], $currentAdministrator)) {
						  if ($potentialUser['role'] != "Organization Administrator" && $potentialUser['id'] != "1") {
							  echo "<option value=\"" . $potentialUser['id'] . "\">" . $potentialUser['firstName'] . " " . $potentialUser['lastName'] . "</option>";
						  }
					  }
				  }
			  }
		  ?>
          </select><br /><br />
          <input type="button" name="right" value="&gt;&gt;" onclick="opt.transferRight()">
          </div>
          </div>
          <div class="halfRight">
          <h3>Selected users:</h3>
          <div class="collapseElement"><input type="text" name="toImport" id="toImport" class="validate[required]" readonly="readonly"<?php
		  if (isset ($id)) {
			  echo " value=\"" . stripslashes($organization['admin']) . "\"";
		  }
		  ?> ></div>
          <div align="center">
          <select name="toList" id="toList" class="multiple" multiple="multiple" ondblclick="opt.transferLeft()">
          <?php
		  //Display all administrators
			  $potentialUserGrabber = mysql_query("SELECT * FROM `users` WHERE `role` != 'Site Administrator' OR 'Site Manager' ORDER BY `firstName` ASC", $connDBA);
		  
			  if (isset ($id)) {
				  $currentAdministrator = explode(",", $organization['admin']);
				  
				  while ($potentialUser = mysql_fetch_array($potentialUserGrabber)) {
					  if (in_array($potentialUser['id'], $currentAdministrator)) {
					  	echo "<option value=\"" . $potentialUser['id'] . "\">" . $potentialUser['firstName'] . " " . $potentialUser['lastName'] . "</option>";
					  }
				  }
			  }
		  ?>
          </select><br /><br /><input type="button" name="right" value="&lt;&lt;" onclick="opt.transferLeft()"></div></div></div> 
    </blockquote>
</blockquote>
</div>
<div class="catDivider three">Submit</div>
<div class="stepContent">
        <blockquote>
          <p>
            <?php submit("submit", "Submit"); ?>
            <input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Cancel" />
		  </p>
	  <?php formErrors(); ?>
      </blockquote>
      </div>
</form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
