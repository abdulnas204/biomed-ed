<?php require_once('../../Connections/connDBA.php'); ?>
<div class="menu">
  <ul>
    <li class="first"><a href="<?php echo $root . "site_administrator/index.php"; ?>"><strong>Home</strong></a></li> <span class="arrow sep">&#x25BA;</span> 
<li class="first"><a href="<?php echo $root . "site_administrator/users/index.php"; ?>">Users</a></li> <span class="arrow sep">&#x25BA;</span> 
<li class="first"><a href="<?php echo $root . "site_administrator/organizations/index.php"; ?>">Organizations</a></li> <span class="arrow sep">&#x25BA;</span> 
<li class="first"><a href="<?php echo $root . "site_administrator/communication/index.php"; ?>">Communication</a></li> <span class="arrow sep">&#x25BA;</span> 
    <li class="first"><a href="<?php echo $root . "site_administrator/modules/index.php"; ?>">Modules</a></li> 
    <span class="arrow sep">&#x25BA;</span> 
    <li class="first"><a href="<?php echo $root . "site_administrator/statistics/index.php"; ?>" class="nav_footer">Statistics</a></li>
    <span class="arrow sep">&#x25BA;</span> 
    <li class="first"><a href="<?php echo $root . "site_administrator/cms/index.php"; ?>">Modify Website</a></li> <span class="arrow sep">&#x25BA;</span> 
    <li class="first"><a href="<?php echo $root . "logout.php"; ?>">Logout</a></li>
  </ul>
</div>
