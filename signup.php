<?php
require_once 'shared/config.inc.php';

if ($_POST) {  
  echo $auth->createUser($_POST['email'], $_POST['password']);
} else { ?>
  <form action="signup.php" method="post">
  Email: <input type="text" name="email" value=""><br />
  Password: <input type="password" name="password" value=""><br />
  <input type="submit">
  </form>
<?php } ?>
