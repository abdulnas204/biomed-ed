<?php require_once('../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Check for organizations
	$organizationsCheck = mysql_query("SELECT * FROM `organizations`", $connDBA);
	
	if (mysql_fetch_array($organizationsCheck)) {
		$organizations = "exist";
	} else {
		$organizations = "empty";
	}
?>
<?php
//Delete an organization
	if (isset ($_GET['action']) && isset ($_GET['id']) && $_GET['action'] == "delete") {
		$id = $_GET['id'];
		$organizationCheck = mysql_query("SELECT * FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
		if ($organization = mysql_fetch_array($organizationCheck)) {
			$organizationID = $organization['id'];
			mysql_query("DELETE FROM `organizations` WHERE `id` = '{$id}'", $connDBA);
			mysql_query("DELETE FROM `users` WHERE `organization` = '{$organizationID}'", $connDBA);
			
			header ("Location: index.php");
			exit;
		} else {
			header ("Location: index.php");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Organizations"); ?>
<?php headers(); ?>
<script src="../../javascripts/common/warningDelete.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Organizations</h2>
<p>Below is a list of all organizations registered within this system. Organizations may be sorted according to a certain criteria by clicking on the text in the header row of the desired column.</p>
<p>&nbsp;</p>
    <div class="toolBar"><a class="toolBarItem new" href="manage_organization.php">Add New Organization</a><a class="toolBarItem billing" href="manage_billing.php">Manage Billing</a>
<?php
	if ($organizations == "exist") {
		echo "<a class=\"toolBarItem search\" href=\"search.php\">Search for Organizations</a>";
	}
?>
</div>
<?php
//An organization was created
	if (isset($_GET['message']) && $_GET['message'] == "organizationCreated") {
		successMessage("The organization was created");	
//An organization was edited
	} elseif (isset($_GET['message']) && $_GET['message'] == "organizationEdited") {
		successMessage("The organization was modified");
	} else {
		echo "<br />";
	}
?>
<?php
	if (isset($_GET['limit'])) {
		$limit = $_GET['limit'];
		
		if ($limit == "all") {
			$showAll = "true";
		}
		
		if ($limit == "1") {
			header("Location: index.php");
			exit;
		}
	} else {
		$limit = "25";
	}
	
	if (isset($_GET['sort']) && isset($_GET['order'])) {
		$sortArray = explode(".", $_GET['sort']);
		$sortArrayValues = count($sortArray) - 1;
		
		$sort = " ORDER BY ";
		for ($count = 0; $count <= $sortArrayValues; $count++) {
			if ($_GET['order']) {
				$orderArray = explode(".", $_GET['order']);
				$orderArrayValues = count($orderArray) - 1;
				
				switch($orderArray[$count]) {
					case "ascending" : $order = " ASC"; break;
					case "descending" : $order = " DESC"; break;
				}
			} else {
				$order = " ASC";
			}
			
			if ($orderArrayValues != $sortArrayValues) {
				header("Location: index.php");
				exit;
			}
			
			$sort .= $sortArray[$count];
			
			if ($count != $sortArrayValues) {
				$sort .= $order . ", ";
			} else {
				$sort .= $order . " ";
			}
		}
	} else {
		$sort = " ORDER BY organization ASC, billingEmail ";
		$order = "ASC ";
	}
	
	if (!isset($showAll)) {
		$objectNumberGrabber = mysql_query("SELECT * FROM organizations", $connDBA);
		$objectNumber = mysql_num_rows($objectNumberGrabber);
		$searchPages = ceil($objectNumber/$limit);
		
		if (!isset($_GET['page'])) {
			$organizationGrabber = mysql_query("SELECT * FROM organizations{$sort}LIMIT 0, {$limit}", $connDBA);
		} else {
			$searchPage = $_GET['page'];
			
			if ($searchPage > $searchPages) {
				header("Location: index.php");
				exit;
			}
			
			if ($searchPage == "1") {
				$lowerLimit = ($searchPage*$limit)-$limit;
			
				$organizationGrabber = mysql_query("SELECT * FROM organizations{$sort}LIMIT 0, {$limit}", $connDBA);
			} else {
				$lowerLimit = ($searchPage*$limit)-$limit;
				
				$organizationGrabber = mysql_query("SELECT * FROM organizations{$sort}LIMIT {$lowerLimit}, {$limit}", $connDBA);
			}
		}
		
		if (!isset($searchPages) || $searchPages != "1") {
			if (!isset($_GET['page'])) {
				$navigationPage = "1";
			} else {
				$navigationPage = $_GET['page'];
			}
			
			if (isset($_GET['sort']) && isset($_GET['order'])) {
				$additionalParameters = "sort=" . $_GET['sort'] . "&order=" . $_GET['order'] . "&";
			} else {
				$additionalParameters = "";
			}
			
			$navigation = "<div class=\"pagesBox\">";
			if (isset($_GET['page'])) {
				if ($_GET['page'] != "1") {
					$previousPage = $navigationPage - 1;
					
					$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $previousPage . "\">(Previous)</a>";
				}
			}
			
			for ($count = 1; $count <= $searchPages; $count++) {
			//If there are less than or equal to 15 pages, then display them all
				if ($searchPages - 15 <= 1) {
					if ($navigationPage != $count) {
						$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
					} else {
						$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
					}
				}
				
			//If there are more than or equal to 15 pages
				if ($searchPages - 15 > 1) {
				//If the pages are in the lower set, then only break the upper set
					if ($navigationPage < 8) {
						$orginalUpper = $navigationPage - 7;
						switch ($searchPages - $navigationPage) {
							case "0" : $additionalLower = 6; break;
							case "1" : $additionalLower = 5; break;
							case "2" : $additionalLower = 4; break;
							case "3" : $additionalLower = 3; break;
							case "4" : $additionalLower = 2; break;
							case "5" : $additionalLower = 1; break;
							case "6" : $additionalLower = 0; break;
						}
						
						if ($count <= 14) {
							if ($navigationPage != $count) {
								$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
							} else {
								$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
							}
							
							if  ($count == 14) {
								$navigation .= "...<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
							}
						}
					}
					
				//If the pages are somewhere in the middle
					if ($navigationPage >= 8) {	
					//If this page is not one page after the first page, break the list (e.g.: NOT 1 3, BUT 1 ... 3)
						$additionalLower = 0;
						
						switch ($searchPages - $navigationPage) {
							case "0" : $additionalLower = 6; break;
							case "1" : $additionalLower = 5; break;
							case "2" : $additionalLower = 4; break;
							case "3" : $additionalLower = 3; break;
							case "4" : $additionalLower = 2; break;
							case "5" : $additionalLower = 1; break;
							case "6" : $additionalLower = 0; break;
						}
						
						if ($count == $navigationPage - 6 - $additionalLower && $count != 2) {
							$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=1\">1</a>...";
						} elseif ($count == $navigationPage - 6 - $additionalLower && $count == 2) {
							$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=1\">1</a>";
						}
					
					//Do not break the upper set of pages, if the user is approaching the end, and display a constant number of suggestions				
						if ($navigationPage + 7 > $searchPages) {
							$orginalLower = $navigationPage - 7;
							
							if ($orginalLower - $additionalLower < $count && $count < $navigationPage + 7) {
								if ($navigationPage != $count) {
									$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
								} else {
									$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
								}
							}
					//Display all pages in the center with a value of +- 6, with the upper and lower extremes
						} else {
						//For all pages in the center of the list
							if ($navigationPage - 7 < $count && $count < $navigationPage + 7) {
							//For the one page before the last page, do not break the list (e.g.: NOT 18 ... 19, BUT 18 19)
								if ($count + 1 == $searchPages) {									
									if ($navigationPage != $count) {
										$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
									} else {
										$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
									}
									
									$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
									break;
								}
								
							//If this page is not one page before the last page, break the list (e.g.: NOT 17 19, BUT 17 ... 19)	
								if ($count + 1 != $searchPages) {
									if ($navigationPage != $count) {
										$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
									} else {
										$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
									}
								}
								
								if ($count == $navigationPage + 6) {
									$navigation .= "...<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
								}
							}
						}
					}
				}
			}
			
			if (isset($_GET['page'])) {
				if ($_GET['page'] != $searchPages) {
					$nextPage = $navigationPage + 1;
					
					$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $nextPage . "\">(Next)</a>";
				}
			} else {
				$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=2\">(Next)</a>";
			}
			$navigation .= "</div><br />";
		}
	} else {
		$organizationGrabber = mysql_query("SELECT * FROM organizations{$sort}", $connDBA);
	}
	
	if (isset($navigation) && $organizations != "empty") {
		echo $navigation;
	}
	
	$organizationNumberGrabber = mysql_query("SELECT * FROM organizations ORDER BY organization ASC, billingEmail ASC", $connDBA);
	$organizationNumber = mysql_num_rows($organizationNumberGrabber);
	
	if (!$organizationGrabber && $organizationNumber) {
		header("Location: index.php");
		exit;
	}
?>
<?php
//If no organizations exist	
	if (isset ($organizations) && $organizations == "empty") {
		echo "<div class=\"noResults\">No organizations exist on this system. <a href=\"manage_organization.php\">Create one now</a>.</div>";
	} else {
//If organizations exist
		echo "<table class=\"dataTable\">
		<tbody>
			<tr>";
			
				if (isset($_GET['limit'])) {
					$additionalParameters = "&limit=" . $_GET['limit'] . "&page=1";
				} else {
					$additionalParameters = "&page=1";
				}
							
				echo "<th width=\"200\" class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "organization.billingEmail" && $_GET['order'] == "descending.ascending") {
					echo "?sort=organization.billingEmail&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Name</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "organization.billingEmail" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=organization.billingEmail&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Name</a>";
				} elseif (!isset ($_GET['sort'])) {
					echo "?sort=organization.billingEmail&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Name</a>";
				} else {
					echo "?sort=organization.billingEmail&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">Name</a>";
				}
				echo "</th>
				<th width=\"150\" class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "billingEmail.organization" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=billingEmail.organization&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Email Address</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "billingEmail.organization" && $_GET['order'] == "descending.ascending") {
					echo "?sort=billingEmail.organization&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Email Address</a>";
				} else {
					echo "?sort=billingEmail.organization&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">Email Address</a>";
				}
				echo "</th>
				<th width=\"175\" class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "phone.organization" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=phone.organization&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Phone Number</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "phone.organization" && $_GET['order'] == "descending.ascending") {
					echo "?sort=phone.organization&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Phone Number</a>";
				} else {
					echo "?sort=phone.organization&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">Phone Number</a>";
				}
				echo "</th>
				<th class=\"tableHeader\"><a href=\"index.php";
				if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "admin.organization" && $_GET['order'] == "ascending.ascending") {
					echo "?sort=admin.organization&order=descending.ascending" . $additionalParameters . "\" class=\"descending\">Administrators</a>";
				} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "admin.organization" && $_GET['order'] == "descending.ascending") {
					echo "?sort=admin.organization&order=ascending.ascending" . $additionalParameters . "\" class=\"ascending\">Administrators</a>";
				} else {
					echo "?sort=admin.organization&order=ascending.ascending" . $additionalParameters . "\" class=\"sortHover\">Administrators</a>";
				}
				echo "</th>
				<th width=\"50\" class=\"tableHeader\">Statistics</th>
				<th width=\"50\" class=\"tableHeader\">Edit</th>
				<th width=\"50\" class=\"tableHeader\">Delete</th>
				
			</tr>";
		$number = 1;
		while(($organizationData = mysql_fetch_array($organizationGrabber)) && ($number <= $organizationNumber)) {
			echo "<tr";
			//Alternate the color of each row.
			if ($number++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
			echo "<td width=\"200\"><a href=\"profile.php?id=" . $organizationData['id'] . "\">" . $organizationData['organization'] . "</a></td>" . 
			"<td width=\"150\">";
				
			if ($organizationData['billingEmail'] == "") {
				echo "<span class=\"notAssigned\">Awaiting Assignment</span>";
			} else {
				echo "<a href=\"../communication/send_email.php?type=organization&id=" . $organizationData['id'] . "\">" . $organizationData['billingEmail'] . "</a></td>";
			}
			
			echo "<td width=\"175\">";
			
			if ($organizationData['phone'] == "") {
				echo "<span class=\"notAssigned\">Awaiting Assignment</span>";
			} else {
				echo $organizationData['phone'];
			}
				
			echo "</td>";
			
			$administratorID = explode(",", $organizationData['admin']);
			$administrator = "";
			
			foreach ($administratorID as $administratorID) {
				$administratorDataGrabber = mysql_query("SELECT * FROM `users` WHERE `id` = '{$administratorID}'", $connDBA);
				$administratorData = mysql_fetch_array($administratorDataGrabber);
				
				$administrator .= $administratorData['firstName'] . " " . $administratorData['lastName'] . ", ";
			}
			
			echo "<td>" . commentTrim("65", rtrim($administrator, ", ")) . "</td>"; 
			echo "<td width=\"50\"><a class=\"action statistics\" href=\"../statistics/index.php?type=organization&period=overall&id=" . $organizationData['id'] . "\" onmouseover=\"Tip('View the <strong>" . $organizationData['organization'] . "\'s</strong> statistics')\" onmouseout=\"UnTip()\"></a></td>" . 
			"<td width=\"50\"><a class=\"action edit\" href=\"manage_organization.php?id=" . $organizationData['id'] . "\" onmouseover=\"Tip('Edit the <strong>" .  $organizationData['organization'] . "</strong> organization')\" onmouseout=\"UnTip()\"></a>
			</td>" . "<td width=\"50\">";
			echo "<a class=\"action delete\" href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&id=" . $organizationData['id'] . "', 'organization')\" onmouseover=\"Tip('Delete the <strong>" . $organizationData['organization'] . "</strong> organization')\" onmouseout=\"UnTip()\"></a>";
			echo "</td></tr>";
		}
		echo "</tbody>
		</table>";
	}
?>
<?php
	if (isset($navigation) && $organizations != "empty") {
		echo "<br />" . $navigation;
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>