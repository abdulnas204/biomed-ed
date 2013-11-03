<?php require_once('../../system/functions.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<!-- TemplateBeginEditable name="doctitle" -->
<title><?php echo $siteName['siteName']; ?>| Pages Control Panel</title>
<!-- TemplateEndEditable -->
<link rel="stylesheet" type="text/css" href="../../styles/style.css" />

<!-- TemplateBeginEditable name="head" --><!-- TemplateEndEditable -->
</head>
<body onload="MM_initInteractions()">
<div id="main_container">

<div id="header">

       <div id="logo"><img src="../../images/logo.png" /></div>
           
       <div class="banner_adds"></div>    


<div class="menu">
<?php include("top_menu.php"); ?>
</div>
</div>
    

    
  <!-- TemplateBeginEditable name="Content" -->
  <div id="main_content">
    <h2>Content</h2>
    <p>&nbsp;</p>
    <!-- end of main_content -->
  </div>
  <!-- TemplateEndEditable -->
  <div id="footer">

	<div id="copyright"></div>
	<?php include("bottom_menu.php"); ?>

    <div class="column1">
      <p class="contact_information">
      <?php include("../../footer.php"); ?></p>
    </div>
</div>
</div>

<!-- end of main_container -->
</body>
</html>