<?php require_once('../../Connections/connDBA.php'); ?>
<?php
//Search for organizations
	if (isset ($_POST['submit'])) {
		$keywords = $_POST['keywords'];
		$searchMethod = $_POST['searchMethod'];
		$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `{$searchMethod}` LIKE '{$keywords}%' ORDER BY `organization` ASC", $connDBA);
		$organizationReturnGrabber = mysql_query("SELECT * FROM `organizations` WHERE `{$searchMethod}` LIKE '{$keywords}%' ORDER BY `organization` ASC", $connDBA);
		$organizationReturnCheck = mysql_fetch_array($organizationReturnGrabber);
		if (!empty($organizationReturnCheck)) {
			$organizationReturn = "true";
		} else {
			$organizationReturn = "false";
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Search for Organizations"); ?>
<?php headers(); ?>
<script src="../../javascripts/common/warningDelete.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Search for organizations</h2>
<?php
//If a search is being performed
	if (!isset ($organizationGrabber)) {
?>
      <p>Search for organizations within this system.</p>
      <div><div align="center">
        <form id="searchOrganizations" name="searchOrganizations" method="post" action="search.php">
          <table width="100%">
            <tr>
              <td width="30%"><div align="right">Keywords:</div></td>
              <td width="70%"><div align="left">
                <input name="keywords" id="keywords" type="text" size="50" autocomplete="off" />
              </td>
            </tr>
            <tr>
              <td width="30%"><div align="right">Search by:</div></td>
              <td width="70%"><div align="left">
                <select name="searchMethod" id="searchMethod">
                  <option value="organization" selected="selected">Name</option>
                  <option value="billingEmail">Email Address</option>
                  <option value="phone">Phone Number</option>
                  <option value="admin">Administrator</option>
                </select>
              </div></td>
            </tr>
            <tr>
              <td width="30%"><div align="right"></div></td>
              <td width="70%"><div align="left">
                <label>
                <input type="submit" name="submit" id="submit" value="Submit" />
                </label>
              </div></td>
            </tr>
          </table>
        </form>
        </div>
	</div>
<?php
//If search results are being displayed	
	} else {
	//If the search results are returned
		if (isset ($organizationReturn) && $organizationReturn == "true") {
			echo "<p>Below are the results for an <strong>organization</strong> ";
			switch ($searchMethod) {
				case "organization" : echo "with a <strong>name</strong> of"; break;
				case "emailAddress" : echo "with an <strong>email address</strong> of"; break;
				case "phone" : echo "with a <strong>phone number</strong> of"; break;
				case "admin" : echo "with an <strong>administrator</strong> of"; break;
			}
			
			echo " <strong>" . $keywords . "</strong>.</p>";
			
			echo "<p>&nbsp;</p><div class=\"toolBar\"><a href=\"search.php\"><img src=\"../../images/admin_icons/search.png\" alt=\"Search\" width=\"24\" height=\"24\" /></a> <a href=\"search.php\">Perform another Search</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php\"><img src=\"../../images/admin_icons/back.png\" alt=\"Back\" width=\"24\" height=\"24\" /></a> <a href=\"index.php\">Back to Organizations</a></div><br />";

		
			echo "<div align=\"center\"><table align=\"center\" class=\"dataTable\">
				<tbody>
					<tr>
						<th width=\"200\" class=\"tableHeader\"><strong>Name</strong></th>
						<th width=\"150\" class=\"tableHeader\"><strong>Email Address</strong></th>
						<th width=\"150\" class=\"tableHeader\"><strong>Phone</strong></th>
						<th class=\"tableHeader\"><strong>Administrator</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Statistics</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Edit</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Delete</strong></th>
					</tr>";
					
					$number = 1;
					while ($organization = mysql_fetch_array($organizationGrabber)) {
						echo "<tr";
						if ($number++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
						echo "<td width=\"200\" class=\"tableHeader\"><div align=\"center\"><a href=\"profile.php?id=" . $organization['id'] . "\">" . $organization['organization'] . "</a></div></td>
						<td width=\"150\" class=\"tableHeader\"><div align=\"center\">";
						
						if ($organization['phone'] == "1") {
							echo "<i>Awaiting Assignment</i>";
						} else {
							echo "<a href=\"mailto:" . $organization['billingEmail'] . "\">" . $organization['billingEmail'] . "</a>";
						}
						
						echo "</div></td>" . 
						"<td width=\"150\" class=\"tableHeader\"><div align=\"center\">";
						if ($organization['phone'] == "1") {
							echo "<i>Awaiting Assignment</i>";
						} else {
							echo "<a href=\"mailto:" . $organization['phone'] . "\">" . $organization['phone'] . "</a>";
						}
						
						echo "</div></td>";
						
						$participatingUser = $organization['organization'];
						$userGrabber = mysql_query("SELECT * FROM `users` WHERE `organization` = '{$participatingUser}' LIMIT 1", $connDBA);
						$userArray = mysql_fetch_array($userGrabber);
						$user = "<a href=\"../users/profile.php?id=" . $userArray['id'] . "\">" . $userArray['lastName'] . ", " . $userArray['firstName'] . "</a>";
						 
						echo "<td> <div align=\"center\">" . $user . "</div></td>" . 
						"<td width=\"50\" class=\"tableHeader\"><div align=\"center\"><a href=\"../statistics/index.php?type=organization&number=single&period=overall&id=" . $organization['id'] . "\">" . "<img src=\"../../images/admin_icons/statistics.png\" alt=\"Statistics\" onmouseover=\"Tip('View <strong>" . $organization['organization'] . " " . "\'s</strong> statistics</strong>')\" onmouseout=\"UnTip()\">" . "</a></div></td>
						<td width=\"50\" class=\"tableHeader\"><div align=\"center\">" . "<a href=\"manage_organization.php?id=" . $organization['id'] . "\"><img src=\"../../images/admin_icons/edit.png\" alt=\"Edit\" onmouseover=\"Tip('Edit <strong> " .  $organization['organization'] . "</strong>')\" onmouseout=\"UnTip()\"></a></div></td>" . 
						"<td width=\"50\" class=\"tableHeader\"><div align=\"center\"><a href=\"javascript:void\" onclick=\"warningDelete('index.php?action=delete&id=" . $organization['id'] . "', 'organization');\"><img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" onmouseover=\"Tip('Delete <strong>" . $organization['organization'] . "</strong>')\" onmouseout=\"UnTip()\"></a></div></td>" . 
						"</tr>";
					}
				 echo "</tbody>
			   </table></div>";
				
		} elseif ($organizationReturn == "false") {
			switch ($searchMethod) {
				case "organization" : $methodError = "with a <strong>name</strong> of"; break;
				case "emailAddress" : $methodError = "with an <strong>email address</strong> of"; break;
				case "phone" : $methodError = "with a <strong>phone number</strong> of"; break;
				case "admin" : $methodError = "with an <strong>administrator</strong> of"; break;
			}

			errorMessage("No results found for an <strong>organization</strong> " . $methodError . " <strong>" . $keywords . "</strong>.");
			echo "<div><div align=\"center\">
					<form id=\"searchOrganizations\" name=\"searchOrganizations\" method=\"post\" action=\"search.php\">
					  <table width=\"640\">
						<tr>
						  <td width=\"30%\"><div align=\"right\">Keywords:</div></td>
						  <td width=\"70%\"><div align=\"left\">
							<label>
							<input name=\"keywords\" type=\"text\" id=\"keywords\" size=\"50\" autocomplete=\"off\" />
							</label>
						  </div></td>
						</tr>
						<tr>
						  <td width=\"30%\"><div align=\"right\">Search by:</div></td>
						  <td width=\"70%\"><div align=\"left\">
							<select name=\"searchMethod\" id=\"searchMethod\">
							  <option value=\"organization\" selected=\"selected\">Name</option>
							  <option value=\"billingEmail\">Email Address</option>
							  <option value=\"phone\">Phone Number</option>
							  <option value=\"admin\">Administrator</option>
							</select>
						  </div></td>
						</tr>
						<tr>
						  <td width=\"30%\"><div align=\"right\"></div></td>
						  <td width=\"70%\"><div align=\"left\">
							<label>
							<input type=\"submit\" name=\"submit\" id=\"submit\" value=\"Submit\" />
							</label>
						  </div></td>
						</tr>
					  </table>
					</form>
					</div>
				</div>";
		}
	}
?>
        <?php footer("site_administrator/includes/bottom_menu.php"); ?>
            </p>
</body>
</html>