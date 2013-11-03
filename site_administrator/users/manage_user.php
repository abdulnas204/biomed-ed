<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Decide whether a user is being edited or created
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$userGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
		if ($userCheck = mysql_fetch_array($userGrabber)) {
			$user = $userCheck;
		} else {
			header("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['role']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['userName']) && !empty($_POST['primaryEmail'])) {
		$sysID = "user_" . randomValue(10, 'alphanum');
		$role = $_POST['role'];
		$firstName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['firstName']));
		$lastName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['lastName']));
		$userName = mysql_real_escape_string($_POST['userName']);
		
		if (isset ($_POST['id']) && $_POST['passWord'] == "") {
			$id = $_POST['id'];
			$checkPassWordGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
			$checkPassWord = mysql_fetch_array($checkPassWordGrabber);
			$passWord = $checkPassWord['passWord'];
		} elseif (isset ($_POST['id']) && $_POST['passWord'] !== "") {
			$passWord = $_POST['passWord'];	
		} elseif (!isset ($_POST['id']) && $_POST['passWord'] == "") {
			header ("Location: manage_user.php");
			exit;
		} elseif (!isset ($_POST['id']) && $_POST['passWord'] !== "") {
			$passWord = $_POST['passWord'];
		}
		
		$userNameCheckGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}'", $connDBA);
		
		if ($userNameCheck = mysql_fetch_array($userNameCheckGrabber)) {
			if (isset($_GET['id'])) {
				$userID = $_GET['id'];
				$currentUserGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$userID}'", $connDBA);
				$currentUser = mysql_fetch_array($currentUserGrabber);
				
				if (strtolower($currentUser['userName']) != strtolower($userName)) {
					header ("Location: manage_user.php?id=" . $id . "&error=identical");
					exit;
				}
			} else {
				header ("Location: manage_user.php?error=identical");
				exit;
			}
		}
	
		$changePassword = $_POST['changePassword'];
		$primaryEmail = $_POST['primaryEmail'];
		
		if (isset ($id)) {
			$adminCheck = mysql_query("SELECT * FROM `users` WHERE `role` = 'Site Administrator'", $connDBA);
			if (mysql_num_rows($adminCheck) == "1" && $role != "Site Administrator") {
				header("Location: index.php?message=noAdmin");
				exit;
			}
			
			mysql_query("UPDATE `users` SET `firstName` = '{$firstName}', `lastName` = '{$lastName}', `userName` = '{$userName}', `passWord` = '{$passWord}', `changePassword` = '{$changePassword}', `emailAddress1` = '{$primaryEmail}', `role` = '{$role}' WHERE `id` = '{$id}'", $connDBA);
			
			if ($user['userName'] == $_SESSION['MM_Username']) {
				$oldGroup = $_SESSION['MM_UserGroup'];
				
				if ($oldGroup != $role) {
					header("Location: " . $root . "logout.php?action=relogin");
					exit;
				}
			}
			
			if ($role !== "Site Administrator" && $role !== "Site Manager") {
				if ($user['organization'] == "1" || $user ['organizationID'] == "1") {					
					header ("Location: index.php?message=organization&id=" . $id);
					exit;
				} else {
					header ("Location: index.php?message=userEdited");
					exit;
				}
			} else {
				header ("Location: index.php?message=userEdited");
				exit;
			}
		} else {
			mysql_query("INSERT INTO `users`(
						id, sysID, staffID, firstName, lastName, userName, passWord, changePassword, emailAddress1, emailAddress2, emailAddress3, phoneWork, phoneHome, phoneMobile, phoneFax, workLocation, jobTitle, department, departmentID, role, organization
						) VALUES (
							NULL, '{$sysID}', '', '{$firstName}', '{$lastName}', '{$userName}', '{$passWord}', '{$changePassword}', '{$primaryEmail}', '', '', '', '', '', '', '', '', '', '', '{$role}', '1'
						)", $connDBA);
			
			if ($role !== "Site Administrator" && $role !== "Site Manager") {
				$idGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
				$id = mysql_fetch_array($idGrabber);
				
				header ("Location: index.php?message=organization&id=" . $id['id']);
				exit;
			} else {
				header ("Location: index.php?message=userCreated");
				exit;
			}
		}
	}
