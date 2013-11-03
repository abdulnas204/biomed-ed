
<ul class="footer_menu">
  <?php	
  			require_once('/../Connections/connDBA.php');
			
			$pageGrabber = mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position ASC", $connDBA);
			if (!$pageGrabber) {
				errorMessage("The pages could not be found.");
			}
					
		$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position DESC LIMIT 1", $connDBA));
	
			$endCheckGrabber = mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position DESC LIMIT 1", $connDBA);
			if (!$endCheckGrabber) {
				errorMessage("The pages could not be found.");
			}

		//$pageData grabs all of the data from the pages table on the database
			$pageData = mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position ASC", $connDBA);
			if (!$pageData) {
			    errorMessage("Database query failed.");
			}
			
			$endCheckFetch = mysql_fetch_array($endCheckGrabber);
			$endCheck = $endCheckFetch['position'];
			$breakCheck = $endCheckFetch['visible'];
		
		//$pageInfo uses only specific information from $pageData
		//Perform a while loop and go through the specified data until there is no more data
		//Hide an lock public out of hidden content
			if (isset($_SESSION['MM_Username'])) {
				while ($pageInfo = mysql_fetch_array($pageData)) {
					if ($pageInfo['visible'] == "0") {
						echo "<li><a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\" class=\"nav_footer\">" . $pageInfo['title'] . " (Hidden)" . "</a></li>";
					} else {
						echo "<li><a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\" class=\"nav_footer\">" . $pageInfo['title'] . "</a>";
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> &bull; ";
						} else {
							echo "</li>";
						}
					} 
				}
			} else {
				while ($pageInfo = mysql_fetch_array($pageData)) {
					if ($pageInfo['visible'] !== "0") {
						echo "<li><a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\" class=\"nav_footer\">" . $pageInfo['title'] . "</a>";
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> &bull; ";
						} else {
							echo "</li>";
						}
					}
				}
			}
		?>
</ul>
<!-- end of main_container -->
