<?php require_once('../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Site Administration"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Site Administration</h2>
    <p>Welcome to the administration home page. This page contains a quick reference to major information about this site. Major parts of this site can be administered by navigating the links above.</p>
    <p>&nbsp;</p>
    <div class="layoutControl">
    <div class="contentLeft">
    <div align="center">
    <embed type="application/x-shockwave-flash" src="statistics/charts/line.swf" id="overallstats" name="overallstats" quality="high" allowscriptaccess="always" flashvars="chartWidth=600&chartHeight=350&debugMode=0&DOMId=overallstats&registerWithJS=0&scaleMode=noScale&lang=EN&dataURL=statistics/data/index.php" wmode="transparent" width="600" height="350">
    </embed>
    </div>
    </div>
    <div class="dataRight">
    <div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Registered Users</h2>
        </div>
      </div>
      <div class="content">
      <?php
	  //Select all users from the site
	  		function userCount($role) {
				global $connDBA;
				$userGrabber = mysql_query("SELECT * FROM users WHERE `role` = '{$role}'", $connDBA);
				$userNumber = mysql_num_rows($userGrabber);
				echo "<strong>" . $userNumber . "</strong>";
			}
			
			$userGrabber = mysql_query("SELECT * FROM users", $connDBA);
			$userNumber = mysql_num_rows($userGrabber);
	  ?>
        <p>Number of registered users:</p>
        <ul>
          <li>Site Admin: <?php userCount("Site Administrator"); ?></li>
          <li>Site Managers: <?php userCount("Site Manager"); ?></li>
          <li>Organization Admin: <?php userCount("Organization Administrator"); ?></li>
          <li>Admin Assistants: <?php userCount("Administrative Assistant"); ?></li>
          <li>Instructors: <?php userCount("Instructor"); ?></li>
          <li>Instructorial Assisstants: <?php userCount("Instructorial Assisstant"); ?></li>
          <li>Students: <?php userCount("Student"); ?></li>
        </ul>
        <hr />
        Total Users: <strong><?php echo $userNumber; ?></strong>
      </div>
    </div>
    <div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Active Users</h2>
        </div>
      </div>
      <div class="content">
      <div style="max-height:250px; overflow:auto;">
      <?php
	  //Select all active users from the site
	  		$activeCheck = mysql_query("SELECT * FROM users WHERE `active` = '1'", $connDBA);
			$count = 0;
			
			if (mysql_fetch_array($activeCheck)) {
				$activeGrabber = mysql_query("SELECT * FROM users WHERE `active` = '1'", $connDBA);
				
				echo "<ul>";
				while($activeUsers = mysql_fetch_array($activeGrabber)) {
					echo "<li>" . $activeUsers['firstName'] . " " . $activeUsers['lastName'] . "</li>";
					$count++;
				}
				echo "</ul>";
			} else {
				echo "<div align=\"center\"><p><i>None</i></p></div>";
			}
	  ?>
      </div>
      <hr />
      Total Active Users: <strong><?php echo $count; ?></strong>
      </div>
    </div>
    </div>
    </div>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>