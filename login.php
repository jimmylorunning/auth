<?php
require_once 'shared/config.inc.php';

if ($_POST) {
  $authcode = $auth->login($_POST['email'], $_POST['password']);
  echo $authcode;
} else { ?>
  <form action="login.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
