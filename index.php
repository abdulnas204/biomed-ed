<?php require_once('Connections/connDBA.php'); ?>
<?php login(); ?>
<?php
//Get page name by URL variable
	$getPageID = $_GET['page'];

//If no page URL variable is defined, then redirect to page=1
	if (!isset ($_GET['page'])) {
		header("Location: index.php?page=1");
		exit;
	}
//Hide the admin menu if an incorrect page displays
	$pageCheckGrabber = mysql_query("SELECT * FROM pages WHERE position = {$getPageID}", $connDBA);
	$pageCheckArray = mysql_fetch_array($pageCheckGrabber);
	$pageCheckResult = $pageCheckArray['position'];
         if (isset ($pageCheckResult)) {
             $pageCheck = 1;
         } else {
		 	$pageCheck = 0;
		 }

//Grab the page data		 
	$pageInfo = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE position = {$getPageID}", $connDBA));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	if ($pageCheck == 0 && $getPageID == 1) {
		$title = "Setup Required";
	} elseif (isset($getPageID)) {
		if (empty($pageInfo['content'])) {
			$title = "Page Not Found";
		} else {
			$title = $pageInfo['title'];
		}
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php meta(); ?>
<script src="javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body>
<?php topPage("includes/top_menu.php"); ?>
<div class="layoutControl">
<div class="contentLeft">
<?php
//Display content based on login status
	if (isset($_SESSION['MM_Username']) && isset($pageCheck) && $pageCheck !== 0) {
	//The admin toolbox div
		echo "<form name=\"pages\" action=\"site_administrator/cms/index.php\"><div style=\"width:100%\"><div align=\"center\"><div align=\"center\" class=\"announcement\"><div align=\"center\"><a href=\"site_administrator/cms/manage_page.php?id=" . $pageInfo['id'] . "\">Edit This Page</a>  | Visible: <input type=\"hidden\" name =\"redirect\" value=\"home\" /><input type=\"hidden\" name =\"action\" value=\"modifySettings\" /><input type=\"hidden\" name =\"id\" value=\"" .  $pageInfo['id'] . "\" /><input type=\"hidden\" name =\"currentPosition\" value=\"" .  $pageInfo['position'] . "\" /><select name=\"visible\" onchange=\"this.form.submit();\"><option value=\"1\""; 
		if ($pageInfo['visible'] == 1) {echo " selected=\"selected\"";} 
		echo ">Yes</option><option value=\"0\""; 
		if ($pageInfo['visible'] == 0) {echo " selected=\"selected\"";} 
		if ($getPageID == 1) {echo " onclick=\"alert ('The page you are currently hiding is the site entry point. Hiding this page will not lock visitors out of the page, but will only hide it from the menu.');\"";}
		echo ">No</option></select> | <a href=\"site_administrator/index.php\">Back to Staff Home Page</a> | <a href=\"site_administrator/cms/index.php\">Back to Pages</a> | <a href=\"logout.php\">Logout</a></div></div></div><br /></div></form>";
	}
	
	if ($pageCheck == 0 && $getPageID == 1) {
		echo "<h2>Setup Required</h2>";
		if (!isset($_SESSION['MM_Username'])) {
			alert("Please <a href=\"login.php\">login</a> to create your first page.");
		} else {
			alert("Please <a href=\"site_administrator/cms/manage_page.php\">create your first page</a>.");
		}
	} elseif (isset($getPageID)) {
		if (empty($pageInfo['content'])) {
			errorMessage("The page you are looking for was not found on our system");
		} else {
			echo "<h2>" . $pageInfo['title'] . "</h2>" . $pageInfo['content'];
		}
	}
?>
</div>
<div class="dataRight">
<br />
<br />
<br />
<?php
	if (!isset ($_SESSION['MM_Username'])) {
?>
    <div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Login</h2>
        </div>
      </div>
      <div class="content">
        <form id="login" name="login" method="post" action="index.php">
        <div align="center">
        <div style="width:75%;">
          <p>User name: <input type="text" name="username" id="username" autocomplete="off" />
          <br />
			 Password: <input type="password" name="password" id="password" autocomplete="off" />
          </p>
          <p>
            <input type="submit" name="submit" id="submit" value="Login" />
          </p>
        </div>
        </div>
        </form>
      </div>
    </div>
    <div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Register</h2>
        </div>
      </div>
      <div class="content">
        <p>Register now gain full access to all of our courses!</p>
        <div>
          <div align="center">
            <input type="button" name="register" id="register" value="Register" onclick="MM_goToURL('parent','register.php');return document.MM_returnValue" />
          </div>
        </div>
        <p>&nbsp; </p>
      </div>
    </div>
<?php 
	}
?>
    </div>
</div>
<?php footer("includes/bottom_menu.php"); ?>
</body>
</html>