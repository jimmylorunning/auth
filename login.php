<?php
if ($_POST) {
  require_once 'auth.class.php';
  require_once 'config.inc.php';
  
  $auth = new Auth($db, $dbconfig);
  echo $auth->login($_POST['email'], $_POST['password']);
  
} else { ?>
  <form action="login.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
