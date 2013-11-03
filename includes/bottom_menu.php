<ul class="footer_menu">
<?php	
require_once('../Connections/connDBA.php');
			
//$pageData grabs all of the data from the pages table on the database
	$pageData = mysql_query("SELECT * FROM pages ORDER BY position ASC", $connDBA);
	if (!$pageData) {
		errorMessage("Database query failed.");
	}

//Grab the last item on the list		
	$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages ORDER BY position DESC LIMIT 1", $connDBA));
	
//Get page name by URL variable
	if (isset ($_GET['page'])) {
		$currentPage = $_GET['page'];
	}

	while ($pageInfo = mysql_fetch_array($pageData)) {
		if (isset ($currentPage)) {
			if ($pageInfo['visible'] != "") {
				if ($currentPage == $pageInfo['id']) {
					echo "<li><a href=\"index.php?page=" . $pageInfo['id'] . "\" class=\"nav_footer\"><strong>" . stripslashes($pageInfo['title']) . "</strong></a>";
				} else {
					echo "<li><a href=\"index.php?page=" . $pageInfo['id'] . "\" class=\"nav_footer\">" . stripslashes($pageInfo['title']) . "</a>";
				}
				
				if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
					echo "</li><span class=\"arrow sep\">&bull;</span>";
				} else {
					echo "</li>";
				}
			}
		} else {
			if ($pageInfo['visible'] != "") {
				if ($pageInfo['position'] == "1") {
					echo "<li><a href=\"index.php?page=" . $pageInfo['id'] . "\" class=\"nav_footer\"><strong>" . stripslashes($pageInfo['title']) . "</strong></a>";
				} else {
					echo "<li><a href=\"index.php?page=" . $pageInfo['id'] . "\" class=\"nav_footer\">" . stripslashes($pageInfo['title']) . "</a>";
				}
				
				if ($lastPageCheck['position'] != $pageInfo['position'] && $lastPageCheck['visible'] != "") {
					echo "</li><span class=\"arrow sep\">&bull;</span>";
				} else {
					echo "</li>";
				}
			}
		}
	}
?>
</ul>