<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Grant access to this page an it is defined and the organizations exists
	if (isset ($_GET['id'])) {
		$id = $_GET['id'];
		$organizationGrabber = mysql_query("SELECT * FROM organizations WHERE id = '{$id}'", $connDBA);
		if ($organizationCheck = mysql_fetch_array($organizationGrabber)) {
			$organization = $organizationCheck;
		} else {
			$organization = false;
			header("Location: index.php");
			exit;
		}
	} else {
		header("Location: index.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title($organization['organization']); ?>
<?php headers(); ?>
<script src="../../javascripts/common/goToURL.js" type="text/javascript"></script>
<script src="../../javascripts/common/warningDelete.js" type="text/javascript"></script>
</head>

<body>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2><?php echo $organization['organization']; ?></h2>
<p>&nbsp;</p>
<?php 
	echo "<div class=\"toolBar\"><a class=\"toolBarItem editTool\" href=\"manage_user.php?id=" . $organization['id'] . "\">Edit this Organization</a><a class=\"toolBarItem deleteTool\" a href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&id=" . $organization['id'] . "', 'organization')\">Delete this Organization</a></div>";
?>
<br />
<div class="catDivider one">Organization Information</div>
<div class="stepContent">
<table width="100%">
  <tr>
    <td width="200"><div align="right">Organization:</div></td>
    <td><?php echo $organization['organization']; ?></td>
  </tr>
  <?php
  //If an ID is configured
	  if ($organization['organizationID'] != "") {
		  echo "<tr>
			  <td><div align=\"right\">Organization ID:</div></td>
			  <td>" . $organization['organizationID'] . "</td>
		  </tr>";
	  }
  ?>  
  <tr>
    <td width="200"><div align="right">Administrator<?php if (sizeof(explode(",", $organization['admin'])) > 1) {echo "s";} ?>:</div></td>
    <td>
	<?php
		$administrators = "";
		
		foreach (explode(",", $organization['admin']) as $administratorID) {
			$administratorDataGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$administratorID}'", $connDBA);
			$administratorData = mysql_fetch_array($administratorDataGrabber);
			
			$administrators .= "<a href=\"../users/profile.php?id=" . $administratorData['id'] . "\">" . $administratorData['firstName'] . " " . $administratorData['lastName'] . "</a>, ";
		}
		
		echo rtrim($administrators, ", ");
	?>
    </td>
  </tr>
  <?php
  //If a type is configured
	  if ($organization['type'] != "") {
		  echo "<tr>
			  <td><div align=\"right\">Type:</div></td>
			  <td>" . $organization['type'] . "</td>
		  </tr>";
	  }
  ?>
  <?php
  //If a website is configured
	  if ($organization['webSite'] != "") {
		  echo "<tr>
			  <td><div align=\"right\">Website:</div></td>
			  <td><a href=\"" . $organization['webSite'] . "\" target=\"_blank\">" . $organization['webSite'] . "</a></td>
		  </tr>";
	  }
  ?>
</table>
</div>
<div class="catDivider two">Contact Information</div>
<div class="stepContent">
<?php
//Display contact info only if avaliable
	if ($organization['phone'] == "" || $organization['mailingAddress1'] == "" || $organization['mailingCity'] == "" || $organization['mailingState'] == "" || $organization['mailingZIP'] == "" || $organization['billingAddress1'] == "" || $organization['billingCity'] == "" || $organization['billingState'] == "" || $organization['billingZIP'] == "" || $organization['billingPhone'] == "" || $organization['billingFax'] == "" || $organization['billingEmail'] == "") {
		echo "<p><div class=\"noResults\">Awaiting information</div></p></div>
				<div class=\"catDivider three\">Finish</div>
				<div class=\"stepContent\">
				  <blockquote>
					<p><input name=\"finish\" id=\"finish\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Finish\" type=\"button\"></p>
				  </blockquote>
				</div>";
	} else {
?>
    <table width="100%">
    <tr>
      <td width="200"><div align="right">Phone Number:</div></td>
      <td><?php echo $organization['phone']; ?></td>
    </tr>
    <tr>
      <td width="200"><div align="right">Billing Phone Number:</div></td>
      <td><?php echo $organization['billingPhone']; ?></td>
    </tr>
    <tr>
      <td width="200"><div align="right">Billing Fax Number:</div></td>
      <td><?php echo $organization['billingFax']; ?></td>
    </tr>
<?php
	//Do not repeat the mailing and billing addresses if they are the same
		if ($organization['mailingAddress1'] == $organization['billingAddress1'] && $organization['mailingAddress2'] == $organization['billingAddress2'] && $organization['mailingCity'] == $organization['billingCity'] && $organization['mailingState'] == $organization['billingState'] && $organization['mailingZIP'] == $organization['billingZIP']) {
?>
	<tr>
      <td width="200"><div align="right">Address:</div></td>
      <td>
	  <?php
		  if ($organization['mailingAddress2'] != "") {
			  $link = $organization['mailingAddress2'] . " ";
		  }	else {
			  $link = "";
		  }
		  
		 echo "<a href=\"http://maps.google.com/maps?q=" . urlencode($organization['mailingAddress1'] . " " . $link . $organization['mailingCity'] . ", " . $organization['mailingState'] . " " . $organization['mailingZIP']) . "\" target=\"_blank\">" . $organization['mailingAddress1'];
		  
		  if ($organization['mailingAddress2'] != "") {
			  echo "<br />" .$organization['mailingAddress2'];
		  }
		  
		  echo "<br />" . $organization['mailingCity'] . ", " . $organization['mailingState'] . " " . $organization['mailingZIP'];
	  ?></td>
    </tr>
<?php
		} else {
?>

	<tr>
      <td width="200"><div align="right">Mailing Address:</div></td>
      <td>
	  <?php
		  if ($organization['mailingAddress2'] != "") {
			  $link = $organization['mailingAddress2'] . " ";
		  }	else {
			  $link = "";
		  }
		  
		 echo "<a href=\"http://maps.google.com/maps?q=" . urlencode($organization['mailingAddress1'] . " " . $link . $organization['mailingCity'] . ", " . $organization['mailingState'] . " " . $organization['mailingZIP']) . "\" target=\"_blank\">" . $organization['mailingAddress1'];
		  
		  if ($organization['mailingAddress2'] != "") {
			  echo "<br />" .$organization['mailingAddress2'];
		  }
		  
		  echo "<br />" . $organization['mailingCity'] . ", " . $organization['mailingState'] . " " . $organization['mailingZIP'];
	  ?></td>
    </tr>
    <tr>
      <td width="200"><div align="right">Billing Address:</div></td>
      <td>
	  <?php
		  if ($organization['billingAddress2'] != "") {
			  $link = $organization['billingAddress2'] . " ";
		  }	else {
			  $link = "";
		  }
		    
		  echo "<a href=\"http://maps.google.com/maps?q=" . urlencode($organization['billingAddress1'] . " " . $link . $organization['billingCity'] . ", " . $organization['billingState'] . " " . $organization['billingZIP']) . "\" target=\"_blank\">" . $organization['billingAddress1'];
		  
		  if ($organization['billingAddress2'] != "") {
			  echo "<br />" .$organization['billingAddress2'];
		  }
		  
		  echo "<br />" . $organization['billingCity'] . ", " . $organization['billingState'] . " " . $organization['billingZIP'] . "</a>";
	  ?></td>
    </tr>
<?php
		}
?>
  </table>
</div>
<div class="catDivider three">Contract Information</div>
<div class="stepContent">
  <table width="100%">
  	<tr>
      <td width="200"><div align="right">Contract Start:</div></td>
      <td><?php echo $organization['contractStart']; ?></td>
    </tr>
    <tr>
      <td width="200"><div align="right">Contract End (next bill due):</div></td>
      <td><?php echo $organization['contractEnd']; ?></td>
    </tr>
    <tr>
      <td width="200"><div align="right">Active:</div></td>
      <td>
	  <?php
		  if ($organization['active'] == "1") {
			  echo "Yes";
		  } else {
			  echo "No";
		  }
	  ?>
      </td>
    </tr>
  </table>
</div>
<div class="catDivider four">Finish</div>
<div class="stepContent">
  <blockquote>
    <p><input name="finish" id="finish" onclick="MM_goToURL('parent','index.php');return document.MM_returnValue" value="Finish" type="button"></p>
  </blockquote>
</div>
<?php			
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>
