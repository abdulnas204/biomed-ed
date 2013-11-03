<?php require_once("../../../Connections/connDBA.php"); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Define this as a javascript file
	header ("Content-type: text/javascript");

//Grab all of the pages	
	$pageCheck = mysql_query("SELECT *FROM pages", $connDBA);
	if (mysql_fetch_array($pageCheck)) {
		$pageDataGrabber = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);
		$pageCountGrabber = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);
		$pageCount = mysql_num_rows($pageCountGrabber);
		
		echo "var tinyMCELinkList = new Array(";
		while ($page = mysql_fetch_array($pageDataGrabber)) {
			echo "[\"" . $page['title'] . "\", \"" . $root . "index.php?page=" . $page['id'] . "\"]";
			
			if ($page['position'] != $pageCount) {
				echo ", ";
			}
		}
		echo ");";
	} else {
		echo "var tinyMCELinkList = new Array([\"Home Page\", \"" . $root . "index.php\"]);";
	}
?>