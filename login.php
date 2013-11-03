<?php require_once('Connections/connDBA.php'); ?>
<?php login(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php title("Login"); ?>
<?php if (!isset($_GET['action'])) { headers();} ?>
</head>
<body<?php bodyClass(); ?>>
<?php if (!isset($_GET['action'])) { topPage("includes/top_menu.php");} ?>
      <form method="post" action="login.php<?php if (isset($_GET['accesscheck'])) {echo "?accesscheck=" .  urlencode($_GET['accesscheck']);} ?>" name="login" id="login">
        <h1>Login</h1>
        <p> Login with your username and password to access your account. </p>
        <table width="100%" border="0" align="center">
          <tr>
            <td width="30%"><div align="right">Username:</div></td>
          <td width="70%"><div align="left">
                <label>
                <input type="text" name="username" id="username" />
                </label>
            </div></td>
          </tr>
          <tr>
            <td width="30%"><div align="right">Password:</div></td>
          <td width="70%"><div align="left">
                <label>
                <input type="password" name="password" id="password" />
                </label>
            </div></td>
          </tr>
          <tr>
            <td width="30%">&nbsp;</td>
            <td width="70%"><div align="left">
              <input type="submit" name="submit" id="submit" value="Login" />
              </div>
                </label></td>
          </tr>
          <?php
			  if (!isset($_GET['action'])) {
				  echo "<tr><td width=\"30%\">&nbsp;</td><td width=\"70%\"><p><a href=\"forgot_password.php\">Forgot your password?</a><br /><a href=\"register.php\">Register</a></p></td></tr>";
			  }
		  ?>
        </table>
</form>
<?php if (!isset($_GET['action'])) { footer("includes/bottom_menu.php");} ?>
</body>
</html>