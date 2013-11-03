  <ul>
    <?php
	require_once('/../Connections/connDBA.php');

//$pageData grabs all of the data from the pages table on the database
        $pageData = mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position ASC", $connDBA);
        if (!$pageData) {
            errorMessage("Database query failed.");
        }

//Grab the last item on the list		
		$lastPageCheck = mysql_fetch_array(mysql_query("SELECT * FROM pages WHERE level = 1 ORDER BY position DESC LIMIT 1", $connDBA));
		
//Get page name by URL variable
		if (isset ($_GET['page'])) {
			$currentPage = $_GET['page'];
		}
		
		if (isset ($_GET['subMenu'])) {
			$currentMenu = $_GET['subMenu'];
		}

//$pageInfo uses only specific information from $pageData
//Perform a while loop and go through the specified data until there is no more data
//Hide public out of hidden content
        if (isset($_SESSION['MM_Username']) && isset($_GET['page'])) {
            while ($pageInfo = mysql_fetch_array($pageData)) {
                if ($pageInfo['visible'] == "0" && $currentPage == $pageInfo['position']) {
					   	echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . " (Hidden)</a>";
						
						$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
										} else {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
						
				} elseif ($pageInfo['visible'] == "0" && $currentPage !== $pageInfo['position']) {
					   	echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . " (Hidden)</a>";
						
						$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
										} else {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
                } elseif ($pageInfo['visible'] == "1" && $currentPage == $pageInfo['position']) {
					   	echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . "</a>"; 
						
						$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											if ($subMenu['visible'] == "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										} else {
											if ($subMenu['visible'] == "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
				} elseif ($pageInfo['visible'] == "1" && $currentPage !== $pageInfo['position']) {
					   	echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . "</a>";
						
						$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											if ($subMenu['visible'] == "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										} else {
											if ($subMenu['visible'] == "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
                }
            } 
        } elseif (isset($_SESSION['MM_Username']) && !isset($_GET['page'])) {
			while ($pageInfo = mysql_fetch_array($pageData)) {
                if ($pageInfo['visible'] == "0") {
					   	echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . " (Hidden)</a>";
						$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											if ($subMenu['visible'] !== "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										} else {
											if ($subMenu['visible'] == "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
                } elseif ($pageInfo['visible'] == "1") {
					   	echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . "</a>"; 
						$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											if ($subMenu['visible'] !== "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										} else {
											if ($subMenu['visible'] == "0") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . " (Hidden)</a> " . "</li> | ";
											} else {
												echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
				}
            }
		} else {
            while ($pageInfo = mysql_fetch_array($pageData)) {
				if (isset ($currentPage)) {
					if ($pageInfo['visible'] == "1" && $currentPage == $pageInfo['position']) {
							echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . "</a>"; 
							$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											if ($subMenu['visible'] == "1") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										} else {
											if ($subMenu['visible'] == "1") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
					} elseif ($pageInfo['visible'] == "1" && $currentPage !== $pageInfo['position']) {
							echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . "</a>";
							$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											if ($subMenu['visible'] == "1") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										} else {
											if ($subMenu['visible'] == "1") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
					}
				} else {
					if ($pageInfo['visible'] == "1")	 {
						echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "\">" . $pageInfo['title'] . "</a>";
						$subMenuGrabber = mysql_query("SELECT * FROM pages WHERE level = 2 AND item = {$pageInfo['position']}", $connDBA);
						$subMenuLoop = mysql_fetch_array($subMenuGrabber);
						if ($subMenuLoop['item'] == $pageInfo['position']) {
							echo "<ul>";
							$subMenuCheck = mysql_query("SELECT * FROM pages WHERE item = {$pageInfo['position']} ORDER BY subPosition ASC", $connDBA);
								while($subMenu = mysql_fetch_array($subMenuCheck)) {
									if ($subMenu['item'] == $pageInfo['position']) {
										if (isset ($currentMenu) && $currentMenu == $subMenu['subPosition'] && $currentPage == $pageInfo['position']) {
											if ($subMenu['visible'] == "1") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										} else {
											if ($subMenu['visible'] == "1") {
											echo "<li class=\"first\">" . "<a href=\"index.php?page=" . urlencode ($pageInfo['position']) . "&subMenu=" . urlencode ($subMenu['subPosition']) . "\">" . $subMenu['title'] . "</a> " . "</li> | ";
											}
										}
									}
								}
							echo "</ul>";
						}
						if ($lastPageCheck['position'] !== $pageInfo['position']) {
							echo "</li> <span class=\"arrow sep\">&#x25BA;</span> ";
						} else {
							echo "</li>";
						}
					}
				}
            }
        }
    
//Get the page ID number
    if (isset ($_GET['page'])) {
        $getPageID = $_GET['page'];
            
        $pageInfoGrabber = mysql_query("SELECT * FROM pages WHERE position = {$getPageID} ORDER BY position ASC", $connDBA);
         if ($pageInfoGrabber) {
             $pageInfo = mysql_fetch_array($pageInfoGrabber);
         }
    }
?>
  </ul>
