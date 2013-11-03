<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Check to see if the item is being edited
	if (isset ($_GET['id'])) {
		$item = $_GET['id'];
		$itemGrabber = mysql_query("SELECT * FROM sidebar WHERE `id` = '{$item}'", $connDBA);
		if ($itemCheck = mysql_fetch_array($itemGrabber)) {
			$item = $itemCheck;
		} else {
			header ("Location: sidebar.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['type'])) {	
		if (!isset ($item)) {
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			$type = $_POST['type'];
			
			$positionGrabber = mysql_query ("SELECT * FROM sidebar ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newItemQuery = "INSERT INTO sidebar (
								`id`, `position`, `visible`, `type`, `title`, `content`
							) VALUES (
								NULL, '{$position}', 'on', '{$type}', '{$title}', '{$content}'
							)";
			
			mysql_query($newItemQuery, $connDBA);
			header ("Location: sidebar.php?added=item");
			exit;
		} else {
			$item = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			$type = $_POST['type'];
				
			$editItemQuery = "UPDATE sidebar SET type = '{$type}', title = '{$title}', content = '{$content}' WHERE `id` = '{$item}'";
			
			mysql_query($editItemQuery, $connDBA);
			header ("Location: sidebar.php?updated=item");
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($item)) {
		$title = "Edit the " . stripslashes(htmlentities($item['title'])) . " Box";
	} else {
		$title =  "Create a New Box";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCESimple(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/showHide.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
    <h2>
      <?php if (isset ($item)) {echo "Edit the \"" . $item['title'] . "\" Box";} else {echo "Create New Box";} ?>
    </h2>
<p>Use this page to <?php if (isset ($item)) {echo "edit the content of the \"<strong>" . stripslashes(htmlentities($item['title'])) . "</strong>\" box";} else {echo "create a new box";} ?>.</p>
    <p>&nbsp;</p>
    <form action="manage_sidebar.php<?php 
		if (isset ($item)) {
			echo "?id=" . $item['id'];
		}
	?>" method="post" name="manageItem" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Settings</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The text that will display in big letters on the top-left of each page <br />and at the top of the browser window')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($item)) {
					echo " value=\"" . stripslashes(htmlentities($item['title'])) . "\"";
				}
			?> />
          </p>
        </blockquote>
        <p>Type<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The type of content that will be displayed in the text box.<br />Different ones will be avaliable at different times, <br />depending on their current use.<br /><br /><strong>Custom Content</strong> - A box which can contain any desired content.<br /><strong>Login</strong> - A box with a pre-built form to log in a user.<br /><strong>Register</strong> - A box which will link a visitor to the site registration page.<br />')" onmouseout="UnTip()" /></p>
        <?php
		//Grab the sidebar items to ensure there aren't any unnecessary duplicates
			$sideBarGrabber = mysql_query("SELECT * FROM sidebar", $connDBA);
			$login = "";
			$register = "";
			
			while ($sideBarResults = mysql_fetch_array($sideBarGrabber)) {
				if ($sideBarResults['type'] == "Login")	{
					$login = "true";
				}
				
				if ($sideBarResults['type'] == "Register")	{
					$register = "true";
				}
			}
		?>
        <blockquote>
          <p>
            <select name="type" id="type" class="validate[required]" onchange="toggleTypeDiv(this.value);">
              <option value="Custom Content"<?php if (isset ($item) && $item['type'] == "Custom Content") {echo " selected=\"selected\"";} else {echo " selected=\"selected\"";} ?>>Custom Content</option>
              <?php
				  if (!isset($item)) {
					  if ($login == "") {
						  echo "<option value=\"Login\">Login</option>";
					  }
				  } else {
					  echo "<option value=\"Login\""; if ($item['type'] == "Login") {echo " selected=\"selected\"";} echo ">Login</option>";
				  }
			  ?>
              <?php
				  if (!isset($item)) {
					  if ($register == "") {
						  echo "<option value=\"Register\">Register</option>";
					  }
				  } else {
					  echo "<option value=\"Register\""; if ($item['type'] == "Register") {echo " selected=\"selected\"";} echo ">Register</option>";
				  }
			  ?>
            </select>
          </p>
        </blockquote>
      </blockquote>
      </div>
      <div class="catDivider two">Content</div>
      	<div class="stepContent">
        <div id="contentAdvanced" <?php if (isset ($item) && $item['type'] != "Login") {echo "class=\"contentShow\"";} else {echo "class=\"contentHide\"";} ?>>
        <blockquote>
        <p>Content: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the box')" onmouseout="UnTip()" /> </p>
        <blockquote>
        <p><textarea name="content" id="content1" cols="45" rows="5" style="width:450px;" /><?php 
				if (isset ($item)) {
					echo stripslashes($item['content']);
				}
			?></textarea></p>
        </blockquote>
      </blockquote>
      </div>
        <div id="contentMessage" <?php if (isset ($item) && $item['type'] == "Login") {echo "class=\"noResults contentShow\"";} else {echo "class=\"noResults contentHide\"";} ?>>The system has filled out the rest of the needed information. No further input is needed.</div>
      </div>
      <div class="catDivider three">Finish</div>
      <div class="stepContent">
	  <blockquote>
      	<p>
          <?php submit("submit", "Submit"); ?>
			<input name="reset" type="reset" id="reset" onclick="GP_popupConfirmMsg('Are you sure you wish to clear the content in this form? \rPress \&quot;cancel\&quot; to keep current content.');return document.MM_returnValue" value="Reset" />
            <input name="cancel" type="button" id="cancel" onclick="MM_goToURL('parent','sidebar.php');return document.MM_returnValue" value="Cancel" />
        </p>
          <?php formErrors(); ?>
      </blockquote>
      </div>
    </form>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
