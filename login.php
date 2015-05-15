<?php
if ($_POST) {
  require_once 'auth.class.php';
  
  $auth = new Auth();
  $authcode = $auth->login($_POST['email'], $_POST['password']);
  echo $authcode;

} else { ?>
  <form action="login.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
