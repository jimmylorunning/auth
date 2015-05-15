<?php
if ($_POST) {
  require_once 'auth.class.php';
  require_once 'config.inc.php';
  
  $pdo = Auth::newPdo($dbconfig);
  $auth = new Auth($pdo);
  $authcode = $auth->login($_POST['email'], $_POST['password']);
  echo $authcode;

} else { ?>
  <form action="login.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
