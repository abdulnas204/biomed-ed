<?php require_once('../../Connections/connDBA.php'); ?>
<?php
//Check to see if the page is being edited
	if (isset ($_GET['id'])) {
		$page = $_GET['id'];
		$pageGrabber = mysql_query("SELECT * FROM pages WHERE `id` = '{$page}'", $connDBA);
		if ($pageCheck = mysql_fetch_array($pageGrabber)) {
			$page = $pageCheck;
		} else {
			header ("Location: index.php");
			exit;
		}
	}
	
//Process the form
	if (isset($_POST['submit']) && !empty ($_POST['title']) && !empty($_POST['content'])) {	
		if (!isset ($page)) {
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
			
			$positionGrabber = mysql_query ("SELECT * FROM pages ORDER BY position DESC", $connDBA);
			$positionArray = mysql_fetch_array($positionGrabber);
			$position = $positionArray{'position'}+1;
				
			$newPageQuery = "INSERT INTO pages (
								`id`, `title`, `visible`, `position`, `content`
							) VALUES (
								NULL, '{$title}', 'on', '{$position}', '{$content}'
							)";
			
			mysql_query($newPageQuery, $connDBA);
			header ("Location: index.php?added=page");
			exit;
		} else {
			$page = $_GET['id'];
			$title = mysql_real_escape_string($_POST['title']);
			$content = mysql_real_escape_string($_POST['content']);
				
			$editPageQuery = "UPDATE pages SET title = '{$title}', content = '{$content}' WHERE `id` = '{$page}'";
			
			mysql_query($editPageQuery, $connDBA);
			header ("Location: index.php?updated=page");
			exit;
		}
	} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php 
	if (isset ($page)) {
		$title = "Edit the " . stripslashes(htmlentities($page['title'])) . " Page";
	} else {
		$title =  "Create a New Page";
	}
	
	title($title); 
?>
<?php headers(); ?>
<?php tinyMCEAdvanced(); ?>
<?php validate(); ?>
<script src="../../javascripts/common/popupConfirm.js" type="text/javascript"></script>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
      
    <h2>
      <?php if (isset ($page)) {echo "Edit the \"" . $page['title'] . "\" Page";} else {echo "Create New Page";} ?>
    </h2>
<p>Use this page to <?php if (isset ($page)) {echo "edit the content of \"<strong>" . stripslashes(htmlentities($page['title'])) . "</strong>\"";} else {echo "create a new page";} ?>.</p>
    <p>&nbsp;</p>
    <form action="manage_page.php<?php 
		if (isset ($page)) {
			echo "?id=" . $page['id'];
		}
	?>" method="post" name="managePage" id="validate" onsubmit="return errorsOnSubmit(this);">
      <div class="catDivider one">Content</div>
      <div class="stepContent">
      <blockquote>
        <p>Title<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The text that will display in big letters on the top-left of each page &lt;br /&gt;and at the top of the browser window')" onmouseout="UnTip()" /></p>
        <blockquote>
          <p>
            <input name="title" type="text" id="title" size="50" autocomplete="off" class="validate[required]"<?php
            	if (isset ($page)) {
					echo " value=\"" . stripslashes(htmlentities($page['title'])) . "\"";
				}
			?> />
          </p>
        </blockquote>
        <p>Content<span class="require">*</span>: <img src="../../images/admin_icons/help.png" alt="Help" width="17" height="17" onmouseover="Tip('The main content or body of the webpage')" onmouseout="UnTip()" /> </p>
        <blockquote>
        <p><span id="contentCheck">
            <textarea name="content" id="content2" cols="45" rows="5" style="width:640px; height:320px;" /><?php 
				if (isset ($page)) {
					echo stripslashes($page['content']);
				}
			?></textarea>
          <span class="textareaRequiredMsg"></span></span>
          </p>
        </blockquote>
        <p align="left">&nbsp;</p>
      </blockquote>
      </div>
      <div class="catDivider two">Finish</div>
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
<script type="text/javascript">
<!--
var sprytextarea1 = new Spry.Widget.ValidationTextarea("contentCheck");
//-->
</script>
</body>
</html>
