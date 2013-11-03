<?php require_once('../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Site Administration"); ?>
<?php headers(); ?>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
    <h2>Site Administration</h2>
    <div class="layoutControl">
    <div class="contentLeft">
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus  turpis elit, ultrices in vestibulum in, fermentum nec quam.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas. Morbi venenatis, nulla ut eleifend pharetra,  odio urna vulputate arcu, ut ullamcorper quam mauris non purus. Quisque  lacus turpis, scelerisque sed commodo rutrum, tempor sed nisi. Nunc  tortor sem, eleifend quis imperdiet sed, imperdiet sit amet orci. Nunc  vel justo diam, ac sollicitudin erat. Nam non tortor vel est porttitor  placerat. Sed a justo sed neque viverra mollis. Curabitur eu enim ut  tortor cursus accumsan ac a purus. Maecenas cursus malesuada dolor, in  dignissim magna porttitor at. Maecenas vel metus sit amet felis  faucibus congue ac eget mauris. Nam elit urna, scelerisque vel sodales  vel, tempus sed nibh. Vestibulum facilisis rutrum tellus nec accumsan.  Vivamus pellentesque sem elit. Aliquam id orci ut ligula auctor  fringilla. Phasellus auctor adipiscing placerat.</p>
    <p>Donec vitae quam elit. Donec venenatis, augue ac ultricies semper,  sapien est porttitor nibh, quis aliquam elit elit non lorem. Class  aptent taciti sociosqu ad litora torquent per conubia nostra, per  inceptos himenaeos. Vivamus lobortis lacinia justo, in vehicula elit  pharetra nec. Nullam sem massa, lobortis pharetra mattis a, blandit  vestibulum massa. Donec vel tincidunt dui. Aliquam laoreet elementum  egestas. Aliquam blandit rutrum commodo. Curabitur lacinia consequat  lacus in posuere. Nulla semper pulvinar laoreet. Phasellus velit  tortor, mattis quis gravida tincidunt, commodo vitae dui. Vestibulum  ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia  Curae; Nullam feugiat, orci sit amet ullamcorper eleifend, nulla augue  varius turpis, dignissim sagittis risus lorem sit amet augue.  Suspendisse potenti. Nulla condimentum dignissim sem, a dictum elit  fermentum a. Etiam porttitor, metus non eleifend rutrum, erat sem  adipiscing est, sed dignissim dolor ligula ut orci.</p>
    <p>Vivamus pharetra venenatis dapibus. Duis pellentesque justo vel sem  laoreet sed fringilla tellus sollicitudin. Aenean ultrices scelerisque  nulla, pulvinar consequat est volutpat ut. Nunc in augue nec quam  viverra rhoncus a nec diam. Praesent tortor tortor, laoreet id egestas  ut, accumsan vel mauris. Quisque venenatis convallis nibh, sed cursus  ligula fringilla eget. Integer quis ipsum ipsum, quis posuere risus.  Praesent dapibus, orci at vestibulum elementum, nibh nunc tristique  nisi, ut tincidunt nunc elit quis leo. Curabitur sed erat eros.  Pellentesque habitant morbi tristique senectus et netus et malesuada  fames ac turpis egestas. Etiam eget leo et ligula euismod gravida. Nunc  non viverra augue. Vestibulum rhoncus venenatis elit a aliquet. Aliquam  viverra, purus id fringilla pretium, lorem arcu pretium erat, ac  consectetur sem elit sagittis nisl. Integer a rhoncus dui. Proin  lacinia tellus rutrum tortor iaculis consectetur. Praesent vulputate  est in ligula aliquam vel gravida ipsum condimentum. Quisque nec  feugiat felis. Suspendisse pharetra tincidunt orci, id pretium diam  consequat a. Aliquam interdum dignissim fringilla.</p>
    <p>Proin risus velit, adipiscing sit amet accumsan nec, condimentum ut  urna. Maecenas non libero at lacus consectetur tincidunt. Cras nulla  magna, placerat eu malesuada et, pulvinar ut dolor. Suspendisse quam  metus, sollicitudin sed facilisis et, sollicitudin in erat. Lorem ipsum  dolor sit amet, consectetur adipiscing elit. Pellentesque laoreet sem  et ante pulvinar commodo. Fusce vestibulum odio quis mauris aliquet nec  ultrices nunc commodo. Cras posuere, velit ac consectetur venenatis,  velit lectus molestie nisi, eget ornare urna ligula et libero. Proin  dui metus, ultrices ullamcorper tincidunt non, adipiscing non est.  Praesent condimentum molestie augue, ac ultrices magna rhoncus non.  Phasellus odio mi, auctor eu viverra id, posuere a diam. Sed et justo  tortor, eu fermentum orci. Mauris consectetur, metus vel bibendum  dapibus, mauris urna dictum quam, vitae porttitor nibh nibh nec orci.  Donec diam lectus, mollis vitae posuere a, fringilla non tellus. Nam mi  velit, luctus a eleifend et, eleifend sit amet sapien. Nunc dapibus  felis at dui pellentesque vitae bibendum enim pharetra. In imperdiet  tincidunt viverra. Proin vitae nisi sem.</p>
    <p>Curabitur mattis tempor aliquam. Class aptent taciti sociosqu ad  litora torquent per conubia nostra, per inceptos himenaeos. Cras vel  neque et nisi convallis dignissim tempor at lorem. Morbi eu est ut enim  hendrerit rhoncus ac et tellus. Mauris convallis massa ac quam  tincidunt mollis. Proin nec tincidunt purus. Cras viverra suscipit ante  a feugiat. Sed sodales nisi vitae neque blandit venenatis. Quisque  vitae nulla in turpis porta consectetur sed in nibh. Nam et metus quam.</p>
    </div>
    <div class="dataRight">
    <div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Quick Stats</h2>
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
          <li>Site Administrators: <?php userCount("Site Administrator"); ?></li>
          <li>Site Managers: <?php userCount("Site Manager"); ?></li>
          <li>Organization Administrators: <?php userCount("Organization Administrator"); ?></li>
          <li>Administrative Assistants: <?php userCount("Administrative Assistant"); ?></li>
          <li>Instructors: <?php userCount("Instructor"); ?></li>
          <li>Instructorial Assisstants: <?php userCount("Instructorial Assisstant"); ?></li>
          <li>Students: <?php userCount("Student"); ?></li>
        </ul>
        <hr />
        Total Users: <strong><?php echo $userNumber; ?></strong>
      </div>
    </div>
    </div>
    </div>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>