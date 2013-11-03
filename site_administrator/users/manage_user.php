<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Decide whether a user is being edited or created
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$userGrabber = mysql_query("SELECT * FROM users WHERE id = '{$id}'", $connDBA);
		if ($userCheck = mysql_fetch_array($userGrabber)) {
			$user = $userCheck;
		
		//If the user is updating themself	
			if ($user['userName'] == $_SESSION['MM_Username']) {
				header("Location: index.php");
				exit;
			}
		} else {
			header("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['role']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['userName']) && !empty($_POST['primaryEmail']) && !empty($_POST['phoneWork']) && !empty($_POST['phoneHome']) && !empty($_POST['workLocation']) && !empty($_POST['jobTitle']) && !empty($_POST['staffID']) && !empty($_POST['department']) && !empty($_POST['departmentID'])) {
		$sysID = "user_" . randomValue(10, 'alphanum');
		$role = $_POST['role'];
		$firstName = $_POST['firstName'];
		$lastName = $_POST['lastName'];
		$userName = $_POST['userName'];
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
		$changePassword = $_POST['changePassword'];
		$primaryEmail = $_POST['primaryEmail'];
		$secondaryEmail = $_POST['secondaryEmail'];
		$tertiaryEmail = $_POST['tertiaryEmail'];
		$phoneWork = $_POST['phoneWork'];
		$phoneHome = $_POST['phoneHome'];
		$phoneMobile = $_POST['phoneMobile'];
		$phoneFax = $_POST['phoneFax'];
		$workLocation = $_POST['workLocation'];
		$jobTitle = $_POST['jobTitle'];
		$staffID = $_POST['staffID'];
		$department = $_POST['department'];
		$departmentID = $_POST['departmentID'];
		
		if (isset ($id)) {	
			mysql_query("UPDATE `users` SET `staffID` = '{$staffID}', `firstName` = '{$firstName}', `lastName` = '{$lastName}', `userName` = '{$userName}', `passWord` = '{$passWord}', `changePassword` = '{$changePassword}', `emailAddress1` = '{$primaryEmail}', `emailAddress2` = '{$secondaryEmail}', `emailAddress3` = '{$tertiaryEmail}', `phoneWork` = '{$phoneWork}', `phoneHome` = '{$phoneHome}', `phoneMobile` = '{$phoneMobile}', `phoneFax` = '{$phoneFax}', `workLocation` = '{$workLocation}', `jobTitle` = '{$jobTitle}', `department` = '{$department}', `departmentID` = '{$departmentID}', `role` = '{$role}' WHERE `id` = '{$id}'", $connDBA);
			
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
							NULL, '{$sysID}', '{$staffID}', '{$firstName}', '{$lastName}', '{$userName}', '{$passWord}', '{$changePassword}', '{$primaryEmail}', '{$secondaryEmail}', '{$tertiaryEmail}', '{$phoneWork}', '{$phoneHome}', '{$phoneMobile}', '{$phoneFax}', '{$workLocation}', '{$jobTitle}', '{$department}', '{$departmentID}', '{$role}', '1'
						)", $connDBA);
			
			if ($role !== "Site Administrator" && $role !== "Site Manager") {
				$idGrabber = mysql_query("SELECT * FROM `users` WHERE `firstName` = '{$firstName}' LIMIT 1", $connDBA);
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
//Grab user information if they are being edited
	if (isset ($id)) {
		$name = $user['firstName'] . " " . $user['lastName'];
	} else {
		$name = "Create New User";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Modify " . $name) ?>
<?php headers(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
<h2><?php echo $name ?></h2>
<p><?php if (isset($id)) {echo "Modify " . $name . "'s information";} else {echo "Create a new user";} ?> by <?php if (isset($id)) {echo "modifying";} else {echo "filling in";} ?> the information below.</p>
<p>&nbsp;</p>
<form name="users" method="post" action="manage_user.php<?php if (isset($id)) {echo"?id=" . $id;} ?>" id="validate" onsubmit="return errorsOnSubmit(this);">
<?php
//Echo the user id, if the user is being edited
	if (isset($id)) {
		echo"<input type=\"hidden\" name=\"id\" id=\"id\" value =\"" . $id . "\" />";
	}
?>
<div class="catDivider"><img src="../../images/numbering/1.gif" alt="1." width="22" height="22" /> Select a Role</div>
<div class="stepContent">
<blockquote>
  <p>Select a role for this user<span class="require">*</span>: </p>
  <blockquote>
    <p>
      <label>
      <select name="role" id="role" class="validate[required]">
        <option value=""<?php if (!isset($id)) {echo " selected=\"selected\"";} ?>>- Select Role -</option>
        <option value="Student"<?php if (isset($id)) {if ($user['role'] == "Student") {echo " selected=\"selected\"";}} ?>>Student</option>
        <option value="Instructorial Assisstant"<?php if (isset($id)) {if ($user['role'] == "Instructorial Assisstant") {echo " selected=\"selected\"";}} ?>>Instructorial Assisstant</option>
        <option value="Instructor"<?php if (isset($id)) {if ($user['role'] == "Instructor") {echo " selected=\"selected\"";}} ?>>Instructor</option>
        <option value="Administrative Assistant"<?php if (isset($id)) {if ($user['role'] == "Administrative Assistant") {echo " selected=\"selected\"";}} ?>>Administrative Assistant</option>
        <option value="Organization Administrator"<?php if (isset($id)) {if ($user['role'] == "Organization Administrator") {echo " selected=\"selected\"";}} ?>>Organization Administrator</option>
        <option value="Site Manager"<?php if (isset($id)) {if ($user['role'] == "Site Manager") {echo " selected=\"selected\"";}} ?>>Site Manager</option>
        <option value="Site Administrator"<?php if (isset($id)) {if ($user['role'] == "Site Administrator") {echo " selected=\"selected\"";}} ?>>Site Administrator</option>
      </select>
      </label>
    </p>
    </blockquote>
  </blockquote>
<p>&nbsp;</p>
</div>
<div class="catDivider"><img src="../../images/numbering/2.gif" alt="2." width="22" height="22" /> User Information</div>
<div class="stepContent">
<blockquote>
  <p>First Name<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <label>
      <input name="firstName" type="text" id="firstName" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['firstName'] . "\"";} ?> />
      </label>
    </p>
  </blockquote>
  <p>Last Name<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <label>
      <input name="lastName" type="text" id="lastName" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['lastName'] . "\"";} ?> />
      </label>
    </p>
  </blockquote>
  <p>User Name<span class="require">*</span>:</p>
  <blockquote>
    <p>
      <label>
      <input name="userName" type="text" id="userName" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['userName'] . "\"";} ?> />
      </label>
    </p>
  </blockquote>
  <p>Password<?php if (!isset($id)) {echo "<span class=\"require\">*</span>";} ?>:</p>
  <blockquote>
    <p>
      <label>
      <input name="passWord" type="password" id="passWord" size="50" autocomplete="off"<?php if (!isset($id)) {echo " class=\"validate[required]\"";} ?> />
      </label>
      <label>
        <input type="checkbox" name="changePassword" id="changePassword"<?php if (isset($id)) { if ($user['changePassword'] == "on") {echo " checked=\"checked\"";}} ?> />
        Force Password Change
      </label>
    </p>
  </blockquote>
</blockquote>
<p>&nbsp;</p>
</div>
<div class="catDivider"><img src="../../images/numbering/3.gif" alt="3." width="22" height="22" /> Contact Information</div>
<div class="stepContent">
  <blockquote>
    <p>Primary Email Address<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="primaryEmail" type="text" id="primaryEmail" size="50" autocomplete="off" class="validate[required,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress1'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Secondary Email Address:</p>
    <blockquote>
      <p>
        <label>
        <input name="secondaryEmail" type="text" id="secondaryEmail" size="50" autocomplete="off" class="validate[optional,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress2'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Tertiary Email Address:</p>
    <blockquote>
      <p>
        <label>
        <input name="tertiaryEmail" type="text" id="tertiaryEmail" size="50" autocomplete="off" class="validate[optional,custom[email]]"<?php if (isset($id)) {echo " value=\"" . $user['emailAddress3'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Work Phone<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="phoneWork" type="text" id="phoneWork" size="50" autocomplete="off" class="validate[required,custom[telephone]]"<?php if (isset($id)) {echo " value=\"" . $user['phoneWork'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Home Phone<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="phoneHome" type="text" id="phoneHome" size="50" autocomplete="off" class="validate[required,custom[telephone]]"<?php if (isset($id)) {echo " value=\"" . $user['phoneHome'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Mobile Phone:</p>
    <blockquote>
      <p>
        <label>
        <input name="phoneMobile" type="text" id="phoneMobile" size="50" autocomplete="off" class="validate[optional,custom[telephone]]"<?php if (isset($id)) {echo " value=\"" . $user['phoneMobile'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Fax:</p>
    <blockquote>
      <p>
        <label>
        <input name="phoneFax" type="text" id="phoneFax" size="50" autocomplete="off" class="validate[optional,custom[telephone]]"<?php if (isset($id)) {echo " value=\"" . $user['phoneFax'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
  </blockquote>
  <p>&nbsp;</p>
</div>
<div class="catDivider"><img src="../../images/numbering/4.gif" alt="4." width="22" height="22" /> Workplace Information</div>
<div class="stepContent">
  <blockquote>
    <p>Work Location<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="workLocation" type="text" id="workLocation" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['workLocation'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Job Title<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="jobTitle" type="text" id="jobTitle" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['jobTitle'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Staff ID<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="staffID" type="text" id="staffID" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['staffID'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Department<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="department" type="text" id="department" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['department'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>Department ID<span class="require">*</span>:</p>
    <blockquote>
      <p>
        <label>
        <input name="departmentID" type="text" id="departmentID" size="50" autocomplete="off" class="validate[required]"<?php if (isset($id)) {echo " value=\"" . $user['departmentID'] . "\"";} ?> />
        </label>
      </p>
    </blockquote>
    <p>&nbsp;</p>
  </blockquote>
</div>
<div class="catDivider"><img src="../../images/numbering/5.gif" alt="5." width="22" height="22" /> Submit</div>
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