<?php require_once('../../Connections/connDBA.php'); ?>
<?php
//Search for users
	if (isset ($_POST['submit']) && !empty($_POST['keywords'])) {
		$keywords = $_POST['keywords'];
		$searchMethod = $_POST['searchMethod'];
		$userGrabber = mysql_query("SELECT * FROM `users` WHERE `{$searchMethod}` LIKE '{$keywords}%' ORDER BY `lastName` ASC", $connDBA);
		$userReturnGrabber = mysql_query("SELECT * FROM `users` WHERE `{$searchMethod}` LIKE '{$keywords}%' ORDER BY `lastName` ASC", $connDBA);
		$userReturnCheck = mysql_fetch_array($userReturnGrabber);
		if (!empty($userReturnCheck)) {
			$userReturn = "true";
		} else {
			$userReturn = "false";
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Search for Users"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Search for Users</h2>
<?php
//If a search is being performed
	if (!isset ($userGrabber)) {
?>
      <p>Search for users within this system.</p>
      <div><div align="center">
        <form id="searchUsers" name="searchUsers" method="post" action="search.php">
          <table width="100%">
            <tr>
              <td width="30%"><div align="right">Keywords:</div></td>
              <td width="70%"><div align="left">
                <input name="keywords" id="keywords" type="text" size="50" autocomplete="off" />
                </div>
              </td>
            </tr>
            <tr>
              <td width="30%"><div align="right">Search by:</div></td>
              <td width="70%"><div align="left">
                <select name="searchMethod" id="searchMethod">
                  <option value="firstName" selected="selected">First Name</option>
                  <option value="lastName">Last Name</option>
                  <option value="userName">User Name</option>
                  <option value="emailAddress">Email Address</option>
                  <option value="role">Role</option>
                  <option value="organization">Organization</option>
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
		if (isset ($userReturn) && $userReturn == "true") {
			echo "<p>Below are the results for a <strong>user</strong> ";
			switch ($searchMethod) {
				case "firstName" : echo "with a <strong>first name</strong> of"; break;
				case "lastName" : echo "with a <strong>last name</strong> of"; break;
				case "userName" : echo "with a <strong>user name</strong> of"; break;
				case "emailAddress" : echo "with an <strong>email address</strong> of"; break;
				case "role" : echo "with a <strong>role</strong> of"; break;
				case "organization" : echo "who is participating in the <strong>organization</strong>"; break;
			}
			
			echo " <strong>" . $keywords . "</strong>.</p>";
			
			echo "<p>&nbsp;</p><div class=\"toolBar\"<a class=\"toolBarItem search\" href=\"search.php\">Perform another Search</a><a class=\"toolBarItem back\" href=\"index.php\">Back to Users</a></div><br />";

		
			echo "<div align=\"center\"><table align=\"center\" class=\"dataTable\">
				<tbody>
					<tr>
						<th width=\"200\" class=\"tableHeader\"><strong>Name</strong></th>
						<th width=\"200\" class=\"tableHeader\"><strong>Primary Email</strong></th>
						<th width=\"175\" class=\"tableHeader\"><strong>Role</strong></th>
						<th class=\"tableHeader\"><strong>Participating Organization</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Statistics</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Edit</strong></th>
						<th width=\"50\" class=\"tableHeader\"><strong>Delete</strong></th>
					</tr>";
					
					$number = 1;
					while ($user = mysql_fetch_array($userGrabber)) {
						echo "<tr";
						if ($number++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
						echo "<td width=\"200\" class=\"tableHeader\"><div align=\"center\"><a href=\"profile.php?id=" . $user['id'] . "\">" . $user['lastName'] . ", " . $user['firstName'] . "</a></div></td>
						<td width=\"200\" class=\"tableHeader\"><div align=\"center\"><a href=\"mailto:" . $user['emailAddress1'] . "\">" . $user['emailAddress1'] . "</a></div></td>
						<td width=\"175\" class=\"tableHeader\"><div align=\"center\">" . $user['role'] . "</div></td>";
						if (!$user['organization'] || $user['organization'] == "1") {
							if ($user['role'] !== "Site Administrator" && $user['role'] !== "Site Manager") {
								$organization = "<img src=\"../../images/admin_icons/warning.png\" alt=\"Alert\" width=\"24\" height=\"24\" onmouseover=\"Tip('This user is not assigned to an organization')\" onmouseout=\"UnTip()\" /> <i>None</i>";
							} else {
								$organization = "<i>None</i>";
							}
						} else {
							$participatingOrganization = $user['organization'];
							$organizationGrabber = mysql_query("SELECT * FROM `organizations` WHERE `organization` = '{$participatingOrganization}' LIMIT 1", $connDBA);
							$organizationArray = mysql_fetch_array($organizationGrabber);
							$organization = "<a href=\"../organizations/profile.php?id=" . $organizationArray['id'] . "\">" . $organizationArray['organization'] . "</a>";
						}
						 
						echo "<td> <div align=\"center\">" . $organization . "</div></td>" . 
						"<td width=\"50\" class=\"tableHeader\"><div align=\"center\"><a href=\"../statistics/index.php?type=user&number=single&period=overall&id=" . $user['id'] . "\">" . "<img src=\"../../images/admin_icons/statistics.png\" alt=\"Statistics\" onmouseover=\"Tip('View <strong>" . $user['firstName'] . " " .  $user['lastName'] . "\'s</strong> statistics</strong>')\" onmouseout=\"UnTip()\">" . "</a></div></td>
						<td width=\"50\" class=\"tableHeader\"><div align=\"center\">";
						if ($user['userName'] !== $_SESSION['MM_Username']) {
							echo "<a href=\"manage_user.php?id=" . $user['id'] . "\"><img src=\"../../images/admin_icons/edit.png\" alt=\"Edit\" onmouseover=\"Tip('Edit <strong> " .  $user['firstName'] . " " . $user['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>";
						} else {
							echo "<img src=\"../../images/admin_icons/noEdit.png\" alt=\"Edit\" onmouseover=\"Tip('You may not edit yourself')\" onmouseout=\"UnTip()\">";
						}
						echo "</div></td>" . 
						"<td width=\"50\" class=\"tableHeader\"><div align=\"center\">";
						if ($user['userName'] !== $_SESSION['MM_Username']) {
							echo "<a href=\"index.php?action=delete&id=" . $user['id'] . "\" onclick=\"return confirm ('This action cannot be undone. Continue?');\"><img src=\"../../images/admin_icons/delete.png\" alt=\"Delete\" onmouseover=\"Tip('Delete <strong>" . $user['firstName'] . " " .  $user['lastName'] . "</strong>')\" onmouseout=\"UnTip()\"></a>";
						} else {
							echo "<img src=\"../../images/admin_icons/noDelete.png\" alt=\"Delete\" onmouseover=\"Tip('You may not delete yourself')\" onmouseout=\"UnTip()\">";
						}
						echo "</div></td>" . 
						"</tr>";
					}
				 echo "</tbody>
			   </table></div>";
				
		} elseif ($userReturn == "false") {
			switch ($searchMethod) {
				case "firstName" : $methodError = "with a <strong>first name</strong> of"; break;
				case "lastName" : $methodError = "with a <strong>last name</strong> of"; break;
				case "userName" : $methodError = "with a <strong>user name</strong> of"; break;
				case "emailAddress" : $methodError = "with an <strong>email address</strong> of"; break;
				case "role" : $methodError = "with a <strong>role</strong> of"; break;
				case "organization" : $methodError = "who is participating in the <strong>organization</strong>"; break;
			}

			errorMessage("No results found for a <strong>user</strong> " . $methodError . " <strong>" . $keywords . "</strong>.");
			echo "<div><div align=\"center\">
					<form id=\"searchUsers\" name=\"searchUsers\" method=\"post\" action=\"search.php\">
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
							  <option value=\"firstName\" selected=\"selected\">First Name</option>
							  <option value=\"lastName\">Last Name</option>
							  <option value=\"userName\">User Name</option>
							  <option value=\"emailAddress\">Email Address</option>
							  <option value=\"role\">Role</option>
							  <option value=\"organization\">Organization</option>
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