?>
<?php
	if (isset($_GET['checkName'])) {
		$inputNameSpaces = $_GET['checkName'];
		$inputNameNoSpaces = str_replace(" ", "", $_GET['checkName']);
		$checkName = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$inputNameSpaces}'", $connDBA);
		
		if($name = mysql_fetch_array($checkName)) {	
			if (isset($_GET['id'])) {
				$userID = $_GET['id'];
				$currentUserGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$userID}'", $connDBA);
				$currentUser = mysql_fetch_array($currentUserGrabber);
				
				if (strtolower($currentUser['userName']) != strtolower(mysql_real_escape_string($inputNameSpaces))) {
					echo "<div class=\"error\" id=\"errorWindow\">This user name is already taken</div>";
				} else {
					echo "<p>&nbsp;</p>";
				}
			} else {
				echo "<div class=\"error\" id=\"errorWindow\">This user name is already taken</div>";
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
//Grab user information if they are being edited
	if (isset ($id)) {
		$title = "Modify " . $user['firstName'] . " " . $user['lastName'];
	} else {
		$title = "Create New User";
	}
	
	title($title);
?>
<?php headers(); ?>
<?php validate(); ?>
<?php liveError(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
<h2><?php echo $title ?></h2>
<p><?php if (isset($id)) {echo "Modify " . $user['firstName'] . " " . $user['lastName'] . "'s information";} else {echo "Create a new user";} ?> by <?php if (isset($id)) {echo "modifying";} else {echo "filling in";} ?> the information below.</p>
<?php errorWindow("database", "This user name is already taken", "error", "identical", "true"); ?>
<form name="users" method="post" action="manage_user.php<?php if (isset($id)) {echo"?id=" . $id;} ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<?php
//Echo the user id, if the user is being edited
	if (isset($id)) {
		echo"<input type=\"hidden\" name=\"id\" id=\"id\" value =\"" . $id . "\" />";
	}
?>
<div class="catDivider one">Select a Role</div>
<div class="stepContent">
<blockquote>
  <p>Select a role for this user<span class="require">*</span>: </p>
  <blockquote>
    <?php
		if (isset ($id) && $user['role'] == "Organization Administrator" && $user['organization'] != "1") {
			echo "<input type=\"hidden\" name=\"role\" id=\"role\" value=\"Organization Administrator\"><p>This Organization Administrator is currently assigned to an organization, <br />please <a href=\"../organizations/manage_organization.php?id=" . $user['organization'] . "\">un-assign " . $user['firstName'] . " from the organization</a> to change " . $user['firstName'] . "'s role.</p>";
		} else {
			echo "<p><select name=\"role\" id=\"role\" class=\"validate[required]\">";
			echo "<option value=\"\"";
				if (!isset($id)) {echo " selected=\"selected\"";}
			echo ">- Select Role -</option>";
			
        	echo "<option value=\"Student\"";
				if (isset($id)) {if ($user['role'] == "Student") {echo " selected=\"selected\"";}} 
			echo ">Student</option>";
			
			echo "<option value=\"Instructorial Assisstant\"";
				if (isset($id)) {if ($user['role'] == "Instructorial Assisstant") {echo " selected=\"selected\"";}}
			echo ">Instructorial Assisstant</option>";
			
			echo "<option value=\"Instructor\"";
				if (isset($id)) {if ($user['role'] == "Instructor") {echo " selected=\"selected\"";}}
			echo ">Instructor</option>";
			
			echo "<option value=\"Administrative Assistant\"";
				if (isset($id)) {if ($user['role'] == "Administrative Assistant") {echo " selected=\"selected\"";}}
			echo ">Administrative Assistant</option>";
			
			echo "<option value=\"Organization Administrator\"";
				if (isset($id)) {if ($user['role'] == "Organization Administrator") {echo " selected=\"selected\"";}}
			echo ">Organization Administrator</option>";
			
			echo "<option value=\"Site Manager\"";
				if (isset($id)) {if ($user['role'] == "Site Manager") {echo " selected=\"selected\"";}}
			echo ">Site Manager</option>";
			
			echo "<option value=\"Site Administrator\"";
				if (isset($id)) {if ($user['role'] == "Site Administrator") {echo " selected=\"selected\"";}}
			echo ">Site Administrator</option>";
			echo "</select></p>";
		}
	?>
    </blockquote>
</blockquote>
</div>
<div class="catDivider two">User Information</div>
<div class="stepContent">
<blockquote>
  <p>First Name<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <input name="firstName" type="text" id="firstName" size="50" autocomplete="off" class="validate[required,custom[noSpecialCharacters]]"<?php if (isset($id)) {echo " value=\"" . $user['firstName'] . "\"";} ?> />
    </p>
  </blockquote>
  <p>Last Name<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <input name="lastName" type="text" id="lastName" size="50" autocomplete="off" class="validate[required,custom[noSpecialCharacters]]"<?php if (isset($id)) {echo " value=\"" . $user['lastName'] . "\"";} ?> />
    </p>
  </blockquote>
  <p>User Name<?php if (isset($user)) {if ($user['userName'] != $_SESSION['MM_Username']) {echo "<span class=\"require\">*</span>";}} else {echo "<span class=\"require\">*</span>";} ?>:</p>
  <blockquote>
    <p>
      <input name="userName" type="text" id="userName" size="50" autocomplete="off" class="validate[required,length[6,30],custom[noSpecialCharactersSpaces]]<?php if (isset($user)) {if ($user['userName'] == $_SESSION['MM_Username']) {echo " disabled";}} ?>" onblur="checkName(this.name, 'manage_user'<?php if (isset ($user)) {echo ", 'id=" . $user['id'] . "'";}?>)"<?php if (isset($user)) {if ($user['userName'] == $_SESSION['MM_Username']) {echo " readonly=\"readonly\"";}} if (isset($id)) {echo " value=\"" . $user['userName'] . "\"";} ?> />
    </p>
  </blockquote>
  <p>Password<?php if (!isset($id)) {echo "<span class=\"require\">*</span>";} ?>:</p>
  <blockquote>
    <p>
      <input name="passWord" type="password" id="passWord" size="50" autocomplete="off"<?php if (!isset($id)) {echo " class=\"validate[required,length[6,30]]\"";} ?> />
      <?php
		  if (isset($user)) {
			  if ($user['userName'] != $_SESSION['MM_Username']) { 
				echo "<label>
				  <input type=\"checkbox\" name=\"changePassword\" id=\"changePassword\"";
				  if (isset($id)) {
					  if ($user['changePassword'] == "on") {
						  echo " checked=\"checked\"";
					  }
				  }
				  echo " /> Force Password Change
				</label>";
			  }
		  } else {
			  echo "<label>
				<input type=\"checkbox\" name=\"changePassword\" id=\"changePassword\"";
				if (isset($id)) {
					if ($user['changePassword'] == "on") {
						echo " checked=\"checked\"";
					}
				}
				echo " /> Force Password Change
			  </label>";
		  }
      ?>
    </p>
  </blockquote>
</blockquote>
</div>
<div class="catDivider three">Contact Information</div>
<div class="stepContent">
  <blockquote>
    <p>Primary Email Address<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <input name="primaryEmail" type="text" id="primaryEmail" size="50" autocomplete="off" class="validate[required,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress1'] . "\"";} ?> />
      </p>
    </blockquote>
    <p>Secondary Email Address:</p>
    <blockquote>
      <p>
        <input name="secondaryEmail" type="text" id="secondaryEmail" size="50" autocomplete="off" class="validate[optional,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress2'] . "\"";} ?> />
      </p>
    </blockquote>
    <p>Tertiary Email Address:</p>
    <blockquote>
      <p>
        <input name="tertiaryEmail" type="text" id="tertiaryEmail" size="50" autocomplete="off" class="validate[optional,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress3'] . "\"";} ?> />
      </p>
    </blockquote>
  </blockquote>
</div>
<div class="catDivider four">Submit</div>
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