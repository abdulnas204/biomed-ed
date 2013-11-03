<?php 
//Header functions
	require_once('system/connections/connDBA.php');
	headers("Register", false, "validate");
	
//Process the form
	if (isset($_POST['submit']) && !empty($_POST['firstName']) && !empty($_POST['lastName']) && !empty($_POST['userName']) && !empty($_POST['primaryEmail'])) {
		$firstName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['firstName']));
		$lastName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['lastName']));
		$userName = mysql_real_escape_string(preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['userName']));
		$passWord = $_POST['passWord'];
		$primaryEmail = $_POST['primaryEmail'];
		$secondaryEmail = $_POST['secondaryEmail'];
		$tertiaryEmail = $_POST['tertiaryEmail'];
		
		query("INSERT INTO `users`(
			  id, locked, active, staffID, firstName, lastName, userName, passWord, changePassword, emailAddress1, emailAddress2, emailAddress3, phoneWork, phoneHome, phoneMobile, phoneFax, workLocation, jobTitle, department, departmentID, role, organization
			  ) VALUES (
				  NULL, '', '', '', '{$firstName}', '{$lastName}', '{$userName}', '{$passWord}', '', '{$primaryEmail}', '{$secondaryEmail}', '{$tertiaryEmail}', '', '', '', '', '', '', '', '', 'Student', '1'
			  )");
			  
		$_SESSION['MM_Username'] = $userName;
		$_SESSION['MM_UserGroup'] = "Student";
		
		redirect("portal/index.php");
	}

//Title
	title("Register", "Begin here to set up an account to enroll in our training program.");
	
//Registration form
	form("register");
	catDivider("User Information", "one", true);
    echo "<blockquote>";
	directions("First name", true);
	echo "<blockquote><p>";
    textField("firstName", "firstName", false, false, false, true);
	echo "</p></blockquote>";
	directions("Last name", true);
	echo "<blockquote><p>";
    textField("lastName", "lastName", false, false, false, true);
	echo "</p></blockquote>";
	directions("User name", true);
	echo "<blockquote><p>";
    textField("userName", "userName", false, false, false, true, ",length[6,30],custom[noSpecialCharactersSpaces]");
	echo "</p></blockquote>";
	directions("Password", true);
	echo "<blockquote><p>";
    textField("passWord", "passWord", false, false, true, true, ",length[6,30]");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Contact Information", "two");
	echo "<blockquote>";
	directions("Primary email address", true);
	echo "<blockquote><p>";
    textField("primaryEmail", "primaryEmail", false, false, false, true, ",custom[email]");
	echo "</p></blockquote>";
	directions("Secondary email address");
	echo "<blockquote><p>";
    textField("secondaryEmail", "secondaryEmail", false, false, false, false, ",custom[email]");
	echo "</p></blockquote>";
	directions("Tertiary email address");
	echo "<blockquote><p>";
    textField("tertiaryEmail", "tertiaryEmail", false, false, false, false, ",custom[email]");
	echo "</p></blockquote></blockquote>";
	
	catDivider("Submit", "three");
	echo "<blockquote><p>";
	button("submit", "submit", "Register", "submit");
	echo "</p></blockquote>";
	closeForm(true, true);
	
//Include the footer
	footer();
?>