<?php 
//Header functions
	require_once('../../../Connections/connDBA.php');
	monitor("Welcome");

//Process the form
	if (isset ($_POST['submit'])) {
		$_SESSION['step'] = "lessonSettings";
		redirect("lesson_settings.php");
	}
//Title
	title("Welcome to the Module Setup Wizard", "This wizard will guide you through the process of setting up a module. Click &quot;Launch Wizard&quot; to begin.");

//Entry form
	form("startUp");
	echo "<div class=\"spacer\">";
	button("submit", "submit", "Launch Wizard", "submit");
	echo "</div>";
	closeForm();
	
//Include the footer
	footer();
?>