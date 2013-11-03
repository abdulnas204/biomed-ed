<?php require_once('system/connections/connDBA.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<?php validate(); ?>
</head>

<body>
<form id="validate" name="validate" method="post" action="test.php">
  <a href="#" onclick="$.validationEngine.buildPrompt('#sweet','* This is an invalid file type','success')">Build a prompt on a div</a>
  <p>&nbsp;</p>
  <p>
    <input type="text" name="sweet" id="sweet" class="validate[required]" />
  </p>
  <p>&nbsp;</p>
  <input type="submit" name="submit" id="submit" value="Submit" />
</form>
</body>
</html>